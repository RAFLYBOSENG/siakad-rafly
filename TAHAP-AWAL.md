Cara menjalankannya di XAMPP:
Pindahkan folder proyek ke:
C:\xampp\htdocs\siakad

Jalankan Apache dan MySQL dari XAMPP Control Panel.

Buka phpMyAdmin:
http://localhost/phpmyadmin

Buat/import database:
Pilih menu Import
Pilih file [siakad.sql](database\\siakad.sql)
Klik Import

Pastikan konfigurasi di [database.php](config\\database.php) sesuai. Default XAMPP biasanya sudah benar:
const DB_HOST = '127.0.0.1';
const DB_NAME = 'siakad';
const DB_USER = 'root';
const DB_PASS = '';

Buka sekali URL berikut untuk membuat password akun demo:
http://localhost/siakad/database/password_setup.php

Ubah APP_URL di [functions.php](includes\\functions.php) menjadi:
const APP_URL = '/siakad/';

Buka aplikasi:
http://localhost/siakad/

Akun demo:
Admin: admin / admin123
Dosen: dosen1 / dosen123
Mahasiswa: mhs1 / mahasiswa123
Setelah akun dibuat, hapus file database/password_setup.php agar tidak dapat dipakai ulang.