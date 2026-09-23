<?php
require_once __DIR__.'/../app/seguridad/guardia.php';
require_once __DIR__.'/../app/config/conexion.php';
$pdo=Conexion::obtener();
$filas=$pdo->query("SELECT c.nombre categoria, COALESCE(SUM(d.cantidad),0) unidades, COALESCE(SUM(d.cantidad*d.precio_unitario),0) total FROM categorias c LEFT JOIN productos pr ON pr.categoria_id=c.id LEFT JOIN detalle_pedido d ON d.producto_id=pr.id GROUP BY c.nombre")->fetchAll();
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="ventas-'.date('Ymd').'.csv"');
$s=fopen('php://output','w');fwrite($s,"\xEF\xBB\xBF");
fputcsv($s,['Categoría','Unidades','Total vendido'],';');
foreach($filas as $f){fputcsv($s,[$f['categoria'],$f['unidades'],$f['total']],';');}
fclose($s);
