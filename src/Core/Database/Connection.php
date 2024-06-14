<?php

namespace App\Core\Database;

use PDO;
use PDOStatement;

class Connection
{
    private const OPTIONS = [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ];
    private static $maxIdle = 30;
    private static  $lastQueryTime;
    private static  $pdo;
    private static $lastInstance;

    public static function prepare(string $sql): PDOStatement 
    {
        self::$lastInstance = self::connect();
        $stmt = self::$lastInstance->prepare($sql);
        return $stmt;
    }

    public static function execute(PDOStatement $stmt, array $parameters = []): bool 
    {
        try {
            $result = $stmt->execute($parameters);
            self::$lastQueryTime = time();
            return $result;
        } catch (\PDOException $ex) {
            throw new \PDOException("Database error." . $ex->getMessage());
            ;
        }
    }

    private static function connect(): PDO
    {
        $timeout = time() - self::$maxIdle;
        if (isset(self::$lastQueryTime) && self::$lastQueryTime > $timeout) {
            return self::$pdo;
        }

        // $credentials = self::getCredentials();
        $connection = "mysql:host='localhost;dbname=my_database'";

        try {
            self::$pdo = new PDO($connection, 'my_user', 'my_password', self::OPTIONS);
            self::$lastQueryTime = time();
            
            return self::$pdo; 
        } catch (\PDOException $ex) {
            throw new \PDOException("Database error." . $ex->getMessage());
        }
    }

}