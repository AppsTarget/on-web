<!-- Modal -->
<div class="modal fade" id="encaminhanteModal" aria-labelledby="encaminhanteModalLabel" aria-modal="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="encaminhanteModalLabel">
                    Encaminhante
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id = "encaminhanteModalForm">
                    <div class="row">
                        <div class="col-8 form-group" style = "margin-bottom:0">
                            <label for="enc_nome" class = "custom-label-form">Nome:</label>
                            <input id = "enc_nome" name = "enc_nome" class = "form-control" autocomplete = "off" type = "text" onkeyup = "encaminhante_tamanho()" />
                            <span id = "enc_nome_length" class = "custom-label-form" style = "
                                text-align: right;
                                width: 100%;
                                display: block;
                            ">0/100</span>
                        </div>
                        <div class="col-4 form-group" style = "margin-bottom:0">
                            <label for="enc_tel" class = "custom-label-form">Telefone:</label>
                            <input id = "enc_tel" name = "enc_tel" class = "form-control" autocomplete = "off" type = "text" onkeyup = "this.value=phoneMask(this.value)"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-5 form-group">
                            <label for="enc_doc" class = "custom-label-form">Documento:</label>
                            <input id = "enc_doc" name = "enc_doc" class = "form-control" autocomplete = "off" type = "text" />
                        </div>
                        <div class="col-3 form-group">
                            <label for="enc_tpdoc" class = "custom-label-form">Tipo:</label>
                            <select id = "enc_tpdoc" name = "enc_tpdoc" class = "form-control">
                                <option value="crm">CRM</option>
                                <option value="cref">CREF</option>
                                <option value="creft">CREFT</option>
                                <option value="crn">CRN</option>
                                <option value="rg_ie">RG/IE</option>
                                <option value="cpf_cnpj">CPF/CNPJ</option>
                            </select>
                        </div>
                        <div class="col-4 form-group">
                            <label for="enc_doc_uf" class = "custom-label-form">UF emissora:</label>
                            <select id = "enc_doc_uf" name = "enc_doc_uf" class = "form-control">
                                <option value="AC">Acre</option>
                                <option value="AL">Alagoas</option>
                                <option value="AP">Amapá</option>
                                <option value="AM">Amazonas</option>
                                <option value="BA">Bahia</option>
                                <option value="CE">Ceará</option>
                                <option value="DF">Distrito Federal</option>
                                <option value="ES">Espírito Santo</option>
                                <option value="GO">Goiás</option>
                                <option value="MA">Maranhão</option>
                                <option value="MT">Mato Grosso</option>
                                <option value="MS">Mato Grosso do Sul</option>
                                <option value="MG">Minas Gerais</option>
                                <option value="PA">Pará</option>
                                <option value="PB">Paraíba</option>
                                <option value="PR">Paraná</option>
                                <option value="PE">Pernambuco</option>
                                <option value="PI">Piauí</option>
                                <option value="RJ">Rio de Janeiro</option>
                                <option value="RN">Rio Grande do Norte</option>
                                <option value="RS">Rio Grande do Sul</option>
                                <option value="RO">Rondônia</option>
                                <option value="RR">Roraima</option>
                                <option value="SC">Santa Catarina</option>
                                <option value="SP">São Paulo</option>
                                <option value="SE">Sergipe</option>
                                <option value="TO">Tocantins</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 form-group">
                            <table style = "width:100%">
                                <tr>
                                    <td style = "font-size:12px">Área(s) da saúde:</td>
                                    <td style = "font-size:12px;text-align:right">
                                        @if (App\Pessoa::find(Auth::user()->id_profissional)->administrador == 'S')
                                            <a href = '/saude-beta/especialidade' target = "_blank">Gerenciar</a>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                            <select id = "enc_esp" name = "enc_esp" class = "form-control" multiple style = 'height:200px'></select>
                        </div>
                    </div>
                    <div class = "text-center">
                        <button class="btn my-3 px-5 btn-danger" type="button" onclick = "excluirEncaminhante()">Excluir</button>
                        &nbsp;&nbsp;&nbsp;
                        <button class="btn my-3 px-5 btn-success" type="button" onclick = "gravarEncaminhante()">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type = "text/javascript" language = "JavaScript">
    function resetaModal() {
        $("#enc_tpdoc").val("crm");
        $("#enc_doc_uf").val("AC");
        $("#enc_esp").val([]);
        $("input.form-control").each(function() {
            $(this).val("");
        });
    }

    function encaminhante_tamanho() {
        var val = document.getElementById("enc_nome").value;
        var tamanho = val.length;
        if (tamanho > 100) $("#enc_nome").val($("#enc_nome").val().substring(0, 100));
        document.getElementById("enc_nome_length").innerHTML = tamanho + "/100";
    }

    function excluirEncaminhante() {
        if ($("#pedido_encaminhante_id").val() != "") {
            if (confirm("Tem certeza que deseja deletar esse encaminhante?")) {
                $.post("/saude-beta/encaminhamento/encaminhante/excluir", {
                    _token : $("meta[name=csrf-token]").attr('content'),
                    id : $("#pedido_encaminhante_id").val()
                }, function() {
                    $("#encaminhanteModal").modal("hide");
                });
            }
        } else {
            resetaModal();
            encaminhante_tamanho();
        }
        $("#pedido_encaminhante_id").val("");
        $("#pedido_encaminhante_nome").val("");
        muda_legenda_encaminhante("");
    }

    function gravarEncaminhante() {
        var rota = $("#pedido_encaminhante_id").val() != "" ? "editar" : "salvar"
        $.post("/saude-beta/encaminhamento/encaminhante/" + rota, {
            _token : $("meta[name=csrf-token]").attr('content'),
            id : $("#pedido_encaminhante_id").val(),
            nome_fantasia : $("#enc_nome").val(),
            telefone : $("#enc_tel").val(),
            documento : $("#enc_doc").val(),
            documento_estado : $("#enc_doc_uf").val(),
            tpdoc : $("#enc_tpdoc").val(),
            esp : $("#enc_esp").val().join(',')
        }, function(data) {
            console.log(data);
            $("#encaminhanteModal").modal("hide");
            $("#pedido_encaminhante_id").val("");
            $("#pedido_encaminhante_nome").val("");
            muda_legenda_encaminhante("");
        });
    }
</script>