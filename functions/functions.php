<?php
/**
 * ============================================
 * FILE: functions/auth.php
 * ============================================
 * Deskripsi: Fungsi buat urusan login-loginan
 * Meliputi: Daftar, Masuk, Cek Sesi, Keluar
 * ============================================
 */

// Panggil db dulu
require_once __DIR__ . '/../konek.php';

// Mulai sesi biar aman kalo belom nyala
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Buat daftar user baru nih
 * @param string $nama - Nama lengkap user
 * @param string $username - Username buat login
 * @param string $password - Sandi (bakal di-acak)
 * @return array - Status sama pesannya
 */
function registerUser($nama, $username, $password) {
    global $koneksi;
    
    // Cek usernamenya udah dipake belom
    $cek = mysqli_query($koneksi, "SELECT id_user FROM users WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        return ['status' => false, 'pesan' => 'Yah, username udah dipake orang!'];
    }
    
    // Acak password biar aman pake bcrypt
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    // Bersihin input biar ga di-hack
    $nama = mysqli_real_escape_string($koneksi, $nama);
    $username = mysqli_real_escape_string($koneksi, $username);
    
    // Masukin data user baru ke db
    $query = "INSERT INTO users (nama, username, password) VALUES ('$nama', '$username', '$hashed_password')";
    
    if (mysqli_query($koneksi, $query)) {
        return ['status' => true, 'pesan' => 'Mantap, berhasil daftar! Langsung login gih.'];
    } else {
        return ['status' => false, 'pesan' => 'Waduh gagal daftar: ' . mysqli_error($koneksi)];
    }
}

/**
 * Buat login user
 * @param string $username - Username user
 * @param string $password - Sandi user
 * @return array - Status, pesan, sama datanya kalo sukses
 */
function loginUser($username, $password) {
    global $koneksi;
    
    // Bersihin input biar aman
    $username = mysqli_real_escape_string($koneksi, $username);
    
    // Cari user pake username
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);
    
    // Cek usernya ketemu ga
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Cocokin password sama yang di db
        if (password_verify($password, $user['password'])) {
            return [
                'status' => true, 
                'pesan' => 'Sip, berhasil masuk!',
                'data' => $user
            ];
        } else {
            return ['status' => false, 'pesan' => 'Password salah bro!'];
        }
    } else {
        return ['status' => false, 'pesan' => 'Username ga ketauan siapa!'];
    }
}

/**
 * Buat daftar admin baru nih
 * @param string $nama - Nama lengkap admin
 * @param string $username - Username buat login
 * @param string $password - Sandi (bakal di-acak)
 * @return array - Status sama pesannya
 */
function registerAdmin($nama, $username, $password) {
    global $koneksi;
    
    // Cek usernamenya udah dipake belom
    $cek = mysqli_query($koneksi, "SELECT id_admin FROM admin WHERE username = '$username'");
    if (mysqli_num_rows($cek) > 0) {
        return ['status' => false, 'pesan' => 'Yah, username udah dipake orang!'];
    }
    
    // Acak password biar aman pake bcrypt
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    // Bersihin input biar ga di-hack
    $nama = mysqli_real_escape_string($koneksi, $nama);
    $username = mysqli_real_escape_string($koneksi, $username);
    
    // Masukin data admin baru ke db
    $query = "INSERT INTO admin (nama, username, password) VALUES ('$nama', '$username', '$hashed_password')";
    
    if (mysqli_query($koneksi, $query)) {
        return ['status' => true, 'pesan' => 'Mantap, admin berhasil daftar! Langsung login gih.'];
    } else {
        return ['status' => false, 'pesan' => 'Waduh gagal daftar admin: ' . mysqli_error($koneksi)];
    }
}

/**
 * Buat login admin
 * @param string $username - Username admin
 * @param string $password - Sandi admin
 * @return array - Status, pesan, sama datanya kalo sukses
 */
function loginAdmin($username, $password) {
    global $koneksi;
    
    // Bersihin input biar aman
    $username = mysqli_real_escape_string($koneksi, $username);
    
    // Cari admin pake username
    $query = "SELECT * FROM admin WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);
    
    // Cek adminnya ketemu ga
    if (mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        
        // Cocokin password sama yang di db
        if (password_verify($password, $admin['password'])) {
            return [
                'status' => true, 
                'pesan' => 'Sip, admin berhasil masuk!',
                'data' => $admin
            ];
        } else {
            return ['status' => false, 'pesan' => 'Password salah bro!'];
        }
    } else {
        return ['status' => false, 'pesan' => 'Username admin ga ketauan siapa!'];
    }
}

/**
 * Cek user udah login belom
 * @return bool - True kalo udah, false kalo belom
 */
function isUserLoggedIn() {
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'user') {
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1200)) {
            // Kalo diem 20 menit tendang aja
            session_unset();
            session_destroy();
            return false;
        }
        $_SESSION['last_activity'] = time(); // Update waktu aktivitas
        return true;
    }
    return false;
}

function isAdminLoggedIn() {
    if (isset($_SESSION['admin_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin') {
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1200)) {
            // Kalo diem 20 menit tendang aja
            session_unset();
            session_destroy();
            return false;
        }
        $_SESSION['last_activity'] = time(); // Update waktu aktivitas
        return true;
    }
    return false;
}

/**
 * Lempar user kalo belom login
 * @param string $redirect_url - Mau dilempar kemana
 */
function requireUserLogin($redirect_url = 'userlogin.php') {
    if (!isUserLoggedIn()) {
        header("Location: $redirect_url");
        exit();
    }
}

/**
 * Lempar admin kalo belom login
 * @param string $redirect_url - Mau dilempar kemana
 */
function requireAdminLogin($redirect_url = 'adminlogin.php') {
    if (!isAdminLoggedIn()) {
        header("Location: $redirect_url");
        exit();
    }
}

/**
 * Update profil user
 * @param int $id - ID usernya
 * @param string $nama - Nama baru
 * @param string $username - Username baru
 * @param string|null $password - Password baru (kalo mau ganti)
 * @return array - Status sama pesannya
 */
function updateUserProfile($id, $nama, $username, $password = null) {
    global $koneksi;
    
    $nama = mysqli_real_escape_string($koneksi, $nama);
    $username = mysqli_real_escape_string($koneksi, $username);
    
    // Cek username dipake orang lain ga
    $cek = mysqli_query($koneksi, "SELECT id_user FROM users WHERE username = '$username' AND id_user != $id");
    if (mysqli_num_rows($cek) > 0) {
        return ['status' => false, 'pesan' => 'Yah, username udah dipake orang lain!'];
    }
    
    if ($password) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $query = "UPDATE users SET nama = '$nama', username = '$username', password = '$hashed_password' WHERE id_user = $id";
    } else {
        $query = "UPDATE users SET nama = '$nama', username = '$username' WHERE id_user = $id";
    }
    
    if (mysqli_query($koneksi, $query)) {
        // Update session juga biar ga aneh
        $_SESSION['user_nama'] = $nama;
        return ['status' => true, 'pesan' => 'Mantap, profil berhasil diupdate!'];
    } else {
        return ['status' => false, 'pesan' => 'Gagal update profil: ' . mysqli_error($koneksi)];
    }
}

/**
 * Update profil admin
 * @param int $id - ID adminnya
 * @param string $nama - Nama baru
 * @param string $username - Username baru
 * @param string|null $password - Password baru (kalo mau ganti)
 * @return array - Status sama pesannya
 */
function updateAdminProfile($id, $nama, $username, $password = null) {
    global $koneksi;
    
    $nama = mysqli_real_escape_string($koneksi, $nama);
    $username = mysqli_real_escape_string($koneksi, $username);
    
    // Cek username dipake orang lain ga
    $cek = mysqli_query($koneksi, "SELECT id_admin FROM admin WHERE username = '$username' AND id_admin != $id");
    if (mysqli_num_rows($cek) > 0) {
        return ['status' => false, 'pesan' => 'Yah, username udah dipake admin lain!'];
    }
    
    if ($password) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $query = "UPDATE admin SET nama = '$nama', username = '$username', password = '$hashed_password' WHERE id_admin = $id";
    } else {
        $query = "UPDATE admin SET nama = '$nama', username = '$username' WHERE id_admin = $id";
    }
    
    if (mysqli_query($koneksi, $query)) {
        // Update session juga biar ga aneh
        $_SESSION['admin_nama'] = $nama;
        return ['status' => true, 'pesan' => 'Mantap, profil admin berhasil diupdate!'];
    } else {
        return ['status' => false, 'pesan' => 'Gagal update profil admin: ' . mysqli_error($koneksi)];
    }
}
?>