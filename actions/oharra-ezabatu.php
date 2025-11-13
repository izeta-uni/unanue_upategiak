<?php

require_once '../bootstrap.php';
require_once '../database/db.php';
require_once '../model/Oharra.php';

// Sarbide-kontrola: Administratzaileek bakarrik sar dezakete orrialde honetara
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    $_SESSION['error_message'] = "Ez duzu baimenik ekintza hori egiteko.";
    header('Location: ../index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $oharra_id = $_POST['id'] ?? null;

    if (!$oharra_id || !is_numeric($oharra_id)) {
        $_SESSION['error_message'] = "Oharraren IDa ez da baliozkoa.";
        header('Location: ../oharrak-kudeatu.php');
        exit;
    }

    try {
        $conn = konektatuDatuBasera();
        if (Oharra::borratu($conn, (int)$oharra_id)) {
            $_SESSION['success_message'] = "Oharra ondo ezabatu da.";
        } else {
            $_SESSION['error_message'] = "Errorea oharra ezabatzean.";
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Errorea datu-basearekin: " . htmlspecialchars($e->getMessage());
    } finally {
        if ($conn) {
            $conn->close();
        }
    }
} else {
    $_SESSION['error_message'] = "Baimenik gabeko sarbidea.";
}

header('Location: ../oharrak-kudeatu.php');
exit;
