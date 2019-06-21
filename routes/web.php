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

    //Trabajadores
    Route::resource('job', 'JobController');
    Route::get('/job/{id}/afectacion', 'JobController@afectacion')->name('job.afectacion');

    //Metas
    Route::resource('meta', 'MetaController');

});