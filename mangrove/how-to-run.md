# How to run (Mangrove)

- Prerequisites: PHP 8.3+ with `pdo_sqlite` (e.g. `brew install php`) and Node.js 22.13+. Composer only for the backend tests.
- Setup command: `npm install --prefix frontend`, then demo data with `php backend/bin/seed.php`.
- Start the frontend on the grove port: `API_PORT=$(( {{port}} + 5 )) npm run dev --prefix frontend -- --port {{port}} --strictPort`
- Extra process "api": `cd backend && php -S 0.0.0.0:$(( {{port}} + 5 )) -t public public/index.php`
- PHP code is loaded per request, so the API picks up changes without a restart.
- Tests: `cd backend && composer install && composer test` (PHPUnit), `npm test --prefix frontend` (Vitest).
