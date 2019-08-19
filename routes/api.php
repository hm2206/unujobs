<?php


Route::group(["prefix" => 'v1'], function() {

    Route::get('planilla', 'ApiRest@planilla');
    Route::get('planilla/{id}', 'ApiRest@planillaShow');
    Route::get('cargo/{id}', 'ApiRest@cargoShow');
    Route::get('meta', 'ApiRest@meta');
    
    
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


    // registrar un nuevo cargo
    Route::post("/cargo", "CargoController@store");
    // actualizar un cargo determinado
    Route::put("/cargo/{id}", "CargoController@update");

    // registrar una nueva categoria
    Route::post("/categoria", "CategoriaController@store");
    // actualizar una categoria determinada
    Route::put("/categoria/{id}", "CategoriaController@update");

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

    // Recursos de modulos
    Route::resource("modulo", "ModuloController");

    // Recursos de los roles
    Route::resource("role", "RoleController");

    // Guaradar nuevo usuario
    Route::post("/user", "UserController@store");
    Route::put("/user/{id}", "UserController@update");
    Route::get("/menu/{id}", "UserController@menu");
    // Liquidar trabajador
    Route::post("/liquidar", "LiquidarController@store");


    Route::post("/export/mef/{year}/{mes}", "ExportController@mef");

    Route::post("/export/alta-baja/{year}/{mes}", "ExportController@altaBaja");

    Route::post("/export/resumen/{year}/{mes}", "ExportController@resumen");

    // Remuneracion de los trabajadores
    Route::get('work/{id}/remuneracion', 'JobController@remuneracion');
    Route::put('work/{id}/remuneracion', 'JobController@remuneracionUpdate');
    Route::get('work/{id}/descuento', 'JobController@descuento');
    Route::put('work/{id}/descuento', 'JobController@descuentoUpdate');
    Route::get('work/{id}/obligacion', 'JobController@obligacion');
    Route::get('work/{id}/info', 'JobController@informacion');
    Route::post('work/{id}/config', 'JobController@configStore');
    Route::delete('work/{id}/config', 'JobController@configDelete');
    Route::post('work/{id}/sindicato', 'JobController@sindicatoStore');
    Route::get('work/{id}/retencion', 'JobController@retencion');
    Route::post('work/{id}/retencion', 'JobController@retencionStore');

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

});
