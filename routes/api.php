<?php


Route::group(["prefix" => 'v1'], function() {

    Route::get('planilla', 'ApiRest@planilla');
    Route::get('planilla/{id}', 'ApiRest@planillaShow');
    Route::get('cargo/{id}', 'ApiRest@cargoShow');
    Route::get('meta', 'ApiRest@meta');

    
    //Recursos para generar boletas
    Route::get("/boleta/{slug}", "JobController@boleta");

    // Crear planillas x mes
    Route::post("/cronograma", "CronogramaController@store");
    // actualizar planilla x mes
    Route::put("/cronograma/{id}", "CronogramaController@update");
    // obtener trabajadores para poder agregar a la planilla x mes
    Route::get("/cronograma/{id}/add", "CronogramaController@add");
    // agregar trabajadores a la planilla x mes
    Route::post("/cronograma/{id}/add", "CronogramaController@addStore");

});
