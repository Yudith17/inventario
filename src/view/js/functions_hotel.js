function numero_pagina_hotel(pagina) {
    document.getElementById('pagina_hotel').value = pagina;
    listar_hoteles();
}

async function listar_hoteles() {
    try {
        mostrarPopupCarga();
        let pagina = document.getElementById('pagina_hotel').value;
        let cantidad_mostrar = document.getElementById('cantidad_mostrar_hotel').value;
        let busqueda_tabla_nombre = document.getElementById('busqueda_tabla_nombre').value;
        let busqueda_tabla_ruc = document.getElementById('busqueda_tabla_ruc').value;
        let busqueda_tabla_estado = document.getElementById('busqueda_tabla_estado').value;

        document.getElementById('filtro_nombre').value = busqueda_tabla_nombre;
        document.getElementById('filtro_ruc').value = busqueda_tabla_ruc;
        document.getElementById('filtro_estado').value = busqueda_tabla_estado;

        const formData = new FormData();
        formData.append('pagina', pagina);
        formData.append('cantidad_mostrar', cantidad_mostrar);
        formData.append('busqueda_tabla_nombre', busqueda_tabla_nombre);
        formData.append('busqueda_tabla_ruc', busqueda_tabla_ruc);
        formData.append('busqueda_tabla_estado', busqueda_tabla_estado);
        formData.append('sesion', session_session);
        formData.append('token', token_token);

        let respuesta = await fetch(base_url_server + 'src/control/Hotel.php?tipo=listar_hoteles_tabla', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });

        let json = await respuesta.json();
        document.getElementById('tablas_hotel').innerHTML = `<table class="table dt-responsive" width="100%">
                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>RUC</th>
                            <th>Estrellas</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="contenido_tabla_hotel">
                    </tbody>
                </table>`;
        document.querySelector('#modals_editar_hotel').innerHTML = ``;

        if (json.status) {
            let datos = json.contenido;
            datos.forEach(item => {
                generar_filas_tabla_hotel(item);
            });
        } else if (json.msg == "Error_Sesion") {
            alerta_sesion();
        } else {
            document.getElementById('tablas_hotel').innerHTML = `<div class="alert alert-info">No se encontraron hoteles</div>`;
        }

        let paginacion = generar_paginacion(json.total, cantidad_mostrar);
        let texto_paginacion = generar_texto_paginacion(json.total, cantidad_mostrar);
        document.getElementById('texto_paginacion_tabla_hotel').innerHTML = texto_paginacion;
        document.getElementById('lista_paginacion_tabla_hotel').innerHTML = paginacion;
    } catch (e) {
        console.log("Error al cargar hoteles: " + e);
    } finally {
        ocultarPopupCarga();
    }
}

function generar_filas_tabla_hotel(item) {
    let cont = 1;
    $(".filas_tabla_hotel").each(function () {
        cont++;
    })

    let nueva_fila = document.createElement("tr");
    nueva_fila.id = "fila_hotel" + item.id;
    nueva_fila.className = "filas_tabla_hotel";

    let estado_texto = item.estado == 1 ? "ACTIVO" : "INACTIVO";
    let estado_class = item.estado == 1 ? "badge badge-success" : "badge badge-danger";

    nueva_fila.innerHTML = `
        <th>${cont}</th>
        <td>${item.nombre}</td>
        <td>${item.direccion || ''}</td>
        <td>${item.telefono || ''}</td>
        <td>${item.email || ''}</td>
        <td>${item.ruc || ''}</td>
        <td>${'★'.repeat(item.estrellas || 0)}</td>
        <td><span class="${estado_class}">${estado_texto}</span></td>
        <td>${item.options || ''}</td>
    `;

    // Modal para editar
    let activo_si = item.estado == 1 ? "selected" : "";
    let activo_no = item.estado == 0 ? "selected" : "";

    let estrellas_options = "";
    for (let i = 1; i <= 5; i++) {
        let selected = i == item.estrellas ? "selected" : "";
        estrellas_options += `<option value="${i}" ${selected}>${i} Estrella${i > 1 ? 's' : ''}</option>`;
    }

    document.querySelector('#modals_editar_hotel').innerHTML += `
        <div class="modal fade modal_editar${item.id}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-center">
                        <h5 class="modal-title h4">Actualizar datos del Hotel</h5>
                        <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12">
                            <form class="form-horizontal" id="frmActualizarHotel${item.id}">
                                <div class="form-group row mb-2">
                                    <label for="nombre${item.id}" class="col-3 col-form-label">Nombre del Hotel</label>
                                    <div class="col-9">
                                        <input type="text" class="form-control" id="nombre${item.id}" name="nombre" value="${item.nombre || ''}" required>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="direccion${item.id}" class="col-3 col-form-label">Dirección</label>
                                    <div class="col-9">
                                        <input type="text" class="form-control" id="direccion${item.id}" name="direccion" value="${item.direccion || ''}" required>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="telefono${item.id}" class="col-3 col-form-label">Teléfono</label>
                                    <div class="col-9">
                                        <input type="text" class="form-control" id="telefono${item.id}" name="telefono" value="${item.telefono || ''}" required>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="email${item.id}" class="col-3 col-form-label">Email</label>
                                    <div class="col-9">
                                        <input type="email" class="form-control" id="email${item.id}" name="email" value="${item.email || ''}">
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="ruc${item.id}" class="col-3 col-form-label">RUC</label>
                                    <div class="col-9">
                                        <input type="text" class="form-control" id="ruc${item.id}" name="ruc" value="${item.ruc || ''}" required>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="estrellas${item.id}" class="col-3 col-form-label">Categoría (Estrellas)</label>
                                    <div class="col-9">
                                        <select name="estrellas" id="estrellas${item.id}" class="form-control" required>
                                            <option value="">Seleccionar</option>
                                            ${estrellas_options}
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <label for="estado${item.id}" class="col-3 col-form-label">Estado</label>
                                    <div class="col-9">
                                        <select name="estado" id="estado${item.id}" class="form-control" required>
                                            <option value=""></option>
                                            <option value="1" ${activo_si}>ACTIVO</option>
                                            <option value="0" ${activo_no}>INACTIVO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group mb-0 justify-content-end row text-center">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">Cancelar</button>
                                        <button type="button" class="btn btn-success waves-effect waves-light" onclick="actualizarHotel(${item.id})">Actualizar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

    document.querySelector('#contenido_tabla_hotel').appendChild(nueva_fila);
}

async function registrar_hotel() {
    const formulario = document.getElementById('frmRegistrarHotel');
    const datos = new FormData(formulario);

    if (!validar_formulario_hotel(datos)) {
        return;
    }

    try {
        datos.append('sesion', session_session);
        datos.append('token', token_token);

        console.log("Datos a enviar:", {
            nombre: datos.get('nombre'),
            direccion: datos.get('direccion'),
            telefono: datos.get('telefono'),
            email: datos.get('email'),
            ruc: datos.get('ruc'),
            estrellas: datos.get('estrellas'),
            estado: datos.get('estado')
        });

        let respuesta = await fetch(base_url_server + 'src/control/Hotel.php?tipo=registrar', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: datos
        });

        let json = await respuesta.json();
        console.log("Respuesta del servidor:", json);

        if (json.status) {
            document.getElementById("frmRegistrarHotel").reset();
            Swal.fire({
                type: 'success',
                title: 'Registro',
                text: json.mensaje,
                confirmButtonClass: 'btn btn-confirm mt-2',
                footer: '',
                timer: 1000
            });
            listar_hoteles();
        } else if (json.msg == "Error_Sesion") {
            alerta_sesion();
        } else {
            Swal.fire({
                type: 'error',
                title: 'Error',
                text: json.mensaje || 'Error al registrar hotel',
                confirmButtonClass: 'btn btn-confirm mt-2',
                footer: '',
                timer: 1000
            });
        }
    } catch (e) {
        console.log("Error al registrar hotel: " + e);
        Swal.fire({
            type: 'error',
            title: 'Error',
            text: 'Error de conexión: ' + e,
            confirmButtonClass: 'btn btn-confirm mt-2',
            footer: '',
            timer: 1000
        });
    }
}

async function actualizarHotel(id) {
    const formulario = document.getElementById('frmActualizarHotel' + id);
    const datos = new FormData(formulario);

    if (!validar_formulario_hotel(datos)) {
        return;
    }

    datos.append('data', id);
    datos.append('sesion', session_session);
    datos.append('token', token_token);

    try {
        let respuesta = await fetch(base_url_server + 'src/control/Hotel.php?tipo=actualizar', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: datos
        });

        let json = await respuesta.json();
        if (json.status) {
            $('.modal_editar' + id).modal('hide');
            Swal.fire({
                type: 'success',
                title: 'Actualizar',
                text: json.mensaje,
                confirmButtonClass: 'btn btn-confirm mt-2',
                footer: '',
                timer: 1000
            });
            listar_hoteles();
        } else if (json.msg == "Error_Sesion") {
            alerta_sesion();
        } else {
            Swal.fire({
                type: 'error',
                title: 'Error',
                text: json.mensaje,
                confirmButtonClass: 'btn btn-confirm mt-2',
                footer: '',
                timer: 1000
            });
        }
    } catch (e) {
        console.log("Error al actualizar hotel: " + e);
    }
}

function validar_formulario_hotel(datos) {
    const camposRequeridos = ['nombre', 'direccion', 'telefono', 'ruc', 'estrellas', 'estado'];

    for (let campo of camposRequeridos) {
        if (!datos.get(campo)) {
            Swal.fire({
                type: 'error',
                title: 'Error',
                text: `Por favor complete el campo: ${campo}`,
                confirmButtonClass: 'btn btn-confirm mt-2',
                footer: '',
                timer: 1000
            });
            return false;
        }
    }

    // Validar RUC (11 dígitos)
    const ruc = datos.get('ruc');
    if (!/^\d{11}$/.test(ruc)) {
        Swal.fire({
            type: 'error',
            title: 'Error',
            text: 'El RUC debe tener 11 dígitos',
            confirmButtonClass: 'btn btn-confirm mt-2',
            footer: '',
            timer: 1000
        });
        return false;
    }

    return true;
}