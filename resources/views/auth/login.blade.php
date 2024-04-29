@extends('layouts.app')

@section('content')
<div class="h-100 d-grid" style="position:absolute; top:0; left:0; width:100%;"> 
    <div class="col-md-7 m-auto">
        <form class="container" method="POST" action="{{ route('login') }}">
                @csrf
            <div class="row card" style="flex-direction:unset; box-shadow: 8px 8px 5px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);display:flex; align-content:center; justify-content: center;">
                <div  style="padding:3.5rem 0">
                    <div class="col-12 text-center">
                        <img class="login-img-top" src="/saude-beta/img/logo_topo_limpo_on.png">
                    </div>

                    <div class="col-10 offset-1">
                        <div class="form-group">
                            <label for="email" class="custom-label-form">Seu email</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                            @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-10 offset-1">
                        <div class="form-group">
                            <label for="password" class="custom-label-form">Sua senha</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                            @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-11 offset-1">
                        <div class="form-group row col">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="remember">salvar usuário neste computador</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-10 offset-1">
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-target px-5">
                                Acessar
                            </button>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-6 d-grid custom-border-left">
                    <img class="login-img-bottom" src="/saude-beta/img/med_vector.png">
                </div> --}}
            </div>

            <a class="btn btn-link btn-link-neutro col mt-3" href="/saude/register">
                Esqueceu sua senha?
            </a>
        </form>
    </div>
</div>

@endsection