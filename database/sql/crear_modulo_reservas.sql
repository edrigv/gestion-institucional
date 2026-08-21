-- Módulo de reservas de espacios
-- Ejecutar sobre la misma base institucional/copía local (uecreyutf8_local)
-- Las referencias a usuario.SERIAL_USR se mantienen lógicas para no alterar la BD institucional existente.

CREATE TABLE IF NOT EXISTS `espacio` (
  `SERIAL_ESP` bigint unsigned NOT NULL AUTO_INCREMENT,
  `NOMBRE_ESP` varchar(120) NOT NULL,
  `DESCRIPCION_ESP` varchar(255) DEFAULT NULL,
  `UBICACION_ESP` varchar(180) DEFAULT NULL,
  `CAPACIDAD_ESP` int unsigned DEFAULT NULL,
  `SERIAL_USR_ENCARGADO` int DEFAULT NULL,
  `ESTADO_ESP` varchar(20) NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`SERIAL_ESP`),
  KEY `espacio_serial_usr_encargado_index` (`SERIAL_USR_ENCARGADO`),
  KEY `espacio_estado_esp_index` (`ESTADO_ESP`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `reserva_espacio` (
  `SERIAL_RES` bigint unsigned NOT NULL AUTO_INCREMENT,
  `NUMERO_RES` varchar(25) NOT NULL,
  `SERIAL_ESP` bigint unsigned NOT NULL,
  `SERIAL_USR_SOLICITA` int NOT NULL,
  `TITULO_RES` varchar(180) NOT NULL,
  `DESCRIPCION_RES` text DEFAULT NULL,
  `FECHA_INICIO_RES` datetime NOT NULL,
  `FECHA_FIN_RES` datetime NOT NULL,
  `ESTADO_RES` varchar(20) NOT NULL DEFAULT 'PENDIENTE',
  `OBSERVACION_RES` text DEFAULT NULL,
  `FECHA_CREACION_RES` datetime NOT NULL,
  `FECHA_RESOLUCION_RES` datetime DEFAULT NULL,
  `SERIAL_USR_RESUELVE` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`SERIAL_RES`),
  UNIQUE KEY `reserva_espacio_numero_res_unique` (`NUMERO_RES`),
  KEY `idx_reserva_horario` (`SERIAL_ESP`,`FECHA_INICIO_RES`,`FECHA_FIN_RES`),
  KEY `reserva_espacio_serial_usr_solicita_index` (`SERIAL_USR_SOLICITA`),
  KEY `reserva_espacio_estado_res_index` (`ESTADO_RES`),
  CONSTRAINT `reserva_espacio_serial_esp_foreign` FOREIGN KEY (`SERIAL_ESP`) REFERENCES `espacio` (`SERIAL_ESP`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `movimiento_reserva` (
  `SERIAL_MRES` bigint unsigned NOT NULL AUTO_INCREMENT,
  `SERIAL_RES` bigint unsigned NOT NULL,
  `SERIAL_USR` int NOT NULL,
  `ACCION_MRES` varchar(50) NOT NULL,
  `ESTADO_ANTERIOR_MRES` varchar(20) DEFAULT NULL,
  `ESTADO_NUEVO_MRES` varchar(20) DEFAULT NULL,
  `OBSERVACION_MRES` text DEFAULT NULL,
  `FECHA_HORA_MRES` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`SERIAL_MRES`),
  KEY `movimiento_reserva_serial_usr_index` (`SERIAL_USR`),
  KEY `movimiento_reserva_accion_mres_index` (`ACCION_MRES`),
  CONSTRAINT `movimiento_reserva_serial_res_foreign` FOREIGN KEY (`SERIAL_RES`) REFERENCES `reserva_espacio` (`SERIAL_RES`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
