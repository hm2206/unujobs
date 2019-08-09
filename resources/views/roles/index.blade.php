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
            <btn-role
                theme="btn-primary"
                redirect="{{ route('acceso.role')}}"
            >
                <i class="fas fa-plus"></i> Nuevo
            </btn-role>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Gestión de Roles
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Clave</th>
                                    <th>Nombre</th>
                                    <th>Modulos</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                    <tr>
                                        <td>{{ $role->id }}</td>
                                        <td>{{ $role->key }}</td>
                                        <td class="capitalize">{{ $role->name }}</td>
                                        <td>
                                            <div class="row">
                                                @foreach ($role->modulos as $modulo)
                                                    <div class="col-xs">
                                                        <button class="btn btn-sm ml-1 mb-1 btn-dark">
                                                            {{ $modulo->name }}
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <btn-role
                                                theme="btn-warning btn-sm"
                                                redirect="{{ route('acceso.role')}}"
                                                :datos="{{ $role }}"
                                            >
                                                <i class="fas fa-pencil-alt"></i>
                                            </btn-role>
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