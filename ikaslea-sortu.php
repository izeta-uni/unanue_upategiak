<?php

require_once 'bootstrap.php';
$orri_titulua = 'Ikaslea Sortu';
include 'templates/header.php';

// Egiaztatu administratzailea den
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo '<div class="alert alert-danger"><p>Ez duzu baimenik orri hau ikusteko. Ez zara administratzaile bat.</p></div>';
    include 'templates/footer.php';
    exit;
}

// Saiotik erroreak eta aurrez bidalitako datuak berreskuratu (badaude)
$erroreak = $_SESSION['form_errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? ['izena' => '','abizena' => '','jaiotze_data' => '','nan' => '','emaila' => ''];

// Erabili ondoren, saiotik ezabatu mezuak berriz ez agertzeko
unset($_SESSION['form_errors']);
unset($_SESSION['form_data']);
?>

<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9 animate__animated animate__fadeIn">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="card-title text-center h3 mb-4">Ikasle berria sortu</h1>

                <?php if (!empty($erroreak)): ?>
                    <div class="alert alert-danger animate__animated animate__shakeX">
                        <strong class="d-block mb-2">Erroreak aurkitu dira:</strong>
                        <ul class="mb-0 ps-3">
                            <?php foreach ($erroreak as $errorea): ?>
                                <li><?php echo htmlspecialchars($errorea); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="actions/ikaslea-gorde.php" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="izena" class="form-label">Izena</label>
                            <input type="text" id="izena" name="izena" class="form-control" value="<?php echo htmlspecialchars($form_data['izena']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="abizena" class="form-label">Abizena</label>
                            <input type="text" id="abizena" name="abizena" class="form-control" value="<?php echo htmlspecialchars($form_data['abizena']); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="emaila" class="form-label">Emaila</label>
                        <input type="email" id="emaila" name="emaila" class="form-control" value="<?php echo htmlspecialchars($form_data['emaila']); ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nan" class="form-label">NAN</label>
                            <input type="text" id="nan" name="nan" class="form-control" placeholder="12345678A" value="<?php echo htmlspecialchars($form_data['nan']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jaiotze_data" class="form-label">Jaiotze Data</label>
                            <input type="date" id="jaiotze_data" name="jaiotze_data" class="form-control" value="<?php echo htmlspecialchars($form_data['jaiotze_data']); ?>">
                        </div>
                    </div>
                    <input type="submit" name="submit" value="Sortu Ikaslea" class="btn btn-primary w-100 mt-3">
                </form>
            </div>
        </div>
    </div>
</div>


<?php include 'templates/footer.php'; ?>
