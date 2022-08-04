<?php

namespace App\Http\Controllers\Connection;

use App\Models\Connection;
use Illuminate\Support\Facades\DB;

class ConnectionService
{

  public static function testConnection($connection)
  {
    $connection = ConnectionService::createConnection($connection);
    $statement = "SELECT 1";
    try {
      $result = DB::connection( $connection->name )->select($statement);
    }
    catch(\Throwable $e) {
      throw $e;
    }

    return $result == true;
  }

  public static function createConnection(Connection $connection)
  {
    $config = $connection->type == 'sqlite'
      ? self::getSqliteConfig($connection)
      : self::getConfig($connection);

    $configName = 'database.connections.' . str($connection->name)->slug();

    config(["$configName" => $config]);

    return (Object) [
      "config"  => $configName,
      "name"    => str($connection->name)->slug()->toString()
    ];
  }

  public static function getSqliteConfig($connection)
  {
    return [
      'driver'    => 'sqlite',
      'url'       => '',
      'database'  => $connection->database,
      'prefix'    => '',
      'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
    ];
  }

  public static function getConfig($connection)
  {
    return [
      'driver'    => $connection->type,
      'url'       => '',
      'host'      => $connection->host,
      'port'      => $connection->port,
      'database'  => $connection->database,
      'username'  => $connection->user,
      'password'  => $connection->password,
      'prefix'    => '',
      'trust_server_certificate' => true
    ];
  }

}
