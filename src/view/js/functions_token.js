async function listar_tokens() {
    try {
        mostrarPopupCarga();
        
        const pagina = document.getElementById('pagina_token')?.value || 1;
        const cantidad_mostrar = document.getElementById('cantidad_mostrar_token')?.value || 10;
        const id_cliente_api = document.getElementById('busqueda_tabla_cliente_api')?.value || '';
        const estado = document.getElementById('busqueda_tabla_estado_token')?.value || '';

        const formData = new FormData();
        formData.append('pagina', pagina);
        formData.append('cantidad_mostrar', cantidad_mostrar);
        formData.append('id_cliente_api', id_cliente_api);
        formData.append('estado', estado);
        formData.append('sesion', session_session);
        formData.append('token', token_token);

        let respuesta = await fetch(base_url_server + 'src/control/Token.php?tipo=listar', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });

        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }

        const json = await respuesta.json();

        document.getElementById('tablas_token').innerHTML = `
            <table class="table dt-responsive" width="100%">
                <thead>
                    <tr>
                        <th>Nro</th>
                        <th>Cliente API</th>
                        <th>Token Acceso</th>
                        <th>Fecha Expiración</th>
                        <th>IP Origen</th>
                        <th>Estado</th>
                        <th>Fecha Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="contenido_tabla_token">
                </tbody>
            </table>`;
        
        if (json.status && json.contenido && json.contenido.length > 0) {
            let datos = json.contenido;
            datos.forEach((item, index) => {
                generar_filas_tabla_token(item, index + 1);
            });
            
            // Actualizar paginación
            if (document.getElementById('texto_paginacion_tabla_token')) {
                let texto_paginacion = generar_texto_paginacion(json.total, cantidad_mostrar);
                document.getElementById('texto_paginacion_tabla_token').innerHTML = texto_paginacion;
            }
            
            if (document.getElementById('lista_paginacion_tabla_token')) {
                let paginacion = generar_paginacion(json.total, cantidad_mostrar);
                document.getElementById('lista_paginacion_tabla_token').innerHTML = paginacion;
            }
        } else if (json.msg == "Error_Sesion") {
            alerta_sesion();
        } else {
            document.getElementById('tablas_token').innerHTML = `
                <div class="alert alert-info">
                    No se encontraron tokens
                </div>`;
        }
        
    } catch (e) {
        console.log("Error al cargar tokens: " + e);
        document.getElementById('tablas_token').innerHTML = `
            <div class="alert alert-danger">
                Error al cargar tokens: ${e.message}
            </div>`;
    } finally {
        ocultarPopupCarga();
    }
}

function generar_filas_tabla_token(item, contador) {
    const tbody = document.getElementById('contenido_tabla_token');
    
    let estado_texto = item.estado;
    let estado_class = 'badge ';
    switch(item.estado) {
        case 'activo':
            estado_class += 'badge-success';
            break;
        case 'expirado':
            estado_class += 'badge-warning';
            break;
        case 'revocado':
            estado_class += 'badge-danger';
            break;
        default:
            estado_class += 'badge-secondary';
    }
    
    // Formatear fechas
    const fechaExpiracion = new Date(item.fecha_expiracion).toLocaleDateString('es-ES');
    const fechaCreacion = new Date(item.fecha_creacion).toLocaleDateString('es-ES');

    const fila = document.createElement("tr");
    fila.innerHTML = `
        <th>${contador}</th>
        <td>${item.cliente_api_nombre || 'N/A'}</td>
        <td>
            <code style="font-size: 0.8em;" title="${item.token_acceso}">
                ${item.token_acceso}
            </code>
        </td>
        <td>${fechaExpiracion}</td>
        <td>${item.ip_origen || 'N/A'}</td>
        <td><span class="${estado_class}">${estado_texto.toUpperCase()}</span></td>
        <td>${fechaCreacion}</td>
        <td>
            ${item.estado === 'activo' ? `
            <button type="button" title="Revocar Token" class="btn btn-danger waves-effect waves-light btn-sm"
                    onclick="revocar_token(${item.id})">
                <i class="fa fa-ban"></i>
            </button>
            ` : ''}
            <button type="button" title="Ver Detalles" class="btn btn-info waves-effect waves-light btn-sm">
                <i class="fa fa-eye"></i>
            </button>
        </td>
    `;

    tbody.appendChild(fila);
}

async function revocar_token(id) {
    try {
        const result = await Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción revocará el token y no se podrá deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, revocar',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('sesion', session_session);
            formData.append('token', token_token);

            let respuesta = await fetch(base_url_server + 'src/control/Token.php?tipo=revocar', {
                method: 'POST',
                mode: 'cors',
                cache: 'no-cache',
                body: formData
            });

            const json = await respuesta.json();

            if (json.status) {
                Swal.fire({
                    title: 'Token Revocado',
                    text: json.mensaje,
                    icon: 'success',
                    confirmButtonClass: 'btn btn-confirm mt-2'
                });
                listar_tokens();
            } else {
                Swal.fire({
                    title: 'Error',
                    text: json.mensaje,
                    icon: 'error',
                    confirmButtonClass: 'btn btn-confirm mt-2'
                });
            }
        }
    } catch (e) {
        console.log("Error al revocar token: " + e);
        Swal.fire({
            title: 'Error',
            text: 'Error al revocar token: ' + e.message,
            icon: 'error',
            confirmButtonClass: 'btn btn-confirm mt-2'
        });
    }
}

function numero_pagina_token(pagina) {
    if (document.getElementById('pagina_token')) {
        document.getElementById('pagina_token').value = pagina;
    }
    listar_tokens();
}

// Cargar tokens al iniciar la página
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('tablas_token')) {
        listar_tokens();
    }
});