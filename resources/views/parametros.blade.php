@extends('layouts.app')

@section('content')
@include('.components.main-toolbar')

<div class="container-fluid h-100 m-0">
    <div class="row py-4">
        <h3 class="col-12 pl-5">Parâmetros</h3>

        <div class="col-md-3 px-4 py-2 opt-param">
            <a class="card text-center text-dark py-3" href="/saude-beta/anamnese">
                <i class="fad fa-file-import my-icon m-auto" style="font-size:90px"></i>
                <h4>Anamnese</h4>
            </a>
        </div>

        <div class="col-md-3 px-4 py-2 opt-param">
            <a class="card text-center text-dark py-3" href="/saude-beta/cliente">
                <i class="fad fa-user-tie my-icon m-auto" style="font-size:90px"></i>
                <h4>Clientes</h4>
            </a>
        </div>

        <div class="col-md-3 px-4 py-2 opt-param">
            <a class="card text-center text-dark py-3" href="/saude-beta/especialidade">
                <i class="fad fa-stethoscope my-icon m-auto" style="font-size:90px"></i>
                <h4>especialidades</h4>
            </a>
        </div>

        <div class="col-md-3 px-4 py-2 opt-param">
            <a class="card text-center text-dark py-3" href="/saude-beta/etiqueta">
                <i class="fad fa-tags my-icon m-auto" style="font-size:90px"></i>
                <h4>Etiquetas</h4>
            </a>
        </div>

        <div class="col-md-3 px-4 py-2 opt-param">
            <a class="card text-center text-dark py-3" href="/saude-beta/procedimento">
                <i class="fad fa-first-aid my-icon m-auto" style="font-size:90px"></i>
                <h4>Modalidades</h4>
            </a>
        </div>

        <div class="col-md-3 px-4 py-2 opt-param">
            <a class="card text-center text-dark py-3" href="/saude-beta/profissional">
                <i class="fad fa-user-md my-icon m-auto" style="font-size:90px"></i>
                <h4>Membros</h4>
            </a>
        </div>

        <div class="col-md-3 px-4 py-2 opt-param">
            <a class="card text-center text-dark py-3" href="/saude-beta/sala">
                <i class="fad fa-clinic-medical my-icon m-auto" style="font-size:90px"></i>
                <h4>Salas</h4>
            </a>
        </div>

        <div class="col-md-3 px-4 py-2 opt-param">
            <a class="card text-center text-dark py-3" href="/saude-beta/agenda-status">
                <i class="fad fa-calendar-star my-icon m-auto" style="font-size:90px"></i>
                <h4>Status da Agenda</h4>
            </a>
        </div>

        <div class="col-md-3 px-4 py-2 opt-param">
            <a class="card text-center text-dark py-3" href="/saude-beta/agenda-confirmacao">
                <i class="fad fa-calendar-star my-icon m-auto" style="font-size:90px"></i>
                <h4>Tipo de Confirmação</h4>
            </a>
        </div>

        <div class="col-md-3 px-4 py-2 opt-param">
            <a class="card text-center text-dark py-3" href="/saude-beta/convenio">
                <i class="fad fa-book-medical my-icon m-auto" style="font-size:90px"></i>
                <h4>Convênios</h4>
            </a>
        </div>

        @if (getEmpresaObj()->mod_planos_tratamento || getEmpresaObj()->mod_financeiro)
        <div class="col-md-3 px-4 py-2 opt-param">
            <a class="card text-center text-dark py-3" href="/saude-beta/tabela-precos">
                <i class="fad fa-money-check-alt my-icon m-auto" style="font-size:90px"></i>
                <h4>Tabelas de Preços</h4>
            </a>
        </div>

        <div class="col-md-3 px-4 py-2 opt-param">
            <a class="card text-center text-dark py-3" href="/saude-beta/forma-pag">
                <i class="fad fa-credit-card my-icon m-auto" style="font-size:90px"></i>
                <h4>Formas de Pagamento</h4>
            </a>
        </div>

        <div class="col-md-3 px-4 py-2 opt-param">
            <a class="card text-center text-dark py-3" href="/saude-beta/financeira">
                <i class="fad fa-comment-dollar my-icon m-auto" style="font-size:90px"></i>
                <h4>Financeira</h4>
            </a>
        </div>
        @endif 

        <div class="col-md-3 px-4 py-2 opt-param">
            <a class="card text-center text-dark py-3" href="/saude-beta/medicamento">
                <i class="fad fa-pills my-icon m-auto" style="font-size:90px"></i>
                <h4>Medicamentos</h4>
            </a>
        </div>

        <div class="col-md-3 px-4 py-2 opt-param">
            <a class="card text-center text-dark py-3" href="/saude-beta/documento-modelo">
                <i class="fad fa-file-alt my-icon m-auto" style="font-size:90px"></i>
                <h4>Modelos de Documento</h4>
            </a>
        </div>

        <div class="col-md-3 px-4 py-2 opt-param">
            <a class="card text-center text-dark py-3" href="/saude-beta/evolucao-tipo">
                <i class="fad fa-address-book my-icon m-auto" style="font-size:90px"></i>
                <h4>Tipos de Evolução</h4>
            </a>
        </div>

    </div>
</div>

@endsection