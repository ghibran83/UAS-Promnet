<?php
// Mulai sesi
require_once 'functions/functions.php';
require_once 'functions/funcpeminjaman.php';

// Cek admin udah login belom
requireAdminLogin();

// Pagination
$limit = 5;
$page = (int)($_GET['page'] ?? 1);
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Filter status
$filter_status = $_GET['status'] ?? '';

// Pake dashboard mode biar barang dipinjam di atas & filter return 24 jam
$total_data = countSemuaPeminjaman('', $filter_status, '', true);
$total_pages = ceil($total_data / $limit);

$daftar_peminjaman = getSemuaPeminjaman('', $limit, $offset, $filter_status, '', true);

// Mulai pake template
require_once 'header.php';
?>

            <!-- Recent Sales Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-secondary text-center rounded p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="mb-0">Peminjaman Terbaru</h6>
                        <form method="GET" class="d-flex">
                            <select name="status" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="dipinjam" <?= $filter_status == 'dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                                <option value="dikembalikan" <?= $filter_status == 'dikembalikan' ? 'selected' : '' ?>>Dikembalikan</option>
                            </select>
                        </form>
                    </div>
                    <?php if (count($daftar_peminjaman) > 0): ?>
                    <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                <tr class="text-white">
                                    <th scope="col">User</th>
                                    <th scope="col">Barang</th>
                                    <th scope="col">Jumlah</th>
                                    <th scope="col">Tanggal Peminjaman</th>
                                    <th scope="col">Tanggal Kembali</th>
                                    <th scope="col">Status</th>
                                    <!-- <th scope="col">Action</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($daftar_peminjaman as $pinjam): ?>
                                <tr>
                                    <td><?= htmlspecialchars($pinjam['nama_user']) ?></td>
                                    <td><?= htmlspecialchars($pinjam['nama_barang']) ?></td>
                                    <td><?= $pinjam['jumlah'] ?></td>
                                    <td><?= date('d/m/Y', strtotime($pinjam['tgl_pinjam'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($pinjam['tgl_kembali'])) ?></td>
                                    <td>
                                        <?php 
                                        $status_class = 'text-warning'; // pending
                                        if ($pinjam['status'] == 'dipinjam') $status_class = 'text-info';
                                        elseif ($pinjam['status'] == 'dikembalikan') $status_class = 'text-success';
                                        ?>
                                        <span class="<?= $status_class ?>"><?= ucfirst($pinjam['status']) ?></span>
                                    </td>
                                    <!-- <td><a class="btn btn-sm btn-primary" href="">Detail</a></td> -->
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                        <p class="text-center">Belum ada data peminjaman.</p>
                    <?php endif; ?>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination pagination-sm justify-content-center mb-0">
                            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page - 1 ?>&status=<?= urlencode($filter_status) ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&status=<?= urlencode($filter_status) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1 ?>&status=<?= urlencode($filter_status) ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Recent Sales End -->

<?php
require_once 'footer.php';
?>