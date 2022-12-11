<?php

namespace App\Modules\Importer\Services;

use App\Modules\Importer\Repositories\ImporterRepository;
use App\Modules\WorkOrder\Models\WorkOrder;
use App\Modules\WorkOrder\Repositories\WorkOrderRepository;
use DateTime;
use Illuminate\Container\Container;
use Symfony\Component\DomCrawler\Crawler;

class ImporterService
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * @var ImporterRepository
     */
    protected $importer;
    /**
     * @var WorkOrderRepository
     */
    private $workOrderRepository;

    public function __construct(WorkOrderRepository $workOrderRepository)
    {
        $this->workOrderRepository = $workOrderRepository;
    }

    public function createCsv($filename, $woObjects, $existingWoObjects) {
        $csvFile = fopen($filename, 'w');

        fputcsv(
            $csvFile, [
                'Work Order Number',
                'External ID',
                'Priority',
                'Received Date',
                'Category',
                'Store Location',
                'Has Been Processed'
            ]
        );

        foreach ($woObjects as $woObject) {
            fputcsv(
                $csvFile,
                [
                    $woObject->work_order_number,
                    $woObject->external_id,
                    $woObject->priority,
                    $woObject->received_date,
                    $woObject->category,
                    $woObject->fin_loc,
                    'Processed'
                ]
            );
        }

        foreach ($existingWoObjects as $object) {
            fputcsv(
                $csvFile,
                [
                    $object->work_order_number,
                    $object->external_id,
                    $object->priority,
                    $object->received_date,
                    $object->category,
                    $object->fin_loc,
                    'Not processed'
                ]
            );
        }

        fclose($csvFile);

        return $csvFile;
    }

    public function createWoObjects() {
        $file = file_get_contents(base_path() . '/work_orders.html');

        $crawler = new Crawler($file);
        $table = $crawler->filter('table.rgMasterTable')->first();
        $workOrders = $table->filter('tbody tr')->each(function (Crawler $node, $i) {
            $ticketCol = trim($node->filter('td')->getNode(0)->nodeValue);

            $entityIdCol = $node->filter('td')->getNode(0);
            $entityIdCrawler = new Crawler($entityIdCol);
            $href = $entityIdCrawler->filter('a')->attr('href');
            $matches = [];
            preg_match('/entityid=(?<entityId>.*)/mi', $href, $matches);
            $entityId = trim($matches['entityId']);

            $urgencyCol = trim($node->filter('td')->getNode(3)->nodeValue);

            $rcvdDateCol = trim($node->filter('td')->getNode(4)->nodeValue);
            $dtObject = DateTime::createFromFormat('m/d/Y', $rcvdDateCol);
            $dbFriendlyDate = $dtObject->format('Y-m-d');

            $categoryCol = trim($node->filter('td')->getNode(8)->nodeValue);
            $storeCol = trim($node->filter('td')->getNode(10)->nodeValue);

            return [
                'work_order_number' => $ticketCol,
                'external_id' => $entityId,
                'priority' => $urgencyCol,
                'received_date' => $dbFriendlyDate,
                'category' => $categoryCol,
                'fin_loc' => $storeCol
            ];
        });

        $woObjects = [];
        $existingWoObjects = [];
        foreach ($workOrders as $workOrder) {
            $woModel = new WorkOrder();
            foreach ($workOrder as $key => $value) {
                $woModel->$key = $value;
            }

            $woInDatabase = $this->workOrderRepository->findByWoNumber($workOrder['work_order_number']);

            if (!is_null($woInDatabase)) {
                $existingWoObjects[] = $woModel;
            } else {
                $woModel->save();
                $woObjects[] = $woModel;
            }
        }

        return [$woObjects, $existingWoObjects];
    }
}
