@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div id="criar-orcamento" class="container-fluid h-100 p-3">
    <div class="row">
        <h3 class="col header-color mb-3">Novo Orçamento</h3>
        <h5 class="col d-grid">
            <span class="ml-auto my-auto">
                {{ date('d/m/Y') . ' às ' . date('H:i') }}
            </span>
        </h5>
    </div>
    <form class="container-fluid card p-3 mb-4" action="/saude-beta/pedido/salvar" method="POST">
        <div class="row">
            <h5 class="col">
                Número do Pedido
            </h5>
            <h5 class="col text-right">
                Status
            </h5>
        </div>
        
        <div class="row">
            <div class="col-md-6 form-group form-search">
                <label for="paciente_nome" class="custom-label-form">Associado</label>   
                <input id="paciente_nome"
                    name="paciente_nome"  
                    class="form-control autocomplete" 
                    placeholder="Digitar Nome do Associado..."
                    data-input="#paciente_id"
                    data-table="pessoa" 
                    data-column="nome_fantasia" 
                    data-filter_col="paciente"
                    data-filter="S"
                    type="text" 
                    autocomplete="off"
                    required>
                <input id="paciente_id" name="paciente_id" type="hidden">
            </div>
     
            <div class="col-md-4 form-group">
                <label for="id_convenio" class="custom-label-form">Convênio</label>
                <select id="id_convenio" name="id_convenio" class="custom-select">
                    <option value="0">Selecionar Convênio...</option>
                    @foreach ($convenios as $convenio)
                        <option value="{{ $convenio->id }}">
                            {{ $convenio->descr }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="data" class="custom-label-form">Validade</label>
                <input id="data" name="data" class="form-control date" autocomplete="off" type="text" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 form-group form-search">
                <label for="profissional_exa_nome" class="custom-label-form">
                    Profissional Examinador
                </label>   
                <input id="profissional_exa_nome"
                    name="profissional_exa_nome"  
                    class="form-control autocomplete" 
                    placeholder="Digitar Nome do Profissional..."
                    data-input="#profissional_exa_id"
                    data-table="pessoa" 
                    data-column="nome_fantasia" 
                    data-filter_col="colaborador"
                    data-filter="P"
                    type="text" 
                    autocomplete="off"
                    required>
                <input id="profissional_exa_id" name="profissional_exa_id" type="hidden">
            </div>
        </div>

        <hr>

        <div class="row">
            <h4 class="col-12 header-color">
                procedimentos
            </h4>

            <div class="col-md-6 form-group form-search">
                <label for="profissional_exe_nome" class="custom-label-form">
                    Profissional Examinador
                </label>   
                <input id="profissional_exe_nome"
                    name="profissional_exe_nome"  
                    class="form-control autocomplete" 
                    placeholder="Digitar Nome do Membro..."
                    data-input="#profissional_exe_id"
                    data-table="pessoa" 
                    data-column="nome_fantasia" 
                    data-filter_col="colaborador"
                    data-filter="P"
                    type="text" 
                    autocomplete="off"
                    required>
                <input id="profissional_exe_id" name="profissional_exe_id" type="hidden">
            </div>

            <div class="col-md-6 form-group form-search">
                <label for="procedimento_descr" class="custom-label-form">
                    procedimento
                </label>   
                <input id="procedimento_descr"
                    name="procedimento_descr"  
                    class="form-control autocomplete" 
                    placeholder="Digitar Nome do procedimento..."
                    data-input="#procedimento_id"
                    data-table="procedimento" 
                    data-column="descr" 
                    data-filter_col="id_emp"
                    data-filter="{{ getEmpresa() }}"
                    type="text" 
                    autocomplete="off"
                    required>
                <input id="procedimento_id" name="procedimento_id" type="hidden">
            </div>

            <div class="col-md-2 form-group">
                <label for="quantidade" class="custom-label-form">Quantidade</label>
                <input id="quantidade" name="quantidade" class="form-control" type="number" required>
            </div>

            <div class="col-md-3 form-group">
                <label for="acresc" class="custom-label-form">Acréscimo</label>
                <input id="acresc" name="acresc" class="form-control money" type="text" required>
            </div>

            <div class="col-md-3 form-group">
                <label for="desc" class="custom-label-form">Desconto</label>
                <input id="desc" name="desc" class="form-control money" type="number" required>
            </div>

            <div class="col-md-3 form-group">
                <label for="valor" class="custom-label-form">Valor</label>
                <input id="valor" name="valor" class="form-control money" type="number" required>
            </div>

            <div class="col-1 form-group d-grid">
                <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_pedido_servicos()">
                    <i class="my-icon fas fa-plus"></i>
                </button>
            </div>

            <div class="col-md-12">
                <div class="custom-table">
                    <div class="table-header-scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th width="27.5%">Membro</th>
                                    <th width="27.5%">Modalidade</th>
                                    <th width="10%" class="text-right">Quantidade</th>
                                    <th width="10%" class="text-right">Acréscimo</th>
                                    <th width="10%" class="text-right">Desconto</th>
                                    <th width="10%" class="text-right">Valor</th>
                                    <th width="5%" class="text-center"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div>
                        <table id="table-orcamento-procedimentos" class="table table-hover">
                            <tbody>
                                <tr>
                                    <td width="27.5%">Membro</td>
                                    <td width="27.5%">Modalidade</td>
                                    <td width="10%" class="text-right">Quantidade</td>
                                    <td width="10%" class="text-right">Acréscimo</td>
                                    <td width="10%" class="text-right">Desconto</td>
                                    <td width="10%" class="text-right">Valor</td>
                                    <td width="5%"  class="text-center btn-table-action">
                                        <i class="my-icon far fa-trash-alt" onclick="deletar_pedido_servicos(0)"></i>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="row">
            <h4 class="col-12 header-color">
                Formas de Pagamento
            </h4>
     
            <div class="col-md-5 form-group">
                <label for="forma_pag_id" class="custom-label-form">Forma de Pagamento</label>
                <select id="forma_pag_id" name="forma_pag_id" class="custom-select">
                    <option value="0">Escolher Forma de Pagamento...</option>
                    @foreach ($forma_pags as $forma_pag)
                        <option value="{{ $forma_pag->id }}">
                            {{ $forma_pag->descr }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="forma_pag_valor" class="custom-label-form">Valor</label>
                <input id="forma_pag_valor" name="forma_pag_valor" class="form-control" type="number" required>
            </div>

            <div class="col-md-2 form-group">
                <label for="forma_pag_parcela" class="custom-label-form">Parcelas</label>
                <input id="forma_pag_parcela" name="forma_pag_parcela" class="form-control" type="number" required>
            </div>

            <div class="col-md-2 form-group">
                <label for="forma_pag_validade" class="custom-label-form">Validade</label>
                <input id="forma_pag_validade" name="forma_pag_validade" class="form-control date" autocomplete="off" type="text" required>
            </div>

            <div class="col-1 form-group d-grid">
                <button class="btn btn-target mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_pedido_forma_pag()">
                    <i class="my-icon fas fa-plus"></i>
                </button>
            </div>

            <div class="col-md-12">
                <div class="custom-table">
                    <div class="table-header-scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th width="50%">Forma de Pagamento</th>
                                    <th width="15%" class="text-right">Valor</th>
                                    <th width="15%" class="text-right">Parcelas</th>
                                    <th width="15%">Validade</th>
                                    <th width="5%"  class="text-center"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div>
                        <table id="table-orcamento-forma-pag" class="table table-hover">
                            <tbody>
                                <tr>
                                    <td width="50%">Forma de Pagamento</td>
                                    <td width="15%" class="text-right">Valor</td>
                                    <td width="15%" class="text-right">Parcelas</td>
                                    <td width="15%">Validade</td>
                                    <td width="5%" class="text-center btn-table-action">
                                        <i class="my-icon far fa-trash-alt" onclick="pedido_forma_pag(0)"></i>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-12 form-group">
                <label for="obs" class="custom-label-form">Observações</label>
                <textarea id="obs" name="obs" class="form-control" rows="5"></textarea>
            </div>
        </div>

        <div class="row">
            <button type="submit" class="btn btn-target mx-auto my-3 px-6">
                Salvar
            </button>
        </div>

    </form>
</div>
        
@endsection