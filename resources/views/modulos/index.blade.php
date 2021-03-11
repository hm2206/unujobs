@extends('layouts.app')

@section('titulo')
    - Trabajadores
@endsection

@section('link')
    Accesos
@endsection

@section('content')


    <div class="row">

        <div class="col-md-12 mb-2">
            <btn-modulo
                theme="btn-primary"
                redirect="{{ route('acceso.modulo') }}"
            >
                <i class="fas fa-plus"></i> Nuevo
            </btn-modulo>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Gestión de Modulos
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Nombre</th>
                                    <th>Sub-Modulos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($modulos as $modulo)
                                    <tr>
                                        <td>{{ $modulo->id }}</td>
                                        <td class="capitalize">{{ $modulo->name }}</td>
                                        <td>
                                            <div class="row">
                                                @foreach ($modulo->modulos as $mod)
                                                    <btn-modulo
                                                        theme="btn-sm btn-dark ml-1 mb-1"
                                                        redirect="{{ route('acceso.modulo') }}"
                                                        :datos="{{ $mod }}"
                                                    >
                                                        {{ $mod->name }} 
                                                        <i class="fas fa-arrow-right"></i>
                                                        {{ $mod->ruta }}
                                                    </btn-modulo>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <btn-modulo
                                                theme="btn-warning btn-sm"
                                                redirect="{{ route('acceso.modulo') }}"
                                                :datos="{{ $modulo }}"
                                            >
                                                <i class="fas fa-pencil-alt"></i>
                                            </btn-modulo>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>


@endsection