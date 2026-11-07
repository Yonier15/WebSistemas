<?php $u = usuario(); $f = flash(); ?>
<!DOCTYPE html><html lang="es"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($titulo ?? 'Panel') ?> · <?= e(NEGOCIO) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,800&family=Nunito+Sans:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css"></head><body class="<?= e($clase ?? 'admin') ?>">
<?php if ($u): ?>
<header class="barra"><a class="marca" href="panel.php"><?= e(NEGOCIO) ?></a>
<nav><a href="panel.php">Inicio</a><a href="productos.php">Productos</a>
<?php if ($u['rol']==='admin'): ?><a href="usuarios.php">Usuarios</a><?php endif; ?>
<a href="index.php" target="_blank">Ver carta</a></nav>
<form method="post" action="logout.php" class="salir"><?= csrf_field() ?><span><?= e($u['nombre']) ?></span><button class="btn chico">Cerrar sesión</button></form></header>
<?php endif; ?><main class="cont">
<?php if ($f): ?><p class="aviso <?= e($f[1]) ?>" role="alert"><?= e($f[0]) ?></p><?php endif; ?>
