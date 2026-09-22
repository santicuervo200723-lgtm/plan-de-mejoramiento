<?php
declare(strict_types=1);
require_once __DIR__.'/../app/seguridad/guardia.php';
require_once __DIR__.'/../app/config/conexion.php';
header('Content-Type: application/json; charset=utf-8');
$pdo=Conexion::obtener();
try{
$meses=$pdo->query("SELECT strftime('%Y-%m',fecha) periodo, SUM(total) total_vendido, COUNT(*) cantidad FROM pedidos WHERE estado='confirmado' GROUP BY periodo ORDER BY periodo DESC LIMIT 12")->fetchAll();
$meses=array_reverse($meses);
$cats=$pdo->query("SELECT c.nombre categoria, COALESCE(SUM(d.cantidad*d.precio_unitario),0) total_vendido, COALESCE(SUM(d.cantidad),0) unidades FROM categorias c LEFT JOIN productos pr ON pr.categoria_id=c.id LEFT JOIN detalle_pedido d ON d.producto_id=pr.id GROUP BY c.nombre ORDER BY total_vendido DESC")->fetchAll();
}catch(Throwable $e){
$meses=$pdo->query("SELECT DATE_FORMAT(fecha,'%Y-%m') periodo, SUM(total) total_vendido FROM pedidos GROUP BY periodo ORDER BY periodo DESC LIMIT 12")->fetchAll();
$cats=[];
}
echo json_encode(['ventasMes'=>['etiquetas'=>array_column($meses,'periodo'),'valores'=>array_map('floatval',array_column($meses,'total_vendido'))],'categorias'=>['etiquetas'=>array_column($cats,'categoria'),'valores'=>array_map('floatval',array_column($cats,'total_vendido')),'unidades'=>$cats]],JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR);
