<?php
declare(strict_types=1);
// ===== CONFIGURACIÓN (cambiar al subir al hosting) =====
const NEGOCIO  = 'Arepas y Chuzos de la Esquina';
const WHATSAPP = '573000000000';
const DB_HOST = 'localhost'; const DB_NAME = 'arepas'; const DB_USER = 'root'; const DB_PASS = '';

session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS'])]);
session_start();

function db(): PDO {
    static $pdo = null;
    if (!$pdo) {
        $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES=>false]);
    }
    return $pdo;
}
function e(?string $s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function precio(int $n): string { return '$'.number_format($n, 0, ',', '.'); }
function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function csrf_field(): string { return '<input type="hidden" name="csrf" value="'.csrf().'">'; }
function csrf_ok(): bool { return hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? ''); }
function flash(?string $msg = null, string $tipo = 'ok') {
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $tipo]; return; }
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}
function ir(string $url): never { header('Location: '.$url); exit; }
function usuario(): ?array { return $_SESSION['u'] ?? null; }
function requiere_login(): void {
    if (!usuario()) ir('login.php');
    if (time() - ($_SESSION['t'] ?? 0) > 1800) { cerrar_sesion(); ir('login.php?exp=1'); } // 30 min de inactividad
    $_SESSION['t'] = time();
}
function requiere_admin(): void { requiere_login(); if (usuario()['rol'] !== 'admin') { http_response_code(403); exit('Acceso restringido a administradores.'); } }
function cerrar_sesion(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) { $p = session_get_cookie_params();
        setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']); }
    session_destroy();
}
