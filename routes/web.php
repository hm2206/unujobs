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
    Route::get('/etapa/{id}/convocatoria/{convocatoria}/pdf', 'EtapaController@pdf')->name('etapa.pdf');

});


Route::group(["prefix" => "planilla", "middleware" => ["auth"]], function() {

    //Trabajadores
    Route::resource('job', 'JobController');
    Route::get('job/{id}/boleta', 'JobController@boleta')->name('job.boleta');
    Route::post('job/{id}/boleta', 'JobController@boletaStore')->name('job.boleta.store');

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
    Route::post('/etapa/{id}', "ImportController@etapa")->name("import.etapa");
    Route::post("/meta", "ImportController@meta")->name("import.meta");
    Route::post("/categoria", "ImportController@categoria")->name("import.categoria");
    Route::post("/categoria/conceptos", "ImportController@workConfig")->name("import.categoria.conceptos");
    Route::post("/work/config", "ImportController@workConfig")->name("import.work.config");

});



//Bolsa de trabajo
Route::group(["prefix" => "convocatorias-de-trabajo"], function() {
   
    Route::get("/", "BolsaController@index")->name('bolsa.index');
    Route::get("/{numero}/cargo/{titulo}", "BolsaController@show")->name('bolsa.show');
    Route::get("/{numero}/cargo/{titulo}/postular", "BolsaController@postular")->name('bolsa.postular');
    Route::post("/auth", "BolsaController@authenticar")->name('bolsa.auth');
    Route::get("/{id}/personal/{personalID}/pdf", "BolsaController@resultados")->name('bolsa.resultados');

});


 // Accesos
 Route::group(["prefix" => "accesos", "middleware" => ["auth"]], function() {
   
    Route::get("/user", "AccesoController@user")->name("acceso.user");
    Route::get("/modulo", "AccesoController@modulo")->name("acceso.modulo");

});



// Reportes
Route::group(["prefix" => "reportes", "middleware" => ["auth"]], function() {
   
    Route::get("/", "ReportController@index")->name("report.index");

});



//notificaciones
Route::get("user/unread/count", "UserController@countUnread")->middleware('auth');
Route::get("user/unread", "UserController@unread")->middleware('auth');
Route::post("user/{notify}/markasread", "UserController@markAsRead")->middleware('auth');
// Recuperar sesión
Route::post("user/recovery", "UserController@recovery");


Route::get('test', function() {

    $pdf = \PDF::loadView('pdf.resumen');
    $pdf->setPaper('a4', 'landscape');
    return $pdf->stream();

});
