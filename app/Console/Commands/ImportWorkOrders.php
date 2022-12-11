<?php

namespace App\Console\Commands;

use App\Modules\Importer\Http\Controllers\ImporterController;
use App\Modules\Importer\Models\ImporterLog;
use App\Modules\Importer\Repositories\ImporterRepository;
use App\Modules\Importer\Services\ImporterService;
use App\Modules\WorkOrder\Models\WorkOrder;
use App\Modules\WorkOrder\Repositories\WorkOrderRepository;
use DateTime;
use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;

class ImportWorkOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importer:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Work Orders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ImporterService $importerService)
    {
        list($woObjects, $existingWoObjects) = $importerService->createWoObjects();

        $importerLog = new ImporterLog();
        $importerLog->type = 'Artisan Command';
        $importerLog->run_at = date('Y-m-d H:i:s');
        $importerLog->entries_processed = count($woObjects) + count($existingWoObjects);
        $importerLog->entries_created = count($woObjects);
        $importerLog->save();

        $file = $importerService->createCsv('importerReport.csv', $woObjects, $existingWoObjects);

        $headers = array(
            'Content-Type' => 'text/csv',
        );
    }
}
