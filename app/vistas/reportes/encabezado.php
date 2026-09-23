<?php
const APP=['nombre'=>'ZD.TechLab','lema'=>'Sistema de gestión de tienda tecnológica','version'=>'1.0','nit'=>'900.123.456-7','ciudad'=>'Bogotá D.C.','correo'=>'soporte@zdtechlab.co','logo'=>__DIR__.'/../../../assets/img/logo.svg'];
function logoBase64(): string { $f=APP['logo']; return file_exists($f)?'data:image/svg+xml;base64,'.base64_encode(file_get_contents($f)):''; }
?>
<header class="reporte__encabezado">
<img src="<?=logoBase64()?>" alt="Logo de <?=APP['nombre']?>" class="reporte__logo">
<div><h1><?=APP['nombre']?></h1><p><?=APP['lema']?> · v<?=APP['version']?></p><p>NIT <?=APP['nit']?> · <?=APP['ciudad']?> · <?=APP['correo']?></p></div>
<div class="reporte__meta"><p>Generado: <?=date('d/m/Y H:i')?></p><p>Usuario: <?=htmlspecialchars($_SESSION['usuario']['nombre']??'',ENT_QUOTES,'UTF-8')?> (<?=htmlspecialchars($_SESSION['usuario']['rol']??'',ENT_QUOTES,'UTF-8')?>)</p><p>Reporte N.º <?=$numeroReporte??'R-2026-0148'?></p></div>
</header>
