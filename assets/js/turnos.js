const API_TURNOS = "api/turnos/";
const API_MASCOTAS = "api/mascotas/";

let turnosRegistrados = [];
let mascotasDisponibles = [];
let cargaMascotas;

let elementosTurnos = {};

function inicializarTurnos() {
    if (window.turnosInicializado) return;

    const ids = [
        "btnNuevoTurno",
        "btnCerrarModalTurno",
        "btnCancelarFormularioTurno",
        "modalTurno",
        "formTurno",
        "id_turno",
        "id_mascota",
        "fecha_hora",
        "motivo",
        "estado",
        "observaciones",
        "tituloModalTurno",
        "tablaTurnos",
        "mensajeTurnos",
        "buscarTurno",
        "filtroEstado",
        "btnGuardarTurno"
    ];

    const elementos = {};

    for (const id of ids) {
        elementos[id] = document.getElementById(id);
    }

    // Si todavía no está cargada la página del módulo, no inicializamos.
    if (ids.some(id => !elementos[id])) return;

    elementosTurnos = elementos;
    window.turnosInicializado = true;

    elementos.btnNuevoTurno.addEventListener("click", nuevoTurno);
    elementos.btnCerrarModalTurno.addEventListener("click", cerrarModal);
    elementos.btnCancelarFormularioTurno.addEventListener("click", cerrarModal);

    elementos.formTurno.addEventListener("submit", guardarTurno);

    elementos.buscarTurno.addEventListener("input", renderizarTurnos);
    elementos.filtroEstado.addEventListener("change", renderizarTurnos);

    elementos.tablaTurnos.addEventListener("click", manejarAccionesTabla);

   cargaMascotas = cargarMascotas();
   cargarTurnos();
}

// Permite inicializar tanto si la página carga normalmente como si
// este archivo se incorpora después de que el documento ya cargó.
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", inicializarTurnos);
} else {
    inicializarTurnos();
}


// --------------------------------------------------
// FUNCIONES GENERALES
// --------------------------------------------------

async function solicitarJSON(url, opciones = {}) {
    const respuesta = await fetch(url, opciones);

    let datos;

    try {
        datos = await respuesta.json();
    } catch {
        throw new Error("El servidor no devolvió una respuesta válida.");
    }

    if (!respuesta.ok || datos.success === false) {
        throw new Error(
            datos.mensaje || "Ocurrió un error al comunicarse con el servidor."
        );
    }

    return datos;
}

function mostrarMensaje(texto, tipo = "exito") {
    const mensaje = elementosTurnos.mensajeTurnos;

    mensaje.textContent = texto;
    mensaje.classList.remove("exito", "error");
    mensaje.classList.add(tipo);

    mensaje.style.display = "block";
}

function limpiarMensaje() {
    const mensaje = elementosTurnos.mensajeTurnos;

    mensaje.textContent = "";
    mensaje.classList.remove("exito", "error");
    mensaje.style.display = "none";
}

function escaparFecha(fecha) {
    if (!fecha) return "Sin fecha";

    const fechaTexto = String(fecha).replace("T", " ");
    const partes = fechaTexto.split(" ");

    if (partes.length < 2) return fechaTexto;

    const [anio, mes, dia] = partes[0].split("-");
    const hora = partes[1].slice(0, 5);

    if (!anio || !mes || !dia) return fechaTexto;

    return `${dia}/${mes}/${anio} ${hora}`;
}

function crearCelda(texto) {
    const celda = document.createElement("td");
    celda.textContent = texto ?? "";
    return celda;
}

function crearBoton(texto, clase, accion, id, deshabilitado = false) {
    const boton = document.createElement("button");

    boton.type = "button";
    boton.textContent = texto;
    boton.className = clase;
    boton.dataset.accion = accion;
    boton.dataset.id = id;
    boton.disabled = deshabilitado;

    return boton;
}


// --------------------------------------------------
// CARGAR MASCOTAS
// --------------------------------------------------

async function cargarMascotas() {
    const selector = elementosTurnos.id_mascota;

    selector.replaceChildren();

    const opcionInicial = document.createElement("option");
    opcionInicial.value = "";
    opcionInicial.textContent = "Cargando mascotas...";
    selector.appendChild(opcionInicial);

    try {
        const datos = await solicitarJSON(
            `${API_MASCOTAS}listar.php`
        );

        mascotasDisponibles = Array.isArray(datos.mascotas)
            ? datos.mascotas
            : [];

        selector.replaceChildren();

        const opcion = document.createElement("option");
        opcion.value = "";
        opcion.textContent = "Seleccioná una mascota";
        selector.appendChild(opcion);

        mascotasDisponibles.forEach(mascota => {
            // Si la API devuelve el campo activo, no ofrecemos mascotas
            // desactivadas. Si no lo devuelve, se conserva el registro.
            if (
                mascota.activo !== undefined &&
                Number(mascota.activo) !== 1
            ) {
                return;
            }

            const opcionMascota = document.createElement("option");
            opcionMascota.value = mascota.id_mascota;

            const especie = mascota.especie
                ? ` - ${mascota.especie}`
                : "";

            opcionMascota.textContent =
                `${mascota.nombre}${especie} (ID ${mascota.id_mascota})`;

            selector.appendChild(opcionMascota);
        });

        if (selector.options.length === 1) {
            selector.options[0].textContent = "No hay mascotas disponibles";
        }
    } catch (error) {
        selector.replaceChildren();

        const opcionError = document.createElement("option");
        opcionError.value = "";
        opcionError.textContent = "No se pudieron cargar las mascotas";

        selector.appendChild(opcionError);

        mostrarMensaje(error.message, "error");
    }
}


// --------------------------------------------------
// LISTAR TURNOS
// --------------------------------------------------

async function cargarTurnos() {
    elementosTurnos.tablaTurnos.replaceChildren();

    const filaCarga = document.createElement("tr");
    const celdaCarga = document.createElement("td");

    celdaCarga.colSpan = 7;
    celdaCarga.textContent = "Cargando turnos...";

    filaCarga.appendChild(celdaCarga);
    elementosTurnos.tablaTurnos.appendChild(filaCarga);

    try {
        const datos = await solicitarJSON(
            `${API_TURNOS}listar.php`
        );

        turnosRegistrados = Array.isArray(datos.turnos)
            ? datos.turnos
            : [];

        renderizarTurnos();
    } catch (error) {
        elementosTurnos.tablaTurnos.replaceChildren();

        const fila = document.createElement("tr");
        const celda = document.createElement("td");

        celda.colSpan = 7;
        celda.textContent = "No se pudieron cargar los turnos.";

        fila.appendChild(celda);
        elementosTurnos.tablaTurnos.appendChild(fila);

        mostrarMensaje(error.message, "error");
    }
}


// --------------------------------------------------
// MOSTRAR Y FILTRAR TURNOS
// --------------------------------------------------

function renderizarTurnos() {
    const tabla = elementosTurnos.tablaTurnos;
    const busqueda = elementosTurnos.buscarTurno.value
        .trim()
        .toLowerCase();

    const estadoSeleccionado = elementosTurnos.filtroEstado.value;

    tabla.replaceChildren();

    const turnosFiltrados = turnosRegistrados.filter(turno => {
        const nombreCliente =
            `${turno.nombre_cliente || ""} ${turno.apellido_cliente || ""}`;

        const textoBusqueda = [
            turno.id_turno,
            turno.nombre_mascota,
            turno.especie,
            nombreCliente,
            turno.motivo,
            turno.fecha_hora,
            turno.estado
        ].join(" ").toLowerCase();

        const coincideBusqueda = textoBusqueda.includes(busqueda);

        const coincideEstado = !estadoSeleccionado ||
            turno.estado === estadoSeleccionado;

        return coincideBusqueda && coincideEstado;
    });

    if (turnosFiltrados.length === 0) {
        const fila = document.createElement("tr");
        const celda = document.createElement("td");

        celda.colSpan = 7;
        celda.textContent = "No se encontraron turnos.";

        fila.appendChild(celda);
        tabla.appendChild(fila);

        return;
    }

    turnosFiltrados.forEach(turno => {
        const fila = document.createElement("tr");

        fila.appendChild(crearCelda(turno.id_turno));
        fila.appendChild(crearCelda(
            `${turno.nombre_mascota || "Sin nombre"}${turno.especie ? " (" + turno.especie + ")" : ""}`
        ));

        fila.appendChild(crearCelda(
            `${turno.nombre_cliente || ""} ${turno.apellido_cliente || ""}`.trim()
                || "Sin cliente"
        ));

        fila.appendChild(crearCelda(escaparFecha(turno.fecha_hora)));
        fila.appendChild(crearCelda(turno.motivo));

        const celdaEstado = document.createElement("td");
        const etiquetaEstado = document.createElement("span");

        etiquetaEstado.className =
            `estado-turno estado-${turno.estado || "pendiente"}`;

        etiquetaEstado.textContent = capitalizar(turno.estado || "pendiente");

        celdaEstado.appendChild(etiquetaEstado);
        fila.appendChild(celdaEstado);

        const celdaAcciones = document.createElement("td");
        const contenedorAcciones = document.createElement("div");

        contenedorAcciones.className = "acciones-turno";

        const estaCancelado = turno.estado === "cancelado";

        contenedorAcciones.appendChild(
            crearBoton(
                "Editar",
                "btn-editar-turno",
                "editar",
                turno.id_turno,
                estaCancelado
            )
        );

        contenedorAcciones.appendChild(
            crearBoton(
                estaCancelado ? "Cancelado" : "Cancelar",
                "btn-eliminar-turno",
                "cancelar",
                turno.id_turno,
                estaCancelado
            )
        );

        celdaAcciones.appendChild(contenedorAcciones);
        fila.appendChild(celdaAcciones);

        tabla.appendChild(fila);
    });
}

function capitalizar(texto) {
    if (!texto) return "";
    return texto.charAt(0).toUpperCase() + texto.slice(1);
}


// --------------------------------------------------
// NUEVO TURNO
// --------------------------------------------------

async function nuevoTurno() {
    limpiarMensaje();

    elementosTurnos.formTurno.reset();
    elementosTurnos.id_turno.value = "";
    elementosTurnos.estado.value = "pendiente";
    elementosTurnos.tituloModalTurno.textContent = "Registrar turno";
    elementosTurnos.btnGuardarTurno.textContent = "Guardar turno";

    await cargarMascotas();

    abrirModal();
}


// --------------------------------------------------
// EDITAR TURNO
// --------------------------------------------------

    async function editarTurno(id) {
     if (cargaMascotas)  await cargaMascotas;

     limpiarMensaje();
   

    const turno = turnosRegistrados.find(
        elemento => String(elemento.id_turno) === String(id)
    );

    if (!turno) {
        mostrarMensaje("No se encontró el turno seleccionado.", "error");
        return;
    }

    if (turno.estado === "cancelado") {
        mostrarMensaje("No se pueden editar turnos cancelados.", "error");
        return;
    }

    elementosTurnos.formTurno.reset();

    elementosTurnos.id_turno.value = turno.id_turno;
    elementosTurnos.fecha_hora.value =
        String(turno.fecha_hora || "").replace(" ", "T").slice(0, 16);

    elementosTurnos.motivo.value = turno.motivo || "";
    elementosTurnos.estado.value = turno.estado || "pendiente";
    elementosTurnos.observaciones.value = turno.observaciones || "";

    elementosTurnos.tituloModalTurno.textContent = "Editar turno";
    elementosTurnos.btnGuardarTurno.textContent = "Guardar cambios";

    // Si la mascota ya no aparece en la lista, la agregamos temporalmente
    // para poder visualizar el turno y conservar su selección.
    const selector = elementosTurnos.id_mascota;
    const idMascota = String(turno.id_mascota);

    if (![...selector.options].some(op => op.value === idMascota)) {
        const opcion = document.createElement("option");
        opcion.value = idMascota;
        opcion.textContent = turno.nombre_mascota || `Mascota ${idMascota}`;
        selector.appendChild(opcion);
    }

    selector.value = idMascota;

    abrirModal();
}


// --------------------------------------------------
// GUARDAR TURNO NUEVO O EDITADO
// --------------------------------------------------

async function guardarTurno(evento) {
    evento.preventDefault();
    limpiarMensaje();

    const formulario = elementosTurnos.formTurno;
    const idTurno = elementosTurnos.id_turno.value;

    const endpoint = idTurno ? "editar.php" : "crear.php";

    const botonGuardar = elementosTurnos.btnGuardarTurno;

    botonGuardar.disabled = true;
    botonGuardar.textContent = "Guardando...";

    try {
        const datosFormulario = new FormData(formulario);
        const datos = new URLSearchParams();

        for (const [clave, valor] of datosFormulario.entries()) {
            datos.append(clave, valor);
        }

        const respuesta = await solicitarJSON(
            `${API_TURNOS}${endpoint}`,
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
                },
                body: datos.toString()
            }
        );

        cerrarModal();

        mostrarMensaje(
            respuesta.mensaje ||
                (idTurno
                    ? "El turno se actualizó correctamente."
                    : "El turno se registró correctamente."),
            "exito"
        );

        await cargarTurnos();
    } catch (error) {
        mostrarMensaje(error.message, "error");
    } finally {
        botonGuardar.disabled = false;

        botonGuardar.textContent =
            elementosTurnos.id_turno.value
                ? "Guardar cambios"
                : "Guardar turno";
    }
}


// --------------------------------------------------
// CANCELAR TURNO
// --------------------------------------------------

async function cancelarTurno(id) {
    const turno = turnosRegistrados.find(
        elemento => String(elemento.id_turno) === String(id)
    );

    if (!turno) {
        mostrarMensaje("No se encontró el turno seleccionado.", "error");
        return;
    }

    if (turno.estado === "cancelado") {
        mostrarMensaje("Este turno ya está cancelado.", "error");
        return;
    }

    const confirmar = window.confirm(
        `¿Querés cancelar el turno de ${turno.nombre_mascota || "la mascota"}?`
    );

    if (!confirmar) return;

    limpiarMensaje();

    try {
        const datos = new URLSearchParams();
        datos.append("id_turno", id);

        const respuesta = await solicitarJSON(
            `${API_TURNOS}eliminar.php`,
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
                },
                body: datos.toString()
            }
        );

        mostrarMensaje(
            respuesta.mensaje || "El turno se canceló correctamente.",
            "exito"
        );

        await cargarTurnos();
    } catch (error) {
        mostrarMensaje(error.message, "error");
    }
}


// --------------------------------------------------
// ACCIONES DE LA TABLA
// --------------------------------------------------

function manejarAccionesTabla(evento) {
    const boton = evento.target.closest("button[data-accion]");

    if (!boton || boton.disabled) return;

    const id = boton.dataset.id;

    if (boton.dataset.accion === "editar") {
        editarTurno(id);
    }

    if (boton.dataset.accion === "cancelar") {
        cancelarTurno(id);
    }
}


// --------------------------------------------------
// MODAL
// --------------------------------------------------

function abrirModal() {
    const modal = elementosTurnos.modalTurno;

    if (typeof modal.showModal === "function") {
        if (!modal.open) modal.showModal();
    } else {
        modal.setAttribute("open", "");
    }
}

function cerrarModal() {
    const modal = elementosTurnos.modalTurno;

    if (typeof modal.close === "function" && modal.open) {
        modal.close();
    } else {
        modal.removeAttribute("open");
    }

    elementosTurnos.formTurno.reset();
    elementosTurnos.id_turno.value = "";
}
