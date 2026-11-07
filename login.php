<?php
require 'inc/lib.php';
if (usuario()) ir('panel.php');
$err = isset($_GET['exp']) ? 'Tu sesión expiró por inactividad. Ingresa de nuevo.' : '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (($_SESSION['bloqueo'] ?? 0) > time()) $err = 'Demasiados intentos. Espera 5 minutos.';
    elseif (!csrf_ok()) $err = 'Formulario inválido. Recarga la página.';
    else {
        $m = strtolower(trim($_POST['email'] ?? '')); $c = $_POST['clave'] ?? '';
        $s = db()->prepare('SELECT * FROM usuarios WHERE email=? AND activo=1'); $s->execute([$m]); $u = $s->fetch();
        if ($u && password_verify($c, $u['clave_hash'])) {
            session_regenerate_id(true); // evita fijación de sesión
            $_SESSION['u'] = ['id'=>(int)$u['id'],'nombre'=>$u['nombre'],'rol'=>$u['rol']]; $_SESSION['t'] = time();
            ir('panel.php');
        }
        $_SESSION['fallos'] = ($_SESSION['fallos'] ?? 0) + 1;
        if ($_SESSION['fallos'] >= 5) { $_SESSION['bloqueo'] = time()+300; $_SESSION['fallos'] = 0; }
        $err = 'Correo o clave incorrectos.';
    }
}
$titulo='Ingresar'; $clase='acceso'; require 'inc/header.php'; ?>
<section class="tarjeta acceso-caja"><h1><?= e(NEGOCIO) ?></h1><p class="suave">Panel de administración</p>
<?php if($err): ?><p class="aviso error"><?= e($err) ?></p><?php endif; ?>
<form method="post" novalidate><?= csrf_field() ?>
<label>Correo<input type="email" name="email" required maxlength="120" autocomplete="username"></label>
<label>Clave<input type="password" name="clave" required autocomplete="current-password"></label>
<button class="btn">Ingresar</button></form>
<p class="suave"><a href="index.php">Volver a la carta</a></p></section>
<?php require 'inc/footer.php';
