<?php

require_once 'bootstrap.php';
require_once 'database/db.php';
require_once 'model/Oharra.php';

// Sarbide-kontrola: Administratzaileek bakarrik sar dezakete orrialde honetara
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
    exit;
}

$orri_titulua = 'Oharra Editatu';
include 'templates/header.php';

$erroreak = [];
$oharra = null;
$oharra_id = $_GET['id'] ?? null;

// Oharraren IDa baliozkoa dela egiaztatu
if (!$oharra_id || !is_numeric($oharra_id)) {
    $_SESSION['error_message'] = "Oharraren IDa ez da baliozkoa.";
    header('Location: oharrak-kudeatu.php');
    exit;
}

try {
    $conn = konektatuDatuBasera();
    $oharra = Oharra::bilatuId($conn, (int)$oharra_id);

    if (!$oharra) {
        $_SESSION['error_message'] = "Ez da aurkitu oharra ID horrekin.";
        header('Location: oharrak-kudeatu.php');
        exit;
    }

    // Formularioa bidali bada
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $oharra->titulua = trim($_POST['titulua'] ?? '');
        $oharra->edukia = trim($_POST['edukia'] ?? '');

        if (empty($oharra->titulua)) {
            $erroreak[] = "Titulua derrigorrezkoa da.";
        }
        if (empty($oharra->edukia)) {
            $erroreak[] = "Edukia derrigorrezkoa da.";
        }

        if (empty($erroreak)) {
            if ($oharra->gorde($conn)) {
                $_SESSION['success_message'] = "Oharra ondo eguneratu da.";
                header('Location: oharrak-kudeatu.php');
                exit;
            } else {
                $erroreak[] = "Errorea oharra eguneratzean.";
            }
        }
    }

} catch (Exception $e) {
    $erroreak[] = "Errorea datu-basearekin: " . htmlspecialchars($e->getMessage());
} finally {
    if ($conn) {
        $conn->close();
    }
}

?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm animate__animated animate__fadeIn">
            <div class="card-body p-4">
                <h1 class="card-title text-center h3 mb-4">Oharra Editatu</h1>

                <?php if (!empty($erroreak)): ?>
                    <div class="alert alert-danger animate__animated animate__shakeX ">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($erroreak as $errorea): ?>
                                <li><?php echo htmlspecialchars($errorea); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="oharra-editatu.php?id=<?php echo htmlspecialchars($oharra->id); ?>" method="POST">
                    <div class="mb-3">
                        <label for="titulua" class="form-label">Titulua</label>
                        <input type="text" id="titulua" name="titulua" class="form-control" value="<?php echo htmlspecialchars($oharra->titulua); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="edukia" class="form-label">Edukia</label>
                        <textarea id="edukia" name="edukia" class="form-control" rows="5" required><?php echo htmlspecialchars($oharra->edukia); ?></textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Gorde Aldaketak</button>
                        <a href="oharrak-kudeatu.php" class="btn btn-secondary">Atzera</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
