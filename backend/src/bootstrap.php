<?php

declare(strict_types=1);

// Composer's autoloader when `composer install` has run; otherwise a PSR-4 fallback so the API
// runs on a bare PHP install (it has no runtime dependencies).
$composerAutoloader = __DIR__ . "/../vendor/autoload.php";
if (is_file($composerAutoloader)) {
    require_once $composerAutoloader;
} else {
    spl_autoload_register(static function (string $className): void {
        $prefix = "Crm\\";
        if (!str_starts_with($className, $prefix)) {
            return;
        }
        $path = __DIR__ . "/" . str_replace("\\", "/", substr($className, strlen($prefix))) . ".php";
        if (is_file($path)) {
            require_once $path;
        }
    });
}
