<?php
declare(strict_types=1);
final class Conexion
{
private static ?PDO $pdo = null;
public static function obtener(): PDO
{
if (self::$pdo === null) {
$cfgFile = __DIR__ . '/credenciales.php';
if (file_exists($cfgFile)) {
$cfg = require $cfgFile;
if (($cfg['driver'] ?? 'sqlite') === 'mysql') {
$dsn = "mysql:host={$cfg['host']};dbname={$cfg['bd']};charset=utf8mb4";
self::$pdo = new PDO($dsn, $cfg['usuario'], $cfg['clave'], [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
PDO::ATTR_EMULATE_PREPARES => false,
]);
self::initSqliteCompat();
return self::$pdo;
}
}
// Fallback SQLite (portable, cero instalación)
$dbFile = dirname(__DIR__, 2) . '/database.sqlite';
$first = !file_exists($dbFile);
self::$pdo = new PDO('sqlite:' . $dbFile, null, null, [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
if ($first) { self::sembrarSqlite(self::$pdo); }
}
return self::$pdo;
}
private static function initSqliteCompat(): void {}
private static function sembrarSqlite(PDO $pdo): void
{
$est = file_get_contents(dirname(__DIR__, 2) . '/sql/estructura_sqlite.sql');
if ($est) { $pdo->exec($est); }
$h = password_hash('Clave.2026*', PASSWORD_DEFAULT);
$pdo->exec("INSERT INTO categorias(nombre,descripcion) VALUES ('Periféricos','Teclados, mouse'),('Pantallas','Monitores'),('Almacenamiento','SSD, USB'),('Redes','Routers, cables'),('Energía','UPS')");
$st=$pdo->prepare("INSERT INTO usuarios(nombre,correo,clave_hash,rol) VALUES (?,?,?,?)");
$st->execute(['Admin ZD','admin@zdtechlab.co',$h,'administrador']);
$st->execute(['Vendedor ZD','vendedor@zdtechlab.co',$h,'vendedor']);
$st->execute(['Consultor ZD','consultor@zdtechlab.co',$h,'consultor']);
$prods=[['Teclado mecánico RGB',1,120000,14,5],['Mouse inalámbrico',1,65000,32,5],['Audífonos gamer',1,95000,20,5],['Monitor 24 pulg',2,890000,8,3],['Monitor 27 pulg 4K',2,1450000,4,2],['SSD 1 TB',3,320000,7,5],['SSD 512 GB',3,210000,15,5],['Disco HDD 2 TB',3,280000,10,4],['Memoria USB 64 GB',3,45000,50,10],['Router WiFi 6',4,260000,12,4],['Switch 8 puertos',4,180000,9,3],['Cable HDMI 2m',4,35000,40,10],['UPS 1000VA',5,420000,6,2],['Regulador 2000W',5,150000,11,4],['Webcam HD',1,130000,18,5],['Parlante Bluetooth',1,110000,22,5],['Portátil 14 pulg i5',2,2350000,5,2],['Tablet 10 pulg',2,780000,2,3],['Cable red Cat6 3m',4,25000,60,15],['Base refrigerante',1,70000,25,5]];
$sp=$pdo->prepare("INSERT INTO productos(nombre,categoria_id,precio,stock,stock_minimo) VALUES (?,?,?,?,?)");
foreach($prods as $p){$sp->execute($p);}
$clis=[['Ana Ríos','10001','ana@correo.co','3001110001'],['Luis Pérez','10002','luis@correo.co','3001110002'],['Marta Gómez','10003','marta@correo.co','3001110003'],['Carlos Ruiz','10004','carlos@correo.co','3001110004'],['Sofía León','10005','sofia@correo.co','3001110005'],['Diego Mora','10006','diego@correo.co','3001110006'],['Paula Díaz','10007','paula@correo.co','3001110007'],['Jorge Casas','10008','jorge@correo.co','3001110008'],['Lucía Fernández','10009','lucia@correo.co','3001110009'],['Pedro Salas','10010','pedro@correo.co','3001110010']];
$sc=$pdo->prepare("INSERT INTO clientes(nombre,documento,correo,telefono) VALUES (?,?,?,?)");
foreach($clis as $c){$sc->execute($c);}
$peds=[[1,'2026-03-10 10:00',185000],[2,'2026-04-12 11:00',890000],[3,'2026-05-05 09:30',385000],[4,'2026-06-15 14:00',640000],[5,'2026-07-02 16:00',420000],[1,'2026-07-20 10:00',780000],[6,'2026-08-03 12:00',950000],[7,'2026-08-18 15:00',1200000],[8,'2026-09-01 09:00',2350000],[9,'2026-09-05 11:00',650000],[10,'2026-09-06 13:00',320000],[2,'2026-09-07 10:00',1450000],[3,'2026-09-08 12:00',260000],[4,'2026-09-09 14:00',890000],[5,'2026-09-10 16:00',110000]];
$spd=$pdo->prepare("INSERT INTO pedidos(cliente_id,fecha,total,estado) VALUES (?,?,?,'confirmado')");
foreach($peds as $pd){$spd->execute([$pd[0],$pd[1],$pd[2]]);}
$dets=[[1,2,1,65000],[1,9,2,45000],[2,4,1,890000],[3,1,2,120000],[4,6,2,320000],[5,13,1,420000],[6,18,1,780000],[7,7,3,210000],[8,17,1,2350000],[9,17,1,2350000],[10,2,10,65000],[11,6,1,320000],[12,5,1,1450000],[13,10,1,260000],[14,4,1,890000],[15,16,1,110000]];
$sd=$pdo->prepare("INSERT INTO detalle_pedido(pedido_id,producto_id,cantidad,precio_unitario) VALUES (?,?,?,?)");
foreach($dets as $d){$sd->execute($d);}
}
}
