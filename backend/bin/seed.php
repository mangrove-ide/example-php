<?php

declare(strict_types=1);

use Crm\Config;
use Crm\CustomerRepository;
use Crm\Database;
use Crm\NewCustomer;

require_once __DIR__ . "/../src/bootstrap.php";

const DEMO_CUSTOMERS = [
    ["Ada", "Lovelace", "ada@example.com"],
    ["Grace", "Hopper", "grace@example.com"],
    ["Alan", "Turing", "alan@example.com"],
    ["Katherine", "Johnson", "katherine@example.com"],
];

$config = Config::fromEnvironment();
$customers = new CustomerRepository(Database::open($config->databasePath));
$newCustomers = array_filter(
    array_map(static fn (array $fields): NewCustomer => new NewCustomer(...$fields), DEMO_CUSTOMERS),
    static fn (NewCustomer $customer): bool => !$customers->hasEmail($customer->email),
);
array_walk($newCustomers, static fn (NewCustomer $customer) => $customers->create($customer));

echo "Seeded " . count($newCustomers) . " customers into {$config->databasePath}" . PHP_EOL;
