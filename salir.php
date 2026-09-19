<?php require_once __DIR__.'/app/seguridad/sesion.php'; iniciarSesionSegura(); cerrarSesion(); header('Location: login.php'); exit;
