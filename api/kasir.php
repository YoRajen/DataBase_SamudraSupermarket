<?php
require_once __DIR__ . "/../inc/db.php";
require_once __DIR__ . "/../inc/helpers.php";

$action = $_GET['action'] ?? 'list';

try{
    if($action === 'list'){
        $stmt = $pdo->query("SELECT KasirID, KasirNama FROM kasir ORDER BY KasirID ASC");
        json_response(['rows' => $stmt->fetchAll()]);
    }
    json_response(['error'=>'Action tidak dikenal.'], 400);
}catch(PDOException $e){
    json_response(['error'=>$e->getMessage()], 500);
}
