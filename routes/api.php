<?php


Route::group(["prefix" => 'v1'], function() {

    Route::get('planilla', 'ApiRest@planilla');
    Route::get('planilla/{id}', 'ApiRest@planillaShow');
    Route::get('cargo/{id}', 'ApiRest@cargoShow');
    Route::get('meta', 'ApiRest@meta');
    

    Route::resource('banco', 'BancoController');
    
    // api de categoria
    Route::get('/categoria/{id}/type_remuneracion', 'CategoriaController@type_remuneracion');
    Route::post('/categoria/{id}/type_remuneracion', 'CategoriaController@assignTypeRemuneracion');
    Route::delete('/categoria/{id}/type_remuneracion', 'CategoriaController@detachTypeRemuneracion');
    
    //Recursos para generar boletas
    Route::get("/boleta/{id}", "JobController@boleta");
    // Generar boletas de un trabajador en pdf
    Route::post('/boleta/{id}', 'JobController@boletaStore');

    // Mostrar un recurso de un cronograma determinado
    Route::get("/cronograma/{id}", "CronogramaController@show");
    // Crear planillas x mes
    Route::post("/cronograma", "CronogramaController@store");
    // actualizar planilla x mes
    Route::put("/cronograma/{id}", "CronogramaController@update");
    // obtener trabajadores para poder agregar a la planilla x mes
    Route::get("/cronograma/{id}/add", "CronogramaController@add");
    // agregar trabajadores a la planilla x mes
    Route::post("/cronograma/{id}/add", "CronogramaController@addStore");
    // cambiar estado de la planilla
    Route::post("/cronograma/{id}/estado", "CronogramaController@estado");
    // obtener informacion de los infos
    Route::get('/cronograma/{id}/historial', 'CronogramaController@historial');
    // eliminar informacion de los infos
    Route::post('/cronograma/{id}/destroy-historial', 'CronogramaController@destroyHistorial');
    

    // registrar un nuevo cargo
    Route::post("/cargo", "CargoController@store");
    // actualizar un cargo determinado
    Route::put("/cargo/{id}", "CargoController@update");


    // registrar una nueva categoria
    Route::post("/categoria", "CategoriaController@store");
    // actualizar una categoria determinada
    Route::put("/categoria/{id}", "CategoriaController@update");
    Route::put("/categoria/{id}/concepto", "CategoriaController@updateConcepto");
    // quitar conceptos
    Route::delete("/categoria/{id}/concepto", "CategoriaController@deleteConcepto");

    // registrar un nuevo concepto
    Route::post("/concepto", "ConceptoController@store");
    // actualizar un concepto determinado
    Route::put("/concepto/{id}", "ConceptoController@update");

    // registrar un nuevo descuento
    Route::post("/descuento", "DescuentoController@store");
    // actualizar un descuento determinado
    Route::put("/descuento/{id}", "DescuentoController@update");

    // registrar una nueva remuneración
    Route::post("/remuneracion", "TypeRemuneracionController@store");
    // actualizar una remuneración determinada
    Route::put("/remuneracion/{id}", "TypeRemuneracionController@update");
    // 
    Route::get('/type_remuneracion', 'TypeRemuneracionController@index');

    // Recursos de modulos
    Route::resource("modulo", "ModuloController");

    // Recursos de los roles
    Route::resource("role", "RoleController");

    // Guardar nuevo usuario
    Route::post("/user", "UserController@store");
    Route::put("/user/{id}", "UserController@update");
    Route::get("/menu/{id}", "UserController@menu");
    // Liquidar trabajador
    Route::post("/liquidar", "LiquidarController@store");


    Route::post("/export/mef/{year}/{mes}", "ExportController@mef");
    Route::post("/export/alta-baja/{year}/{mes}", "ExportController@altaBaja");
    Route::post("/export/resumen/{year}/{mes}", "ExportController@resumen");

    // Works
    Route::resource("work", 'WorkController');
    Route::get("work/{id}/historial", 'WorkController@historial');
    Route::post('work/{id}/report', 'WorkController@report');

    Route::get('work/{id}/obligacion', 'JobController@obligacion');
    Route::get('work/{id}/info', 'JobController@informacion');
    Route::post('work/{id}/config', 'JobController@configStore');
    Route::delete('work/{id}/config', 'JobController@configDelete');
    Route::post('work/{id}/sindicato', 'JobController@sindicatoStore');
    Route::get('work/{id}/retencion', 'JobController@retencion');
    Route::post('work/{id}/retencion', 'JobController@retencionStore');
    Route::get('work/{id}/detalle', 'JobController@detalle');
    Route::post('work/{id}/observacion', 'JobController@observacionUpdate');

    // informacion de los trabajadores
    Route::resource("info", "InfoController")->except(["create", "edit"]);
    Route::get('info/{id}/historial', 'InfoController@historial');
    Route::post('info/{id}/type_aportacion', 'InfoController@type_aportacion');
    Route::post('info/{id}/active', 'InfoController@active');
    Route::delete('info/{id}/delete_aportacion', 'InfoController@delete_aportacion');

    // tipo de aportacion
    Route::resource('type_aportacion', 'TypeAportacionController');

    // aportaciones
    Route::resource('aportacion', 'AportacionController');

    // afps
    Route::get('afp', 'AfpController@index');
    Route::post('afp/{id}/pdf', 'AfpController@pdf');

    //Obligaciones
    Route::resource('obligacion', 'ObligacionController');

    // Configuración de los descuentos
    Route::resource('config_descuento', 'ConfigDescuentoController');


    // obtener los sindicatos
    Route::resource('sindicato', 'SindicatoController');

    // obtener los archivos de los reportes
    Route::get('file/{id}/type/{type}', 'ReportController@files');

    // Generar Reportes
    Route::post("cronograma/{id}/pdf", "ExportCronogramaController@pdf");
    Route::post("cronograma/{id}/meta", "ExportCronogramaController@meta");
    Route::post("cronograma/{id}/boleta", "ExportCronogramaController@boleta");
    Route::post("cronograma/{id}/pago", "ExportCronogramaController@pago");
    Route::post("cronograma/{id}/afp-net", "ExportCronogramaController@afp");
    Route::post("cronograma/{id}/planilla", "ExportCronogramaController@planilla");
    Route::post("cronograma/{id}/descuento", "ExportCronogramaController@descuento");
    Route::post("cronograma/{id}/personal", 'ExportCronogramaController@personal');
    // reportes de ejecucion de los resumenes de todas las metas
    Route::post("cronograma/{id}/descuento-bruto", 'RptController@descuentoBruto');
    Route::post("cronograma/{id}/descuento-bruto-detallado", 'RptController@descuentoBrutoDetalle');
    Route::post("cronograma/{id}/descuento-neto", 'RptController@descuentoNeto');
    Route::post("cronograma/{id}/descuento-neto-detallado", 'RptController@descuentoNetoDetalle');


    // Tipos de descuentos
    Route::resource('type_descuento', 'TypeDescuentoController');

    // Detalles de los descuentos
    Route::resource('type_detalle', 'TypeDetalleController');

    // Recursos de los detalles
    Route::resource('detalle', 'DetalleController');

    // Marcar los reportes como leidos
    Route::post("/report/{id}/markasread", "ReportController@markAsRead");
    // Generar reporte general de todos los trabajadores
    Route::post("/report-personal", "RptController@personalGeneral");


    // Importaciones
    Route::post("/descuento/{id}", "ImportController@descuento")->name("import.descuento");
    Route::post("/remuneracion/{id}", "ImportController@remuneracion")->name("import.remuneracion");

    // generar files txt
    Route::resource("file", "FileController");
    Route::get("file/judicial/{id}", "FileController@judicial");
    Route::get("file/enc/{year}/{mes}", "FileController@txtEnc");


    // api para obtener el historial
    Route::resource('historial', 'HistorialController')->except(['create', 'edit']);
    Route::get('historial/{id}/remuneracion', 'HistorialController@remuneracion');
    Route::get('historial/{id}/descuento', 'HistorialController@descuento');
    Route::get('historial/{id}/obligacion', 'HistorialController@obligacion');
    Route::get('historial/{id}/educacional', 'HistorialController@educacional');
    Route::get('historial/{id}/aportacion', 'HistorialController@aportacion');


    // api para procesar las remuneraciones
    Route::put('/type_remuneracion/{history}/all', 'RemuneracionController@updateAll');
    // api para procesar los descuentos
    Route::put('/type_descuento/{history}/all', 'TypeDescuentoController@updateAll');

    // api de typeEducacional
    Route::resource('/type_educacional', 'TypeEducacionalController')->except(['create', 'edit']);

    // api de  educacional
    Route::resource('/educacional', 'EducacionalController')->except(['create', 'edit']);
    Route::put('/educacional/{history}/historial', 'EducacionalController@historial');

});
