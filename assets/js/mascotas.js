
"use strict";

const API_MASCOTAS = "api/mascotas/";
const API_CLIENTES = "api/clientes/";

const btnNuevaMascota = document.getElementById("btn-NuevaMascota");
const buscar = document.getElementById("buscar");
const modal = document.getElementById("modalMascota");
const formulario = document.getElementById("formMascota");
const mensaje = document.getElementById("mensaje");
const selectorCliente = document.getElementById("id_cliente");
const botonGuardar = document.getElementById("btnGuardar");
const tabla = document.getElementById("tablaMascotas");

let mascotas = [];
let clientes = [];

function mostrarMensaje(texto, tipo = "ok") {
    mensaje.textContent = texto;
    mensaje.className = "mensaje " + tipo;
}

function ocultarMensaje() {
    mensaje.textContent = "";
    mensaje.className = "mensaje";
}

async function pedirJSON(url, opciones = {}) {
    const respuesta = await fetch(url, opciones);

    let datos;

    try {
        datos = await respuesta.json();
    } catch {
        throw new Error("El servidor no devolvió un JSON válido.");
    }

    if (!respuesta.ok || datos.success === false) {
        throw new Error(datos.mensaje || "Ocurrió un error.");
    }

    return datos;
}

// Cargar propietarios activos desde la API de clientes.
async function cargarClientes() {
    selectorCliente.replaceChildren();

    const opcionInicial = document.createElement("option");
    opcionInicial.value = "";
    opcionInicial.textContent = "Seleccioná un propietario";
    selectorCliente.appendChild(opcionInicial);

    const datos = await pedirJSON(API_CLIENTES + "listar.php");
    clientes = datos.clientes || [];

    clientes.forEach(cliente => {
        const opcion = document.createElement("option");
        opcion.value = cliente.id_cliente;
        opcion.textContent =
            `${cliente.nombre} ${cliente.apellido}` +
            (cliente.dni ? ` - DNI ${cliente.dni}` : "");

        selectorCliente.appendChild(opcion);
    });

    if (clientes.length === 0) {
        opcionInicial.textContent = "No hay clientes activos";
    }
}

// Cargar mascotas.
async function cargarMascotas() {
    tabla.innerHTML = `
        <tr><td colspan="11" class="sin-resultados">
            Cargando mascotas...
        </td></tr>`;

    try {
        const datos = await pedirJSON(API_MASCOTAS + "listar.php");
        mascotas = datos.mascotas || [];
        mostrarMascotas();
    } catch (error) {
        tabla.innerHTML = `
            <tr><td colspan="11" class="sin-resultados">
                No se pudieron cargar las mascotas.
            </td></tr>`;

        mostrarMensaje(error.message, "error");
    }
}

function crearCelda(texto) {
    const celda = document.createElement("td");
    celda.textContent = texto ?? "";
    return celda;
}

// Mostrar y filtrar la tabla.
function mostrarMascotas() {
    const termino = buscar.value.trim().toLowerCase();

    const filtradas = mascotas.filter(mascota => {
        const propietario =
            `${mascota.nombre_cliente || ""} ` +
            `${mascota.apellido_cliente || ""}`.trim();

        const texto = [
            mascota.nombre,
            mascota.especie,
            mascota.raza,
            propietario,
            mascota.color
        ].join(" ").toLowerCase();

        return texto.includes(termino);
    });

    tabla.replaceChildren();

    if (filtradas.length === 0) {
        const fila = document.createElement("tr");
        const celda = crearCelda(
            termino
                ? "No se encontraron mascotas."
                : "Todavía no hay mascotas registradas."
        );

        celda.colSpan = 11;
        celda.className = "sin-resultados";
        fila.appendChild(celda);
        tabla.appendChild(fila);
        return;
    }

    filtradas.forEach(mascota => {
        const fila = document.createElement("tr");

        fila.appendChild(crearCelda(mascota.id_mascota));
        fila.appendChild(crearCelda(
            `${mascota.nombre_cliente || ""} ` +
            `${mascota.apellido_cliente || ""}`.trim()
        ));
        fila.appendChild(crearCelda(mascota.nombre));
        fila.appendChild(crearCelda(mascota.especie));
        fila.appendChild(crearCelda(mascota.raza || "—"));

        const sexo = {
            macho: "Macho",
            hembra: "Hembra",
            no_especificado: "No especificado"
        };

        fila.appendChild(
            crearCelda(sexo[mascota.sexo] || "No especificado")
        );
        fila.appendChild(crearCelda(mascota.fecha_nacimiento || ""));

        fila.appendChild(crearCelda(
            mascota.peso !== null && mascota.peso !== ""
                ? `${mascota.peso} kg`
                : "—"
        ));

        fila.appendChild(crearCelda(mascota.color || ""));
        fila.appendChild(crearCelda(mascota.observaciones || ""));

        const acciones = document.createElement("td");

        const editar = document.createElement("button");
        editar.type = "button";
        editar.className = "btn-editar";
        editar.textContent = "Editar";
        editar.addEventListener("click", () => editarMascota(mascota));

        const desactivar = document.createElement("button");
        desactivar.type = "button";
        desactivar.className = "btn-eliminar";
        desactivar.textContent = "Desactivar";
        desactivar.addEventListener(
            "click",
            () => desactivarMascota(mascota)
        );

        acciones.append(editar, desactivar);
        fila.appendChild(acciones);
        tabla.appendChild(fila);
    });
}

// Abrir formulario nuevo.
async function abrirNuevaMascota() {
    formulario.reset();
    document.getElementById("id_mascota").value = "";

    document.getElementById("tituloFormulario").textContent =
        "Nueva mascota";

    botonGuardar.textContent = "Guardar mascota";
    ocultarMensaje();

    try {
        await cargarClientes();

        if (clientes.length === 0) {
            mostrarMensaje(
                "Primero tenés que registrar un cliente activo.",
                "error"
            );
            return;
        }

        modal.showModal();
    } catch (error) {
        mostrarMensaje(
            "No se pudieron cargar los propietarios: " + error.message,
            "error"
        );
    }
}

// Abrir formulario de edición.
async function editarMascota(mascota) {
    formulario.reset();
    ocultarMensaje();

    try {
        await cargarClientes();

        if (!clientes.some(c => String(c.id_cliente) ===
            String(mascota.id_cliente))) {
            throw new Error(
                "El propietario de esta mascota no está activo."
            );
        }

        document.getElementById("id_mascota").value =
            mascota.id_mascota;

        selectorCliente.value = mascota.id_cliente;
        document.getElementById("nombre").value =
            mascota.nombre || "";
        document.getElementById("especie").value =
            mascota.especie || "";
        document.getElementById("raza").value =
            mascota.raza || "";
        document.getElementById("sexo").value =
            mascota.sexo || "no_especificado";
        document.getElementById("fecha_nacimiento").value =
            mascota.fecha_nacimiento || "";
        document.getElementById("peso").value =
            mascota.peso ?? "";
        document.getElementById("color").value =
            mascota.color || "";
        document.getElementById("observaciones").value =
            mascota.observaciones || "";

        document.getElementById("tituloFormulario").textContent =
            "Editar mascota";

        botonGuardar.textContent = "Guardar cambios";
        modal.showModal();

    } catch (error) {
        mostrarMensaje(error.message, "error");
    }
}

// Guardar nueva mascota o cambios.
formulario.addEventListener("submit", async evento => {
    evento.preventDefault();

    const id = document.getElementById("id_mascota").value;
    const endpoint = id ? "editar.php" : "crear.php";

    const datos = new URLSearchParams(new FormData(formulario));

    botonGuardar.disabled = true;
    botonGuardar.textContent = "Guardando...";

    try {
        const resultado = await pedirJSON(
            API_MASCOTAS + endpoint,
            {
                method: "POST",
                headers: {
                    "Content-Type":
                        "application/x-www-form-urlencoded;charset=UTF-8"
                },
                body: datos
            }
        );

        modal.close();
        formulario.reset();

        await cargarMascotas();

        mostrarMensaje(
            resultado.mensaje ||
                (id
                    ? "Mascota actualizada correctamente."
                    : "Mascota registrada correctamente."),
            "ok"
        );

    } catch (error) {
        mostrarMensaje(error.message, "error");
    } finally {
        botonGuardar.disabled = false;
        botonGuardar.textContent =
            document.getElementById("id_mascota").value
                ? "Guardar cambios"
                : "Guardar mascota";
    }
});

// Desactivar mascota sin borrarla físicamente.
async function desactivarMascota(mascota) {
    const confirmar = confirm(
        `¿Querés desactivar a ${mascota.nombre}? ` +
        "Su historial se conservará."
    );

    if (!confirmar) return;

    const datos = new URLSearchParams();
    datos.set("id_mascota", mascota.id_mascota);

    try {
        const resultado = await pedirJSON(
            API_MASCOTAS + "eliminar.php",
            {
                method: "POST",
                headers: {
                    "Content-Type":
                        "application/x-www-form-urlencoded;charset=UTF-8"
                },
                body: datos
            }
        );

        await cargarMascotas();

        mostrarMensaje(
            resultado.mensaje || "Mascota desactivada correctamente.",
            "ok"
        );
    } catch (error) {
        mostrarMensaje(error.message, "error");
    }
}

// Eventos.
btnNuevaMascota.addEventListener(
    "click",
    abrirNuevaMascota
);

document.getElementById("btnCancelar").addEventListener(
    "click",
    () => modal.close()
);

document.getElementById("btnCerrarMascota").addEventListener(
    "click",
    () => {
        formulario.reset();
        if (typeof modal.close === "function" && modal.open) {
            modal.close();
        }
    }
);

buscar.addEventListener("input", mostrarMascotas);

// Iniciar pantalla.
cargarMascotas();
