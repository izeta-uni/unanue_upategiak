<?php

require_once 'bootstrap.php';
require_once 'database/db.php';
require_once 'model/Langilea.php';
require_once 'model/Oharra.php';

// Erabiltzailea saioa hasita dagoen egiaztatu
if (!isset($_SESSION['user_id'])) {
    header('Location: hasi-saioa.php');
    exit;
}

$orri_titulua = 'Hasiera';
$conn = null; // Konexioa inizializatu

try {
    $conn = konektatuDatuBasera();

    if ($_SESSION['is_admin']) {
        // Administratzailearen ikuspegia
        $orri_titulua = 'Agintarien Panela';
        include 'templates/header.php';
        ?>
        <div class="text-center mb-5 animate__animated animate__fadeInDown">
            <h1>Ongi etorri, <?php echo htmlspecialchars($_SESSION['username']); ?> (Administratzailea)</h1>
            <p class="lead">Hemen duzu Unanue Upategiak-eko barne kudeaketa panela.</p>
        </div>

        <div class="row justify-content-center animate__animated animate__fadeIn">
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Langileak Kudeatu</h5>
                        <p class="card-text">Langileen informazioa ikusi, gehitu, editatu eta ezabatu.</p>
                        <a href="langile-zerrenda.php" class="btn btn-primary">Joan Langileetara</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Oharrak Kudeatu</h5>
                        <p class="card-text">Segurtasun eta prestakuntza oharrak sortu, eguneratu eta ezabatu.</p>
                        <a href="oharrak-kudeatu.php" class="btn btn-info">Joan Oharretara</a>
                    </div>
                </div>
            </div>
        </div>

        <?php
    } else {
        // Langile arruntaren ikuspegia (Oharrak)
        $orri_titulua = 'Oharrak';
        include 'templates/header.php';

        $oharrak = Oharra::lortuOharGuztiak($conn);
        ?>
        <div class="text-center mb-5 animate__animated animate__fadeInDown">
            <h1>Ongi etorri, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
            <p class="lead">Hemen duzu Unanue Upategiak-eko ohar garrantzitsuen panela.</p>
        </div>

        <div class="row justify-content-center animate__animated animate__fadeIn">
            <div class="col-lg-8">
                <?php if (!empty($oharrak)): ?>
                    <?php foreach ($oharrak as $oharra): ?>
                        <div class="card mb-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title text-primary"><?php echo htmlspecialchars($oharra->titulua); ?></h5>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($oharra->edukia)); ?></p>
                                <p class="card-text"><small class="text-muted">Argitaratze data: <?php echo htmlspecialchars($oharra->data); ?></small></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info text-center">Ez dago oharrik une honetan.</div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Errorea datuak kargatzean: " . htmlspecialchars($e->getMessage()) . "</div>";
} finally {
    if ($conn) {
        $conn->close();
    }
}

include 'templates/footer.php';
?>
