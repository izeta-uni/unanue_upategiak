<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../database/db.php';
require_once '../model/Matrikula.php';

// Egiaztatu erabiltzailea ikasle bat den eta saioa hasita duen
if (empty($_SESSION['erabiltzailea']) || !empty($_SESSION['is_admin']) || empty($_SESSION['pertsona_id'])) {
    $_SESSION['error_message'] = "Baimenik gabeko ekintza.";
    header('Location: ../index.php');
    exit;
}

// Egiaztatu eskaera POST den
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$ikasle_id = $_SESSION['pertsona_id'];

try {
    $conn = konektatuDatuBasera();
    
    if (Matrikula::ezabatuIkaslearenMatrikula($conn, $ikasle_id)) {
        $_SESSION['success_message'] = "Matrikula ondo desegin da.";
    } else {
        $_SESSION['error_message'] = "Ezin izan da matrikula desegin edo ez zeunden matrikulatuta.";
    }

} catch (Exception $e) {
    $_SESSION['error_message'] = "Errore bat gertatu da matrikula desegitean: " . $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
    header('Location: ../index.php');
    exit;
}
?>