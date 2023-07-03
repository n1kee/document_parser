<?php

require_once './APP.php';

class DB {

    static $connection;

    static function beginTransaction() {
        self::$connection->beginTransaction();
    }

    static function commit() {
        self::$connection->commit();
    }

    static function fetchAll() {
        self::$connection->fetchAll();
    }

    static function connect() {

        if (self::$connection) return self::$connection;

        $config = APP::getConfig();

        self::$connection = new PDO(sprintf("mysql:host=%s;dbname=%s", $config['servername'], $config['dbname']), $config['username'], $config['password']);
        self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return self::$connection ?? null;
    }
}
