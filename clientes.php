<?php
require_once __DIR__.'/app/seguridad/guardia.php';
require_once __DIR__.'/app/seguridad/csrf.php';
require_once __DIR__.'/app/config/conexion.php';
exigirRol('administrador','vendedor');
$pdo=Conexion::obtener();
if($_SERVER['REQUEST_METHOD']==='POST'){
if(!validarCsrf($_POST['csrf']??null)){http_response_code(419);exit;}
$nombre=trim($_POST['nombre']??'');$doc=trim($_POST['documento']??'');$correo=trim($_POST['correo']??'');
$err=[];
if(mb_strlen($nombre)<3)$err[]='Nombre mínimo 3 caracteres.';
if($doc==='')$err[]='Documento obligatorio.';
if($correo!==''&&!filter_var($correo,FILTER_VALIDATE_EMAIL))$err[]='Correo inválido.';
if(!$err){
try{
if(!empty($_POST['id'])){$pdo->prepare("UPDATE clientes SET nombre=:n,documento=:d,correo=:c WHERE id=:id")->execute([':n'=>$nombre,':d'=>$doc,':c'=>$correo,':id'=>(int)$_POST['id']]);}
else{$pdo->prepare("INSERT INTO clientes(nombre,documento,correo) VALUES(:n,:d,:c)")->execute([':n'=>$nombre,':d'=>$doc,':c'=>$correo]);}
$_SESSION['aviso']=['tipo'=>'exito','texto'=>'Cliente guardado.'];
}catch(Throwable $e){$_SESSION['aviso']=['tipo'=>'error','texto'=>'Documento o correo duplicado.'];}
}else{$_SESSION['aviso']=['tipo'=>'error','texto'=>implode(' ',$err)];}
header('Location: clientes.php',true,303);exit;
}
$filas=$pdo->query("SELECT * FROM clientes WHERE activo=1 ORDER BY nombre LIMIT 50")->fetchAll();
$av=$_SESSION['aviso']??null;unset($_SESSION['aviso']);
require __DIR__.'/app/vistas/parciales/cabecera.php';require __DIR__.'/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido"><h1>Clientes</h1>
<?php if($av):?><p class="<?=$av['tipo']==='exito'?'alerta-exito':'alerta-error'?>"><?=htmlspecialchars($av['texto'],ENT_QUOTES,'UTF-8')?></p><?php endif;?>
<table><caption>Clientes</caption><thead><tr><th>Nombre</th><th>Documento</th><th>Correo</th></tr></thead><tbody>
<?php foreach($filas as $f):?><tr><td><?=htmlspecialchars($f['nombre'],ENT_QUOTES,'UTF-8')?></td><td><?=htmlspecialchars($f['documento'],ENT_QUOTES,'UTF-8')?></td><td><?=htmlspecialchars($f['correo']??'',ENT_QUOTES,'UTF-8')?></td></tr><?php endforeach;?>
</tbody></table>
<h2>Crear cliente</h2><form method="post"><input type="hidden" name="csrf" value="<?=htmlspecialchars(tokenCsrf(),ENT_QUOTES,'UTF-8')?>">
<label>Nombre<input name="nombre" required minlength="3"></label>
<label>Documento<input name="documento" required></label>
<label>Correo<input name="correo" type="email"></label>
<p><button class="boton">Guardar</button></p></form></main>
<?php require __DIR__.'/app/vistas/parciales/pie.php'; ?>
