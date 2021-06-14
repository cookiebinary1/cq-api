<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class IdBigint
 * @author Martin Osusky
 *
 * Description: Laravel migration which convert all INTEGER ID fields to BIGINT in database.
 * Use on your own risc.
 *
 * Make sure that dependency 'doctrine/dbal' is solved. Otherwise run: composer require doctrine/dbal
 */
class IdBigint extends Migration
{
    /**
     *
     */
    public function up()
    {
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) use ($table) {
                $columns = Schema::getColumnListing($table);
                foreach ($columns as $column) {
                    if (in_array(DB::getSchemaBuilder()->getColumnType($table, $column), ['integer', 'bigint'])) {
                        if ($column === 'id') {
                            $blueprint->bigIncrements('id')->change();
                        }
                        if (substr($column, -3) === '_id') {
                            $blueprint->unsignedBigInteger($column)->change();
                        }
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
