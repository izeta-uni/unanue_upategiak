<?php

// Composer autoloader-a kargatu
require_once __DIR__ . '/vendor/autoload.php';

// .env fitxategia kargatu
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    // Behar ditugun konstanteak definitu
    define('DB_HOST', $_ENV['DB_HOST'] ?? null);
    define('DB_USER', $_ENV['MYSQL_USER'] ?? null);
    define('DB_PASS', $_ENV['MYSQL_PASSWORD'] ?? null);
    define('DB_NAME', $_ENV['MYSQL_DATABASE'] ?? null);
    define('APP_ENV', $_ENV['APP_ENV'] ?? 'development'); // 'development' lehenetsi gisa

} catch (Exception $e) {
    // Gelditu exekuzioa .env fitxategia kargatu ezin bada
    die('Errorea: Ezin izan da konfigurazio fitxategia kargatu. ' . $e->getMessage());
}

// Ezkutatu oharrak eta erroreak produkziorako, bestela erakutsi denak
if (APP_ENV === "production") {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
}

// Cookien konfigurazioa
session_set_cookie_params([
    'lifetime' => 0, // Nabigatzailea ixtean iraungitzen da
    'path' => '/',
    'domain' => '',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', // True HTTPS badago
    'httponly' => true, // <-- httpOnly aktibatuta
    'samesite' => 'Lax'
]);


// Hasi saioa iada hasita ez badago.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    session_regenerate_id(true);
}

// Saioaren segurtasun neurriak
define('SESSION_TIMEOUT', 1800); // 30 minutu

if (isset($_SESSION['user_id'])) {
    // Egiaztatu saioaren denbora-muga
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_unset();
        session_destroy();
        header('Location: hasi-saioa.php?error=session_expired');
        exit;
    }
    $_SESSION['last_activity'] = time();

    // Egiaztatu User-Agent-a
    if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        session_unset();
        session_destroy();
        header('Location: hasi-saioa.php?error=user_agent_mismatch');
        exit;
    }
    
    if (!isset($_SESSION['user_agent'])) {
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    }
}