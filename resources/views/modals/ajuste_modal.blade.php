<!-- Modal -->
<div class="modal fade" id="ajusteModalidadesModal" aria-labelledby="gradeBloqueioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color" id="ajusteModalidadesModal">Cadastrar Bloqueio de Grade</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container">
                    @foreach($old_modalidades AS $modalidade)
                    <div style="width: 100%; display: flex; margin-bottom: 15px">
                        <div style="width:50%;">
                            <p>{{$modalidade->descr}}
                        </div>
                        <div class="col-6 form-group">
                            <input id="modalidade_antiga" type="hidden" value="{{ $modalidade->id }}">
                            <select id="modalidade_nova" class="custom-select">
                                @foreach($modalidades AS $m)
                                    <option value="{{$m->id}}">{{$m->descr}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="container">
                    <div class="row my-3">
                        <button id="botao-lote-agendamento-modal" class="btn btn-target m-auto px-5" type="button" onclick="atualizar_modalidades();">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function atualizar_modalidades() {
        antigas_ar = []
        novas_ar = []

        document.querySelectorAll("#modalidade_nova").forEach(el => {
            novas_ar.push(el.value)
        })
        document.querySelectorAll("#modalidade_antiga").forEach(el => {
            antigas_ar.push(el.value)
        })
        $.get('/saude-beta/bordero/atualizar-modalidades', {
            antigas: antigas_ar,
            novas:   novas_ar
        }, function(data,status){
            if (data == 'true') {
                alert('modalidades relacionadas com sucesso')
                $("#ajusteModalidadesModal").modal('hide')
            }
        })
    }
</script>