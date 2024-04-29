
<!-- Modal -->
<div class="modal fade" id="addAreasIecModal" aria-labelledby="addAreasIecModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="agendaStatusModalLabel">Cadastrar Status da Agendas</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <input id='question' type='hidden'>
                <div class="modal-body">
                    <div class="container">
                        <div id='areas-saude' class="row">
                            {{-- @foreach($especialidades as $especialidade)
                                <div class="col-6">
                                    <div class="custom-control custom-switch">
                                        <input id="{{$especialidade->descr}}" name="{{$especialidade->descr}}" class="custom-control-input permissoes" type="checkbox" value="{{$especialidade->id}}">
                                        <label for="{{$especialidade->descr}}" class="custom-control-label" style="width:120px">{{$especialidade->descr}}</label>
                                    </div>
                                </div>
                            @endforeach  --}}
                        </div>
                    </div>
                </div>

                <div class="d-flex">
                    <button onclick="add_ids_modalidade();" class="btn btn-target mx-auto my-3 px-5">
                        Salvar
                    </button>
                </div>
        </div>
    </div>
</div>