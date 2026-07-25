# SIAKAD — Sistem Informasi Akademik

Aplikasi tugas kuliah berbasis **PHP Native**, MySQL (PDO), Bootstrap 5, DataTables, SweetAlert2, dan Chart.js. Aplikasi menyediakan peran Admin, Dosen, serta Mahasiswa.

## Instalasi

1. Salin folder ini ke document root server PHP (contoh `htdocs/siakad`).
2. Buat database dengan mengimpor [database/siakad.sql](database/siakad.sql) lewat phpMyAdmin atau MySQL CLI.
3. Sesuaikan kredensial MySQL di [config/database.php](config/database.php). Jika folder berada di subfolder, set `APP_URL` di [includes/functions.php](includes/functions.php), misalnya `/siakad/`.
4. Buka `http://localhost/siakad/database/password_setup.php` sekali untuk membuat password hash akun demo, lalu **hapus file tersebut**.
5. Buka aplikasi melalui `index.php`.

## Akun demo

| Peran | Username | Password |
| --- | --- | --- |
| Admin | `admin` | `admin123` |
| Dosen | `dosen1` | `dosen123` |
| Mahasiswa | `mhs1` | `mahasiswa123` |

## Fitur

- Login, session timeout, CSRF, password hash, PDO prepared statements, dan output escaping.
- CRUD mahasiswa, dosen, mata kuliah, kelas, tahun akademik, dan pengguna untuk Admin.
- KRS mahasiswa, total SKS, nilai dengan kalkulasi akhir (30% tugas, 30% UTS, 40% UAS), nilai huruf, IP semester, dan IPK.
- Dashboard per peran, tabel interaktif, dan grafik Admin.

## Struktur ringkas

```
assets/       CSS aplikasi
auth/         login dan logout
config/       koneksi database PDO
database/     SQL dan setup password demo
includes/     layout, helper, keamanan
admin/        dashboard dan master data
dosen/        kelas ajar dan nilai
mahasiswa/    profil, KRS, nilai
```

## Roadmap commit

Gunakan tahapan pada brief tugas: mulai dari struktur dan database, autentikasi, masing-masing CRUD, dashboard, KRS, nilai, keamanan, hingga dokumentasi. Setiap fitur/direktori di atas dapat dijadikan satu commit logis untuk memperoleh lebih dari 25 commit.
