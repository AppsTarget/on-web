<div class="custom-table card">
    <div class="table-header-scroll">
        <h5 class="w-100 btn-link-target" style = "margin:1rem">Checkouts</h5>
        <table border = 1 style = "border-color:#ced4da">
            <thead>
                <tr>
                    <th width="10%" class="text-center">Data</th>
                    <th width="39%" class="text-left">Encaminhante</th>
                    <th width="39%" class="text-left">Para</th>
                    <th width="12%" class="text-center">Ações</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="table-body-scroll custom-scrollbar">
        <table id="table-prontuario-solicitacoes" class="table table-hover" border = 1 style = "border-color:#ced4da;table-layout: fixed;">
            <tbody></tbody>
        </table>
    </div>
</div>
@if (App\Pessoa::find(Auth::user()->id_profissional)->administrador == 'S' ||
     App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'P'   ||
     App\Pessoa::find(Auth::user()->id_profissional)->colaborador == 'A'
    )
<button onclick="abrirSolicitacao(0)" class="btn btn-primary custom-fab" type="button" title = "Nova solicitação">
    <i class="my-icon fas fa-plus"></i>
</button>
@endif
<script type = "text/javascript" language = "JavaScript">
    function abrirSolicitacao(id) {
        $('#solicitacaoModal #id_solicitacao').val(id)
        $('#solicitacaoModal #id_paciente').val($('#id_pessoa_prontuario').val());
        retiraBorda();
        if (id > 0) {
            $('#sol_enc_ret').val($('#sol' + id).data().retorno);
            var id_esp = $('#sol' + id).data().id_especialidade;
            for (var i = 0; i < especialidadesPorDepartamento.length; i++) {
                if (especialidadesPorDepartamento[i].id == id_esp) $("#sol_enc_dpt").val(especialidadesPorDepartamento[i].fk);
            }
            $('#sol_enc_dpt').trigger("change");
            $('#sol_enc_esp').val("c" + id_esp);
            $('#sol_enc_esp').trigger("change");
            $('#sol_enc_prc').val("c" + $('#sol' + id).data().id_procedimento);
            $('#sol_enc_prc').trigger("change");
            $('#sol_enc_vzs').val($('#sol' + id).data().atv_semana);
            var obs = $('#sol' + id).data().obs;
            $('#sol_enc_drc').val(obs.duracao);
            document.getElementById('sol_enc_tst').value = obs.testes.split(",");
            mostraEsp(document.getElementById("sol_enc_tst").value);
            $('#sol_enc_spr').val(obs.esporte);
            $('#sol_enc_prt').val(obs.parte);
            $('#sol_enc_obs').val(obs.obs);
        } else {
            $('#sol_enc_ret').val('');
            $("#sol_enc_dpt").val('H');
            control_solicitacao(document.getElementById("sol_enc_dpt"));
        }
        $('#solicitacaoModal').modal('show');
        $('#solicitacaoModalForm input, #solicitacaoModalForm select, #solicitacaoModalForm textarea').each(function() {
            $(this).click(function() {
                document.getElementById($(this).attr("id")).style.border = '';
            })
        });
    }

    function mudaSolEnc() {
        $('#sol_enc_esp_nome').val('');
        $('#sol_enc_esp_id').val('');
        if ($('#sol_encaminhante_id').val() != '') {
            $.get("/saude-beta/encaminhamento/especialidade/por-encaminhante", {
                id : $('#sol_encaminhante_id').val(),
                col : "id"
            }, function(data) {
                data = $.parseJSON(data);
                if (data.length == 1) {
                    $('#sol_enc_esp_nome').val(data[0].descr);
                    $('#sol_enc_esp_id').val(data[0].id);
                } else if (data.length > 1) $('#sol_enc_esp_nome').removeAttr('disabled');
                if (data.length < 2) $('#sol_enc_esp_nome').attr('disabled', true);
            });
        } else $('#sol_enc_esp_nome').removeAttr('disabled');
    }

    function validarData(dataStr) {
        var regexData = /^(\d{2})\/(\d{2})\/(\d{4})$/;
        if (!regexData.test(dataStr)) return false;
        var partesData = dataStr.match(regexData);
        var dia = parseInt(partesData[1], 10);
        var mes = parseInt(partesData[2], 10);
        var ano = parseInt(partesData[3], 10);
        var isBissexto = (ano % 4 === 0 && ano % 100 !== 0) || (ano % 400 === 0);
        if (mes < 1 || mes > 12) return false;
        var diasNoMes = [0, 31, isBissexto ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        if (dia < 1 || dia > diasNoMes[mes]) return false;
        var aux = $("#sol_enc_ret").val().split("/");
        var dataAtual = new Date();
        var dataComparar = new Date(parseInt(aux[2]), parseInt(aux[1]) - 1, parseInt(aux[0]));
        if (dataComparar <= dataAtual) return false;
        return true;
    }

    function gravarSolicitacao() {
        var erro = new Array();
        if (!validarData($("#sol_enc_ret").val())) erro.push("#sol_enc_ret");
        if (!($("#sol_enc_vzs").val() == '' || parseInt($("#sol_enc_vzs").val()) == $("#sol_enc_vzs").val())) erro.push("#sol_enc_vzs");
        if (!($("#sol_enc_prc").val() != "c403" || $("#sol_enc_tst").val().length)) erro.push("#sol_enc_tst");
        if (!erro.length) {
            var dt = $("#sol_enc_ret").val().split("/");
            $.post('/saude-beta/encaminhamento/solicitacao/gravar', {
                _token: $("meta[name=csrf-token]").attr("content"),
                id: $("#id_solicitacao").val(),
                sol_enc_esp : $("#solicitacaoModal #sol_enc_esp").val().substring(1),
                sol_enc_prc : $("#solicitacaoModal #sol_enc_prc").val().substring(1),
                id_paciente : $("#solicitacaoModal #id_paciente").val(),
                sol_enc_vzs : $("#sol_enc_vzs").val() != '' ? parseInt($("#sol_enc_vzs").val()) : 1,
                sol_enc_ret : dt[2] + "-" + dt[1] + "-" + dt[0],
                obs : JSON.stringify({
                    duracao : $("#sol_enc_drc").val(),
                    testes : $("#sol_enc_tst").val(),
                    esporte : $("#sol_enc_spr").val(),
                    parte : $("#sol_enc_prt").val(),
                    obs : $("#sol_enc_obs").val()
                })
            }, function(data) {
                $("#solicitacaoModal").modal("hide");
                solicitacoes_por_pessoa(data);
                retiraBorda();
            });
        } else {
            for (var i = 0; i < erro.length; i++) $(erro[i]).css("border", "2px solid #F00");
        }
    }

    function retiraBorda() {
        const lista = ["sol_enc_tst", "sol_enc_vzs", "sol_enc_ret"];
        for (var i = 0; i < lista.length; i++) document.getElementById(lista[i]).style.border = '';
    }

    function excluirSolicitacao(_id) {
        if (confirm("Deseja excluir essa solicitação?")) {
            $.post('/saude-beta/encaminhamento/solicitacao/excluir', {
                _token: $("meta[name=csrf-token]").attr("content"),
                id: _id
            }, function(data) {
                solicitacoes_por_pessoa(data);
            });
        }
    }

    function control_solicitacao(el) {
        const selecoes = ["sol_enc_dpt", "sol_enc_esp", "sol_enc_prc"];
        const data = [especialidadesPorDepartamento, [
            {
                id    : 75,
                descr : "PILATES",
                fk    : 1
            },
            {
                id    : 6,
                descr : "CONSULTA MEDICA",
                fk    : 2
            },
            {
                id    : 22,
                descr : "BLOQUEIO- INFILTRAÇÃO",
                fk    : 2
            },
            {
                id    : 27,
                descr : "CONSULTA MEDICA PRÉ-PARTICIPAÇÃO",
                fk    : 2
            },
            {
                id    : 28,
                descr : "CONSULTA MEDICA RETORNO",
                fk    : 2
            },
            {
                id    : 32,
                descr : "CONSULTA NUTRICIONAL",
                fk    : 10
            },
            {
                id    : 340,
                descr : "CONSULTA NUTRICIONAL RETORNO",
                fk    : 10
            },
            {
                id    : 49,
                descr : "FISIOTERAPIA",
                fk    : 11
            },
            {
                id    : 74,
                descr : "OSTEOPATIA",
                fk    : 11
            },
            {
                id    : 404,
                descr : "LIBERAÇÃO MIOFASCIAL",
                fk    : 11
            },
            {
                id    : 62,
                descr : "IEC INICIAL",
                fk    : 12
            },
            {
                id    : 403,
                descr : "IEC AVANÇADO",
                fk    : 12
            },
            {
                id    : 112,
                descr : "TERAPIA POR ONDAS DE CHOQUES",
                fk    : 34
            },
            {
                id    : 115,
                descr : "VISCOSUPLEMENTAÇÃO",
                fk    : 34
            }
        ]];
        var resultado = "";
        if (selecoes.indexOf(el.id) < 2) {
            for (var i = 0; i < data[selecoes.indexOf(el.id)].length; i++) {
                var ref = data[selecoes.indexOf(el.id)][i];
                if (
                    ref.fk == $('#' + selecoes[selecoes.indexOf(el.id)]).val() ||
                    "c" + ref.fk == $('#' + selecoes[selecoes.indexOf(el.id)]).val()
                ) resultado += "<option value = 'c" + ref.id + "'>" + ref.descr + "</option>";
            }
            document.getElementById(selecoes[selecoes.indexOf(el.id) + 1]).innerHTML = resultado;
            for (var i = 0; i < selecoes.length; i++) {
                if (i > (selecoes.indexOf(el.id) + 1)) document.getElementById(selecoes[i]).innerHTML = "";
                document.getElementById(selecoes[i]).disabled = (i > (selecoes.indexOf(el.id) + 1)) || document.getElementById(selecoes[i]).children.length < 2;
            }
            if (!selecoes.indexOf(el.id)) control_solicitacao(document.getElementById(selecoes[1]));
        }
        if (document.getElementById("sol_enc_esp").value != "c11") {
            document.getElementById("sol_enc_vzs").value = "";
            document.getElementById("sol_enc_drc").value = "";
            document.getElementById("sol_enc_vzs").parentElement.style.display = "none";
            document.getElementById("sol_enc_drc").parentElement.style.display = "none";
        } else {
            document.getElementById("sol_enc_vzs").parentElement.style.display = "block";
            document.getElementById("sol_enc_drc").parentElement.style.display = "block";
        }
        if (document.getElementById("sol_enc_prc").value != "c403") {
            document.getElementById("sol_enc_tst").value = "";
            document.getElementById("sol_enc_tst").parentElement.style.display = "none";
        } else document.getElementById("sol_enc_tst").parentElement.style.display = "block";
        if (["c2", "c10", "c34"].indexOf(document.getElementById("sol_enc_esp").value) == -1) {
            document.getElementById("sol_enc_obs").value = "";
            document.getElementById("sol_enc_obs").parentElement.style.display = "none";
        } else document.getElementById("sol_enc_obs").parentElement.style.display = "block";
        mostraEsp(document.getElementById("sol_enc_tst").value);
    }

    function mostraEsp(valor) {
        if (valor.indexOf("c1") == -1) {
            document.getElementById("sol_enc_spr").value = "";
            document.getElementById("sol_enc_spr").parentElement.style.display = "none";
        } else document.getElementById("sol_enc_spr").parentElement.style.display = "block";
        if (valor.indexOf("c4") == -1) {
            document.getElementById("sol_enc_prt").value = "sup";
            document.getElementById("sol_enc_prt").parentElement.style.display = "none";
        } else document.getElementById("sol_enc_prt").parentElement.style.display = "block";
    }
</script>

@include("modals/solicitacao_modal")
@include("modals/infsol_modal")