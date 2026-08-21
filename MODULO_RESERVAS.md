# Módulo de reservas de espacios

## Regla principal
Una coincidencia de horario **no bloquea** una nueva solicitud. La reserva se crea como `PENDIENTE` y se registra `CONFLICTO_DETECTADO` en su historial.

El encargado puede:
1. Aprobar directamente cuando no hay reservas aprobadas en conflicto.
2. Rechazar la nueva solicitud.
3. Si hay conflicto, cancelar las reservas aprobadas anteriores y aprobar la nueva. Todo se ejecuta en una transacción y queda registrado en los historiales.

## Tablas nuevas
- `espacio`
- `reserva_espacio`
- `movimiento_reserva`

Las referencias a `usuario.SERIAL_USR` son lógicas. No se agregan FK físicas hacia las tablas institucionales existentes.

## Instalación recomendada
```powershell
php artisan optimize:clear
php artisan migrate
php artisan db:seed
php artisan serve
```

`db:seed` crea/actualiza los espacios de prueba:
- Auditorio — encargado: `carlos.p`
- Coliseo — encargada: `ana.p`
- Sala de reuniones — encargada: `maria.p`

También están disponibles los scripts manuales:
- `database/sql/crear_modulo_reservas.sql`
- `database/sql/datos_prueba_espacios.sql`

No ejecute el SQL de creación y las migraciones para crear las mismas tablas dos veces; use preferentemente `php artisan migrate`. El SQL queda como alternativa/documentación para despliegue.

## Prueba de conflicto sugerida
1. Inicie sesión como `sofia.p` y solicite el Auditorio para un horario futuro.
2. Inicie sesión como `carlos.p` (encargado del Auditorio) y vaya a **Gestionar reservas**. Apruebe la solicitud.
3. Inicie sesión como `maria.p` y solicite el Auditorio en un horario que se solape con la reserva aprobada.
4. La aplicación debe advertir el conflicto, pero permitir enviar la solicitud.
5. Inicie sesión como `carlos.p`. Abra la nueva solicitud. Verá la reserva anterior en conflicto y podrá:
   - rechazar la nueva, o
   - **Cancelar anteriores y aprobar esta**.
6. Si usa reemplazo, la reserva anterior pasa a `CANCELADA`, la nueva a `APROBADA`, y ambos cambios quedan registrados.

## Horario visual
Se añadió una vista diaria por espacio en `/reservas/horario`. Muestra reservas APROBADAS y PENDIENTES entre las 06:00 y 22:00, identifica solicitudes pendientes que se solapan con reservas aprobadas y permite navegar por fecha y espacio. El horario es informativo: una ocupación nunca bloquea el envío de una nueva solicitud.
