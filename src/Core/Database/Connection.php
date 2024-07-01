<?php

namespace App\Core\Database;

use App\Core\Environment;
use PDO;
use PDOStatement;

class Connection
{
    private const OPTIONS = [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ];
    private static $maxIdle = 30;
    private static $lastQueryTime;
    private static $pdo;
    private static $lastInstance;
    private static $transactionActive = false;

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
            throw new \PDOException("Database error: " . $ex->getMessage());
        }
    }

    private static function connect(): PDO
    {
        $timeout = time() - self::$maxIdle;
        if (isset(self::$lastQueryTime) && self::$lastQueryTime > $timeout) {
            return self::$pdo;
        }

        $credentials = self::getCredentials();
        $connection = "mysql:host={$credentials['host']};dbname={$credentials['name']}";

        try {
            self::$pdo = new PDO($connection, $credentials['user'], $credentials['password'], self::OPTIONS);
            self::$lastQueryTime = time();
            return self::$pdo;
        } catch (\PDOException $ex) {
            throw new \PDOException("Database error: " . $ex->getMessage());
        }
    }

    private static function getCredentials(): array
    {
        return [
            'host' => Environment::get('DATABASE_HOST'),
            'name' => Environment::get('DATABASE_NAME'),
            'user' => Environment::get('DATABASE_USER'),
            'password' => Environment::get('DATABASE_PASSWORD')
        ];
    }

    public static function startTransaction(): void
    {
        if (!self::$transactionActive) {
            self::connect()->beginTransaction();
            self::$transactionActive = true;
        }
    }

    public static function commitTransaction(): void
    {
        if (self::$transactionActive) {
            self::connect()->commit();
            self::$transactionActive = false;
        }
    }

    public static function rollbackTransaction(): void
    {
        if (self::$transactionActive) {
            self::connect()->rollBack();
            self::$transactionActive = false;
        }
    }

    public static function inTransaction(): bool
    {
        return self::$transactionActive;
    }
}
