<?php

require_once '../bootstrap.php';
require_once '../database/db.php';
require_once '../model/Oharra.php';

// Sarbide-kontrola
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: ../index.php');
    exit;
}

$erroreak = [];
$titulua = '';
$edukia = '';

// Datuak jaso eta balidatu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulua = trim($_POST['titulua'] ?? '');
    $edukia = trim($_POST['edukia'] ?? '');

    if (empty($titulua)) {
        $erroreak[] = "Titulua derrigorrezkoa da.";
    }
    if (empty($edukia)) {
        $erroreak[] = "Edukia derrigorrezkoa da.";
    }

    if (empty($erroreak)) {
        try {
            $conn = konektatuDatuBasera();
            $oharra = new Oharra([
                'titulua' => $titulua,
                'edukia' => $edukia
            ]);

            if ($oharra->gorde($conn)) {
                $_SESSION['success_message'] = "Oharra ondo sortu da.";
                header('Location: ../oharrak-kudeatu.php');
                exit;
            } else {
                $erroreak[] = "Errorea oharra sortzean.";
            }
        } catch (Exception $e) {
            $erroreak[] = "Errorea datu-basearekin: " . htmlspecialchars($e->getMessage());
        } finally {
            if ($conn) {
                $conn->close();
            }
        }
    }
    
    // Erroreak badaude, gorde sesioan eta birzuzendu
    if (!empty($erroreak)) {
        $_SESSION['form_data'] = ['titulua' => $titulua, 'edukia' => $edukia];
        $_SESSION['form_errors'] = $erroreak;
        header('Location: ../oharra-sortu.php');
        exit;
    }
} else {
    // POST eskaera ez bada, hasierara birzuzendu
    header('Location: ../index.php');
    exit;
}
