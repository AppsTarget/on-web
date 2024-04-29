@extends('layouts.app')

@section('content')

<input id="id_pessoa" type="hidden" value={{ $id_pessoa }}>
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
            <input id="senha1" type="password" name="senha1" class="form-control" autocomplete="off" type="text" required>
        </div>
        <div class="col-8">
            <label for="senha2" class="custom-label-form">Confirme a senha *</label>
            <input id="senha2" type="password" name="senha2" class="form-control" autocomplete="off" type="text" required>
        </div>
        <div class="col-12" style="margin-top: 25px;">
            <div onclick="recuperarSenha()" class="d-flex" style="justify-content: center;background: #2e434e;color: white;height: 45px;padding: 3%;border-radius: 5px;">
                Confirmar
            </div>
        </div>
    </div>
</div>

<script>
    function recuperarSenha() {
    if (($('#senha1').val() === $('#senha2').val()) && $('#senha1').val() != ""){
        $.get(
            "/saude-beta/api/recriarSenha", {
                id_pessoa: $('#id_pessoa').val(),
                senha: $('#senha2').val()
            }, function(data, status) {
                console.log(data + ' | ' + status)
                alert('Senha alterada com sucesso \n Retorne ao Aplicativo')
                location.reload(true)
            }
        )
    }
    else {
        alert('As senhas não coincidem')
    }
}
</script>
@endsection
