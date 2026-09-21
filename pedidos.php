<?php
require_once __DIR__.'/app/seguridad/guardia.php';
require_once __DIR__.'/app/seguridad/csrf.php';
require_once __DIR__.'/app/config/conexion.php';
exigirRol('administrador','vendedor');
$pdo=Conexion::obtener();
if($_SERVER['REQUEST_METHOD']==='POST'){
if(!validarCsrf($_POST['csrf']??null)){http_response_code(419);exit;}
$clienteId=(int)($_POST['cliente_id']??0);$prodId=(int)($_POST['producto_id']??0);$cant=(int)($_POST['cantidad']??1);
try{
$pdo->beginTransaction();
$pr=$pdo->prepare("SELECT precio,stock FROM productos WHERE id=:id");$pr->execute([':id'=>$prodId]);$p=$pr->fetch();
if(!$p||$p['stock']<$cant)throw new Exception('Stock insuficiente');
$total=$p['precio']*$cant;
$pdo->prepare("INSERT INTO pedidos(cliente_id,fecha,total,estado) VALUES(:c,datetime('now'),:t,'confirmado')")->execute([':c'=>$clienteId,':t'=>$total]);
$pid=(int)$pdo->lastInsertId();
$pdo->prepare("INSERT INTO detalle_pedido(pedido_id,producto_id,cantidad,precio_unitario) VALUES(:p,:pr,:c,:pu)")->execute([':p'=>$pid,':pr'=>$prodId,':c'=>$cant,':pu'=>$p['precio']]);
$pdo->prepare("UPDATE productos SET stock=stock-:c WHERE id=:id")->execute([':c'=>$cant,':id'=>$prodId]);
$pdo->commit();$_SESSION['aviso']=['tipo'=>'exito','texto'=>"Pedido #$pid registrado. Stock descontado."];
}catch(Throwable $e){$pdo->rollBack();$_SESSION['aviso']=['tipo'=>'error','texto'=>'No fue posible registrar el pedido: '.$e->getMessage()];}
header('Location: pedidos.php',true,303);exit;
}
$peds=$pdo->query("SELECT p.id,c.nombre AS cliente,p.fecha,p.total,p.estado FROM pedidos p JOIN clientes c ON c.id=p.cliente_id ORDER BY p.id DESC LIMIT 30")->fetchAll();
$clis=$pdo->query("SELECT id,nombre FROM clientes ORDER BY nombre")->fetchAll();
$prods=$pdo->query("SELECT id,nombre,stock FROM productos WHERE activo=1 ORDER BY nombre")->fetchAll();
$av=$_SESSION['aviso']??null;unset($_SESSION['aviso']);
require __DIR__.'/app/vistas/parciales/cabecera.php';require __DIR__.'/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido"><h1>Pedidos (transacción)</h1>
<?php if($av):?><p class="<?=$av['tipo']==='exito'?'alerta-exito':'alerta-error'?>"><?=htmlspecialchars($av['texto'],ENT_QUOTES,'UTF-8')?></p><?php endif;?>
<form method="post"><input type="hidden" name="csrf" value="<?=htmlspecialchars(tokenCsrf(),ENT_QUOTES,'UTF-8')?>">
<label>Cliente<select name="cliente_id"><?php foreach($clis as $c):?><option value="<?=$c['id']?>"><?=htmlspecialchars($c['nombre'],ENT_QUOTES,'UTF-8')?></option><?php endforeach;?></select></label>
<label>Producto<select name="producto_id"><?php foreach($prods as $p):?><option value="<?=$p['id']?>"><?=htmlspecialchars($p['nombre'],ENT_QUOTES,'UTF-8')?> (stock <?=$p['stock']?>)</option><?php endforeach;?></select></label>
<label>Cantidad<input type="number" name="cantidad" value="1" min="1"></label>
<p><button class="boton">Registrar pedido</button></p></form>
<table><thead><tr><th>ID</th><th>Cliente</th><th>Fecha</th><th>Total</th></tr></thead><tbody>
<?php foreach($peds as $p):?><tr><td><?=$p['id']?></td><td><?=htmlspecialchars($p['cliente'],ENT_QUOTES,'UTF-8')?></td><td><?=$p['fecha']?></td><td>$ <?=number_format((float)$p['total'],0,',','.')?></td></tr><?php endforeach;?>
</tbody></table></main>
<?php require __DIR__.'/app/vistas/parciales/pie.php'; ?>
