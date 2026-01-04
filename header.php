<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Fungsi bantu cek menu aktif
function is_active($page_name) {
    echo basename($_SERVER['PHP_SELF']) == $page_name ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sistem Peminjaman - Admin</title>
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

        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="admindashboard.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><img src="bootstrap/img/Gemini_Image_acwmhaacwmhaacwm-removebg-preview.png" alt="Logo SIMBA" style="height: 80px;"></h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0"><?= isset($_SESSION['admin_nama']) ? htmlspecialchars($_SESSION['admin_nama']) : 'Admin' ?></h6>
                        <span>Admin</span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="admindashboard.php" class="nav-item nav-link <?php is_active('admindashboard.php'); ?>"><i class="fa fa-tachometer-alt me-2"></i>Dashboard</a>

                    <a href="adminbarang.php" class="nav-item nav-link <?php is_active('adminbarang.php'); ?>"><i class="fa fa-box me-2"></i>Kelola Barang</a>
                    <a href="adminpinjam.php" class="nav-item nav-link <?php is_active('adminpinjam.php'); ?>"><i class="fa fa-history me-2"></i>Riwayat Peminjaman</a>
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->


        <!-- Konten utama mulai -->
        <div class="content">
            <!-- Navbar atas mulai -->
            <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
                <a href="admindashboard.php" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><img src="bootstrap/img/Gemini_Image_acwmhaacwmhaacwm-removebg-preview.png" alt="Logo SIMBA" style="height: 80px;"></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <div class="navbar-nav align-items-center ms-auto">
                    
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex"><?= isset($_SESSION['admin_nama']) ? htmlspecialchars($_SESSION['admin_nama']) : 'Admin' ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                            <a href="adminprofile.php" class="dropdown-item">Edit Profil</a>
                            <a href="adminlogout.php" class="dropdown-item" onclick="return confirm('Apakah Anda yakin ingin logout?')">Log Out</a>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar atas selesais -->
