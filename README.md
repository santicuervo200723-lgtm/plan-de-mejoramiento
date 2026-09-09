# ZD.TechLab — Panel de gestión de tienda tecnológica

## Instalación
1. Opción rápida (sin MySQL): `php -S localhost:8000` en esta carpeta. Abre `http://localhost:8000/login.php`. La BD SQLite `database.sqlite` se crea sola.
2. Opción XAMPP/MySQL: crear BD, importar `sql/estructura.sql`, `sql/datos.sql`, `sql/vistas.sql`. Copiar `app/config/credenciales.example.php` a `app/config/credenciales.php` y completar. Poner carpeta en `htdocs`.

## Credenciales de prueba (clave: Clave.2026*)
- admin@zdtechlab.co — administrador
- vendedor@zdtechlab.co — vendedor
- consultor@zdtechlab.co — consultor

## Estructura
/assets/img, /css (tokens, estilos, reporte), /js, /app (config, seguridad, modelos, vistas), /api, /sql, /reportes
## Seguridad
password_hash/verify, CSRF, mensajes genéricos, bloqueo 5 intentos, sesiones regenerate_id, HttpOnly+SameSite, guardia.php, exigirRol.
## Reportes
reportes.php (pantalla+print), reportes/ventas-csv.php (CSV con BOM). PDF: usar Imprimir→Guardar como PDF (logo y totales incluidos).
