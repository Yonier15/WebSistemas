<?php
require 'inc/lib.php'; requiere_admin();
$yo = usuario()['id']; $err = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
    if (!csrf_ok()) { http_response_code(400); exit('Solicitud inválida.'); }
    $a = $_POST['accion'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    if ($a === 'crear') {
        $n = trim($_POST['nombre'] ?? ''); $m = strtolower(trim($_POST['email'] ?? '')); $c = $_POST['clave'] ?? ''; $r = $_POST['rol'] ?? '';
        if (mb_strlen($n) < 3 || mb_strlen($n) > 80) $err[] = 'El nombre debe tener entre 3 y 80 caracteres.';
        if (!filter_var($m, FILTER_VALIDATE_EMAIL)) $err[] = 'Escribe un correo válido.';
        if (strlen($c) < 8 || !preg_match('/\d/', $c) || !preg_match('/[A-Za-z]/', $c)) $err[] = 'La clave debe tener mínimo 8 caracteres, con letras y números.';
        if (!in_array($r, ['admin','empleado'], true)) $err[] = 'Rol no válido.';
        if (!$err) { $x = db()->prepare('SELECT 1 FROM usuarios WHERE email=?'); $x->execute([$m]); if ($x->fetch()) $err[] = 'Ya existe un usuario con ese correo.'; }
        if (!$err) { db()->prepare('INSERT INTO usuarios (nombre,email,clave_hash,rol) VALUES (?,?,?,?)')->execute([$n,$m,password_hash($c, PASSWORD_DEFAULT),$r]); flash('Usuario creado.'); ir('usuarios.php'); }
    } elseif ($id === $yo) { flash('No puedes modificar tu propia cuenta desde aquí.', 'error'); ir('usuarios.php');
    } elseif ($a === 'estado') { db()->prepare('UPDATE usuarios SET activo = 1-activo WHERE id=?')->execute([$id]); flash('Estado actualizado.'); ir('usuarios.php');
    } elseif ($a === 'eliminar') { db()->prepare('DELETE FROM usuarios WHERE id=?')->execute([$id]); flash('Usuario eliminado.'); ir('usuarios.php'); }
}
$lista = db()->query('SELECT * FROM usuarios ORDER BY nombre')->fetchAll();
$titulo='Usuarios'; require 'inc/header.php'; ?>
<h1>Usuarios</h1><div class="dos">
<section class="tarjeta"><h2>Nuevo usuario</h2>
<?php foreach ($err as $x): ?><p class="aviso error"><?= e($x) ?></p><?php endforeach; ?>
<form method="post" novalidate><?= csrf_field() ?><input type="hidden" name="accion" value="crear">
<label>Nombre<input name="nombre" required minlength="3" maxlength="80" value="<?= e($_POST['nombre'] ?? '') ?>"></label>
<label>Correo<input type="email" name="email" required maxlength="120" value="<?= e($_POST['email'] ?? '') ?>"></label>
<label>Clave (8+, letras y números)<input type="password" name="clave" required minlength="8" autocomplete="new-password"></label>
<label>Rol<select name="rol"><option value="empleado">Empleado</option><option value="admin">Administrador</option></select></label>
<button class="btn">Crear usuario</button></form></section>
<section class="tarjeta"><div class="tabla"><table><tr><th>Usuario</th><th>Rol</th><th>Estado</th><th></th></tr>
<?php foreach ($lista as $u): ?><tr><td><b><?= e($u['nombre']) ?></b><br><small><?= e($u['email']) ?></small></td><td><?= e($u['rol']) ?></td><td><?= $u['activo'] ? 'Activo' : 'Inactivo' ?></td>
<td class="acc"><?php if ((int)$u['id'] !== $yo): ?>
<form method="post"><?= csrf_field() ?><input type="hidden" name="accion" value="estado"><input type="hidden" name="id" value="<?= $u['id'] ?>"><button class="btn chico claro"><?= $u['activo'] ? 'Desactivar' : 'Activar' ?></button></form>
<form method="post" onsubmit="return confirm('¿Eliminar este usuario?')"><?= csrf_field() ?><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?= $u['id'] ?>"><button class="btn chico peligro">Eliminar</button></form>
<?php else: ?><small>Tú</small><?php endif; ?></td></tr><?php endforeach; ?></table></div></section></div>
<?php require 'inc/footer.php';
