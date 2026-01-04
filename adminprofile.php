<?php
require_once 'functions/functions.php';
requireAdminLogin();

$admin_id = $_SESSION['admin_id'];
$pesan = '';
$status = false;

// Ambil data admin terbaru
$query = "SELECT * FROM admin WHERE id_admin = $admin_id";
$result = mysqli_query($koneksi, $query);
$admin = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = !empty($_POST['password']) ? $_POST['password'] : null;
    
    $update = updateAdminProfile($admin_id, $nama, $username, $password);
    $pesan = $update['pesan'];
    $status = $update['status'];
    
    if ($status) {
        // Refresh data admin
        $result = mysqli_query($koneksi, $query);
        $admin = mysqli_fetch_assoc($result);
    }
}

include 'header.php';
?>

<div class="container-fluid pt-4 px-4">
    <div class="row g-4 justify-content-center">
        <div class="col-sm-12 col-xl-6">
            <div class="bg-secondary rounded h-100 p-4">
                <h6 class="mb-4">Edit Profil Admin</h6>
                
                <?php if ($pesan): ?>
                    <div class="alert alert-<?= $status ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                        <i class="fa fa-<?= $status ? 'check-circle' : 'exclamation-circle' ?> me-2"></i><?= $pesan ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($admin['nama']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru (Kosongkan bila tidak ingin ganti)</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="admindashboard.php" class="btn btn-outline-light ms-2">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
