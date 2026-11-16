<?php

declare(strict_types=1);

namespace Crm;

final readonly class NewCustomer
{
    private const string EMAIL_PATTERN = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
    private const array REQUIRED_FIELDS = ["firstName", "lastName", "email"];

    public function __construct(public string $firstName, public string $lastName, public string $email)
    {
    }

    /**
     * Validates a decoded request body; returns the error message when it is not a valid customer.
     */
    public static function tryParse(mixed $body): self|string
    {
        $values = array_combine(self::REQUIRED_FIELDS, array_map(
            static fn (string $field): string => is_array($body) && is_string($body[$field] ?? null) ? trim($body[$field]) : "",
            self::REQUIRED_FIELDS,
        ));
        $missingFields = array_keys(array_filter($values, static fn (string $value): bool => $value === ""));
        if ($missingFields !== []) {
            return "Missing required fields: " . implode(", ", $missingFields);
        }
        if (preg_match(self::EMAIL_PATTERN, $values["email"]) !== 1) {
            return "Email is not valid";
        }
        return new self(...$values);
    }
}
