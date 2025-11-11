<?php
require_once __DIR__ . '/../config.php';

function konektatuDatuBasera() {
    // Saiatu konexioa sortzen
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        // Konexio errorea egiaztatu
        if ($conn->connect_error) {
            throw new Exception("Konexioak huts egin du: " . $conn->connect_error);
        }
        
        return $conn;
    } catch (Exception $e) {
        die("Ezin izan da datu basera konektatu. Saiatu berriro geroago.");
    }
}
?>
