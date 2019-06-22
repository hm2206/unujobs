<?php


Auth::routes();

Route::get('/', 'HomeController@index')->name('home');

//Controlador de Recursos Humano
Route::group(["prefix" => "RRHH"], function() {
    
    //Personal
    Route::resource("personal", "PersonalController");

    //sedes
    Route::resource("sede", "SedeController");

    //convocatorias
    Route::resource("convocatoria", "ConvocatoriaController");

    //Postulantes
    Route::resource("postulante", "PostulanteController");

    //Evalusiones
    Route::resource("evaluacion", "EvaluacionController");

});


Route::group(["prefix" => "planilla"], function() {

    Route::get('/', 'HomeController@planilla')->name('planilla');
    Route::get('/config', 'HomeController@configPlanilla')->name('planilla.config');

    //Trabajadores
    Route::resource('job', 'JobController');
    Route::get('job/{id}/remuneracion', 'JobController@remuneracion')->name('job.remuneracion');

    //Metas
    Route::resource('meta', 'MetaController');

    //gestion de cargos
    Route::resource('cargo', 'CargoController');
    Route::get('cargo/{id}/categoria', 'CargoController@categoria')->name('cargo.categoria');
    Route::post('cargo/{id}/categoria', 'CargoController@categoriaStore')->name('cargo.categoria.store');

});