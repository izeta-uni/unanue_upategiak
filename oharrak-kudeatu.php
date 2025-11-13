<?php

require_once 'bootstrap.php';
require_once 'database/db.php';
require_once 'model/Oharra.php';

// Sarbide-kontrola: Administratzaileek bakarrik sar dezakete orrialde honetara
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: index.php');
    exit;
}

$orri_titulua = 'Oharrak Kudeatu';
include 'templates/header.php';

$oharrak = [];
$conn = null;

try {
    $conn = konektatuDatuBasera();
    $oharrak = Oharra::lortuOharGuztiak($conn);
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Errorea oharrak kargatzean: " . htmlspecialchars($e->getMessage()) . "</div>";
} finally {
    if ($conn) {
        $conn->close();
    }
}

?>

<div class="text-center mb-5 animate__animated animate__fadeInDown">
    <h1>Oharrak Kudeatu</h1>
    <p class="lead">Sortu, editatu edo ezabatu segurtasun eta prestakuntza oharrak.</p>
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

<div class="row justify-content-center animate__animated animate__fadeIn">
    <div class="col-lg-10">
        <div class="d-flex justify-content-end mb-3">
            <a href="oharra-sortu.php" class="btn btn-success">Ohar Berria Sortu</a>
        </div>

        <?php if (!empty($oharrak)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover shadow-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Titulua</th>
                            <th>Edukia</th>
                            <th>Data</th>
                            <th>Ekintzak</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($oharrak as $oharra): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($oharra->id); ?></td>
                                <td><?php echo htmlspecialchars($oharra->titulua); ?></td>
                                <td><?php echo nl2br(htmlspecialchars(mb_strimwidth($oharra->edukia, 0, 100, "..."))); ?></td>
                                <td><?php echo htmlspecialchars($oharra->data); ?></td>
                                <td>
                                    <a href="oharra-editatu.php?id=<?php echo $oharra->id; ?>" class="btn btn-sm btn-primary me-2">Editatu</a>
                                    <form action="actions/oharra-ezabatu.php" method="POST" style="display:inline-block;">
                                        <input type="hidden" name="id" value="<?php echo $oharra->id; ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Ziur zaude ohar hau ezabatu nahi duzula?');">Ezabatu</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">Ez dago oharrik kudeatzeko.</div>
        <?php endif; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
