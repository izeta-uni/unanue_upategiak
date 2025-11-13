<?php

require_once 'bootstrap.php';
require_once 'database/db.php';
require_once 'model/Langilea.php';

$orri_titulua = 'Langilea Sortu';
include 'templates/header.php';

// Egiaztatu administratzailea den
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    $_SESSION['error_message'] = "Ez duzu baimenik orri hau ikusteko. Administratzailea izan behar duzu.";
    header('Location: index.php');
    exit;
}

// Saiotik erroreak eta aurrez bidalitako datuak berreskuratu (badaude)
$erroreak = $_SESSION['form_errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? [
    'izena' => '',
    'abizena' => '',
    'email' => '',
    'departamentua' => '',
    'kargua' => '',
    'kontratazio_data' => '',
    'erabiltzailea' => '',
    'pasahitza' => '',
    'is_admin' => false
];

// Erabili ondoren, saiotik ezabatu mezuak berriz ez agertzeko
unset($_SESSION['form_errors']);
unset($_SESSION['form_data']);

$departamentuak = ['Gerentzia', 'Administrazioa', 'IKT', 'Enologia', 'Mahats-biltzaileak'];
?>

<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9 animate__animated animate__fadeIn">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="card-title text-center h3 mb-4">Langile berria sortu</h1>

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

                <form action="actions/langilea-gorde.php" method="POST">
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
                        <label for="email" class="form-label">Emaila</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($form_data['email']); ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="departamentua" class="form-label">Departamentua</label>
                            <select id="departamentua" name="departamentua" class="form-select" required>
                                <option value="">Aukeratu departamentua</option>
                                <?php foreach ($departamentuak as $dep): ?>
                                    <option value="<?php echo htmlspecialchars($dep); ?>" <?php echo ($form_data['departamentua'] == $dep) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dep); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="kargua" class="form-label">Kargua</label>
                            <input type="text" id="kargua" name="kargua" class="form-control" value="<?php echo htmlspecialchars($form_data['kargua']); ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="kontratazio_data" class="form-label">Kontratazio Data</label>
                        <input type="date" id="kontratazio_data" name="kontratazio_data" class="form-control" value="<?php echo htmlspecialchars($form_data['kontratazio_data']); ?>">
                    </div>
                    <hr>
                    <h5 class="mb-3">Sarbide Datuak</h5>
                    <div class="mb-3">
                        <label for="erabiltzailea" class="form-label">Erabiltzaile Izena</label>
                        <input type="text" id="erabiltzailea" name="erabiltzailea" class="form-control" value="<?php echo htmlspecialchars($form_data['erabiltzailea']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="pasahitza" class="form-label">Pasahitza</label>
                        <input type="password" id="pasahitza" name="pasahitza" class="form-control" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin" value="1" <?php echo $form_data['is_admin'] ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_admin">Administratzailea da?</label>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Langilea Sortu</button>
                        <a href="langile-zerrenda.php" class="btn btn-secondary">Atzera</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
