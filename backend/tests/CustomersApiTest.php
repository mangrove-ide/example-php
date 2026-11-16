<?php

declare(strict_types=1);

namespace Crm\Tests;

use Crm\App;
use Crm\CustomerRepository;
use Crm\Database;
use Crm\Http\HttpStatus;
use Crm\Http\Request;
use Crm\Http\Response;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class CustomersApiTest extends TestCase
{
    private const string CUSTOMERS_PATH = "/api/customers";

    private App $app;

    protected function setUp(): void
    {
        $this->app = new App(new CustomerRepository(Database::open(Database::IN_MEMORY_PATH)));
    }

    #[Test]
    public function createsListsAndDeletesACustomer(): void
    {
        $createResponse = $this->postCustomer(["firstName" => " Ada ", "lastName" => "Lovelace", "email" => "ada@example.com"]);
        self::assertSame(HttpStatus::CREATED, $createResponse->status);
        ["id" => $customerId, "createdAt" => $createdAt] = $this->decode($createResponse);
        self::assertSame(
            ["firstName" => "Ada", "lastName" => "Lovelace", "email" => "ada@example.com"],
            array_diff_key($this->decode($createResponse), ["id" => true, "createdAt" => true]),
        );
        self::assertIsInt($customerId);
        self::assertNotEmpty($createdAt);

        self::assertCount(1, $this->decode($this->send("GET", self::CUSTOMERS_PATH)));

        self::assertSame(HttpStatus::NO_CONTENT, $this->send("DELETE", self::CUSTOMERS_PATH . "/{$customerId}")->status);
        self::assertSame([], $this->decode($this->send("GET", self::CUSTOMERS_PATH)));
    }

    #[Test]
    public function rejectsMissingFieldsAndInvalidEmails(): void
    {
        $missingResponse = $this->postCustomer(["firstName" => "Ada"]);
        self::assertSame(HttpStatus::BAD_REQUEST, $missingResponse->status);
        self::assertStringContainsString("lastName, email", $this->decode($missingResponse)["error"]);

        $invalidResponse = $this->postCustomer(["firstName" => "Ada", "lastName" => "Lovelace", "email" => "not-an-email"]);
        self::assertSame(HttpStatus::BAD_REQUEST, $invalidResponse->status);
    }

    #[Test]
    public function rejectsDuplicateEmailsRegardlessOfCase(): void
    {
        $this->postCustomer(["firstName" => "Ada", "lastName" => "Lovelace", "email" => "ada@example.com"]);
        $duplicateResponse = $this->postCustomer(["firstName" => "Ada", "lastName" => "L", "email" => "ADA@example.com"]);
        self::assertSame(HttpStatus::CONFLICT, $duplicateResponse->status);
    }

    #[Test]
    public function returnsNotFoundWhenDeletingAnUnknownCustomer(): void
    {
        self::assertSame(HttpStatus::NOT_FOUND, $this->send("DELETE", self::CUSTOMERS_PATH . "/999")->status);
    }

    #[Test]
    public function listsCustomersByLastNameThenFirstName(): void
    {
        $this->postCustomer(["firstName" => "Grace", "lastName" => "Hopper", "email" => "grace@example.com"]);
        $this->postCustomer(["firstName" => "Ada", "lastName" => "Lovelace", "email" => "ada@example.com"]);
        $this->postCustomer(["firstName" => "Alan", "lastName" => "Hopper", "email" => "alan@example.com"]);

        $fullNames = array_map(
            static fn (array $customer): string => "{$customer["firstName"]} {$customer["lastName"]}",
            $this->decode($this->send("GET", self::CUSTOMERS_PATH)),
        );
        self::assertSame(["Alan Hopper", "Grace Hopper", "Ada Lovelace"], $fullNames);
    }

    private function send(string $method, string $path, string $body = ""): Response
    {
        return $this->app->handle(new Request($method, $path, $body));
    }

    /** @param array<string, string> $customer */
    private function postCustomer(array $customer): Response
    {
        return $this->send("POST", self::CUSTOMERS_PATH, json_encode($customer, JSON_THROW_ON_ERROR));
    }

    private function decode(Response $response): mixed
    {
        return json_decode($response->encodedBody(), associative: true, flags: JSON_THROW_ON_ERROR);
    }
}
