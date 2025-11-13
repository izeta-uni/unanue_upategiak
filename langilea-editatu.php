<?php

require_once 'bootstrap.php';
require_once 'database/db.php';
require_once 'model/Langilea.php';

$orri_titulua = 'Langilea Editatu';
include 'templates/header.php';

// Egiaztatu administratzailea
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    $_SESSION['error_message'] = "Ez duzu baimenik orri hau ikusteko. Administratzailea izan behar duzu.";
    header('Location: index.php');
    exit;
}

// Saiotik erroreak eta aurrez bidalitako datuak berreskuratu
$erroreak = $_SESSION['form_errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? null;
unset($_SESSION['form_errors'], $_SESSION['form_data']);

// Langilearen ID-a lortu URL-tik
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$langilea = null;

if (!$id) {
    $_SESSION['error_message'] = "Langilearen ID baliogabea da.";
    header('Location: langile-zerrenda.php');
    exit;
}

try {
    $conn = konektatuDatuBasera();
    $langilea = Langilea::bilatuId($conn, $id);

    if (!$langilea) {
        $_SESSION['error_message'] = "Ez da ID hori duen langilerik aurkitu.";
        header('Location: langile-zerrenda.php');
        exit;
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = "Errorea gertatu da: " . htmlspecialchars($e->getMessage());
    header('Location: langile-zerrenda.php');
    exit;
} finally {
    if (isset($conn)) $conn->close();
}

$departamentuak = ['Gerentzia', 'Administrazioa', 'IKT', 'Enologia', 'Mahats-biltzaileak'];
?>

<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9 animate__animated animate__fadeIn">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="card-title text-center h3 mb-4">Editatu Langilea</h1>

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

                <?php if ($langilea): ?>
                    <form action="actions/langilea-eguneratu.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($langilea->id); ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="izena" class="form-label">Izena</label>
                                <input type="text" id="izena" name="izena" class="form-control" value="<?php echo htmlspecialchars($form_data['izena'] ?? $langilea->izena); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="abizena" class="form-label">Abizena</label>
                                <input type="text" id="abizena" name="abizena" class="form-control" value="<?php echo htmlspecialchars($form_data['abizena'] ?? $langilea->abizena); ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Emaila</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($form_data['email'] ?? $langilea->email); ?>" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="departamentua" class="form-label">Departamentua</label>
                                <select id="departamentua" name="departamentua" class="form-select" required>
                                    <option value="">Aukeratu departamentua</option>
                                    <?php foreach ($departamentuak as $dep): ?>
                                        <option value="<?php echo htmlspecialchars($dep); ?>" <?php echo (($form_data['departamentua'] ?? $langilea->departamentua) == $dep) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($dep); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kargua" class="form-label">Kargua</label>
                                <input type="text" id="kargua" name="kargua" class="form-control" value="<?php echo htmlspecialchars($form_data['kargua'] ?? $langilea->kargua); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="kontratazio_data" class="form-label">Kontratazio Data</label>
                            <input type="date" id="kontratazio_data" name="kontratazio_data" class="form-control" value="<?php echo htmlspecialchars($form_data['kontratazio_data'] ?? $langilea->kontratazio_data); ?>">
                        </div>
                        <hr>
                        <h5 class="mb-3">Sarbide Datuak</h5>
                        <div class="mb-3">
                            <label for="erabiltzailea" class="form-label">Erabiltzaile Izena</label>
                            <input type="text" id="erabiltzailea" name="erabiltzailea" class="form-control" value="<?php echo htmlspecialchars($form_data['erabiltzailea'] ?? $langilea->erabiltzailea); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="pasahitza" class="form-label">Pasahitz Berria (hutsik utzi ez aldatzeko)</label>
                            <input type="password" id="pasahitza" name="pasahitza" class="form-control">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin" value="1" <?php echo (($form_data['is_admin'] ?? $langilea->is_admin) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="is_admin">Administratzailea da?</label>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Gorde Aldaketak</button>
                            <a href="langile-zerrenda.php" class="btn btn-secondary">Atzera</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
