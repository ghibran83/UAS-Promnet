<?php

// Mulai sesi dulu
session_start();

// Panggil fungsi cek login
require_once 'functions/functions.php';

// Wadah buat pesen error/sukses
$pesan = '';
$tipe_pesan = '';

// Kalo ada yang kirim data daftar (POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Tangkap isian form
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';
    
    // Cek isiannya beres ga
    if (empty($nama) || empty($username) || empty($password)) {
        $pesan = 'Isi semua dong bos!';
        $tipe_pesan = 'danger';
    } elseif (strlen($password) < 6) {
        $pesan = 'Password kependekan, minimal 6 lah!';
        $tipe_pesan = 'danger';
    } elseif ($password !== $konfirmasi) {
        $pesan = 'Password beda sama konfirmasinya bro!';
        $tipe_pesan = 'danger';
    } else {
        // Daftarin sekarang
        $hasil = registerUser($nama, $username, $password);
        $pesan = $hasil['pesan'];
        $tipe_pesan = $hasil['status'] ? 'success' : 'danger';
        
        // Kalo sukses, lempar ke login (2 detik)
        if ($hasil['status']) {
            header("refresh:2;url=userlogin.php");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sistem Peminjaman - Register User</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet"> 
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="bootstrap/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="bootstrap/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="bootstrap/css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid position-relative d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Sign Up Start -->
        <div class="container-fluid">
            <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
                <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                    <div class="bg-secondary rounded p-4 p-sm-5 my-4 mx-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <a href="index.php" class="">
                                <h3 class="text-primary"><img src="bootstrap/img/Gemini_Image_acwmhaacwmhaacwm-removebg-preview.png" alt="Logo SIMBA" style="height: 80px;"></h3>
                            </a>
                            <h3>Register User</h3>
                        </div>

                        <?php if (!empty($pesan)): ?>
                            <div class="alert alert-<?= $tipe_pesan ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($pesan) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Lengkap" value="<?= htmlspecialchars($nama ?? '') ?>" required>
                                <label for="nama">Nama Lengkap</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?= htmlspecialchars($username ?? '') ?>" required>
                                <label for="username">Username</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                <label for="password">Password (Min. 6 Karakter)</label>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="password" class="form-control" id="konfirmasi" name="konfirmasi" placeholder="Konfirmasi Password" required>
                                <label for="konfirmasi">Konfirmasi Password</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary py-3 w-100 mb-4">Sign Up</button>
                            <p class="text-center mb-0">Sudah punya akun? <a href="userlogin.php">Login di sini</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sign Up End -->
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="bootstrap/lib/chart/chart.min.js"></script>
    <script src="bootstrap/lib/easing/easing.min.js"></script>
    <script src="bootstrap/lib/waypoints/waypoints.min.js"></script>
    <script src="bootstrap/lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="bootstrap/lib/tempusdominus/js/moment.min.js"></script>
    <script src="bootstrap/lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="bootstrap/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="bootstrap/js/main.js"></script>
</body>

</html>