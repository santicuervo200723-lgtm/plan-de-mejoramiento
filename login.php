<?php
declare(strict_types=1);
require_once __DIR__.'/app/seguridad/sesion.php';
require_once __DIR__.'/app/seguridad/csrf.php';
require_once __DIR__.'/app/config/conexion.php';
iniciarSesionSegura();
if(!empty($_SESSION['usuario'])){header('Location: dashboard.php');exit;}
$error='';
function registrarIntento(PDO $pdo,string $correo,bool $exito): void{
$pdo->prepare("INSERT INTO intentos_acceso(correo,exito,ip) VALUES(:c,:e,:ip)")->execute([':c'=>$correo,':e'=>$exito?1:0,':ip'=>$_SERVER['REMOTE_ADDR']??'cli']);
}
if($_SERVER['REQUEST_METHOD']==='POST'){
if(!validarCsrf($_POST['csrf']??null)){http_response_code(419);exit('Solicitud no válida. Recargue.');}
$correo=trim((string)($_POST['correo']??''));$clave=(string)($_POST['clave']??'');
if(!filter_var($correo,FILTER_VALIDATE_EMAIL)||strlen($clave)<8){$error='Correo o contraseña incorrectos.';}
else{
$pdo=Conexion::obtener();
// bloqueo: 5 fallos en 15 min
$st=$pdo->prepare("SELECT COUNT(*) c FROM intentos_acceso WHERE correo=:c AND exito=0 AND fecha>datetime('now','-15 minutes')");
try{$st->execute([':c'=>$correo]);$fallos=(int)($st->fetch()['c']??0);}catch(Throwable $e){$st=$pdo->prepare("SELECT COUNT(*) c FROM intentos_acceso WHERE correo=:c AND exito=0 AND fecha>DATE_SUB(NOW(),INTERVAL 15 MINUTE)");$st->execute([':c'=>$correo]);$fallos=(int)($st->fetch()['c']??0);}
if($fallos>=5){$error='Cuenta bloqueada temporalmente. Intente más tarde.';}
else{
$st=$pdo->prepare("SELECT id,nombre,clave_hash,rol,activo,bloqueado_hasta FROM usuarios WHERE correo=:correo LIMIT 1");
$st->execute([':correo'=>$correo]);$u=$st->fetch();
if($u&&(int)$u['activo']===1&&password_verify($clave,$u['clave_hash'])){
if(password_needs_rehash($u['clave_hash'],PASSWORD_DEFAULT)){$n=password_hash($clave,PASSWORD_DEFAULT);$pdo->prepare("UPDATE usuarios SET clave_hash=:h WHERE id=:id")->execute([':h'=>$n,':id'=>$u['id']]);}
registrarIntento($pdo,$correo,true);
require_once __DIR__.'/app/seguridad/sesion.php'; abrirSesion($u);
header('Location: dashboard.php');exit;
}else{registrarIntento($pdo,$correo,false);$error='Correo o contraseña incorrectos.';}
}
}
}
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Ingreso — ZD.TechLab</title><link rel="stylesheet" href="css/tokens.css"><link rel="stylesheet" href="css/estilos.css"></head>
<body><main class="pantalla-ingreso">
<img src="assets/img/logo.svg" alt="Logo de ZD.TechLab" width="120">
<h1>Ingreso al panel de gestión</h1>
<form method="post" action="login.php" autocomplete="on" id="form-login">
<input type="hidden" name="csrf" value="<?=htmlspecialchars(tokenCsrf(),ENT_QUOTES,'UTF-8')?>">
<?php if($error!==''):?><p class="alerta-error" role="alert"><?=htmlspecialchars($error,ENT_QUOTES,'UTF-8')?></p><?php endif;?>
<fieldset><legend>Credenciales</legend>
<label for="correo">Correo electrónico</label><input type="email" id="correo" name="correo" required autocomplete="username">
<label for="clave">Contraseña</label><input type="password" id="clave" name="clave" required autocomplete="current-password" minlength="8">
</fieldset>
<button type="submit" class="boton">Iniciar sesión</button>
<p><small>Prueba: admin@zdtechlab.co / Clave.2026* · vendedor@zdtechlab.co / Clave.2026* · consultor@zdtechlab.co / Clave.2026*</small></p>
</form></main></body></html>
