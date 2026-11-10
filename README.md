# Veterinaria

Sistema web de gestión para una veterinaria. Permite administrar clientes, mascotas y turnos desde una interfaz en español desarrollada en PHP, JavaScript y CSS.

## Funcionalidades

- Panel inicial con conteos de clientes, mascotas y turnos.
- Gestión de clientes y mascotas, incluyendo bajas lógicas.
- Agenda de turnos con búsqueda, edición, estados y cancelación.
- Inicio de sesión de administrador con sesión PHP y contraseñas almacenadas como hash.

## Tecnologías

- PHP 8.0 o superior
- MySQL o MariaDB mediante PDO
- JavaScript sin framework
- CSS personalizado

## Estructura

```text
api/                   Endpoints JSON para clientes, mascotas y turnos
assets/css/            Estilos globales y específicos por página
assets/js/             Interacción frontend y llamadas a la API
config/auth.example.php Ejemplo de configuración local del acceso
includes/auth.php      Sesiones, autenticación y utilidades CSRF
includes/              Layout compartido
pages/                 Pantallas de inicio, clientes, mascotas y turnos
index.php              Entrada del sistema (requiere iniciar sesión)
login.php              Formulario de acceso
logout.php             Cierre de sesión
```

## Requisitos

- PHP 8.0 o superior con las extensiones PDO y PDO MySQL habilitadas.
- MySQL o MariaDB y la base de datos del proyecto ya creada.
- Apache/XAMPP, WAMP, MAMP o el servidor de desarrollo de PHP.

## Instalación y configuración

1. Cloná el repositorio y entrá a la carpeta:

   ```bash
   git clone https://github.com/lili227-99/veterinaria.git
   cd veterinaria
   ```

2. Configurá `database.php` en la raíz con los datos de conexión de tu base existente. Este archivo está excluido de Git para no publicar credenciales.

3. Creá el archivo privado de acceso a partir del ejemplo:

   ```bash
   cp config/auth.example.php config/auth.php
   ```

   En Windows, también podés copiar `config/auth.example.php` desde el Explorador de archivos y renombrar la copia como `auth.php`.

4. Generá un hash para tu contraseña (reemplazá el texto de ejemplo por una contraseña propia):

   ```bash
   php -r "echo password_hash('CAMBIAME_POR_UNA_CLAVE_SEGURA', PASSWORD_DEFAULT), PHP_EOL;"
   ```

   Copiá el resultado en `config/auth.php`, en `password_hash`, y elegí el nombre de usuario que quieras. No guardes una contraseña en texto plano ni subas `config/auth.php` al repositorio.

5. Iniciá el servidor. Con PHP integrado:

   ```bash
   php -S localhost:8000
   ```

   O con XAMPP, iniciá Apache y MySQL y colocá la carpeta en `htdocs`.

6. Abrí `http://localhost:8000/login.php` (o la URL local correspondiente a XAMPP) e ingresá con el usuario y la contraseña que configuraste.

## Uso

- Inicio: `index.php?pages=inicio`
- Clientes: `index.php?pages=clientes`
- Mascotas: `index.php?pages=mascotas`
- Turnos: `index.php?pages=turnos`
- Para salir, usá el botón **Cerrar sesión** del menú lateral.

Las páginas principales redirigen al login cuando no existe una sesión autenticada. El formulario incluye protección CSRF, la sesión se renueva al iniciar sesión y la cookie se configura como HttpOnly y SameSite=Lax.

## Importante sobre la API

El login protege las pantallas del sistema. Para que las rutas de `api/` también queden cerradas al acceso directo, cada endpoint debe validar la sesión antes de ejecutar su lógica. Al comienzo de cada archivo PHP dentro de `api/clientes/`, `api/mascotas/` y `api/turnos/`, agregá esta línea antes de incluir `database.php`:

```php
require_once __DIR__ . '/../../includes/auth.php';
auth_require_json();
```

En producción también se recomienda verificar token CSRF en las operaciones POST de la API. No publiques el sistema en Internet hasta completar esa protección y probar cada endpoint sin sesión.

## Solución de problemas

- **Falta `config/auth.php`:** copiá el archivo de ejemplo, generá el hash y completá la configuración.
- **No conecta a la base:** revisá `database.php`, que MySQL/MariaDB esté iniciado y que PDO MySQL esté habilitado.
- **Se redirige al login continuamente:** verificá que el navegador acepte cookies y que PHP pueda escribir sesiones.

## Seguridad y alcance

Este login es una protección básica para un entorno local o una primera versión. Para un sistema en producción, añadí protección CSRF a las llamadas de escritura de la API, límite de intentos de acceso, HTTPS, gestión de usuarios y roles, y pruebas de seguridad. Usá siempre una contraseña fuerte y mantené los archivos con credenciales fuera del control de versiones.
