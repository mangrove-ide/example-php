<?php

declare(strict_types=1);

namespace Crm;

use JsonSerializable;

final readonly class Customer implements JsonSerializable
{
    public function __construct(
        public int $id,
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $createdAt,
    ) {
    }

    /** @param array{id: int|string, firstName: string, lastName: string, email: string, createdAt: string} $row */
    public static function fromRow(array $row): self
    {
        return new self((int) $row["id"], $row["firstName"], $row["lastName"], $row["email"], $row["createdAt"]);
    }

    /** @return array{id: int, firstName: string, lastName: string, email: string, createdAt: string} */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
