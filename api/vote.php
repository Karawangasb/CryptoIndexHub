<?php
// api/vote.php

require '../config/db.php';

// hanya terima POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// ambil data
$coin_id = isset($_POST['coin_id']) ? (int)$_POST['coin_id'] : 0;
$ip_address = $_SERVER['REMOTE_ADDR'];
$vote_date = date('Y-m-d');

// validasi sederhana
if ($coin_id <= 0) {
    http_response_code(400);
    exit('Invalid coin');
}

try {
    // simpan vote
    $stmt = $pdo->prepare("
        INSERT INTO votes (coin_id, ip_address, vote_date)
        VALUES (:coin_id, :ip_address, :vote_date)
    ");
    $stmt->execute([
        ':coin_id'   => $coin_id,
        ':ip_address' => $ip_address,
        ':vote_date' => $vote_date
    ]);

    echo '✅ Vote berhasil';

} catch (PDOException $e) {

    // duplicate vote (1 IP / hari)
    if ($e->getCode() == 23000) {
        http_response_code(409);
        echo '❌ Kamu sudah vote hari ini';
    } else {
        http_response_code(500);
        echo '❌ Error server';
    }
}
