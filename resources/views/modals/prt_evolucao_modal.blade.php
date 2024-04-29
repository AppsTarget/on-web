<!-- Modal -->
<div class="modal fade" id="criarEvolucaoModal" aria-labelledby="criarEvolucaoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="criarEvolucaoModalLabel">Cadastrar Evolução</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <form id="form-salvar-evolucao" class="col-12 row" action="/saude-beta/evolucao/salvar" method="POST" onsubmit="salvar_evolucao(event)">
                            @csrf
                            <input id="id_evolucao" name="id_evolucao" type="hidden">
                            @if (getEmpresaObj()->tipo == 'M') {{-- todo_ verificar --}}
                            <div class="titulo-evolucao-mobile col-8 mt-2">
                                <label for="titulo-evolucao" class="custom-label-form">Título</label>
                                <input id="titulo-evolucao" name="titulo_evolucao" class="form-control" autocomplete="off" type="text">
                            </div>
                            {{-- <div class="col-8 form-group form-search">
                                <label for="cid_descr" class="custom-label-form">Associado</label>
                                <input id="cid_descr" 
                                            name="cid_descr" 
                                            class="form-control autocomplete" 
                                            placeholder="Digitar CID..." 
                                            data-input="#cid_id" 
                                            data-table="pessoa" 
                                            data-column="nome_fantasia" 
                                            data-filter_col="cid" 
                                            data-filter="S" 
                                            type="text" 
                                            autocomplete="off" 
                                            required="">
                                <input id="cid_id" name="cid_id" type="hidden">
                            </div> --}}
                            <div class="col-4 mt-2 tipo-evolucao-mobile">
                                
                                <label for="    " class="custom-label-form">Tipo de Evolução</label>
                                <select onchange="changeTipoEvolucao();" id="id_evolucao_tipo" name="id_evolucao_tipo" class="custom-select">
                                </select>
                            </div>
                            <div class="col-6 mt-2 especialidade-evolucao-mobile">
                                <label for="especialidade" class="custom-label-form">Área da saúde *</label>
                                <select id="especialidade" name="especialidade" class="form-control custom-select">
                                    @foreach ($especialidades_ as $especialidade)
                                        <option value="{{ $especialidade->id }}">
                                            {{ $especialidade->descr }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- <div class="col-1 d-grid p-0">
                                <a class="mt-auto mb-2" href="#" onclick="buscar_cid($('#cid').val())">
                                    <i class="my-icon fal fa-external-link"></i>
                                </a>
                            </div> --}}
                            @endif

                            <div class="col-3 mt-2 hora-evolucao-mobile">
                                <label for="hora" class="custom-label-form">Hora</label>
                                <input id="hora" name="hora" class="form-control timing" autocomplete="off" type="text" value="{{ date('H:i') }}">
                            </div>
                            <div class="col-3 mt-2 data-evolucao-mobile">
                                <label for="data" class="custom-label-form">Data</label>
                                <input id="data" name="data" class="form-control date" autocomplete="off" type="text" value="{{ date('d/m/Y') }}">
                            </div>
                            
                            
                            <div id="div-id-corpo" class="col-4 mt-2 lll" style="display: none">
                                <label for="id_parte_corpo" class="custom-label-form" style="">
                                    Parte do Corpo
                                </label>
                                <select id="id_parte_corpo" name="id_parte_corpo" class="custom-select" style="">
                                </select>
                            </div>

                            <div class="col-10 cid-evolucao-mobile" style="padding-top: 0.6%;padding-top: 0.6%;">
                                <div class="col" style="padding: 0" style="min-width: 86%;display: flex">
                                    <label for="cid" class="custom-label-form">CID</label>
                                    <input id="cid" 
                                                name="cid" 
                                                class="form-control autocomplete" 
                                                placeholder="Digitar CID..." 
                                                data-input="#cid_id" 
                                                data-table="cid" 
                                                data-column="nome"  
                                                data-filter="S" 
                                                type="text" 
                                                autocomplete="off" 
                                                onchange=""
                                                >
                                    <input id="cid_id" name="cid_id" type="hidden">
                                </div>
                            </div>
                            <div id="btns-cid" class="col-md-2 form-group" style="margin:0; display: flex">
                                {{-- <button class="btn btn-danger mt-auto mr-1" style="height:calc(1.5em + 0.75rem + 8px)" onclick="deletar_CID_evolucao($(this)); return false;" type="button">
                                    <i class="my-icon fas fa-trash"></i>
                                </button> --}}
                                <button class="btn btn-success mt-auto" style="height:calc(1.5em + 0.75rem + 8px);width:100%" onclick="controlCidEvolucao($('.cid-evolucao-mobile #cid'))" type="button">
                                    <i class="my-icon fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="col-12 mt-2">
                                <label class="custom-label-form">Diagnóstico</label>
                                <textarea id="diagnostico" name="diagnostico" class="summernote"></textarea>
                            </div>

                            <div class="col-12 d-grid">
                                <div class="col-6">
                                    <div class="custom-control custom-switch">
                                        <input id="publico-notificacao" name="publico-notificacao" class="custom-control-input" type="checkbox">
                                        <label for="publico-notificacao" class="custom-control-label">Privado</label>
                                        <img id="travar-escolha-ag-status" class="ico-info" style="position: relative;top: -2px;left: 0px;" src="/saude-beta/img/icone-de-informacao.png">
                                    </div>
                                </div>
                                <button class="btn btn-target my-3 mx-auto px-5" type="submit">Salvar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .lll{
        max-width: 35%
    }
</style>
<script>
    window.addEventListener('load', () => {
        $('#criarEvolucaoModal .note-editable').empty()
    })
</script>