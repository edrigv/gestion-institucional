# Mensajería interna - prueba local

Este proyecto incluye mensajería entre usuarios obtenidos de las tablas institucionales `usuario`, `empleado` y `perfil`.

## Preparación

1. La base local debe llamarse `uecreyutf8_local` (o cambia `DB_DATABASE` en `.env`).
2. Ejecuta las migraciones nuevas:
   `php artisan migrate`
3. Si la copia solo contiene estructura y todavía no cargaste usuarios de prueba:
   `php artisan db:seed`
4. Limpia caché:
   `php artisan optimize:clear`
5. Inicia:
   `php artisan serve`

## Prueba

Abre `/mensajes`.

- Selecciona a Carlos Mendoza, Maria Torres o Ana Vera como usuario actual.
- Pulsa **Nuevo mensaje**.
- Elige un destinatario diferente.
- Envía el mensaje.
- Cambia a la bandeja del destinatario para comprobar la recepción.
- Al abrir el mensaje desde la bandeja del destinatario se registra como leído.

El selector de usuario actual es temporal. Cuando se implemente la autenticación, será sustituido por el usuario de la sesión.
