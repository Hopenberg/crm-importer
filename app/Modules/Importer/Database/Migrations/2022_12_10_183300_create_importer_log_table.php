<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImporterLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $primaryKeyName = 'id';
    
        if (!Schema::hasTable('importer_log')) {
            Schema::create('importer_log',
                function (Blueprint $table) use ($primaryKeyName) {
                      $table->increments($primaryKeyName);
                });
        }

        $columns = Schema::getColumnListing('importer_log');

        Schema::table('importer_log',
            function (Blueprint $table) use ($columns, $primaryKeyName) {

                if (!in_array($primaryKeyName, $columns)) {
                    $table->increments($primaryKeyName);
                }
                if (!in_array('', $columns)) {
                    $table->string('type', 1000);
                    $table->timestamp('run_at');
                    $table->integer('entries_processed');
                    $table->integer('entries_created');
                }

        });
    }

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down()
    {
        /* we need to assume everything could exist so cannot reverse it */
    }
}
