<?php
require 'config/db.php';

// ambil daftar coin (hanya yang approved)
$stmt = $pdo->query("
    SELECT id, name, symbol, total_votes
    FROM coins
    WHERE status = 'approved'
    ORDER BY total_votes DESC
");
$coins = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CryptoIndexHub</title>
</head>
<body>

<h1>🚀 CryptoIndexHub</h1>
<p>Vote coin favorit kamu (1x per hari)</p>

<hr>

<?php if (empty($coins)): ?>
    <p>Belum ada coin.</p>
<?php else: ?>

    <?php foreach ($coins as $coin): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <h3>
                <?= htmlspecialchars($coin['name']) ?>
                (<?= htmlspecialchars($coin['symbol']) ?>)
            </h3>

            <p>👍 Total Vote: <?= (int)$coin['total_votes'] ?></p>

            <form method="POST" action="api/vote.php">
                <input type="hidden" name="coin_id" value="<?= $coin['id'] ?>">
                <button type="submit">Vote</button>
            </form>
        </div>
    <?php endforeach; ?>

<?php endif; ?>

</body>
</html>
