
<div class="container-fluid h-100 px-3 py-4">
    <div class="row">
        <h3 class="col header-color mb-3">
            Contratos
        </h3>
        <div id="filtro-grid" class="input-group col-12 mb-3" data-table="#table-plano_tratamento">
            <input type="text" class="form-control form-control-lg" placeholder="Procurar por..." aria-label="Procurar por..." aria-describedby="btn-filtro">
            <div class="input-group-append">
                <button class="btn btn-secondary btn-search-grid" type="button" id="btn-filtro">
                    <i class="my-icon fas fa-search"></i>
                </button>
            </div>
         </div>
    </div>
    <div class="custom-table card">
        <div class="table-header-scroll">
            <table>
                <thead>
                    <tr class="sortable-columns" for="#table-plano_tratamento">
                        <th width="10%" class="text-center">Nº Agentamento</th>
                        <th width="15%">Associado</th>
                        <th width="10%">Solicitante</th>
                        <th width="10%" class="text-right">Data</th>
                        <th width="10%">Criado Em</th>
                        <th width="10%">Válido Até</th>
                        <th width="10%">Status</th>
                        <th width="15%" class="text-center">Ações</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll custom-scrollbar">
            <table id="table-plano_tratamento" class="table table-hover">
                <tbody>
                    {{-- @foreach ($pedidos as $pedido)
                        <tr>
                            <td width="10%" class="text-center">
                                {{ str_pad($pedido->num_pedido, 6, "0", STR_PAD_LEFT) }}
                            </td>
                            <td width="15%" onclick="verificar_cad_redirecionar({{ $pedido->id_paciente }})">{{ $pedido->descr_paciente }}</td>
                            <td width="10%">{{ $pedido->descr_prof_exa }}</td>
                            <td width="10%">{{ $pedido->descr_convenio }}</td>
                            <td width="10%" class="text-right">R$ {{ number_format($pedido->total, 2, ',', '.') }}</td>
                            <td width="10%">
                                {{ date('d/m/Y', strtotime($pedido->created_at)) }}
                            </td>
                            <td width="10%">{{ date('d/m/Y', strtotime($pedido->data_validade)) }}</td>
                            <td width="10%" style="font-size:0.75rem">
                                @if ($pedido->status == 'F')
                                    <div class="tag-pedido-finalizado">
                                        Em Execução
                                    </div>
                                @elseif ($pedido->status == 'E')
                                    <div class="tag-pedido-aberto">
                                        Aprovação do associado
                                    </div>
                                @elseif ($pedido->status == 'A')
                                    <div class="tag-pedido-primary">
                                        Em Edição
                                    </div>
                                @else
                                    <div class="tag-pedido-cancelado">
                                        Cancelado
                                    </div>
                                @endif
                            </td>
                            <td width="15%" class="text-right btn-table-action">
                                @if ($pedido->status != 'C')
                                    @if ($pedido->status == 'A')
                                        <i class="my-icon far fa-user-check" onclick="mudar_status_pedido({{ $pedido->id }}, 'F')"></i>
                                    @endif
                                    <i class="my-icon far fa-file-times" onclick="mudar_status_pedido({{ $pedido->id }}, 'C')"></i>
                                    <i class="my-icon far fa-print" onclick="redirect('pedido/imprimir/{{ $pedido->id }}', true)"></i>
                                    <i class="my-icon far fa-edit" onclick="editar_pedido({{ $pedido->id }})"></i>
                                @endif
                                <i class="my-icon far fa-trash-alt" onclick="deletar_pedido({{ $pedido->id }})"></i>
                            </td>
                        </tr>
                    @endforeach --}}
                </tbody>
            </table>
        </div>
    </div>
</div>


