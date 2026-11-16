<?php

declare(strict_types=1);

namespace Crm;

final readonly class Config
{
    private const string DEFAULT_DATABASE_PATH = __DIR__ . "/../data/crm.sqlite";

    public function __construct(public string $databasePath)
    {
    }

    public static function fromEnvironment(): self
    {
        $databasePath = getenv("DATABASE_PATH");
        return new self($databasePath === false || $databasePath === "" ? self::DEFAULT_DATABASE_PATH : $databasePath);
    }
}
