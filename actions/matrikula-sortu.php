<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../database/db.php';
require_once '../model/Matrikula.php';

// Egiaztapenak
if (!isset($_SESSION['erabiltzailea'])) {
    $_SESSION['error_message'] = "Ezin da matrikulatu saioa hasi gabe.";
    header('Location: ../index.php');
    exit;
}
if (!isset($_SESSION['pertsona_id']) || $_SESSION['pertsona_id'] === null) {
    $_SESSION['error_message'] = "Errorea: Zure erabiltzaileak ez du ikasle id-rik esleituta. Mesedez, hasi saioa berriro.";
    header('Location: ../index.php');
    exit;
}
if (!empty($_SESSION['is_admin'])) {
    $_SESSION['error_message'] = "Administratzaileek ezin dute matrikulaziorik egin.";
    header('Location: ../index.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$ikasle_id = $_SESSION['pertsona_id'];
$kurtso_id = filter_input(INPUT_POST, 'kurtso_id', FILTER_VALIDATE_INT);

if (!$kurtso_id) {
    $_SESSION['error_message'] = "Kurtso baliogabea hautatu da.";
    header('Location: ../index.php');
    exit;
}

// Matrikulazio logika
try {
    $conn = konektatuDatuBasera();

    // Egiaztatu ikaslea iada beste kurtso batean matrikulatuta dagoen
    if (Matrikula::ikasleaMatrikulatutaDago($conn, $ikasle_id)) {
        // Hala bada, birbideratu JavaScript alerta erakusteko URL parametroarekin
        header('Location: ../index.php?errorea=matrikulatuta');
        exit;
    }

    // Ikaslea matrikulatuta ez badago, matrikula berria sortu
    $matrikula_berria = new Matrikula([
        'ikasle_id' => $ikasle_id,
        'kurtso_id' => $kurtso_id
    ]);

    // Matrikula datu-basean gorde
    if ($matrikula_berria->gorde($conn)) {
        $_SESSION['success_message'] = "Kurtsoan ondo matrikulatu zara!";
    } else {
        $_SESSION['error_message'] = "Errore bat gertatu da matrikulazioan. Saiatu berriro geroago.";
    }

} catch (Exception $e) {
    // Modelotan sortutako erroreak kudeatu.
    $_SESSION['error_message'] = "Datu basearekin errorea: " . $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
    // Beti hasierako orrira birbideratu
    header('Location: ../index.php');
    exit;
}
?>