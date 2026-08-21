-- Datos de prueba del módulo de reservas.
-- Requiere que existan carlos.p, maria.p y ana.p en la tabla usuario.

INSERT INTO espacio (NOMBRE_ESP, DESCRIPCION_ESP, UBICACION_ESP, CAPACIDAD_ESP, SERIAL_USR_ENCARGADO, ESTADO_ESP, created_at, updated_at)
SELECT 'Auditorio', 'Auditorio institucional para reuniones y eventos.', 'Bloque principal', 180,
       (SELECT SERIAL_USR FROM usuario WHERE CODIGO_USR='carlos.p' LIMIT 1), 'ACTIVO', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM espacio WHERE NOMBRE_ESP='Auditorio');

INSERT INTO espacio (NOMBRE_ESP, DESCRIPCION_ESP, UBICACION_ESP, CAPACIDAD_ESP, SERIAL_USR_ENCARGADO, ESTADO_ESP, created_at, updated_at)
SELECT 'Coliseo', 'Espacio deportivo y de eventos masivos.', 'Área deportiva', 600,
       (SELECT SERIAL_USR FROM usuario WHERE CODIGO_USR='ana.p' LIMIT 1), 'ACTIVO', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM espacio WHERE NOMBRE_ESP='Coliseo');

INSERT INTO espacio (NOMBRE_ESP, DESCRIPCION_ESP, UBICACION_ESP, CAPACIDAD_ESP, SERIAL_USR_ENCARGADO, ESTADO_ESP, created_at, updated_at)
SELECT 'Sala de reuniones', 'Sala para reuniones administrativas y académicas.', 'Administración', 24,
       (SELECT SERIAL_USR FROM usuario WHERE CODIGO_USR='maria.p' LIMIT 1), 'ACTIVO', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM espacio WHERE NOMBRE_ESP='Sala de reuniones');
