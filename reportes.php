<?php
require_once __DIR__.'/app/seguridad/guardia.php';
require_once __DIR__.'/app/config/conexion.php';
$pdo=Conexion::obtener();
$numeroReporte='R-2026-0148';
$ventas=$pdo->query("SELECT c.nombre categoria, COALESCE(SUM(d.cantidad),0) unidades, COALESCE(SUM(d.cantidad*d.precio_unitario),0) total FROM categorias c LEFT JOIN productos pr ON pr.categoria_id=c.id LEFT JOIN detalle_pedido d ON d.producto_id=pr.id GROUP BY c.nombre")->fetchAll();
$total=array_sum(array_column($ventas,'total'));
$crit=$pdo->query("SELECT pr.nombre,c.nombre cat,pr.stock,pr.stock_minimo FROM productos pr JOIN categorias c ON c.id=pr.categoria_id WHERE pr.activo=1 AND pr.stock<=pr.stock_minimo")->fetchAll();
$top=$pdo->query("SELECT cl.nombre, COUNT(p.id) pedidos, COALESCE(SUM(p.total),0) total FROM clientes cl LEFT JOIN pedidos p ON p.cliente_id=cl.id GROUP BY cl.nombre ORDER BY total DESC LIMIT 10")->fetchAll();
require __DIR__.'/app/vistas/parciales/cabecera.php';require __DIR__.'/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido">
<?php $numeroReporte='R-2026-0148'; require __DIR__.'/app/vistas/reportes/encabezado.php'; ?>
<p class="no-imprimir"><button class="boton" onclick="window.print()">Imprimir / Guardar PDF</button> <a class="boton boton--sec" href="reportes/ventas-csv.php">Descargar CSV</a></p>
<h2>Reporte de ventas por categoría</h2>
<p><small>Filtros: 01/01/2026 al 13/09/2026 · Estado: confirmado · Todas las categorías</small></p>
<table><thead><tr><th>Categoría</th><th>Unidades</th><th>Total vendido</th><th>%</th></tr></thead><tbody>
<?php foreach($ventas as $v):?><tr><td><?=htmlspecialchars($v['categoria'],ENT_QUOTES,'UTF-8')?></td><td><?=$v['unidades']?></td><td>$ <?=number_format((float)$v['total'],0,',','.')?></td><td><?= $total>0?number_format($v['total']/$total*100,1):0?></td></tr><?php endforeach;?>
<tr><td><b>Total general</b></td><td><b><?=array_sum(array_column($ventas,'unidades'))?></b></td><td><b>$ <?=number_format((float)$total,0,',','.')?></b></td><td><b>100</b></td></tr>
</tbody></table>
<h2>Inventario con stock crítico</h2>
<table><thead><tr><th>Producto</th><th>Categoría</th><th>Stock</th><th>Mínimo</th></tr></thead><tbody>
<?php foreach($crit as $c):?><tr><td><?=htmlspecialchars($c['nombre'],ENT_QUOTES,'UTF-8')?></td><td><?=$c['cat']?></td><td><?=$c['stock']?></td><td><?=$c['stock_minimo']?></td></tr><?php endforeach;?>
</tbody></table>
<h2>Pedidos por cliente (top)</h2>
<table><thead><tr><th>Cliente</th><th>Pedidos</th><th>Total</th></tr></thead><tbody>
<?php foreach($top as $t):?><tr><td><?=htmlspecialchars($t['nombre'],ENT_QUOTES,'UTF-8')?></td><td><?=$t['pedidos']?></td><td>$ <?=number_format((float)$t['total'],0,',','.')?></td></tr><?php endforeach;?>
</tbody></table>
<div class="reporte__pie"><span>Documento generado automáticamente por ZD.TechLab. Información de uso interno.</span><span>Página 1 de 1</span></div>
</main>
<?php require __DIR__.'/app/vistas/parciales/pie.php'; ?>
