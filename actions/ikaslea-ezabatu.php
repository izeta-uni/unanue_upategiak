<?php

require_once '../database/db.php';
require_once '../model/Pertsona.php';

// Saioa hasi, mezuak gorde ahal izateko.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Egiaztatu administratzailea
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    $_SESSION['error_message'] = "Ez duzu baimenik ikasleak borratzeko. Ez zara administratzaile bat.";
    header("Location: ikasle-zerrenda.php");
    exit;
}

// Ezabatu behar den ikaslearen ID-a formulariotik hartu
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// URL-ko query parametrotan id-a jarrita dagoen egiaztatu.
if ($id) {
    try {
        $conn = konektatuDatuBasera();
        if (Pertsona::borratu($conn, $id)) {
            $_SESSION['success_message'] = "Ikaslea egoki ezabatu da.";
        } else {
            $_SESSION['error_message'] = "Ezin izan da ikaslea ezabatu edo ez da existitzen.";
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Errorea ikaslea ezabatzean. Ziurtatu ez duela loturarik (adib. matrikulak, kontuak).";
    } finally {
        if(isset($conn)) $conn->close();
    }
} else {
    $_SESSION['error_message'] = "ID baliogabea edo ez da zehaztu.";
}

header("Location: ../ikasle-zerrenda.php");
exit;
?>