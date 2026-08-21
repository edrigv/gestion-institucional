# Autenticación institucional - copia local

El proyecto ya no permite seleccionar manualmente "quién soy". Toda acción usa el usuario que inició sesión.

## Preparación

1. Asegúrate de que `.env` apunta a `uecreyutf8_local` y usa:
   - `SESSION_DRIVER=file`
   - `CACHE_STORE=file`
   - `QUEUE_CONNECTION=sync`
2. Ejecuta:

```powershell
php artisan optimize:clear
php artisan migrate
php artisan db:seed
php artisan serve
```

El seeder es idempotente para los datos de demostración y puede ejecutarse más de una vez.

## Usuarios de prueba

Contraseña para todos: `PRUEBA123`

- `carlos.p` - Carlos Mendoza - Administrador - Sistemas
- `maria.p` - Maria Torres - Empleado - Administración
- `ana.p` - Ana Vera - Autoridad - Académico
- `sofia.p` - Sofia Lopez - Empleado - Talento Humano

## Prueba de mensajería

1. Inicia sesión como `sofia.p`.
2. Abre **Mensajes > Nuevo mensaje**.
3. El remitente aparece fijo como Sofia Lopez; no existe un selector para cambiarlo.
4. Selecciona a Carlos Mendoza como destinatario y envía el mensaje.
5. Cierra sesión.
6. Inicia sesión como `carlos.p`.
7. En la bandeja aparecerá el mensaje de Sofia.
8. Un usuario que no sea remitente ni destinatario recibe HTTP 403 si intenta abrir ese mensaje directamente.

## Requerimientos

Al crear un requerimiento, `SERIAL_USR_SOLICITA` se toma automáticamente de la sesión y `SERIAL_DEP_ORIGEN` se calcula desde `empleado -> sucursaldepartamentos -> departamentos`. El usuario ya no puede indicar manualmente que otra persona es el solicitante.

## Producción

El login usa `usuario.CODIGO_USR` y `usuario.CLAVE_USR`. Para la copia local, las claves de prueba están guardadas directamente como `PRUEBA123`. El controlador también acepta hashes bcrypt/Argon. Antes del despliegue definitivo debe confirmarse cómo cifra actualmente la aplicación institucional `CLAVE_USR`; si usa un esquema heredado distinto, solo se adapta el método `claveValida()` de `AuthController`.
