@extends('layouts.app')

@section('titulo')
    - Registro de Evaluación y Selección
@endsection

@section('link')
    Recursos Humanos
@endsection

@section('content')

<div class="col-md-12">
    <a href="{{ route('home') }}" class="btn btn-primary"><i class="material-icons">undo</i> atrás</a>
</div>
    
<div class="col-md-12">
    <form class="card">
        <h4 class="card-header">Registro de Evaluación y Selección</h4>
        <div class="card-body">
            <div class="row">
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Número de Requerimiento</label>
                        <input type="text" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Dependencia, sede y/u oficina solicitante</label>
                        <input type="text" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Denominación del requerimiento o cargo</label>
                        <input type="text" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Cantidad Requerida</label>
                        <input type="text" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Honorarios</label>
                        <input type="text" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Afectación presupuestal</label>
                        <input type="text" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Periodo de contratación</label>
                        <input type="text" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Lugar de prestación de servicio</label>
                        <input type="text" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Dependencia Supervisora</label>
                        <input type="text" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Perfil de puesto</label>
                        <input type="text" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Principales funciones</label>
                        <input type="text" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Lista de preguntas y respuestas</label>
                        <input type="text" class="form-control">
                    </div>
                </div>


                <div class="col-md-12 mt-4">
                    <button class="btn btn-primary">Guardar <i class="material-icons">save</i></button>
                </div>

            </div>        
        </div>
    </form>
</div>

@endsection