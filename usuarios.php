<?php
require_once __DIR__.'/app/seguridad/guardia.php';
require_once __DIR__.'/app/config/conexion.php';
exigirRol('administrador');
$pdo=Conexion::obtener();
$us=$pdo->query("SELECT id,nombre,correo,rol,activo FROM usuarios ORDER BY id")->fetchAll();
require __DIR__.'/app/vistas/parciales/cabecera.php';require __DIR__.'/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido"><h1>Usuarios (solo administrador)</h1>
<table><thead><tr><th>ID</th><th>Nombre</th><th>Correo</th><th>Rol</th></tr></thead><tbody>
<?php foreach($us as $u):?><tr><td><?=$u['id']?></td><td><?=htmlspecialchars($u['nombre'],ENT_QUOTES,'UTF-8')?></td><td><?=htmlspecialchars($u['correo'],ENT_QUOTES,'UTF-8')?></td><td><?=htmlspecialchars($u['rol'],ENT_QUOTES,'UTF-8')?></td></tr><?php endforeach;?>
</tbody></table>
<p><small>Prueba XSS: si crea un usuario &lt;script&gt;alert(1)&lt;/script&gt; se ve como texto gracias a htmlspecialchars.</small></p></main>
<?php require __DIR__.'/app/vistas/parciales/pie.php'; ?>
