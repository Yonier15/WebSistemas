<?php
require 'inc/lib.php'; requiere_login();
$p = db()->query('SELECT COUNT(*) t, SUM(disponible) d FROM productos')->fetch();
$us = (int)db()->query('SELECT COUNT(*) FROM usuarios WHERE activo=1')->fetchColumn();
$titulo='Inicio'; require 'inc/header.php'; ?>
<h1>Hola, <?= e(usuario()['nombre']) ?></h1>
<div class="cifras">
<div class="tarjeta"><b><?= (int)$p['t'] ?></b><span>productos en la carta</span></div>
<div class="tarjeta"><b><?= (int)$p['d'] ?></b><span>disponibles hoy</span></div>
<div class="tarjeta"><b><?= $us ?></b><span>usuarios activos</span></div></div>
<p><a class="btn" href="productos.php">Administrar productos</a></p>
<?php require 'inc/footer.php';
