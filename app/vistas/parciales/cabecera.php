<?php $u=$_SESSION['usuario']??['nombre'=>'','rol'=>'']; ?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>ZD.TechLab</title><link rel="stylesheet" href="css/tokens.css"><link rel="stylesheet" href="css/estilos.css"><link rel="stylesheet" href="css/reporte.css"></head>
<body><div class="panel">
<header class="panel__barra">
<button class="boton-menu" aria-expanded="false" aria-controls="menu-lateral">☰ Menú</button>
<span><img src="assets/img/logo.svg" alt="Logo ZD.TechLab" width="28" style="vertical-align:middle"> ZD.TechLab</span>
<span><?=htmlspecialchars($u['nombre']??'',ENT_QUOTES,'UTF-8')?> (<?=htmlspecialchars($u['rol']??'',ENT_QUOTES,'UTF-8')?>)</span>
</header>
