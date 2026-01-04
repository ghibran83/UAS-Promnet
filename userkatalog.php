<?php

// Panggil fungsi-fungsi
require_once 'functions/functions.php';
require_once 'functions/funcbarang.php';

// Cek user udah login belom
requireUserLogin();

// Panggil navigasi
require_once 'header_user.php';

// Pencarian & Pagination
$keyword = $_GET['search'] ?? '';
$limit = 5;
$page = (int)($_GET['page'] ?? 1);
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$total_data = countSemuaBarang($keyword);
$total_pages = ceil($total_data / $limit);

$daftar_barang = getSemuaBarang($keyword, $limit, $offset);
?>

<!-- Table Start -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-secondary rounded h-100 p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">Katalog Barang</h6>
                    <form method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Cari barang..." value="<?= htmlspecialchars($keyword) ?>">
                        <button type="submit" class="btn btn-sm btn-primary">Cari</button>
                    </form>
                </div>
                

                <div></div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Kode Barang</th>
                                <th scope="col">Nama Barang</th>
                                <th scope="col">Stok</th>
                                <th scope="col">Status</th>
                                <th scope="col">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($daftar_barang) > 0): ?>
                                <?php $no = 1; foreach ($daftar_barang as $barang): ?>
                                    <tr>
                                        <th scope="row"><?= $no++ ?></th>
                                        <td><strong><?= htmlspecialchars($barang['kode_barang']) ?></strong></td>
                                        <td><?= htmlspecialchars($barang['nama_barang']) ?></td>
                                        <td><?= $barang['stok'] ?></td>
                                        <td>
                                            <?php if ($barang['stok'] > 0): ?>
                                                <span class="text-success">Tersedia</span>
                                            <?php else: ?>
                                                <span class="text-danger">Tidak Tersedia</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($barang['deskripsi']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data barang.</td>
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
                            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($keyword) ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($keyword) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($keyword) ?>" aria-label="Next">
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