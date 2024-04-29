@extends('layouts.app')

@section('content')

<div style="margin-top: -100px; max-width: 100%">
    <div class="row">
        </div class="col-12">
            <img src="{{ asset('img/logo_topo_limpo_on.png') }}">
        </div>
        <div class="col-12">
            <h2 style="color: #2e434e;">Recuperação de senha</h2>
        </div>
        <div class="col-8" style="margin-top: 25px;">
            <label for="senha1" class="custom-label-form">Digite sua nova senha *</label>
            <input id="senha1" name="senha1" class="form-control" autocomplete="off" type="text" required>
        </div>
        <div class="col-8">
            <label for="senha2" class="custom-label-form">Confirme a senha *</label>
            <input id="senha2" name="senha2" class="form-control" autocomplete="off" type="text" required>
        </div>
        <div class="col-12" style="margin-top: 25px;">
            <div onclick="recuperarSenha()" class="d-flex" style="justify-content: center;background: #2e434e;color: white;height: 45px;padding: 3%;border-radius: 5px;">
                Confirmar
            </div>
        </div>
    </div>
    <div style="position: absolute;height: 100%;width: 100%;background: white;">
        <div>
            <div>
                <h2>Alterado com sucesso</h2>
            </div>
        </div>
    </div>
</div>


@endsection
