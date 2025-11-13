<?php

require_once '../database/db.php';
require_once '../model/Pertsona.php';

// Saioa hasi, mezuak gorde ahal izateko.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Egiaztatu administratzailea
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    die("Baimenik gabe.");
}

// POST ez den eskaerak hasierara birbideratu.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header('Location: ../ikaslea-editatu.php');
    exit;
}

// Balidazio erroreen lista.
$erroreak = [];

// Eguneratu behar den ikaslearen ID-a formulariotik hartu
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

// Balidatu ID-a existitzen dela
if (!$id) {
    $_SESSION['error_message'] = "Ikaslearen ID baliogabea da.";
    header('Location: ../ikasle-zerrenda.php');
    exit;
}

// Formularioko datuak jaso
$izena = trim($_POST['izena']);
$abizena = trim($_POST['abizena']);
$jaiotze_data = trim($_POST['jaiotze_data']);
$nan = trim($_POST['nan']);
$emaila = trim($_POST['emaila']);

// Balidazioak
if (empty($izena)) $erroreak[] = "Izena derrigorrezkoa da.";
if (empty($abizena)) $erroreak[] = "Abizena derrigorrezkoa da.";
if (!filter_var($emaila, FILTER_VALIDATE_EMAIL)) {
    $erroreak[] = "Email formatua ez da zuzena.";
}
if (!preg_match('/@zubirimanteo\.eus$/i', $emaila)) {
    $erroreak[] = "Emailak @zubirimanteo.eus domeinua izan behar du.";
}
if (!preg_match('/^[0-9]{8}[A-Z]$/i', $nan)) $erroreak[] = "NAN formatua ez da zuzena (adib: 12345678A).";
if (!empty($jaiotze_data) && !DateTime::createFromFormat('Y-m-d', $jaiotze_data)) {
    $erroreak[] = "Jaiotze dataren formatua 'UUUU-HH-EE' izan behar da.";
}   

// Erroreak badaude, bidalitako datuak gorde formularioa berriro betetzeko eta birbideratu orrira.
if (!empty($erroreak)) {
    $_SESSION['form_errors'] = $erroreak;
    $_SESSION['form_data'] = $_POST; 
    header('Location: ../ikaslea-editatu.php?id=' . $id);
    exit;
}

// Datuak datu-basean eguneratu
try {
    $conn = konektatuDatuBasera();

    // Egiaztatu ea emandako emaila beste pertsona batena den
    $pertsona_email = Pertsona::bilatuEmaila($conn, $emaila);
    if ($pertsona_email && $pertsona_email->id != $id) {
        $erroreak[] = "Email hau beste pertsona batek erabiltzen du.";
    }

    // Egiaztatu ea emandako NANa beste pertsona batena den
    $pertsona_nan = Pertsona::bilatuNan($conn, $nan);
    if ($pertsona_nan && $pertsona_nan->id != $id) {
        $erroreak[] = "NAN hau beste pertsona batek erabiltzen du.";
    }

    // Datu-baseko balidazioetan erroreak badaude, bueltatu formulariora
    if (!empty($erroreak)) {
        $_SESSION['form_errors'] = $erroreak;
        $_SESSION['form_data'] = $_POST;
        header('Location: ../ikaslea-editatu.php?id=' . $id);
        exit;
    }

    // Lehendik dagoen ikaslea bilatu
    $ikaslea = Pertsona::bilatuId($conn, $id);

    if (!$ikaslea) {
        throw new Exception("Ez da zehaztutako ID-a duen ikaslerik aurkitu.");
    }
    
    // Propietateak eguneratu
    $ikaslea->izena = $izena;
    $ikaslea->abizena = $abizena;
    $ikaslea->jaiotze_data = $jaiotze_data;
    $ikaslea->nan = $nan;
    $ikaslea->emaila = $emaila;
    
    if ($ikaslea->gorde($conn)) {
        $_SESSION['success_message'] = "Ikaslearen datuak egoki eguneratu dira.";
        header("Location: ../ikasle-zerrenda.php");
        exit;
    } else {
        throw new Exception("Errorea datuak eguneratzean.");
    }
} catch (Exception $e) {
    $_SESSION['form_errors'] = ["Datu basearen errorea: " . $e->getMessage()];
    $_SESSION['form_data'] = $_POST;
    header('Location: ../ikaslea-editatu.php?id=' . $id);
    exit;
} finally {
    if (isset($conn)) $conn->close();
}