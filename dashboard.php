<?php
require_once __DIR__.'/app/seguridad/guardia.php';
require_once __DIR__.'/app/config/conexion.php';
$pdo=Conexion::obtener();
try{
$ind=$pdo->query("SELECT (SELECT COUNT(*) FROM productos WHERE activo=1) AS pa,(SELECT COUNT(*) FROM pedidos WHERE strftime('%Y-%m',fecha)=strftime('%Y-%m','now')) AS pm,(SELECT COALESCE(SUM(total),0) FROM pedidos WHERE strftime('%Y-%m',fecha)=strftime('%Y-%m','now')) AS vm,(SELECT COUNT(*) FROM productos WHERE stock<5 AND activo=1) AS sc")->fetch();
}catch(Throwable $e){
$ind=$pdo->query("SELECT (SELECT COUNT(*) FROM productos WHERE activo=1) AS pa,(SELECT COUNT(*) FROM pedidos WHERE YEAR(fecha)=YEAR(CURDATE()) AND MONTH(fecha)=MONTH(CURDATE())) AS pm,(SELECT COALESCE(SUM(total),0) FROM pedidos WHERE YEAR(fecha)=YEAR(CURDATE()) AND MONTH(fecha)=MONTH(CURDATE())) AS vm,(SELECT COUNT(*) FROM productos WHERE stock<5 AND activo=1) AS sc")->fetch();
}
require __DIR__.'/app/vistas/parciales/cabecera.php';
require __DIR__.'/app/vistas/parciales/menu.php';
?>
<main class="panel__contenido"><h1>Resumen general</h1>
<p>Periodo: septiembre de 2026</p>
<section class="indicadores">
<article class="tarjeta"><p class="tarjeta__rotulo">Productos activos</p><p class="tarjeta__valor"><?=(int)$ind['pa']?></p></article>
<article class="tarjeta"><p class="tarjeta__rotulo">Pedidos del mes</p><p class="tarjeta__valor"><?=(int)$ind['pm']?></p></article>
<article class="tarjeta"><p class="tarjeta__rotulo">Ventas del mes</p><p class="tarjeta__valor">$ <?=number_format((float)$ind['vm'],0,',','.')?></p></article>
<article class="tarjeta"><p class="tarjeta__rotulo">Stock crítico</p><p class="tarjeta__valor"><?=(int)$ind['sc']?></p></article>
</section>
<section class="graficos">
<div class="grafico-card"><h3>Ventas por mes (barras)</h3><canvas id="g-ventas"></canvas></div>
<div class="grafico-card"><h3>Ventas por categoría (dona)</h3><canvas id="g-categorias"></canvas></div>
<div class="grafico-card"><h3>Unidades por categoría (línea)</h3><canvas id="g-linea"></canvas></div>
</section></main>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="js/graficos.js"></script>
<?php require __DIR__.'/app/vistas/parciales/pie.php'; ?>
