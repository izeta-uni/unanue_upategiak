<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../database/db.php';
require_once '../model/Kontua.php';
require_once '../model/Pertsona.php';

$erroreak = [];
$form_data = [
    'emaila' => trim($_POST['emaila'] ?? ''),
    'erabiltzaile_izena' => trim($_POST['erabiltzaile_izena'] ?? ''),
];
$pasahitza = trim($_POST['pasahitza'] ?? '');
$pasahitza_errepikatu = trim($_POST['pasahitza_errepikatu'] ?? '');

if (!filter_var($form_data['emaila'], FILTER_VALIDATE_EMAIL)) $erroreak[] = "Email formatua ez da zuzena.";
if (empty($form_data['erabiltzaile_izena'])) $erroreak[] = "Erabiltzaile izena derrigorrezkoa da.";
if (strlen($pasahitza) < 6) $erroreak[] = "Pasahitzak gutxienez 6 karaktere izan behar ditu.";
if ($pasahitza !== $pasahitza_errepikatu) $erroreak[] = "Pasahitzak ez datoz bat.";

if (empty($erroreak)) {
    try {
        $conn = konektatuDatuBasera();
        $pertsona = Pertsona::bilatuEmaila($conn, $form_data['emaila']);

        if (!$pertsona) {
            $erroreak[] = "Email hau ez dago erregistratuta sisteman. Jarri harremanetan administratzailearekin.";
        } else {
            // Egiaztatu ea pertsona honek iada kontu bat duen
            if (Kontua::bilatuPertsonaId($conn, $pertsona->id)) {
                $erroreak[] = "Email honi lotutako kontu bat existitzen da jada.";
            } elseif (Kontua::bilatuErabiltzaileIzenez($conn, $form_data['erabiltzaile_izena'])) {
                // Bestela, egiaztatu ea erabiltzaile izena hartuta dagoen
                $erroreak[] = "Erabiltzaile izen hau jada existitzen da.";
            } else {
                $kontu_berria = new Kontua(['erabiltzaile_izena' => $form_data['erabiltzaile_izena']]);
                $kontu_berria->setPasahitza($pasahitza);
                $kontu_berria->pertsona_id = $pertsona->id;

                if ($kontu_berria->gorde($conn)) {
                    $_SESSION['success_message'] = "Kontua ondo sortu da. Hasi saioa orain.";
                    header("Location: ../hasi-saioa.php");
                    exit;
                } else {
                    $erroreak[] = "Errorea kontua sortzean.";
                }
            }
        }
    } catch (Exception $e) {
        $erroreak[] = "Datu basearekin errorea: " . $e->getMessage();
    } finally {
        if (isset($conn)) $conn->close();
    }
}

// Akatsen bat egon bada, gorde datuak saioan eta itzuli formulariora
if (!empty($erroreak)) {
    $_SESSION['form_errors'] = $erroreak;
    $_SESSION['form_data'] = $form_data;
    header("Location: ../kontua-sortu.php");
    exit;
}