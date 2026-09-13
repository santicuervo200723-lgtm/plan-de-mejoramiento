<?php
$paginaActual=basename($_SERVER['PHP_SELF']??'dashboard.php');
$opciones=[
['archivo'=>'dashboard.php','texto'=>'Tablero','roles'=>['administrador','vendedor','consultor']],
['archivo'=>'productos.php','texto'=>'Productos','roles'=>['administrador','vendedor','consultor']],
['archivo'=>'categorias.php','texto'=>'Categorías','roles'=>['administrador','vendedor']],
['archivo'=>'clientes.php','texto'=>'Clientes','roles'=>['administrador','vendedor']],
['archivo'=>'pedidos.php','texto'=>'Pedidos','roles'=>['administrador','vendedor']],
['archivo'=>'reportes.php','texto'=>'Reportes','roles'=>['administrador','consultor','vendedor']],
['archivo'=>'usuarios.php','texto'=>'Usuarios','roles'=>['administrador']],
];
?>
<aside class="panel__menu" id="menu-lateral"><nav aria-label="Menú principal"><ul class="menu">
<?php foreach($opciones as $op): if(!puede(...$op['roles'])) continue; $activo=($paginaActual===$op['archivo']); ?>
<li><a href="<?=$op['archivo']?>" class="menu__enlace<?=$activo?' menu__enlace--activo':''?>"<?=$activo?' aria-current="page"':''?>><?=htmlspecialchars($op['texto'],ENT_QUOTES,'UTF-8')?></a></li>
<?php endforeach; ?></ul>
<a href="salir.php" class="menu__salir">Cerrar sesión</a></nav></aside>
