<?php

// Saioa hasi iada ez badago hasita
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base URL definitu, estekak (CSS, nabigazioa) ondo eraikitzeko.
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

// Proiektua azpikarpeta batean badago, ondo funtzionatzeko
$script_name = str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
define('BASE_URL', $protocol . $host . $script_name);

?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($orri_titulua . ' - IES Xabier Zubiri Manteo BHI' ?? 'IES Xabier Zubiri Manteo BHI'); ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>public/img/logo.png" type="image/x-icon">
</head>
<body class="d-flex flex-column min-vh-100">

<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>index.php">
                <img src="<?php echo BASE_URL; ?>public/img/logo.png" alt="Logo" style="height: 40px;">
                Zubiri Manteo
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item me-2">
                        <a class="btn btn-light" href="<?php echo BASE_URL; ?>index.php">Hasiera</a>
                    </li>
                    <?php 
                        // Ikaslearen aukerak administratzaileak bakarrik ditu.
                        if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
                            echo '<li class="nav-item me-2"><a class="btn btn-light" href="' . BASE_URL . 'ikasle-zerrenda.php">Ikasle Zerrenda</a></li>';
                            echo '<li class="nav-item"><a class="btn btn-light" href="' . BASE_URL . 'ikaslea-sortu.php">Ikaslea Sortu</a></li>';
                        }
                    ?>
                </ul>
                <div class="d-flex align-items-center">
                    <?php if (isset($_SESSION['erabiltzailea'])): ?>
                        <span class="navbar-text me-3">Kaixo, <?php echo htmlspecialchars($_SESSION['erabiltzailea']); ?></span>
                        <a href="<?php echo BASE_URL; ?>itxi-saioa.php" class="btn btn-outline-primary">Saioa Itxi</a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>hasi-saioa.php" class="btn btn-primary me-2">Hasi Saioa</a>
                        <a href="<?php echo BASE_URL; ?>kontua-sortu.php" class="btn btn-secondary">Sortu Kontua</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>

<main class="container my-5 flex-grow-1">
