<?php

$NS = MODULES_NS.'Importer\Http\Controllers\\';

$router->get('importer/import', $NS.'ImporterController@doImport');
$router->get('importer/index', $NS.'ImporterController@index');