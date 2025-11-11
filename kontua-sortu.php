<?php

require_once 'bootstrap.php';
$orri_titulua = 'Sortu Kontua';
include 'templates/header.php';

// Saiotik erroreak eta aurrez bidalitako datuak berreskuratu
$erroreak = $_SESSION['form_errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? ['emaila' => '', 'erabiltzaile_izena' => ''];

// Erabili ondoren, saiotik ezabatu
unset($_SESSION['form_errors']);
unset($_SESSION['form_data']);
?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card shadow-sm animate__animated animate__fadeIn">
            <div class="card-body p-4 ">
                <h1 class="card-title text-center h3 mb-4">Sortu Kontua</h1>

                <?php if (!empty($erroreak)): ?> 
                    <div class="alert alert-danger animate__animated animate__shakeX">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($erroreak as $errorea): ?>
                                <li><?php echo htmlspecialchars($errorea); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form id="sortuKontuaForm" action="actions/kontua-gorde.php" method="POST">
                    <div class="mb-3">
                        <label for="emaila" class="form-label">Emaila</label>
                        <input type="email" id="emaila" name="emaila" class="form-control" placeholder="zure.emaila@zubirimanteo.eus" value="<?php echo htmlspecialchars($form_data['emaila']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="erabiltzaile_izena" class="form-label">Erabiltzaile izena</label>
                        <input type="text" id="erabiltzaile_izena" name="erabiltzaile_izena" class="form-control" placeholder="erabiltzailea123" value="<?php echo htmlspecialchars($form_data['erabiltzaile_izena']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="pasahitza" class="form-label">Pasahitza</label>
                        <input type="password" id="pasahitza" name="pasahitza" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="pasahitza_errepikatu" class="form-label">Errepikatu pasahitza</label>
                        <input type="password" id="pasahitza_errepikatu" name="pasahitza_errepikatu" class="form-control" required>
                    </div>
                    <input type="submit" value="Sortu Kontua" class="btn btn-primary w-100 mt-3">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('sortuKontuaForm').addEventListener('submit', function(event) {
    var pasahitza = document.getElementById('pasahitza').value;
    var pasahitzaErrepikatu = document.getElementById('pasahitza_errepikatu').value;

    if (pasahitza !== pasahitzaErrepikatu) {
        alert('Pasahitzak ez datoz bat. Mesedez, egiaztatu.');
        event.preventDefault();
    }
});
</script>

<?php include 'templates/footer.php'; ?>