<?php
    // ========================================================================
    // [XAMPP-TWEAK] LOGOUT + LEPAS KUNCI PERANGKAT
    // ========================================================================
    // Pastikan helper device lock tersedia supaya konstanta DEVICE_LOCK_COOKIE
    // dikenali saat menghapus cookie perangkat di browser.
    // ========================================================================
    include_once __DIR__ . "/model/class/class_device_check.php";

    session_start();
    $_SESSION['session_username'] = "";
    $_SESSION['session_password'] = "";

    // [XAMPP-TWEAK] Lepas token perangkat dari sesi saat logout.
    // Token di database akan otomatis ditimpa ketika login berikutnya,
    // sehingga tidak perlu query DB tambahan di sini.
    unset($_SESSION['session_device_token']);
    unset($_SESSION['session_role']);
    session_destroy();

    $cookie_name = "cookie_username";
    $cookie_value = "";
    $time = time() - (60 * 1);
    setcookie($cookie_name,$cookie_value,$time,"/");

    $cookie_name = "cookie_password";
    $cookie_value = "";
    $time = time() - (60 * 1);
    setcookie($cookie_name,$cookie_value,$time,"/");

    // [XAMPP-TWEAK] Hapus cookie token perangkat agar perangkat lain bersih.
    setcookie(DEVICE_LOCK_COOKIE, "", time() - 3600, "/");

    header('location: index');