<?php
// Saioa hasi, errore mezuak eta bidalitako datuak gorde ahal izateko.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fitxategiak karpeta baten barruan daudenez, bide-izenak aldatu behar dira (../)
require_once '../database/db.php';
require_once '../model/Pertsona.php';

// Egiaztatu administratzailea den eta eskaera POST bidez datorren.
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    die("Baimenik gabe.");
}

// POST ez den eskaerak hasierara birbideratu.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header('Location: ../ikaslea-sortu.php');
    exit;
}

$erroreak = [];
$form_data = [
    'izena' => trim($_POST['izena'] ?? ''),
    'abizena' => trim($_POST['abizena'] ?? ''),
    'jaiotze_data' => trim($_POST['jaiotze_data'] ?? ''),
    'nan' => trim($_POST['nan'] ?? ''),
    'emaila' => trim($_POST['emaila'] ?? '')
];

// Balidazioak
if (empty($form_data['izena'])) $erroreak[] = "Izena derrigorrezkoa da.";
if (empty($form_data['abizena'])) $erroreak[] = "Abizena derrigorrezkoa da.";
if (!filter_var($form_data['emaila'], FILTER_VALIDATE_EMAIL)) {
    $erroreak[] = "Email formatua ez da zuzena.";
}
if (!preg_match('/@zubirimanteo\.eus$/i', $form_data['emaila'])) {
    $erroreak[] = "Emailak @zubirimanteo.eus domeinua izan behar du.";
}
if (!preg_match('/^[0-9]{8}[A-Z]$/i', $form_data['nan'])) $erroreak[] = "NAN formatua ez da zuzena (adib: 12345678A).";
if (!empty($form_data['jaiotze_data']) && !DateTime::createFromFormat('Y-m-d', $form_data['jaiotze_data'])) {
    $erroreak[] = "Jaiotze dataren formatua 'UUUU-HH-EE' izan behar da.";
}

// Lehen balidazio sortaren ondoren errorerik badago, ez jarraitu.
if (!empty($erroreak)) {
    $_SESSION['form_errors'] = $erroreak;
    $_SESSION['form_data'] = $form_data;
    header('Location: ../ikaslea-sortu.php');
    exit;
}

// Errorerik ez badago, datu-basean gordetzen saiatu
try {
    $conn = konektatuDatuBasera();

    // Egiaztatu ea NANa datu-basean existitzen den
    if (Pertsona::bilatuNan($conn, $form_data['nan'])) {
        $erroreak[] = "NAN hau jada erregistratuta dago.";
    }

    // Egiaztatu ea emaila datu-basean existitzen den
    if (Pertsona::bilatuEmaila($conn, $form_data['emaila'])) {
        $erroreak[] = "Email hau jada erregistratuta dago.";
    }

    // Datu-baseko balidazioetan erroreak badaude, gorde saioan eta bueltatu formulariora
    if (!empty($erroreak)) {
        $_SESSION['form_errors'] = $erroreak;
        $_SESSION['form_data'] = $form_data;
        header('Location: ../ikaslea-sortu.php');
        exit;
    }
    
    // Errorerik ez badago, sortu ikaslea
    $ikasle_berria = new Pertsona($form_data);
    $ikasle_berria->rola = 'ikasle';
    
    $ikasle_berria->gorde($conn);
    
    // Dena ondo joan da
    $_SESSION['success_message'] = "Ikaslea egoki sortu da.";
    header("Location: ../ikasle-zerrenda.php");
    exit;

} catch (Exception $e) {
    // Errore generikoa harrapatu
    $_SESSION['form_errors'] = ["Datu basearen errorea: " . $e->getMessage()];
    $_SESSION['form_data'] = $form_data;
    header('Location: ../ikaslea-sortu.php');
    exit;
} finally {
    if (isset($conn)) $conn->close();
}