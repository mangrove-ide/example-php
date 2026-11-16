<?php

declare(strict_types=1);

namespace Crm\Http;

final readonly class Request
{
    public function __construct(public string $method, public string $path, public string $body = "")
    {
    }

    public static function fromGlobals(): self
    {
        return new self(
            $_SERVER["REQUEST_METHOD"] ?? "GET",
            parse_url($_SERVER["REQUEST_URI"] ?? "/", PHP_URL_PATH) ?: "/",
            file_get_contents("php://input") ?: "",
        );
    }
}
