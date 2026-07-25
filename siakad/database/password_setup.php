<?php
// Jalankan sekali melalui browser setelah mengimpor siakad.sql, lalu hapus file ini.
require_once __DIR__ . '/../config/database.php';
$pdo = db();
$pdo->prepare("UPDATE users SET password=? WHERE username='admin'")->execute([password_hash('admin123', PASSWORD_DEFAULT)]);
$users = [['dosen1','dosen123','dosen',1],['mhs1','mahasiswa123','mahasiswa',1]];
foreach($users as [$username,$password,$role,$profile]) { $pdo->prepare('INSERT IGNORE INTO users(username,password,role) VALUES(?,?,?)')->execute([$username,password_hash($password,PASSWORD_DEFAULT),$role]); $id=$pdo->lastInsertId() ?: $pdo->query("SELECT id FROM users WHERE username=".$pdo->quote($username))->fetchColumn(); if($role==='dosen') $pdo->prepare('UPDATE dosen SET user_id=? WHERE id=1')->execute([$id]); else $pdo->prepare('UPDATE mahasiswa SET user_id=? WHERE id=1')->execute([$id]); }
echo 'Akun demo berhasil disiapkan. Hapus database/password_setup.php demi keamanan.';
