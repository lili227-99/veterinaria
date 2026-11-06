<?php
$pages = 'clientes'; // Establece la página actual para resaltar el menú
?>
<div class="contenedor">

    <header class="encabezado">
        <div>
            <h1>Gestión de clientes</h1>
            <p>Administrá los clientes de tu veterinaria.</p>
        </div>

        <button class="btn-principal" id="btnNuevo">
            + Nuevo cliente
        </button>
    </header>

    <section class="panel">
        <div id="mensaje" class="mensaje" role="status"
             aria-live="polite"></div>

        <input
            type="search"
            id="buscar"
            class="barra-busqueda"
            placeholder="Buscar por nombre, apellido o DNI..."
            aria-label="Buscar clientes"
        >

        <div class="tabla-contenedor">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre y apellido</th>
                        <th>DNI</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Dirección</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody id="tablaClientes">
                    <tr>
                        <td colspan="7" class="sin-resultados">
                            Cargando clientes...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <dialog id="modalCliente" class="modalCliente"
            aria-labelledby="tituloFormulario">
        <div class="modal-contenido" role="dialog" aria-modal="true">
            <div class="modal-header">
                <h2 id="tituloFormulario">Nuevo cliente</h2>
                <button type="button" class="btn-cerrar-formulario" id="btnCerrarCliente" aria-label="Cerrar formulario">
                    &times;
                </button>
            </div>

            <form id="formCliente">
                <input type="hidden" name="id_cliente" id="id_cliente">

                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" required>

                <label for="apellido">Apellido:</label>
                <input type="text" name="apellido" id="apellido" required>

                <label for="dni">DNI:</label>
                <input type="text" name="dni" id="dni" required>

                <label for="telefono">Teléfono:</label>
                <input type="text" name="telefono" id="telefono">

                <label for="email">Email:</label>
                <input type="email" name="email" id="email">

                <label for="direccion">Dirección:</label>
                <input type="text" name="direccion" id="direccion">

                <div class="modal-botones">
                    <button type="submit" class="btnGuardar" id="btnGuardar">
                        Guardar
                    </button>
                    <button type="button" class="btn-secundario"
                            id="btnCancelar">
                        Cancelar
                    </button>
                </div>
            </form>
        </div> <!-- cierra .modal-contenido -->
    </dialog>
</div> <!-- cierra .contenedor -->




