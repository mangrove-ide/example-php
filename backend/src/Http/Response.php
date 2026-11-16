<?php

declare(strict_types=1);

namespace Crm\Http;

final readonly class Response
{
    public function __construct(public HttpStatus $status, public mixed $body = null)
    {
    }

    public static function json(mixed $body, HttpStatus $status = HttpStatus::OK): self
    {
        return new self($status, $body);
    }

    public static function error(string $message, HttpStatus $status): self
    {
        return new self($status, ["error" => $message]);
    }

    public static function noContent(): self
    {
        return new self(HttpStatus::NO_CONTENT);
    }

    public function encodedBody(): string
    {
        return $this->body === null ? "" : json_encode($this->body, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function send(): void
    {
        http_response_code($this->status->value);
        if ($this->body !== null) {
            header("Content-Type: application/json; charset=utf-8");
        }
        echo $this->encodedBody();
    }
}
