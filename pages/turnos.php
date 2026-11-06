<section class="turnos-contenedor">

    <div class="turnos-encabezado">
        <div>
            <h1>Gestión de turnos</h1>
            <p>Administrá los turnos y las citas de las mascotas.</p>
        </div>

        <button type="button" id="btnNuevoTurno" class="btn-nuevo-turno">
            + Nuevo turno
        </button>
    </div>

    <div id="mensajeTurnos" class="mensaje-turnos" role="status"
         aria-live="polite"></div>

    <section class="turnos-panel">

        <div class="turnos-panel-encabezado">
            <div>
                <h2>Turnos registrados</h2>
                <p>Consultá, modificá o cancelá los turnos.</p>
            </div>

            <div class="turnos-filtros">
                <input
                    type="search"
                    id="buscarTurno"
                    placeholder="Buscar mascota, cliente o motivo..."
                    aria-label="Buscar turnos"
                >

                <select id="filtroEstado" aria-label="Filtrar por estado">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="confirmado">Confirmado</option>
                    <option value="atendido">Atendido</option>
                    <option value="cancelado">Cancelado</option>
                    <option value="ausente">Ausente</option>
                </select>
            </div>
        </div>

        <div class="tabla-turnos-contenedor">
            <table class="tabla-turnos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mascota</th>
                        <th>Cliente</th>
                        <th>Fecha y hora</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody id="tablaTurnos">
                    <tr>
                        <td colspan="7">Cargando turnos...</td>
                    </tr>
                </tbody>
            </table>
        </div>

    </section>

    <dialog id="modalTurno" class="modal-turno" aria-labelledby="tituloModalTurno">
        <div class="modal-contenido" role="dialog" aria-modal="true">
            <div class="modal-turno-encabezado">
                <h2 id="tituloModalTurno">Registrar turno</h2>
                <button type="button" id="btnCerrarModalTurno" class="btn-cerrar-modal" aria-label="Cerrar formulario">
                    &times;
                </button>
            </div>

            <form id="formTurno">
                <input type="hidden" id="id_turno" name="id_turno">

                <div class="campo-turno">
                    <label for="id_mascota">Mascota *</label>
                    <select id="id_mascota" name="id_mascota" required>
                        <option value="">Seleccioná una mascota</option>
                    </select>
                </div>

                <div class="campo-turno">
                    <label for="fecha_hora">Fecha y hora *</label>
                    <input type="datetime-local" id="fecha_hora" name="fecha_hora" required>
                </div>

                <div class="campo-turno">
                    <label for="motivo">Motivo *</label>
                    <textarea id="motivo" name="motivo" rows="3" required></textarea>
                </div>

                <div class="campo-turno">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="pendiente">Pendiente</option>
                        <option value="confirmado">Confirmado</option>
                        <option value="atendido">Atendido</option>
                        <option value="cancelado">Cancelado</option>
                        <option value="ausente">Ausente</option>
                    </select>
                </div>

                <div class="campo-turno">
                    <label for="observaciones">Observaciones</label>
                    <textarea id="observaciones" name="observaciones" rows="2"></textarea>
                </div>

                <div class="modal-botones-turno">
                    <button type="button" id="btnCancelarFormularioTurno" class="btn-secundario">
                        Cancelar
                    </button>
                    <button type="submit" id="btnGuardarTurno" class="btn-principal">
                        Guardar turno
                    </button>
                </div>
            </form>
        </div>
    </dialog>

</section>

