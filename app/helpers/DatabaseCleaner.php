<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class DatabaseCleaner
{
    public static function clean(array $tables, $connection = null)
    {
        $connection = DB::connection($connection);
        $driver = $connection->getDriverName();

        if ($driver === 'mysql') {
            $connection->statement('SET FOREIGN_KEY_CHECKS=0;');
        } elseif ($driver === 'pgsql') {
            $connection->statement('SET CONSTRAINTS ALL DEFERRED;');
        }

        foreach ($tables as $table) {
            $connection->table($table)->truncate();
        }

        if ($driver === 'mysql') {
            $connection->statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }
}
