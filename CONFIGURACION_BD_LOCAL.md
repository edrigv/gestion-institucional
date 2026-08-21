# Configuración local con copia de uecreyutf8

Este proyecto está preparado para trabajar sobre una copia local de la base institucional.

## Migraciones
Solo se conservan las migraciones del módulo nuevo:
- tipo_requerimiento
- requerimiento
- movimiento_requerimiento
- archivo_requerimiento
- documento_gestion
- firma_documento

Las migraciones genéricas de Laravel para users, cache y jobs fueron retiradas.

## Variables recomendadas en .env
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=uecreyutf8_local
DB_USERNAME=root
DB_PASSWORD=TU_CLAVE_LOCAL

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

Importa primero la copia institucional en `uecreyutf8_local` y luego ejecuta:

```powershell
php artisan optimize:clear
php artisan migrate
php artisan storage:link
php artisan serve
```

Las referencias a usuario y departamentos se mantienen sin claves foráneas físicas contra las tablas institucionales.
