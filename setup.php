<?php // Crea el primer administrador. Se bloquea solo cuando ya existe un usuario.
require 'inc/lib.php';
$hay = (int)db()->query('SELECT COUNT(*) FROM usuarios')->fetchColumn() > 0;
$err = '';
if (!$hay && $_SERVER['REQUEST_METHOD']==='POST' && csrf_ok()) {
    $n = trim($_POST['nombre'] ?? ''); $m = strtolower(trim($_POST['email'] ?? '')); $c = $_POST['clave'] ?? '';
    if (mb_strlen($n)<3 || !filter_var($m, FILTER_VALIDATE_EMAIL) || strlen($c)<8) $err = 'Revisa los datos: nombre, correo válido y clave de mínimo 8 caracteres.';
    else { db()->prepare("INSERT INTO usuarios (nombre,email,clave_hash,rol) VALUES (?,?,?, 'admin')")->execute([$n,$m,password_hash($c, PASSWORD_DEFAULT)]); flash('Administrador creado. Ya puedes iniciar sesión.'); ir('login.php'); }
}
$titulo='Instalación'; $clase='acceso'; require 'inc/header.php'; ?>
<section class="tarjeta acceso-caja"><h1>Primer administrador</h1>
<?php if ($hay): ?><p>La instalación ya se hizo. <a href="login.php">Ir al ingreso</a>.</p>
<?php else: ?><?php if($err): ?><p class="aviso error"><?= e($err) ?></p><?php endif; ?>
<form method="post" novalidate><?= csrf_field() ?>
<label>Nombre<input name="nombre" required minlength="3" maxlength="80"></label>
<label>Correo<input type="email" name="email" required maxlength="120"></label>
<label>Clave (mínimo 8)<input type="password" name="clave" required minlength="8"></label>
<button class="btn">Crear administrador</button></form><?php endif; ?></section>
<?php require 'inc/footer.php';
