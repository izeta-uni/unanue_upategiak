<?php
// Saioa hasi, errore mezuak eta bidalitako datuak gorde ahal izateko.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fitxategiak karpeta baten barruan daudenez, bide-izenak aldatu behar dira (../)
require_once '../bootstrap.php';
require_once '../database/db.php';
require_once '../model/Langilea.php';

// Egiaztatu administratzailea den eta eskaera POST bidez datorren.
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    $_SESSION['error_message'] = "Ez duzu baimenik ekintza hori egiteko.";
    header('Location: ../index.php');
    exit;
}

// POST ez den eskaerak hasierara birbideratu.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header('Location: ../langile-zerrenda.php');
    exit;
}

$erroreak = [];
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT); // IDa eguneratzeko
$is_update = ($id !== false && $id !== null); // Eguneratze bat den ala ez

$form_data = [
    'izena' => trim($_POST['izena'] ?? ''),
    'abizena' => trim($_POST['abizena'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'departamentua' => trim($_POST['departamentua'] ?? ''),
    'kargua' => trim($_POST['kargua'] ?? ''),
    'kontratazio_data' => trim($_POST['kontratazio_data'] ?? ''),
    'erabiltzailea' => trim($_POST['erabiltzailea'] ?? ''),
    'pasahitza' => $_POST['pasahitza'] ?? '', // Pasahitza ez da trim egiten, hutsik egon daitekeelako
    'is_admin' => isset($_POST['is_admin']) ? true : false
];

// Balidazioak
if (empty($form_data['izena'])) $erroreak[] = "Izena derrigorrezkoa da.";
if (empty($form_data['abizena'])) $erroreak[] = "Abizena derrigorrezkoa da.";
if (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
    $erroreak[] = "Email formatua ez da zuzena.";
}
if (empty($form_data['departamentua'])) $erroreak[] = "Departamentua derrigorrezkoa da.";
if (empty($form_data['erabiltzailea'])) $erroreak[] = "Erabiltzaile izena derrigorrezkoa da.";

// Pasahitzaren balidazioa
if (!$is_update || (!empty($form_data['pasahitza']))) { // Sortzean edo pasahitza aldatzean
    if (empty($form_data['pasahitza'])) {
        $erroreak[] = "Pasahitza derrigorrezkoa da.";
    } elseif (strlen($form_data['pasahitza']) < 8) {
        $erroreak[] = "Pasahitzak gutxienez 8 karaktere izan behar ditu.";
    }
}

// Lehen balidazio sortaren ondoren errorerik badago, ez jarraitu.
if (!empty($erroreak)) {
    $_SESSION['form_errors'] = $erroreak;
    $_SESSION['form_data'] = $form_data;
    $redirect_url = $is_update ? '../langilea-editatu.php?id=' . $id : '../langilea-sortu.php';
    header('Location: ' . $redirect_url);
    exit;
}

// Errorerik ez badago, datu-basean gordetzen saiatu
try {
    $conn = konektatuDatuBasera();

    // Emailaren bakartasuna egiaztatu
    $existing_email_langilea = Langilea::bilatuEmail($conn, $form_data['email']);
    if ($existing_email_langilea && (!$is_update || $existing_email_langilea->id !== $id)) {
        $erroreak[] = "Email hau jada erregistratuta dago.";
    }

    // Erabiltzaile izenaren bakartasuna egiaztatu
    $existing_username_langilea = Langilea::bilatuErabiltzailea($conn, $form_data['erabiltzailea']);
    if ($existing_username_langilea && (!$is_update || $existing_username_langilea->id !== $id)) {
        $erroreak[] = "Erabiltzaile izen hau jada erregistratuta dago.";
    }

    // Datu-baseko balidazioetan erroreak badaude, gorde saioan eta bueltatu formulariora
    if (!empty($erroreak)) {
        $_SESSION['form_errors'] = $erroreak;
        $_SESSION['form_data'] = $form_data;
        $redirect_url = $is_update ? '../langilea-editatu.php?id=' . $id : '../langilea-sortu.php';
        header('Location: ' . $redirect_url);
        exit;
    }
    
    $langilea = null;
    if ($is_update) {
        $langilea = Langilea::bilatuId($conn, $id);
        if (!$langilea) {
            $_SESSION['error_message'] = "Ez da ID hori duen langilerik aurkitu eguneratzeko.";
            header('Location: ../langile-zerrenda.php');
            exit;
        }
        // Eguneratu propietateak
        $langilea->izena = $form_data['izena'];
        $langilea->abizena = $form_data['abizena'];
        $langilea->email = $form_data['email'];
        $langilea->departamentua = $form_data['departamentua'];
        $langilea->kargua = $form_data['kargua'];
        $langilea->kontratazio_data = $form_data['kontratazio_data'];
        $langilea->erabiltzailea = $form_data['erabiltzailea'];
        $langilea->is_admin = $form_data['is_admin'];

        // Pasahitza aldatu bada bakarrik hashetu
        if (!empty($form_data['pasahitza'])) {
            $langilea->pasahitza = password_hash($form_data['pasahitza'], PASSWORD_DEFAULT);
        }
    } else {
        // Langile berria sortu
        $langilea = new Langilea([
            'izena' => $form_data['izena'],
            'abizena' => $form_data['abizena'],
            'email' => $form_data['email'],
            'departamentua' => $form_data['departamentua'],
            'kargua' => $form_data['kargua'],
            'kontratazio_data' => $form_data['kontratazio_data'],
            'erabiltzailea' => $form_data['erabiltzailea'],
            'pasahitza' => password_hash($form_data['pasahitza'], PASSWORD_DEFAULT), // Pasahitza hashetu
            'is_admin' => $form_data['is_admin']
        ]);
    }
    
    if ($langilea->gorde($conn)) {
        $_SESSION['success_message'] = $is_update ? "Langilea ondo eguneratu da." : "Langilea ondo sortu da.";
        header("Location: ../langile-zerrenda.php");
        exit;
    } else {
        $erroreak[] = $is_update ? "Errorea langilea eguneratzean." : "Errorea langilea sortzean.";
        $_SESSION['form_errors'] = $erroreak;
        $_SESSION['form_data'] = $form_data;
        $redirect_url = $is_update ? '../langilea-editatu.php?id=' . $id : '../langilea-sortu.php';
        header('Location: ' . $redirect_url);
        exit;
    }

} catch (Exception $e) {
    // Errore generikoa harrapatu
    $_SESSION['form_errors'] = ["Datu basearen errorea: " . htmlspecialchars($e->getMessage())];
    $_SESSION['form_data'] = $form_data;
    $redirect_url = $is_update ? '../langilea-editatu.php?id=' . $id : '../langilea-sortu.php';
    header('Location: ' . $redirect_url);
    exit;
} finally {
    if (isset($conn)) $conn->close();
}