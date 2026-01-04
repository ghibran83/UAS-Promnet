<?php

require_once 'functions/functions.php';
require_once 'functions/funcbarang.php';

requireAdminLogin();

// Terima kiriman form
$pesan = '';
$tipe_pesan = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = trim($_POST['nama_barang'] ?? '');
    $stok = (int)($_POST['stok'] ?? 0);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    
    // Cek ini mau edit apa mau nambah
    if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
        // Ini EDIT bos
        $edit_id = (int)$_POST['edit_id'];
        
        // Pake input dari form edit
        $nama_barang = trim($_POST['edit_nama'] ?? '');
        $stok = (int)($_POST['edit_stok'] ?? 0);
        $deskripsi = trim($_POST['edit_deskripsi'] ?? '');
        
        $hasil = editBarang($edit_id, $nama_barang, $stok, $deskripsi);
        $pesan = $hasil['pesan'];
        $tipe_pesan = $hasil['status'] ? 'success' : 'danger';
    } else {
        // Ini NAMBAH BARU
        if (empty($nama_barang)) {
            $pesan = 'Nama barang wajib diisi bos!';
            $tipe_pesan = 'danger';
        } else {
            // Function tambahBarang skrg cuma butuh nama, stok, deskripsi. Kode otomatis.
            $hasil = tambahBarang($nama_barang, $stok, $deskripsi);
            $pesan = $hasil['pesan'];
            $tipe_pesan = $hasil['status'] ? 'success' : 'danger';
        }
    }
}

// hapus barang
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $hasil = hapusBarang($delete_id);
    $pesan = $hasil['pesan'];
    $tipe_pesan = $hasil['status'] ? 'success' : 'danger';
    header("Location: adminbarang.php?msg=" . urlencode($pesan));
    exit();
}

// Pencarian & Pagination
$keyword = $_GET['search'] ?? '';
$limit = 5;
$page = (int)($_GET['page'] ?? 1);
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$total_data = countSemuaBarang($keyword);
$total_pages = ceil($total_data / $limit);

$daftar_barang = getSemuaBarang($keyword, $limit, $offset);



require_once 'header.php';
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <!-- Input Form -->
        <div class="col-sm-12 col-xl-4">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4">Tambah Barang</h6>
                
                <?php if (!empty($pesan)): ?>
                    <div class="alert alert-<?= $tipe_pesan ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($pesan) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                    </div>
                    <!-- Kode barang otomatis -->
                    <div class="mb-3">
                        <label for="stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="stok" name="stok" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Tambah Barang</button>
                </form>
            </div>
        </div>
        
        <!-- Table View -->
        <div class="col-sm-12 col-xl-8">
            <div class="bg-secondary rounded h-100 p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">Daftar Barang</h6>
                    <form method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Cari barang..." value="<?= htmlspecialchars($keyword) ?>">
                        <button type="submit" class="btn btn-sm btn-primary">Cari</button>
                    </form>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Kode</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Stok</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($daftar_barang) > 0): ?>
                                <?php foreach ($daftar_barang as $barang): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($barang['kode_barang']) ?></td>
                                        <td><?= htmlspecialchars($barang['nama_barang']) ?></td>
                                        <td><?= $barang['stok'] ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-warning mb-1" onclick="editBarang(<?= $barang['id_barang'] ?>, '<?= htmlspecialchars(addslashes($barang['nama_barang'])) ?>', <?= $barang['stok'] ?>, '<?= htmlspecialchars(addslashes($barang['deskripsi'])) ?>')">Edit</button>
                                            <a href="adminbarang.php?delete=<?= $barang['id_barang'] ?>" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Hapus barang ini?')">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5">Belum ada data barang.</td>
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

        <!-- Edit Form -->
        <div class="col-sm-12" id="editForm" style="display: none;">
            <div class="bg-secondary rounded h-100 p-4">
                <h3>Edit Barang</h3>
                <form method="POST" action="">
                    <input type="hidden" id="edit_id" name="edit_id">
                    <div class="mb-3">
                        <label for="edit_nama" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" id="edit_nama" name="edit_nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_stok" class="form-label">Stok</label>
                        <input type="number" class="form-control" id="edit_stok" name="edit_stok" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="edit_deskripsi" name="edit_deskripsi" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Barang</button>
                    <button type="button" class="btn btn-danger" onclick="cancelEdit()">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function editBarang(id, nama, stok, deskripsi) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_stok').value = stok;
        document.getElementById('edit_deskripsi').value = deskripsi;
        document.getElementById('editForm').style.display = 'block';
        window.scrollTo(0, document.getElementById('editForm').offsetTop);
    }
    
    function cancelEdit() {
        document.getElementById('editForm').style.display = 'none';
    }
</script>

<?php
require_once 'footer.php';
?>
