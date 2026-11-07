<?php
require 'inc/lib.php'; requiere_login();
$cats = db()->query('SELECT * FROM categorias ORDER BY orden')->fetchAll();
$ids = array_column($cats, 'id'); $err = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!csrf_ok()) { http_response_code(400); exit('Solicitud inválida.'); }
    $id = (int)($_POST['id'] ?? 0);
    if (($_POST['accion'] ?? '') === 'eliminar') {
        db()->prepare('DELETE FROM productos WHERE id=?')->execute([$id]); flash('Producto eliminado.'); ir('productos.php');
    }
    $n = trim($_POST['nombre'] ?? ''); $d = trim($_POST['descripcion'] ?? ''); $c = (int)($_POST['categoria_id'] ?? 0);
    $pr = filter_var($_POST['precio'] ?? '', FILTER_VALIDATE_INT, ['options'=>['min_range'=>100,'max_range'=>1000000]]);
    $disp = isset($_POST['disponible']) ? 1 : 0;
    if (mb_strlen($n) < 3 || mb_strlen($n) > 80) $err[] = 'El nombre debe tener entre 3 y 80 caracteres.';
    if (mb_strlen($d) > 200) $err[] = 'La descripción no puede pasar de 200 caracteres.';
    if (!in_array($c, $ids, true)) $err[] = 'Elige una categoría válida.';
    if ($pr === false) $err[] = 'El precio debe ser un número entre 100 y 1.000.000.';
    if (!$err) {
        if ($id) { db()->prepare('UPDATE productos SET nombre=?,descripcion=?,categoria_id=?,precio=?,disponible=? WHERE id=?')->execute([$n,$d,$c,$pr,$disp,$id]); flash('Producto actualizado.'); }
        else { db()->prepare('INSERT INTO productos (nombre,descripcion,categoria_id,precio,disponible,creado_por) VALUES (?,?,?,?,?,?)')->execute([$n,$d,$c,$pr,$disp,usuario()['id']]); flash('Producto registrado.'); }
        ir('productos.php');
    }
}
$ed = null;
if (isset($_GET['editar'])) { $s = db()->prepare('SELECT * FROM productos WHERE id=?'); $s->execute([(int)$_GET['editar']]); $ed = $s->fetch() ?: null; }
$f = $_POST ?: ($ed ?: []);
$q = trim($_GET['q'] ?? '');
$s = db()->prepare('SELECT p.*, c.nombre cat FROM productos p JOIN categorias c ON c.id=p.categoria_id WHERE p.nombre LIKE ? ORDER BY c.orden, p.nombre');
$s->execute(['%'.$q.'%']); $lista = $s->fetchAll();
$titulo='Productos'; require 'inc/header.php'; ?>
<h1>Productos</h1>
<div class="dos">
<section class="tarjeta"><h2><?= $ed ? 'Editar producto' : 'Nuevo producto' ?></h2>
<?php foreach ($err as $x): ?><p class="aviso error"><?= e($x) ?></p><?php endforeach; ?>
<form method="post" novalidate><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)($f['id'] ?? 0) ?>">
<label>Nombre<input name="nombre" required minlength="3" maxlength="80" value="<?= e($f['nombre'] ?? '') ?>"></label>
<label>Categoría<select name="categoria_id" required><?php foreach ($cats as $c): ?>
<option value="<?= $c['id'] ?>" <?= (int)($f['categoria_id'] ?? 0)===(int)$c['id'] ? 'selected':'' ?>><?= e($c['nombre']) ?></option><?php endforeach; ?></select></label>
<label>Descripción<textarea name="descripcion" maxlength="200" rows="2"><?= e($f['descripcion'] ?? '') ?></textarea></label>
<label>Precio (COP)<input type="number" name="precio" required min="100" max="1000000" step="100" value="<?= e((string)($f['precio'] ?? '')) ?>"></label>
<label class="check"><input type="checkbox" name="disponible" <?= (!$f || !empty($f['disponible'])) ? 'checked':'' ?>> Disponible hoy</label>
<button class="btn"><?= $ed ? 'Guardar cambios' : 'Registrar producto' ?></button>
<?php if ($ed): ?><a class="btn claro" href="productos.php">Cancelar</a><?php endif; ?></form></section>
<section class="tarjeta"><form method="get" class="buscar"><input type="search" name="q" placeholder="Buscar producto" value="<?= e($q) ?>"><button class="btn chico">Buscar</button></form>
<?php if (!$lista): ?><p class="suave">No hay productos con ese nombre. Registra el primero en el formulario.</p><?php else: ?>
<div class="tabla"><table><tr><th>Producto</th><th>Precio</th><th>Estado</th><th></th></tr>
<?php foreach ($lista as $p): ?><tr><td><b><?= e($p['nombre']) ?></b><br><small><?= e($p['cat']) ?></small></td>
<td><?= precio((int)$p['precio']) ?></td><td><?= $p['disponible'] ? 'Disponible' : 'Agotado' ?></td>
<td class="acc"><a class="btn chico claro" href="?editar=<?= $p['id'] ?>">Editar</a>
<form method="post" onsubmit="return confirm('¿Eliminar este producto?')"><?= csrf_field() ?><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= $p['id'] ?>"><button class="btn chico peligro">Eliminar</button></form></td></tr>
<?php endforeach; ?></table></div><?php endif; ?></section></div>
<?php require 'inc/footer.php';
