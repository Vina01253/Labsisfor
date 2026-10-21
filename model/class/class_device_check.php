<?php
/**
 * ============================================================================
 *  [XAMPP-TWEAK] SINGLE-DEVICE LOCK (1 AKUN = 1 PERANGKAT)
 * ============================================================================
 *  File ini adalah INTI dari fitur "hanya 1 perangkat yang boleh mengakses".
 *
 *  Cara kerja singkat:
 *   1. Saat login sukses, server membuat "device_token" acak (token perangkat)
 *      lalu disimpan ke kolom user.session_device_id di database.
 *   2. Token yang sama juga disimpan di $_SESSION dan di sebuah cookie khusus
 *      (khusus PERANGKAT/BROWSER tersebut).
 *   3. Setiap kali sebuah halaman admin dibuka, token yang dikirim browser
 *      dibandingkan dengan token terakhir yang tersimpan di database.
 *        - Cocok  -> silakan lanjut (perangkat ini yang sah).
 *        - BEDA   -> berarti ada login baru dari perangkat lain, jadi
 *                    perangkat lama ini DIPAKSA LOGOUT.
 *
 *  Jadi hanya perangkat yang login PALING TERAKHIR yang bisa mengakses;
 *  login dari perangkat lain otomatis "menendang" perangkat sebelumnya.
 * ============================================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Nama cookie khusus perangkat. Jangan diubah setelah produksi, karena
 * setiap perubahan akan membuat semua sesi aktif harus login ulang.
 */
if (!defined('DEVICE_LOCK_COOKIE')) {
    define('DEVICE_LOCK_COOKIE', 'labsisfor_device_token');
}

/**
 * [XAMPP-TWEAK] Daftar alamat (IP) yang DIBEBASKAN dari pengecekan perangkat.
 * Ini berguna untuk development / login langsung dari server itu sendiri.
 * Untuk keamanan penuh, kosongkan array ini (hapus '127.0.0.1' dan '::1').
 */
if (!function_exists('device_lock_bypass')) {
    function device_lock_bypass()
    {
        /* -------------------------------------------------------------------
         * [XAMPP-TWEAK] Daftar IP yang dibebaskan dari pengecekan perangkat.
         * Default: hanya localhost (untuk development).
         *
         * Cara mengubah:
         *  - Untuk MENONAKTIFKAN bypass (mode keamanan penuh), set environment
         *    variable DEVICE_LOCK_DISABLE_BYPASS=1, contoh:
         *        set DEVICE_LOCK_DISABLE_BYPASS=1
         *  - Atau edit langsung array di bawah ini.
         * ------------------------------------------------------------------- */
        if (getenv('DEVICE_LOCK_DISABLE_BYPASS') === '1') {
            return false;
        }

        $bypass_ips = array('127.0.0.1', '::1');

        if (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], $bypass_ips, true)) {
            return true;
        }

        /* -------------------------------------------------------------------
         * [XAMPP-TWEAK] OPSI 2 - BYPASS BERDASARKAN KODE RAHASIA (magic link)
         * Jika Anda ingin tetap menguji dari perangkat lain tanpa terkunci,
         * buka URL:  admin?page=home&device_bypass=KUNCI_RAHASIA
         * Ganti 'KUNCI_RAHASIA' dengan kode rahasia Anda sendiri.
         * Hapus/komentari blok ini saat sudah tidak dibutuhkan.
         * ------------------------------------------------------------------- */
        // if (isset($_GET['device_bypass']) && $_GET['device_bypass'] === 'KUNCI_RAHASIA') {
        //     return true;
        // }

        return false;
    }
}

/**
 * Membuat sidik jari perangkat (device signature) dari data yang tersedia.
 * Ini BUKAN pengaman tunggal, tapi penguat: token di database + cookie +
 * sidik jari perangkat. Jika browser berbeda / user-agent berubah, sidik jari
 * ikut berubah sehingga token tidak lagi dianggap cocok.
 */
if (!function_exists('device_lock_signature')) {
    function device_lock_signature()
    {
        // [XAMPP-TWEAK] Ganti nilai salt ini dengan string acak rahasia milik Anda.
        $salt = 'LABSISFOR_DEVICE_SALT_2024';

        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $ip    = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $lang  = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '';

        return hash('sha256', $salt . '|' . $agent . '|' . $ip . '|' . $lang);
    }
}

/**
 * Membuat token perangkat baru (acak + sidik jari).
 */
if (!function_exists('device_lock_new_token')) {
    function device_lock_new_token()
    {
        return bin2hex(random_bytes(32)) . '.' . device_lock_signature();
    }
}

/**
 * Memeriksa apakah token berasal dari perangkat yang benar.
 */
if (!function_exists('device_lock_token_valid')) {
    function device_lock_token_valid($token)
    {
        if (empty($token) || strpos($token, '.') === false) {
            return false;
        }

        $parts = explode('.', $token);
        $signature = end($parts); // bagian terakhir adalah sidik jari

        // Bandingkan sidik jari dengan perangkat yang sedang mengakses.
        return hash_equals(device_lock_signature(), $signature);
    }
}

/**
 * [XAMPP-TWEAK] INTI PENGEKANGAN.
 * Panggil fungsi ini di HANYA SATU tempat (mis. admin.php) tepat setelah
 * pengecekan role. Fungsi akan:
 *   - mengeluarkan perangkat yang bukan pemilik token terakhir (forced logout).
 *   - mengizinkan perangkat yang token-nya cocok.
 *
 * @param mysqli $db          Koneksi database.
 * @param string $username    Username yang sedang mengakses.
 * @param string $login_url   URL halaman login (default 'login').
 */
if (!function_exists('device_lock_enforce')) {
    function device_lock_enforce($db, $username, $login_url = 'login')
    {
        // Bebaskan alamat localhost / server sendiri jika diizinkan.
        if (device_lock_bypass()) {
            return true;
        }

        // Token yang dikirim oleh perangkat ini (prioritas: cookie, lalu session).
        $device_token = isset($_COOKIE[DEVICE_LOCK_COOKIE]) ? $_COOKIE[DEVICE_LOCK_COOKIE] : '';
        if ($device_token === '' && isset($_SESSION['session_device_token'])) {
            $device_token = $_SESSION['session_device_token'];
        }

        // Ambil token terakhir yang sah dari database.
        $stmt = mysqli_prepare($db, "SELECT session_device_id FROM user WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_array(mysqli_stmt_get_result($stmt));
        $device_db = $row ? $row['session_device_id'] : null;

        // Perangkat ini tidak punya token, atau token tidak identik dengan DB,
        // atau sidik jari perangkat tidak cocok -> tolak (forced logout).
        $is_valid = ($device_db !== null)
            && !empty($device_token)
            && hash_equals($device_db, $device_token)
            && device_lock_token_valid($device_token);

        if (!$is_valid) {
            device_lock_release_session($login_url, 'Perangkat ini tidak lagi berhak atas sesi tersebut. Silakan login kembali.');
        }

        return true;
    }
}

/**
 * [XAMPP-TWEAK] Membersihkan session + cookie perangkat dan melempar ke login.
 * Dipakai baik saat forced logout maupun logout normal.
 *
 * @param string $login_url
 * @param string|null $message  Pesan/alert opsional yang ditampilkan di login.
 */
if (!function_exists('device_lock_release_session')) {
    function device_lock_release_session($login_url = 'login', $message = null)
    {
        if ($message !== null) {
            $_SESSION['alert'] = "
                <div class='alert alert-danger alert-dismissible'>
                    <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                    <strong>Perhatian!</strong> " . htmlspecialchars($message, ENT_QUOTES) . "
                </div>
            ";
        }

        $_SESSION['session_username'] = "";
        $_SESSION['session_password'] = "";
        unset($_SESSION['session_device_token']);
        unset($_SESSION['session_role']);
        session_destroy();

        // Hapus cookie perangkat di browser ini.
        $time = time() - 3600;
        setcookie(DEVICE_LOCK_COOKIE, "", $time, "/");
        setcookie('cookie_username', "", $time, "/");
        setcookie('cookie_password', "", $time, "/");
        setcookie('cookie_role', "", $time, "/");

        header('location: ' . $login_url);
        exit();
    }
}

/**
 * [XAMPP-TWEAK] Mendaftarkan perangkat ini sebagai SATU-SATUNYA perangkat sah
 * untuk akun tersebut. Dipanggil dari proses login setelah password benar.
 * Login baru otomatis mengganti token lama di database (perangkat lama tergusur).
 *
 * @return string Token perangkat yang baru dibuat.
 */
if (!function_exists('device_lock_claim_device')) {
    function device_lock_claim_device($db, $username)
    {
        $token = device_lock_new_token();

        $stmt = mysqli_prepare($db, "UPDATE user SET session_device_id = ? WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "ss", $token, $username);
        mysqli_stmt_execute($stmt);

        // Simpan di session dan cookie perangkat ini.
        $_SESSION['session_device_token'] = $token;
        // Cookie 1 hari; sesudah itu perangkat harus login kembali.
        setcookie(DEVICE_LOCK_COOKIE, $token, time() + 86400, "/");

        return $token;
    }
}
