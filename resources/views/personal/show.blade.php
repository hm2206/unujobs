@extends('layouts.app')

@section('content')
    
<div class="row">

    <div class="col-md-12 mb-4">
        <a href="{{ route('personal.index') }}" class="btn btn-danger">
            <i class="fas fa-arrow-left"></i> atrás
        </a>
        <a href="{{ route('personal.edit', $personal->id) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> atrás
        </a>
    </div>

    <div class="col-md-12">
        <div class="row">

            <div class="col-md-6" id="">
                <div class="form-group">
                    <label for="" class="form-control-label">Sede</label>
                    <span class="form-control">{{ $personal->sede->descripcion }}</span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="" class="form-control-label">Dependencia, Unidad Órganica y/o Área Solicitante</label>
                    <span class="form-control">{{ $personal->dependencia_txt }}</span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="" class="form-control-label">Denominación del requerimiento o cargo</label>
                    <span class="form-control">{{ $personal->cargo_txt }}</span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="" class="form-control-label">Cantidad </label>
                    <span class="form-control w-100">{{ $personal->cantidad }}</span>
                </div>
            </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Honorarios</label>
                        <span class="form-control">{{ $personal->honorarios }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Meta</label>
                        <span class="form-control">{{ $personal->meta->meta }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fuente</label>
                        {{-- <span class="form-control">{{ $personal->fuente_txt }}</span> --}}
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Lugar de prestación de servicio</label>
                        <span class="form-control">{{ $personal->lugar_txt }}</span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha Inicio <small>(Periodo de Contratación)</small></label>
                        <input type="date" class="form-control" name="fecha_inicio" value="{{ $personal->fecha_inicio }}" disabled>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Fecha Final <small>(Periodo de Contratación)</small></label>
                        <input type="date" class="form-control" name="fecha_inicio" value="{{ $personal->fecha_final }}" disabled>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="form-control-label">Dependencia Supervisora</label>
                        <span class="form-control">{{ $personal->supervisora_txt }}</span>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form-group">
                        <a href="{{ url($personal->file) }}" target="__blank" class="form-control text-center btn-primary">
                            Archivo word <i class="fas fa-file-word"></i>
                        </a>
                    </div>
                </div>
        </div>
    </div>

</div>

@endsection