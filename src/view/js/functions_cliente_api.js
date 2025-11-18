async function listar_clientes_api() {
    try {
        mostrarPopupCarga();
        
        const formData = new FormData();
        formData.append('sesion', session_session);
        formData.append('token', token_token);

        let respuesta = await fetch(base_url_server + 'src/control/ClienteApi.php?tipo=listar', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });

        // Verificar si la respuesta es válida
        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }

        const texto = await respuesta.text();
        console.log("Respuesta del servidor:", texto);

        let json;
        try {
            json = JSON.parse(texto);
        } catch (parseError) {
            console.error("Error parseando JSON:", parseError);
            throw new Error("Respuesta del servidor no es JSON válido");
        }

        // Limpiar tabla
        document.getElementById('tablas_cliente_api').innerHTML = `
            <table class="table dt-responsive" width="100%">
                <thead>
                    <tr>
                        <th>Nro</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>API Key</th>
                        <th>IPs Permitidas</th>
                        <th>Límite Requests</th>
                        <th>Estado</th>
                        <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="contenido_tabla_cliente_api">
                </tbody>
            </table>`;
        
        if (json.status && json.contenido && json.contenido.length > 0) {
            let datos = json.contenido;
            datos.forEach((item, index) => {
                generar_filas_tabla_cliente_api(item, index + 1);
            });
        } else if (json.msg == "Error_Sesion") {
            alerta_sesion();
        } else {
            document.getElementById('tablas_cliente_api').innerHTML = `
                <div class="alert alert-info">
                    No se encontraron clientes API registrados
                </div>`;
        }
        
    } catch (e) {
        console.log("Error al cargar clientes API: " + e);
        document.getElementById('tablas_cliente_api').innerHTML = `
            <div class="alert alert-danger">
                Error al cargar datos: ${e.message}
            </div>`;
    } finally {
        ocultarPopupCarga();
    }
}

function generar_filas_tabla_cliente_api(item, contador) {
    const tbody = document.getElementById('contenido_tabla_cliente_api');
    
    let estado_texto = item.estado == 1 ? "ACTIVO" : "INACTIVO";
    let estado_class = item.estado == 1 ? "badge badge-success" : "badge badge-danger";
    
    // Formatear fecha
    const fechaCreacion = new Date(item.fecha_creacion).toLocaleDateString('es-ES');

    const fila = document.createElement("tr");
    fila.innerHTML = `
        <th>${contador}</th>
        <td>${item.nombre || 'N/A'}</td>
        <td>${item.descripcion || 'Sin descripción'}</td>
        <td>
            <code class="api-key" style="cursor: pointer;" onclick="mostrar_credenciales(${item.id})" 
                  title="Click para ver credenciales">
                ${item.api_key || 'N/A'}
            </code>
        </td>
        <td>${item.ip_permitidas || 'Todas'}</td>
        <td>${item.limite_requests || '1000'}</td>
        <td><span class="${estado_class}">${estado_texto}</span></td>
        <td>${fechaCreacion}</td>
        <td>
            <button type="button" title="Ver Detalles" class="btn btn-info waves-effect waves-light btn-sm"
                    onclick="mostrar_credenciales(${item.id})">
                <i class="fa fa-key"></i>
            </button>
            <button type="button" title="Editar" class="btn btn-primary waves-effect waves-light btn-sm">
                <i class="fa fa-edit"></i>
            </button>
            <button type="button" title="${item.estado == 1 ? 'Desactivar' : 'Activar'}" 
                    class="btn ${item.estado == 1 ? 'btn-warning' : 'btn-success'} waves-effect waves-light btn-sm">
                <i class="fa ${item.estado == 1 ? 'fa-times' : 'fa-check'}"></i>
            </button>
        </td>
    `;

    tbody.appendChild(fila);
}

async function mostrar_credenciales(id) {
    try {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('sesion', session_session);
        formData.append('token', token_token);

        let respuesta = await fetch(base_url_server + 'src/control/ClienteApi.php?tipo=obtener_por_id', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });

        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }

        const json = await respuesta.json();
        
        if (json.status) {
            const cliente = json.contenido;
            Swal.fire({
                title: 'Credenciales API',
                html: `
                    <div class="text-left">
                        <p><strong>Nombre:</strong> ${cliente.nombre}</p>
                        <p><strong>API Key:</strong></p>
                        <code style="background: #f8f9fa; padding: 10px; border-radius: 5px; display: block; word-break: break-all;">
                            ${cliente.api_key}
                        </code>
                        <p class="mt-3"><strong>API Secret:</strong></p>
                        <code style="background: #f8f9fa; padding: 10px; border-radius: 5px; display: block; word-break: break-all;">
                            ${cliente.api_secret}
                        </code>
                        <p class="mt-3 text-warning">
                            <i class="fa fa-warning"></i> Guarda estas credenciales en un lugar seguro
                        </p>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Cerrar',
                confirmButtonClass: 'btn btn-confirm mt-2',
                width: '600px'
            });
        } else {
            Swal.fire({
                title: 'Error',
                text: 'No se pudieron obtener las credenciales',
                icon: 'error',
                confirmButtonClass: 'btn btn-confirm mt-2'
            });
        }
    } catch (e) {
        console.log("Error al obtener credenciales: " + e);
        Swal.fire({
            title: 'Error',
            text: 'Error al obtener credenciales: ' + e.message,
            icon: 'error',
            confirmButtonClass: 'btn btn-confirm mt-2'
        });
    }
}

async function registrar_cliente_api() {
    const formulario = document.getElementById('frmRegistrarClienteApi');
    const datos = new FormData(formulario);

    if (!validar_formulario_cliente_api(datos)) {
        return;
    }

    try {
        datos.append('sesion', session_session);
        datos.append('token', token_token);

        console.log("Datos a enviar:", Object.fromEntries(datos));

        let respuesta = await fetch(base_url_server + 'src/control/ClienteApi.php?tipo=registrar', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: datos
        });

        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }

        const texto = await respuesta.text();
        console.log("Respuesta del servidor:", texto);

        let json;
        try {
            json = JSON.parse(texto);
        } catch (parseError) {
            console.error("Error parseando JSON:", parseError);
            throw new Error("Respuesta del servidor no es JSON válido");
        }

        if (json.status) {
            document.getElementById("frmRegistrarClienteApi").reset();
            Swal.fire({
                title: 'Registro Exitoso',
                text: json.mensaje,
                icon: 'success',
                confirmButtonClass: 'btn btn-confirm mt-2',
                timer: 2000
            }).then(() => {
                listar_clientes_api();
                $('#modalRegistrarClienteApi').modal('hide');
            });
        } else if (json.msg == "Error_Sesion") {
            alerta_sesion();
        } else {
            Swal.fire({
                title: 'Error',
                text: json.mensaje || 'Error al registrar cliente API',
                icon: 'error',
                confirmButtonClass: 'btn btn-confirm mt-2'
            });
        }
    } catch (e) {
        console.log("Error al registrar cliente API: " + e);
        Swal.fire({
            title: 'Error',
            text: 'Error de conexión: ' + e.message,
            icon: 'error',
            confirmButtonClass: 'btn btn-confirm mt-2'
        });
    }
}

function validar_formulario_cliente_api(datos) {
    const nombre = datos.get('nombre');
    
    if (!nombre || nombre.trim() === '') {
        Swal.fire({
            title: 'Error',
            text: 'El nombre es obligatorio',
            icon: 'error',
            confirmButtonClass: 'btn btn-confirm mt-2'
        });
        return false;
    }

    return true;
}

// Cargar clientes API al iniciar la página
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('tablas_cliente_api')) {
        listar_clientes_api();
    }
});