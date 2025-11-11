<?php

require_once 'bootstrap.php';
require_once 'database/db.php';
require_once 'model/Kurtsoa.php';
require_once 'model/Matrikula.php';

$orri_titulua = 'Hasiera';
include 'templates/header.php';

// Kurtsoak eta ikaslearen matrikula lortu
$kurtsoak = [];
$matrikulatutakoKurtsoId = null;

try {
    $conn = konektatuDatuBasera();
    $kurtsoak = Kurtsoa::lortuGuztiak($conn);

    // Erabiltzailea ikasle bat bada, egiaztatu ea matrikulatuta dagoen
    if (!empty($_SESSION['pertsona_id']) && empty($_SESSION['is_admin'])) {
        $matrikulatutakoKurtsoId = Matrikula::lortuIkaslearenMatrikulaKurtsoId($conn, $_SESSION['pertsona_id']);
    }

} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Ezin izan dira datuak kargatu: " . $e->getMessage() . "</div>";
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>

<div class="text-center mb-5 animate__animated animate__fadeInDown">
    <h1>XABIER ZUBIRI MANTEO INSTITUTUA, DONOSTIA</h1>
</div>


<div class="mt-4">
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success animate__animated animate__flipInX"><p class="mb-0"><?php echo $_SESSION['success_message']; ?></p></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger animate__animated animate__shakeX"><p class="mb-0"><?php echo $_SESSION['error_message']; ?></p></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
</div>

<div class="row g-4 animate__animated animate__fadeIn">
    <?php if (!empty($kurtsoak)): ?>
        <?php foreach ($kurtsoak as $kurtsoa): ?>
            <div class="col-lg-4 col-md-6 d-flex align-items-stretch">
                <div class="card h-100 course-card w-100">
                    <div class="card-body d-flex flex-column">
                        <h2 class="card-title h4 text-primary"><?php echo htmlspecialchars($kurtsoa->izena); ?></h2>
                        <p class="card-text"><strong>Maila:</strong> <?php echo htmlspecialchars($kurtsoa->gradu_maila); ?></p>
                        <p class="card-text"><strong>Hizkuntza:</strong> <?php echo htmlspecialchars($kurtsoa->hizkuntza); ?></p>
                        <p class="card-text"><strong>Iraupena:</strong> <?php echo htmlspecialchars($kurtsoa->urte_kopurua); ?> urte</p>
                        <?php if (!empty($kurtsoa->sartzeko_baldintzak)): ?>
                            <p class="card-text flex-grow-1"><strong>Baldintzak:</strong> <?php echo htmlspecialchars($kurtsoa->sartzeko_baldintzak); ?></p>
                        <?php else: ?>
                            <div class="flex-grow-1"></div>
                        <?php endif; ?>

                        <div class="mt-auto">
                            <?php if (!empty($_SESSION['erabiltzailea']) && empty($_SESSION['is_admin'])): ?>
                                <?php if ($matrikulatutakoKurtsoId === $kurtsoa->id): ?>
                                    <form action="<?php echo BASE_URL; ?>actions/matrikula-ezabatu.php" method="POST">
                                        <input type="submit" value="Desegin Matrikula" class="btn btn-danger w-100 desmatrikula-button">
                                    </form>
                                <?php elseif ($matrikulatutakoKurtsoId === null): ?>
                                    <form action="<?php echo BASE_URL; ?>actions/matrikula-sortu.php" method="POST">
                                        <input type="hidden" name="kurtso_id" value="<?php echo $kurtsoa->id; ?>">
                                        <input type="submit" value="Matrikulatu" class="btn btn-success w-100">
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col">
            <p class="alert alert-info">Une honetan ez dago kurtso erabilgarririk.</p>
        </div>
    <?php endif; ?>
</div>

<?php
include 'templates/footer.php';
?>

