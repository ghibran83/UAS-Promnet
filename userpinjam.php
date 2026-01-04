<?php

require_once 'functions/functions.php';
require_once 'functions/funcpeminjaman.php';

// Cek login dulu sebelum loading header (biar redirect jalan)
requireUserLogin();

require_once 'header_user.php';

// Ngurusin form yang dikirim
$pesan = '';
$tipe_pesan = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_barang = (int)($_POST['id_barang'] ?? 0);
    $jumlah = (int)($_POST['jumlah'] ?? 0);
    $tgl_pinjam = $_POST['tgl_pinjam'] ?? '';
    $tgl_kembali = $_POST['tgl_kembali'] ?? '';
    
    // Cek isian bener ga
    if ($id_barang <= 0 || $jumlah <= 0 || empty($tgl_pinjam) || empty($tgl_kembali)) {
        $pesan = 'Isi yang bener dong bos!';
        $tipe_pesan = 'danger';
    } else {
        // Bikin nota pinjem
        $hasil = buatPeminjaman($_SESSION['user_id'], $id_barang, $jumlah, $tgl_pinjam, $tgl_kembali);
        $pesan = $hasil['pesan'];
        $tipe_pesan = $hasil['status'] ? 'success' : 'danger';
        if ($hasil['status']) {
            $pesan = 'Sip, berhasil minjem! Barang bisa langsung diambil.';
        }
    }
}

$daftar_barang = getBarangTersedia();

?>

<!-- Form Start -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-6">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4">Pinjam Barang</h6>
                
                <?php if (!empty($pesan)): ?>
                    <div class="alert alert-<?= $tipe_pesan ?> alert-dismissible fade show" role="alert">
                        <i class="fa fa-exclamation-circle me-2"></i><?= htmlspecialchars($pesan) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-floating mb-3">
                        <select class="form-select" id="id_barang" name="id_barang" aria-label="Pilih Barang" required>
                            <option value="" selected>-- Pilih Barang --</option>
                            <?php foreach ($daftar_barang as $barang): ?>
                                <option value="<?= $barang['id_barang'] ?>">
                                    <?= htmlspecialchars($barang['nama_barang']) ?> (Stok: <?= $barang['stok'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="id_barang">Pilih Barang</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="jumlah" name="jumlah" placeholder="Jumlah" min="1" required>
                        <label for="jumlah">Jumlah</label>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tgl_pinjam" class="form-label">Tanggal Pinjam</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                            <input type="date" class="form-control" id="tgl_pinjam" name="tgl_pinjam" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="tgl_kembali" class="form-label">Tanggal Kembali</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                            <input type="date" class="form-control" id="tgl_kembali" name="tgl_kembali" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 m-2">Pinjam Barang</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Form End -->

<?php
require_once 'footer.php';
?>