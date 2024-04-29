<!-- Modal -->
<div class="modal fade" id="pessoaModal" aria-labelledby="pessoaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id='myform' action="/saude-beta/pessoa/salvar" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title header-color" id="pessoaModalLabel">Cadastro
                        {{ ucfirst(substr(Request::route()->getPrefix(), 1)) }}</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-4 text-right">
                                        <img id="foto-preview" class="user-photo"
                                            src="{{ asset('img/foto_purple.png') }}" onclick="$('#foto').click();"
                                            onerror="this.onerror=null;this.src='/saude-beta/img/foto_purple.png'">
                                    </div>
                                    <div class="col-md-6 d-flex">
                                        <div class="input-group m-auto">
                                            <div class="custom-file">
                                                <input id="foto" name="foto" class="custom-file-input"
                                                    type="file" onchange="preview_photo(this)"
                                                    @if ((substr(Request::route()->getPrefix(), 1) === 'paciente' ||
                                                        strpos(Route::currentRouteAction(), 'abrir_prontuario')) &&
                                                        getEmpresaObj()->mod_trava_foto_paciente) @endif>


                                                <label for="foto" class="custom-file-label">Escolher
                                                    arquivo...</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    @if (getEmpresaObj()->mod_cod_interno)
                                        <div class="col-3 form-group">
                                            <label for="cod_interno" class="custom-label-form">Cód. Interno</label>
                                            <input id="cod_interno" name="cod_interno" class="form-control"
                                                autocomplete="off" type="text" maxlength="20" required>
                                        </div>
                                    @endif
                                    <div class="col form-group">
                                        <label for="nome_fantasia" class="custom-label-form">Nome *</label>
                                        <input id="nome_fantasia" name="nome_fantasia" class="form-control"
                                            autocomplete="off" type="text" required>
                                    </div>
                                    <div class="col-12 form-group">
                                        <label for="nome_reduzido" class="custom-label-form">Nome Reduzido</label>
                                        <input id="nome_reduzido" name="nome_reduzido" class="form-control"
                                            autocomplete="off" type="text" required>
                                    </div>
                                </div>
                                @if (App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'R' ||
                                    App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'A')
                                    <div class="row
                                        @if (substr(Request::route()->getPrefix(), 1) !== 'paciente' &&
                                            !strpos(Route::currentRouteAction(), 'abrir_prontuario'))
                                                mb-5
                                        @endif
                                    ">
                                        <div class="col-md-12 form-group">
                                            <label for="email" class="custom-label-form">E-mail</label>
                                            <input id="email" name="email" class="form-control" autocomplete="off"
                                                type="text" @if (substr(Request::route()->getPrefix(), 1) === 'profissional') required @endif>
                                        </div>
                                    </div>
                                    @if (substr(Request::route()->getPrefix(), 1) === 'paciente' ||
                                        strpos(Route::currentRouteAction(), 'abrir_prontuario'))
                                        <div class="row mb-5">
                                            <div class="col-md-12 form-group">
                                                <label for="psq" class="custom-label-form">Por onde nos conheceu?</label>
                                                <select id="psq" name="psq" class="form-control">
                                                    <option value = "">Não informado</option>
                                                    <option value = "midiasocial">Mídia social</option>
                                                    <option value = "indicacao">Indicação</option>
                                                    <option value = "fachada">Fachada</option>
                                                    <option value = "networkmembro">Network membro</option>
                                                </select>
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                @if (substr(Request::route()->getPrefix(), 1) === 'profissional')
                                    <div>
                                        <div class="custom-control custom-switch col text-center" style="width: 30%;margin: -70px 0px 15px 0px;
                                            @if (!(substr(Request::route()->getPrefix(), 1) === 'profissional'))display:none @endif">
                                            <input style="width: 100px;position: relative;left: 61px;height:24px"
                                                id="nao-gerar-faturamento" name="nao_gerar_faturamento"
                                                class="checkbox custom-control-input" type="checkbox"
                                                onchange="controlMembroGeraFaturamento($(this))" value="N">
                                            <label for="nao-gerar-faturamento" class="custom-control-label">Não gera faturamento?<label>
                                        </div>

                                        <div style="display: flex"> 
                                            <div class="col-md-6">
                                                <label for="data" class="custom-label-form">Desde quando?</label>
                                                <input id="data" name="data" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____" required disabled>                                        
                                            </div>
                                            <div>
                                                <label for="aplicar-desconto" class="custom-label-form">Aplicar desconto</label>
                                                <select id="aplicar-desconto" name="aplicar_desconto" class="form-control custom-select" style="width: 50%;height: 41px" type="text" autocomplete="off">
                                                    <option hidden></option>
                                                    <option value="S">Sim</option>
                                                    <option value="N">Não</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                    <hr>
                                    <div class="row crud-section" data-id_hide="#pessoa-dados-profissional">
                                        <h5 class="col-8">
                                            <i class="my-icon fas fa-address-card"></i>
                                            Dados do Profissional
                                        </h5>
                                        <div class="col-4 text-right indicator">
                                            <i class="my-icon fas fa-plus"></i>
                                        </div>
                                    </div>

                                    <div id="pessoa-dados-profissional">
                                        <div class="row" style='display:none;'>
                                            <div class="col-md-12 form-group">
                                                <label for="crm_cro" class="custom-label-form">
                                                    {{-- @if (getEmpresaObj()->tipo == 'D')
                                                    CRO
                                                @else
                                                    CRM
                                                @endif --}} {{-- todo_ verificar --}}
                                                </label>
                                                <input id="crm_cro" name="crm_cro" class="form-control"
                                                    autocomplete="off" type="text" maxlength="15">
                                            </div>
                                        </div>

                                        <div id="lista-especialidade" class="w-100">
                                            <div class="row">
                                                <div class="col-md-10 form-group">
                                                    <label for="especialidade" class="custom-label-form">Área da saúde
                                                        *</label>
                                                    <select id="especialidade" name="especialidade[]"
                                                        class="form-control custom-select">
                                                        <option value="0">Selecionar Área da Saúde...</option>
                                                        @foreach ($especialidades as $especialidade)
                                                            <option value="{{ $especialidade->id }}">
                                                                {{ $especialidade->descr }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-2 form-group d-flex">
                                                    <button class="btn btn-danger mt-auto mr-1"
                                                        style="height:calc(1.5em + 0.75rem + 8px)"
                                                        onclick="delete_especialidade_pessoa($(this)); return false;">
                                                        <i class="my-icon fas fa-trash"></i>
                                                    </button>
                                                    <button class="btn btn-success mt-auto"
                                                        style="height:calc(1.5em + 0.75rem + 8px)"
                                                        onclick="add_especialidade_pessoa($(this)); return false;">
                                                        <i class="my-icon fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="lista-empresa" class="w-100">
                                            <div class="row">
                                                <div class="col-md-10 form-group">
                                                    <label for="empresa" class="custom-label-form">Empresas *</label>
                                                    <select id="empresa" name="empresa[]"
                                                        class="form-control custom-select">
                                                        <option value="0">Selecionar Empresa...</option>
                                                        @foreach ($empresas as $empresa)
                                                            <option value="{{ $empresa->id }}">
                                                                {{ $empresa->descr }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-2 form-group d-flex">
                                                    <button class="btn btn-danger mt-auto mr-1"
                                                        style="height:calc(1.5em + 0.75rem + 8px)"
                                                        onclick="delete_empresa_pessoa($(this)); return false;">
                                                        <i class="my-icon fas fa-trash"></i>
                                                    </button>
                                                    <button class="btn btn-success mt-auto"
                                                        style="height:calc(1.5em + 0.75rem + 8px)"
                                                        onclick="add_empresa_pessoa($(this)); return false;">
                                                        <i class="my-icon fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="w-100">
                                            <label for="crm" class="custom-label-form">UF</label>
                                            <label for="crm" class="custom-label-form"
                                                style="margin-left:105px">Nº CRM</label>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <select id="uf-crm" name="uf_crm"
                                                        class="form-control custom-select">

                                                    </select>
                                                </div>

                                                <div class="col-9">
                                                    <input id="crm" name="crm" class="form-control crm"
                                                        data-mask="000000" data-mask-reverse="true"
                                                        autocomplete="off" type="text" maxlength="14">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-100">
                                            <label for="cref" class="custom-label-form">UF</label>
                                            <label for="cref" class="custom-label-form"
                                                style="margin-left:105px">Nº CREF</label>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <select id="uf-cref" name="uf_cref"
                                                        class="form-control custom-select">

                                                    </select>
                                                </div>

                                                <div class="col-9">
                                                    <input id="cref" name="cref" class="form-control cref"
                                                        data-mask="000000" data-mask-reverse="true"
                                                        autocomplete="off" type="text" maxlength="14">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-100">
                                            <label for="creft" class="custom-label-form">UF</label>
                                            <label for="creft" class="custom-label-form"
                                                style="margin-left:105px">Nº Crefito</label>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <select id="uf-creft" name="uf_creft"
                                                        class="form-control custom-select">

                                                    </select>
                                                </div>

                                                <div class="col-9">
                                                    <input id="creft" name="creft" class="form-control creft"
                                                        data-mask="000000" data-mask-reverse="true"
                                                        autocomplete="off" type="text" maxlength="14">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-100">
                                            <label for="crn" class="custom-label-form">UF</label>
                                            <label for="crn" class="custom-label-form"
                                                style="margin-left:105px">CRN</label>
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <select id="uf-crn" name="uf_crn"
                                                        class="form-control custom-select">

                                                    </select>
                                                </div>

                                                <div class="col-9">
                                                    <input id="crn" name="crn" class="form-control crn"
                                                        data-mask="000000" data-mask-reverse="true"
                                                        autocomplete="off" type="text" maxlength="14">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                @endif

                                <hr>

                                <div class="row crud-section" data-id_hide="#pessoa-dados-pessoais">
                                    <h5 class="col-8">
                                        <i class="my-icon fal fa-address-card"></i>
                                        Dados Pessoais *
                                    </h5>
                                    <div class="col-4 text-right indicator">
                                        <i class="my-icon fas fa-plus"></i>
                                    </div>
                                </div>

                                <div id='pessoa-dados-pessoais' class="mb-5">
                                    <div class="row">
                                        <div class="custom-control custom-switch text-right col">
                                            <input id="tpessoa" name="tpessoa" class="custom-control-input"
                                                type="checkbox" onchange="pessoa_fisica_juridica()">
                                            <label for="tpessoa" class="custom-control-label">Pessoa Jurídica</label>
                                        </div>
                                    </div>

                                    <div id="pessoa-fisica">

                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label for="sexo" class="custom-label-form">Sexo</label>
                                                <select id="sexo" name="sexo"
                                                    class="form-control custom-select">
                                                    <option value="">Selecionar...</option>
                                                    <option value="F">Feminino</option>
                                                    <option value="M">Masculino</option>
                                                </select>
                                            </div>
                                            @if (App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'R' ||
                                                App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'A')
                                                <div class="col-md-4 form-group">
                                                    <label for="estado-civil" class="custom-label-form">Estado
                                                        Civil</label>
                                                    <select id="estado-civil" name="estado_civil"
                                                        class="form-control custom-select">
                                                        <option value="">Selecionar...</option>
                                                        <option value="S">Solteiro (a)</option>
                                                        <option value="C">Casado (a)</option>
                                                        <option value="D">Divorciado (a)</option>
                                                        <option value="V">Viúvo (a)</option>
                                                    </select>
                                                </div>
                                            @endif
                                            <div class="col-md-4 form-group">
                                                <label for="data_nasc" class="custom-label-form">Data de Nascimento
                                                    *</label>
                                                <input id="data_nasc" name="data_nasc" class="form-control date"
                                                    autocomplete="off" type="text">
                                            </div>
                                        </div>
                                        @if (App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'R' ||
                                            App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'A')
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label for="cpf" class="custom-label-form">CPF</label>
                                                    <span id="aviso-CPF" style="color:red; display: none">* CPF já cadastrado</span>
                                                    <input id="cpf" name="cpf" class="form-control cpf"
                                                        data-mask="00.000.000-00" data-mask-reverse="true"
                                                        autocomplete="off" type="text" onkeyup="validaCpfCadastroPessoa($(this))">
                                                </div>

                                                <div class="col-md-6 form-group">
                                                    <label for="rg" class="custom-label-form">RG</label>
                                                    <input id="rg" name="rg" class="form-control rg"
                                                        autocomplete="off" type="text">
                                                </div>
                                            </div>

                                            @if (substr(Request::route()->getPrefix(), 1) === 'paciente' ||
                                                strpos(Route::currentRouteAction(), 'abrir_prontuario') !== false)
                                                <div class="row">
                                                    <div class="col-md-12 form-group">
                                                        <label for="profissao"
                                                            class="custom-label-form">Profissão</label>
                                                        <input id="profissao" name="profissao" class="form-control"
                                                            autocomplete="off" type="text">
                                                    </div>
                                                </div>
                                            @endif
                                        @endif

                                        @if (getEmpresaObj()->tipo != 'D' && substr(Request::route()->getPrefix(), 1) !== 'cliente')
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label for="peso" class="custom-label-form">Peso</label>
                                                    <input id="peso" name="peso" class="form-control"
                                                        autocomplete="off" type="text" value="0.00">
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label for="altura" class="custom-label-form">Altura</label>
                                                    <input id="altura" name="altura" class="form-control"
                                                        autocomplete="off" type="text" value="0.00">
                                                </div>
                                            </div>
                                        @endif {{-- todo_ verificar --}}
                                    </div>
                                    @if (App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'R' ||
                                        App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'A')
                                        <div id="pessoa-juridico" style="display:none">
                                            <div class="row">
                                                <div class="col-md-12 form-group">
                                                    <label for="razao_social" class="custom-label-form">Razão
                                                        Social</label>
                                                    <input id="razao_social" name="razao_social" class="form-control"
                                                        autocomplete="off" type="text">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label for="cnpj" class="custom-label-form">CNPJ</label>
                                                    <input id="cnpj" name="cnpj" class="form-control cnpj"
                                                        data-mask="00.000.000/0000-00" data-mask-reverse="true"
                                                        autocomplete="off" type="text">
                                                </div>

                                                <div class="col-md-6 form-group">
                                                    <label for="ie" class="custom-label-form">IE</label>
                                                    <input id="ie" name="ie" class="form-control ie"
                                                        autocomplete="off" type="text">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <hr>
                                @if (App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'R' ||
                                    App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'A')
                                    <div class="row crud-section" data-id_hide="#pessoa-endereco">
                                        <h5 class="col-8">
                                            <i class="my-icon far fa-map-marker-alt"></i>
                                            Endereço
                                        </h5>
                                        <div class="col-4 text-right indicator">
                                            <i class="my-icon fas fa-plus"></i>
                                        </div>
                                    </div>

                                    <div id="pessoa-endereco" class="mb-5"> {{-- Endereço --}}
                                        <div class="row">
                                            <div class="col-md-4 form-group">
                                                <label for="cep" class="custom-label-form">CEP</label>
                                                <input id="cep" name="cep" class="form-control cep"
                                                    autocomplete="off" type="text">
                                            </div>

                                            <div class="col-md-4 form-group">
                                                <label for="cidade" class="custom-label-form">Cidade</label>
                                                <input id="cidade" name="cidade" class="form-control"
                                                    type="text">
                                            </div>

                                            <div class="col-md-4 form-group">
                                                <label for="uf" class="custom-label-form">UF</label>
                                                <input id="uf" name="uf" class="form-control"
                                                    type="text">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-9 form-group">
                                                <label for="endereco" class="custom-label-form">Endereco</label>
                                                <input id="endereco" name="endereco" class="form-control"
                                                    type="text">
                                            </div>

                                            <div class="col-md-3 form-group">
                                                <label for="numero" class="custom-label-form">Numero</label>
                                                <input id="numero" name="numero" class="form-control"
                                                    autocomplete="off" type="text">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-7 form-group">
                                                <label for="bairro" class="custom-label-form">Bairro</label>
                                                <input id="bairro" name="bairro" class="form-control"
                                                    type="text">
                                            </div>

                                            <div class="col-md-5 form-group">
                                                <label for="complemento" class="custom-label-form">Complemento</label>
                                                <input id="complemento" name="complemento" class="form-control"
                                                    type="text">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <hr>
                                @if (App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'R' ||
                                    App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'A')
                                    <div class="row crud-section" data-id_hide="#pessoa-contato">
                                        <h5 class="col-8">
                                            <i class="my-icon fal fa-phone-square"></i>
                                            Contato *
                                        </h5>
                                        <div class="col-4 text-right indicator">
                                            <i class="my-icon fas fa-plus"></i>
                                        </div>
                                    </div>

                                    <div id="pessoa-contato" class="mb-5">
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label for="celular1" class="custom-label-form">Celular *</label>
                                                <input id="celular1" name="celular1" class="form-control celular"
                                                    type="text" onkeyup="validaCelularCadastroPessoa($(this))" required>
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <label for="celular2" class="custom-label-form">Celular (2)</label>
                                                <input id="celular2" name="celular2" class="form-control celular"
                                                    type="text">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label for="telefone1" class="custom-label-form">Telefone</label>
                                                <input id="telefone1" name="telefone1" class="form-control telefone"
                                                    type="text">
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <label for="telefone2" class="custom-label-form">Telefone (2)</label>
                                                <input id="telefone2" name="telefone2" class="form-control telefone"
                                                    type="text">
                                            </div>
                                        </div>
                                    </div>

                                    @if (substr(Request::route()->getPrefix(), 1) === 'paciente' ||
                                        strpos(Route::currentRouteAction(), 'abrir_prontuario') !== false ||
                                        substr(Request::route()->getPrefix(), 1) === 'agenda')
                                        <hr>

                                        <div class="row crud-section" data-id_hide="#pessoa-dados-responsavel">
                                            <h5 class="col-8">
                                                <i class="my-icon fad fa-user-friends"></i>
                                                Dados do Responsável (Opcional)
                                            </h5>
                                            <div class="col-4 text-right indicator">
                                                <i class="my-icon fas fa-plus"></i>
                                            </div>
                                        </div>

                                        <div id="pessoa-dados-responsavel">
                                            <div class="row">
                                                <div class="col-md-12 form-group">
                                                    <label for="resp-nome" class="custom-label-form">Nome do
                                                        Responsável</label>
                                                    <input id="resp-nome" name="resp_nome" class="form-control"
                                                        autocomplete="off" type="text">
                                                </div>

                                                <div class="col-md-6 form-group">
                                                    <label for="resp-grau-parente" class="custom-label-form">Grau
                                                        Parentesco</label>
                                                    <input id="resp-grau-parente" name="resp_grau_parente"
                                                        class="form-control" autocomplete="off" type="text">
                                                </div>

                                                <div class="col-md-6 form-group">
                                                    <label for="resp-celular"
                                                        class="custom-label-form">Celular</label>
                                                    <input id="resp-celular" name="resp_celular"
                                                        class="form-control celular" type="text">
                                                </div>

                                                <div class="col-md-6 form-group">
                                                    <label for="resp-cpf" class="custom-label-form">CPF</label>
                                                    <input id="resp-cpf" name="resp_cpf" class="form-control cpf"
                                                        data-mask="00.000.000-00" data-mask-reverse="true"
                                                        autocomplete="off" type="text">
                                                </div>

                                                <div class="col-md-6 form-group">
                                                    <label for="resp-rg" class="custom-label-form">RG</label>
                                                    <input id="resp-rg" name="resp_rg" class="form-control rg"
                                                        autocomplete="off" type="text">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12 form-group">
                                                    <div class="custom-control custom-checkbox mt-2 ml-1">
                                                        <input id="resp_localizacao" name="resp_localizacao"
                                                            class="custom-control-input" type="checkbox" checked>
                                                        <label for="resp_localizacao"
                                                            class="custom-control-label">Mesma Localização do
                                                            associado?</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div id="responsavel-localização" class="row mb-5" style="display:none">
                                                <div class="col-md-4 form-group">
                                                    <label for="resp-cep" class="custom-label-form">CEP</label>
                                                    <input id="resp-cep" name="resp_cep" class="form-control cep"
                                                        autocomplete="off" type="text">
                                                </div>

                                                <div class="col-md-4 form-group">
                                                    <label for="resp-cidade" class="custom-label-form">Cidade</label>
                                                    <input id="resp-cidade" name="resp_cidade" class="form-control"
                                                        type="text">
                                                </div>

                                                <div class="col-md-4 form-group">
                                                    <label for="resp-uf" class="custom-label-form">UF</label>
                                                    <input id="resp-uf" name="resp_uf" class="form-control"
                                                        type="text">
                                                </div>

                                                <div class="col-md-9 form-group">
                                                    <label for="resp-endereco"
                                                        class="custom-label-form">Endereco</label>
                                                    <input id="resp-endereco" name="resp_endereco"
                                                        class="form-control" type="text">
                                                </div>

                                                <div class="col-md-3 form-group">
                                                    <label for="resp-numero" class="custom-label-form">Numero</label>
                                                    <input id="resp-numero" name="resp_numero" class="form-control"
                                                        autocomplete="off" type="text">
                                                </div>

                                                <div class="col-md-7 form-group">
                                                    <label for="resp-bairro" class="custom-label-form">Bairro</label>
                                                    <input id="resp-bairro" name="resp_bairro" class="form-control"
                                                        type="text">
                                                </div>

                                                <div class="col-md-5 form-group">
                                                    <label for="resp-complemento"
                                                        class="custom-label-form">Complemento</label>
                                                    <input id="resp-complemento" name="resp_complemento"
                                                        class="form-control" type="text">
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if (substr(Request::route()->getPrefix(), 1) !== 'cliente')
                                        <hr>

                                        <div class="row crud-section" data-id_hide="#lista-convenio-pessoa">
                                            <h5 class="col-8">
                                                <i class="my-icon far fa-book-medical"></i>
                                                Convênios
                                            </h5>
                                            <div class="col-4 text-right indicator">
                                                <i class="my-icon fas fa-plus"></i>
                                            </div>
                                        </div>
                                        <div id="lista-convenio-pessoa">
                                            <div class="row">
                                                <div class="col form-group">
                                                    <label for="convenio" class="custom-label-form">Convênio</label>
                                                    <select id="convenio" name="convenio[]"
                                                        class="form-control custom-select">
                                                        <option value="">Selecionar Convênio...</option>
                                                    </select>
                                                </div>

                                                <div class="col-md-4 form-group"
                                                    @if (substr(Request::route()->getPrefix(), 1) === 'profissional') style="display:none" @endif>
                                                    <label for="num-convenio" class="custom-label-form">Nº do
                                                        Cartão</label>
                                                    <input id="num-convenio" name="num_convenio[]"
                                                        class="form-control" autocomplete="off" type="text"
                                                        maxlength="45">
                                                </div>

                                                <div class="col-md-2 form-group d-flex">
                                                    <button class="btn btn-danger mt-auto mr-1"
                                                        style="height:calc(1.5em + 0.75rem + 8px)"
                                                        onclick="delete_convenio_pessoa($(this)); return false;">
                                                        <i class="my-icon fas fa-trash"></i>
                                                    </button>
                                                    <button class="btn btn-success mt-auto"
                                                        style="height:calc(1.5em + 0.75rem + 8px)"
                                                        onclick="add_convenio_pessoa($(this)); return false;">
                                                        <i class="my-icon fas fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <hr>
                                @endif

                                <div class="row mb-5">
                                    <div class="col-md-12 form-group">
                                        <label for="obs" class="m-0 mt-2">Observações</label>
                                        <textarea id="obs" name="obs" class="form-control" rows="4"></textarea>
                                    </div>
                                    {{-- <div class="col-md-12 form-group">
                                        <label for="senha_app" class="custom-label-form">Senha do aplicativo</label>
                                        <input id="senha_app" name="senha_app" class="form-control" autocomplete="off" type="text">
                                    </div> --}}
                                </div>

                                @if (substr(Request::route()->getPrefix(), 1) === 'profissional')
                                    <hr>

                                    <div class="row crud-section">
                                        <h5 class="col-sm-12">
                                            <i class="my-icon far fa-user-lock"></i>
                                            Acesso
                                        </h5>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 form-group">
                                            <label for="password" class="custom-label-form">Senha</label>
                                            <input id="password" name="password" class="form-control"
                                                type="password" @if (substr(Request::route()->getPrefix(), 1) === 'profissional') required @endif>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="container">
                        <div class="row">
                            <div class="custom-control custom-switch col text-center" style="display:none">
                                <input id="isPaciente" name="isPaciente" class="custom-control-input"
                                    type="checkbox" @if (substr(Request::route()->getPrefix(), 1) === 'paciente' ||
                                        strpos(Route::currentRouteAction(), 'abrir_prontuario')) checked readonly @endif>
                                <label for="isPaciente" class="custom-control-label">associado</label>
                            </div>
                            <div class="custom-control custom-switch col text-center" style="display:none">
                                <input id="isCliente" name="isCliente" class="custom-control-input" type="checkbox"
                                    @if (substr(Request::route()->getPrefix(), 1) === 'cliente') checked @endif>
                                <label for="isCliente" class="custom-control-label">Cliente</label>
                            </div>
                            <div class="custom-control custom-switch col text-center"
                                @if (!(substr(Request::route()->getPrefix(), 1) === 'profissional')) style="display:none" @endif>
                                <input style="width: 100px;position: relative;left: 61px;height:24px" id="isMedico"
                                    name="isMedico" class="checkbox custom-control-input" type="checkbox"
                                    @if (substr(Request::route()->getPrefix(), 1) === 'profissional') checked @endif>
                                <label for="isMedico" class="custom-control-label">Membro<label>
                            </div>
                            @if (substr(Request::route()->getPrefix(), 1) === 'profissional')
                                <div class="row" id='footer-options'>
                                    <div style="left: -45px;" class="custom-control custom-switch col text-right">
                                        <input style="width: 100px;position: relative;left: 67px;height: 24px;"
                                            id="isAdministrador" name="isAdministrador" class="custom-control-input"
                                            type="checkbox">
                                        <label for="isAdministrador" class="custom-control-label"
                                            class="mt-2">Administrador</label>
                                    </div>
                                </div>
                                <div id="footer-options" style="top: -31px;"
                                    class="custom-control custom-switch col text-center"
                                    @if (!(substr(Request::route()->getPrefix(), 1) === 'profissional')) style="display:none" @endif>
                                    <input style="width: 165px;position: relative;left: -21px;height: 24px;top: 30px;"
                                        id="isRecepcao" name="isRecepcao" class="checkbox custom-control-input"
                                        type="checkbox">
                                    <label for="isRecepcao" class="custom-control-label">Consultor de vendas</label>
                                </div>
                                {{-- <div class="custom-control custom-switch col text-center d-none">
                                <input id="isFornecedor" name="isFornecedor" class="custom-control-input" type="checkbox" @if (substr(Request::route()->getPrefix(), 1) === 'fornecedores')  checked readonly @endif>
                                <label for="isFornecedor" class="custom-control-label">Fornecedor</label>
                            </div> --}}
                            @endif
                        </div>

                        {{-- @if (!strpos(Route::currentRouteAction(), 'abrir_prontuario') !== false)  --}}

                        <div class="row mt-3">
                            <button id="id" name="id" class="btn btn-primary m-auto px-5"
                                type="submit">Salvar</button>
                        </div>

                        {{-- @endif --}}
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    window.addEventListener('load', () => {
        if (location.href.substr(0, 59) == 'http://vps.targetclient.com.br/saude-beta/pessoa/prontuario') {
            document.querySelectorAll('#footer-options').forEach(el => {
                el.style.display = 'none'
            })
        }
        importAPIestados()
    })

    function validaCpfCadastroPessoa($obj) {
        if (document.querySelector('#pessoaModal #id').value == '') id_pessoa = 0
        else                                                        id_pessoa = document.querySelector('#pessoaModal #id').value
        console.log($obj.val())

        if ($obj.val().length == 14){
            $.get('/saude-beta/pessoa/verificar-duplicidade/' + $obj.val() + '/' + id_pessoa,
            function(data, status) {
                console.log(data + ' | ' + status)
                if (data == 'N'){
                    $obj.addClass('campo-bloqueado')
                    $('#aviso-CPF').show()
                }
                else {
                    $obj.removeClass('campo-bloqueado')
                    $('#aviso-CPF').hide()
                }
            })
        }
        else {
            $obj.removeClass('campo-bloqueado')
            $('#aviso-CPF').hide()
        }
    }
</script>