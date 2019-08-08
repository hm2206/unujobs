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


});
