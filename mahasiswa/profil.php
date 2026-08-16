<?php
$page_title = 'Profil Saya';
require_once __DIR__ . '/../includes/functions.php';
require_login(['mahasiswa']);

$pdo = db();
$user = user();
$q = $pdo->prepare('SELECT * FROM mahasiswa WHERE id = ? OR user_id = ? LIMIT 1');
$q->execute([student_id(), $user['id']]);
$m = $q->fetch() ?: null;

require_once __DIR__ . '/../includes/header.php';
?>
<h3 class="mb-4">Profil Mahasiswa</h3>

<div class="card p-4 col-lg-7">
	<?php if ($m): ?>
		<div class="row g-3">
			<div class="col-sm-4 text-secondary">NPM</div>
			<div class="col-sm-8 fw-semibold"><?= e($m['npm']) ?></div>

			<div class="col-sm-4 text-secondary">Nama</div>
			<div class="col-sm-8 fw-semibold"><?= e($m['nama']) ?></div>

			<div class="col-sm-4 text-secondary">Jenis Kelamin</div>
			<div class="col-sm-8"><?= e($m['jk'] === 'L' ? 'Laki-laki' : 'Perempuan') ?></div>

			<div class="col-sm-4 text-secondary">Program Studi</div>
			<div class="col-sm-8"><?= e($m['prodi']) ?></div>

			<div class="col-sm-4 text-secondary">Angkatan</div>
			<div class="col-sm-8"><?= e($m['angkatan']) ?></div>

			<div class="col-sm-4 text-secondary">Alamat</div>
			<div class="col-sm-8"><?= e($m['alamat']) ?></div>
		</div>
	<?php else: ?>
		<div class="alert alert-warning mb-0">
			Data profil mahasiswa belum terhubung ke akun ini. Silakan hubungkan data mahasiswa dengan akun pengguna.
		</div>
	<?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
