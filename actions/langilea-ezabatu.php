<?php

require_once '../bootstrap.php';
require_once '../database/db.php';
require_once '../model/Langilea.php';

// Egiaztatu administratzailea
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    $_SESSION['error_message'] = "Ez duzu baimenik langileak ezabatzeko. Administratzailea izan behar duzu.";
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ezabatu behar den langilearen ID-a formulariotik hartu
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    // ID-a baliozkoa dela egiaztatu.
    if ($id) {
        try {
            $conn = konektatuDatuBasera();
            if (Langilea::borratu($conn, $id)) {
                $_SESSION['success_message'] = "Langilea ondo ezabatu da.";
            } else {
                $_SESSION['error_message'] = "Ezin izan da langilea ezabatu edo ez da existitzen.";
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Errorea langilea ezabatzean: " . htmlspecialchars($e->getMessage());
        } finally {
            if(isset($conn)) $conn->close();
        }
    } else {
        $_SESSION['error_message'] = "ID baliogabea edo ez da zehaztu.";
    }
} else {
    $_SESSION['error_message'] = "Baimenik gabeko sarbidea.";
}

header("Location: ../langile-zerrenda.php");
exit;
?>