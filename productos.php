<?php
require_once __DIR__.'/app/seguridad/guardia.php';
require_once __DIR__.'/app/seguridad/csrf.php';
require_once __DIR__.'/app/config/conexion.php';
require_once __DIR__.'/app/modelos/ProductoModelo.php';
/* Consultor puede VER, solo admin/vendedor pueden GUARDAR/ELIMINAR (matriz Fig.11) */
$pdo=Conexion::obtener();$modelo=new ProductoModelo($pdo);
if($_SERVER['REQUEST_METHOD']==='POST'){
exigirRol('administrador','vendedor');
if(!validarCsrf($_POST['csrf']??null)){http_response_code(419);exit;}
$acc=$_POST['acc']??'guardar';
if($acc==='eliminar'){
$modelo->desactivar((int)$_POST['id']);
$_SESSION['aviso']=['tipo'=>'exito','texto'=>'Producto desactivado (borrado lógico).'];
header('Location: productos.php',true,303);exit;
}
$nombre=trim((string)($_POST['nombre']??''));
$precio=filter_input(INPUT_POST,'precio',FILTER_VALIDATE_FLOAT);
$stock=filter_input(INPUT_POST,'stock',FILTER_VALIDATE_INT);
$catId=filter_input(INPUT_POST,'categoria_id',FILTER_VALIDATE_INT);
$err=[];
if(mb_strlen($nombre)<3)$err[]='El nombre debe tener al menos 3 caracteres.';
if($precio===false||$precio<=0)$err[]='El precio debe ser mayor que cero.';
if($stock===false||$stock<0)$err[]='El stock no puede ser negativo.';
if(!$catId)$err[]='Seleccione una categoría.';
if(!$err){
$id=(int)($_POST['id']??0);
$d=['nombre'=>$nombre,'precio'=>$precio,'stock'=>$stock,'categoria_id'=>$catId];
$id>0?$modelo->actualizar($id,$d):$modelo->crear($d);
$_SESSION['aviso']=['tipo'=>'exito','texto'=>'Producto guardado correctamente.'];
header('Location: productos.php',true,303);exit;
}
$_SESSION['aviso']=['tipo'=>'error','texto'=>implode(' ',$err)];
header('Location: productos.php',true,303);exit;
}
$bus=$_GET['q']??'';$pag=max(1,(int)($_GET['pag']??1));
$filas=$modelo->listar($bus,$pag,10);
$cats=$pdo->query("SELECT id,nombre FROM categorias WHERE activo=1 ORDER BY nombre")->fetchAll();
$aviso=$_SESSION['aviso']??null;unset($_SESSION['aviso']);
require __DIR__.'/app/vistas/parciales/cabecera.php';
require __DIR__.'/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido"><h1>Productos</h1>
<?php if($aviso):?><p class="<?=$aviso['tipo']==='exito'?'alerta-exito':'alerta-error'?>" role="alert"><?=htmlspecialchars($aviso['texto'],ENT_QUOTES,'UTF-8')?></p><?php endif;?>
<form method="get" class="no-imprimir"><label for="q">Buscar</label><input id="q" name="q" value="<?=htmlspecialchars($bus,ENT_QUOTES,'UTF-8')?>"><button class="boton">Buscar</button></form>
<table id="tabla-productos"><caption>Listado (10 por página)</caption><thead><tr><th>Producto</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Acciones</th></tr></thead>
<tbody><?php foreach($filas as $f):?><tr><td><?=htmlspecialchars($f['nombre'],ENT_QUOTES,'UTF-8')?></td><td><?=htmlspecialchars($f['categoria'],ENT_QUOTES,'UTF-8')?></td><td>$ <?=number_format((float)$f['precio'],0,',','.')?></td><td><?=$f['stock']?></td>
<td><?php if(puede('administrador','vendedor')):?><button class="boton-mini" data-accion="editar" data-id="<?=$f['id']?>" data-nombre="<?=htmlspecialchars($f['nombre'],ENT_QUOTES,'UTF-8')?>">Editar</button>
<form method="post" style="display:inline" onsubmit="return confirm('¿Desactivar?')"><input type="hidden" name="csrf" value="<?=htmlspecialchars(tokenCsrf(),ENT_QUOTES,'UTF-8')?>"><input type="hidden" name="acc" value="eliminar"><input type="hidden" name="id" value="<?=$f['id']?>"><button class="boton-mini boton-peligro">Eliminar</button></form><?php else:?>Solo lectura<?php endif;?></td></tr><?php endforeach;?></tbody></table>
<p><a href="?pag=<?=$pag+1?>&q=<?=urlencode($bus)?>">Siguiente →</a><?php if($pag>1):?> · <a href="?pag=<?=$pag-1?>&q=<?=urlencode($bus)?>">← Anterior</a><?php endif;?></p>
<?php if(puede('administrador','vendedor')):?>
<h2>Crear / editar</h2>
<form method="post" id="form-producto"><input type="hidden" name="csrf" value="<?=htmlspecialchars(tokenCsrf(),ENT_QUOTES,'UTF-8')?>"><input type="hidden" name="id" id="f-id">
<label for="f-nombre">Nombre (mín. 3)</label><input id="f-nombre" name="nombre" required minlength="3">
<label for="f-categoria">Categoría</label><select id="f-categoria" name="categoria_id" required><option value="">— Seleccione —</option><?php foreach($cats as $c):?><option value="<?=$c['id']?>"><?=htmlspecialchars($c['nombre'],ENT_QUOTES,'UTF-8')?></option><?php endforeach;?></select>
<label for="f-precio">Precio (&gt;0)</label><input id="f-precio" name="precio" type="number" step="0.01" required>
<label for="f-stock">Stock (≥0)</label><input id="f-stock" name="stock" type="number" required>
<p><button class="boton">Guardar (POST/Redirect/GET)</button></p></form>
<?php endif;?>
</main>
<script>
document.querySelector('#tabla-productos tbody').addEventListener('click',e=>{
const b=e.target.closest('button[data-accion]');if(!b||b.dataset.accion!=='editar')return;
document.querySelector('#f-id').value=b.dataset.id;
document.querySelector('#f-nombre').value=b.dataset.nombre;
});
</script>
<?php require __DIR__.'/app/vistas/parciales/pie.php'; ?>
