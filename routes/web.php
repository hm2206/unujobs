<?php

// authenticacion de perdida de sesion
Route::post('current', 'UserController@current');


// ruta de authenticacion
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
    Route::get("convocatoria/{id}/etapas", "ConvocatoriaController@etapas")->name("convocatoria.etapas");

    //Postulantes
    Route::resource("postulante", "PostulanteController");
    Route::post("postulante/{id}/cv", "PostulanteController@upload")->name('postulante.cv');


    //Etapas
    Route::resource('etapa', 'EtapaController');
    Route::get('etapa/{id}/convocatoria/{convocatoria}/pdf', 'EtapaController@pdf')->name('etapa.pdf');

});


Route::group(["prefix" => "planilla", "middleware" => ["auth"]], function() {

    //Trabajadores
    Route::resource('job', 'JobController');
    Route::get('job/{id}/remuneracion', 'JobController@remuneracion')->name('job.remuneracion');
    Route::put('job/{id}/remuneracion', 'JobController@remuneracionUpdate')->name('job.remuneracion.update');
    Route::get('job/{id}/descuento', 'JobController@descuento')->name('job.descuento');
    Route::put('job/{id}/descuento', 'JobController@descuentoUpdate')->name('job.descuento.update');
    Route::get('job/{id}/obligacion', 'JobController@obligacion')->name('job.obligacion');
    Route::get('job/{id}/boleta', 'JobController@boleta')->name('job.boleta');
    Route::post('job/{id}/boleta', 'JobController@boletaStore')->name('job.boleta.store');
    Route::get('job/{id}/config', 'JobController@config')->name('job.config');
    Route::post('job/{id}/config', 'JobController@configStore')->name('job.config.store');
    Route::post('job/{id}/sindicato', 'JobController@sindicatoStore')->name('job.sindicato.store');

    //Obligaciones
    Route::resource('obligacion', 'ObligacionController');

    //Metas
    Route::resource('meta', 'MetaController');

    //gestion de cargos
    Route::resource('cargo', 'CargoController');
    Route::get('cargo/{id}/categoria', 'CargoController@categoria')->name('cargo.categoria');
    Route::post('cargo/{id}/categoria', 'CargoController@categoriaStore')->name('cargo.categoria.store');
    Route::delete('cargo/{id}/categoria/delete', 'CargoController@categoriaDelete')->name('cargo.categoria.delete');
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
    // Route::post('/cronograma/{id}/add', 'CronogramaController@addStore')->name('cronograma.add.store');

    
    //Descuentos
    Route::resource('descuento', 'DescuentoController');
    Route::get('descuento/{id}/config', 'DescuentoController@config')->name('descuento.config');
    Route::post('descuento/{id}/config', 'DescuentoController@configStore')->name('descuento.config.store');

    //Remuneraciones
    Route::resource('remuneracion', 'TypeRemuneracionController');


    //AFPS
    Route::resource('afp', 'AfpController');


});



//Reportes
Route::group(["prefix" => "export"], function() {

    Route::get("cronograma/{id}/pdf", "ExportCronogramaController@pdf")->name('export.cronograma.pdf');
    Route::get("reporte/mes/{mes}/year/{year}/adicional/{adicional}", 'ExportCronogramaController@reporte')->name('export.reporte');


    //exportaciones generales
    Route::post("work", "ExportController@work")->name("export.work");
    Route::post("meta", "ExportController@meta")->name("export.meta");
    Route::post("cargo", "ExportController@cargo")->name("export.cargo");
    Route::post("categoria", "ExportController@categoria")->name("export.categoria");
    Route::post("cronograma/{id}", "ExportController@cronograma")->name("export.cronograma");

});


// Importaciones de datos en excel
Route::group(["prefix" => "import"], function() {

    Route::post("/work", "ImportController@work")->name("import.work");
    Route::post("/remuneracion/{id}", "ImportController@remuneracion")->name("import.remuneracion");
    Route::post("/descuento/{id}", "ImportController@descuento")->name("import.descuento");

});



//Bolsa de trabajo
Route::group(["prefix" => "convocatorias-de-trabajo"], function() {
   
    Route::get("/", "BolsaController@index")->name('bolsa.index');
    Route::get("/{numero}/cargo/{titulo}", "BolsaController@show")->name('bolsa.show');
    Route::get("/{numero}/cargo/{titulo}/postular", "BolsaController@postular")->name('bolsa.postular');
    Route::post("/auth", "BolsaController@authenticar")->name('bolsa.auth');
    Route::get("/{id}/personal/{personalID}/pdf", "BolsaController@resultados")->name('bolsa.resultados');

});



//notificaciones
Route::get("user/unread/count", "UserController@countUnread")->middleware('auth');
Route::get("user/unread", "UserController@unread")->middleware('auth');
Route::post("user/{notify}/markasread", "UserController@markAsRead")->middleware('auth');





