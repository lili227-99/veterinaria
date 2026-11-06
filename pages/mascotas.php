<?php
$pages = 'mascotas';
?>
<div class="contenedor">
    <header class="encabezado">
        <div>
            <h1>Gestión de mascotas</h1>
            <p>Administrá las mascotas y sus propietarios.</p>
        </div>

        <button type="button" id="btn-NuevaMascota" class="btn-principal">
            + Nueva mascota
        </button>
    </header>

    <section class="panel">
        <div id="mensaje" class="mensaje" role="status"
             aria-live="polite"></div>

        <input
            type="search"
            id="buscar"
            class="barra-busqueda"
            placeholder="Buscar por mascota, especie o dueño..."
            aria-label="Buscar mascotas"
        >

        <div class="tabla-contenedor">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Propietario</th>
                        <th>Mascota</th>
                        <th>Especie</th>
                        <th>Raza</th>
                        <th>Sexo</th>
                        <th>Fecha de nacimiento</th>
                        <th>Peso (kg)</th>
                        <th>Color</th>
                        <th>Observaciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody id="tablaMascotas">
                    <tr>
                        <td colspan="11" class="sin-resultados">
                            Cargando mascotas...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <dialog id="modalMascota" class="modal" aria-labelledby="tituloFormulario">
        <div class="modal-contenido" role="dialog" aria-modal="true">
            <div class="modal-header">
                <h2 id="tituloFormulario">Nueva mascota</h2>
                <button type="button" class="btn-cerrar-formulario" id="btnCerrarMascota" aria-label="Cerrar formulario">
                    &times;
                </button>
            </div>

            <form id="formMascota">
                <input type="hidden" id="id_mascota" name="id_mascota">

                <div class="form-grid">
                    <div class="campo completo">
                        <label for="id_cliente">Propietario *</label>
                        <select id="id_cliente" name="id_cliente" required>
                            <option value="">Cargando clientes...</option>
                        </select>
                    </div>

                    <div class="campo">
                        <label for="nombre">Nombre *</label>
                        <input type="text" id="nombre" name="nombre" maxlength="80" required>
                    </div>

                    <div class="campo">
                        <label for="especie">Especie *</label>
                        <select id="especie" name="especie" required>
                            <option value="">Seleccioná una especie</option>
                            <option value="Perro">Perro</option>
                            <option value="Gato">Gato</option>
                            <option value="Ave">Ave</option>
                            <option value="Conejo">Conejo</option>
                            <option value="Roedor">Roedor</option>
                            <option value="Reptil">Reptil</option>
                            <option value="Otra">Otra</option>
                        </select>
                    </div>

                    <div class="campo">
                        <label for="raza">Raza</label>
                        <input type="text" id="raza" name="raza" maxlength="80">
                    </div>

                    <div class="campo">
                        <label for="sexo">Sexo</label>
                        <select id="sexo" name="sexo">
                            <option value="">No especificado</option>
                            <option value="macho">Macho</option>
                            <option value="hembra">Hembra</option>
                        </select>
                    </div>

                    <div class="campo">
                        <label for="fecha_nacimiento">Fecha de nacimiento</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento">
                    </div>

                    <div class="campo">
                        <label for="peso">Peso (kg)</label>
                        <input type="number" id="peso" name="peso" step="0.01" min="0">
                    </div>

                    <div class="campo">
                        <label for="color">Color</label>
                        <input type="text" id="color" name="color" maxlength="60">
                    </div>

                    <div class="campo completo">
                        <label for="observaciones">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-botones">
                    <button type="button" id="btnCancelar" class="btn-secundario">
                        Cancelar
                    </button>
                    <button type="submit" id="btnGuardar" class="btn-principal">
                        Guardar mascota
                    </button>
                </div>
            </form>
        </div>
    </dialog>
</div>