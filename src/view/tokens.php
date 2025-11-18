<?php
// tokens.php - Vista para gestionar tokens
// Asegúrate de incluir este archivo en tu directorio de vistas
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Gestión de Tokens API</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label>Cliente API</label>
                            <select class="form-control" id="busqueda_tabla_cliente_api" onchange="listar_tokens()">
                                <option value="">Todos</option>
                                <!-- Opciones se cargan dinámicamente -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Estado</label>
                            <select class="form-control" id="busqueda_tabla_estado_token" onchange="listar_tokens()">
                                <option value="">Todos</option>
                                <option value="activo">Activo</option>
                                <option value="expirado">Expirado</option>
                                <option value="revocado">Revocado</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Mostrar</label>
                            <select class="form-control" id="cantidad_mostrar_token" onchange="listar_tokens()">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tabla -->
                    <div id="tablas_token">
                        <div class="alert alert-info">Cargando tokens...</div>
                    </div>

                    <!-- Paginación -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div id="texto_paginacion_tabla_token"></div>
                        </div>
                        <div class="col-md-6">
                            <ul class="pagination justify-content-end" id="lista_paginacion_tabla_token"></ul>
                        </div>
                    </div>

                    <input type="hidden" id="pagina_token" value="1">
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url; ?>js/functions_token.js"></script>