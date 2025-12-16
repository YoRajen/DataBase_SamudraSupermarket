<?php
require_once __DIR__ . "/../inc/db.php";
require_once __DIR__ . "/../inc/helpers.php";

$action = $_GET['action'] ?? 'list';

try{
    if($action === 'list'){
        $q = trim((string)($_GET['q'] ?? ''));
        $from = trim((string)($_GET['from'] ?? ''));
        $to = trim((string)($_GET['to'] ?? ''));

        $where = [];
        $params = [];

        if($q !== ''){
            $where[] = "t.TransID LIKE :q";
            $params[':q'] = '%' . $q . '%';
        }
        if($from !== ''){
            $where[] = "DATE(t.Tanggal) >= :from";
            $params[':from'] = $from;
        }
        if($to !== ''){
            $where[] = "DATE(t.Tanggal) <= :to";
            $params[':to'] = $to;
        }

        $sql = "
            SELECT t.TransID, t.Tanggal, t.Total, t.Potongan, t.Tunai, t.Kembali,
                   t.KasirID, k.KasirNama
            FROM transaksi t
            LEFT JOIN kasir k ON k.KasirID = t.KasirID
        ";
        if(count($where) > 0){
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY t.Tanggal DESC LIMIT 200";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        json_response(['rows' => $stmt->fetchAll()]);
    }

    if($action === 'get'){
        $transid = trim((string)($_GET['transid'] ?? ''));
        if($transid === '') json_response(['error'=>'transid wajib.'], 400);

        // transaksi + kasir
        $stmt = $pdo->prepare("
            SELECT t.TransID, t.Tanggal, t.Total, t.Potongan, t.Tunai, t.Kembali,
                   t.KasirID, k.KasirNama
            FROM transaksi t
            LEFT JOIN kasir k ON k.KasirID = t.KasirID
            WHERE t.TransID = :id
            LIMIT 1
        ");
        $stmt->execute([':id'=>$transid]);
        $t = $stmt->fetch();
        if(!$t) json_response(['error'=>'Transaksi tidak ditemukan.'], 404);

        // detail - coba pakai stored procedure GetDetailTransaksi2 (kalau ada)
        $items = [];
        try{
            $call = $pdo->prepare("CALL GetDetailTransaksi2(:id)");
            $call->execute([':id'=>$transid]);
            $items = $call->fetchAll();
            $call->closeCursor();
        }catch(PDOException $e){
            // fallback manual (untuk server yang sensitif terhadap case nama tabel)
            $stmt2 = $pdo->prepare("
                SELECT d.DetailID, d.TransID, p.ProductName, d.Qty, d.Subtotal
                FROM detailtransaksi d
                INNER JOIN produk p ON p.ProductID = d.ProductID
                WHERE d.TransID = :id
                ORDER BY d.DetailID ASC
            ");
            $stmt2->execute([':id'=>$transid]);
            $items = $stmt2->fetchAll();
        }

        // hitung ppn lewat function HitungPPN jika tersedia
        $ppn = 0;
        try{
            $dpp = max(((int)$t['Total']) - ((int)$t['Potongan']), 0);
            $stmt3 = $pdo->prepare("SELECT HitungPPN(:nominal) AS ppn");
            $stmt3->execute([':nominal' => $dpp]);
            $ppn = (int)($stmt3->fetch()['ppn'] ?? 0);
        }catch(PDOException $e){
            $ppn = 0;
        }

        json_response(['transaksi'=>$t, 'items'=>$items, 'ppn'=>$ppn]);
    }

    if($action === 'create'){
        $body = read_json_body();
        $kasirId = trim((string)($body['KasirID'] ?? ''));
        $potongan = (int)($body['Potongan'] ?? 0);
        $tunai = (int)($body['Tunai'] ?? 0);
        $items = $body['items'] ?? [];

        if($kasirId === '') json_response(['error'=>'KasirID wajib.'], 400);
        if(!is_array($items) || count($items) === 0) json_response(['error'=>'items wajib & tidak boleh kosong.'], 400);

        // validasi kasir
        $stKasir = $pdo->prepare("SELECT KasirID FROM kasir WHERE KasirID=:id");
        $stKasir->execute([':id'=>$kasirId]);
        if(!$stKasir->fetch()) json_response(['error'=>'KasirID tidak ditemukan.'], 400);

        $pdo->beginTransaction();

        $transId = generate_trans_id($pdo, $kasirId);

        // insert transaksi (Total & Kembali akan diupdate setelah detail)
        $stIns = $pdo->prepare("
            INSERT INTO transaksi (TransID, KasirID, Tanggal, Total, Potongan, Tunai, Kembali)
            VALUES (:tid, :kid, NOW(), 0, :pot, :tun, 0)
        ");
        $stIns->execute([
            ':tid'=>$transId,
            ':kid'=>$kasirId,
            ':pot'=>$potongan,
            ':tun'=>$tunai,
        ]);

        // insert detail items
        $stProd = $pdo->prepare("SELECT ProductID, HargaSatuan FROM produk WHERE ProductID=:pid");
        $stDet  = $pdo->prepare("
            INSERT INTO detailtransaksi (TransID, ProductID, Qty, HargaSatuan, Subtotal)
            VALUES (:tid, :pid, :qty, :harga, NULL)
        ");

        foreach($items as $it){
            $pid = (int)($it['ProductID'] ?? 0);
            $qty = (int)($it['Qty'] ?? 0);
            if($pid <= 0 || $qty <= 0){
                $pdo->rollBack();
                json_response(['error'=>'Item tidak valid (ProductID/Qty).'], 400);
            }

            $stProd->execute([':pid'=>$pid]);
            $p = $stProd->fetch();
            if(!$p){
                $pdo->rollBack();
                json_response(['error'=>"Produk ID {$pid} tidak ditemukan."], 400);
            }

            $harga = (int)$p['HargaSatuan'];
            $stDet->execute([
                ':tid'=>$transId,
                ':pid'=>$pid,
                ':qty'=>$qty,
                ':harga'=>$harga
            ]);
        }

        // hitung total berdasarkan detail (Subtotal dihitung trigger trg_subtotal)
        $stSum = $pdo->prepare("SELECT COALESCE(SUM(Subtotal),0) AS total FROM detailtransaksi WHERE TransID=:tid");
        $stSum->execute([':tid'=>$transId]);
        $total = (int)($stSum->fetch()['total'] ?? 0);

        $bayar = max($total - $potongan, 0);
        $kembali = $tunai - $bayar;

        $stUpd = $pdo->prepare("UPDATE transaksi SET Total=:total, Kembali=:kembali WHERE TransID=:tid");
        $stUpd->execute([':total'=>$total, ':kembali'=>$kembali, ':tid'=>$transId]);

        $pdo->commit();

        json_response(['ok'=>true, 'TransID'=>$transId, 'Total'=>$total, 'Bayar'=>$bayar, 'Kembali'=>$kembali]);
    }

    json_response(['error'=>'Action tidak dikenal.'], 400);

}catch(PDOException $e){
    if($pdo->inTransaction()) $pdo->rollBack();
    json_response(['error'=>$e->getMessage()], 500);
}
