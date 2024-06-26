<?php

namespace App\Core\Database;

class Transaction
{
    public static function startTransaction(): void
    {
        if (!self::isTransactionActive()) {
            Connection::startTransaction();
        }
    }

    public static function commitTransaction(): void
    {
        if (self::isTransactionActive()) {
            Connection::commitTransaction();
        }
    }

    public static function rollbackTransaction(): void
    {
        if (self::isTransactionActive()) {
            Connection::rollbackTransaction();
        }
    }

    public static function isTransactionActive(): bool
    {
        return Connection::inTransaction();
    }
}
