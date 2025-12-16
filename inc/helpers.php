<?php
// inc/helpers.php

function json_response($data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function read_json_body(): array {
    $raw = file_get_contents('php://input');
    if (!$raw) return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function esc(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function rupiah($number): string {
    if ($number === null || $number === '') return '0';
    return number_format((float)$number, 0, ',', '.');
}

// Format TransID mirip contoh: 004-YYMMDD### (### = urutan per hari)
function generate_trans_id(PDO $pdo, string $kasirId): string {
    $datePart = date('ymd'); // YYMMDD
    $prefix = $kasirId . '-' . $datePart;

    $stmt = $pdo->prepare("SELECT COUNT(*) AS c FROM transaksi WHERE TransID LIKE :p");
    $stmt->execute([':p' => $prefix . '%']);
    $count = (int)($stmt->fetch()['c'] ?? 0);

    $seq = str_pad((string)($count + 1), 3, '0', STR_PAD_LEFT);
    return $prefix . $seq;
}
