<?php
require_once 'bootstrap.php';
require_once 'database/db.php';
require_once 'model/Langilea.php';

$orri_titulua = 'Langile Zerrenda';
include 'templates/header.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    $_SESSION['error_message'] = "Ez duzu baimenik orri hau ikusteko. Administratzailea izan behar duzu.";
    header('Location: index.php');
    exit;
}

$langileak = [];
try {
    $conn = konektatuDatuBasera();
    $langileak = Langilea::lortuLangileGuztiak($conn);
} catch (Exception $e) {
    $_SESSION['error_message'] = "Ezin izan dira langileak kargatu: " . htmlspecialchars($e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>

<h1 class="animate__animated animate__fadeInDown">Langile zerrenda</h1>

<div>
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success animate__animated animate__flipInX"><p class="mb-0"><?php echo $_SESSION['success_message']; ?></p></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger animate__animated animate__shakeX"><p class="mb-0"><?php echo $_SESSION['error_message']; ?></p></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
</div>

<div class="d-flex justify-content-end mb-3 animate__animated animate__fadeIn">
    <a class="btn btn-primary" href="./langilea-sortu.php">Langilea sortu</a>
</div>

<?php if (!empty($langileak)): ?>
    <div class="table-responsive animate__animated animate__fadeIn">
        <table class="table table-hover border">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Izena</th>
                    <th>Abizena</th>
                    <th>Email</th>
                    <th>Departamentua</th>
                    <th>Kontratazio Data</th>
                    <th>Erabiltzailea</th>
                    <th>Admin</th>
                    <th>Ekintzak</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($langileak as $langilea): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($langilea->id); ?></td>
                        <td><?php echo htmlspecialchars($langilea->izena); ?></td>
                        <td><?php echo htmlspecialchars($langilea->abizena); ?></td>
                        <td><?php echo htmlspecialchars($langilea->email); ?></td>
                        <td><?php echo htmlspecialchars($langilea->departamentua); ?></td>
                        <td><?php echo htmlspecialchars($langilea->kontratazio_data); ?></td>
                        <td><?php echo htmlspecialchars($langilea->erabiltzailea); ?></td>
                        <td><?php echo $langilea->is_admin ? 'Bai' : 'Ez'; ?></td>
                        <td>
                            <a href="langilea-editatu.php?id=<?php echo $langilea->id; ?>" class="btn btn-sm btn-primary me-2">Editatu</a>
                            <form action="actions/langilea-ezabatu.php" method="POST" style="display:inline-block;">
                                <input type="hidden" name="id" value="<?php echo $langilea->id; ?>">
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Ziur zaude langile hau ezabatu nahi duzula?');">Ezabatu</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        Ez dago langilerik datu basean. <a href="langilea-sortu.php" class="alert-link">Sortu bat!</a>
    </div>
<?php endif; ?>

<?php
include 'templates/footer.php';
?>