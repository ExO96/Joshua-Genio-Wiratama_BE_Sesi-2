<?php

// ===== FUNCTION =====

function cariProduk(&$produk, $kode) {
    foreach ($produk as &$item) {
        if ($item['kode'] === $kode) {
            return $item;
        }
    }
    return null;
}

function hitungSubtotal($harga, $jumlah) {
    return $harga * $jumlah;
}

function hitungDiskon($total) {
    if ($total >= 100000) return $total * 0.10;
    if ($total >= 50000)  return $total * 0.05;
    return 0;
}

function hitungPajak($total, $persen = 11) {
    return $total * ($persen / 100);
}

function kurangiStok(&$produk, $kode, $jumlah) {
    foreach ($produk as &$item) {
        if ($item['kode'] === $kode) {
            $item['stok'] -= $jumlah;
        }
    }
}

function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

// ===== DATA PRODUK =====

$produk = [
    ["kode" => "A001", "nama" => "Indomie Goreng", "harga" => 3500,  "stok" => 100],
    ["kode" => "A002", "nama" => "Teh Botol Sosro", "harga" => 4000,  "stok" => 50],
    ["kode" => "A003", "nama" => "Susu Ultra Milk", "harga" => 12000, "stok" => 30],
    ["kode" => "A004", "nama" => "Roti Tawar Sari Roti","harga" => 15000, "stok" => 20],
    ["kode" => "A005", "nama" => "Minyak Goreng Bimoli","harga" => 18000, "stok" => 15]
];

// ===== SESI TRANSAKSI =====

session_start();

if (!isset($_SESSION['transaksi'])) {
    $_SESSION['transaksi'] = [];
}

// Add product to transaction

if (isset($_POST['tambah'])) {
    $kode = strtoupper($_POST['kode']);
    $jumlah = intval($_POST['jumlah']);

    $item = cariProduk($produk, $kode);
    if ($item !== null) {
        $_SESSION['transaksi'][] = ["kode" => $kode, "jumlah" => $jumlah];
    }
}

// Reset tcaransaction

if (isset($_POST['reset'])) {
    $_SESSION['transaksi'] = [];
}

// ===== HTML =====
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mini Market</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">

    <h2 class="text-center mb-4">🛒 Mini Market nyooO</h2>

    <!-- FORM TAMBAH PRODUK -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Kode Produk</label>
                        <input type="text" class="form-control" name="kode" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Jumlah</label>
                        <input type="number" class="form-control" name="jumlah" min="1" required>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" name="tambah" class="btn btn-primary w-100">
                            Tambah Produk
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- TABEL PRODUK -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">📦 Daftar Produk</div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Stok</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($produk as $p): ?>
                <tr>
                    <td><?= $p['kode'] ?></td>
                    <td><?= $p['nama'] ?></td>
                    <td><?= formatRupiah($p['harga']) ?></td>
                    <td><?= $p['stok'] ?></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <!-- DAFTAR TRANSAKSI -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">📝 Daftar Belanja</div>
        <div class="card-body">

            <?php if (empty($_SESSION['transaksi'])): ?>
                <p class="text-muted">Belum ada produk yang ditambahkan.</p>
            <?php else: ?>
                <ul class="list-group mb-3">
                    <?php foreach ($_SESSION['transaksi'] as $t): ?>
                        <li class="list-group-item">
                            <?= $t['kode'] ?> — Jumlah: <?= $t['jumlah'] ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <form method="POST">
                    <button type="submit" name="reset" class="btn btn-danger">Reset</button>
                    <a href="?checkout=1" class="btn btn-success float-end">Checkout</a>
                </form>
            <?php endif; ?>

        </div>
    </div>

    <?php

    // ===== STRUK =====

    if (isset($_GET['checkout'])):

        $subtotal = 0;

        foreach ($_SESSION['transaksi'] as $t) {
            $prod = cariProduk($produk, $t['kode']);
            if ($prod !== null) {
                $sub = hitungSubtotal($prod['harga'], $t['jumlah']);
                $subtotal += $sub;
            }
        }

        $diskon = hitungDiskon($subtotal);
        $setelahDiskon = $subtotal - $diskon;
        $pajak = hitungPajak($setelahDiskon);
        $total = $setelahDiskon + $pajak;
    ?>

    <div class="card shadow-sm mb-5">
        <div class="card-header bg-success text-white">🧾 Struk Pembelian</div>
        <div class="card-body">

            <?php foreach ($_SESSION['transaksi'] as $t): 
                $prod = cariProduk($produk, $t['kode']);
                $sub = hitungSubtotal($prod['harga'], $t['jumlah']);
            ?>

            <p>
                <strong><?= $prod['nama'] ?></strong> <br>
                <?= formatRupiah($prod['harga']) ?> x <?= $t['jumlah'] ?>
                = <strong><?= formatRupiah($sub) ?></strong>
            </p>

            <?php endforeach ?>

            <hr>

            <p>Subtotal: <strong><?= formatRupiah($subtotal) ?></strong></p>
            <p>Diskon: <strong><?= formatRupiah($diskon) ?></strong></p>
            <p>PPN (11%): <strong><?= formatRupiah($pajak) ?></strong></p>

            <h4 class="mt-4">TOTAL BAYAR: <?= formatRupiah($total) ?></h4>
        </div>
    </div>

    <?php endif; ?>

</div>

</body>
</html>
