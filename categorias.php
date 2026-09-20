<?php
require_once __DIR__.'/app/seguridad/guardia.php';
require_once __DIR__.'/app/config/conexion.php';
$pdo=Conexion::obtener();
$cats=$pdo->query("SELECT * FROM categorias ORDER BY nombre")->fetchAll();
require __DIR__.'/app/vistas/parciales/cabecera.php';require __DIR__.'/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido"><h1>Categorías</h1>
<table><thead><tr><th>ID</th><th>Nombre</th></tr></thead><tbody>
<?php foreach($cats as $c):?><tr><td><?=$c['id']?></td><td><?=htmlspecialchars($c['nombre'],ENT_QUOTES,'UTF-8')?></td></tr><?php endforeach;?>
</tbody></table></main>
<?php require __DIR__.'/app/vistas/parciales/pie.php'; ?>
