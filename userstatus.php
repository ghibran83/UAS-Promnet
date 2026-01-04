<?php

require_once 'functions/functions.php';
require_once 'functions/funcpeminjaman.php';

// Cek user udah login belom (sebelum header biar redirect aman)
requireUserLogin();

require_once 'header_user.php';

// Ngurusin form
$pesan = '';
$tipe_pesan = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $id_pinjam = (int)($_POST['id_pinjam'] ?? 0);
    
    // Balikin barang
    if ($action == 'kembalikan' && $id_pinjam > 0) {
        $tgl_kembali = $_POST['tgl_kembali'] ?? date('Y-m-d');
        $hasil = ajukanPengembalian($id_pinjam, $tgl_kembali);
        $pesan = $hasil['pesan'];
        $tipe_pesan = $hasil['status'] ? 'success' : 'danger';
    }
}

// Pencarian & Pagination
$keyword = $_GET['search'] ?? '';
$search_date = $_GET['search_date'] ?? '';
$limit = 5;
$page = (int)($_GET['page'] ?? 1);
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$id_user = $_SESSION['user_id'] ?? 0;
$total_data = countPeminjamanByUser($id_user, $keyword, $search_date);
$total_pages = ceil($total_data / $limit);

$daftar_peminjaman = getPeminjamanByUser($id_user, $keyword, $limit, $offset, $search_date);
?>

<!-- Table Start -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-secondary rounded h-100 p-4">
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap">
                    <h6 class="mb-0 mb-2">Status Peminjaman</h6>
                    <form method="GET" class="d-flex align-items-center">
                        <div class="input-group input-group-sm me-2" style="width: auto;">
                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                            <input type="date" name="search_date" class="form-control form-control-sm" placeholder="Pilih tanggal" value="<?= htmlspecialchars($_GET['search_date'] ?? '') ?>">
                        </div>
                        <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Cari barang/status..." value="<?= htmlspecialchars($keyword) ?>">
                        <button type="submit" class="btn btn-sm btn-primary">Cari</button>
                    </form>
                </div>
                
                <?php if (!empty($pesan)): ?>
                    <div class="alert alert-<?= $tipe_pesan ?> alert-dismissible fade show" role="alert">
                        <i class="fa fa-exclamation-circle me-2"></i><?= htmlspecialchars($pesan) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table text-start align-middle table-bordered table-hover mb-0">
                        <thead>
                            <tr class="text-white">
                                <th scope="col">Kode Barang</th>
                                <th scope="col">Nama Barang</th>
                                <th scope="col">Jumlah</th>
                                <th scope="col">Tanggal Pinjam</th>
                                <th scope="col">Tanggal Kembali</th>
                                <th scope="col">Status</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($daftar_peminjaman) > 0): ?>
                                <?php foreach ($daftar_peminjaman as $pinjam): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($pinjam['kode_barang']) ?></td>
                                        <td><?= htmlspecialchars($pinjam['nama_barang']) ?></td>

                                        <td><?= $pinjam['jumlah'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($pinjam['tgl_pinjam'])) ?></td>
                                        <td><?= date('d/m/Y', strtotime($pinjam['tgl_kembali'])) ?></td>
                                        <td>
                                            <?php 
                                            $status_class = 'warning';
                                            if ($pinjam['status'] == 'dipinjam') $status_class = 'warning';
                                            elseif ($pinjam['status'] == 'dikembalikan') $status_class = 'success';
                                            elseif ($pinjam['status'] == 'rejected') $status_class = 'danger';
                                            ?>
                                            <span class="badge bg-<?= $status_class ?>">
                                                <?= ucfirst($pinjam['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($pinjam['status'] == 'dipinjam'): ?>
                                                <form method="POST" action="" class="d-flex flex-column gap-1">
                                                    <input type="hidden" name="action" value="kembalikan">
                                                    <input type="hidden" name="id_pinjam" value="<?= $pinjam['id_pinjam'] ?>">
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                        <input type="date" name="tgl_kembali" class="form-control form-control-sm" placeholder="Tanggal kembali" value="<?= date('Y-m-d') ?>" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" 
                                                            onclick="return confirm('Apakah Anda yakin ingin mengembalikan barang ini?')">Kembalikan</button>
                                                </form>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data peminjaman.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination pagination-sm justify-content-center mb-0">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($keyword) ?>&search_date=<?= urlencode($search_date) ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($keyword) ?>&search_date=<?= urlencode($search_date) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($keyword) ?>&search_date=<?= urlencode($search_date) ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- Table End -->

<?php
require_once 'footer.php';
?>