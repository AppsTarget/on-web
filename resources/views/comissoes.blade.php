@extends('layouts.app')

@section('content')
    @include('components.main-toolbar')

    <div class="container-fluid h-100 px-3 py-4">
        <div class="row">
            <h3 class="col header-color mb-3">
                Baixa de Comissões
            </h3>
            <div id="" class="input-group col-12 mb-3" data-table="#table-plano_tratamento">
                <div class="col-2 form-group">
                    <select id="empresa" class="custom-select">
                        <option value="0">Selecionar Empresa</option>
                        @foreach ($empresas as $empresa)
                            <option value="{{ $empresa->id }}">{{ $empresa->descr }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-4 form-group">
                    <input id="paciente_nome" name="paciente_nome" class="form-control autocomplete"
                        placeholder="Digitar Nome do associado..." data-input="#paciente_id" data-table="pessoa"
                        data-column="nome_fantasia" data-filter_col="paciente" data-filter="S" type="text"
                        autocomplete="off" required>
                    <input id="paciente_id" name="paciente_id" type="hidden">
                </div>
                
                <div class="col-2 form-group">
                    <input id="data-inicial" name="data" class="form-control date" autocomplete="off" type="text"
                        placeholder="__/__/____" required>
                </div>
                <div class="col-2 form-group">
                    <input id="data-final" name="data" class="form-control date" autocomplete="off" type="text"
                        placeholder="__/__/____" required>
                </div>
                <div class="col-2 form-group">
                    <select id="venc-ou-lanc" class="custom-select">
                        <option value="vencimento">Vencimento</option>
                        <option value="lancamento">Lançamento</option>
                    </select>
                </div>


                <div class="col-2 form-group">
                    <input id="contrato" class="form-control" type='number' placeholder="Contrato...">
                </div>
                
                <div class="col-4 custom-control custom-checkbox custom-control-inline mr-2"
                    style='position: relative;top: 8.6%;left: 5%;'>
                    <input id="liquidados" name="liquidados" style="width: 17px;margin: -1.5% 5px 0px -6%;" type="checkbox">
                    <span style='color: #001284;position: relative;top:34%'>Mostrar títulos liquidados</span>
                </div>
                <div class="col-2"></div>
                <div class="col-3 text-right" style='margin-top: 2%;flex: 0 0 32.7%;max-width: 33%;'>
                    <button type="button" class="btn btn-target px-5" style="width:100%" id="imprimir" onclick="pesquisarTitulosReceber()"
                        style="border-radius: 2px;color: #e8e8e8;background-color: #02366e;font-size:15px">Pesquisar</button>
                </div>
            </div>
        </div>
        <div class="d-flex" style="justify-content: space-between;width: 29%;">
            <div class="d-flex" style="align-items: center">
                <img style="width: 27px; height: 27px" src="{{ asset('img/vencido.png') }}">
                <p style="font-size: 13px;margin: 5px 0 0 0;">Títulos Vencidos</p>
            </div>
            <div class="d-flex" style="align-items: center">
                <img style="width: 19px; height: 19px" src="{{ asset('img/pendente.png') }}">
                <p style="font-size: 13px;margin: 5px 0 0 7px;">Titulos Pendentes</p>
            </div>
            <div class="d-flex" style="align-items: center">
                <img style="width:18px; height: 18px" src="{{ asset('img/pago.png') }}">
                <p style="font-size: 13px;margin: 5px 0 0 7px;">Títulos Pagos</p>
            </div>
        </div>
        <div class="custom-table card">
            <div class="table-header-scroll">
                <table>
                    <thead>
                        <tr class="sortable-columns" for="#table-plano_tratamento">
                            <th style='width:5%; min-width: 8%; max-width: 8%' class="text-left">Status</th>
                            <th style='width:5%;  min-width: 5%; max-width: 5%' class="text-left">Contrato</th>
                            <th style="width:19%; min-width: 22%; max-width: 22%" class="text-left">Nome</th>
                            <th style="width:10%; min-width: 10%; max-width: 10%" class="text-left">Pagamento</th>
                            <th style="width:10%; min-width: 10%; max-width: 10%" class="text-right">Parcela</th>
                            <th style="width:10%; min-width: 10%; max-width: 10%" class="text-right">Valor</th>
                            {{-- <th width="10%">Acréscimo</th> --}}
                            <th style="width:10%; min-width: 10%; max-width: 10%" class='text-right'>Valor Pago</th>
                            <th style="width:10%;" class="text-right">Dt. Lanc</th>
                            <th style="width:10%;" class="text-right">Dt. Venc</th>
                            <th style="width:10%;" class="text-right"></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="table-body-scroll custom-scrollbar">
                <table id="table-plano_tratamento" class="table table-hover">
                    <tbody>
                        
                    </tbody>
                </table>
            </div>
            <div style="height: 50px"></div>
        </div>
    </div>

    <button class="btn btn-primary custom-fab" type="button" onclick="$('#cadastrarTituloReceberModal').modal('show')">
        <i class="my-icon fas fa-plus"></i>
    </button>

    @if (App\Pessoa::find(Auth::user()->id_profissional)->administrador != 'S' || Auth::user()->id_profissional == 28480001071 || Auth::user()->id_profissional == 14672 || Auth::user()->id_profissional == 429000000)
    <script>
        window.addEventListener("load", function() {
            location.href = "/saude-beta/"
        });
    </script>
    @endif

    @include('.modals.cadastrar_titulo_receber_modal')
    @include('.modals.ver_titulo_receber_modal')
    @include('.modals.baixar_titulo_receber_modal')
@endsection
