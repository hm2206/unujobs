<?php


Auth::routes();

Route::get('/', 'HomeController@index')->name('home');

//Controlador de Recursos Humano
Route::group(["prefix" => "RRHH"], function() {
    
    //Personal
    Route::resource("personal", "PersonalController");
    Route::post('personal/{id}/aceptar', 'PersonalController@aceptar')->name('personal.aceptar');
    Route::get('personal/{id}/pdf', 'PersonalController@pdf')->name('personal.pdf');

    //sedes
    Route::resource("sede", "SedeController");

    //convocatorias
    Route::resource("convocatoria", "ConvocatoriaController");
    Route::post("convocatoria/{id}/aceptar", "ConvocatoriaController@aceptar")->name("convocatoria.aceptar");
    Route::get("convocatoria/{id}/pdf", "ConvocatoriaController@pdf")->name("convocatoria.pdf");

    //Postulantes
    Route::resource("postulante", "PostulanteController");

    //Evalusiones
    Route::resource("evaluacion", "EvaluacionController");

});


Route::group(["prefix" => "planilla", "middleware" => ["auth"]], function() {

    Route::get('/', 'HomeController@planilla')->name('planilla');

    //Trabajadores
    Route::resource('job', 'JobController');
    Route::get('job/{id}/remuneracion', 'JobController@remuneracion')->name('job.remuneracion');
    Route::put('job/{id}/remuneracion', 'JobController@remuneracionUpdate')->name('job.remuneracion.update');
    Route::get('job/{id}/descuento', 'JobController@descuento')->name('job.descuento');
    Route::put('job/{id}/descuento', 'JobController@descuentoUpdate')->name('job.descuento.update');
    Route::get('job/{id}/obligacion', 'JobController@obligacion')->name('job.obligacion');
    Route::get('job/{id}/boleta', 'JobController@boleta')->name('job.boleta');
    Route::post('job/{id}/boleta', 'JobController@boletaStore')->name('job.boleta.store');

    //Metas
    Route::resource('meta', 'MetaController');

    //gestion de cargos
    Route::resource('cargo', 'CargoController');
    Route::get('cargo/{id}/categoria', 'CargoController@categoria')->name('cargo.categoria');
    Route::post('cargo/{id}/categoria', 'CargoController@categoriaStore')->name('cargo.categoria.store');
    Route::get('cargo/{id}/config', 'CargoController@config')->name('cargo.config');
    Route::post('cargo/{id}/config', 'CargoController@configStore')->name('cargo.config.store');

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

    
    //Descuentos
    Route::resource('descuento', 'DescuentoController');
    Route::get('descuento/{id}/config', 'DescuentoController@config')->name('descuento.config');
    Route::post('descuento/{id}/config', 'DescuentoController@configStore')->name('descuento.config.store');


});



//Reportes
Route::group(["prefix" => "export"], function() {

    Route::get("cronograma/{id}/pdf", "ExportCronogramaController@pdf")->name('export.cronograma.pdf');
    Route::get("reporte/mes/{mes}/year/{year}/adicional/{adicional}", 'ExportCronogramaController@reporte')->name('export.reporte');

});



//Bolsa de trabajo
Route::group(["prefix" => "convocatorias-de-trabajo"], function() {
   
    Route::get("/", "BolsaController@index")->name('bolsa.index');
    Route::get("/{numero}/cargo/{titulo}", "BolsaController@show")->name('bolsa.show');
    Route::get("/{numero}/cargo/{titulo}/postular", "BolsaController@postular")->name('bolsa.postular');
    Route::post("/auth", "BolsaController@authenticar")->name('bolsa.auth');

});