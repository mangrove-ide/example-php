<?php

declare(strict_types=1);

namespace Crm;

use PDO;

final readonly class CustomerRepository
{
    private const string CUSTOMER_COLUMNS = "id, first_name AS firstName, last_name AS lastName, email, created_at AS createdAt";

    public function __construct(private PDO $connection)
    {
    }

    /** @return list<Customer> */
    public function list(): array
    {
        $statement = $this->connection->query("SELECT " . self::CUSTOMER_COLUMNS . " FROM customers ORDER BY last_name, first_name, id");
        return array_map(Customer::fromRow(...), $statement->fetchAll());
    }

    public function get(int $customerId): ?Customer
    {
        $statement = $this->connection->prepare("SELECT " . self::CUSTOMER_COLUMNS . " FROM customers WHERE id = ?");
        $statement->execute([$customerId]);
        $row = $statement->fetch();
        return $row === false ? null : Customer::fromRow($row);
    }

    public function hasEmail(string $email): bool
    {
        $statement = $this->connection->prepare("SELECT id FROM customers WHERE email = ?");
        $statement->execute([$email]);
        return $statement->fetch() !== false;
    }

    public function create(NewCustomer $customer): Customer
    {
        $statement = $this->connection->prepare("INSERT INTO customers (first_name, last_name, email) VALUES (?, ?, ?)");
        $statement->execute([$customer->firstName, $customer->lastName, $customer->email]);
        return $this->get((int) $this->connection->lastInsertId());
    }

    public function remove(int $customerId): bool
    {
        $statement = $this->connection->prepare("DELETE FROM customers WHERE id = ?");
        $statement->execute([$customerId]);
        return $statement->rowCount() > 0;
    }
}
