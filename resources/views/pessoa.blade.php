@extends('layouts.app')

@section('content')
@include('components.main-toolbar')

<div class="container-fluid h-100 px-3 py-4">
    <div class="row" style="margin-right: 5px;">
        <h3 class="col-11 header-color mb-3" id='titulo'>
        @if(substr(Request::route()->getPrefix(), 1) === 'paciente')
            Associados
        @else 
            Membros
        @endif
        
        </h3>

        @if (substr(Request::route()->getPrefix(), 1) == 'profissional')
            <select id="filtro" class="form-control col-1" onchange="location.replace('/saude-beta/profissional/'+this.value)"> 
                <option value="T">Todos</option>
                <option value="E" @if ($filtro == 'E') selected @endif>Ativos</option>
                <option value="S" @if ($filtro == 'S') selected @endif>Inativos</option>
            </select>
        @endif

        
    </div>
    <div class="row">
        <div @if(substr(Request::route()->getPrefix(), 1) === 'paciente')
                id="filtro-grid-paciente"
             @else
                id="filtro-grid"
             @endif

            class="input-group col-12 mb-3" data-table="#table-pessoas">
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
                    <tr class="sortable-columns" for="#table-pessoas">
                        <th width="5%">Foto</th>
                        {{-- <th width="10%" class="text-right">
                            @if (getEmpresaObj()->mod_cod_interno)
                                Cód. Interno
                            @else
                                Código
                            @endif
                        </th> --}}
                        @if (substr(Request::route()->getPrefix(), 1) == 'profissional')
                            <th width="55%">Nome</th>
                            <th width="10%" class="text-center">Data</th>
                            <th width="10%">Especialidade</th>
                        @else 
                            <th width="65%">Nome</th>
                        @endif
                        {{-- <th width="15%">Contato</th> --}}
                        {{-- <th width="20%">Email</th> --}}
                        @if (substr(Request::route()->getPrefix(), 1) == 'paciente')
                            <th width="10%">Localidade</th>
                        @endif
                        <th width="13%" class="text-center">Situação</th>
                        <th width="15%" class="text-center"></th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="table-body-scroll custom-scrollbar">
            <table id="table-pessoas" class="table table-hover">
                <tbody>
                    @foreach ($pessoas as $pessoa)
                        <div style='display:none'>{{$contador = 0}}</div>
                        <tr>
                            <td width="5%">
                                <img class="user-photo-sm" src="/saude-beta/img/pessoa/{{ $pessoa->id }}.jpg"
                                    onerror="this.onerror=null;this.src='/saude-beta/img/paciente_default.png'">
                            </td>
                            <td width="@if(substr(Request::route()->getPrefix(), 1) == 'profissional') 55%
                                       @else 65%
                                       @endif" 
                            {{-- @if (substr(Request::route()->getPrefix(), 1) == 'paciente') --}}
                                                onclick="window.location.href = '/saude-beta/pessoa/prontuario/{{ $pessoa->id }}'"
                                            {{-- @endif --}}
                                            >
                                {{ strtoupper($pessoa->nome_fantasia) }}
                                @if (substr(Request::route()->getPrefix(), 1) == 'paciente' && $pessoa->iec_atrasado == "S")
                                    <div style = "
                                        display:inline-block;
                                        width:10px;
                                        height:10px;
                                        background-color:#F00;
                                        border-color:#F00;
                                        border-radius:50%
                                    " title = "IEC atrasado ou não existente"></div>
                                @endif
                            </td>
                            @if (substr(Request::route()->getPrefix(), 1) == 'profissional')
                                <td class="
                                    hide-mobile 
                                    text-center
                                " 
                                width="10%">
                                <div class=" 
                                    @if ($pessoa->acao == 'E')
                                        tag-pedido-finalizado
                                    @else
                                        tag-pedido-cancelado
                                    @endif"> {{ $pessoa->data }}
                                </div>
                            </td>
                                <td class="hide-mobile" width="10%">
                                    @php
                                        $especialidades_pessoa = mostrar_especialidades($pessoa->id);
                                        if (sizeof($especialidades_pessoa) > 0) {
                                            echo $especialidades_pessoa[0]->descr;
                                        } else {
                                            echo '—————';
                                        }
                                    @endphp
                                </td>
                            @endif
                            {{-- <td width="15%">{{ $pessoa->celular1 }}</td> --}}
                            {{-- <td width="20%">{{ $pessoa->email }}</td> --}}
                            @if (substr(Request::route()->getPrefix(), 1) == 'paciente')
                                <td class="hide-mobile" width="10%">{{ $pessoa->cidade . '/' . $pessoa->uf }}</td>
                            @endif
                            @if (isset($pessoa->associado))
                                @if( $pessoa->associado == 'N')
                                    <td class='text-center'  width="13%"><div class="tag-pedido-cancelado" style="font-size:13px">
                                        Não Associado
                                    </div></td>
                                @else
                                    <td class='text-center' width="13%"><div class="tag-pedido-finalizado" style="font-size:13px">Associado</div></td>
                                @endif
                            @else
                                @if( $associados[$contador] == 0)
                                    <td class='text-center'  width="13%"><div class="tag-pedido-cancelado" style="font-size:13px">
                                        Não Associado
                                    </div></td>
                                @else
                                    <td class='text-center' width="13%"><div class="tag-pedido-finalizado" style="font-size:13px">Associado</div></td>
                                @endif
                            @endif
                            

                            <td class="text-right btn-table-action hide-mobile">
                                @if (substr(Request::route()->getPrefix(), 1) == 'profissional')
                                    <i class="my-icon far fa-calendar-alt"   title="Grades do Profissional" @if (($pessoa->colaborador == 'P' || $pessoa->colaborador == 'A')) onclick="abrir_grades_pessoa({{ $pessoa->id }})"    @else style="cursor:default; opacity:0" @endif></i>
                                    <i class="my-icon far fa-calendar-times" title="Bloqueios de Grade"     @if ($pessoa->colaborador == 'P' || $pessoa->colaborador == 'A') onclick="bloquear_grades_pessoa({{ $pessoa->id }})" @else style="cursor:default; opacity:0" @endif></i>
                                @endif
                                <i class="my-icon far fa-edit"      onclick="editar_pessoa({{ $pessoa->id }})"></i>
                                @if (App\Pessoa::find(Auth::user()->id_profissional)->administrador == 'S')
                                    <i class="my-icon far fa-trash-alt" onclick="deletar_pessoa({{ $pessoa->id }})"></i>
                                @endif
                            </td>
                        </tr>
                        <div style='display:none'>{{$contador++}}</div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<button class="btn-voltar-agenda-mobile btn btn-primary custom-fab" type="button">
    <a href="http://vps.targetclient.com.br/saude-beta/">
        <img style="width: 30px; filter: invert(1)" src="{{ asset('img/agenda.png') }}">
    </a>
</button>
<script>
    window.addEventListener("load", () => {
        if (location.href == 'http://vps.targetclient.com.br/saude-beta/paciente'){
            document.querySelector('#titulo').innerHTML             = 'Associados'
            document.querySelectorAll('#footer-options').forEach(el => {
                el.style.display = 'none';
            })

        }
        else {
            document.querySelector('#titulo').innerHTML             = 'Membros'
            document.querySelector('#footer-options').style.display = 'flex'
        }
    })
</script>

<button class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#pessoaModal">
    <i class="my-icon fas fa-plus"></i>
</button>

@include('modals.pessoa_modal')
@include('modals.grade_modal')
@include('modals.grade_bloqueio_modal')

@endsection
