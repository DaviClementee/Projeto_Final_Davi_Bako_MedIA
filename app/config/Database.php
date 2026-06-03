<?php

class DatabaseSingle {
    private static $connection = null;

    private static $host     = 'localhost';
    private static $db_name  = 'mydevpapdatabase';
    private static $username = 'root';
    private static $password = '';

    public static function connect(): PDO {
        if (!self::$connection) {
            self::$connection = new PDO(
                "mysql:host=" . self::$host . ";dbname=" . self::$db_name . ";charset=utf8mb4",
                self::$username,
                self::$password
            );
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$connection;
    }
}
