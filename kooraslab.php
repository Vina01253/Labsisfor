<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['session_role']) || $_SESSION['session_role'] !== 'kooraslab') {
        header('location: login');
        exit();
    }

    // ========================================================================
    // [XAMPP-TWEAK] SINGLE-DEVICE LOCK (1 AKUN = 1 PERANGKAT)
    // Blok yang sama diterapkan di semua entry point role agar pembatasan
    // berlaku untuk SEMUA akun, bukan hanya admin.
    // ========================================================================
    include __DIR__ . '/config.php';
    include __DIR__ . '/model/class/class_device_check.php';

    if (!device_lock_bypass()) {
        $chk_col = mysqli_query($db, "SHOW COLUMNS FROM user LIKE 'session_device_id'");
        if ($chk_col && mysqli_num_rows($chk_col) === 0) {
            mysqli_query($db, "ALTER TABLE user ADD COLUMN session_device_id VARCHAR(255) NULL DEFAULT NULL COMMENT 'Token perangkat terakhir yang login (single-device lock)'");
        }
        device_lock_enforce($db, $_SESSION['session_username'], 'login');
    }

    define('__FOLDER_VIEW__', __DIR__ . '/app/view/');
    define('__FOLDER_LAYOUT__', __DIR__. '/app/view/layout/');
    define('__FOLDER_IMAGES__', __DIR__. '/asset/images/');
    define('__FOLDER_CLASS__', __DIR__. '/model/class/');
    define('__FOLDER_LINK__', __DIR__. '/model/link/');
    define('__FOLDER_JS__', __DIR__. '/asset/js/');
    
    $__asset = "asset/";

    $page = isset($_GET['page']) ? $_GET['page'] : 'login';

    $file_controller = __DIR__ . "/app/control/".$page.".php";

    if(file_exists($file_controller)){
        include $file_controller;
    }
    else{
        header('location: 404');
    }
?>