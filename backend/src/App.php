<?php

declare(strict_types=1);

namespace Crm;

use Crm\Http\HttpStatus;
use Crm\Http\Request;
use Crm\Http\Response;
use JsonException;

final readonly class App
{
    private const string CUSTOMERS_PATH = "/api/customers";
    private const string CUSTOMER_PATH_PATTERN = "#^/api/customers/([^/]+)$#";

    public function __construct(private CustomerRepository $customers)
    {
    }

    public function handle(Request $request): Response
    {
        $customerId = preg_match(self::CUSTOMER_PATH_PATTERN, $request->path, $matches) === 1 ? $matches[1] : null;
        return match (true) {
            $request->path === self::CUSTOMERS_PATH && $request->method === "GET" => Response::json($this->customers->list()),
            $request->path === self::CUSTOMERS_PATH && $request->method === "POST" => $this->createCustomer($request),
            $customerId !== null && $request->method === "DELETE" => $this->removeCustomer($customerId),
            default => Response::error("Not found", HttpStatus::NOT_FOUND),
        };
    }

    private function createCustomer(Request $request): Response
    {
        try {
            $body = $request->body === "" ? [] : json_decode($request->body, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return Response::error("Request body is not valid JSON", HttpStatus::BAD_REQUEST);
        }
        $customer = NewCustomer::tryParse($body);
        if (is_string($customer)) {
            return Response::error($customer, HttpStatus::BAD_REQUEST);
        }
        if ($this->customers->hasEmail($customer->email)) {
            return Response::error("A customer with this email already exists", HttpStatus::CONFLICT);
        }
        return Response::json($this->customers->create($customer), HttpStatus::CREATED);
    }

    private function removeCustomer(string $rawCustomerId): Response
    {
        $customerId = filter_var($rawCustomerId, FILTER_VALIDATE_INT);
        if ($customerId === false || !$this->customers->remove($customerId)) {
            return Response::error("Customer not found", HttpStatus::NOT_FOUND);
        }
        return Response::noContent();
    }
}
