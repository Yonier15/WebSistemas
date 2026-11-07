<?php
require 'inc/lib.php';
$rows = db()->query('SELECT p.*, c.nombre cat FROM productos p JOIN categorias c ON c.id=p.categoria_id ORDER BY c.orden, p.nombre')->fetchAll();
$g = []; foreach ($rows as $r) $g[$r['cat']][] = $r;
$titulo='Carta'; $clase='publica'; require 'inc/header.php'; ?>
<section class="portada"><h1>Arepas, perros y chuzos</h1>
<p>Todo se hace al momento, en la parrilla y en la plancha. Mira la carta y pide por WhatsApp.</p>
<a class="btn" href="https://wa.me/<?= e(WHATSAPP) ?>?text=<?= rawurlencode('Hola, quiero hacer un pedido') ?>">Pedir por WhatsApp</a></section>
<section class="pizarra" aria-label="Carta"><?php foreach ($g as $cat => $ps): ?>
<h2><?= e($cat) ?></h2><ul><?php foreach ($ps as $p): ?>
<li class="<?= $p['disponible'] ? '' : 'agotado' ?>"><div class="linea"><span class="nom"><?= e($p['nombre']) ?></span><i></i><span class="pre"><?= $p['disponible'] ? precio((int)$p['precio']) : 'Agotado' ?></span></div>
<?php if ($p['descripcion']): ?><small><?= e($p['descripcion']) ?></small><?php endif; ?></li><?php endforeach; ?></ul>
<?php endforeach; ?></section>
<footer class="pie"><?= e(NEGOCIO) ?> <a href="login.php">Acceso del personal</a></footer>
<?php require 'inc/footer.php';