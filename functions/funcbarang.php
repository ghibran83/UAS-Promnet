<?php

require_once __DIR__ . '/../konek.php';

/**
 * Ambil semua data barang (support search & pagination)
 * @param string $keyword - Kata kunci pencarian
 * @param int $limit - Jumlah data per halaman
 * @param int $offset - Mulai dari data ke berapa
 * @return array - List barang
 */
function getSemuaBarang($keyword = '', $limit = null, $offset = 0) {
    global $koneksi;
    
    $where = "";
    if (!empty($keyword)) {
        $keyword = mysqli_real_escape_string($koneksi, $keyword);
        $where = "WHERE b.nama_barang LIKE '%$keyword%' OR b.kode_barang LIKE '%$keyword%'";
    }
    
    $query = "SELECT b.* FROM barang b $where ORDER BY b.created_at DESC";
    
    if ($limit !== null) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $query .= " LIMIT $limit OFFSET $offset";
    }
    
    $result = mysqli_query($koneksi, $query);
    
    $barang = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $barang[] = $row;
    }
    return $barang;
}

/**
 * Hitung total barang (buat pagination)
 * @param string $keyword - Kata kunci pencarian
 * @return int - Total barang
 */
function countSemuaBarang($keyword = '') {
    global $koneksi;
    
    $where = "";
    if (!empty($keyword)) {
        $keyword = mysqli_real_escape_string($koneksi, $keyword);
        $where = "WHERE nama_barang LIKE '%$keyword%' OR kode_barang LIKE '%$keyword%'";
    }
    
    $query = "SELECT COUNT(*) as total FROM barang $where";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);
    return (int)$row['total'];
}

/**
 * Cari barang berdasarkan ID
 * @param int $id - ID barang
 * @return array|null - Data barang atau null
 */
function getBarangById($id) {
    global $koneksi;
    $id = (int)$id;
    
    $query = "SELECT * FROM barang WHERE id_barang = $id";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) == 1) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

/**
 * Nambah barang baru
 * @param string $kode - Kode barang
 * @param string $nama - Nama barang
 * @param int $stok - Jumlah stok
 * @param string $deskripsi - Deskripsi barang
 * @return array - Status & pesan
 */
// Fungsi helper buat generate kode barang otomatis
function generateKodeBarang() {
    global $koneksi;
    $prefix = "BRG-";
    
    // Ambil kode terakhir
    $query = "SELECT kode_barang FROM barang ORDER BY id_barang DESC LIMIT 1";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $last_code = $row['kode_barang'];
        // Ambil angkanya aja (misal BRG-005 jadi 5)
        $last_number = (int)substr($last_code, 4);
        $new_number = $last_number + 1;
    } else {
        $new_number = 1;
    }
    
    // Format jadi 3 digit (BRG-001)
    return $prefix . str_pad($new_number, 3, "0", STR_PAD_LEFT);
}

/**
 * Nambah barang baru (Kode otomatis)
 * @param string $nama - Nama barang
 * @param int $stok - Jumlah stok
 * @param string $deskripsi - Deskripsi barang
 * @return array - Status & pesan
 */
function tambahBarang($nama, $stok, $deskripsi) {
    global $koneksi;
    
    if (empty($nama)) {
        return ['status' => false, 'pesan' => 'Nama barang wajib diisi!'];
    }
    
    $kode = generateKodeBarang(); // Generate otomatis
    $nama = mysqli_real_escape_string($koneksi, $nama);
    $stok = (int)$stok;
    $deskripsi = mysqli_real_escape_string($koneksi, $deskripsi);
    
    $query = "INSERT INTO barang (kode_barang, nama_barang, stok, deskripsi) 
              VALUES ('$kode', '$nama', $stok, '$deskripsi')";
              
    if (mysqli_query($koneksi, $query)) {
        return ['status' => true, 'pesan' => 'Barang berhasil ditambahin! Kode: ' . $kode];
    } else {
        return ['status' => false, 'pesan' => 'Gagal nambah barang: ' . mysqli_error($koneksi)];
    }
}

/**
 * Edit barang
 * @param int $id - ID barang
 * @param string $kode - Kode barang
 * @param string $nama - Nama barang
 * @param int $stok - Stok baru
 * @param string $deskripsi - Deskripsi baru
 * @return array - Status & pesan
 */
/**
 * Edit barang (Kode ga bisa diedit)
 * @param int $id - ID barang
 * @param string $nama - Nama barang
 * @param int $stok - Stok baru
 * @param string $deskripsi - Deskripsi baru
 * @return array - Status & pesan
 */
function editBarang($id, $nama, $stok, $deskripsi) {
    global $koneksi;
    
    $id = (int)$id;
    $nama = mysqli_real_escape_string($koneksi, $nama);
    $stok = (int)$stok;
    $deskripsi = mysqli_real_escape_string($koneksi, $deskripsi);
    
    $query = "UPDATE barang SET 
              nama_barang = '$nama', 
              stok = $stok, 
              deskripsi = '$deskripsi' 
              WHERE id_barang = $id";
              
    if (mysqli_query($koneksi, $query)) {
        return ['status' => true, 'pesan' => 'Data barang berhasil diupdate!'];
    } else {
        return ['status' => false, 'pesan' => 'Gagal update barang: ' . mysqli_error($koneksi)];
    }
}

/**
 * Hapus barang
 * @param int $id - ID barang
 * @return array - Status & pesan
 */
function hapusBarang($id) {
    global $koneksi;
    $id = (int)$id;
    
    // Cek dulu ada yg minjem ga (kalo relasi on delete cascade mungkin aman, tapi lebih baik cek manual biar user tau)
    // Cek tabel peminjaman
    $cek_pinjam = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id_barang = $id AND status = 'dipinjam'");
    if (mysqli_num_rows($cek_pinjam) > 0) {
        return ['status' => false, 'pesan' => 'Barang lagi dipinjem, ga bisa dihapus!'];
    }
    
    $query = "DELETE FROM barang WHERE id_barang = $id";
    if (mysqli_query($koneksi, $query)) {
        return ['status' => true, 'pesan' => 'Barang berhasil dihapus!'];
    } else {
        return ['status' => false, 'pesan' => 'Gagal hapus barang: ' . mysqli_error($koneksi)];
    }
}

/**
 * Kurangi stok (otomatis pas dipinjam)
 */
function kurangiStok($id, $jumlah) {
    global $koneksi;
    $id = (int)$id;
    $jumlah = (int)$jumlah;
    
    $query = "UPDATE barang SET stok = stok - $jumlah WHERE id_barang = $id AND stok >= $jumlah";
    mysqli_query($koneksi, $query);
    
    return mysqli_affected_rows($koneksi) > 0;
}

/**
 * Tambah stok (otomatis pas kembali)
 */
function tambahStok($id, $jumlah) {
    global $koneksi;
    $id = (int)$id;
    $jumlah = (int)$jumlah;
    
    $query = "UPDATE barang SET stok = stok + $jumlah WHERE id_barang = $id";
    return mysqli_query($koneksi, $query);
}

// ==========================================



/**
 * Ambil semua barang yang stoknya ada (tersedia)
 * @return array - List barang tersedia
 */
function getBarangTersedia() {
    global $koneksi;
    // Ambil barang yang stoknya > 0
    $query = "SELECT b.* FROM barang b WHERE b.stok > 0 ORDER BY b.nama_barang ASC";
    $result = mysqli_query($koneksi, $query);
    
    $barang = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $barang[] = $row;
    }
    return $barang;
}
?>