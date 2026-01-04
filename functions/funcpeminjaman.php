<?php

// Panggil koneksi database sama fungsi barang
require_once __DIR__ . '/../konek.php';
require_once __DIR__ . '/funcbarang.php';

/**
 * Bikin nota pinjem baru
 * @param int $id_user - ID user yang minjem
 * @param int $id_barang - ID barang yang dipinjem
 * @param int $jumlah - Jumlah barang yang dipinjem
 * @param string $tgl_pinjam - Tanggal pinjam (format: Y-m-d)
 * @param string $tgl_kembali - Tanggal kembali (format: Y-m-d)
 * @return array - Status dan pesan
 */
function buatPeminjaman($id_user, $id_barang, $jumlah, $tgl_pinjam, $tgl_kembali) {
    global $koneksi;
    
    // Cek inputan beres ga
    $id_user = (int) $id_user;
    $id_barang = (int) $id_barang;
    $jumlah = (int) $jumlah;
    $tgl_pinjam = mysqli_real_escape_string($koneksi, $tgl_pinjam);
    $tgl_kembali = mysqli_real_escape_string($koneksi, $tgl_kembali);
    
    // Cek stoknya ada ga
    $barang = getBarangById($id_barang);
    if (!$barang) {
        return ['status' => false, 'pesan' => 'Barang ga ketemu bos!'];
    }
    
    if ($barang['stok'] < $jumlah) {
        return ['status' => false, 'pesan' => 'Stok abis atau kurang! Sisa cuma: ' . $barang['stok']];
    }
    
    // Cek tanggalnya masuk akal ga
    if (strtotime($tgl_kembali) < strtotime($tgl_pinjam)) {
        return ['status' => false, 'pesan' => 'Masa balik sebelum pinjam? Ga logis bro!'];
    }
    
    // Mulai transaksi biar aman
    mysqli_begin_transaction($koneksi);
    
    try {
        // Masukin data peminjaman
        $query = "INSERT INTO peminjaman (id_user, id_barang, jumlah, tgl_pinjam, tgl_kembali, status) 
                  VALUES ($id_user, $id_barang, $jumlah, '$tgl_pinjam', '$tgl_kembali', 'dipinjam')";
        
        if (!mysqli_query($koneksi, $query)) {
            throw new Exception('Gagal nyimpen data pinjem');
        }
        
        // Kurangi stok barang
        if (!kurangiStok($id_barang, $jumlah)) {
            throw new Exception('Gagal ngurangin stok barang');
        }
        
        // Simpen permanen
        mysqli_commit($koneksi);
        
        return ['status' => true, 'pesan' => 'Sip, berhasil minjem!'];
        
    } catch (Exception $e) {
        // Batalin semua kalo error
        mysqli_rollback($koneksi);
        return ['status' => false, 'pesan' => $e->getMessage()];
    }
}

/**
 * Tarik semua history peminjaman (buat admin) - Support search & pagination
 * @param string $keyword - Kata kunci pencarian
 * @param int $limit - Jumlah data per halaman
 * @param int $offset - Mulai dari data ke berapa
 * @return array - Array isi semua data pinjeman + nama user & barang
 */
/**
 * Tarik semua history peminjaman (buat admin) - Support search, pagination & status filter
 * @param string $keyword - Kata kunci pencarian
 * @param int $limit - Jumlah data per halaman
 * @param int $offset - Mulai dari data ke berapa
 * @param string $filter_status - Filter status khusus (dipinjam, dikembalikan, dll)
 * @return array - Array isi semua data pinjeman + nama user & barang
 */
function getSemuaPeminjaman($keyword = '', $limit = null, $offset = 0, $filter_status = '', $date = '', $dashboard_mode = false) {
    global $koneksi;
    
    $where_clauses = [];
    
    if (!empty($keyword)) {
        $keyword = mysqli_real_escape_string($koneksi, $keyword);
        $where_clauses[] = "(u.nama LIKE '%$keyword%' 
                  OR b.nama_barang LIKE '%$keyword%' 
                  OR b.kode_barang LIKE '%$keyword%' 
                  OR p.status LIKE '%$keyword%')";
    }
    
    if (!empty($date)) {
        $date = mysqli_real_escape_string($koneksi, $date);
        $where_clauses[] = "(p.tgl_pinjam = '$date' OR p.tgl_kembali = '$date')";
    }
    
    if (!empty($filter_status)) {
        $filter_status = mysqli_real_escape_string($koneksi, $filter_status);
        $where_clauses[] = "p.status = '$filter_status'";
    }
    
    // Dashboard mode: filter barang yang dikembalikan cuma yang 24 jam terakhir
    if ($dashboard_mode && empty($filter_status)) {
        $where_clauses[] = "(p.status = 'dipinjam' 
                            OR p.status = 'rejected' 
                            OR (p.status = 'dikembalikan' AND p.tgl_kembali >= DATE_SUB(NOW(), INTERVAL 1 DAY)))";
    }
    
    $where = "";
    if (count($where_clauses) > 0) {
        $where = "WHERE " . implode(" AND ", $where_clauses);
    }
    
    // Pake JOIN biar dapet nama user sama barang
    $query = "SELECT p.*, u.nama as nama_user, u.username, b.kode_barang, b.nama_barang 
              FROM peminjaman p 
              JOIN users u ON p.id_user = u.id_user 
              JOIN barang b ON p.id_barang = b.id_barang 
              $where";
    
    // Dashboard mode: prioritas barang dipinjam di atas
    if ($dashboard_mode) {
        $query .= " ORDER BY 
                    CASE 
                        WHEN p.status = 'dipinjam' THEN 1
                        WHEN p.status = 'rejected' THEN 2
                        WHEN p.status = 'dikembalikan' THEN 3
                        ELSE 4
                    END,
                    p.created_at DESC";
    } else {
        $query .= " ORDER BY p.created_at DESC";
    }
    
    if ($limit !== null) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $query .= " LIMIT $limit OFFSET $offset";
    }
    
    $result = mysqli_query($koneksi, $query);
    
    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    
    return $data;
}

/**
 * Hitung total records peminjaman (buat pagination)
 * @param string $keyword - Kata kunci pencarian
 * @param string $filter_status - Filter status khusus
 * @return int - Total records
 */
function countSemuaPeminjaman($keyword = '', $filter_status = '', $date = '', $dashboard_mode = false) {
    global $koneksi;
    
    $where_clauses = [];
    
    if (!empty($keyword)) {
        $keyword = mysqli_real_escape_string($koneksi, $keyword);
        $where_clauses[] = "(u.nama LIKE '%$keyword%' 
                  OR b.nama_barang LIKE '%$keyword%' 
                  OR b.kode_barang LIKE '%$keyword%' 
                  OR p.status LIKE '%$keyword%')";
    }
    
    if (!empty($date)) {
        $date = mysqli_real_escape_string($koneksi, $date);
        $where_clauses[] = "(p.tgl_pinjam = '$date' OR p.tgl_kembali = '$date')";
    }
    
    if (!empty($filter_status)) {
        $filter_status = mysqli_real_escape_string($koneksi, $filter_status);
        $where_clauses[] = "p.status = '$filter_status'";
    }
    
    // Dashboard mode: filter barang yang dikembalikan cuma yang 24 jam terakhir
    if ($dashboard_mode && empty($filter_status)) {
        $where_clauses[] = "(p.status = 'dipinjam' 
                            OR p.status = 'rejected' 
                            OR (p.status = 'dikembalikan' AND p.tgl_kembali >= DATE_SUB(NOW(), INTERVAL 1 DAY)))";
    }
    
    $where = "";
    if (count($where_clauses) > 0) {
        $where = "WHERE " . implode(" AND ", $where_clauses);
    }
    
    $query = "SELECT COUNT(*) as total 
              FROM peminjaman p 
              JOIN users u ON p.id_user = u.id_user 
              JOIN barang b ON p.id_barang = b.id_barang 
              $where";
              
    $result = mysqli_query($koneksi, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return (int)($row['total'] ?? 0);
    }
    return 0;
}

/**
 * Liat history user tertentu - Support search & pagination
 * @param int $id_user - ID user
 * @param string $keyword - Kata kunci (bisa nama barang atau tgl)
 * @param int $limit - Limit record
 * @param int $offset - Offset record
 * @return array - Array data pinjeman dia
 */
function getPeminjamanByUser($id_user, $keyword = '', $limit = null, $offset = 0, $date = '') {
    global $koneksi;
    
    $id_user = (int) $id_user;
    $where = "WHERE p.id_user = $id_user";
    
    if (!empty($keyword)) {
        $keyword = mysqli_real_escape_string($koneksi, $keyword);
        $where .= " AND (b.nama_barang LIKE '%$keyword%' 
                    OR b.kode_barang LIKE '%$keyword%' 
                    OR p.status LIKE '%$keyword%')";
    }

    if (!empty($date)) {
        $date = mysqli_real_escape_string($koneksi, $date);
        $where .= " AND (p.tgl_pinjam = '$date' OR p.tgl_kembali = '$date')";
    }
    
    // Pake JOIN biar dapet nama barang
    $query = "SELECT p.*, b.kode_barang, b.nama_barang 
              FROM peminjaman p 
              JOIN barang b ON p.id_barang = b.id_barang 
              $where
              ORDER BY p.created_at DESC";
    
    if ($limit !== null) {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $query .= " LIMIT $limit OFFSET $offset";
    }
    
    $result = mysqli_query($koneksi, $query);
    
    $data = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    
    return $data;
}

/**
 * Hitung total records pinjeman user (buat pagination)
 */
function countPeminjamanByUser($id_user, $keyword = '', $date = '') {
    global $koneksi;
    $id_user = (int)$id_user;
    
    $where = "WHERE p.id_user = $id_user";
    if (!empty($keyword)) {
        $keyword = mysqli_real_escape_string($koneksi, $keyword);
        $where .= " AND (b.nama_barang LIKE '%$keyword%' 
                    OR b.kode_barang LIKE '%$keyword%' 
                    OR p.status LIKE '%$keyword%')";
    }

    if (!empty($date)) {
        $date = mysqli_real_escape_string($koneksi, $date);
        $where .= " AND (p.tgl_pinjam = '$date' OR p.tgl_kembali = '$date')";
    }
    
    $query = "SELECT COUNT(*) as total 
              FROM peminjaman p 
              JOIN barang b ON p.id_barang = b.id_barang 
              $where";
              
    $result = mysqli_query($koneksi, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return (int)($row['total'] ?? 0);
    }
    return 0;
}

/**
 * Ambil detail satu peminjaman
 * @param int $id_pinjam - ID peminjaman
 * @return array|null - Data atau null
 */
function getPeminjamanById($id_pinjam) {
    global $koneksi;
    
    $id_pinjam = (int) $id_pinjam;
    
    $query = "SELECT p.*, b.kode_barang, b.nama_barang, u.nama as nama_user 
              FROM peminjaman p 
              JOIN barang b ON p.id_barang = b.id_barang 
              JOIN users u ON p.id_user = u.id_user 
              WHERE p.id_pinjam = $id_pinjam";
    
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) == 1) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Ganti status pinjem
 * @param int $id_pinjam - ID peminjaman
 * @param string $status - Status baru ('dipinjam' atau 'dikembalikan')
 * @return array - Status dan pesan
 */
function updateStatusPeminjaman($id_pinjam, $status) {
    global $koneksi;
    
    $id_pinjam = (int) $id_pinjam;
    $status = mysqli_real_escape_string($koneksi, $status);
    
    // Cek status valid ga
    if (!in_array($status, ['dipinjam', 'dikembalikan'])) {
        return ['status' => false, 'pesan' => 'Status ga jelas!'];
    }
    
    // Ambil data pinjeman
    $peminjaman = getPeminjamanById($id_pinjam);
    if (!$peminjaman) {
        return ['status' => false, 'pesan' => 'Data pinjeman ga ketemu!'];
    }
    
    // Cek kalo status udah sama
    if ($peminjaman['status'] == $status) {
        return ['status' => false, 'pesan' => 'Status udah ' . $status . ' keles'];
    }
    
    // Mulai transaksi
    mysqli_begin_transaction($koneksi);
    
    try {
        // Update status pinjem
        $query = "UPDATE peminjaman SET status = '$status' WHERE id_pinjam = $id_pinjam";
        
        if (!mysqli_query($koneksi, $query)) {
            throw new Exception('Gagal update status');
        }
        
        // Kalo dikembaliin, balikin stok barang
        if ($status == 'dikembalikan') {
            if (!tambahStok($peminjaman['id_barang'], $peminjaman['jumlah'])) {
                throw new Exception('Gagal nambahin stok barang');
            }
        }
        
        // Kalo dipinjam lagi (dari dikembalikan), kurangi stok lagi
        if ($status == 'dipinjam' && $peminjaman['status'] == 'dikembalikan') {
            if (!kurangiStok($peminjaman['id_barang'], $peminjaman['jumlah'])) {
                throw new Exception('Gagal ngurangin stok barang');
            }
        }
        
        // Simpen permanen
        mysqli_commit($koneksi);
        
        return ['status' => true, 'pesan' => 'Status berhasil diganti!'];
        
    } catch (Exception $e) {
        // Batalin transaksi
        mysqli_rollback($koneksi);
        return ['status' => false, 'pesan' => $e->getMessage()];
    }
}


/**
 * Balikin barang (versi user)
 * @param int $id_pinjam - ID peminjaman
 * @return array - Status dan pesan
 */
function ajukanPengembalian($id_pinjam, $tgl_kembali = '') {
    global $koneksi;
    
    $id_pinjam = (int) $id_pinjam;
    // Kalo tgl_kembali kosong, pake hari ini
    if (empty($tgl_kembali)) {
        $tgl_kembali = date('Y-m-d');
    }
    $tgl_kembali = mysqli_real_escape_string($koneksi, $tgl_kembali);
    
    // Ambil data pinjeman
    $peminjaman = getPeminjamanById($id_pinjam);
    if (!$peminjaman || $peminjaman['status'] != 'dipinjam') {
        return ['status' => false, 'pesan' => 'Pinjeman ga ketemu atau emang udah dibalikin!'];
    }
    
    // Mulai transaksi
    mysqli_begin_transaction($koneksi);
    
    try {
        // Update status jadi dikembalikan dan catat tanggal kembalinya
        $query = "UPDATE peminjaman SET status = 'dikembalikan', tgl_kembali = '$tgl_kembali' WHERE id_pinjam = $id_pinjam";
        if (!mysqli_query($koneksi, $query)) {
            throw new Exception('Gagal update status');
        }
        
        // Balikin stok
        if (!tambahStok($peminjaman['id_barang'], $peminjaman['jumlah'])) {
            throw new Exception('Gagal balikin stok barang');
        }
        
        // Simpen permanen
        mysqli_commit($koneksi);
        
        return ['status' => true, 'pesan' => 'Barang berhasil dipulangin!'];
        
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        return ['status' => false, 'pesan' => 'Gagal mulangin barang: ' . $e->getMessage()];
    }
}
?>