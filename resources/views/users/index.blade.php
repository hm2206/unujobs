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
            <btn-user
                theme="btn-primary"
                redirect="{{ route('acceso.user')}}"
            >
                <i class="fas fa-plus"></i> Nuevo
            </btn-user>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Gestión de Usuarios
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th>Nombre Completo</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td class="capitalize">{{ $user->nombre_completo }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <div class="row">
                                                @foreach ($user->roles as $role)
                                                    <div class="col-md-6">
                                                        <button class="btn btn-sm btn-dark">
                                                            {{ $role->name }}
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <btn-user
                                                theme="btn-warning btn-sm"
                                                redirect="{{ route('acceso.user')}}"
                                                :datos="{{ $user }}"   
                                            >
                                                <i class="fas fa-pencil-alt"></i>
                                            </btn-user>
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