<?php

declare(strict_types=1);

namespace Crm;

use PDO;

final class Database
{
    public const string IN_MEMORY_PATH = ":memory:";

    private const string SCHEMA = <<<SQL
        CREATE TABLE IF NOT EXISTS customers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            first_name TEXT NOT NULL,
            last_name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE COLLATE NOCASE,
            created_at TEXT NOT NULL DEFAULT (strftime('%Y-%m-%dT%H:%M:%fZ', 'now'))
        )
        SQL;

    public static function open(string $path): PDO
    {
        if ($path !== self::IN_MEMORY_PATH && !is_dir(dirname($path))) {
            mkdir(dirname($path), recursive: true);
        }
        $connection = new PDO("sqlite:{$path}", options: [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ]);
        $connection->exec(self::SCHEMA);
        return $connection;
    }
}
