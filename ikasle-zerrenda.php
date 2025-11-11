<?php
require_once 'bootstrap.php';
require_once 'database/db.php';
require_once 'model/Pertsona.php';

$orri_titulua = 'Ikasle Zerrenda';
include 'templates/header.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo '<div class="alert alert-danger"><p>Ez duzu baimenik orri hau ikusteko. Ez zara administratzaile bat.</p></div>';
    include 'templates/footer.php';
    exit;
}

$ikasleak = [];
try {
    $conn = konektatuDatuBasera();
    $ikasleak = Pertsona::lortuIkasleGuztiak($conn);
    $conn->close();
} catch (Exception $e) {
    $_SESSION['error_message'] = "Ezin izan dira ikasleak kargatu: " . $e->getMessage();
}
?>

<h1 class="animate__animated animate__fadeInDown">Ikasle zerrenda</h1>

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
    <a class="btn btn-primary" href="./ikaslea-sortu.php">Ikaslea sortu</a>
</div>

<?php if (!empty($ikasleak)): ?>
    <div class="table-responsive animate__animated animate__fadeIn">
        <table class="table table-hover border">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Izena</th>
                    <th>Abizena</th>
                    <th>Jaiotze Data</th>
                    <th>NAN</th>
                    <th>Emaila</th>
                    <th>Editatu</th>
                    <th>Ezabatu</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ikasleak as $ikaslea): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ikaslea->id); ?></td>
                        <td><?php echo htmlspecialchars($ikaslea->izena); ?></td>
                        <td><?php echo htmlspecialchars($ikaslea->abizena); ?></td>
                        <td><?php echo htmlspecialchars($ikaslea->jaiotze_data); ?></td>
                        <td><?php echo htmlspecialchars($ikaslea->nan); ?></td>
                        <td><?php echo htmlspecialchars($ikaslea->emaila); ?></td>
                        <td class="edit">
                            <a href="ikaslea-editatu.php?id=<?php echo $ikaslea->id; ?>" title="Editatu">🖊️</a>
                        </td> 
                        <td class="delete">
                            <a href="actions/ikaslea-ezabatu.php?id=<?php echo $ikaslea->id; ?>" onclick="return confirm('Ziur zaude ikasle hau ezabatu nahi duzula?');" title="Ezabatu">🗑️</a>
                        </td> 
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        Ez dago ikaslerik datu basean. <a href="ikaslea-sortu.php" class="alert-link">Sortu bat!</a>
    </div>
<?php endif; ?>

<?php
include 'templates/footer.php';
?>