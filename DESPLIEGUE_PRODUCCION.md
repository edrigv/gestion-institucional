# Manual de Despliegue en Servidor de Producción
## Sistema de Gestión Institucional y Reservas · U.E.P. Cristo Rey

Este documento contiene las instrucciones precisas para el administrador del servidor (SysAdmin) para poner en marcha el sistema en el entorno de producción.

---

### 1. Requisitos del Servidor
* **PHP**: `>= 8.2` (recomendado PHP 8.3 o 8.4) con las extensiones:
  * `pdo_mysql`, `openssl`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `curl`.
* **Base de Datos**: MySQL 8.0+ o MariaDB 10.5+ con la base institucional existente (`usuario`, `empleado`, `perfil`, `departamentos`, `sucursal`, `sucursaldepartamentos`).
* **Servidor Web**: Apache (con `mod_rewrite` activo) o Nginx apuntando el `DocumentRoot` a la carpeta `/public`.
* **Composer**: `>= 2.2`.

---

### 2. Tablas Nuevas Creadas por las Migraciones
Al ejecutar las migraciones, Laravel **no altera ni elimina ninguna tabla institucional previa**. Únicamente crea las **10 tablas nuevas** necesarias para la operación de los módulos:

1. `tipo_requerimiento` (Catálogo institucional de trámites y reglas de firma/aprobación).
2. `requerimiento` (Expedientes y solicitudes entre departamentos).
3. `movimiento_requerimiento` (Historial y bitácora inmutable de trazabilidad de requerimientos).
4. `archivo_requerimiento` (Archivos, evidencias y documentos adjuntos).
5. `documento` (Documentos formales y resoluciones emitidas).
6. `firma_documento` (Firmas electrónicas y conformidades registradas).
7. `mensaje_usuario` (Mensajería interna y comunicados entre personal/departamentos).
8. `espacio` (Espacios físicos: auditorios, coliseos, laboratorios, canchas).
9. `reserva_espacio` (Solicitudes y reservas de franjas horarias).
10. `movimiento_reserva` (Historial y bitácora de reservas y resolución de conflictos).

---

### 3. Pasos de Instalación en el Servidor

#### Paso 1: Clonar o extraer el proyecto en el servidor
Ubicar el proyecto en la ruta web del servidor (ej. `/var/www/gestion-institucional` o `C:\laragon\www\gestion-institucional`).

#### Paso 2: Configurar las Variables de Entorno (`.env`)
Copiar la plantilla de producción:
```bash
cp .env.production.example .env
```
Editar `.env` y configurar las credenciales de la base de datos real:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio-o-ip-del-servidor

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_base_datos_institucional
DB_USERNAME=usuario_mysql
DB_PASSWORD=contrasena_mysql
```

#### Paso 3: Instalar dependencias de PHP (sin desarrollo)
```bash
composer install --no-dev --optimize-autoloader
```

#### Paso 4: Generar la Clave de Cifrado de la Aplicación
```bash
php artisan key:generate
```

#### Paso 5: Ejecutar las Migraciones (Crea las 10 tablas nuevas)
```bash
php artisan migrate --force
```
*(No requiere correr seeders porque el sistema trabaja directamente con los datos reales existentes).*

#### Paso 6: Crear el Enlace Simbólico de Almacenamiento de Archivos
```bash
php artisan storage:link
```

#### Paso 7: Optimizar Caché de Producción
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Paso 8: Permisos de Carpetas (En Linux / Unix)
Asegurar que el servidor web (`www-data`, `nginx` o `apache`) tenga permisos de escritura en:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

### 4. Configuración del VirtualHost

#### Ejemplo Apache (`.htaccess` en `/public`):
Asegurar que el `DocumentRoot` apunte a la carpeta `/public` del proyecto:
```apache
<VirtualHost *:80>
    ServerName gestion.cristorey.edu.ec
    DocumentRoot /var/www/gestion-institucional/public

    <Directory /var/www/gestion-institucional/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/gestion_error.log
    CustomLog ${APACHE_LOG_DIR}/gestion_access.log combined
</VirtualHost>
```

#### Ejemplo Nginx:
```nginx
server {
    listen 80;
    server_name gestion.cristorey.edu.ec;
    root /var/www/gestion-institucional/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

### 5. Verificación de Seguridad y Puesta en Marcha
* **CORS**: Restringido a las URLs institucionales.
* **Content Security Policy (CSP)**: Activo mediante el middleware `SecurityHeaders`.
* **Rate Limiting**: Activo en `POST /login` contra ataques de fuerza bruta (5 intentos por minuto).
* **Sesiones**: Cifradas con AES-256-CBC.
