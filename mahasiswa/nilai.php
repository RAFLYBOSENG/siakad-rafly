<?php
$page_title = 'Nilai & IPK';
require_once __DIR__ . '/../includes/header.php';
require_login(['mahasiswa']);

$pdo = db();
$studentId = student_id();

?><h3 class="mb-4">Nilai Akademik</h3>
<?php if (!$studentId): ?>
<div class="alert alert-warning">Akun mahasiswa ini belum terhubung ke data profil mahasiswa, sehingga nilai belum bisa ditampilkan.</div>
<?php else: ?>
<?php $q=$pdo->prepare('SELECT t.tahun,t.semester,mk.kode,mk.nama,mk.sks,n.nilai_akhir,n.nilai_huruf,n.bobot FROM krs k JOIN tahun_akademik t ON t.id=k.id_tahun_akademik JOIN mata_kuliah mk ON mk.id=k.id_mk LEFT JOIN nilai n ON n.id_krs=k.id WHERE k.id_mahasiswa=? ORDER BY t.tahun DESC,t.semester,mk.kode');$q->execute([$studentId]);$rows=$q->fetchAll();$q=$pdo->prepare('SELECT COALESCE(SUM(mk.sks*n.bobot)/NULLIF(SUM(mk.sks),0),0) FROM krs k JOIN mata_kuliah mk ON mk.id=k.id_mk JOIN nilai n ON n.id_krs=k.id WHERE k.id_mahasiswa=?');$q->execute([$studentId]);$ipk=$q->fetchColumn(); ?>
<div class="d-flex justify-content-between mb-4"><div></div><div class="card px-3 py-2"><span class="text-secondary">IPK</span> <strong class="fs-5"><?=number($ipk)?></strong></div></div><div class="card p-4"><div class="table-responsive"><table class="table datatable"><thead><tr><th>Periode</th><th>Kode</th><th>Mata Kuliah</th><th>SKS</th><th>Nilai Akhir</th><th>Huruf</th><th>Bobot</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><?=e($r['tahun'].' '.$r['semester'])?></td><td><?=e($r['kode'])?></td><td><?=e($r['nama'])?></td><td><?=$r['sks']?></td><td><?=is_null($r['nilai_akhir'])?'Belum dinilai':number($r['nilai_akhir'])?></td><td><?=e($r['nilai_huruf']??'-')?></td><td><?=is_null($r['bobot'])?'-':number($r['bobot'])?></td></tr><?php endforeach?></tbody></table></div></div>
<?php endif; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
