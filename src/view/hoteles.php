<!-- start page title -->
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <div class="card">
            <div class="card-body">
                <div class="page-title-box d-flex align-items-center justify-content-between p-0">
                    <h4 class="mb-0 font-size-18">Gestión de Hoteles</h4>
                    <div class="page-title-right">
                        <button type="button" class="btn btn-primary waves-effect waves-light" data-toggle="modal" data-target=".modal_registrar">
                            + Nuevo Hotel
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Filtros de Búsqueda</h4>
                <div class="row">
                    <div class="form-group row mb-3 col-md-4">
                        <label for="busqueda_tabla_nombre" class="col-4 col-form-label">Nombre:</label>
                        <div class="col-8">
                            <input type="text" class="form-control" name="busqueda_tabla_nombre" id="busqueda_tabla_nombre">
                        </div>
                    </div>
                    <div class="form-group row mb-3 col-md-4">
                        <label for="busqueda_tabla_ruc" class="col-4 col-form-label">RUC:</label>
                        <div class="col-8">
                            <input type="text" class="form-control" name="busqueda_tabla_ruc" id="busqueda_tabla_ruc">
                        </div>
                    </div>
                    <div class="form-group row mb-3 col-md-4">
                        <label for="busqueda_tabla_estado" class="col-4 col-form-label">Estado:</label>
                        <div class="col-8">
                            <select class="form-control" name="busqueda_tabla_estado" id="busqueda_tabla_estado">
                                <option value="">TODOS</option>
                                <option value="1">ACTIVO</option>
                                <option value="0">INACTIVO</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group mb-0 text-center">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="numero_pagina_hotel(1);">
                        <i class="fa fa-search"></i> Buscar
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Lista de Hoteles</h4>
                <div id="filtros_tabla_header" class="form-group row page-title-box d-flex align-items-center justify-content-between m-0 mb-1 p-0">
                    <input type="hidden" id="pagina_hotel" value="1">
                    <input type="hidden" id="filtro_nombre" value="">
                    <input type="hidden" id="filtro_ruc" value="">
                    <input type="hidden" id="filtro_estado" value="">
                    <div>
                        <label for="cantidad_mostrar_hotel">Mostrar</label>
                        <select name="cantidad_mostrar_hotel" id="cantidad_mostrar_hotel" class="form-control-sm" onchange="numero_pagina_hotel(1);">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <label for="cantidad_mostrar_hotel">registros</label>
                    </div>
                </div>
                <div id="tablas_hotel"></div>
                <div id="filtros_tabla_footer" class="form-group row page-title-box d-flex align-items-center justify-content-between m-0 mb-1 p-0">
                    <div id="texto_paginacion_tabla_hotel"></div>
                    <div id="paginacion_tabla_hotel">
                        <ul class="pagination justify-content-end" id="lista_paginacion_tabla_hotel"></ul>
                    </div>
                </div>
                <div id="modals_editar_hotel"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Registrar -->
<div class="modal fade modal_registrar" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header text-center">
                <h5 class="modal-title h4">Registrar Nuevo Hotel</h5>
                <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-12">
                    <form class="form-horizontal" id="frmRegistrarHotel">
                        <div class="form-group row mb-2">
                            <label for="nombre" class="col-3 col-form-label">Nombre del Hotel *</label>
                            <div class="col-9">
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label for="direccion" class="col-3 col-form-label">Dirección *</label>
                            <div class="col-9">
                                <input type="text" class="form-control" id="direccion" name="direccion" required>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label for="telefono" class="col-3 col-form-label">Teléfono *</label>
                            <div class="col-9">
                                <input type="text" class="form-control" id="telefono" name="telefono" required>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label for="email" class="col-3 col-form-label">Email</label>
                            <div class="col-9">
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label for="ruc" class="col-3 col-form-label">RUC *</label>
                            <div class="col-9">
                                <input type="text" class="form-control" id="ruc" name="ruc" required>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-2">
                            <label for="estrellas" class="col-3 col-form-label">Categoría (Estrellas) *</label>
                            <div class="col-9">
                                <select name="estrellas" id="estrellas" class="form-control" required>
                                    <option value="">Seleccionar</option>
                                    <option value="1">1 Estrella</option>
                                    <option value="2">2 Estrellas</option>
                                    <option value="3">3 Estrellas</option>
                                    <option value="4">4 Estrellas</option>
                                    <option value="5">5 Estrellas</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label for="estado" class="col-3 col-form-label">Estado *</label>
                            <div class="col-9">
                                <select name="estado" id="estado" class="form-control" required>
                                    <option value="1">ACTIVO</option>
                                    <option value="0">INACTIVO</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group mb-0 justify-content-end row text-center">
                            <div class="col-12">
                                <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-success waves-effect waves-light" onclick="registrar_hotel()">Registrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo BASE_URL; ?>src/view/js/functions_hotel.js"></script>
<script>
    listar_hoteles();
</script>
<!-- end page title -->