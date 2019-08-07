<?php


Route::group(["prefix" => 'v1'], function() {

    Route::get('planilla', 'ApiRest@planilla');
    Route::get('planilla/{id}', 'ApiRest@planillaShow');
    Route::get('cargo/{id}', 'ApiRest@cargoShow');
    Route::get('meta', 'ApiRest@meta');

    
    //Recursos para generar boletas
    Route::get("/boleta/{slug}", "JobController@boleta");

});
