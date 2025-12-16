<?php
require_once __DIR__ . "/../inc/db.php";
require_once __DIR__ . "/../inc/helpers.php";

$action = $_GET['action'] ?? 'list';

try{
    if($action === 'list'){
        $stmt = $pdo->query("SELECT ProductID, ProductName, HargaSatuan FROM produk ORDER BY ProductID DESC");
        json_response(['rows' => $stmt->fetchAll()]);
    }

    $body = read_json_body();

    if($action === 'create'){
        $name = trim((string)($body['ProductName'] ?? ''));
        $harga = (int)($body['HargaSatuan'] ?? 0);
        if($name === '') json_response(['error' => 'ProductName wajib.'], 400);

        $stmt = $pdo->prepare("INSERT INTO produk (ProductName, HargaSatuan) VALUES (:n,:h)");
        $stmt->execute([':n'=>$name, ':h'=>$harga]);

        json_response(['ok'=>true, 'ProductID'=>$pdo->lastInsertId()]);
    }

    if($action === 'update'){
        $id = (int)($body['ProductID'] ?? 0);
        $name = trim((string)($body['ProductName'] ?? ''));
        $harga = (int)($body['HargaSatuan'] ?? 0);
        if($id <= 0) json_response(['error'=>'ProductID tidak valid.'], 400);
        if($name === '') json_response(['error'=>'ProductName wajib.'], 400);

        $stmt = $pdo->prepare("UPDATE produk SET ProductName=:n, HargaSatuan=:h WHERE ProductID=:id");
        $stmt->execute([':n'=>$name, ':h'=>$harga, ':id'=>$id]);

        json_response(['ok'=>true]);
    }

    if($action === 'delete'){
        $id = (int)($body['ProductID'] ?? 0);
        if($id <= 0) json_response(['error'=>'ProductID tidak valid.'], 400);

        // catatan: jika produk sudah dipakai di detailtransaksi, FK akan menolak delete
        $stmt = $pdo->prepare("DELETE FROM produk WHERE ProductID=:id");
        $stmt->execute([':id'=>$id]);

        json_response(['ok'=>true]);
    }

    json_response(['error'=>'Action tidak dikenal.'], 400);
}catch(PDOException $e){
    json_response(['error'=>$e->getMessage()], 500);
}
