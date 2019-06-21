@extends('layouts.guest')

@section('content')
    
<div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card">
                <h4 class="card-header text-center">Iniciar sesión</h4>

                    <form method="POST" class="card-body pl-5 pr-5" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group">
                            <label for="email" class="col-form-label text-md-right">Cuenta de usuario</label>

                            <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autofocus>

                            @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror

                        </div>

                        <div class="form-group">
                            <label for="password" class="col-form-label text-md-right">Contraseña</label>

           
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            
                        </div>


                        <div class="form-group mt-5">
                            <div class="">
                                <button type="submit" class="btn btn-primary btn-block btn-lg">
                                    Entrar
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

            </div>
        </div>
    </div>

@endsection