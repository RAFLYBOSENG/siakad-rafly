<?php
require_once __DIR__ . '/../includes/functions.php';
require_login(['admin']);

$type = $_GET['type'] ?? 'mahasiswa';
$configs = [
	'mahasiswa' => ['title' => 'Mahasiswa', 'table' => 'mahasiswa', 'pk' => 'id', 'fields' => ['npm' => 'NPM', 'nama' => 'Nama', 'jk' => 'Jenis Kelamin (L/P)', 'alamat' => 'Alamat', 'prodi' => 'Program Studi', 'angkatan' => 'Angkatan'], 'list' => 'SELECT * FROM mahasiswa ORDER BY nama'],
	'dosen' => ['title' => 'Dosen', 'table' => 'dosen', 'pk' => 'id', 'fields' => ['nidn' => 'NIDN', 'nama' => 'Nama'], 'list' => 'SELECT * FROM dosen ORDER BY nama'],
	'mata_kuliah' => ['title' => 'Mata Kuliah', 'table' => 'mata_kuliah', 'pk' => 'id', 'fields' => ['kode' => 'Kode', 'nama' => 'Nama', 'sks' => 'SKS'], 'list' => 'SELECT * FROM mata_kuliah ORDER BY kode'],
	'tahun_akademik' => ['title' => 'Tahun Akademik', 'table' => 'tahun_akademik', 'pk' => 'id', 'fields' => ['tahun' => 'Tahun (mis. 2025/2026)', 'semester' => 'Semester (Ganjil/Genap)', 'status' => 'Status (Aktif/Nonaktif)'], 'list' => 'SELECT * FROM tahun_akademik ORDER BY tahun DESC'],
	'kelas' => ['title' => 'Kelas', 'table' => 'kelas', 'pk' => 'id', 'fields' => ['id_mk' => 'ID Mata Kuliah', 'id_dosen' => 'ID Dosen', 'id_tahun_akademik' => 'ID Tahun Akademik', 'nama_kelas' => 'Nama Kelas', 'kapasitas' => 'Kapasitas'], 'list' => 'SELECT k.*,m.kode,m.nama mk,d.nama dosen,t.tahun,t.semester FROM kelas k JOIN mata_kuliah m ON m.id=k.id_mk LEFT JOIN dosen d ON d.id=k.id_dosen JOIN tahun_akademik t ON t.id=k.id_tahun_akademik ORDER BY k.id DESC'],
	'users' => ['title' => 'Pengguna', 'table' => 'users', 'pk' => 'id', 'fields' => ['username' => 'Username', 'password' => 'Password (kosongkan saat edit)', 'role' => 'Peran (admin/dosen/mahasiswa)', 'person_id' => 'Pilih nama', 'is_active' => 'Aktif (1/0)'], 'list' => 'SELECT u.id,u.username,u.role,u.is_active,u.created_at,COALESCE(m.nama,d.nama,\'Admin\') nama_pengguna FROM users u LEFT JOIN mahasiswa m ON m.user_id=u.id LEFT JOIN dosen d ON d.user_id=u.id ORDER BY u.id DESC'],
];

if (!isset($configs[$type])) exit('Data master tidak ditemukan.');

$c = $configs[$type];
$pdo = db();
$id = (int)($_GET['id'] ?? 0);

if (isset($_GET['delete']) && $id) {
	if (!hash_equals(csrf(), $_GET['csrf'] ?? '')) exit('Permintaan tidak valid.');
	if ($type === 'users') {
		$existing = $pdo->prepare('SELECT role FROM users WHERE id=?');
		$existing->execute([$id]);
		$existingRole = $existing->fetchColumn();
		if ($existingRole === 'mahasiswa') {
			$pdo->prepare('UPDATE mahasiswa SET user_id=NULL WHERE user_id=?')->execute([$id]);
		} elseif ($existingRole === 'dosen') {
			$pdo->prepare('UPDATE dosen SET user_id=NULL WHERE user_id=?')->execute([$id]);
		}
	}
	$pdo->prepare("DELETE FROM {$c['table']} WHERE {$c['pk']}=?")->execute([$id]);
	flash('success', 'Data dihapus.');
	redirect("admin/master.php?type=$type");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	verify_csrf();
	$data = [];
	foreach ($c['fields'] as $field => $label) {
		if ($type === 'users' && $field === 'person_id') {
			continue;
		}
		$value = trim($_POST[$field] ?? '');
		if ($field === 'password' && $value === '') continue;
		$data[$field] = $field === 'password' ? password_hash($value, PASSWORD_DEFAULT) : $value;
	}

	try {
		if ($id) {
			$set = implode(',', array_map(fn($field) => "$field=?", array_keys($data)));
			$pdo->prepare("UPDATE {$c['table']} SET $set WHERE {$c['pk']}=?")->execute([...array_values($data), $id]);
		} else {
			$fields = array_keys($data);
			$pdo->prepare("INSERT INTO {$c['table']} (" . implode(',', $fields) . ") VALUES (" . implode(',', array_fill(0, count($fields), '?')) . ")")->execute(array_values($data));
			if ($type === 'users') {
				$id = (int) $pdo->lastInsertId();
			}
		}

		if ($type === 'users') {
			$role = $data['role'] ?? ($_POST['role'] ?? '');
			$personId = (int) ($_POST['person_id'] ?? 0);
			$personType = $role;

			$pdo->prepare('UPDATE mahasiswa SET user_id=NULL WHERE user_id=?')->execute([$id]);
			$pdo->prepare('UPDATE dosen SET user_id=NULL WHERE user_id=?')->execute([$id]);

			if ($role === 'mahasiswa' && $personType === 'mahasiswa' && $personId) {
				$pdo->prepare('UPDATE mahasiswa SET user_id=? WHERE id=?')->execute([$id, $personId]);
			} elseif ($role === 'dosen' && $personType === 'dosen' && $personId) {
				$pdo->prepare('UPDATE dosen SET user_id=? WHERE id=?')->execute([$id, $personId]);
			}
		}

		flash('success', 'Data berhasil disimpan.');
		redirect("admin/master.php?type=$type");
	} catch (PDOException $e) {
		flash('error', 'Gagal menyimpan: ' . $e->getMessage());
	}
}

$edit = $id ? $pdo->prepare("SELECT * FROM {$c['table']} WHERE {$c['pk']}=?") : null;
if ($edit) {
	$edit->execute([$id]);
	$edit = $edit->fetch() ?: [];
}

$linkedPersonId = null;
if ($type === 'users' && !empty($edit)) {
	if (($edit['role'] ?? '') === 'mahasiswa') {
		$q = $pdo->prepare('SELECT id FROM mahasiswa WHERE user_id=? LIMIT 1');
		$q->execute([$id]);
		$linkedPersonId = $q->fetchColumn();
	} elseif (($edit['role'] ?? '') === 'dosen') {
		$q = $pdo->prepare('SELECT id FROM dosen WHERE user_id=? LIMIT 1');
		$q->execute([$id]);
		$linkedPersonId = $q->fetchColumn();
	}
}

$rows = $pdo->query($c['list'])->fetchAll();
$page_title = 'CRUD ' . $c['title'];
require_once __DIR__ . '/../includes/header.php';
?>
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
	<div>
		<h3 class="mb-1"><?= $c['title'] ?></h3>
		<p class="text-secondary mb-0">Kelola data <?= $c['title'] ?>.</p>
	</div>
	<a class="btn btn-primary" href="?type=<?= $type ?>&id=0"><i class="bi bi-plus-lg me-1"></i> Tambah</a>
</div>

<?php if ($id || isset($_GET['id'])): ?>
<div class="card p-4 mb-4">
	<h5 class="mb-3"><?= $id ? 'Edit' : 'Tambah' ?> <?= $c['title'] ?></h5>
	<form method="post" class="row g-3">
		<?= csrf_field() ?>
		<?php foreach ($c['fields'] as $field => $label): ?>
			<div class="col-12 <?= $field === 'alamat' ? '' : 'col-md-6' ?>">
				<label class="form-label"><?= e($label) ?></label>
				<?php if ($field === 'alamat'): ?>
					<textarea class="form-control" name="<?= $field ?>" rows="4"><?= e($edit[$field] ?? '') ?></textarea>
				<?php elseif ($type === 'users' && $field === 'role'): ?>
					<select class="form-select" name="role" id="roleSelect">
						<option value="admin" <?= ($edit['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
						<option value="dosen" <?= ($edit['role'] ?? '') === 'dosen' ? 'selected' : '' ?>>Dosen</option>
						<option value="mahasiswa" <?= ($edit['role'] ?? '') === 'mahasiswa' ? 'selected' : '' ?>>Mahasiswa</option>
					</select>
				<?php elseif ($type === 'users' && $field === 'person_id'): ?>
					<?php
					$studentRows = $pdo->query('SELECT id,nama FROM mahasiswa ORDER BY nama')->fetchAll();
					$lecturerRows = $pdo->query('SELECT id,nama FROM dosen ORDER BY nama')->fetchAll();
					$currentRole = $edit['role'] ?? 'admin';
					?>
					<select class="form-select" name="person_id" id="personIdSelect">
						<option value="">Pilih nama</option>
						<optgroup label="Mahasiswa">
							<?php foreach ($studentRows as $student): ?>
								<option value="<?= $student['id'] ?>" data-type="mahasiswa" <?= ($currentRole === 'mahasiswa' && (int)$linkedPersonId === (int)$student['id']) ? 'selected' : '' ?>><?= e($student['nama']) ?></option>
							<?php endforeach; ?>
						</optgroup>
						<optgroup label="Dosen">
							<?php foreach ($lecturerRows as $lecturer): ?>
								<option value="<?= $lecturer['id'] ?>" data-type="dosen" <?= ($currentRole === 'dosen' && (int)$linkedPersonId === (int)$lecturer['id']) ? 'selected' : '' ?>><?= e($lecturer['nama']) ?></option>
							<?php endforeach; ?>
						</optgroup>
					</select>
					<div class="form-text">Admin tidak perlu memilih nama.</div>
				<?php elseif ($type === 'users' && $field === 'is_active'): ?>
					<select class="form-select" name="is_active">
						<option value="1" <?= (string)($edit['is_active'] ?? '1') === '1' ? 'selected' : '' ?>>1</option>
						<option value="0" <?= (string)($edit['is_active'] ?? '1') === '0' ? 'selected' : '' ?>>0</option>
					</select>
				<?php else: ?>
					<input class="form-control" name="<?= $field ?>" value="<?= e($field === 'password' ? '' : ($edit[$field] ?? '')) ?>" <?= $field === 'password' ? 'type="password"' : 'required' ?>>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
		<div class="col-12 d-flex gap-2">
			<button class="btn btn-primary">Simpan</button>
			<a class="btn btn-light" href="?type=<?= $type ?>">Batal</a>
		</div>
	</form>
</div>
<?php endif; ?>

<div class="card p-4">
	<div class="table-responsive">
		<table class="table table-hover align-middle datatable mb-0">
			<thead>
				<tr>
					<?php if ($type === 'users'): ?>
						<th>Nama</th>
					<?php endif; ?>
					<?php foreach ($c['fields'] as $field => $label): ?>
						<?php if ($type === 'users' && $field === 'person_id') continue; ?>
						<th><?= e(strtok($label, '(')) ?></th>
					<?php endforeach; ?>
					<th>Aksi</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($rows as $row): ?>
				<tr>
					<?php if ($type === 'users'): ?>
						<td><?= e($row['nama_pengguna'] ?? '-') ?></td>
					<?php endif; ?>
					<?php foreach ($c['fields'] as $field => $label): ?>
						<?php if ($type === 'users' && $field === 'person_id') continue; ?>
						<td><?= e($field === 'password' ? '••••••' : ($row[$field] ?? ($field === 'id_mk' ? ($row['kode'] . ' — ' . $row['mk']) : ''))) ?></td>
					<?php endforeach; ?>
					<td class="text-nowrap">
						<a class="btn btn-sm btn-outline-primary" href="?type=<?= $type ?>&id=<?= $row['id'] ?>">Edit</a>
						<a onclick="return confirm('Hapus data ini?')" class="btn btn-sm btn-outline-danger" href="?type=<?= $type ?>&id=<?= $row['id'] ?>&delete=1&csrf=<?= csrf() ?>">Hapus</a>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<?php if ($type === 'users'): ?>
<script>
(function () {
	const roleSelect = document.getElementById('roleSelect');
	const personIdSelect = document.getElementById('personIdSelect');
	if (!roleSelect || !personIdSelect) return;
	const syncOptions = () => {
		const role = roleSelect.value;
		[...personIdSelect.options].forEach(option => {
			if (!option.dataset.type) return;
			option.hidden = role === 'admin' || option.dataset.type === role ? false : true;
		});
		if (role === 'admin') {
			personIdSelect.value = '';
		}
	};
	roleSelect.addEventListener('change', syncOptions);
	syncOptions();
})();
</script>
<?php endif; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
