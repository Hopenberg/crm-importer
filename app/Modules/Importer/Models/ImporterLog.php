<?php

namespace App\Modules\Importer\Models;

use App\Core\LogModel;
use Illuminate\Database\Eloquent\Model;

class ImporterLog extends Model
{
    protected $table = 'importer_log';
    protected $primaryKey  = 'id';
    public $timestamps = false;

    protected $fillable = [];

    // relationships

    // scopes

    // getters
}
