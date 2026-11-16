# CRM — PHP port

A basic CRM for adding and removing customers (first name, last name, email). Same REST API as
[example-typescript](https://github.com/mangrove-ide/example-typescript),
[example-java](https://github.com/mangrove-ide/example-java) and
[example-python](https://github.com/mangrove-ide/example-python).

- `backend/` — REST API in modern PHP (8.3+: readonly classes, enums, typed constants, `match`) with no runtime
  dependencies. It runs on PHP's built-in web server and stores data in SQLite via PDO (`ext-pdo_sqlite`).
  Composer is only needed for the tests (PHPUnit).
- `frontend/` — React 19 app (Vite) that calls the API through a `/api` dev proxy.

Requires PHP 8.3+ with `pdo_sqlite` and Node.js 22.13+.

## Setup

```sh
npm install --prefix frontend
(cd backend && composer install)   # only for the tests
php backend/bin/seed.php           # optional demo customers
```

## Run

In two terminals:

```sh
php -S 0.0.0.0:4000 -t backend/public backend/public/index.php
API_PORT=4000 npm run dev --prefix frontend
```

| Variable        | Used by  | Default                     | Purpose                              |
| --------------- | -------- | --------------------------- | ------------------------------------ |
| `DATABASE_PATH` | backend  | `backend/data/crm.sqlite`   | SQLite file (ignored by git)         |
| `API_PORT`      | frontend | `4000`                      | Backend port the Vite proxy targets  |

The API port is the one passed to `php -S`. The built-in server does not need restarting after
PHP changes: each request loads the code again.

## Test

```sh
(cd backend && composer test)
npm test --prefix frontend
```

## API

| Method   | Path                  | Body                               | Responses                     |
| -------- | --------------------- | ---------------------------------- | ----------------------------- |
| `GET`    | `/api/customers`      | —                                  | `200` list of customers       |
| `POST`   | `/api/customers`      | `{firstName, lastName, email}`     | `201`, `400` invalid, `409` duplicate email |
| `DELETE` | `/api/customers/:id`  | —                                  | `204`, `404`                  |
