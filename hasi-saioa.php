<?php

require_once 'bootstrap.php';
require_once 'database/db.php';
require_once 'model/Kontua.php';
require_once 'model/Pertsona.php';

$erroreak = [];
$erabiltzaile_izena = '';

if (isset($_SESSION['erabiltzailea'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $erabiltzaile_izena = trim($_POST['erabiltzaile_izena'] ?? '');
    $pasahitza = trim($_POST['pasahitza'] ?? '');

    if (empty($erabiltzaile_izena)) $erroreak[] = "Erabiltzaile izena derrigorrezkoa da.";
    if (empty($pasahitza)) $erroreak[] = "Pasahitza derrigorrezkoa da.";

    if (empty($erroreak)) {
        try {
            $conn = konektatuDatuBasera();
            $kontua = Kontua::bilatuErabiltzaileIzenez($conn, $erabiltzaile_izena);

            if ($kontua && $kontua->egiaztatuPasahitza($pasahitza)) {
                
                $_SESSION['erabiltzailea'] = $kontua->erabiltzaile_izena;
                $_SESSION['pertsona_id'] = $kontua->pertsona_id; 
                $_SESSION['is_admin'] = false;

                if ($kontua->pertsona_id) {
                    $pertsona = Pertsona::bilatuId($conn, $kontua->pertsona_id);
                    if ($pertsona && $pertsona->rola === 'admin') {
                        $_SESSION['is_admin'] = true;
                    }
                }

                if ($_SESSION['is_admin']) {
                    header("Location: ikasle-zerrenda.php");
                } else {
                    header("Location: index.php");
                }
                exit;
            } else {
                $erroreak[] = "Erabiltzaile izena edo pasahitza ez da zuzena.";
            }
        } catch (Exception $e) {
            $erroreak[] = "Errorea datu basearekin konektatzean: " . $e->getMessage();
        } finally {
            if (isset($conn)) $conn->close();
        }
    }
}

$orri_titulua = 'Hasi Saioa';
include 'templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-5 col-md-7">
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success animate__animated animate__flipInX"><p class="mb-0"><?php echo $_SESSION['success_message']; ?></p></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <div class="card shadow-sm animate__animated animate__fadeIn">
            <div class="card-body p-4">
                <h1 class="card-title text-center h3 mb-4">Hasi Saioa</h1>

                <?php if (!empty($erroreak)): ?>
                    <div class="alert alert-danger animate__animated animate__shakeX ">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($erroreak as $errorea): ?>
                                <li><?php echo htmlspecialchars($errorea); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="hasi-saioa.php" method="POST">
                    <div class="mb-3">
                        <label for="erabiltzaile_izena" class="form-label">Erabiltzaile izena</label>
                        <input type="text" id="erabiltzaile_izena" name="erabiltzaile_izena" class="form-control" value="<?php echo htmlspecialchars($erabiltzaile_izena); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="pasahitza" class="form-label">Pasahitza</label>
                        <input type="password" id="pasahitza" name="pasahitza" class="form-control" required>
                    </div>
                    <input type="submit" value="Hasi Saioa" class="btn btn-primary w-100 mt-3">
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
