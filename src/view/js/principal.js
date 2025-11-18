// Mostrar el popup de carga
function mostrarPopupCarga() {
    const popup = document.getElementById('popup-carga');
    if (popup) {
        popup.style.display = 'flex';
    }
}

// Ocultar el popup de carga
function ocultarPopupCarga() {
    const popup = document.getElementById('popup-carga');
    if (popup) {
        popup.style.display = 'none';
    }
}

//funcion en caso de session caducada
async function alerta_sesion() {
    Swal.fire({
        type: 'error',
        title: 'Error de Sesión',
        text: "Sesión Caducada, Por favor inicie sesión",
        confirmButtonClass: 'btn btn-confirm mt-2',
        footer: '',
        timer: 1000
    });
    location.replace(base_url + "login");
}

// cargar hoteles en el menu
async function cargar_hoteles_menu(id_hotel = 0) {
    const formData = new FormData();
    formData.append('sesion', session_session);
    formData.append('token', token_token);
    try {
        let respuesta = await fetch(base_url_server + 'src/control/Hotel.php?tipo=listar_activos', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
        let json = await respuesta.json();
        if (json.status) {
            let datos = json.contenido;
            let contenido = '';
            let hotel_seleccionado = 'Seleccionar Hotel';
            
            datos.forEach(item => {
                if (id_hotel == item.id) {
                    hotel_seleccionado = item.nombre;
                }
                contenido += `<button href="javascript:void(0);" class="dropdown-item notify-item" onclick="actualizar_hotel_menu(${item.id});">${item.nombre}</button>`;
            });
            
            if (document.getElementById('contenido_menu_hoteles')) {
                document.getElementById('contenido_menu_hoteles').innerHTML = contenido;
            }
            if (document.getElementById('menu_hotel')) {
                document.getElementById('menu_hotel').innerHTML = hotel_seleccionado;
            }
        }
    } catch (e) {
        console.log("Error al cargar hoteles: " + e);
    }
}

async function cargar_datos_menu(hotel) {
    cargar_hoteles_menu(hotel);
}

// actualizar hotel en el menu
// Elimina o comenta estas funciones viejas que ya no existen
/*
async function cargar_institucion_menu(id_ies = 0) {
    // Esta función ya no existe
}
*/

// En su lugar, asegúrate de tener esta función:
async function cargar_hoteles_menu(id_hotel = 0) {
    const formData = new FormData();
    formData.append('sesion', session_session);
    formData.append('token', token_token);
    try {
        let respuesta = await fetch(base_url_server + 'src/control/Hotel.php?tipo=listar_activos', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
        
        // Verificar si la respuesta es HTML en lugar de JSON
        const text = await respuesta.text();
        let json;
        
        try {
            json = JSON.parse(text);
        } catch (parseError) {
            console.error("Error parsing JSON:", parseError);
            console.log("Response was:", text);
            return;
        }
        
        if (json.status) {
            let datos = json.contenido;
            let contenido = '';
            let hotel_seleccionado = 'Seleccionar Hotel';
            
            datos.forEach(item => {
                if (id_hotel == item.id) {
                    hotel_seleccionado = item.nombre;
                }
                contenido += `<button href="javascript:void(0);" class="dropdown-item notify-item" onclick="actualizar_hotel_menu(${item.id});">${item.nombre}</button>`;
            });
            
            if (document.getElementById('contenido_menu_hoteles')) {
                document.getElementById('contenido_menu_hoteles').innerHTML = contenido;
            }
            if (document.getElementById('menu_hotel')) {
                document.getElementById('menu_hotel').innerHTML = hotel_seleccionado;
            }
        }
    } catch (e) {
        console.log("Error al cargar hoteles: " + e);
    }
}

async function cargar_datos_menu(hotel) {
    cargar_hoteles_menu(hotel);
}

function generar_paginacion(total, cantidad_mostrar) {
    let actual = document.getElementById('pagina').value;
    let paginas = Math.ceil(total / cantidad_mostrar);
    let paginacion = '<li class="page-item';
    if (actual == 1) {
        paginacion += ' disabled';
    }
    paginacion += ' "><button class="page-link waves-effect" onclick="numero_pagina(1);">Inicio</button></li>';
    paginacion += '<li class="page-item ';
    if (actual == 1) {
        paginacion += ' disabled';
    }
    paginacion += '"><button class="page-link waves-effect" onclick="numero_pagina(' + (actual - 1) + ');">Anterior</button></li>';
    
    if (actual > 4) {
        var iin = (actual - 2);
    } else {
        var iin = 1;
    }
    
    for (let index = iin; index <= paginas; index++) {
        if ((paginas - 7) > index) {
            var n_n = iin + 5;
        }
        if (index == n_n) {
            var nn = actual + 1;
            paginacion += '<li class="page-item"><button class="page-link" onclick="numero_pagina(' + nn + ')">...</button></li>';
            index = paginas - 2;
        }
        paginacion += '<li class="page-item ';
        if (actual == index) {
            paginacion += "active";
        }
        paginacion += '" ><button class="page-link" onclick="numero_pagina(' + index + ');">' + index + '</button></li>';
    }
    
    paginacion += '<li class="page-item ';
    if (actual >= paginas) {
        paginacion += "disabled";
    }
    paginacion += '"><button class="page-link" onclick="numero_pagina(' + (parseInt(actual) + 1) + ');">Siguiente</button></li>';

    paginacion += '<li class="page-item ';
    if (actual >= paginas) {
        paginacion += "disabled";
    }
    paginacion += '"><button class="page-link" onclick="numero_pagina(' + paginas + ');">Final</button></li>';
    
    return paginacion;
}

function generar_texto_paginacion(total, cantidad_mostrar) {
    let actual = document.getElementById('pagina').value;
    let paginas = Math.ceil(total / cantidad_mostrar);
    let iniciar = (actual - 1) * cantidad_mostrar;
    let fin = parseInt(iniciar) + parseInt(cantidad_mostrar);
    
    if (fin > total) {
        fin = total;
    }
    
    var texto = '<label>Mostrando del ' + (parseInt(iniciar) + 1) + ' al ' + fin + ' de un total de ' + total + ' registros</label>';
    return texto;
}

// ---------------------------------------------  DATOS DE CARGA PARA FILTRO DE BUSQUEDA -----------------------------------------------
//cargar habitaciones para filtro
function cargar_habitaciones_filtro(datos, form = 'busqueda_tabla_habitacion', filtro = 'filtro_habitacion') {
    let habitacion_actual = document.getElementById(filtro).value;
    let lista_habitaciones = `<option value="0">TODAS</option>`;
    
    datos.forEach(habitacion => {
        let selected = "";
        if (habitacion.id == habitacion_actual) {
            selected = "selected";
        }
        lista_habitaciones += `<option value="${habitacion.id}" ${selected}>${habitacion.numero_habitacion} - ${habitacion.tipo_habitacion}</option>`;
    });
    
    if (document.getElementById(form)) {
        document.getElementById(form).innerHTML = lista_habitaciones;
    }
}

//cargar hoteles para filtro
function cargar_hoteles_filtro(hoteles) {
    let hotel_actual = document.getElementById('hotel_actual_filtro').value;
    let lista_hoteles = `<option value="0">TODOS</option>`;
    
    hoteles.forEach(hotel => {
        let hotel_selected = "";
        if (hotel.id == hotel_actual) {
            hotel_selected = "selected";
        }
        lista_hoteles += `<option value="${hotel.id}" ${hotel_selected}>${hotel.nombre}</option>`;
    });
    
    if (document.getElementById('busqueda_tabla_hotel')) {
        document.getElementById('busqueda_tabla_hotel').innerHTML = lista_hoteles;
    }
}

//cargar tipos de habitacion para filtro
function cargar_tipos_habitacion_filtro() {
    const tipos = [
        {id: 'individual', nombre: 'Individual'},
        {id: 'doble', nombre: 'Doble'},
        {id: 'suite', nombre: 'Suite'},
        {id: 'familiar', nombre: 'Familiar'},
        {id: 'ejecutiva', nombre: 'Ejecutiva'}
    ];
    
    let tipo_actual = document.getElementById('tipo_habitacion_actual').value;
    let lista_tipos = `<option value="">TODOS</option>`;
    
    tipos.forEach(tipo => {
        let tipo_selected = "";
        if (tipo.id == tipo_actual) {
            tipo_selected = "selected";
        }
        lista_tipos += `<option value="${tipo.id}" ${tipo_selected}>${tipo.nombre}</option>`;
    });
    
    if (document.getElementById('busqueda_tabla_tipo_habitacion')) {
        document.getElementById('busqueda_tabla_tipo_habitacion').innerHTML = lista_tipos;
    }
}

//cargar estados de reserva para filtro
function cargar_estados_reserva_filtro() {
    const estados = [
        {id: 'pendiente', nombre: 'Pendiente'},
        {id: 'confirmada', nombre: 'Confirmada'},
        {id: 'activa', nombre: 'Activa'},
        {id: 'completada', nombre: 'Completada'},
        {id: 'cancelada', nombre: 'Cancelada'}
    ];
    
    let estado_actual = document.getElementById('estado_reserva_actual').value;
    let lista_estados = `<option value="">TODOS</option>`;
    
    estados.forEach(estado => {
        let estado_selected = "";
        if (estado.id == estado_actual) {
            estado_selected = "selected";
        }
        lista_estados += `<option value="${estado.id}" ${estado_selected}>${estado.nombre}</option>`;
    });
    
    if (document.getElementById('busqueda_tabla_estado_reserva')) {
        document.getElementById('busqueda_tabla_estado_reserva').innerHTML = lista_estados;
    }
}

// ------------------------------------------- FIN DE DATOS DE CARGA PARA FILTRO DE BUSQUEDA -----------------------------------------------
async function validar_datos_reset_password() {
    let id = document.getElementById('data').value;
    let token = document.getElementById('data2').value;
    const formData = new FormData();
    formData.append('id', id);
    formData.append('token', token);
    formData.append('sesion', '');
    
    try {
        let respuesta = await fetch(base_url_server + 'src/control/Usuario.php?tipo=validar_datos_reset_password', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
        let json = await respuesta.json();
        
        if (json.status == false) {
            Swal.fire({
                type: 'error',
                title: 'Error de Link',
                text: "Link caducado, verifique su correo",
                confirmButtonClass: 'btn btn-confirm mt-2',
                footer: '',
                timer: 1000
            });
            setTimeout(() => {
                location.replace(base_url + "login");
            }, 1500);
        }
    } catch (e) {
        console.log("Error al validar datos: " + e);
    }
}

function validar_imputs_password() {
    let pass1 = document.getElementById('password').value;
    let pass2 = document.getElementById('password1').value;
    
    if (pass1 !== pass2) {
        Swal.fire({
            type: 'error',
            title: 'Error',
            text: "Contraseña no coincide",
            footer: '',
            timer: 1500
        });
        return;
    }
    
    if (pass1.length < 8 && pass2.length < 8) {
        Swal.fire({
            type: 'error',
            title: 'Error',
            text: "La contraseña tiene que tener 8 caracteres",
            footer: '',
            timer: 1500
        });
        return;
    } else {
        actualizar_password();
    }
}

async function actualizar_password() {
    let id = document.getElementById('data').value;
    let token = document.getElementById('data2').value;
    let nueva_password = document.getElementById('password').value;
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('token', token);
    formData.append('password', nueva_password);
    formData.append('sesion', '');
    
    try {
        Swal.fire({
            title: 'Actualizando...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        let respuesta = await fetch(base_url_server + 'src/control/Usuario.php?tipo=actualizar_password_reset', {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            body: formData
        });
        
        let json = await respuesta.json();
        
        if (json.status == true) {
            Swal.fire({
                type: 'success',
                title: 'Éxito',
                text: json.msg,
                confirmButtonClass: 'btn btn-confirm mt-2',
                footer: '',
                timer: 2000
            }).then(() => {
                location.replace(base_url + "login");
            });
        } else {
            Swal.fire({
                type: 'error',
                title: 'Error',
                text: json.msg,
                confirmButtonClass: 'btn btn-confirm mt-2',
                footer: '',
                timer: 2000
            });
        }
        
    } catch (error) {
        console.log("Error al actualizar contraseña: " + error);
        Swal.fire({
            type: 'error',
            title: 'Error',
            text: 'Error de conexión. Intente nuevamente.',
            confirmButtonClass: 'btn btn-confirm mt-2',
            footer: '',
            timer: 2000
        });
    }
}

// Función para formatear fecha
function formatearFecha(fecha) {
    if (!fecha) return '';
    const date = new Date(fecha);
    return date.toLocaleDateString('es-ES');
}

// Función para formatear moneda
function formatearMoneda(monto) {
    if (!monto) return 'S/ 0.00';
    return 'S/ ' + parseFloat(monto).toFixed(2);
}

// Función para validar email
function validarEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Función para validar DNI
function validarDNI(dni) {
    return /^\d{8}$/.test(dni);
}

// Función para validar RUC
function validarRUC(ruc) {
    return /^\d{11}$/.test(ruc);
}

// Función para calcular días entre dos fechas
function calcularDias(fechaInicio, fechaFin) {
    const inicio = new Date(fechaInicio);
    const fin = new Date(fechaFin);
    const diffTime = Math.abs(fin - inicio);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
}

// Función para validar que un elemento exista antes de usarlo
function elementExists(id) {
    return document.getElementById(id) !== null;
}