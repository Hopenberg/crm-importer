<?php

namespace App\Modules\Importer\Repositories;

use App\Core\AbstractRepository;
use App\Modules\Importer\Models\Importer;
use Illuminate\Container\Container;
use App\Modules\Importer\Http\Requests\ImporterRequest;

/**
 * Importer repository class
 */
class ImporterRepository extends AbstractRepository
{
    /**
     * {@inheritdoc}
     */
    protected $searchable = [];

    /**
     * Repository constructor
     *
     * @param Container $app
     * @param Importer $importer
     */
    public function __construct(Container $app, Importer $importer)
    {
        parent::__construct($app,  $importer);
    }

    /**
     * Get front-end validation rules
     *
     * @return array
     */
    public function getRequestRules()
    {
        $req = new ImporterRequest();

        return $req->getFrontendRules();
    }
}
