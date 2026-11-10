# Sistema de Gestión Veterinaria

Aplicación web para administrar una veterinaria: clientes, mascotas y turnos. Desarrollada con PHP, MySQL, HTML, CSS y JavaScript.

## Funcionalidades

- **Inicio**: panel resumen con contadores generales.
- **Clientes**: alta, edición, listado (activos, inactivos o todos) y baja lógica.
- **Mascotas**: alta, edición, listado y baja lógica, asociadas a un cliente.
- **Turnos**: alta con validación, edición, listado con filtros y cancelación.
- Formularios en ventanas modales, con la lógica del lado del cliente en JavaScript y la comunicación con el servidor a través de la carpeta `api/`.

## Tecnologías

- PHP
- MySQL
- HTML y CSS (estilos propios)
- JavaScript
- XAMPP (entorno local)

## Estructura del proyecto

```
index.php               Router de entrada, selecciona la página mediante ?pages=
includes/
  header.php            Layout compartido, menú lateral y CSS
  footer.php            Cierra el layout
pages/
  inicio.php            Panel con resumen y contadores
  clientes.php          Interfaz de clientes y formulario modal
  mascotas.php          Interfaz de mascotas y formulario modal
  turnos.php            Interfaz de turnos y formulario modal
api/
  clientes/
    listar.php          Lista clientes activos, inactivos o todos
    crear.php           Crea un cliente
    editar.php          Actualiza un cliente
    eliminar.php        Baja lógica (desactiva) de un cliente
  mascotas/
    listar.php          Lista mascotas
    crear.php           Crea una mascota
    editar.php          Actualiza una mascota
    eliminar.php        Baja lógica (desactiva) de una mascota
  turnos/
    listar.php          Lista turnos con filtros
    crear.php           Valida y crea un turno
    editar.php          Actualiza un turno
    eliminar.php        Cancela un turno
assets/
  css/                  Estilos específicos de cada página
  js/                   Lógica del lado del cliente para clientes, mascotas y turnos
```

## Cómo funciona

1. `index.php` recibe el parámetro `?pages=` (por ejemplo `?pages=clientes`) y carga la vista correspondiente de `pages/`.
2. Cada vista incluye `includes/header.php` y `includes/footer.php` para mantener el mismo diseño y menú.
3. Las vistas no consultan la base directamente: el JavaScript de `assets/js/` llama a los archivos de `api/`, que ejecutan las consultas en MySQL y devuelven el resultado.
4. Las bajas son **lógicas**: el registro se desactiva en lugar de borrarse, para conservar el historial.

## Instalación local

1. Instalar [XAMPP](https://www.apachefriends.org/) e iniciar **Apache** y **MySQL**.
2. Copiar la carpeta del proyecto en `C:\xampp\htdocs\`.
3. Crear la base de datos en phpMyAdmin e importar el script SQL del proyecto.
4. Configurar las credenciales de conexión a MySQL en el archivo de conexión del proyecto.
5. Abrir en el navegador: `http://localhost/NOMBRE_DE_LA_CARPETA/`

## Rutas principales

| Página  | URL                 |
|---------|---------------------|
| Inicio  | `?pages=inicio`     |
| Clientes| `?pages=clientes`   |
| Mascotas| `?pages=mascotas`   |
| Turnos  | `?pages=turnos`     |

## Seguridad

- Consultas con sentencias preparadas (PDO) para evitar inyección SQL.
- Salida escapada con `htmlspecialchars()` en las vistas.
- Validación de datos en el servidor además de la del navegador.

## Autor

Proyecto desarrollado por Liliana F.