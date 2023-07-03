<?php

require_once './APP.php';

class DB
{
    public static $connection;

    public static function beginTransaction()
    {
        self::$connection->beginTransaction();
    }

    public static function commit()
    {
        self::$connection->commit();
    }

    public static function connect()
    {

        if (self::$connection) {
            return self::$connection;
        }

        $config = APP::getConfig();

        self::$connection = new PDO(sprintf("mysql:host=%s;dbname=%s", $config['servername'], $config['dbname']), $config['username'], $config['password']);
        self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return self::$connection ?? null;
    }
}
