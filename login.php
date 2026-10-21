<?php
    // ========================================================================
    // [XAMPP-TWEAK] OUTPUT BUFFERING UNTUK REDIRECT LOGIN
    // ------------------------------------------------------------------------
    // Wajib ada di baris paling atas, SEBELUM output apa pun. Tanpa ini,
    // header('location: ...') di class_login.php (yang di-include setelah HTML
    // mulai dikirim) akan gagal dengan "headers already sent", sehingga
    // redirect ke halaman admin / kembali ke login tidak berjalan.
    // ========================================================================
    if (!ob_get_level()) {
        ob_start();
    }

    define('__FOLDER_VIEW__', __DIR__ . '/app/view/');
    define('__FOLDER_LAYOUT__', __DIR__. '/app/view/layout/');
    define('__FOLDER_IMAGES__', __DIR__. '/asset/images/');
    define('__FOLDER_CLASS__', __DIR__. '/model/class/');
    define('__FOLDER_LINK__', __DIR__. '/model/link/');
    
    $__asset = "asset/";

    $page = "login";

    $file_controller = __DIR__ . "/app/control/".$page.".php";

    if(file_exists($file_controller)){
        include $file_controller;
    }
    else{
        header('location: 404');
    } 
?>