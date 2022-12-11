<?php

namespace App\Modules\Importer\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Modules\Importer\Models\ImporterLog;
use App\Modules\Importer\Repositories\ImporterRepository;
use App\Modules\Importer\Services\ImporterService;
use App\Modules\WorkOrder\Repositories\WorkOrderRepository;
use Illuminate\Http\Response;
use App;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Class ImporterController
 *
 * @package App\Modules\Importer\Http\Controllers
 */
class ImporterController extends Controller
{
    /**
     * @var ImporterService
     */
    private $importerService;

    public function __construct(ImporterService $importerService)
    {
        $this->importerService = $importerService;
    }

    public function index() {
        return view('importer.index');
    }

    public function doImport() {
        list($woObjects, $existingWoObjects) = $this->importerService->createWoObjects();

        $importerLog = new ImporterLog();
        $importerLog->type = 'Browser';
        $importerLog->run_at = date('Y-m-d H:i:s');
        $importerLog->entries_processed = count($woObjects) + count($existingWoObjects);
        $importerLog->entries_created = count($woObjects);
        $importerLog->save();

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=importReport.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $callback = function() use ($woObjects, $existingWoObjects)
        {
            $this->importerService->createCsv('php://output', $woObjects, $existingWoObjects);
        };

        return \response()->stream($callback, 200, $headers);
    }
}
