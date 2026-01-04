<?php
/**
 * ============================================
 * FILE: adminpinjam.php
 * ============================================
 * Deskripsi: Liat history peminjaman di sini
 * ============================================
 */

require_once 'functions/functions.php';
require_once 'functions/funcpeminjaman.php';
require_once 'konek.php';

requireAdminLogin();

// Ngurusin pesan alert
$pesan = '';
$tipe_pesan = '';

if (isset($_GET['msg'])) {
    $pesan = $_GET['msg'];
    $tipe_pesan = 'success';
}

// Pencarian & Pagination
$keyword = $_GET['search'] ?? '';
$search_date = $_GET['search_date'] ?? '';
$limit = 5;
$page = (int)($_GET['page'] ?? 1);
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$total_data = countSemuaPeminjaman($keyword, '', $search_date);
$total_pages = ceil($total_data / $limit);

$semua_peminjaman = getSemuaPeminjaman($keyword, $limit, $offset, '', $search_date);

require_once 'header.php';
?>

<div class="container-fluid pt-4 px-4">
    <div class="bg-secondary text-center rounded p-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Riwayat Peminjaman</h6>
            <form method="GET" class="d-flex align-items-center">
                <div class="input-group input-group-sm me-2" style="width: auto;">
                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    <input type="date" name="search_date" class="form-control form-control-sm" placeholder="Pilih tanggal" value="<?= htmlspecialchars($_GET['search_date'] ?? '') ?>">
                </div>
                <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Cari data..." value="<?= htmlspecialchars($keyword) ?>">
                <button type="submit" class="btn btn-sm btn-primary">Cari</button>
            </form>
        </div>

        <?php if (!empty($pesan)): ?>
            <div class="alert alert-<?= $tipe_pesan ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($pesan) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table text-start align-middle table-bordered table-hover mb-0">
                <thead>
                    <tr class="text-white">
                        <th scope="col">User</th>
                        <th scope="col">Barang</th>

                        <th scope="col">Jumlah</th>
                        <th scope="col">Tgl Pinjam</th>
                        <th scope="col">Tgl Kembali</th>
                        <th scope="col">Status</th>
                        <th scope="col">Tgl Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($semua_peminjaman) > 0): ?>
                        <?php foreach ($semua_peminjaman as $pinjam): ?>
                            <tr>
                                <td><?= htmlspecialchars($pinjam['nama_user']) ?></td>
                                <td><?= htmlspecialchars($pinjam['nama_barang']) ?></td>

                                <td><?= $pinjam['jumlah'] ?></td>
                                <td><?= date('d/m/Y', strtotime($pinjam['tgl_pinjam'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($pinjam['tgl_kembali'])) ?></td>
                                <td>
                                    <?php 
                                    $status_class = 'text-warning';
                                    if ($pinjam['status'] == 'dipinjam') $status_class = 'text-info';
                                    elseif ($pinjam['status'] == 'dikembalikan') $status_class = 'text-success';
                                    elseif ($pinjam['status'] == 'rejected') $status_class = 'text-danger';
                                    ?>
                                    <span class="<?= $status_class ?>">
                                        <?= ucfirst(str_replace('_', ' ', $pinjam['status'])) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($pinjam['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">Belum ada data peminjaman.</td>
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

<?php
require_once 'footer.php';
?>