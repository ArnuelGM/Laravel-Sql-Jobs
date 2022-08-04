<?php

namespace App\Http\Controllers\SqlJobs;

use App\Models\SqlJob;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Connection\ConnectionService;
use Exception;

class SqlJobService {


  public static function runScript(SqlJob $sqlJob)
  {

    // Actualizamos el estado del job actual
    $sqlJob->status = SqlJob::STATUS_IN_PROGRESS;
    $sqlJob->save();

    try {

      // Ejecution del script, se intenta ejecutar maximo 3 veces
      retry(3, function () use ($sqlJob) {

        // Creamos la conneccion
        $connection = ConnectionService::createConnection($sqlJob->connection)->name;

        try {
          DB::connection($connection)->beginTransaction();
          DB::connection($connection)->statement($sqlJob->script);
          DB::connection($connection)->commit();

          // Actualizamos el estado del job actual
          $sqlJob->status = SqlJob::STATUS_DONE;
          $sqlJob->save();
        }
        catch (\Throwable $e) {
          DB::connection($connection)->rollBack();
          throw $e;
        }

      }, 100);

    }
    catch(\Throwable $e) {

      // Actualizamos el estado del job actual
      $sqlJob->status = SqlJob::STATUS_ERROR;
      $sqlJob->error  = $e->getMessage();
      $sqlJob->save();

      return false;
    }

    return true;
  }


}
