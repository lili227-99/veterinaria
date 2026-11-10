# Veterinaria

Sistema de gestión para una veterinaria desarrollado en PHP con una interfaz web simple, rápida y funcional para administrar clientes, mascotas y turnos.

## Descripción

Esta aplicación está pensada para ser utilizada por una clínica veterinaria o consultorio para llevar el control de:

- clientes
- mascotas
- turnos
- estados de atención
- resumen del día

El proyecto sigue un enfoque tipo CRUD, con frontend en JavaScript y CSS, y backend en PHP usando PDO para acceder a la base de datos.

## Stack tecnológico

- PHP 8+
- MySQL / MariaDB
- JavaScript (vanilla)
- CSS personalizado
- PDO para consultas preparadas

## Características principales

- Dashboard inicial con resumen general
- Gestión de clientes con listado, búsqueda y edición
- Gestión de mascotas con alta, baja lógica y edición
- Gestión de turnos con validación por fecha, motivo y estado
- Filtros de búsqueda por turno, estado o datos relevantes
- Formularios modal para mejorar la experiencia de uso
- API REST-like interna dentro de `api/`

## Estructura del proyecto

```text
veterinaria/
├── api/
│   ├── clientes/
│   │   ├── crear.php
│   │   ├── editar.php
│   │   ├── eliminar.php
│   │   └── listar.php
│   ├── mascotas/
│   │   ├── crear.php
│   │   ├── editar.php
│   │   ├── eliminar.php
│   │   └── listar.php
│   └── turnos/
│       ├── crear.php
│       ├── editar.php
│       ├── eliminar.php
│       └── listar.php
├── assets/
│   ├── css/
│   └── js/
├── includes/
│   ├── footer.php
│   └── header.php
├── pages/
│   ├── clientes.php
│   ├── inicio.php
│   ├── mascotas.php
│   └── turnos.php
├── .gitignore
├── index.php
├── database.php
├── README.md
└── LICENSE (si aplica)
```

## Requisitos previos

Antes de levantar el proyecto, necesitás tener instalado:

- PHP 8 o superior
- MySQL o MariaDB
- Un servidor local como XAMPP, WAMP, MAMP o PHP embebido

## Instalación

1. Cloná el repositorio:

```bash
git clone https://github.com/lili227-99/veterinaria.git
cd veterinaria
```

2. Configurá la conexión a la base de datos.

El proyecto espera un archivo `database.php` en la raíz del proyecto. Si ya lo tenés creado, solo dejalo allí con tus credenciales reales.

Ejemplo:

```php
<?php
$host = 'localhost';
$db   = 'veterinaria';
$user = 'root';
$pass = '';

try {
    $conexion = new PDO(
        "mysql:host={$host};dbname={$db};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}
```

3. Verificá que la base de datos tenga las tablas necesarias según tu estructura real.

4. Levantá el proyecto.

## Ejecución local

Opcionalmente, podés usar el servidor embebido de PHP:

```bash
php -S localhost:8000
```

Luego abrí en el navegador:

```text
http://localhost:8000/index.php?pages=inicio
```

## Páginas disponibles

- `inicio` → resumen general
- `clientes` → gestión de clientes
- `mascotas` → gestión de mascotas
- `turnos` → gestión de turnos

## Cómo funciona el sistema

1. `index.php` recibe el parámetro `pages` y decide qué módulo cargar.
2. Cada vista se renderiza desde la carpeta `pages/`.
3. La lógica del frontend en `assets/js/` llama a los archivos de `api/`.
4. Los archivos en `api/` ejecutan consultas a la base de datos y devuelven JSON.
5. La UI actualiza los resultados sin recargar toda la página.

## Base de datos

La aplicación trabaja con una estructura típica de un sistema de gestión veterinaria. En general, las tablas más relevantes son:

- `clientes`
- `mascotas`
- `turnos`

Si ya tenés la base de datos creada, solo debés asegurarte de que los nombres de tablas y columnas coincidan con las consultas del proyecto.

## Seguridad

El proyecto incorpora algunas buenas prácticas básicas, como:

- uso de PDO con consultas preparadas
- validación de datos del lado del servidor
- control de errores mediante `try/catch`

Aun así, si se va a usar en entorno real o de producción, se recomienda reforzar:

- autenticación de usuarios
- protección por sesiones
- protección CSRF
- control de permisos por rol
- validación más rígida de entradas
- logs y alertas de errores

## Solución de problemas

### 1. Error de conexión a MySQL

Verificá:

- que MySQL esté levantado
- que el host sea correcto (`localhost` o IP real)
- que el usuario, contraseña y nombre de base sean correctos

### 2. `database.php` no existe

Asegurate de que el archivo esté presente en la raíz del proyecto.

### 3. La app no carga las páginas

Revisá que el servidor esté activo y que la URL sea:

```text
http://localhost:8000/index.php?pages=inicio
```

## Consideraciones para producción

Este proyecto está bien para uso local y para una primera versión funcional, pero si querés llevarlo a producción, te recomiendo:

- agregar login y autenticación
- separar entorno de desarrollo y producción
- documentar variables de entorno
- implementar logs y manejo de errores centralizado
- montar el app con Docker o servidor web profesional

## Autor

Proyecto desarrollado por Liliana.

## Siguiente paso recomendado

Si ya tenés la base de datos armada y funcionando, lo que más necesitás ahora es:

- proteger las páginas con sesión/login,
- validar mejor los permisos,
- documentar el esquema de la base de datos,
- dejar un entorno más seguro y profesional.

## Qué hacer con el README que ya tenías

El README anterior ya servía como punto de partida, pero el nuevo contenido lo deja más claro para cualquiera que quiera clonar el proyecto, levantarlo, entender la estructura y saber cómo configurarlo.

Si querés, en el próximo paso puedo ayudarte a hacer una segunda mejora importante: agregar autenticación con login y protección por sesión para que la app quede realmente segura y lista para usar en una clínica real.