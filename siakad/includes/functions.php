<?php
require_once __DIR__ . '/../config/database.php';

const APP_URL = '/'; // Ubah menjadi '/nama-folder/' jika aplikasi tidak berada di web root.
const SESSION_TIMEOUT = 1800;

if (session_status() === PHP_SESSION_NONE) session_start();

function e(?string $value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function url(string $path = ''): string { return APP_URL . ltrim($path, '/'); }
function redirect(string $path): never { header('Location: ' . url($path)); exit; }
function flash(string $key, ?string $message = null): ?string {
    if ($message !== null) { $_SESSION['flash'][$key] = $message; return null; }
    $value = $_SESSION['flash'][$key] ?? null; unset($_SESSION['flash'][$key]); return $value;
}
function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . csrf() . '">'; }
function verify_csrf(): void { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Permintaan tidak valid. Silakan ulangi.'); } }
function user(): ?array { return $_SESSION['user'] ?? null; }
function logged_in(): bool { return user() !== null; }
function require_login(array $roles = []): void {
    if (!logged_in()) redirect('auth/login.php');
    if (time() - ($_SESSION['last_activity'] ?? 0) > SESSION_TIMEOUT) { session_unset(); session_destroy(); redirect('auth/login.php?timeout=1'); }
    $_SESSION['last_activity'] = time();
    if ($roles && !in_array(user()['role'], $roles, true)) { http_response_code(403); exit('Akses ditolak.'); }
}
function dashboard_path(): string { return match(user()['role']) { 'admin' => 'admin/dashboard.php', 'dosen' => 'dosen/dashboard.php', default => 'mahasiswa/dashboard.php' }; }
function number(float|int|null $n): string { return number_format((float)$n, 2, ',', '.'); }
function grade(float $score): array {
    return match(true) { $score >= 85 => ['A',4], $score >= 80 => ['AB',3.5], $score >= 75 => ['B',3], $score >= 70 => ['BC',2.5], $score >= 65 => ['C',2], $score >= 55 => ['D',1], default => ['E',0] };
}
function student_id(): ?int { return user()['mahasiswa_id'] ?? null; }
function lecturer_id(): ?int { return user()['dosen_id'] ?? null; }
