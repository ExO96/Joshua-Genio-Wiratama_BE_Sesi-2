<?php
function cariProduk(&$produk, $kode) {
    foreach ($produk as &$item) {
        if ($item['kode'] === $kode) {
            return $item;
        }
    }
    echo "Err: produk dengan kode: {$kode} tidak ditemukan.<br>\n";
    return 0;
}

function hitungSubtotal($harga, $jumlah) {
    return $harga * $jumlah;
}

function hitungDiskon($total) {
    if ($total >= 100000) {
        return $total * 0.10;
    } elseif ($total >= 50000) {
        return $total * 0.05;
    } else {
        return 0;
    }
}

function hitungPajak($total, $persen = 11) {
    return $total * ($persen / 100);
}

function kurangiStok(&$produk, $kode, $jumlah) {
    foreach ($produk as &$item) {
        if ($item['kode'] === $kode) {
            $item['stok'] -= $jumlah;
            return;
        }
    }
}

function formatRupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}

function buatStrukBelanja($transaksi, &$array_produk) {
    echo "================================<br>\n";
    echo "         MINIMARKET SEJAHTERA<br>\n";
    echo "================================<br>\n";
    echo "Tanggal: " . date("d F Y") . "<br><br>\n";

    $subtotal = 0;

    foreach ($transaksi as $t) {
        $produk = cariProduk($array_produk, $t['kode']);
        if ($produk) {
            $sub = hitungSubtotal($produk['harga'], $t['jumlah']);
            $subtotal += $sub;

            echo "{$produk['nama']}<br>\n";
            echo formatRupiah($produk['harga']) . " x {$t['jumlah']} = " 
                . formatRupiah($sub) . "<br><br>\n";

            kurangiStok($array_produk, $t['kode'], $t['jumlah']);
        }
    }

    $diskon = hitungDiskon($subtotal);
    $setelahDiskon = $subtotal - $diskon;
    $pajak = hitungPajak($setelahDiskon);
    $total = $setelahDiskon + $pajak;

    echo "--------------------------------<br>\n";
    echo "Subtotal            = " . formatRupiah($subtotal) . "<br>\n";
    echo "Diskon              = " . formatRupiah($diskon) . "<br>\n";
    echo "Subtotal stl diskon = " . formatRupiah($setelahDiskon) . "<br>\n";
    echo "PPN (11%)           = " . formatRupiah($pajak) . "<br>\n";
    echo "--------------------------------<br>\n";
    echo "<b>TOTAL BAYAR         = " . formatRupiah($total) . "</b>\n";
    echo "<br>\n================================<br><br>\n";

    echo "Status Stok Setelah Transaksi:<br>\n";
    foreach ($array_produk as $p) {
        echo "- {$p['nama']}: {$p['stok']} pcs<br>\n";
    }
    echo "<br>\n================================<br>\n";
    echo "     Terimakasih atas kunjungan anda<br>\n";
    echo "================================<br>\n";
}


// ===== isi produk ======
$produk = [
    ["kode" => "A001", "nama" => "Indomie Goreng", "harga" => 3500, "stok" => 100],
    ["kode" => "A002", "nama" => "Teh Botol Sosro", "harga" => 4000, "stok" => 50],
    ["kode" => "A003", "nama" => "Susu Ultra Milk", "harga" => 12000, "stok" => 30],
    ["kode" => "A004", "nama" => "Roti Tawar Sari Roti", "harga" => 15000, "stok" => 20],
    ["kode" => "A005", "nama" => "Minyak Goreng Bimoli 1L", "harga" => 18000, "stok" => 15]
];

// ===== main() =====
echo "Selamat datang di Toko Serba Ada! <br><br>\n";
echo "Berikut daftar produk yang tersedia: <br>\n";

foreach ($produk as $item) {
    echo "{$item['kode']} - {$item['nama']} - Harga: " 
        . formatRupiah($item['harga']) . " - Stok: {$item['stok']} <br>\n";
}

echo "<br>\nSilahkan masukkan kode dan produk yang ingin anda beli: <br><br>\n\n";

// ====== transaksi pembeli ======
$transaksi = [
    ["kode" => "A001", "jumlah" => 5],
    ["kode" => "A003", "jumlah" => 2],
    ["kode" => "A004", "jumlah" => 1]
];

buatStrukBelanja($transaksi, $produk);

?>