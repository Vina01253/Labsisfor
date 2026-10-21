<?php
// ============================================================================
//  [XAMPP-TWEAK] TEST SCRIPT: SINGLE-DEVICE LOCK
// ----------------------------------------------------------------------------
//  Jalankan:  C:\Xamppp\php\php.exe tests\test_device_lock.php
//  Skrip ini mensimulasikan dua perangkat berbeda untuk akun yang sama,
//  lalu memastikan perangkat lama TIDAK bisa mengakses (terkunci).
//  Tidak menyentuh data asli: memakai user dummy 'TEST_DEVICE_LOCK'.
// ============================================================================

include __DIR__ . '/../config.php';
include __DIR__ . '/../model/class/class_device_check.php';

$fails = 0;
function check($label, $cond) {
    global $fails;
    echo ($cond ? "  [OK]   " : "  [FAIL] ") . $label . PHP_EOL;
    if (!$cond) { $fails++; }
}

$uname = 'TEST_DEVICE_LOCK';

// Siapkan user dummy.
mysqli_query($db, "DELETE FROM user WHERE username = '$uname'");
mysqli_query($db, "INSERT INTO user (username, password, role) VALUES ('$uname', 'x', 'admin')");

echo "== 1. Kolom session_device_id ==" . PHP_EOL;
$c = mysqli_query($db, "SHOW COLUMNS FROM user LIKE 'session_device_id'");
check("Kolom session_device_id ada di tabel user", $c && mysqli_num_rows($c) > 0);

echo PHP_EOL . "== 2. Perangkat A login (mengklaim perangkat) ==" . PHP_EOL;
$_SERVER['HTTP_USER_AGENT'] = 'DeviceA-Browser';
$_SERVER['REMOTE_ADDR'] = '192.168.1.10';
$_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'id-ID';
$tokenA = device_lock_claim_device($db, $uname);
check("Token A terbentuk", strlen($tokenA) > 60);
check("Session menyimpan token A", $_SESSION['session_device_token'] === $tokenA);

echo PHP_EOL . "== 3. Perangkat A mengakses (harus DIIZINKAN) ==" . PHP_EOL;
$_COOKIE[DEVICE_LOCK_COOKIE] = $tokenA;
check("device_lock_token_valid(tokenA) benar", device_lock_token_valid($tokenA));
$allowedA = true;
// enforce() hanya akan exit kalau ditolak; di sini kita cek kondisinya langsung.
$row = mysqli_fetch_array(mysqli_query($db, "SELECT session_device_id FROM user WHERE username='$uname'"));
check("Token di DB sama dengan token perangkat A", hash_equals($row['session_device_id'], $tokenA));

echo PHP_EOL . "== 4. Perangkat B login (menendang A) ==" . PHP_EOL;
$_SERVER['HTTP_USER_AGENT'] = 'DeviceB-Browser';
$_SERVER['REMOTE_ADDR'] = '10.0.0.99';
$tokenB = device_lock_claim_device($db, $uname);
check("Token B BERBEDA dari token A", $tokenA !== $tokenB);
$row = mysqli_fetch_array(mysqli_query($db, "SELECT session_device_id FROM user WHERE username='$uname'"));
check("Token di DB sekarang milik B", hash_equals($row['session_device_id'], $tokenB));

echo PHP_EOL . "== 5. Perangkat A mencoba akses lagi (harus DITOLAK) ==" . PHP_EOL;
$_COOKIE[DEVICE_LOCK_COOKIE] = $tokenA; // cookie lama A
$row = mysqli_fetch_array(mysqli_query($db, "SELECT session_device_id FROM user WHERE username='$uname'"));
$dbToken = $row['session_device_id'];
$isValidA = hash_equals($dbToken, $tokenA) && device_lock_token_valid($tokenA);
check("Token A tidak lagi dianggap sah (A terkunci)", $isValidA === false);

echo PHP_EOL . "== 6. Perangkat B mengakses (harus DIIZINKAN) ==" . PHP_EOL;
$_COOKIE[DEVICE_LOCK_COOKIE] = $tokenB;
$isValidB = hash_equals($dbToken, $tokenB) && device_lock_token_valid($tokenB);
check("Token B sah (B adalah pemilik sesi)", $isValidB === true);

// Bersihkan.
mysqli_query($db, "DELETE FROM user WHERE username = '$uname'");

echo PHP_EOL . ($fails === 0 ? "SEMUA TEST LULUS." : "ADA $fails TEST GAGAL.") . PHP_EOL;
exit($fails === 0 ? 0 : 1);
