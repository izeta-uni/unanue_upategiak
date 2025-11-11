<?php

require_once 'bootstrap.php';
$orri_titulua = 'Ikaslea Editatu';
require_once 'database/db.php';
require_once 'model/Pertsona.php';
include 'templates/header.php';

// Egiaztatu administratzailea
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo '<div class="alert alert-danger"><p>Ez duzu baimenik orri hau ikusteko. Ez zara administratzaile bat.</p></div>';
    include 'templates/footer.php';
    exit;
}

// Saiotik erroreak eta aurrez bidalitako datuak berreskuratu
$erroreak = $_SESSION['form_errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? null;
unset($_SESSION['form_errors'], $_SESSION['form_data']);

// Ikaslearen ID-a lortu URL-tik
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$ikaslea = null;

if (!$id) {
    $_SESSION['error_message'] = "Ikaslearen ID baliogabea da.";
    header('Location: ikasle-zerrenda.php');
    exit;
}

try {
    $conn = konektatuDatuBasera();
    $ikaslea = Pertsona::bilatuId($conn, $id);

    if (!$ikaslea || $ikaslea->rola !== 'ikasle') {
        $_SESSION['error_message'] = "Ez da ID hori duen ikaslerik aurkitu.";
        header('Location: ikasle-zerrenda.php');
        exit;
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = "Errorea gertatu da: " . $e->getMessage();
    header('Location: ikasle-zerrenda.php');
    exit;
} finally {
    if (isset($conn)) $conn->close();
}
?>

<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9 animate__animated animate__fadeIn">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="card-title text-center h3 mb-4">Editatu Ikaslea</h1>

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

                <?php if ($ikaslea): ?>
                    <form action="actions/ikaslea-eguneratu.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($ikaslea->id); ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="izena" class="form-label">Izena</label>
                                <input type="text" id="izena" name="izena" class="form-control" value="<?php echo htmlspecialchars($form_data['izena'] ?? $ikaslea->izena); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="abizena" class="form-label">Abizena</label>
                                <input type="text" id="abizena" name="abizena" class="form-control" value="<?php echo htmlspecialchars($form_data['abizena'] ?? $ikaslea->abizena); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="emaila" class="form-label">Emaila</label>
                            <input type="email" id="emaila" name="emaila" class="form-control" value="<?php echo htmlspecialchars($form_data['emaila'] ?? $ikaslea->emaila); ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nan" class="form-label">NAN</label>
                                <input type="text" id="nan" name="nan" class="form-control" value="<?php echo htmlspecialchars($form_data['nan'] ?? $ikaslea->nan); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="jaiotze_data" class="form-label">Jaiotze Data</label>
                                <input type="date" id="jaiotze_data" name="jaiotze_data" class="form-control" value="<?php echo htmlspecialchars($form_data['jaiotze_data'] ?? $ikaslea->jaiotze_data ?? ''); ?>">
                            </div>
                        </div>
                        <input type="submit" name="submit" value="Gorde Aldaketak" class="btn btn-primary w-100 mt-3">
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
