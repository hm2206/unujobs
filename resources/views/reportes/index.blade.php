@extends('layouts.app')

@section('titulo')
    - Descuentos
@endsection

@section('link')
    Planilla
@endsection

@section('content')


<div class="col-md-12">

    <div class="card">
        <div class="card-header card-header-danger">
            <h5 class="card-title">AFPNET Y PDT-PLAME</h5>
            <p class="card-category">Sub Módulo de reportes</p>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead class="text-primary">
                        <tr>
                            <th>#ID</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    
                </table>
            </div>
        </div>
    </div>        

</div>
@endsection