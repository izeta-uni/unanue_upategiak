<?php

// Ezkutatu oharrak eta erroreak produkziorako
// error_reporting(0);
// ini_set('display_errors', 0);

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