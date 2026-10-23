-- ============================================================================
--  [XAMPP-TWEAK] MIGRASI: SINGLE-DEVICE LOCK (1 AKUN = 1 PERANGKAT)
-- ============================================================================
--  Jalankan SQL ini SEKALI saja pada database 'labsisfor' untuk menambahkan
--  kolom penanda perangkat ke tabel 'user'.
--
--  Cara menjalankan (pilih salah satu):
--   1) phpMyAdmin  -> pilih database 'labsisfor' -> tab "SQL" -> tempel -> Go.
--   2) Command line:
--      C:\Xamppp\mysql\bin\mysql.exe -u root labsisfor < database\migration_device_lock.sql
--   3) Atau cukup buka sekali halaman admin.php di browser; kode di file
--      admin.php sudah otomatis membuat kolom ini jika belum ada (auto-migrate).
-- ============================================================================

ALTER TABLE `user`
    ADD COLUMN `session_device_id` VARCHAR(255) NULL DEFAULT NULL
    COMMENT 'Token perangkat terakhir yang login (single-device lock)';
