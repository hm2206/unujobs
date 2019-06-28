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
    Route::get('job/{id}/remuneracion', 'JobController@remuneracion')->name('job.remuneracion');
    Route::put('job/{id}/remuneracion', 'JobController@remuneracionUpdate')->name('job.remuneracion.update');

    //Metas
    Route::resource('meta', 'MetaController');

    //gestion de cargos
    Route::resource('cargo', 'CargoController');
    Route::get('cargo/{id}/categoria', 'CargoController@categoria')->name('cargo.categoria');
    Route::post('cargo/{id}/categoria', 'CargoController@categoriaStore')->name('cargo.categoria.store');

    //gestión de categoria
    Route::resource('categoria', 'CategoriaController');
    Route::get('categoria/{id}/concepto', 'CategoriaController@concepto')->name('categoria.concepto');
    Route::post('categoria/{id}/concepto', 'CategoriaController@conceptoStore')->name('categoria.concepto.store');

    //gestión de conceptos
    Route::resource('concepto', 'ConceptoController');

    //cronogramas
    Route::resource('cronograma', 'CronogramaController');
    Route::get('/cronograma/{id}/job', 'CronogramaController@job')->name('cronograma.job');
    Route::get('/cronograma/{id}/add', 'CronogramaController@add')->name('cronograma.add');
    Route::post('/cronograma/{id}/add', 'CronogramaController@addStore')->name('cronograma.add.store');

});


// Route::get("/test", function() {
//     $pdf = PDF::loadView("pdf.test");
//     return $pdf->stream();
// });