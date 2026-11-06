
"use strict";

// ========================================
// CONFIGURACIÓN
// ========================================

const API_CLIENTES = "api/clientes/";

const tablaClientes = document.getElementById("tablaClientes");
const campoBuscar = document.getElementById("buscar");
const modalCliente = document.getElementById("modalCliente");
const formularioCliente = document.getElementById("formCliente");
const mensajeCliente = document.getElementById("mensaje");
const botonGuardar = document.getElementById("btnGuardar");

let clientes = [];

// ========================================
// MENSAJES
// ========================================

function mostrarMensaje(texto, tipo = "ok") {
    mensajeCliente.textContent = texto;
    mensajeCliente.className = "mensaje " + tipo;
}

function ocultarMensaje() {
    mensajeCliente.textContent = "";
    mensajeCliente.className = "mensaje";
}

// ========================================
// COMUNICACIÓN CON LA API
// ========================================

async function pedirJSON(url, opciones = {}) {
    const respuesta = await fetch(url, opciones);

    let datos;

    try {
        datos = await respuesta.json();
    } catch {
        throw new Error(
            "El servidor no devolvió un JSON válido. Revisá el archivo PHP."
        );
    }

    if (!respuesta.ok || datos.success === false) {
        throw new Error(
            datos.mensaje || "Ocurrió un error en la solicitud."
        );
    }

    return datos;
}

// ========================================
// CARGAR CLIENTES
// ========================================

async function cargarClientes() {
    tablaClientes.innerHTML = `
        <tr>
            <td colspan="7" class="sin-resultados">
                Cargando clientes...
            </td>
        </tr>
`;

    try {
        const datos = await pedirJSON(
            API_CLIENTES + "listar.php"
        );

        clientes = datos.clientes || [];

        mostrarClientes();

    } catch (error) {
        tablaClientes.innerHTML = `
            <tr>
                <td colspan="7" class="sin-resultados">
                    No se pudieron cargar los clientes.
                </td>
            </tr>
        `;

        mostrarMensaje(error.message, "error");
    }
}

// ========================================
// CREAR CELDAS SEGURAS PARA LA TABLA
// ========================================

function crearCelda(texto) {
    const celda = document.createElement("td");
    celda.textContent = texto ?? "";
    return celda;
}

// ========================================
// MOSTRAR Y BUSCAR CLIENTES
// ========================================

function mostrarClientes() {
    const termino = campoBuscar.value
        .trim()
        .toLowerCase();

    const clientesFiltrados = clientes.filter(cliente => {
        const texto = [
            cliente.nombre,
            cliente.apellido,
            cliente.dni
        ].join(" ").toLowerCase();

        return texto.includes(termino);
    });

    tablaClientes.replaceChildren();

    if (clientesFiltrados.length === 0) {
        const fila = document.createElement("tr");
        const celda = crearCelda(
            termino
                ? "No se encontraron clientes."
                : "Todavía no hay clientes registrados."
        );

        celda.colSpan = 7;
        celda.className = "sin-resultados";

        fila.appendChild(celda);
        tablaClientes.appendChild(fila);

        return;
    }

    clientesFiltrados.forEach(cliente => {
        const fila = document.createElement("tr");

        fila.appendChild(
            crearCelda(cliente.id_cliente)
        );

        fila.appendChild(
            crearCelda(
                `${cliente.nombre} ${cliente.apellido}`
            )
        );

        fila.appendChild(
            crearCelda(cliente.dni || "—")
        );

        fila.appendChild(
            crearCelda(cliente.telefono || "—")
        );

        fila.appendChild(
            crearCelda(cliente.email || "—")
        );
        fila.appendChild(crearCelda(cliente.direccion || "—"));

        const acciones = document.createElement("td");

        // Botón editar
        const botonEditar = document.createElement("button");

        botonEditar.type = "button";
        botonEditar.className = "btn-editar";
        botonEditar.textContent = "Editar";

        botonEditar.addEventListener("click", () => {
            editarCliente(cliente);
        });

        // Botón desactivar
        const botonDesactivar = document.createElement("button");

        botonDesactivar.type = "button";
        botonDesactivar.className = "btn-eliminar";
        botonDesactivar.textContent = "Desactivar";

        botonDesactivar.addEventListener("click", () => {
            desactivarCliente(cliente);
        });

        acciones.append(botonEditar, botonDesactivar);
        fila.appendChild(acciones);

        tablaClientes.appendChild(fila);
    });
}

// ========================================
// ABRIR FORMULARIO PARA NUEVO CLIENTE
// ========================================

function abrirNuevoCliente() {
    formularioCliente.reset();

    document.getElementById("id_cliente").value = "";

    document.getElementById("tituloFormulario").textContent =
        "Nuevo cliente";

    botonGuardar.textContent = "Guardar cliente";

    ocultarMensaje();

    modalCliente.showModal();
}

// ========================================
// ABRIR FORMULARIO PARA EDITAR
// ========================================

function editarCliente(cliente) {
    formularioCliente.reset();

    document.getElementById("id_cliente").value =
        cliente.id_cliente;

    document.getElementById("nombre").value =
        cliente.nombre || "";

    document.getElementById("apellido").value =
        cliente.apellido || "";

    document.getElementById("dni").value =
        cliente.dni || "";

    document.getElementById("telefono").value =
        cliente.telefono || "";

    document.getElementById("email").value =
        cliente.email || "";

    document.getElementById("direccion").value =
        cliente.direccion || "";

    document.getElementById("tituloFormulario").textContent =
        "Editar cliente";

    botonGuardar.textContent = "Guardar cambios";

    ocultarMensaje();

    modalCliente.showModal();
}

// ========================================
// REGISTRAR O ACTUALIZAR CLIENTE
// ========================================

formularioCliente.addEventListener("submit", async evento => {
    evento.preventDefault();

    const id = document.getElementById("id_cliente").value;

    const endpoint = id
        ? "editar.php"
        : "crear.php";

    const datos = new URLSearchParams(
        new FormData(formularioCliente)
    );

    botonGuardar.disabled = true;
    botonGuardar.textContent = "Guardando...";

    try {
        const resultado = await pedirJSON(
            API_CLIENTES + endpoint,
            {
                method: "POST",
                headers: {
                    "Content-Type":
                        "application/x-www-form-urlencoded;charset=UTF-8"
                },
                body: datos
            }
        );

        modalCliente.close();
        formularioCliente.reset();

        await cargarClientes();

        mostrarMensaje(
            resultado.mensaje ||
                (id
                    ? "Cliente actualizado correctamente."
                    : "Cliente registrado correctamente."),
            "ok"
        );

    } catch (error) {
        mostrarMensaje(error.message, "error");

    } finally {
        botonGuardar.disabled = false;

        botonGuardar.textContent =
            document.getElementById("id_cliente").value
                ? "Guardar cambios"
                : "Guardar cliente";
    }
});

// ========================================
// DESACTIVAR CLIENTE
// ========================================

async function desactivarCliente(cliente) {
    const nombre = `${cliente.nombre} ${cliente.apellido}`;

    const confirmar = confirm(
        `¿Querés desactivar a ${nombre}? ` +
        "Su historial se conservará."
    );

    if (!confirmar) {
        return;
    }

    const datos = new URLSearchParams();

    datos.set("id_cliente", cliente.id_cliente);

    try {
        const resultado = await pedirJSON(
            API_CLIENTES + "eliminar.php",
            {
                method: "POST",
                headers: {
                    "Content-Type":
                        "application/x-www-form-urlencoded;charset=UTF-8"
                },
                body: datos
            }
        );

        await cargarClientes();

        mostrarMensaje(
            resultado.mensaje ||
                "Cliente desactivado correctamente.",
            "ok"
        );

    } catch (error) {
        mostrarMensaje(error.message, "error");
    }
}

// ========================================
// EVENTOS DE LA PANTALLA
// ========================================

document.getElementById("btnNuevo").addEventListener(
    "click",
    abrirNuevoCliente
);

document.getElementById("btnCancelar").addEventListener(
    "click",
    () => modalCliente.close()
);

document.getElementById("btnCerrarCliente").addEventListener(
    "click",
    () => {
        formularioCliente.reset();
        if (typeof modalCliente.close === "function" && modalCliente.open) {
            modalCliente.close();
        }
    }
);

campoBuscar.addEventListener(
    "input",
    mostrarClientes
);

// ========================================
// INICIAR PANTALLA
// ========================================

cargarClientes();
