<?php
// ============================================================================
// [XAMPP-TWEAK] FIX: AKTIFKAN OUTPUT BUFFERING
// ----------------------------------------------------------------------------
// Kenapa perlu? File ini di-include oleh lay-login.php SETELAH beberapa baris
// HTML dikirim ke browser. Tanpa output buffer, semua header('location: ...')
// (redirect ke halaman admin / kembali ke login) akan GAGAL dengan warning
// "headers already sent". Dengan ob_start(), redirect kembali normal di
// lingkungan mana pun (Apache XAMPP maupun PHP built-in server).
// ============================================================================
if (!ob_get_level()) {
    ob_start();
}

include "config.php";

// [XAMPP-TWEAK] Muat helper single-device lock (1 akun = 1 perangkat).
include_once __DIR__ . "/class_device_check.php";

global $arrayuserdatabyusername, $ALERT_inputloginkosong, $ALERT_inputloginusernamesalah, $ALERT_inputloginpasswordsalah;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['alert'])) {
    $alert =  $_SESSION['alert'];
    unset($_SESSION['alert']);
}

// input cookie & create session
if (isset($_COOKIE['cookie_username'])) {
    $cookie_username = $_COOKIE['cookie_username'];
    $cookie_password = $_COOKIE['cookie_password'];

    $stmt_cookie = mysqli_prepare($db, "SELECT * FROM user WHERE username = ?");
    mysqli_stmt_bind_param($stmt_cookie, "s", $cookie_username);
    mysqli_stmt_execute($stmt_cookie);
    $resultlogincookie = mysqli_fetch_array(mysqli_stmt_get_result($stmt_cookie));

    if ($resultlogincookie && $resultlogincookie['password'] == $cookie_password) {
        $_SESSION['session_username'] = $cookie_username;
        $_SESSION['session_password'] = $cookie_password;
        $_SESSION['session_role'] = $resultlogincookie['role'];

        // ================================================================
        // [XAMPP-TWEAK] PEMULIHAN SESI DARI COOKIE
        // ================================================================
        // Cookie hanya hidup 1 menit (lihat setcookie di bawah), jadi blok ini
        // jarang terpakai. Bila sesi dipulihkan dari cookie, perangkat ini
        // tetap harus memegang token perangkat yang sah. Jika token perangkat
        // sudah hilang / digantikan login lain, sesi tidak dipulihkan
        // (pengguna harus login ulang).
        // ================================================================
        if (!device_lock_bypass()) {
            $device_cookie = isset($_COOKIE[DEVICE_LOCK_COOKIE]) ? $_COOKIE[DEVICE_LOCK_COOKIE] : '';
            $device_db_cookie = isset($resultlogincookie['session_device_id']) ? $resultlogincookie['session_device_id'] : null;

            if (empty($device_cookie) || $device_db_cookie === null || !hash_equals($device_db_cookie, $device_cookie)) {
                // Tidak sah -> batalkan pemulihan sesi, minta login ulang.
                $_SESSION['session_username'] = "";
                $_SESSION['session_password'] = "";
                unset($_SESSION['session_role']);
            } else {
                $_SESSION['session_device_token'] = $device_cookie;
            }
        }
    }
}

// redirect by role
if (isset($_SESSION['session_username'])) {

    if ($_SESSION['session_role'] == "admin") {
        header('location: admin?page=home');
    } elseif ($_SESSION['session_role'] == "ketualab") {
        header('location: ketualab?page=home');
    } elseif ($_SESSION['session_role'] == "dosen") {
        header('location: dosen?page=home');
    } elseif ($_SESSION['session_role'] == "kooraslab") {
        header('location: kooraslab?page=home');
    } else {
        header('location: mahasiswa?page=home');
    }

    exit();
}

if (isset($_POST['login'])) {
    $usernamelogin = $_POST['inputid'];
    $passwordlogin = $_POST['inputpassword'];

    // SQL Get user data by username login using prepared statement
    $stmt_login = mysqli_prepare($db, "SELECT * FROM user WHERE username = ?");
    mysqli_stmt_bind_param($stmt_login, "s", $usernamelogin);
    mysqli_stmt_execute($stmt_login);
    $arrayuserdatabyusername = mysqli_fetch_array(mysqli_stmt_get_result($stmt_login));

    if ($usernamelogin == '' or $passwordlogin == '') {
        $_SESSION['alert'] = $ALERT_inputloginkosong;
        header('location: login');
    } else {
        if (empty($arrayuserdatabyusername['username'])) {
            $_SESSION['alert'] = $ALERT_inputloginusernamesalah;
            header('location: login');
        } elseif (!password_verify($passwordlogin, $arrayuserdatabyusername['password'])) {
            // Fallback: Cek apakah password di database masih menggunakan hash SHA-512 lama
            $enc_passlogin = hash_init('sha512');
            hash_update($enc_passlogin, $passwordlogin);
            $legacy_hash = hash_final($enc_passlogin);

            if ($arrayuserdatabyusername['password'] === $legacy_hash) {
                // Auto-migrate hash SHA-512 lama ke Bcrypt baru di database
                $new_bcrypt_hash = password_hash($passwordlogin, PASSWORD_BCRYPT);
                $stmt_rehash = mysqli_prepare($db, "UPDATE user SET password = ? WHERE username = ?");
                mysqli_stmt_bind_param($stmt_rehash, "ss", $new_bcrypt_hash, $usernamelogin);
                mysqli_stmt_execute($stmt_rehash);
                $arrayuserdatabyusername['password'] = $new_bcrypt_hash;
            } else {
                $_SESSION['alert'] = $ALERT_inputloginpasswordsalah;
                header('location: login');
            }
        }
    }

    if (empty($_SESSION['alert'])) {
        $_SESSION['session_username'] = $usernamelogin;
        $_SESSION['session_password'] = $arrayuserdatabyusername['password'];
        $_SESSION['session_role'] = $arrayuserdatabyusername['role'];

        // ================================================================
        // [XAMPP-TWEAK] KUNCI PERANGKAT (SINGLE-DEVICE LOCK)
        // ================================================================
        // Perangkat yang baru login ini didaftarkan sebagai SATU-SATUNYA
        // perangkat sah untuk akun tersebut. Jika akun yang sama login dari
        // perangkat lain, token lama tergantikan dan perangkat lama otomatis
        // kehilangan akses (dikeluarkan saat membuka admin.php).
        //
        // Catatan: pembatasan berlaku untuk SEMUA akun (admin, ketualab,
        // dosen, kooraslab, mahasiswa) karena dijalankan di titik login ini.
        // Jika HANYA ingin membatasi akun admin, tambahkan syarat:
        //   if ($arrayuserdatabyusername['role'] === 'admin') { ... }
        // ================================================================
        device_lock_claim_device($db, $usernamelogin);

        $cookie_name = 'cookie_username';
        $cookie_value = $usernamelogin;
        $cookie_time = time() + (60 * 1);
        setcookie($cookie_name, $cookie_value, $cookie_time, "/");

        $cookie_name = 'cookie_password';
        $cookie_value = $arrayuserdatabyusername['password'];
        $cookie_time = time() + (60 * 1);
        setcookie($cookie_name, $cookie_value, $cookie_time, "/");

        $cookie_name = 'cookie_role';
        $cookie_value = $arrayuserdatabyusername['role'];
        $cookie_time = time() + (60 * 1);
        setcookie($cookie_name, $cookie_value, $cookie_time, "/");

        if ($_SESSION['session_role'] == "admin") {
            header('location: admin?page=home');
        } elseif ($_SESSION['session_role'] == "ketualab") {
            header('location: ketualab?page=home');
        } elseif ($_SESSION['session_role'] == "dosen") {
            header('location: dosen?page=home');
        } elseif ($_SESSION['session_role'] == "kooraslab" || $_SESSION['session_role'] == "aslab") {
            header('location: kooraslab?page=home');
        } else {
            header('location: mahasiswa?page=home');
        }
    }
}
