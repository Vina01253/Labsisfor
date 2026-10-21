<?php
// ============================================================================
//  [XAMPP-TWEAK] TEST HTTP END-TO-END: SINGLE-DEVICE LOCK
// ----------------------------------------------------------------------------
//  Jalankan (PowerShell / cmd) dari folder proyek:
//    1)  C:\Xamppp\php\php.exe -S 127.0.0.1:8099
//    2)  C:\Xamppp\php\php.exe tests\http_device_lock.php
//
//  Skrip ini berbicara ke server seperti dua "perangkat" berbeda (cookie jar
//  terpisah), login sebagai akun yang sama dua kali, lalu memastikan:
//    - perangkat A bisa buka admin setelah login A
//    - setelah B login, perangkat A DITENDANG ke halaman login
//    - perangkat B tetap bisa buka admin
// ============================================================================

$base = 'http://127.0.0.1:8099';
$fails = 0;
function ok($label, $cond) { global $fails; echo ($cond ? "  [OK]   " : "  [FAIL] ") . $label . PHP_EOL; if (!$cond) $fails++; }

function http($url, $cookieJar, $agent, $post = null) {
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HEADER         => true,
        CURLOPT_COOKIEJAR      => $cookieJar,
        CURLOPT_COOKIEFILE     => $cookieJar,
        CURLOPT_USERAGENT      => $agent,
        CURLOPT_TIMEOUT        => 15,
    ));
    if ($post !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    $resp = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return array('body' => $resp, 'url' => $info['url'], 'code' => $info['http_code']);
}

// --- Siapkan akun uji -------------------------------------------------------
include __DIR__ . '/../config.php';
$uname = 'TEST_HTTP_LOCK';
$pass  = 'RahasiaUji123';
mysqli_query($db, "DELETE FROM user WHERE username = '$uname'");
$hash = password_hash($pass, PASSWORD_BCRYPT);
$stmt = mysqli_prepare($db, "INSERT INTO user (username, password, role) VALUES (?, ?, 'admin')");
mysqli_stmt_bind_param($stmt, "ss", $uname, $hash);
mysqli_stmt_execute($stmt);

$jarA = __DIR__ . '/_jar_a.txt';
$jarB = __DIR__ . '/_jar_b.txt';
@unlink($jarA); @unlink($jarB);

try {
    echo "== 1. Perangkat A login ==" . PHP_EOL;
    $r = http("$base/login", $jarA, 'DeviceA-UA', array('login' => '1', 'inputid' => $uname, 'inputpassword' => $pass));
    ok("A diarahkan ke halaman admin setelah login", strpos($r['url'], 'admin') !== false);

    echo PHP_EOL . "== 2. Perangkat A membuka admin (DIIZINKAN) ==" . PHP_EOL;
    $r = http("$base/admin?page=home", $jarA, 'DeviceA-UA');
    ok("A tetap di area admin (tidak dilempar ke login)", strpos($r['url'], 'admin') !== false && strpos($r['url'], 'login') === false);

    echo PHP_EOL . "== 3. Perangkat B login (menendang A) ==" . PHP_EOL;
    $r = http("$base/login", $jarB, 'DeviceB-UA', array('login' => '1', 'inputid' => $uname, 'inputpassword' => $pass));
    ok("B berhasil masuk admin", strpos($r['url'], 'admin') !== false);

    echo PHP_EOL . "== 4. Perangkat A mencoba admin lagi (DITENDANG) ==" . PHP_EOL;
    $r = http("$base/admin?page=home", $jarA, 'DeviceA-UA');
    ok("A kini dilempar ke halaman login", strpos($r['url'], 'login') !== false);

    echo PHP_EOL . "== 5. Perangkat B masih bisa admin (DIIZINKAN) ==" . PHP_EOL;
    $r = http("$base/admin?page=home", $jarB, 'DeviceB-UA');
    ok("B tetap di area admin", strpos($r['url'], 'admin') !== false && strpos($r['url'], 'login') === false);
} finally {
    mysqli_query($db, "DELETE FROM user WHERE username = '$uname'");
    @unlink($jarA); @unlink($jarB);
}

echo PHP_EOL . ($fails === 0 ? "SEMUA TEST HTTP LULUS." : "ADA $fails TEST GAGAL.") . PHP_EOL;
exit($fails === 0 ? 0 : 1);
