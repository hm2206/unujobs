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
    Route::get('job/{id}/descuento', 'JobController@descuento')->name('job.descuento');
    Route::put('job/{id}/descuento', 'JobController@descuentoUpdate')->name('job.descuento.update');
    Route::get('job/{id}/obligacion', 'JobController@obligacion')->name('job.obligacion');

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
    Route::get('categoria/{id}/config', 'CategoriaController@config')->name('categoria.config');
    Route::post('categoria/{id}/config', 'CategoriaController@configStore')->name('categoria.config.store');

    //gestión de conceptos
    Route::resource('concepto', 'ConceptoController');

    //cronogramas
    Route::resource('cronograma', 'CronogramaController');
    Route::get('/cronograma/{id}/job', 'CronogramaController@job')->name('cronograma.job');
    Route::get('/cronograma/{id}/add', 'CronogramaController@add')->name('cronograma.add');
    Route::post('/cronograma/{id}/add', 'CronogramaController@addStore')->name('cronograma.add.store');

});



//Reportes
Route::group(["prefix" => "export"], function() {

    Route::get("cronograma/{id}/pdf", "ExportCronogramaController@pdf")->name('export.cronograma.pdf');

});