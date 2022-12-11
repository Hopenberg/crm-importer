<?php

namespace App\Modules\Importer\Models;

use App\Core\LogModel;

class Importer extends LogModel
{
    protected $table = 'importer';
    protected $primaryKey  = 'id';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'modified_at';

    protected $fillable = [];

    // relationships

    // scopes

    // getters
}
