# 🔒 Single-Device Lock — 1 Akun Hanya 1 Perangkat

> **Fitur:** Membatasi akses akun (termasuk **admin**) agar hanya bisa dipakai dari
> **satu perangkat** pada satu waktu. Jika akun yang sama login dari perangkat
> lain, perangkat lama **otomatis dikeluarkan** (forced logout) saat membuka
> halaman admin.
>
> Semua penanda perubahan di kode diberi komentar **`[XAMPP-TWEAK]`**.
> Cari dengan Ctrl+F: `[XAMPP-TWEAK]`.

---

## 1. Cara Kerja Singkat

```
Login berhasil  →  server buat token perangkat acak  →  simpan ke
                    kolom user.session_device_id  +  cookie  +  session
                 →  perangkat lain login  →  token lama tergantikan
                 →  perangkat lama buka admin  →  token TIDAK cocok  →  LOGOUT
```

Hanya perangkat yang **login paling terakhir** yang bisa mengakses.

---

## 2. File yang Diubah / Ditambahkan

| File | Peran | Penanda |
|---|---|---|
| `model/class/class_device_check.php` | **BARU** — inti logika kunci perangkat | seluruh file |
| `admin.php` | Pintu masuk admin, memasang kunci | blok `[XAMPP-TWEAK]` |
| `ketualab.php`, `dosen.php`, `kooraslab.php`, `mahasiswa.php` | Pintu masuk role lain, kunci yang sama | blok `[XAMPP-TWEAK]` |
| `model/class/class_login.php` | Klaim perangkat saat login sukses | blok `[XAMPP-TWEAK]` |
| `logout.php` | Lepas token perangkat saat logout | blok `[XAMPP-TWEAK]` |
| `login.php` | Aktifkan output buffering agar redirect jalan | blok `[XAMPP-TWEAK]` |
| `database/migration_device_lock.sql` | Menambah kolom `session_device_id` | seluruh file |

---

## 3. Cara Menjalankan (WAJIB sekali saja)

### a. Migrasi database
Pilih salah satu:
- **phpMyAdmin**: pilih database `labsisfor` → tab **SQL** → tempel isi
  `database/migration_device_lock.sql` → **Go**.
- **Command line**:
  ```
  C:\Xamppp\mysql\bin\mysql.exe -u root labsisfor < database\migration_device_lock.sql
  ```
- **Cara paling mudah**: cukup buka `admin.php` sekali di browser. Kode sudah
  **auto-migrate** — kolom dibuat otomatis jika belum ada.

### b. Cek hasil
```
C:\Xamppp\mysql\bin\mysql.exe -u root labsisfor -e "SHOW COLUMNS FROM user LIKE 'session_device_id';"
```
Harus muncul kolom `session_device_id` bertipe `varchar(255)`.


---

## 4. ★ CARA MERUBAH (TANDA PENJELASAN)

### A. Mengubah siapa yang dibatasi (semua akun vs hanya admin)

**Saat ini: SEMUA akun dibatasi** (karena `device_lock_claim_device()`
dipanggil di `class_login.php` untuk semua role).

- **Agar hanya ADMIN yang dibatasi:**
  Buka `model/class/class_login.php`, cari blok `[XAMPP-TWEAK] KUNCI PERANGKAT`,
  ganti baris:
  ```php
  device_lock_claim_device($db, $usernamelogin);
  ```
  menjadi:
  ```php
  if ($arrayuserdatabyusername['role'] === 'admin') {
      device_lock_claim_device($db, $usernamelogin);
  }
  ```

### B. Menambah / menghapus IP yang dikecualikan (bypass)

Buka `model/class/class_device_check.php`, cari `[XAMPP-TWEAK] Daftar alamat (IP)`,
ubah array:
```php
$bypass_ips = array('127.0.0.1', '::1');
```
- **Tambahkan** IP lain, contoh: `array('127.0.0.1', '::1', '192.168.1.5')`.
- **Matikan bypass sama sekali** (mode keamanan penuh): kosongkan jadi
  `array()` **atau** set environment variable:
  ```
  set DEVICE_LOCK_DISABLE_BYPASS=1
  ```

### C. Mengaktifkan bypass sementara lewat URL (untuk uji coba)

Di `class_device_check.php`, cari `OPSI 2 - BYPASS BERDASARKAN KODE RAHASIA`,
hapus tanda komentar `//` pada blok `if (isset($_GET['device_bypass'])...)`
dan ganti `KUNCI_RAHASIA` dengan kode rahasia Anda. Lalu buka:
```
admin?page=home&device_bypass=KUNCI_RAHASIA
```

### D. Mengubah masa berlaku cookie perangkat

Di `class_device_check.php` → fungsi `device_lock_claim_device()`, cari:
```php
setcookie(DEVICE_LOCK_COOKIE, $token, time() + 86400, "/");
```
- `86400` = 1 hari. Ganti nilainya (detik), misal `time() + (3600 * 8)` = 8 jam.

### E. Mengubah salt sidik jari perangkat

Di `class_device_check.php` → fungsi `device_lock_signature()`, cari:
```php
$salt = 'LABSISFOR_DEVICE_SALT_2024';
```
Ganti dengan string acak rahasia milik Anda. **Setelah diubah, semua sesi
aktif harus login ulang.**

### F. Menyesuaikan pesan forced logout

Di `class_device_check.php` → fungsi `device_lock_enforce()`, cari teks:
```
'Perangkat ini tidak lagi berhak atas sesi tersebut. Silakan login kembali.'
```
Ubah sesuai keinginan.

---

## 5. Cara Menguji

### Uji logika (cepat, tanpa server)
```
C:\Xamppp\php\php.exe tests\test_device_lock.php
```

### Uji end-to-end lewat HTTP (paling meyakinkan)
```
tests\run_http_test.bat
```
Skrip ini menyalakan server sementara, mensimulasikan **2 perangkat**
(cookie terpisah) untuk akun yang sama, lalu memastikan perangkat lama
**ditendang** dan perangkat baru **tetap bisa** mengakses.

> Catatan: folder `tests/` hanya untuk pengujian. Boleh dihapus saat produksi.

---

## 6. Hal Penting & Keterbatasan

- **Akun offline**: jika pengguna menutup browser tanpa logout, token di DB
  tetap miliknya sampai ada login baru. Login dari perangkat lain akan
  otomatis menendangnya.
- **Jaringan NAT / IP berubah-ubah** (misal ponsel berpindah jaringan):
  `REMOTE_ADDR` berubah → sidik jari berubah → pengguna harus login ulang.
  Untuk kasus ini, kurangi sensitivitas dengan mengeluarkan `$ip` dari
  `device_lock_signature()` pada `class_device_check.php`.
- **Bypass localhost ON secara default** — ini agar development lancar.
  **Untuk produksi, matikan** (lihat 4.B) agar pembatasan benar-benar ketat.
- **`login.php` sekarang memakai `ob_start()`** untuk memperbaiki redirect
  yang sebelumnya gagal saat `output_buffering = 0`. Ini tidak mengubah
  perilaku selain membuat `header('location: ...')` di halaman login bekerja.

---

## 7. Tidak Ada Perubahan Data Lama

Kolom `session_device_id` bernilai `NULL` untuk semua user lama — artinya
tidak ada yang terkunci sampai mereka login. Setelah login, token terisi.
