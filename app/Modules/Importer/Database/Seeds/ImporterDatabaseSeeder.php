<?php

namespace App\Modules\Importer\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Core\Rbac\Permission;

class ImporterDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $list = $this->getPermissions();

        $result = Permission::set('importer', $list);

        foreach ($result as $info) {
            $this->command->info($info);
        }
    }

    /**
     * Get permissions list
     */
    public function getPermissions()
    {
        return [
            /*
            'importer.index'   => 'Importer index',
            'importer.show'    => 'Importer show',
            'importer.store'   => 'Importer store',
            'importer.update'  => 'Importer update',
            'importer.destroy' => 'Importer destroy',
            */
        ];
    }
}
