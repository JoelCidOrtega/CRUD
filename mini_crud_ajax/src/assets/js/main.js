// -----------------------------------------------------------------------------
// Mini CRUD AJAX — Lado cliente (sin librerías)
// Archivo: /assets/js/main.js
// -----------------------------------------------------------------------------

/** URL absoluta o relativa del endpoint PHP (API del servidor) */
const URL_API_SERVIDOR = '/api.php';

/** Elementos de la interfaz que necesitamos manipular */
const nodoCuerpoTablaUsuarios = document.getElementById('tbody'); // <tbody> del listado
const nodoFilaEstadoVacio = document.getElementById('fila-estado-vacio');
const formularioAltaUsuario = document.getElementById('formCreate');
const nodoZonaMensajesEstado = document.getElementById('msg');
const nodoBotonAgregarUsuario = document.getElementById('boton-agregar-usuario');
const nodoIndicadorCargando = document.getElementById('indicador-cargando');

// -----------------------------------------------------------------------------
// Mensajes de estado
// -----------------------------------------------------------------------------
function mostrarMensajeDeEstado(tipoEstado, textoMensaje) {
    nodoZonaMensajesEstado.className = tipoEstado;
    nodoZonaMensajesEstado.textContent = textoMensaje;

    if (tipoEstado !== '') {
        setTimeout(() => {
            nodoZonaMensajesEstado.className = '';
            nodoZonaMensajesEstado.textContent = '';
        }, 2000);
    }
}

// -----------------------------------------------------------------------------
// Indicador de carga
// -----------------------------------------------------------------------------
function activarEstadoCargando() {
    if (nodoBotonAgregarUsuario) nodoBotonAgregarUsuario.disabled = true;
    if (nodoIndicadorCargando) nodoIndicadorCargando.hidden = false;
}
function desactivarEstadoCargando() {
    if (nodoBotonAgregarUsuario) nodoBotonAgregarUsuario.disabled = false;
    if (nodoIndicadorCargando) nodoIndicadorCargando.hidden = true;
}

// -----------------------------------------------------------------------------
// Sanitización
// -----------------------------------------------------------------------------
function convertirATextoSeguro(entradaPosiblementePeligrosa) {
    return String(entradaPosiblementePeligrosa)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

// -----------------------------------------------------------------------------
// RENDERIZADO DE TABLA
// -----------------------------------------------------------------------------
function renderizarTablaDeUsuarios(arrayUsuarios) {

    nodoCuerpoTablaUsuarios.innerHTML = '';

    if (!Array.isArray(arrayUsuarios) || arrayUsuarios.length === 0) {
        nodoFilaEstadoVacio.hidden = false;
        return;
    }

    nodoFilaEstadoVacio.hidden = true;

    arrayUsuarios.forEach((usuario, posicionEnLista) => {
        const nodoFila = document.createElement('tr');

        nodoFila.innerHTML = `
            <td>${posicionEnLista + 1}</td>
            <td>${convertirATextoSeguro(usuario?.nombre ?? '')}</td>
            <td>${convertirATextoSeguro(usuario?.contrasena ?? '')}</td>
            <td>${convertirATextoSeguro(usuario?.email ?? '')}</td>
            <td>${convertirATextoSeguro(usuario?.rol ?? '')}</td>
            <td>
                <button
                    type="button"
                    data-accion="editar"
                    data-posicion="${posicionEnLista}"
                    aria-label="Editar usuario ${posicionEnLista + 1}"
                    style="margin-right: 0.5rem;">
                    Editar
                </button>
                <button
                    type="button"
                    data-accion="eliminar"
                    data-posicion="${posicionEnLista}"
                    aria-label="Eliminar usuario ${posicionEnLista + 1}">
                    Eliminar
                </button>
            </td>
        `;

        nodoCuerpoTablaUsuarios.appendChild(nodoFila);
    });
}

// -----------------------------------------------------------------------------
// Cargar listado inicial
// -----------------------------------------------------------------------------
async function obtenerYMostrarListadoDeUsuarios() {
    try {
        const respuestaHttp = await fetch(`${URL_API_SERVIDOR}?action=list`);
        const cuerpoJson = await respuestaHttp.json();

        if (!cuerpoJson.ok) {
            throw new Error(cuerpoJson.error || 'No fue posible obtener el listado.');
        }

        renderizarTablaDeUsuarios(cuerpoJson.data);

    } catch (error) {
        mostrarMensajeDeEstado('error', error.message);
    }
}

// -----------------------------------------------------------------------------
// Alta de usuario (POST create)
// -----------------------------------------------------------------------------
formularioAltaUsuario?.addEventListener('submit', async (evento) => {
    evento.preventDefault();

    const datosFormulario = new FormData(formularioAltaUsuario);

    const datosUsuarioNuevo = {
        nombre: String(datosFormulario.get('nombre') || '').trim(),
        contrasena: String(datosFormulario.get('contrasena') || '').trim(),
        email: String(datosFormulario.get('email') || '').trim(),
        rol: String(datosFormulario.get('rol') || '').trim(),
    };

    if (!datosUsuarioNuevo.nombre || !datosUsuarioNuevo.email || !datosUsuarioNuevo.contrasena || !datosUsuarioNuevo.rol) {
        mostrarMensajeDeEstado('error', 'Nombre, Email, Contraseña y rol son obligatorios.');
        return;
    }

    try {
        activarEstadoCargando();

        const respuestaHttp = await fetch(`${URL_API_SERVIDOR}?action=create`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(datosUsuarioNuevo),
        });

        const cuerpoJson = await respuestaHttp.json();

        if (!cuerpoJson.ok) {
            throw new Error(cuerpoJson.error || 'No fue posible crear el usuario.');
        }

        renderizarTablaDeUsuarios(cuerpoJson.data);
        formularioAltaUsuario.reset();

        mostrarMensajeDeEstado('ok', 'Usuario agregado correctamente.');

    } catch (error) {
        mostrarMensajeDeEstado('error', error.message);
    } finally {
        desactivarEstadoCargando();
    }
});

// -----------------------------------------------------------------------------
// Eliminación y Edición de usuario
// -----------------------------------------------------------------------------
nodoCuerpoTablaUsuarios?.addEventListener('click', async (evento) => {
    const nodoBoton = evento.target.closest('button[data-posicion]');
    if (!nodoBoton) return;

    const posicion = parseInt(nodoBoton.dataset.posicion, 10);
    const accion = nodoBoton.dataset.accion;

    if (!Number.isInteger(posicion)) return;

    // --- ACCIÓN: ELIMINAR ---
    if (accion === 'eliminar') {
        if (!window.confirm('¿Deseas eliminar este usuario?')) return;
        await eliminarUsuario(posicion);
    }

    // --- ACCIÓN: EDITAR ---
    if (accion === 'editar') {
        if (!window.confirm('¿Deseas editar este usuario? Se cargará en el formulario y se eliminará de la lista.')) return;

        try {
            const respuestaHttp = await fetch(`${URL_API_SERVIDOR}?action=list`);
            const cuerpoJson = await respuestaHttp.json();

            if (!cuerpoJson.ok) throw new Error('Error al obtener datos para editar.');

            const usuario = cuerpoJson.data[posicion];
            if (!usuario) throw new Error('Usuario no encontrado.');

            // 2. Llenar formulario
            if (formularioAltaUsuario) {
                formularioAltaUsuario.elements['nombre'].value = usuario.nombre || '';
                formularioAltaUsuario.elements['email'].value = usuario.email || '';
                formularioAltaUsuario.elements['rol'].value = usuario.rol || '';
                // La contraseña NO se puede recuperar. Se deja vacía.
                formularioAltaUsuario.elements['contrasena'].value = '';
                formularioAltaUsuario.elements['contrasena'].focus();
            }

            // 3. Eliminar usuario (sin mensaje)
            await eliminarUsuario(posicion, false);

            mostrarMensajeDeEstado('ok', 'Usuario cargado para editar. Ingresa nueva contraseña y guarda.');

        } catch (error) {
            mostrarMensajeDeEstado('error', error.message);
        }
    }
});

async function eliminarUsuario(index, mostrarMensaje = true) {
    try {
        const respuestaHttp = await fetch(`${URL_API_SERVIDOR}?action=delete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ index: index }),
        });

        const cuerpoJson = await respuestaHttp.json();

        if (!cuerpoJson.ok) {
            throw new Error(cuerpoJson.error || 'No fue posible eliminar el usuario.');
        }

        renderizarTablaDeUsuarios(cuerpoJson.data);

        if (mostrarMensaje) {
            mostrarMensajeDeEstado('ok', 'Usuario eliminado correctamente.');
        }

    } catch (error) {
        mostrarMensajeDeEstado('error', error.message);
        throw error;
    }
}

// -----------------------------------------------------------------------------
// Inicialización
// -----------------------------------------------------------------------------
obtenerYMostrarListadoDeUsuarios();
