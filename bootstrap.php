<?php

// Composer autoloader-a kargatu
require_once __DIR__ . '/vendor/autoload.php';

// Kargatu beharreko .env fitxategia zehaztu
$envFile = $_ENV['APP_ENV'] === "production" ? '.env.prod' : '.env.dev';

// Dagokion .env fitxategia kargatu
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, $envFile);
    $dotenv->load();

    // Behar ditugun konstanteak definitu
    define('DB_HOST', 'db'); // Docker sareko zerbitzuaren izena
    define('DB_USER', $_ENV['DB_USER'] ?? null);
    define('DB_PASS', $_ENV['DB_PASS'] ?? null);
    define('DB_NAME', $_ENV['DB_NAME'] ?? null);
    define('APP_ENV', $_ENV['APP_ENV'] ?? 'production');

} catch (Exception $e) {
    // Gelditu exekuzioa .env fitxategia kargatu ezin bada
    die('Errorea: Ezin izan da konfigurazio fitxategia kargatu. ' . $e->getMessage());
}

// Ezkutatu oharrak eta erroreak produkziorako
if ($_ENV['APP_ENV'] === "production") {
    error_reporting(0);
    ini_set('display_errors', 0);
} else {
    error_reporting(1);
    ini_set('display_errors', 1);
}

// Erakutsi errore guztiak
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cookien konfigurazioa
session_set_cookie_params([
    'lifetime' => 0, // Nabigatzailea ixtean iraungitzen da
    'path' => '/',
    'domain' => '',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', // True HTTPS badago
    'httponly' => false, // <-- httpOnly desaktibatuta
    'samesite' => 'Lax'
]);


// Hasi saioa iada hasita ez badago.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}