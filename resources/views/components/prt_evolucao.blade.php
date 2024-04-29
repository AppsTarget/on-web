<div id="evolucoes_avulsas_list" class="container-fluid card p-3">
    <h5 class="w-100 mb-3 btn-link-target">Evoluções Anteriores</h5>
    <div id='filtro-areas-saude'></div>
    <button id="button-vitruviano" type="button" data-toggle="modal" data-target="#criarVitruviano" ontouchstart="#criarVitruviano">
            <img id="button-vitruviano-png"src="/saude-beta/img/vitruvian_circle.png">
    </button>
    <ul id="table-prontuario-evolucao" class="timeline">
    </ul>
</div>

<button class="btn btn-primary custom-fab" type="button" onclick="criarEvolucao()">
    <i class="my-icon fas fa-plus"></i>
</button>
<script>
    function criarEvolucao() {
        t = new Date();

        data = insertZero(t.getDate(), 2) + '/' + insertZero((t.getMonth()+ 1), 2) + '/' + t.getFullYear()
        hora = insertZero(t.getHours(), 2) + ':' + insertZero(t.getMinutes(), 2)
        $('#criarEvolucaoModal #data').val(data)
        $('#criarEvolucaoModal #hora').val(hora)

        $('#criarEvolucaoModal #id_evolucao').val(0)
        $('#criarEvolucaoModal #titulo-evolucao').val('')
        $('#criarEvolucaoModal #id_evolucao_tipo').val('')
        $('#criarEvolucaoModal #especialidade').val('')
        $('#criarEvolucaoModal #id_parte_corpo').val('')
        $('#criarEvolucaoModal #cid').val('')
        $('#criarEvolucaoModal #diagnostico').val('')
        $('#criarEvolucaoModal #publico-notificacao').val('')
        $('#criarEvolucaoModal').modal('show')
    }
</script>
@include('.modals.prt_evolucao_modal')
@include('modals.encaminhamento_modal')


