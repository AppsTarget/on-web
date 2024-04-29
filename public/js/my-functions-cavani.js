// Caixa \\
function toCapitalize(value) {
    try {
        array = value.split(' ')
        result = ''
    
        array.forEach(el => {
            if (el != '') {
                if (el != '') el += ' '
                result += el[0].toUpperCase() + el.substr(1).toLowerCase()
            }
        })
        return result;    
    } catch(err) {
        return "APP"
    }
}

function add_consultor_caixa() {
    if (!campo_invalido("#caixa_consultor_profissional_id", true)) {
        nome_consultor = $("#caixa_consultor_profissional_descr").val();
        id_consultor = $("#caixa_consultor_profissional_id").val();

        if (nome_consultor != nome_consultor.substr(0, 28))
            nome_consultor = nome_consultor.substr(0, 28);

        html = '<tr value="' + id_consultor + '">';
        html += '   <td width="100%">';
        html += nome_consultor;
        html += "   </td>";
        html += "   <td>";
        html +=
            '       <img style="opacity:.8;cursor: pointer" src="http://vps.targetclient.com.br/saude-beta/img/lixeira.png" onclick="$(this).parent().parent().remove()">';
        html += "</tr>";

        $("#table-cadastro-caixa tbody").append(html);
        $("#caixa_consultor_profissional_descr").val("");
        $("#caixa_consultor_profissional_id").val("");
    }
}



function selecionarPlanoContasreceber() {

}
function salvarCadastroCaixa() {
    operadores_ar = [];
    document.querySelectorAll("#table-cadastro-caixa tr").forEach((el) => {
        operadores_ar.push($(el).attr("value"));
    });
    $.post(
        "/saude-beta/caixa/salvar-cadastro-caixa",
        {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: $("#cadastroCaixaModal #id_caixa").val(),
            descr: $("#cadastroCaixaModal #descr").val(),
            situacao: $("#cadastroCaixaModal #situacao").val(),
            emp: $("#cadastroCaixaModal #empresa").val(),
            h_abertura: $("#cadastroCaixaModal #horainicial").val(),
            h_fechamento: $("#cadastroCaixaModal #horafinal").val(),
            operadores: operadores_ar,
        },
        function (data, status) {
            console.log(data + " | " + status);
            if (data == "S") {
                $("#table-cadastro-caixa tbody").empty();
                $("#caixa_consultor_profissional_descr").val("");
                $("#caixa_consultor_profissional_id").val("");
                $("#cadastroCaixaModal #descr").val("");
                $("#cadastroCaixaModal #situacao").val("");
                $("#cadastroCaixaModal #empresa").val("");
                $("#cadastroCaixaModal #horainicial").val("");
                $("#cadastroCaixaModal #horafinal").val("");
                $("#cadastroCaixaModal").modal("hide");
                location.reload(true);
            }
        }
    );
}

function verificar_caixa_aberto($value) {
    $.get(
        "/saude-beta/caixa/verificar-situacao",
        {},
        function (data, status) {
            console.log(data + " | " + status);
            data = $.parseJSON(data);
            if (data.situacao == "X") {
                alert("Seu usuário não é um operador de caixa desta empresa");
            } else {
                if (data.abrir_fechar == true) {
                } else {
                    return true;
                }
            }
        }
    );
}

// function abrir_pedido() {
//     $.get('/saude-beta/caixa/verificar-situacao', {},
//         function (data, status) {
//             console.log(data + ' | ' + status)
//             data = $.parseJSON(data)
//             if (data.situacao == 'X') {
//                 alert('Seu usuário não está vinculado ao caixa desta empresa!')
//             }
//             else {
//                 if (data.situacao == 'A') {
//                     $.get('/saude-beta/pedido/gerar-num', function (data, status) {
//                         console.log(data + '|' + status)
//                         $('#pedidoModalLabel').html('Contrato | Nº #' + ("000000" + data.num_pedido).slice(-6));
//                         $('#pedidoModal #pedido_validade').val(moment().add(15, 'days').format('DD/MM/YYYY'));
//                         $('#pedidoModal #pedido_id').val(data.id);
//                         $('#pedidoModal #salvar-pedido').html('Salvar');
//                         $('#pedidoModal #status-pedido')
//                             .html('Novo')
//                             .removeAttr('class')
//                             .addClass('tag-pedido-primary');

//                         $('#pedidoModal').modal('show');
//                         setTimeout(function () {
//                             $("#pedidoModal #pedido_paciente_nome").first().focus();
//                             $("#pedidoModal #pedido_paciente_id").trigger('change');
//                         }, 50);
//                     });
//                 }
//                 else {
//                     switch (data.situacao) {
//                         case 'A':
//                             text_aux1 = 'Para continuar será necessário fechar caixa aberto anteriormente?'

//                             if (formatDataBr(data.data_ult_abertura) == '01/01/0001') {
//                                 text_aux2 = 'Caixa nunca foi aberto anteriormente!'
//                             }
//                             else {
//                                 text_aux2 = 'Caixa foi aberto pela última vez em ' + formatDataBr(data.data_ult_abertura)
//                             }
//                             break;
//                         case 'F':
//                             text_aux1 = 'Para continuar será necessário abrir o caixa de hoje!'

//                             if (formatDataBr(data.data_ult_abertura) == '01/01/0001') {
//                                 text_aux2 = 'Caixa nunca foi aberto anteriormente!'
//                             }
//                             else {
//                                 text_aux2 = 'Caixa foi aberto pela última vez em ' + formatDataBr(data.data_ult_abertura)
//                             }

//                             break;
//                     }
//                     ShowConfirmationBox(
//                         text_aux1,
//                         text_aux2,
//                         true, true, false,
//                         function () {
//                             $('#caixaModal').modal('show')
//                             atualizarCaixaModal() },
//                         function () { console.log(false) },
//                         'Sim',
//                         'Não'
//                     );
//                 }
//             }
//         })
// }

function abrir_pedido() {
    $.get(
        "/saude-beta/caixa/verificar-situacao",
        {},
        function (data, status) {
            console.log(data + " | " + status);
            data = $.parseJSON(data);
            auxiliar = data;
            if (data.situacao == "X") {
                alert("Seu usuário não está vinculado ao caixa desta empresa!");
            } else {
                if (data.situacao == "A" && data.abrir_fechar == false) {
                    $.get(
                        "/saude-beta/pedido/gerar-num",
                        function (data, status) {
                            console.log(data + "|" + status);
                            $("#pedidoModalLabel").html(
                                "Contrato | Nº #" +
                                ("000000" + data.num_pedido).slice(-6)
                            );
                            $("#pedidoModal #pedido_validade").val(
                                moment().add(15, "days").format("DD/MM/YYYY")
                            );
                            $("#pedidoModal #pedido_id").val(data.id);
                            $("#pedidoModal #salvar-pedido").html("Salvar");
                            $("#pedidoModal #status-pedido")
                                .html("Novo")
                                .removeAttr("class")
                                .addClass("tag-pedido-primary");

                            $("#pedidoModal").modal("show");
                            setTimeout(function () {
                                $("#pedidoModal #pedido_paciente_nome")
                                    .first()
                                    .focus();
                                $("#pedidoModal #pedido_paciente_id").trigger(
                                    "change"
                                );
                            }, 50);
                        }
                    );
                } else {
                    switch (data.situacao) {
                        case "A":
                            text_aux1 =
                                "Para continuar será necessário fechar caixa aberto anteriormente.";

                            /*if (
                                formatDataBr(data.data_ult_abertura) ==
                                "01/01/0001"
                            ) {
                                text_aux2 =
                                    "Caixa nunca foi aberto anteriormente!";
                            } else {
                                text_aux2 =
                                    "Caixa foi aberto pela última vez em " +
                                    formatDataBr(data.data_ult_abertura);
                            }*/
                            break;
                        case "F":
                            text_aux1 =
                                "Para continuar será necessário abrir o caixa de hoje.";

                            /* if (
                                 formatDataBr(data.data_ult_abertura) ==
                                 "01/01/0001"
                             ) {
                                 text_aux2 =
                                     "Caixa nunca foi aberto anteriormente!";
                             } else {
                                 text_aux2 =
                                     "Caixa foi aberto pela última vez em " +
                                     formatDataBr(data.data_ult_abertura);
                             }*/

                            break;
                    }
                    text_aux2 = "Deseja continuar?";
                    ShowConfirmationBox(
                        text_aux1,
                        text_aux2,
                        true,
                        true,
                        false,
                        function () {
                            $("#caixaModal").modal("show");
                            console.log(
                                formatDataBr(auxiliar.data_ult_abertura) ==
                                "01/01/0001"
                            );
                            if (
                                formatDataBr(auxiliar.data_ult_abertura) ==
                                "01/01/0001"
                            ) {
                                $("#caixaModal #id_caixa").val(
                                    auxiliar.id_caixa
                                );
                                var data = new Date();
                                var dia = String(data.getDate()).padStart(
                                    2,
                                    "0"
                                );
                                var mes = String(data.getMonth() + 1).padStart(
                                    2,
                                    "0"
                                );
                                var ano = data.getFullYear();
                                dataAtual = ano + "-" + mes + "-" + dia;
                                $("#caixaModal #data-selecionada").val(
                                    dataAtual
                                );
                            } else {
                                $("#caixaModal #id_caixa").val(
                                    auxiliar.id_caixa
                                );
                                $("#caixaModal #data-selecionada").val(
                                    auxiliar.data_ult_abertura
                                );
                                console.log(auxiliar.data_ult_abertura);
                                atualizarCaixaModal();
                                $("#caixaModal").modal("show");
                            }
                            atualizarCaixaModal();
                        },
                        function () {
                            console.log(false);
                        },
                        "Sim",
                        "Não"
                    );
                }
            }
        }
    );
}

function controlContaCaixa($obj) {
    $('#contaBancariaModal #conta_cofre').prop('checked', false)
    if ($obj.prop("checked")) {
        $("#contaBancariaModal #caixa").parent().show();
        $("#contaBancariaModal #banco_descr").val("").attr("disabled", true);
        $("#contaBancariaModal #banco_id").val("").attr("disabled", true);
        $("#contaBancariaModal #conta").val("").attr("disabled", true);
        $("#contaBancariaModal #agencia").val("").attr("disabled", true);
    } else {

        $("#contaBancariaModal #banco_descr").val("").removeAttr("disabled");
        $("#contaBancariaModal #banco_id").val("").removeAttr("disabled");
        $("#contaBancariaModal #conta").val("").removeAttr("disabled");
        $("#contaBancariaModal #agencia").val("").removeAttr("disabled");
    }
}
function controlContaCofre($obj) {
    $('#contaBancariaModal #conta_caixa').prop('checked', false)
    if ($obj.prop("checked")) {
        $("#contaBancariaModal #banco_descr").val("").attr("disabled", true);
        $("#contaBancariaModal #banco_id").val("").attr("disabled", true);
        $("#contaBancariaModal #conta").val("").attr("disabled", true);
        $("#contaBancariaModal #agencia").val("").attr("disabled", true);
    } else {
        $("#contaBancariaModal #banco_descr").val("").removeAttr("disabled");
        $("#contaBancariaModal #banco_id").val("").removeAttr("disabled");
        $("#contaBancariaModal #conta").val("").removeAttr("disabled");
        $("#contaBancariaModal #agencia").val("").removeAttr("disabled");
    }
}
function bloquearCadastroCaixa($id) {
    $.post(
        "/saude-beta/caixa/bloquear-cadastro-caixa",
        {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: $id,
        },
        function (data, status) {
            console.log(data + " | " + status);
            alert("Bloqueado com sucesso");
            location.reload(true);
        }
    );
}
function desbloquearCadastroCaixa($id) {
    $.post(
        "/saude-beta/caixa/desbloquear-cadastro-caixa",
        {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: $id,
        },
        function (data, status) {
            console.log(data + " | " + status);
            alert("Desbloqueado com sucesso");
            location.reload(true);
        }
    );
}
function excluirCadastroCaixa($id) {
    $.post(
        "/saude-beta/caixa/excluir-cadastro-caixa",
        {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: $id,
        },
        function (data, status) {
            console.log(data + " | " + status);
            alert("Excluído com sucesso");
            location.reload(true);
        }
    );
}

function separar_dinheiro(val) {
    val = val.split(",");
    return val[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".") + "," + val[1];
}

function add_forma_pag_pedido() {
    var row_number = $("#table-pedido-forma-pag > tbody tr").length + 1,
        html = "";
    
    creditos = $("#pedidoModal #creditos-pessoa");
    valor = $("#pedidoModal #pedido_forma_pag_valor");
    if ($("#pedidoModal #pedido_forma_pag").val() == 101) {
        if (parseFloat(valor.val()) > parseFloat(creditos.data().creditos)) {
            alert("Não há créditos suficientes");
            return;
        }
    }
    if (
        $("#pedido_forma_pag_parcela").val() != "" &&
        $("#pedido_forma_pag_parcela").val() != 0
    )
        parcelas = $("#pedido_forma_pag_parcela").val();
    else parcelas = 1;
    if ((valor.val() == 0 && $("#pedidoModal #pedido_forma_pag").val() != 103 && $("#pedidoModal #pedido_forma_pag").val() != 100 && $("#pedidoModal #pedido_forma_pag").val() != 11) || valor.val() == "" || valor.val() == null) {
        alert("Valor incorreto!");
        return;
    }
    html = '<tr row_number="' + row_number + '">';
    html +=
        '    <td width="25%" data-forma_pag="' +
        $("#pedido_forma_pag").val() +
        '">';
    html += $("#pedido_forma_pag option:selected").text();
    html += "    </td>";
    if ($('#pedidoModal #pedido_forma_pag').val() == 4 || $('#pedidoModal #pedido_forma_pag').val() == 5) {
        html += '    <td width="25%" data-financeira_id="' + $("#pedidoModal #conta-bancaria").val() + '">';
        if ($("#pedidoModal #conta-bancaria").val() != 0) html += $("#pedidoModal #conta-bancaria option:selected").text();
    }
    else {
        html += '    <td width="25%" data-financeira_id="' + $("#pedidoModal #financeira").val() + '">';
        if ($("#pedidoModal #financeira").val() != 0) html += $("#pedidoModal #financeira option:selected").text();
    }
    html += "    </td>";
    html +=
        '    <td width="15%" data-forma_pag_parcela="' +
        parcelas +
        '"  class="text-right">';
    html +=
        parcelas +
        "x de R$ " +
        (
            parseFloat($("#pedido_forma_pag_valor").val().replace(",", ".").replace("R$ ", "")) /
            parseInt(parcelas)
        )
            .toFixed(2)
            .toString()
            .replace(".", ",");
    html += "    </td>";
    html +=
        '    <td width="15%" data-forma_pag_valor="' +
        $("#pedido_forma_pag_valor").val().replace('R$ ', '') +
        '"  class="text-right">';
    html += "       R$ " + $("#pedido_forma_pag_valor").val().replace('.', ',').replace('R$ ', '');
    html += "    </td>";
    html +=
        '    <td width="15%" data-pedido_data_vencimento="' +
        $("#pedido_data_vencimento").val() +
        '">';
    html += $("#pedido_data_vencimento").val();
    html += "    </td>";
    html += '    <td width="5%">';
    html +=
        '        <i class="my-icon far fa-trash-alt" onclick="deletar_pedido_grid(' +
        "'table-pedido-forma-pag'," +
        row_number +
        "); deletar_pedido_grid(" +
        "'table-pedido-forma-pag-resumo'," +
        row_number +
        '); att_pedido_total_proc_pagamento()"></i>';
    html += "    </td>";
    html += "</tr>";
    $("#table-pedido-forma-pag > tbody").append(html);

    html = '<tr row_number="' + row_number + '">';
    html +=
        '    <td width="27.5%" data-forma_pag="' +
        $("#pedido_forma_pag").val() +
        '">';
    html += $("#pedido_forma_pag option:selected").text();
    html += "    </td>";
    html +=
        '    <td width="27.5%" data-financeira_id="' +
        $("#financeira").val() +
        '">';
    if ($("#financeira").val() != 0)
        html += $("#financeira option:selected").text();
    html += "    </td>";
    html +=
        '    <td width="15%" data-forma_pag_parcela="' +
        $("#pedido_forma_pag_parcela").val() +
        '"  class="text-right">';
    html +=
        $("#pedido_forma_pag_parcela").val() +
        "x de R$ " +
        separar_dinheiro(
            (
                parseFloat($("#pedido_forma_pag_valor").val().replace(".", "").replace(",", ".").replace("R$ ", "")) /
                parseInt($("#pedido_forma_pag_parcela").val())
            )
                .toFixed(2)
                .toString()
                .replace(".", ",")
        );
    html += "    </td>";
    html +=
        '    <td width="15%" data-forma_pag_valor="' +
        $("#pedido_forma_pag_valor").val().replace("R$ ", "") +
        '"  class="text-right">';
    html += "      " + separar_dinheiro($("#pedido_forma_pag_valor").val().replace(".", "").replace(".", ","));
    html += "    </td>";
    html +=
        '    <td width="15%" data-pedido_data_vencimento="' +
        $("#pedido_data_vencimento").val() +
        '">';
    html += $("#pedido_data_vencimento").val();
    html += "    </td>";
    html += "</tr>";
    $("#table-pedido-forma-pag-resumo > tbody").append(html);

    $("#pedido_forma_pag_parcela").val(1);
    $("#pedido_forma_pag_valor").val("");
    att_pedido_total_proc_pagamento();
}

function att_pedido_total_proc_pagamento() {
    var total_parcelas = 0,
        total_valor = 0;
    $("#pedidoModal #table-pedido-forma-pag > tbody tr").each(function () {
        total_parcelas += parseInt(
            $(this)
                .find("[data-forma_pag_parcela]")
                .data()
                .forma_pag_parcela.toString()
                .replace(",", ".")
        );
        total_valor += parseFloat(
            $(this)
                .find("[data-forma_pag_valor]")
                .data()
                .forma_pag_valor.toString()
                .replace(",", ".")
                .replace("R$ ", "")
        );
    });

    $("#pedidoModal #table-pedido-forma-pag [data-total_pag_parcela]")
        .data("total_pag_parcela", total_parcelas)
        .attr("data-total_pag_parcela", total_parcelas)
        .html(total_parcelas);
    console.log("TOTAL_VALOR: " + total_valor);
    $("#pedidoModal #table-pedido-forma-pag [data-total_pag_valor]")
        .data("total_pag_valor", total_valor)
        .attr("data-total_pag_valor", total_valor)
        .html(
            "R$ " +
            parseFloat(total_valor).toFixed(2).toString().replace(".", ",")
        );

    $("#pedidoModal #table-pedido-forma-pag-resumo [data-total_pag_parcela]")
        .data("total_pag_parcela", total_parcelas)
        .attr("data-total_pag_parcela", total_parcelas)
        .html(total_parcelas);

    $("#pedidoModal #table-pedido-forma-pag-resumo [data-total_pag_valor]")
        .data("total_pag_valor", total_valor)
        .attr("data-total_pag_valor", total_valor)
        .html(
            parseFloat(total_valor).toFixed(2).toString().replace(".", ",")
        );

    calcularTroco();
}
function getValTroco() {
    total_pagar = $("[data-total_pag_pendente]").data().total_pag_pendente;
    total_pago = $("[data-total_pag_valor]").data().total_pag_valor;

    return (
        parseFloat(total_pago.toString().replace(",", ".")) -
        parseFloat(total_pagar.toString().replace(",", "."))
    );
}
function temTroco() {
    total_pagar = $("[data-total_pag_pendente]").data().total_pag_pendente;
    total_pago = $("[data-total_pag_valor]").data().total_pag_valor;

    troco =
        parseFloat(total_pago.toString().replace(",", ".")) -
        parseFloat(total_pagar.toString().replace(",", "."));
    if (troco > 0) {
        return true;
    } else {
        return false;
    }
}

function calcularTroco() {
    total_pagar = $("[data-total_pag_pendente]").data().total_pag_pendente;
    total_pago = $("[data-total_pag_valor]").data().total_pag_valor;

    troco =
        parseFloat(total_pago.toString().replace(",", ".")) -
        parseFloat(total_pagar.toString().replace(",", "."));
    if (troco > 0) {
        $("[data-total_troco]")
            .data("total_troco", troco)
            .html(
                "Valor total do troco - R$ " +
                troco.toString().replace(".", ",")
            );
    } else {
        $("[data-total_troco]").data("total_troco", "").html("");
    }
}

var desc_sup = 0;
var desc_motivo = "";
function validarSupervisor() {
    $.post("/saude-beta/pedido/validar-supervisor", {
        _token: $("meta[name=csrf-token]").attr("content"),
        email : $("#emailsup").val(),
        password : $("#passwordsup").val()
    }, function(data) {
        if (parseInt(data)) {
            if (!montando_resumo) {
                desc_sup = data;
                desc_motivo = $("#motivosup").val();
                salvar_pedido_main();
            } else {
                permitiuPagamentoDiferente = true;
                $("#supervisorModal").modal("hide");
                $("#avancar-pedido").trigger("click");
            }
        } else alert("Senha incorreta");
    });
}

function salvar_pedido() {
    if (alterouPlano.length) $("#supervisorModal").modal("show");
    else salvar_pedido_main();
}

function salvar_pedido_main() {
    if (location.href.indexOf("agenda") > -1) {
        if (window.confirm("Deseja finalizar agendamento?")) {
            $.post(
                "/saude-beta/agenda/finalizar-agendamento",
                {
                    _token: $("meta[name=csrf-token]").attr("content"),
                    id: $("#pedidoModal #agenda_id").val(),
                    id_modalidade: $(
                        "#criarAgendamentoModal #procedimento_id"
                    ).val(),
                },
                function (data, status) {
                    a = data;
                    console.log(data + " | " + status);
                    if (!isNaN(data)) {
                        alert("Agendamento confirmado");
                        // location.reload()
                    } else alert("erro");
                }
            );
        } else return;
    }
    var id = $("#pedidoModal #pedido_id").val(),
        tipo_forma_pag = $("#pedidoModal #pedido_forma_pag_tipo").val(),
        id_paciente = $("#pedidoModal [data-resumo_paciente]").data()
            .resumo_paciente,
        id_convenio = $("#pedidoModal #pedido_id_convenio").val(),
        data = $("#pedidoModal #data-resumo_validade").html(),
        id_profissional_exa = $(
            "#pedidoModal [data-resumo_profissional_exa]"
        ).data().resumo_profissional_exa,
        obs = $("#pedidoModal [data-resumo_obs]").data().resumo_obs,
        planos = [],
        formas_pag = [];

    data_validade =
        data[6] +
        data[7] +
        data[8] +
        data[9] +
        "-" +
        data[3] +
        data[4] +
        "-" +
        data[0] +
        data[1];
    // if (confirm("Atenção!\nDeseja gerar contrato já finalizado?")) {
    //     _status = "F";
    // } else {
    //     _status = "A";
    // }
    _status = 'F'

    if (temTroco()) {
        if (confirm("O troco está saindo do caixa?")) {
            _troco = "S";
        } else {
            _troco = "N";
        }
    } else {
        _troco = "X";
    }

    if ($("#pedidoModal #button-aceitar").val() == 1) {
        $.get(
            "/saude-beta/encaminhamento/salvarsucesso",
            {
                _token: $("meta[name=csrf-token]").attr("content"),
                id_agendamento: $("#pedidoModal #agenda_id").val(),
            },
            function (data, status) {
                console.log(id_agendamento);
                console.log(data + "|" + status);
            }
        );
    }

    $("#pedidoModal #table-pedido-forma-pag-resumo tbody tr").each(function () {
        formas_pag.push({
            id_forma_pag: $(this).find("[data-forma_pag]").data().forma_pag,
            id_financeira: $(this).find("[data-financeira_id]").data()
                .financeira_id,
            parcela: $(this).find("[data-forma_pag_parcela]").data()
                .forma_pag_parcela,
            forma_pag_valor: String(
                $(this).find("[data-forma_pag_valor]").data().forma_pag_valor
            ).replace(",", ".").replace("R$ ", ""),
            data_vencimento: $(this)
                .find("[data-pedido_data_vencimento]")
                .data().pedido_data_vencimento,
        });
    });

    $("#pedidoModal #tabela-planos > tbody tr").each(function () {
        id_p = $(this).find("[data-plano_id]").data().plano_id;
        planos.push({
            id_plano: id_p,
            list_id: $(this).find("[data-plano_id_pessoas]").val(),
            qtd: $(this).find("#n_pessoas").html(),
            valor: parseInt(phoneInt($(this).find("#valor_plano").html())) / 100,
            valor_original: valoresPlanoReal["p" + id_p]
        });
    });

    console.log(planos);
    var enviar = {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: id,
        tipo_forma_pag: tipo_forma_pag,
        id_paciente: id_paciente,
        id_convenio: id_convenio,
        id_profissional_exa: id_profissional_exa,
        data_validade: data_validade,
        status: _status,
        troco: _troco,
        vtroco: getValTroco(),
        obs: obs,
        formas_pag: formas_pag,
        planos: planos,
        id_agendamento: $("#pedidoModal #agenda_id").val(),
        d_sup: desc_sup,
        d_motivo: desc_motivo
    };
    if ($("#pedido_encaminhante_id").val() !== undefined) {
        enviar.enc_id_de = $("#pedido_encaminhante_id").val();
        enviar.enc_id_especialidade = $("#pedido_enc_esp_id").val();
        enviar.enc_id_cid = $("#enc_cid_id").val();
        enviar.enc_data = $("#enc_data").val();
        enviar.enc_sol = parseInt($("input[name=sol_pedido]:checked").attr("id").substring(3));
        enviar.enc_para = $("#pedido_enc_para_id").val();
    }

    $.get(
        "/saude-beta/pedido/salvar", enviar,
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                data = $.parseJSON(data);
                if (data.contrato == 'S') {
                    ShowConfirmationBox(
                        "Deseja enviar o contrato para o associado?",
                        "",
                        true,
                        true,
                        false,
                        function () {
                            anexos_enc_pedido(data.id_paciente, data.id);
                            $("#pedidoModal").modal("hide");
                            $("#criarAgendamentoModal").modal("hide");
                            $("#supervisorModal").modal("hide");
                            abrirModalContratoZapSign(data.id, data.id_paciente);
                        },
                        function () {
                            anexos_enc_pedido(data.id_paciente, data.id);
                            $("#supervisorModal").modal("hide");
                            fecharPedidoModal()
                        },
                        "Sim",
                        "Não"
                    );
                }
                else {
                    anexos_enc_pedido(data.id_paciente, data.id);
                    $("#supervisorModal").modal("hide");
                    fecharPedidoModal()
                }
            }
        }
    );
}
function anexos_enc_pedido(id_paciente, id_pedido) {
    if ($("#enc_arquivo").val() != "") {
        $("#enc_paciente").val(id_paciente);
        $("#enc_pedido").val(id_pedido);
        var form = $('#enc_arquivo_form')[0];
        var data = new FormData(form);
        a = data;
        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: "/saude-beta/encaminhamento/anexar/pedido",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (data) {
                console.log(data);
            },
            error: function (e) {
                console.log(e);
            }
        });
    }
}
function anexos_enc_agenda(id_agendamento, id_paciente, id_profissional) {
    if ($("#enc_arquivo").val() != "") {
        $("#enc_agendamento").val(id_agendamento);
        $("#enc_paciente").val(id_paciente);
        $("#enc_profissional").val(id_profissional);
        var form = $('#enc_arquivo_form')[0];
        var data = new FormData(form);
        a = data;
        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: "/saude-beta/encaminhamento/anexar/agenda",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (data) {
                console.log(data);
            },
            error: function (e) {
                console.log(e);
            }
        });
    }
}
function fecharPedidoModal() {
    if (
        location.href ===
        "http://vps.targetclient.com.br/saude-beta/agenda" ||
        location.href ===
        "http://vps.targetclient.com.br/saude-beta/agenda#"
    ) {
        $("#pedidoModal").modal("hide");
        $("#criarAgendamentoModal").modal("hide");
        $('#zapsignPessoaModal').modal('hide');
        mostrar_agendamentos_semanal();
        mostrar_agendamentos();
        pesquisarAgendamentosPendentes();
    } else {
        alert("Pedido realizado com sucesso");
        location.reload(true);
    }
}
function gerarContratoZapSign(id_pedido, bPessoa = false) {

    if (!validaCamposContratoPaciente()) {
        /*mostrar_agendamentos()
        mostrar_agendamentos_semanal()
        document.querySelectorAll('.modal').forEach(el => {
            $(el).modal('hide')
        })*/
        return;
    } else setLoadingContrato(true)

    atualizarCadastroPessoaContrato($('#zapSignPessoaModal #id_pessoa').val())
    setTimeout(() => {
        if (bPessoa) {
            ShowConfirmationBox(
                "Deseja enviar o link por e-mail?",
                "",
                true,
                true,
                false,
                function () {
                    enviarLinkAssinaturaEmail(id_pedido)
                },
                function () {
                    enviarLinkAssinaturaWhatsapp(id_pedido)
                },
                "Sim",
                "Não"
            );
            return
        }


        $.get('/saude-beta/ZapSign/cadastrar-signatario/' + id_pedido,
            function (data, status) {
                console.log(data + ' | ' + status)
                ShowConfirmationBox(
                    "Deseja enviar o link por e-mail?",
                    "",
                    true,
                    true,
                    true,
                    function () {
                        enviarLinkAssinaturaEmail(id_pedido)
                    },
                    function () {
                        enviarLinkAssinaturaWhatsapp(id_pedido)
                    },
                    "Sim",
                    "Não"
                );
                if (window.location.pathname.includes('/pessoa/prontuario')) {
                    pedidos_por_pessoa($('#id_pessoa_prontuario').val());
                }
                exibir_contrato_zap_sign(id_pedido)
            })
    }, 5000)
}

function enviarLinkAssinaturaEmail(id) {
    $.get(
        '/saude-beta/ZapSign/enviar/email', {
        _token: $('meta[name=csrf-token').attr('content'),
        id_pedido: id
    },
        function (data, status) {
            console.log(data + ' | ' + status)
            if (data == 'true') {
                alert('Enviado com sucesso')
                fecharPedidoModal()
            }
        }
    )
}
function enviarLinkAssinaturaWhatsapp(id) {
    mostrar_agendamentos()
    mostrar_agendamentos_semanal()
    document.querySelectorAll('.modal').forEach(el => {
        $(el).modal('hide')
    })

    return
    $.post(
        '/saude-beta/ZapSign/enviar/whatsapp'
    )
}


function setLoadingContrato(bool) {
    if (bool) $('#zapsignPessoaModal #loading-contrato').attr('style', "opacity: 0.8;display: flex;justify-content: center;width: 100%;height: 414px;margin-top: -15px;margin-bottom: -44.5%;background: white;position: relative;z-index: 2;")
    else $('#zapsignPessoaModal #loading-contrato').attr('style', "opacity: 0.8;display: none;justify-content: center;width: 100%;height: 414px;margin-top: -15px;margin-bottom: -44.5%;background: white;position: relative;z-index: 2;")
}

function exibir_contrato_zap_sign(id_contrato) {
    // $.get('/saude-beta/ZapSign/exibir-contrato/'+ id_contrato,
    // function(data, status) {
    //     console.log(data + ' | ' + status)

    // })
}

function atualizarCadastroPessoaContrato(id_pessoa) {
    console.log(id_pessoa)
    $.get('/saude-beta/pessoa/mostrar/' + id_pessoa,
        function (data, status) {
            console.log(data + ' | ' + status)
            if ($("#zapsignPessoaModal #nome").val() != data.nome_fantasia ||
                $("#zapsignPessoaModal #cpf").val() != data.cpf_cnpj ||
                $("#zapsignPessoaModal #rg").val() != data.rg_ie ||
                $("#zapsignPessoaModal #cep").val() != data.cep ||
                $("#zapsignPessoaModal #cidade").val() != data.cidade ||
                $("#zapsignPessoaModal #uf").val() != data.uf ||
                $("#zapsignPessoaModal #endereco").val() != data.endereco ||
                $("#zapsignPessoaModal #numero").val() != data.numero ||
                $("#zapsignPessoaModal #bairro").val() != data.bairro ||
                $("#zapsignPessoaModal #complemento").val() != data.complemento ||
                $("#zapsignPessoaModal #email").val() != data.email ||
                $("#zapsignPessoaModal #celular").val() != data.celular1) {
                $.post('/saude-beta/pessoa/atualizar-cadastro-contrato', {
                    _token: $('meta[name=csrf-token]').attr('content'),
                    id_paciente: $('#zapsignPessoaModal #id_pessoa').val(),
                    nome_fantasia: $("#zapsignPessoaModal #nome").val(),
                    cpf_cnpj: $("#zapsignPessoaModal #cpf").val(),
                    rg_ie: $("#zapsignPessoaModal #rg").val(),
                    cep: $("#zapsignPessoaModal #cep").val(),
                    cidade: $("#zapsignPessoaModal #cidade").val(),
                    uf: $("#zapsignPessoaModal #uf").val(),
                    endereco: $("#zapsignPessoaModal #endereco").val(),
                    numero: $("#zapsignPessoaModal #numero").val(),
                    bairro: $("#zapsignPessoaModal #bairro").val(),
                    complemento: $("#zapsignPessoaModal #complemento").val(),
                    celular: $('#zapsignPessoaModal #celular').val(),
                    email: $('#zapsignPessoaModal #email').val()
                })
            }
        })
}
function validaCamposContratoPaciente() {
    resposta = "Resolva os seguintes problemas:\n\n"
    aux = "Resolva os seguintes problemas:\n\n"

    if (campo_invalido('#zapsignPessoaModal #id_pedido', true)) {
        resposta += '\n- Erro de contrato'
    }
    if (campo_invalido('#zapsignPessoaModal #id_pessoa', true)) {
        resposta += '\n- Erro de paciente'
    }


    if (campo_invalido('#zapsignPessoaModal #nome', false)) {
        resposta += '\n- Nome do associado inválido'
    }
    if (campo_invalido('#zapsignPessoaModal #cpf', false)) {
        resposta += '\n- CPF inválido'
    }
    if (campo_invalido('#zapsignPessoaModal #rg', false)) {
        resposta += '\n- RG inválido'
    }
    if (campo_invalido('#zapsignPessoaModal #cep', false)) {
        resposta += '\n- CEP inválido'
    }
    if (campo_invalido('#zapsignPessoaModal #cidade', false)) {
        resposta += '\n- Cidade inválida'
    }
    if (campo_invalido('#zapsignPessoaModal #uf', false)) {
        resposta += '\n- UF inválido'
    }
    if (campo_invalido('#zapsignPessoaModal #endereco', false)) {
        resposta += '\n- Endereço inválido'
    }
    if (campo_invalido('#zapsignPessoaModal #numero', true)) {
        resposta += '\n- Numero do endereço incorreto'
    }
    if (campo_invalido('#zapsignPessoaModal #bairro', false)) {
        resposta += '\n- Bairro inválido'
    }

    if (resposta == aux) return true
    else {
        alert(resposta)
        return false
    }

}

function abrirModalContratoZapSign($id, $id_paciente) {
    $.get('/saude-beta/pessoa/mostrar/' + $id_paciente,
        function (data, status) {
            console.log(data + ' | ' + status)
            data = $.parseJSON(data)

            $('#zapsignPessoaModal #id_pedido').val($id)
            $('#zapsignPessoaModal #id_pessoa').val($id_paciente)

            $("#zapsignPessoaModal #nome").val(data.nome_fantasia)
            $("#zapsignPessoaModal #cpf").val(data.cpf_cnpj)
            $("#zapsignPessoaModal #rg").val(data.rg_ie)
            $("#zapsignPessoaModal #cep").val(data.cep)
            $("#zapsignPessoaModal #cidade").val(data.cidade)
            $("#zapsignPessoaModal #uf").val(data.uf)
            $("#zapsignPessoaModal #endereco").val(data.endereco)
            $("#zapsignPessoaModal #numero").val(data.numero)
            $("#zapsignPessoaModal #bairro").val(data.bairro)
            $("#zapsignPessoaModal #complemento").val(data.complemento)
            $('#zapsignPessoaModal #email').val(data.email)
            $('#zapsignPessoaModal #celular').val(data.celular1)

            $('#zapsignPessoaModal #section-1').show()
            $('#zapsignPessoaModal #section-2').hide()
            $('#zapsignPessoaModal').modal('show')

            endereco = location.href.substr(location.href.indexOf('saude-beta')).replaceAll('saude-beta/', '').substr(0, 6)
            if (endereco == 'pessoa') {
                $('#zapsignPessoaModal #botao-zap-sign-modal').attr('onclick', "gerarContratoZapSign($('#zapsignPessoaModal #id_pedido').val())")
            }
            else {
                $('#zapsignPessoaModal #botao-zap-sign-modal').attr('onclick', "gerarContratoZapSign($('#zapsignPessoaModal #id_pedido').val())")
            }
        })
}
function salvar_iec(e) {
    var respostas = [],
        opcoes;
    e.preventDefault();
    $("#questionario-iec [data-id_pergunta]").each(function () {
        opcoes = [];
        $("#resposta_" + $(this).data().id_pergunta + " input:checked").each(
            function () {
                opcoes.push($(this).val());
            }
        );

        respostas.push({
            id_pergunta: $(this).data().id_pergunta,
            tipo: $(this).data().tipo,
            resposta: opcoes,
        });
    });

    $.post(
        "/saude-beta/IEC/responder-iec",
        {
            _token: $("meta[name=csrf-token]").attr("content"),
            id_iec: $("#iecModal #id_iec").val(),
            id_paciente: $("#id_pessoa_prontuario").val(),
            obs: $("#iecModal #obs").val(),
            respostas: respostas,
        },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                console.log(data);
                data = $.parseJSON(data);
                iec_por_pessoa($("#id_pessoa_prontuario").val());
                $("#iecModal").modal("hide");
                //listarAgendamentosDiariosIEC();
            }
        }
    );
}

function listarAgendamentosDiariosIEC() {
    var html = "";
    $.get(
        "/saude-beta/evolucao/listar-agendamentos/" +
        $("#id_pessoa_prontuario").val(),
        function (data, status) {
            $(
                "#agendamentosDiariosModal #conteudo-agendamentos-diario-iec"
            ).empty();
            if (data.length > 0) {
                data.forEach((agendamento) => {
                    if (agendamento.id != undefined && agendamento.id != null) {
                        html =
                            '<li data-id_agendamento="' + agendamento.id + '"';
                        html +=
                            ' onclick="encaminharDoIEC(' +
                            agendamento.id +
                            ')"';
                        html +=
                            ' style="background:' +
                            agendamento.cor_status +
                            "; color: " +
                            agendamento.cor_letra +
                            ';max-height: 94px;min-width: 100%;margin-bottom: 20px; margin-left: 50px; cursor: pointer" >';

                        html += '    <div class="my-1 mx-1 d-flex">';
                        html +=
                            '       <img class="foto-paciente-agenda" data-id_paciente="' +
                            agendamento.id_paciente +
                            '" src="/saude-beta/img/pessoa/' +
                            agendamento.id_paciente +
                            '.jpg" onerror="this.onerror=null;this.src=' +
                            "'/saude-beta/img/paciente_default.png'" +
                            '" onclick="verificar_cad_redirecionar(' +
                            agendamento.id_paciente +
                            ')">';
                        html += "       <div>";
                        html += '           <p class="col p-0">';
                        html +=
                            '               <span class="ml-0 my-auto" style="font-weight:600" onclick="verificar_cad_redirecionar(' +
                            agendamento.id_paciente +
                            ')">';
                        html +=
                            agendamento.hora.substring(0, 5) +
                            "  -  " +
                            agendamento.nome_paciente.toUpperCase();
                        html += "               </span>";
                        html += "           </p>";
                        html +=
                            '           <p class="tag-agenda" style="font-weight:400">';
                        html += agendamento.nome_profissional + " | ";
                        if (agendamento.retorno) html += "Retorno: ";
                        if (agendamento.descr_procedimento != null)
                            html += agendamento.descr_procedimento + " | ";
                        if (agendamento.tipo_procedimento != null)
                            html += agendamento.tipo_procedimento + " | ";
                        if (agendamento.convenio_nome != null)
                            html += agendamento.convenio_nome;
                        else html += "Particular";
                        html += "           </p>";
                        html += "       </div>";

                        html += '   <div class="tags">';
                        html += "   </div>";

                        html += "</div>";
                        html += "</li>";
                        $(
                            "#agendamentosDiariosModal #conteudo-agendamentos-diario-iec"
                        ).append(html);
                        $("#agendamentosDiariosModal").modal("show");
                    }
                });
            }
        }
    );
}

function listarCaixas() {
    $.get(
        "/saude-beta/caixa/listar-caixas/" +
        $("#caixaModal #id_caixa").val(),
        function (data, status) {
            console.log(data + " | " + status);
            data = $.parseJSON(data);
            $("#listarCaixasModal #table-lista-caixa tbody").empty();
            data.forEach((caixa) => {
                html = "<tr>";
                html += '   <td width="100%">';
                html += caixa.nome;
                html += "   </td>";
                html += "   <td>";
                html += "</tr>";
                $("#listarCaixasModal #table-listar-caixa tbody").append(html);
            });
            $("#listarCaixasModal").modal("show");
        }
    );
}

function realToFloat(value) {
    if (value.replaceAll(",", "").replaceAll(".", "") != value) {
        return parseFloat(value.replaceAll(",", "").replaceAll(".", "")) / 100;
    } else return value;
}

function addPlanoContaReceberModal() {
    $('#planoContasReceberModal').modal('show')
}

function editarCadastroCaixa($id) {
    $.get("/saude-beta/caixa/editar/" + $id, function (data, status) {
        console.log(data + " | " + status);
        data = $.parseJSON(data);
        testando = data;
        $("#cadastroCaixaModal #id_caixa").val(data.caixa.id);
        $("#cadastroCaixaModal #descr").val(data.caixa.descr);
        $("#cadastroCaixaModal #empresa").val(data.caixa.id_emp);
        $("#cadastroCaixaModal #situacao").val(data.caixa.ativo);
        $("#cadastroCaixaModal #horainicial").val(data.caixa.h_inicial);
        $("#cadastroCaixaModal #horafinal").val(data.caixa.h_final);

        $("#table-cadastro-caixa tbody").empty();
        data.operadores.forEach((operador) => {
            html = '<tr value="' + operador.id + '">';
            html += '   <td width="100%">';
            html += operador.nome;
            html += "   </td>";
            html += "   <td>";
            html +=
                '       <img style="opacity:.8;cursor: pointer" src="http://vps.targetclient.com.br/saude-beta/img/lixeira.png" onclick="$(this).parent().parent().remove()">';
            html += "</tr>";

            $("#table-cadastro-caixa tbody").append(html);
        });
        $("#cadastroCaixaModal").modal("show");
    });
}

function CalcularParcelasTitulosReceber() {
    valor = realToFloat(
        $("#cadastrarTituloReceberModal #valor-titulo-receber").val()
    );
    parcela = $("#cadastrarTituloReceberModal #n-parcela");
    emissao = $("#cadastrarTituloReceberModal #data-vencimento");
    if (valor > 0) {
        parcela.removeAttr("disabled");
        if (!isNaN(parcela.val())) {
            data = new Date(formatDataUniversal(emissao.val()));

            $("#cadastrarTituloReceberModal #table-parcelas tbody").empty();
            for (i = 0; i < parcela.val(); i++) {
                html = " <tr>";
                html += '   <td width="10%">';
                html += i + 1;
                html += "   </td>";
                html += '   <td width="30%">';
                html += $(
                    "#cadastrarTituloReceberModal #descr-recebimento"
                ).val();
                html += "   </td>";
                html += '   <td width="30%" class="text-right">';
                html +=
                    i +
                    1 +
                    " X " +
                    (valor / parcela.val()).toLocaleString("pt-BR", {
                        minimumFractionDigits: 2,
                        style: "currency",
                        currency: "BRL",
                    });
                html += "   </td>";
                html += '   <td width="30%" class="text-right">';
                html += data.toLocaleDateString();
                html += "   </td>";
                data.setMonth(data.getMonth() + 1);
                $("#cadastrarTituloReceberModal #table-parcelas tbody").append(
                    html
                );
            }
        }
    }
}
function setarTipoTituloReceber($value) {
    document.querySelectorAll("tipo-titulo-selected").forEach((el) => {
        el.className = "";
    });
    switch ($value) {
        case "unico":
            $("#cadastrarTituloReceberModal #table-parcelas").hide();
            $('[data-tipo="titulo-unico"]').addClass("tipo-titulo-selected");
            $("#cadastrarTituloReceberModal #id-parcelas-t-receber").html(
                "N. Parcela"
            );
            $('[data-tipo="parcelado"]').removeAttr("class");
            $('[onclick="CalcularParcelasTitulosReceber()"]').parent().hide();
            break;
        case "parcelado":
            $("#cadastrarTituloReceberModal #table-parcelas").show();
            $("#cadastrarTituloReceberModal #table-parcelas tbody").empty();
            $('[data-tipo="titulo-unico"]').removeAttr("class");
            $("#cadastrarTituloReceberModal #id-parcelas-t-receber").html(
                "N. Parcelas"
            );
            $('[data-tipo="parcelado"]').addClass("tipo-titulo-selected");
            $('[onclick="CalcularParcelasTitulosReceber()"]').parent().show();
            break;
    }
}
function salvarTituloReceber() {
    valor = realToFloat(
        $("#cadastrarTituloReceberModal #valor-titulo-receber").val()
    );
    parcela = $("#cadastrarTituloReceberModal #n-parcela");
    emissao = $("#cadastrarTituloReceberModal #data-vencimento");

    (array_parcela = []), (array_valor = []), (array_vencimento = []);
    if (valor > 0) {
        data = new Date(formatDataUniversal(emissao.val()));

        if (
            $("#cadastrarTituloReceberModal .tipo-titulo-selected").data()
                .tipo == "parcelado"
        ) {
            for (i = 0; i < parcela.val(); i++) {
                array_parcela.push(i + 1);
                array_valor.push(valor / parcela.val());
                array_vencimento.push(
                    formatDataUniversal(data.toLocaleDateString())
                );

                data.setMonth(data.getMonth() + 1);
            }
        }

        $.get(
            "/saude-beta/financeiro/salvar-titulo-receber",
            {
                _token: $("meta[name=csrf-token]").attr("content"),
                tipo: $(
                    "#cadastrarTituloReceberModal .tipo-titulo-selected"
                ).data().tipo,
                nDoc: $("#cadastrarTituloReceberModal #n-documento").val(),
                descr: $(
                    "#cadastrarTituloReceberModal #descr-recebimento"
                ).val(),
                id_pessoa: $("#cadastrarTituloReceberModal #devedor_id").val(),
                valor_total: valor,
                emissao: formatDataUniversal(
                    $("#cadastrarTituloReceberModal #data-emissao").val()
                ),
                vencimento: formatDataUniversal(
                    $("#cadastrarTituloReceberModal #data-vencimento").val()
                ),
                forma_pag: $(
                    "#cadasrarTituloReceberModal #plano-pagamento"
                ).val(),
                parcela: $("#cadastrarTituloReceberModal #n-parcela").val(),
                parcelas: array_parcela,
                valores: array_valor,
                vencimentos: array_vencimento,
            },
            function (data, status) {
                console.log(data + " | " + status);
                location.reload();
            }
        );
    }
}

function salvarTituloPagar() {
    valor = realToFloat(
        $("#cadastrarTituloPagarModal #valor-titulo-pagar").val()
    );
    parcela = $("#cadastrarTituloPagarModal #n-parcela");
    emissao = $("#cadastrarTituloPagarModal #data-vencimento");

    (array_parcela = []), (array_valor = []), (array_vencimento = []);
    if (valor > 0) {
        data = new Date(formatDataUniversal(emissao.val()));

        if (
            $("#cadastrarTituloPagarModal .tipo-titulo-selected").data().tipo ==
            "parcelado"
        ) {
            for (i = 0; i < parcela.val(); i++) {
                array_parcela.push(i + 1);
                array_valor.push(valor / parcela.val());
                array_vencimento.push(data.toLocaleDateString());

                data.setMonth(data.getMonth() + 1);
            }
        }

        $.get(
            "/saude-beta/financeiro/salvar-titulo-pagar",
            {
                _token: $("meta[name=csrf-token]").attr("content"),
                tipo: $(
                    "#cadastrarTituloPagarModal .tipo-titulo-selected"
                ).data().tipo,
                nDoc: $("#cadastrarTituloPagarModal #n-documento").val(),
                descr: $("#cadastrarTituloPagarModal #descr-recebimento").val(),
                id_pessoa: $("#cadastrarTituloPagarModal #devedor_id").val(),
                valor_total: valor,
                emissao: $("#cadastrarTituloPagarModal #data-emissao").val(),
                vencimento: $(
                    "#cadastrarTituloPagarModal #data-vencimento"
                ).val(),
                forma_pag: $(
                    "#cadasrarTituloPagarModal #plano-pagamento"
                ).val(),
                parcela: $("#cadastrarTituloPagarModal #n-parcela").val(),
                parcelas: array_parcela,
                valores: array_valor,
                vencimentos: array_vencimento,
            },
            function (data, status) {
                console.log(data + " | " + status);
                location.reload();
            }
        );
    }
}

function verTituloReceber($id) {
    $.get(
        "/saude-beta/financeiro/exibir-titulo-receber/" + $id,
        function (data, status) {
            console.log(data + " | " + status);
        }
    );
}

// function abrirModalCaixa() {
//     $.get('/saude-beta/caixa/abrir-modal', {},
//         function (data, status) {
//             console.log(data + ' | ' + status)
//             data = $.parseJSON(data)
//             $('#caixaModal #id_caixa').val(data.id_caixa)
//             $('#caixaModal #input-saldo').val(data.saldo_caixa.toLocaleString('pt-BR', { minimumFractionDigits: 2, style: 'currency', currency: 'BRL' }))
//             $('#caixaModal #input-vendas').val(data.faturamento_dia.toLocaleString('pt-BR', { minimumFractionDigits: 2, style: 'currency', currency: 'BRL' }))
//             $('#caixaModal #input-recebimentos').val(data.recebimentos.toLocaleString('pt-BR', { minimumFractionDigits: 2, style: 'currency', currency: 'BRL' }))
//             $('#caixaModal #input-saida').val(data.saida.toLocaleString('pt-BR', { minimumFractionDigits: 2, style: 'currency', currency: 'BRL' }))
//             $('#caixaModal').modal('show')
//             switch (data.situacao) {
//                 case 'A':
//                     $('#caixaModal #button-caixa-abrir-fechar').html('Fechar Caixa').attr('onclick', 'fecharCaixa()')
//                     break
//                 case 'F':
//                     $('#caixaModal #button-caixa-abrir-fechar').html('Abrir Caixa').attr('onclick', 'abrirCaixa()')
//                     break
//             }

//         })
// }
var exibidas = 0;
function buscarNotificacoes() {
    $.get(
        '/saude-beta/notificacao/listar', {},
        function (data, status) {
            data = $.parseJSON(data)
            console.log(data.notificacoes_ar)
            var aExibir = 0;
            for (i = 0; i < data.notificacoes_ar.length; i++) {
                if (!parseInt(data.notificacoes_ar[i].viz)) aExibir++;
            }
            if (exibidas < aExibir) {
                var html = '';
                exibidas = 0;
                for (i = 0; i < data.notificacoes_ar.length; i++) {
                    if (!parseInt(data.notificacoes_ar[i].viz)) {
                        var resumo = data.notificacoes_ar[i].assunto.substr(0, 46) + '...',
                        b = data.notificacoes_ar[i].created_at
                        tempo = b.substr(8, 2) + '/' + b.substr(5, 2) + '/' + b.substr(0, 4)
                        html = ' <li class="li-not-bar" onclick="removerNotificacaoAparecendo($(this))" id="notificacoes-aparecendo" style="position: fixed;top: 1' + $('body > li').length + '%;z-index: 100;right: 10px;border: 1px solid #b0b0b0;transition: 1.5s opacity;opacity: 0;"> '
                        html += '   <div style="display: flex;cursor: pointer"> '
                        html += '       <div class="img-not-bar"> '
                        html += '           <img class="user-photo-sm" style="width: 35px;" src="/saude-beta/img/pessoa/' + data.notificacoes_ar[i].created_by + '.jpg"'
                        html += '            onerror="this.onerror=null;this.src=' + "'/saude-beta/img/paciente_default.png'" + '">';
                        html += '       </div>'
                        html += '       <div style="width:100%;padding: 0px 8px 0px 0px;">'
                        html += '           <div class="div-nome-not-bar"><div class="notific-mobile" style="width: 325px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;">'
                        html += '               <span id="n' + data.notificacoes_ar[i].id_notificacao + '" class="nome-not-bar"onclick="visualizar_notificacao(' + data.notificacoes_ar[i].id_notificacao + ',' + "'" + resumo + "'" + ')">' + data.notificacoes_ar[i].descr_paciente + '</span></div>'
                        html += '               <span style="font-size: 12px;position: relative; bottom: 5px">Por: ' + tempo + '</span>'
                        html += '           </div>'
                        html += '               <span>' + toCapitalize(data.notificacoes_ar[i].descr_profissional) + '</span>'
                        html += '           <div id="not-txt-' + data.notificacoes_ar[i].id_notificacao + '" style="width: 85%;word-wrap: break-word;">'
                        html += '               <p style="white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;">' + resumo + "</p>"
                        html += '           </div>'
                        html += '           <div class="remover-notificacao">   <span onclick="excluirNotificacao(' + data.notificacoes_ar[i].id_notificacao + ')"></span></div>'
                        html += '       </div>'
                        html += '   </div>'
                        html += '</li>'
                        $('body').append(html);
                        exibidas++;
                    }
                }
                setTimeout(() => {
                    document.querySelectorAll('body > li').forEach(el => {
                        el.style.opacity = 1;
                    })
                }, 500);
                setTimeout(() => {
                    document.querySelectorAll('body > li').forEach(el => {
                        el.style.opacity = 0;
                        setTimeout(() => {
                            el.remove()
                        }, 1500)
                    })
                }, 3000);
            }
            $('#notificacao-navbar > ul').empty()
            a = data
            contador = 0;
            data.notificacoes_ar.forEach(not => {
                var resumo = not.assunto.substr(0, 46) + '...',
                    b = not.created_at
                tempo = b.substr(8, 2) + '/' + b.substr(5, 2) + '/' + b.substr(0, 4)

                html = '<li class="li-not-bar" style="width: 102%"> '
                html += '   <div style="display: flex;cursor: pointer" onclick="visualizar_notificacao(' + not.id_notificacao + ',' + "'" + resumo + "'" + ', this)"> '
                html += '       <div class="img-not-bar"> '
//                if (data.notificacoes_n_visualizadas[contador] != 1) {
                if (!parseInt(not.viz)) html += '<span id="not-nao-lida-i-' + not.id_notificacao + '" class="not-nao-lida-i" style="background-color: red"></span>'
  //              }
                html += '           <img class="user-photo-sm" style=" width: 45px;height: 45px;" src="/saude-beta/img/pessoa/' + not.created_by + '.jpg"'
                html += '            onerror="this.onerror=null;this.src=' + "'/saude-beta/img/paciente_default.png'" + '">';
                html += '       </div>'
                html += '       <div style="width:100%;padding: 0px 8px 0px 0px;">'
                html += '           <div class="div-nome-not-bar"><div class="notific-mobile" style="width: 325px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;">'
                html += '               <span id="n' + not.id_notificacao + '" class="nome-not-bar">' + not.descr_paciente + '</span></div>'
                html += '               <span>' + tempo + '</span>'
                html += '           </div>'
                html += '               <span style="font-size: 12px;position: relative; bottom: 5px">Por: ' + toCapitalize(not.descr_profissional) + '</span>'
                html += '           <div id="not-txt-' + not.id_notificacao + '" style="width: 85%;word-wrap: break-word;">'
                html += '               <p style="white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;">' + resumo + "</p>"
                html += '           </div>'
                html += '           <div class="remover-notificacao">   <span onclick="excluirNotificacao(' + not.id_notificacao + ')"></span></div>'
                html += '       </div>'
                html += '   </div>'
                html += '</li>'
                $('#notificacao-navbar > ul').append(html)
                contador++;
            })
            $aux_notificacoes = data.notificacoes_ar
            $(".qtde-notificacao").each(function(){$(this).html(document.getElementsByClassName("not-nao-lida-i").length)})
        }
    )
}

function removerNotificacaoAparecendo($obj) {
    $obj.css('opacity', 0)
    setTimeout(() => {
        $obj.remove()
    }, 1500)
}

function atualizarCaixaModal() {
    $.get(
        "/saude-beta/caixa/abrir-modal/" +
        $("#caixaModal #data-selecionada").val() +
        "/" +
        $("#caixaModal #id_caixa").val(),
        function (data, status) {
            console.log(data + " | " + status);
            data = $.parseJSON(data);
            // if (data.message == 'A') {
            $("#caixaModal #nome-usuario").html(
                data.usuario.substr(0, 1).toUpperCase() +
                data.usuario.substr(1).toLowerCase()
            );

            console.log(data.data_selecionada);
            $("#caixaModal #data-selecionada").val(data.data_selecionada);
            if (auxiliar.situacao == "A")
                $("#caixaModal #mensagem1")
                    .html(data.msg1)
                    .css("color", "green");
            else
                $("#caixaModal #mensagem1").html(data.msg1).css("color", "red");

            $("#caixaModal #id_caixa").val(data.id_caixa);

            $("#caixaModal #total-dinheiro").html(
                parseFloat(data.saldo_caixa).toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                })
            );
            $("#caixaModal #saldo-inicial").html(
                parseFloat(data.saldo_inicial).toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                })
            );
            $("#caixaModal #suprimento-caixa").html(
                parseFloat(data.suprimento).toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                })
            );
            $("#caixaModal #recebimento-vista").html(
                parseFloat(data.recebimento_vista).toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                })
            );
            $("#caixaModal #recebimento-prazo").html(
                parseFloat(data.recebimento_prazo).toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                })
            );
            console.log(data.sangria)
            $("#caixaModal #sangria-de-caixa").html(
                parseFloat(data.sangria).toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                })
            );

            // $('#caixaModal #total-entrada-dinheiro').html(data.total_entrada_dinheiro.toLocaleString('pt-BR', { minimumFractionDigits: 2, style: 'currency', currency: 'BRL' }))
            // $('#caixaModal #total-saida-dinheiro').html(data.total_saida_dinheiro.toLocaleString('pt-BR', { minimumFractionDigits: 2, style: 'currency', currency: 'BRL' }))

            console.log(data.total_cartao)
            $("#caixaModal #total-cartao").html(
                parseFloat(data.total_cartao).toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                })
            );
            $("#caixaModal #valor-debito").html(
                parseFloat(data.total_cartao_debito).toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                })
            );
            $("#caixaModal #valor-credito").html(
                parseFloat(data.total_cartao_credito).toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                })
            );

            $("#caixaModal #total-transferencias").html(
                parseFloat(data.total_transferencia).toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                })
            );
            $("#caixaModal #valor-pix").html(
                parseFloat(data.valor_pix).toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                })
            );
            $("#caixaModal #valor-transferencia").html(
                parseFloat(data.valor_transferencia).toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                })
            );

            $("#caixaModal #total-convenio").html(
                parseFloat(data.total_convenio).toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                })
            );
            $("#caixaModal #lista-convenio").empty();
            data.lista_convenios.forEach((convenio) => {
                $valor = convenio.valor_total.toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                });
                $("#caixaModal #lista-convenio").append(
                    "<li>" +
                    convenio.descr +
                    " <span>" +
                    $valor +
                    "</span></li>"
                );
            });
            // $('#caixaModal #total-boleto').html(data.total_boleto)
            // $('#caixaModal #total-duplicata').html(data.total_duplicata)

            $("#caixaModal #total-recebimentos").html(
                parseFloat(data.total_recebimentos).toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                })
            );
            $("#caixaModal #total-vendas").html(
                parseFloat(data.total_vendas).toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                })
            );
            $("#caixaModal #total-sangrias-suprimentos").html(
                parseFloat(data.total_sangrias_suprimentos).toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                })
            );
            $("#caixaModal #total-suprimento").html(
                parseFloat(data.total_suprimento).toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                })
            );
            $("#caixaModal #total-sangria").html(
                parseFloat(data.total_sangria).toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                })
            );

            $("#caixaModal").modal("show");
            // }
            // mensagem
            console.log(data.message);
            var data1 = new Date();
            var dia = String(data1.getDate()).padStart(2, "0");
            var mes = String(data1.getMonth() + 1).padStart(2, "0");
            var ano = data1.getFullYear();
            dataAtual = ano + "-" + mes + "-" + dia;

            testando = data;
            console.log(dataAtual == $('#caixaModal #data-selecionada').val() && data.msg1.split(' ')[0] != 'Feche')
            if (dataAtual == $('#caixaModal #data-selecionada').val() || data.msg1.split(' ')[0] == 'Feche' || data.msg1 == 'O Caixa de hoje ainda está fechado') {
                if (data.message == "F") {
                    $("#caixaModal #div-button-fechar").hide();
                    $("#caixaModal #div-button-abrir").show();
                } else {
                    $("#caixaModal #div-button-fechar").show();
                    $("#caixaModal #div-button-abrir").hide();
                }
            }
            else {
                $("#caixaModal #div-button-fechar").hide();
                $("#caixaModal #div-button-abrir").hide();
            }
        }
    );

}

function abrirModalCaixa() {
    $.get(
        "/saude-beta/caixa/verificar-situacao",
        {},
        function (data, status) {
            console.log(data + " | " + status);
            data = $.parseJSON(data);
            auxiliar = data;
            if (data.situacao == "X") {
                alert("Seu usuário não está vinculado ao caixa desta empresa!");
            } else {
                if (data.situacao == "A" && data.abrir_fechar == false) {
                    $("#caixaModal #id_caixa").val(data.id_caixa);
                    var data = new Date();
                    var dia = String(data.getDate()).padStart(2, "0");
                    var mes = String(data.getMonth() + 1).padStart(2, "0");
                    var ano = data.getFullYear();
                    dataAtual = ano + "-" + mes + "-" + dia;
                    $("#caixaModal #data-selecionada").val(dataAtual);
                    atualizarCaixaModal();
                    $("#caixaModal").modal("show");
                } else {
                    switch (data.situacao) {
                        case "A":
                            text_aux1 =
                                "Para continuar será necessário fechar caixa aberto anteriormente.";

                            /*if (
                                formatDataBr(data.data_ult_abertura) ==
                                "01/01/0001"
                            ) {
                                text_aux2 =
                                    "Caixa nunca foi aberto anteriormente!";
                            } else {
                                text_aux2 =
                                    "Caixa foi aberto pela última vez em " +
                                    formatDataBr(data.data_ult_abertura);
                            }*/
                            break;
                        case "F":
                            text_aux1 =
                                "Para continuar será necessário abrir o caixa de hoje.";

                            /* if (
                                 formatDataBr(data.data_ult_abertura) ==
                                 "01/01/0001"
                             ) {
                                 text_aux2 =
                                     "Caixa nunca foi aberto anteriormente!";
                             } else {
                                 text_aux2 =
                                     "Caixa foi aberto pela última vez em " +
                                     formatDataBr(data.data_ult_abertura);
                             }*/

                            break;
                    }
                    text_aux2 = "Deseja continuar?";
                    ShowConfirmationBox(
                        text_aux1,
                        text_aux2,
                        true,
                        true,
                        false,
                        function () {
                            $("#caixaModal").modal("show");
                            if (
                                formatDataBr(auxiliar.data_ult_abertura) ==
                                "01/01/0001"
                            ) {
                                $("#caixaModal #id_caixa").val(
                                    auxiliar.id_caixa
                                );
                                var data = new Date();
                                var dia = String(data.getDate()).padStart(
                                    2,
                                    "0"
                                );
                                var mes = String(data.getMonth() + 1).padStart(
                                    2,
                                    "0"
                                );
                                var ano = data.getFullYear();
                                dataAtual = ano + "-" + mes + "-" + dia;
                                $("#caixaModal #data-selecionada").val(
                                    dataAtual
                                );
                            } else {
                                $("#caixaModal #id_caixa").val(
                                    auxiliar.id_caixa
                                );
                                $("#caixaModal #data-selecionada").val(
                                    auxiliar.data_ult_abertura
                                );
                                atualizarCaixaModal();
                                $("#caixaModal").modal("show");
                            }
                            atualizarCaixaModal();
                        },
                        function () {
                            console.log(false);
                        },
                        "Sim",
                        "Não"
                    );
                }
            }
        }
    );
}

function atualizar_modal() {
    $.get("/saude-beta/caixa/abrir-modal", {}, function (data, status) {
        console.log(data + " | " + status);
        data = $.parseJSON(data);
        // if (data.message == 'A') {
        $("#caixaModal #nome-usuario").html(
            data.usuario.substr(0, 1).toUpperCase() +
            data.usuario.substr(1).toLowerCase()
        );

        $("#caixaModal #data-selecionada").val(data.data_selecionada);
        if (auxiliar.situacao == "A")
            $("#caixaModal #mensagem1").html(data.msg1).css("color", "green");
        else $("#caixaModal #mensagem1").html(data.msg1).css("color", "red");

        $("#caixaModal #id_caixa").val(data.id_caixa);

        $("#caixaModal #total-dinheiro").html(
            data.saldo_caixa.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                style: "currency",
                currency: "BRL",
            })
        );
        $("#caixaModal #saldo-inicial").html(
            data.saldo_inicial.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                style: "currency",
                currency: "BRL",
            })
        );
        $("#caixaModal #suprimento-caixa").html(
            data.suprimento.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                style: "currency",
                currency: "BRL",
            })
        );
        $("#caixaModal #recebimento-vista").html(
            data.recebimento_vista.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                style: "currency",
                currency: "BRL",
            })
        );
        $("#caixaModal #recebimento-prazo").html(
            data.recebimento_prazo.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                style: "currency",
                currency: "BRL",
            })
        );
        $("#caixaModal #sangria-de-caixa").html(
            data.sangria.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                style: "currency",
                currency: "BRL",
            })
        );

        // $('#caixaModal #total-dinheiro').html(data.saldo_caixa.toLocaleString('pt-BR', { minimumFractionDigits: 2, style: 'currency', currency: 'BRL' }))
        // $('#caixaModal #total-entrada-dinheiro').html(data.total_entrada_dinheiro.toLocaleString('pt-BR', { minimumFractionDigits: 2, style: 'currency', currency: 'BRL' }))
        // $('#caixaModal #total-saida-dinheiro').html(data.total_saida_dinheiro.toLocaleString('pt-BR', { minimumFractionDigits: 2, style: 'currency', currency: 'BRL' }))

        $("#caixaModal #total-cartao").html(
            data.total_cartao.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                style: "currency",
                currency: "BRL",
            })
        );
        $("#caixaModal #valor-debito").html(
            data.total_cartao_debito.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                style: "currency",
                currency: "BRL",
            })
        );
        $("#caixaModal #valor-credito").html(
            data.total_cartao_credito.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                style: "currency",
                currency: "BRL",
            })
        );

        $("#caixaModal #total-transferencias").html(
            data.total_transferencia.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                style: "currency",
                currency: "BRL",
            })
        );
        $("#caixaModal #valor-pix").html(
            data.valor_pix.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                style: "currency",
                currency: "BRL",
            })
        );
        $("#caixaModal #valor-transferencia").html(
            data.valor_transferencia.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                style: "currency",
                currency: "BRL",
            })
        );

        $("#caixaModal #total-convenio").html(
            data.total_convenio.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                style: "currency",
                currency: "BRL",
            })
        );
        $("#caixaModal #lista-convenio").empty();
        data.lista_convenios.forEach((convenio) => {
            $valor = convenio.valor_total.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                style: "currency",
                currency: "BRL",
            });
            $("#caixaModal #lista-convenio").append(
                "<li>" + convenio.descr + " <span>" + $valor + "</span></li>"
            );
        });
        // $('#caixaModal #total-boleto').html(data.total_boleto)
        // $('#caixaModal #total-duplicata').html(data.total_duplicata)

        $("#caixaModal #total-recebimentos").html(
            data.total_recebimentos.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                style: "currency",
                currency: "BRL",
            })
        );
        $("#caixaModal #total-vendas").html(
            data.total_vendas.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                style: "currency",
                currency: "BRL",
            })
        );
        $("#caixaModal #total-sangrias-suprimentos").html(
            data.total_sangrias_suprimentos.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                style: "currency",
                currency: "BRL",
            })
        );
        $("#caixaModal #total-suprimento").html(
            data.total_suprimento.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                style: "currency",
                currency: "BRL",
            })
        );
        $("#caixaModal #total-sangria").html(
            data.total_sangria.toLocaleString("pt-BR", {
                minimumFractionDigits: 2,
                style: "currency",
                currency: "BRL",
            })
        );

        $("#caixaModal").modal("show");
        // }
        switch (data.message) {
            case "A":
                $("#caixaModal #div-button-fechar").show();
                $("#caixaModal #div-button-abrir").hide();
                break;
            case "F":
                $("#caixaModal #div-button-fechar").hide();
                $("#caixaModal #div-button-abrir").show();
                break;
        }
    });
}

function atualizarValoresConversaoPedido() {
    creditos = $("#pedidoModal #creditos-pessoa");
    valor = $("#pedidoModal #pedido_forma_pag_valor");

    aux = 0;
    $('#pedidoModal [data-forma_pag="101"]').each(function ($sql) {
        aux += parseFloat(
            $(this).parent().find("[data-forma_pag_valor]").data()
                .forma_pag_valor
        );
    });

    total_gasto = parseFloat(valor.val().replace(",", ".")) + aux / 2;

    if (
        creditos.val().replace(",", ".") != 0 &&
        !isNaN(valor.val().replace(",", ".")) &&
        parseFloat(valor.val().replace(",", ".")) <= total_gasto
    ) {
        creditos.val(parseFloat(creditos.data().creditos) - total_gasto);
    }
}

function abrirVendasCaixaModal() {
    $("#resumoVendasModal").modal("show");
}
function control_valor_pedido1() {
    valor_pendente =
        $("#table-pedido-forma-pag [data-total_pag_pendente]").data()
            .total_pag_pendente -
        $("#table-pedido-forma-pag [data-total_pag_valor]").data()
            .total_pag_valor;
    valor_entrada = parseFloat(
        $("#pedidoModal #pedido_forma_pag_valor").val().replaceAll(",", ".")
    );

    if (
        valor_entrada > 0 &&
        $("#pedidoModal #pedido_forma_pag_valor").val() != "" &&
        $("#pedidoModal #pedido_forma_pag").val() == 2
    ) {
        $("#pedidoModal [data-troco]").parent().show();

        console.log(valor_pendente);
        console.log(valor_entrada);
        console.log(valor_entrada > valor_pendente);
        if (valor_entrada > valor_pendente) {
            troco = valor_entrada - valor_pendente;
        } else {
            troco = 0;
        }
        if ($("#pedidoModal [data-total_troco]").data().total_troco != "")
            total_troco =
                parseFloat(
                    $("#pedidoModal [data-total_troco]")
                        .data()
                        .total_troco.toString()
                        .replaceAll(",", ".")
                ) + troco;
        else total_troco = 0 + troco;

        $("#pedidoModal [data-troco]").data("troco", troco);
        $("#pedidoModal [data-troco]").val(troco);

        $("#pedidoModal [data-total_troco]").data("troco", total_troco);
        $("#pedidoModal [data-total_troco]").html(
            "Valor total do Troco - R$ <span style='color:red'>" +
            total_troco.toString().replaceAll(",", ".") +
            "</span>"
        );
    } else if (
        $("#pedidoModal #pedido_forma_pag").val() == 2 &&
        valor_entrada > valor_pendente
    ) {
        alert(
            "O valor que você está tentando adicionar excede o total de sua compra. \n Troco só é permitido com pagamento em dinheiro!"
        );
    } else {
        atualizarValoresConversaoPedido();
        $("#pedidoModal #creditos-pessoa").parent().hide();
    }
}
function salvar_valor_caixa(bfechar = false) {
    $.post(
        "/saude-beta/caixa/salvar-valor-caixa",
        {
            _token: $("meta[name=csrf-token]").attr("content"),
            id_caixa: $("#caixaModal #id_caixa").val(),
            valor: $("#ajusteSaldoCaixa #valor_total").val(),
            obs: $("#ajusteSaldoCaixa #motivo").val(),
        },
        function (data, status) {
            console.log(data + " | " + status);
            if (data.error) {
                alert(data.error``);
            } else {
                if (data == "true") {
                    let valor_novo = parseFloat(
                        $("#ajusteSaldoCaixa #valor_total")
                            .val()
                            .toString()
                            .replaceAll(",", ".")
                    ),
                        valor_antigo = parseFloat(
                            $("#ajusteSaldoCaixa #valor_total")
                                .data()
                                .valor_total.toString()
                                .replaceAll(",", ".")
                        );
                    if (valor_novo > valor_antigo)
                        alerta =
                            "Foi adicionado R$ " +
                            (valor_novo - valor_antigo) +
                            " ao caixa";
                    else if (valor_novo < valor_antigo)
                        alerta =
                            "Foi retirado R$ " +
                            (valor_antigo - valor_novo).toFixed(2) +
                            " do caixa";
                    else alerta = "Nenhuma mudança de valor foi realizada";

                    alert(alerta);
                    $("#ajusteSaldoCaixa").modal("hide");
                    $("#caixaModal").modal();
                    atualizarCaixaModal();
                    if (bFechar) fecharCaixa()
                }
            }
        }
    );
}

function corrigir_valor_caixa() {
    $.get(
        "/saude-beta/caixa/abrir-modal-saldo",
        {
            id_caixa: $("#caixaModal #id_caixa").val(),
        },
        function (data, status) {
            console.log(data + " | " + status);
            $("#ajusteSaldoCaixa #valor_total").val(data);
            $("#ajusteSaldoCaixa #valor_total").data("valor_total", data);
            $("#ajusteSaldoCaixa #inserir_valor").val(0);
            $("#ajusteSaldoCaixa #title-valor-inserido-caixa")
                .html("Sem Alteração")
                .css("color", "blue");
            $("#ajusteSaldoCaixa").modal("show");
        }
    );
}
function control_valor_inserido_caixa($obj) {
    if (realToFloat($obj.val()) > 0) {
        $("#ajusteSaldoCaixa #title-valor-inserido-caixa")
            .html("Adicionando Dinheiro ao Caixa")
            .css("color", "green");
    } else if (realToFloat($obj.val()) < 0) {
        $("#ajusteSaldoCaixa #title-valor-inserido-caixa")
            .html("Retirando Dinheiro do Caixa")
            .css("color", "red");
    } else {
        $("#ajusteSaldoCaixa #title-valor-inserido-caixa")
            .html("Sem Alteração")
            .css("color", "blue");
        $("#ajusteSaldoCaixa #valor_total").val(
            parseFloat(
                $("#ajusteSaldoCaixa #valor_total")
                    .data()
                    .valor_total.toString()
                    .replaceAll(",", ".")
            )
        );
    }

    if (realToFloat($obj.val()) > 0 || realToFloat($obj.val()) < 0) {
        console.log(
            parseFloat(
                $("#ajusteSaldoCaixa #valor_total")
                    .data()
                    .valor_total.toString()
                    .replaceAll(",", ".")
            ) * 1000
        );

        $("#ajusteSaldoCaixa #valor_total").val(
            (parseFloat(
                $("#ajusteSaldoCaixa #valor_total")
                    .data()
                    .valor_total.toString()
                    .replaceAll(",", ".")
            ) *
                1000 +
                realToFloat($obj.val().toString().replaceAll(",", ".")) *
                1000) /
            1000
        );
    }
}
function control_forma_pag_pedido1() {
    if ($("#pedidoModal #pedido_forma_pag").val() == 101) {
        $.get(
            "/saude-beta/pedido/creditos-restantes/" +
            $("#pedidoModal #pedido_paciente_id").val(),
            function (data, status) {
                console.log(data + "| " + status);
                if (data != 0) {
                    $("#pedidoModal #creditos-pessoa")
                        .val(data)
                        .data("creditos", data)
                        .attr("disabled", true);
                    atualizarValoresConversaoPedido();
                    $("#pedidoModal #pedido_forma_pag_valor").keyup(
                        function () {
                            atualizarValoresConversaoPedido();
                        }
                    );

                    $("#pedidoModal #pedido_forma_pag_valor").click(
                        function () {
                            atualizarValoresConversaoPedido();
                        }
                    );
                } else {
                    alert("Associado não tem créditos disponíveis");
                    $("#pedidoModal #pedido_forma_pag").val(0);
                }
            }
        );
        $("#pedidoModal #creditos-pessoa").parent().show();
        $("#pedidoModal #financeira").parent().hide();
        $('#pedidoModal #conta-bancaria').parent().hide();
    }
    else if ($("#pedidoModal #pedido_forma_pag").val() == 2) {
        $("#pedidoModal #troco").parent().show();
        $("#pedidoModal #creditos-pessoa").parent().hide();
        $("#pedidoModal #financeira").parent().hide();
        $('#pedidoModal #conta-bancaria').parent().hide();
    }
    else if ($('#pedidoModal #pedido_forma_pag').val() == 4 ||
        $('#pedidoModal #pedido_forma_pag').val() == 5) {
        $('#pedidoModal #troco').parent().hide()
        $('#pedidoMOdal #creditos-pessoa').parent().hide()
        $('#pedidoModal #financeira').parent().hide()
        $('#pedidoModal #conta-bancaria').parent().show();
    }
    else {
        $("#pedidoModal #troco").parent().hide();
        $("#pedidoModal #creditos-pessoa").parent().hide();
        $("#pedidoModal #financeira").parent().show();
        $('#pedidoModal #conta-bancaria').parent().hide();
    }

    $.get(
        "/saude-beta/financeira-formas-pag/listar-financeiras/" +
        $("#pedidoModal #pedido_forma_pag").val(),
        function (data, status) {
            console.log(data + " | " + status);
            $("#pedidoModal #financeira").empty();
            if (data.length == 0)
                $("#pedidoModal #financeira").append('<option value="0">Sem Financeira</option>');
            data.forEach((el) => {
                html = '<option value="' + el.id + '">' + el.descr + "</option>";
                $("#pedidoModal #financeira").append(html);
            });
        }
    );
}

function abrirCaixa() {
    if (window.confirm('Você estará abrindo o caixa se continuar')) {
        $.get(
            "/saude-beta/caixa/abrir-caixa/",
            {
                _token: $("meta[name=csrf-token]").attr("content"),
                id: $("#caixaModal #id_caixa").val(),
            },
            function (data, status) {
                console.log(data + " | " + status);
                if (data == "S") {
                    atualizarCaixaModal()
                    if (!parseInt(document.querySelector("#criarAgendamentoModal #convenio_id").value)) {
                        ShowConfirmationBox(
                            "Caixa aberto com sucesso!",
                            "Deseja abrir janela de venda?",
                            true,
                            true,
                            false,
                            function () {
                                $("#caixaModal").modal("hide");
                                abrir_pedido();
                            },
                            function () {
                                console.log(false);
                            },
                            "Sim",
                            "Não"
                        );    
                    }
                }
            }
        );
    }

}

function abrirBaixaTituloReceberModal($id) {
    $.get(
        "/saude-beta/financeiro/abrir-modal-baixa-receber/" + $id,
        function (data, status) {
            console.log(data + " | " + status);
            data = $.parseJSON(data);
            $("#baixarTitulosReceberModal #valor-total").val(
                parseFloat(data.valor)
                    .toFixed(2)
                    .toString()
                    .replaceAll(".", ",")
            );
            $("#baixarTitulosReceberModal #id-titulo-baixar").val($id);
            $("#baixarTitulosReceberModal #data-baixa").val(data.data);
            $("#baixarTitulosReceberModal").modal("show");
        }
    );
}



function salvarBaixaReceber() {
    if (window.confirm("Tem certeza que deseja baixar título?")) {
        if ($("#baixarTitulosReceberModal #desconto").val() == "") desconto = 0;
        else
            desconto = realToFloat($("#baixarTitulosReceberModal #desconto").val());
        if ($("#baixarTitulosReceberModal #acrescimo").val() == "")
            acrescimo = 0;
        else
            acrescimo = realToFloat($("#baixarTitulosReceberModal #desconto").val());
        $.get(
            "/saude-beta/financeiro/salvar-baixa-receber",
            {
                _token: $("meta[name=csrf-token]").attr("content"),
                id: $("#baixarTitulosReceberModal #id-titulo-baixar").val(),
                valor_total: realToFloat($("#baixarTitulosReceberModal #valor-total").val()),
                conta: $("#baixarTitulosReceberModal #conta").val(),
                data_baixa: formatDataUniversal($("#baixarTitulosReceberModal #data-baixa").val()),
                forma_pag: $("#baixarTitulosReceberModal #forma_pag").val(),
                desconto: desconto,
                acrescimo: acrescimo,
                historico: $('#baixarTitulosReceberModal #historico_id').val()
            },
            function (data, status) {
                console.log(data + " | " + status);
                if (data == "true") {
                    alert("Baixado com sucesso!");
                    $("#baixarTitulosReceberModal").modal("hide");
                    pesquisarTitulosReceber();
                }
            }
        );
    }
}

function salvarContabancaria() {
    msg = "";
    // TITULAR
    if (campo_invalido("#contaBancariaModal #titular", false)) {
        msg += "\n- Titular não preenchido";
    }

    // EMPRESA
    if (campo_invalido("#contaBancariaModal #empresa", true)) {
        msg += "\n- Empresa incorreta";
    }

    if ($("#contaBancariaModal #conta_caixa").prop("checked") != true) {
        // CONTA
        if (campo_invalido("#contaBancariaModal #conta", false)) {
            msg += "\n- Conta não preenchida";
        }

        // AGENCIA
        if (campo_invalido("#contaBancariaModal #agencia", false)) {
            msg += "\n- Agência não preenchida";
        }

        // BANCO
        if (campo_invalido("#contaBancariaModal #banco_id", true)) {
            msg += "\n- Banco não preenchido";
        }
    }

    $.get(
        "/saude-beta/contas-bancarias/salvar",
        {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: $("#contaBancariaModal #id").val(),
            titular: $("#contaBancariaModal #titular").val(),
            conta: $("#contaBancariaModal #conta").val(),
            agencia: $("#contaBancariaModal #agencia").val(),
            banco_id: $("#contaBancariaModal #banco_id").val(),
            empresa: $("#contaBancariaModal #empresa").val(),
            caixa: $("#contaBancariaModal #caixa").val(),
            conta_corrente: $("#contaBancariaModal #conta_conrrente").prop(
                "checked"
            ),
            conta_poupanca: $("#contaBancariaModal #conta_poupanca").prop(
                "checked"
            ),
            conta_aplicacao: $("#contaBancariaModal #conta_cofre").prop(
                "checked"
            ),
            conta_caixa: $("#contaBancariaModal #conta_caixa").prop("checked"),
        },
        function (data, status) {
            console.log(data + " | " + status);
            if (data == "true") {
                location.reload(true);
            }
        }
    );
}

function editar_conta_bancaria($id) {
    $.get(
        "/saude-beta/contas-bancarias/editar/" + $id,
        function (data, status) {
            console.log(data + " | " + status);
            data = $.parseJSON(data);
            $("#contaBancariaModal #id").val(data.conta.id);
            $("#contaBancariaModal #titular").val(data.conta.titular);
            if (data.conta.caixa == "N") {
                $("#contaBancariaModal #conta")
                    .val(data.conta.numero)
                    .removeAttr("disabled");
                $("#contaBancariaModal #agencia")
                    .val(data.conta.agencia)
                    .removeAttr("disabled");
                $("#contaBancariaModal #banco_descr")
                    .val(data.banco.title)
                    .removeAttr("disabled");
                $("#contaBancariaModal #banco_id")
                    .val(data.conta.id_banco)
                    .removeAttr("disabled");
                $("#contaBancariaModal #caiva")
                    .val(0)
                    .hide()
                    .attr("disabled", true)
                    .parent()
                    .hide();
            } else {
                $("#contaBancariaModal #conta").val("").attr("disabled", true);
                $("#contaBancariaModal #agencia")
                    .val("")
                    .attr("disabled", true);
                $("#contaBancariaModal #banco_descr")
                    .val("")
                    .attr("disabled", true);
                $("#contaBancariaModal #banco_id")
                    .val("")
                    .attr("disabled", true);
                console.log(data.conta.id_caixa);
                $("#contaBancariaModal #caixa")
                    .val(data.conta.id_caixa)
                    .removeAttr("disabled")
                    .parent()
                    .show();
            }

            $("#contaBancariaModal #empresa").val(data.conta.id_emp);

            $("#contaBancariaModal #conta_conrrente").prop(
                "checked",
                data.conta.corrente == "S"
            );
            $("#contaBancariaModal #conta_poupanca").prop(
                "checked",
                data.conta.poupanca == "S"
            );
            $("#contaBancariaModal #conta_aplicacao").prop(
                "checked",
                data.conta.aplicacao == "S"
            );
            $("#contaBancariaModal #conta_caixa").prop(
                "checked",
                data.conta.caixa == "S"
            );
            $("#contaBancariaModal").modal("show");
        }
    );
}

function excluir_conta_bancaria($id) {
    $.get(
        "/saude-beta/contas-bancarias/editar/" + $id,
        function (data, status) {
            console.log(data + " | " + status);
            data = $.parseJSON(data);
            if (
                window.confirm(
                    "Deseja mesmo excluir conta do titular " + data.titular
                )
            )
                $.post(
                    "/saude-beta/contas-bancarias/excluir",
                    {
                        _token: $("meta[name=csrf-token").attr("content"),
                        id: $id,
                    },
                    function (data, status) {
                        console.log(data + " | " + status);
                        data = $.parseJSON(data);
                        if (data == "true") {
                            location.reload(true);
                        }
                    }
                );
        }
    );
}

function extratoCaixa(value) {
    $.get(
        "/saude-beta/caixa/mostrar-extrato/" + value,
        {
            id_caixa: $("#caixaModal #id_caixa").val(),
            data_selecionada: $("#data-selecionada").val(),
        },
        function (data, status) {
            console.log(data + " | " + status);
            data = $.parseJSON(data);
            testanto = data;
            $("#extratoCaixaModal #header-extrato-caixa").empty();
            data.header.forEach((el, index) => {
                console.log(
                    '<th width="' +
                    data.header_medidas[index] +
                    '%">' +
                    el +
                    "</th>"
                );
                $("#extratoCaixaModal #header-extrato-caixa").append(
                    '<th class="' +
                    data.header_align[index] +
                    '" width="' +
                    data.header_medidas[index] +
                    '%">' +
                    el +
                    "</th>"
                );
            });
            $("#extratoCaixaModal #table-extrato-caixa tbody").empty();
            if (value == "dinheiro") {
                data.mov.forEach((mov, i) => {
                    console.log(mov.valor_total);
                    if (
                        mov.nome_fantasia == "null" ||
                        mov.nome_fantasia == null
                    )
                        mov.nome_fantasia = mov.created_by_descr;
                    if (mov.valor_total == "null" || mov.valor_total == null)
                        mov.valor_total = 0;
                    if (mov.troco == "null" || mov.troco == null) mov.troco = 0;

                    if (i % 2 == 0) html = "<tr>";
                    else html = "<tr style='background-color: #eaeaea;text-transform: uppercase'>";

                    html +=
                        "   <td width='" + data.header_medidas[0] + "%'>";
                    if (mov.tipo == "E") html += "Venda";
                    if (
                        mov.tipo == "R" &&
                        mov.saldo_anterior > mov.saldo_resultante
                    )
                        html += "Sangria";
                    if (
                        mov.tipo == "R" &&
                        mov.saldo_anterior < mov.saldo_resultante
                    )
                        html += "Suprimento";
                    html += "   </td>";

                    html +=
                        "   <td width='" + data.header_medidas[1] + "%'>";
                    html += mov.descr;
                    html += "   </td>";

                    html +=
                        "   <td width='" + data.header_medidas[2] + "%'>";
                    html += mov.nome_fantasia.split(' ')[0]
                    if (mov.nome_fantasia.split(' ').length >= 2) html += ' ' + mov.nome_fantasia.split(' ')[1];
                    if (mov.nome_fantasia.split(' ').length >= 3) html += ' ' + mov.nome_fantasia.split(' ')[2];
                    html += "   </td>";

                    html +=
                        "   <td class='text-right' width='" +
                        data.header_medidas[3] +
                        "%'>";
                    html += mov.valor_total.toLocaleString("pt-BR", {
                        minimumFractionDigits: 2,
                        style: "currency",
                        currency: "BRL",
                    });
                    html += "   </td>";

                    html +=
                        "   <td class='text-right' width='" +
                        data.header_medidas[4] +
                        "%'>";
                    html += parseFloat(mov.troco).toLocaleString("pt-BR", {
                        minimumFractionDigits: 2,
                        style: "currency",
                        currency: "BRL",
                    });
                    html += "   </td>";

                    html +=
                        "   <td class='text-right' width='" +
                        data.header_medidas[5] +
                        "%'>";
                    html += parseFloat(mov.saldo_anterior).toLocaleString("pt-BR", {
                        minimumFractionDigits: 2,
                        style: "currency",
                        currency: "BRL",
                    });
                    html += "   </td>";

                    html +=
                        "   <td class='text-right' width='" +
                        data.header_medidas[6] +
                        "%'>";
                    html += parseFloat(mov.saldo_resultante).toLocaleString("pt-BR", {
                        minimumFractionDigits: 2,
                        style: "currency",
                        currency: "BRL",
                    });
                    html += "   </td>";

                    html +=
                        "   <td class='text-right' width='" +
                        data.header_medidas[7] +
                        "%'>";
                    html += mov.data;
                    html += "   </td>";

                    html +=
                        "   <td class='text-right' width='" +
                        data.header_medidas[8] +
                        "%'>";
                    html += mov.hora;
                    html += "   </td>";

                    html +=
                        "   <td width='" + data.header_medidas[9] + "%'>";
                    html += mov.created_by_descr.split(' ')[0]
                    if (mov.created_by_descr.split(' ').length >= 2) + ' ' + mov.created_by_descr.split(' ')[1];
                    if (mov.created_by_descr.split(' ').length >= 3) + ' ' + mov.created_by_descr.split(' ')[2];
                    html += "   </td>";

                    $("#extratoCaixaModal #table-extrato-caixa tbody").append(
                        html
                    );
                });
            } else {
                data.mov.forEach((mov, i) => {
                    console.log(mov.valor_total);
                    if (
                        mov.nome_fantasia == "null" ||
                        mov.nome_fantasia == null
                    )
                        mov.nome_fantasia = mov.created_by_descr;
                    if (mov.valor_total == "null" || mov.valor_total == null)
                        mov.valor_total = 0;
                    if (mov.troco == "null" || mov.troco == null) mov.troco = 0;

                    if (i % 2 == 0) html = "<tr>";
                    else html = "<tr style='background-color: #eaeaea'>";

                    html +=
                        "   <td width='" + data.header_medidas[0] + "%'>";
                    if (mov.tipo == "E") html += "Entrada";
                    if (mov.tipo == "R") html += "Ajuste";
                    html += "   </td>";

                    html +=
                        "   <td width='" + data.header_medidas[1] + "%'>";
                    html += mov.descr;
                    html += "   </td>";

                    html +=
                        "   <td width='" + data.header_medidas[2] + "%'>";
                    html += mov.nome_fantasia.split(' ')[0]
                    if (mov.nome_fantasia.split(' ').length >= 2) html += ' ' + mov.nome_fantasia.split(' ')[1];
                    if (mov.nome_fantasia.split(' ').length >= 3) html += ' ' + mov.nome_fantasia.split(' ')[2];
                    html += "   </td>";

                    html +=
                        "   <td class='text-right' width='" +
                        data.header_medidas[3] +
                        "%'>";
                    html += parseFloat(mov.valor_total).toLocaleString("pt-BR", {
                        minimumFractionDigits: 2,
                        style: "currency",
                        currency: "BRL",
                    });
                    html += "   </td>";

                    html +=
                        "   <td class='text-right' width='" +
                        data.header_medidas[4] +
                        "%'>";
                    html += mov.data;
                    html += "   </td>";

                    html +=
                        "   <td class='text-right' width='" +
                        data.header_medidas[5] +
                        "%'>";
                    html += mov.hora;
                    html += "   </td>";

                    html +=
                        "   <td width='" + data.header_medidas[6] + "%'>";
                    console.log(mov.created_by_descr.split(' '));
                    testando = mov.created_by_descr.split(' ');
                    if (mov.created_by_descr) {
                        html += mov.created_by_descr.split(' ')[0]
                        if (mov.created_by_descr.split(' ').length >= 2) + ' ' + mov.created_by_descr.split(' ')[1];
                        if (mov.created_by_descr.split(' ').length >= 3) + ' ' + mov.created_by_descr.split(' ')[2];
                    }
                    html  += "   </td>";

                    $("#extratoCaixaModal #table-extrato-caixa tbody").append(
                        html
                    );
                });
                valor_total = 0
                data.mov.forEach(item => {
                    valor_total += parseFloat(item.valor_total)
                })
                
                html = '<tr style="font-weight: 900;font-size: 25px;">'
                html += '<td colspan="3">Valor Total: </td>'
                html += '<td colspan="1" class="text-right">R$ '+ parseFloat(valor_total.toString().replaceAll(',', '.')).toFixed(2).toString().replaceAll('.', '.') +'</td>'
                html += '<td colspan="3"></td>'
                html += '</td>'
                $("#extratoCaixaModal #table-extrato-caixa tbody").append(
                    html
                );
            }
            $("#extratoCaixaModal").modal("show");
        }
    );
}

function fecharCaixa(b = false) {
    $.get(
        "/saude-beta/caixa/extrato-final",
        {
            id_caixa: $("#caixaModal #id_caixa").val(),
            data_selecionada: $("#data-selecionada").val(),
        },
        function (data, status) {
            console.log(data + " | " + status);
            data = $.parseJSON(data);
            testando = data;
            array_valores = data.array_valores.reverse();
            data = data.array.reverse()
            $("#extratoFechamentoModal .modal-body").empty();
            nomes = ["Dinheiro", "Cartão", "Convênio"].reverse()
            data.forEach((data, iPrincipal) => {
                html = "<h1 style='padding: 30px 0px 5px 5px;font-size: 30px;border-bottom: 1px solid #013d01;margin: 0px 30px 0px 15px;color: green;'>" + nomes[iPrincipal] + '</h1> '
                html += ' <div class="table-header-scroll" style="background: #e5e5e5;margin: 5px 10px 0px 10px;""> ';
                html += "   <table>";
                html += '       <thead id="header-extrato-caixa' + iPrincipal + '" class="custom-thead"></thead> ';
                html += "    </table> ";
                html += "</div> ";
                $("#extratoFechamentoModal .modal-body").append(html);

                $("#extratoFechamentoModal #header-extrato-caixa" + iPrincipal).empty();
                data.header.forEach((el, index) => {
                    $("#extratoFechamentoModal #header-extrato-caixa" + iPrincipal).append(
                        '<th class="' + data.header_align[index] + '" width="' + data.header_medidas[index] + '%">' + el + "<div></div></th>"
                    );
                });


                html = ' <div class="table-body-scroll" style="margin: 5px 10px 0px 10px;"> ';
                html += '   <table id="table-extrato-caixa' + iPrincipal + '"> ';
                html += "       <tbody class='custom-tbody'></tbody> ";
                html += "    </table> ";
                html += "</div> ";
                // html += "<h1 style='padding: 9px 0px 5px 5px;font-size: 19px;margin: 0px 30px 0px 15px;color: green;text-align: end;'>Saldo Final: "+array_valores[iPrincipal].toLocaleString("pt-BR", {
                //     minimumFractionDigits: 2,
                //     style: "currency",
                //     currency: "BRL",
                // }) + '</h1> '
                $("#extratoFechamentoModal .modal-body").append(html);

                $("#extratoFechamentoModal #table-extrato-caixa" + iPrincipal + " tbody").empty();
                if (iPrincipal == 2) {
                    data.mov.forEach((mov, i) => {
                        console.log(mov.valor_total);
                        if (
                            mov.nome_fantasia == "null" ||
                            mov.nome_fantasia == null
                        )
                            mov.nome_fantasia = mov.created_by_descr;
                        if (
                            mov.valor_total == "null" ||
                            mov.valor_total == null
                        )
                            mov.valor_total = 0;
                        if (mov.troco == "null" || mov.troco == null)
                            mov.troco = 0;

                        if (i % 2 == 0) html = "<tr>";
                        else html = "<tr style='background-color: #fff'>";

                        html +=
                            "   <td width='" + data.header_medidas[0] + "%'>";
                        if (mov.tipo == "E") html += "Venda";
                        if (
                            mov.tipo == "R" &&
                            mov.saldo_anterior > mov.saldo_resultante
                        )
                            html += "Sangria";
                        if (
                            mov.tipo == "R" &&
                            mov.saldo_anterior < mov.saldo_resultante
                        )
                            html += "Suprimento";
                        html += "   </td>";

                        html +=
                            "   <td width='" + data.header_medidas[1] + "%'>";
                        html += mov.descr;
                        html += "   </td>";

                        html +=
                            "   <td width='" + data.header_medidas[2] + "%'>";
                        html += mov.nome_fantasia.split(' ')[0] + ' ' + mov.nome_fantasia.split(' ')[1];
                        html += "   </td>";

                        html +=
                            "   <td class='text-right' width='" +
                            data.header_medidas[3] +
                            "%'>";
                        html += mov.valor_total.toLocaleString("pt-BR", {
                            minimumFractionDigits: 2,
                            style: "currency",
                            currency: "BRL",
                        });
                        html += "   </td>";

                        html +=
                            "   <td class='text-right' width='" +
                            data.header_medidas[4] +
                            "%'>";
                        html += mov.troco.toLocaleString("pt-BR", {
                            minimumFractionDigits: 2,
                            style: "currency",
                            currency: "BRL",
                        });
                        html += "   </td>";

                        html +=
                            "   <td class='text-right' width='" +
                            data.header_medidas[5] +
                            "%'>";
                        html += mov.saldo_anterior.toLocaleString("pt-BR", {
                            minimumFractionDigits: 2,
                            style: "currency",
                            currency: "BRL",
                        });
                        html += "   </td>";

                        html +=
                            "   <td class='text-right' width='" +
                            data.header_medidas[6] +
                            "%'>";
                        html += mov.saldo_resultante.toLocaleString("pt-BR", {
                            minimumFractionDigits: 2,
                            style: "currency",
                            currency: "BRL",
                        });
                        html += "   </td>";

                        html +=
                            "   <td class='text-right' width='" +
                            data.header_medidas[7] +
                            "%'>";
                        html += mov.data;
                        html += "   </td>";

                        html +=
                            "   <td class='text-right' width='" +
                            data.header_medidas[8] +
                            "%'>";
                        html += mov.hora;
                        html += "   </td>";

                        html +=
                            "   <td width='" + data.header_medidas[9] + "%'>";
                        html += mov.created_by_descr.split(' ')[0]
                        if (mov.created_by_descr.split(' ').length >= 2) + ' ' + mov.created_by_descr.split(' ')[1];
                        if (mov.created_by_descr.split(' ').length >= 3) + ' ' + mov.created_by_descr.split(' ')[2];
                        html += "   </td>";
                        $(
                            "#extratoFechamentoModal #table-extrato-caixa" + iPrincipal + " tbody"
                        ).append(html);

                    });
                } else {
                    console.log(data.mov)
                    data.mov.forEach((mov, i) => {
                        console.log(mov.valor_total);
                        if (
                            mov.nome_fantasia == "null" ||
                            mov.nome_fantasia == null
                        )
                            mov.nome_fantasia = mov.created_by_descr;
                        if (
                            mov.valor_total == "null" ||
                            mov.valor_total == null
                        )
                            mov.valor_total = 0;
                        if (mov.troco == "null" || mov.troco == null)
                            mov.troco = 0;

                        if (i % 2 == 0) html = "<tr>";
                        else html = "<tr style='background-color: #fff'>";

                        html +=
                            "   <td width='" + data.header_medidas[0] + "%'>";
                        if (mov.tipo == "E") html += "Entrada";
                        if (mov.tipo == "R") html += "Ajuste";
                        html += "   </td>";

                        html +=
                            "   <td width='" + data.header_medidas[1] + "%'>";
                        html += mov.descr;
                        html += "   </td>";

                        html +=
                            "   <td width='" + data.header_medidas[2] + "%'>";
                        html += mov.nome_fantasia.split(' ')[0] + ' ' + mov.nome_fantasia.split(' ')[1];
                        html += "   </td>";

                        html +=
                            "   <td class='text-right' width='" +
                            data.header_medidas[3] +
                            "%'>";
                        html += mov.valor_total.toLocaleString("pt-BR", {
                            minimumFractionDigits: 2,
                            style: "currency",
                            currency: "BRL",
                        });
                        html += "   </td>";

                        html +=
                            "   <td class='text-right' width='" +
                            data.header_medidas[4] +
                            "%'>";
                        html += mov.data;
                        html += "   </td>";

                        html +=
                            "   <td class='text-right' width='" +
                            data.header_medidas[5] +
                            "%'>";
                        html += mov.hora;
                        html += "   </td>";

                        html +=
                            "   <td width='" + data.header_medidas[6] + "%'>";
                        html += mov.created_by_descr.split(' ')[0]
                        if (mov.created_by_descr.split(' ').length >= 2) + ' ' + mov.created_by_descr.split(' ')[1];
                        if (mov.created_by_descr.split(' ').length >= 3) + ' ' + mov.created_by_descr.split(' ')[2];
                        html += "   </td>";
                        $(
                            "#extratoFechamentoModal #table-extrato-caixa" + iPrincipal + " tbody"
                        ).append(html);
                    });
                }


                html = "<h1 style='padding: 9px 0px 5px 5px;font-size: 19px;margin: 0px 30px 0px 15px;color: green;text-align: end;'>Saldo Final: " + array_valores[iPrincipal].toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                }) + '</h1> '
                $("#extratoFechamentoModal .modal-body").append(html);

            });
            if (b) {
                $('#extratoFechamentoModal .row.mt-3').hide()
            }
            else {
                $('#extratoFechamentoModal .row.mt-3').show()
            }
            $("#extratoFechamentoModal").modal("show");
        }
    );
    // $.post(
    //     '/saude-beta/caixa/fechar-caixa', {
    //     _token: $("meta[name=csrf-token]").attr("content"),
    //     id: $('#caixaModal #id_caixa').val()
    // }, function (data, status) {
    //     console.log(data + ' | ' + status)
    // })
}

function sangriaCaixa(bfechar = false) {
    $.get(
        "/saude-beta/caixa/abrir-modal-saldo",
        {
            id_caixa: $("#caixaModal #id_caixa").val(),
        },
        function (data, status) {
            console.log(data + " | " + status);
            $("#ajusteSaldoCaixa #valor_total").val(data);
            $("#ajusteSaldoCaixa #valor_total").data("valor_total", data);
            $("#ajusteSaldoCaixa #inserir_valor").val(0 - data);
            $("#ajusteSaldoCaixa #title-valor-inserido-caixa")
                .html("Sem Alteração")
                .css("color", "blue");
            $("#ajusteSaldoCaixa #inserir_valor").change();
            $("#ajusteSaldoCaixa").modal("show");

            if (bfechar) $('#ajusteSaldoCaixa #id').attr('onclick', 'salvar_valor_caixa(true)')
            else $('#ajusteSaldoCaixa #id').attr('onclick', 'salvar_valor_caixa()')
        }
    );
}

function visualizar_titulo_receber($id) {
    $.get(
        "/saude-beta/financeiro/titulos-receber/visualizar/" + $id,
        function (data, status) {
            console.log(data + " | " + status);
            data = $.parseJSON(data);
            $("#n-doc").html(data.ndoc);
            $("#descricao").html(data.descricao);
            if (data.pago == "S") $("#pago").html("Sim");
            else $("#pago").html("Não");
            if (
                data.d_pago != "" &&
                data.d_pago != "null" &&
                data.d_pago != null
            )
                $("#d_pago").html(formatDataBr(data.d_pago));
            else $("#d_pago").html("--/--/----");
            $("#fornecedor").html(data.fornecedor);
            $("#criado-por").html(data.criado_por);
            $("#pago-por").html(data.pago_por);
            if (
                data.entrada != "" &&
                data.entrada != "null" &&
                data.entrada != null
            )
                $("#entrada").html(formatDataBr(data.entrada));
            else $("#entrada").html("--/--/----");
            if (
                data.emissao != "" &&
                data.emissao != "null" &&
                data.emissao != null
            )
                $("#emissao").html(formatDataBr(data.emissao));
            else $("#emissao").html("--/--/----");
            if (
                data.vencimento != "" &&
                data.vencimento != "null" &&
                data.vencimento != null
            )
                $("#vencimento").html(formatDataBr(data.vencimento));
            else $("#vencimento").html("--/--/----");
            $("#valor-total").html(
                data.valor_total.toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                })
            );
            $("#verTituloReceberModal").modal("show");
            $("#table-parcelas tbody").empty();
            data.parcelas.forEach((parcela) => {
                html = " <tr>";

                html += '   <td width="12%" class="text-left">';
                html += parcela.parcela;
                html += "   </td>";

                html += '   <td width="20%" class="text-right">';
                if (
                    parcela.d_entrada != "" &&
                    parcela.d_entrada != null &&
                    parcela.d_entrada != "null"
                )
                    html += formatDataBr(parcela.d_entrada);
                else html += "--/--/----";
                html += "   </td>";

                html += '   <td width="20%" class="text-right">';
                if (
                    parcela.d_emissao != "" &&
                    parcela.d_emissao != null &&
                    parcela.d_emissao != "null"
                )
                    html += formatDataBr(parcela.d_emissao);
                else html += "--/--/----";
                html += "   </td>";

                html += '   <td width="20%" class="text-right">';
                if (
                    parcela.d_vencimento != "" &&
                    parcela.d_vencimento != null &&
                    parcela.d_vencimento != "null"
                )
                    html += formatDataBr(parcela.d_vencimento);
                else html += "--/--/----";
                html += "   </td>";

                html += '   <td width="15%" class="text-right">';
                html += parcela.valor_total.toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                });
                html += "   </td>";

                html += '   <td width="8%" class="text-right">';

                data1 = new Date(parcela.d_vencimento);
                data2 = new Date();
                console.log(parcela.pago);
                console.log(parseInt(parcela.valor_total));
                console.log(parseInt(parcela.valor_total_pago));
                if (
                    parcela.pago == "S" &&
                    parseInt(parcela.valor_total) ==
                    parseInt(parcela.valor_total_pago)
                ) {
                    html +=
                        ' <img style="width:18px; height: 18px" src="http://vps.targetclient.com.br/saude-beta/img/pago.png"> ';
                } else if (data1 >= data2) {
                    html +=
                        ' <img style="width: 19px; height: 19px" src="http://vps.targetclient.com.br/saude-beta/img/pendente.png"> ';
                } else {
                    html +=
                        ' <img style="width: 19px; height: 19px" src="http://vps.targetclient.com.br/saude-beta/img/pendente.png"> ';
                }

                html += "   </td>";

                html += " </tr>";

                $("#table-parcelas tbody").append(html);
            });
        }
    );
}

function visualizar_titulo_pagar($id) {
    $("#verTituloPagarModal").modal("show");
    $.get(
        "/saude-beta/financeiro/titulos-pagar/visualizar/" + $id,
        function (data, status) {
            console.log(data + " | " + status);
            data = $.parseJSON(data);
            $("#n-doc").html(data.ndoc);
            $("#descricao").html(data.descricao);
            $("#pago").html(data.pago);
            if (data.d_pago != "") $("#d_pago").html(formatDataBr(data.d_pago));
            else $("#d_pago").html("NÃO INFORMADO");
            $("#fornecedor").html(data.fornecedor);
            $("#criado-por").html(data.criado_por);
            $("#pago-por").html(data.pago_por);
            if (data.entrada != "")
                $("#entrada").html(formatDataBr(data.entrada));
            else $("#entrada").html("NÃO ENCONTRATO");
            if (data.emissao != "")
                $("#emissao").html(formatDataBr(data.emissao));
            else $("#emissao").html("NÃO ENCONTRATO");
            if (data.vencimento != "")
                $("#vencimento").html(formatDataBr(data.vencimento));
            else $("#vencimento").html("NÃO ENCONTRATO");
            $("#valor-total").html(data.valor_total);
            $("#verTituloPagarModal").modal("show");
            $("#table-parcelas tbody").empty();
            data.parcelas.forEach((parcela) => {
                html = " <tr>";

                html += "   <td>";
                html += parcela.parcela;
                html += "   </td>";

                html += "   <td>";
                html += parcela.d_entrada;
                html += "   </td>";

                html += "   <td>";
                html += parcela.d_emissao;
                html += "   </td>";

                html += "   <td>";
                html += parcela.d_vencimento;
                html += "   </td>";

                html += "   <td>";
                html += parcela.valor_total;
                html += "   </td>";

                html += " </tr>";

                $("#table-parcelas tbody").append(html);
            });
        }
    );
}

function pesquisarTitulosPagar() {
    if ($("#data-inicial").val().length == 10)
        datainicial = formatDataUniversal($("#data-inicial").val());
    else datainicial = "";

    if ($("#data-final").val().length == 10)
        datafinal = formatDataUniversal($("#data-final").val());
    else datafinal = "";

    if ($("#liquidados").prop("checked") == 1) liquidado = "S";
    else liquidado = "N";
    $.get(
        "/saude-beta/financeiro/titulos-pagar/pesquisar",
        {
            contrato: $("#contrato").val(),
            associado: $("#paciente_id").val(),
            empresa: $("#empresa").val(),
            venc_ou_lanc: $("#venc-ou-lanc").val(),
            datainicial: datainicial,
            datafinal: datafinal,
            valor_inicial: $("#valor-inicial").val(),
            valor_final: $("#valor-final").val(),
            forma_pag: $("#forma-pag").val(),
            liquidados: liquidado,
        },
        function (data, status) {
            console.log(data + " | " + status);
            $("#table-plano_tratamento tbody").empty();
            data.forEach((titulo) => {
                $date1 = new Date(titulo.dt_vencimento);
                $date = new Date();
                html = ' <tr style="cursor: pointer; font-size: 12px">';
                html +=
                    ' <td style="width: 5%; min-width: 5%; max-width: 5%" class="text-left"> ';
                if (titulo.pago == "A") {
                    html +=
                        ' <img style="width:18px; height: 18px; margin-left: 8%;" src="http://vps.targetclient.com.br/saude-beta/img/pago.png""> ';
                } else if (titulo.pago != "S" && $date1 < $date) {
                    html +=
                        ' <img style="width:18px; height: 18px; margin-left: 8%;" src="http://vps.targetclient.com.br/saude-beta/img/vencido.png""> ';
                } else
                    html +=
                        ' <img style="width:18px; height: 18px; margin-left: 8%;" src="http://vps.targetclient.com.br/saude-beta/img/pendente.png""> ';
                html += " </td> ";
                html += ' <td style="width: 6%;" class="text-left"> ';
                html += titulo.id_pedido;
                html += " </td>";
                html += '<td style="width: 19%;" class="text-left"> ';
                html += titulo.pessoa;
                html += "</td> ";
                html +=
                    '<td style="width: 10%; min-width: 10%; max-width: 10%" class="text-left"> ';
                html += titulo.pagamento;
                html += "</td> ";
                html +=
                    '<td style="width: 10%; min-width: 10%; max-width: 10%" class="text-right"> ';
                html += titulo.parcela;
                html += "</td> ";
                html +=
                    '<td style="width: 10%; min-width: 10%; max-width: 10%" class="text-right"> ';
                html += titulo.valor_total.toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                });
                html += "</td> ";
                html +=
                    '<td style="width: 10%; min-width: 10%; max-width: 10%" class="text-right"> ';
                html += titulo.valor_total_pago.toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                });
                html += "</td> ";
                html +=
                    '<td style="width: 10%; min-width: 15%; max-width: 15%" class="text-right"> ';
                html += formatDataBr(titulo.dt_lanc);
                html += "</td> ";
                html +=
                    '<td style="width: 10%; min-width: 15%; max-width: 15%" class="text-right"> ';
                html += formatDataBr(titulo.dt_vencimento);
                html += "</td> ";
                html +=
                    '<td style="width: 10%; min-width: 15%; max-width: 15%" class="text-right"> ';
                if (titulo.valor_total != titulo.valor_total_pago) {
                    html +=
                        ' <img onclick="abrirBaixaTituloPagarModal(' +
                        titulo.id +
                        ')" style="width: 28px;height: 27px;margin-right: 15px;margin-top: 3px;" src="http://vps.targetclient.com.br/saude-beta/img/correto.png"></img> ';
                }
                html +=
                    ' <img style="width:20px; height: 20px;margin-right: 10px" src="http://vps.targetclient.com.br/saude-beta/img/olho.png"';
                if (titulo.id_pedido == titulo.ndoc) {
                    html +=
                        ' onclick="new_system_window(' +
                        "'" +
                        "pedido/imprimir/ " +
                        titulo.id_pedido +
                        "/0" +
                        "')";
                }
                html += ">";
                html += "</td>";
                html += "</tr> ";

                $("#table-plano_tratamento tbody").append(html);
            });
        }
    );
}

function pesquisarTitulosReceber() {
    if ($("#data-inicial").val().length == 10)
        datainicial = formatDataUniversal($("#data-inicial").val());
    else datainicial = "";

    if ($("#data-final").val().length == 10)
        datafinal = formatDataUniversal($("#data-final").val());
    else datafinal = "";

    if ($("#liquidados").prop("checked") == 1) liquidado = "S";
    else liquidado = "N";
    $("#table-plano_tratamento tbody").empty();
    $("#table-plano_tratamento tbody").append("<tr><td colspan = 10 style = 'font-size:2.5rem;text-align:center;padding-top:3rem'>" + 
        '<div>' +
            '<div class="d-flex" style="justify-content: center">' +
                '<div class="loader"></div>' +
            '</div>' +
        '</div>' +
    "</td></tr>");
    $.get(
        "/saude-beta/financeiro/titulos-receber/pesquisar",
        {
            contrato: $("#contrato").val(),
            associado: $("#paciente_id").val(),
            empresa: $("#empresa").val(),
            venc_ou_lanc: $("#venc-ou-lanc").val(),
            datainicial: datainicial,
            datafinal: datafinal,
            valor_inicial: parseInt(phoneInt($("#valor-inicial").val())) / 100,
            valor_final: parseInt(phoneInt($("#valor-final").val())) / 100,
            forma_pag: $("#forma-pag").val(),
            liquidados: liquidado,
            analitico: "S",
            id : ""
        },
        function (data, status) {
            console.log(data + " | " + status);
            $("#table-plano_tratamento tbody").empty();
            data.forEach((titulo) => {
                $date1 = new Date(titulo.dt_vencimento);
                $date = new Date();
                html = ' <tr style="cursor: pointer; font-size: 12px">';
                html +=
                    ' <td style="width: 5%; min-width: 5%; max-width: 5%" class="text-left"> ';
                if (titulo.pago == "S") {
                    html +=
                        ' <img style="width:18px; height: 18px; margin-left: 8%;" src="http://vps.targetclient.com.br/saude-beta/img/pago.png""> ';
                } else if (titulo.pago != "S" && $date1 < $date) {
                    html +=
                        ' <img style="width:18px; height: 18px; margin-left: 8%;" src="http://vps.targetclient.com.br/saude-beta/img/vencido.png""> ';
                } else
                    html +=
                        ' <img style="width:18px; height: 18px; margin-left: 8%;" src="http://vps.targetclient.com.br/saude-beta/img/pendente.png""> ';
                html += " </td> ";
                html += ' <td style="width: 6%;" class="text-left"> ';
                html += titulo.id_pedido;
                html += " </td>";
                html += '<td style="width: 19%;" class="text-left"> ';
                html += titulo.pessoa;
                html += "</td> ";
                html +=
                    '<td style="width: 10%; min-width: 10%; max-width: 10%" class="text-left"> ';
                if (titulo.pagamento != null && titulo.pagamento != "null")
                    html += titulo.pagamento;
                else html += "NÃO ENCONTRADO";
                html += "</td> ";
                html +=
                    '<td style="width: 10%; min-width: 10%; max-width: 10%" class="text-right"> ';
                html += titulo.parcela;
                html += "</td> ";
                html +=
                    '<td style="width: 10%; min-width: 10%; max-width: 10%" class="text-right"> ';
                html += titulo.valor_total.toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                });
                html += "</td> ";
                html +=
                    '<td style="width: 10%; min-width: 10%; max-width: 10%" class="text-right"> ';
                html += titulo.valor_total_pago.toLocaleString("pt-BR", {
                    minimumFractionDigits: 2,
                    style: "currency",
                    currency: "BRL",
                });
                html += "</td> ";
                html +=
                    '<td style="width: 10%; min-width: 15%; max-width: 15%" class="text-right"> ';
                html += formatDataBr(titulo.dt_lanc);
                html += "</td> ";
                html +=
                    '<td style="width: 10%; min-width: 15%; max-width: 15%" class="text-right"> ';
                html += formatDataBr(titulo.dt_vencimento);
                html += "</td> ";
                html +=
                    '<td style="width: 10%; min-width: 15%; max-width: 15%" class="text-right"> ';
                if (titulo.valor_total != titulo.valor_total_pago) {
                    html +=
                        ' <img onclick="abrirBaixaTituloReceberModal(' +
                        titulo.id +
                        ')" style="width: 28px;height: 27px;margin-right: 15px;margin-top: 3px;" src="http://vps.targetclient.com.br/saude-beta/img/correto.png"></img> ';
                }
                html +=
                    ' <img style="width:20px; height: 20px;margin-right: 10px" src="http://vps.targetclient.com.br/saude-beta/img/olho.png"';
                if (titulo.id_pedido == titulo.ndoc) {
                    html +=
                        ' onclick="new_system_window(' +
                        "'" +
                        "pedido/imprimir/ " +
                        titulo.id_pedido +
                        "/0" +
                        "')" +
                        '"';
                } else
                    html +=
                        ' onclick="visualizar_titulo_receber(' +
                        titulo.id +
                        ')"';
                html += ">";
                html += "</td>";
                html += "</tr> ";

                $("#table-plano_tratamento tbody").append(html);
            });
        }
    );
}

function abrirModalConversaoCredito(id_pedido, bAntigo) {
    $.get(
        "/saude-beta/pedido/abrir-modal-conversao/" +
        id_pedido +
        "/" +
        bAntigo,
        function (data, status) {
            console.log(data + " | " + status);
            data = $.parseJSON(data);
            if (data === "erro") {
                alert("erro");
                return;
            } else {
                $("#conversaoCreditoModal #id_pedido").val(id_pedido);
                $("#conversaoCreditoModal #bAntigo").val(bAntigo);
                $("#conversaoCreditoModal #data_contrato").html(
                    data.contrato.data
                );
                $("#conversaoCreditoModal #data_validade").html(
                    data.contrato.data_validade
                );
                $("#conversaoCreditoModal #table-conversao").empty();
                if (bAntigo == 1) {
                    data.planos.forEach((plano) => {
                        html = " <tr> ";
                        html +=
                            '     <td width="100%%" class="text-left d-flex" data-id_para_conversao="' +
                            plano.id_plano +
                            '"> ';
                        html +=
                            '         <div style="margin: -1px 10px 0px 0px;width: 5%;"> ';
                        html +=
                            '             <input id="' +
                            plano.id_plano +
                            '" onclick="abrirModalQtdeConversao(' +
                            plano.id_plano +
                            ')" style="width:100%;height:100%" type="checkbox"> ';
                        html += "         </div> ";
                        html += plano.descr_plano;
                        html += "     </td> ";
                        html +=
                            '     <td id="qtd-conv-' +
                            plano.id_plano +
                            '" width="15%" class="text-right qtd_conv">' +
                            0 +
                            "</td> ";
                        html +=
                            '     <td data-qtd_rest="' +
                            plano.qtde_restante +
                            '" id="qtde-restante-' +
                            plano.id_plano +
                            '" width="15%" class="text-right">' +
                            plano.qtde_restante +
                            "</td> ";
                        html +=
                            '     <td id="valor-und-' +
                            plano.id_plano +
                            '" width="15%" class="text-right valor_unitario">' +
                            plano.valor_und.toFixed(2).replaceAll(".", ",") +
                            "</td> ";
                        html +=
                            '     <td class="valor-total text-right" id="valor-total-' +
                            plano.id_plano +
                            '" width="15%">' +
                            plano.valor_total.toFixed(2).replaceAll(".", ",") +
                            "</td> ";
                        html += " </tr> ";
                        $("#conversaoCreditoModal #table-conversao").append(
                            html
                        );
                    });
                    $("#conversaoCreditoModal").modal("show");
                } else {
                    indice = 0;
                    data.planos.forEach((plano) => {
                        html = " <tr> ";
                        html +=
                            '     <td width="100%%" class="text-left d-flex" data-id_para_conversao="' +
                            plano.id_plano +
                            '"> ';
                        html +=
                            '         <div style="margin: -1px 10px 0px 0px;width: 5%;"> ';
                        html +=
                            '             <input id="' +
                            plano.id_plano +
                            '" onclick="abrirModalQtdeConversao(' +
                            plano.id_plano +
                            ')" style="width:100%;height:100%" type="checkbox"> ';
                        html += "         </div> ";
                        html += plano.descr_plano;
                        html += "     </td> ";
                        html +=
                            '     <td id="qtd-conv-' +
                            plano.id_plano +
                            '" width="15%" class="text-right qtd_conv">' +
                            0 +
                            "</td> ";
                        html +=
                            '     <td data-qtd_rest="' +
                            data.restantes_ar[indice] +
                            '" id="qtde-restante-' +
                            plano.id_plano +
                            '" width="15%" class="text-right">' +
                            data.restantes_ar[indice] +
                            "</td> ";
                        html +=
                            '     <td id="valor-und-' +
                            plano.id_plano +
                            '" width="15%" class="text-right valor_unitario">' +
                            plano.valor_und.toFixed(2).replaceAll(".", ",") +
                            "</td> ";
                        html +=
                            '     <td class="valor-total text-right" id="valor-total-' +
                            plano.id_plano +
                            '" width="15%">' +
                            plano.valor_total.toFixed(2).replaceAll(".", ",") +
                            "</td> ";
                        html += " </tr> ";
                        $("#conversaoCreditoModal #table-conversao").append(
                            html
                        );

                        indice++;
                    });
                    $("#conversaoCreditoModal").modal("show");
                }
            }
            atualizarValoresConversao();
        }
    );
}

function salvarFechamentoCaixa() {
    $.get('/saude-beta/caixa/fechar-caixa', {
        _token: $('meta[name=csrf-token]').attr('content'),
        id_caixa: $("#caixaModal #id_caixa").val(),
        data_selecionada: $("#data-selecionada").val(),
    }, function (data, status) {
        console.log(data + ' | ' + status)
        if (data == 'true') {
            alert('Caixa fechado com sucesso!')
            location.reload(true);
        }
    })
}

function salvarFinanceira() {
    rede = [];
    parcela = [];
    taxa = [];

    $("#financeiraModal #table-taxas-parcelas tr").each(function () {
        rede.push($(this).find("[data-rede]").data().rede);
        parcela.push($(this).find("[data-parcela]").data().parcela);
        taxa.push(realToFloat($(this).find("[data-taxa]").data().taxa));
    });

    $.get(
        "/saude-beta/financeira/salvar",
        {
            _token: $("meta[name=csrf-token").attr("content"),
            id: $("#financeiraModal #id_financeira").val(),
            descr: $("#financeiraModal #descr").val(),
            id_emp: $("#financeiraModal #empresa").val(),
            tipo_baixa: $("#financeiraModal #tipo-baixa").val(),
            prazo: $("#financeiraModal #prazo").val(),
            taxa_padrao: realToFloat($("#financeiraModal #taxa-padrao").val()),
            redes: rede,
            parcelas: parcela,
            taxas: taxa,
        },
        function (data, status) {
            console.log(data + " | " + status);
            if (data == "true") {
                location.reload(true);
            }
        }
    );
}

function editar_financeira(id_financeira) {
    $.get(
        "/saude-beta/financeira/mostrar/" + id_financeira,
        function (data) {
            data = $.parseJSON(data);
            $("meta[name=csrf-token").attr("content");
            $("#financeiraModal #id_financeira").val(data.financeira.id);
            $("#financeiraModal #descr").val(data.financeira.descr);
            $("#financeiraModal #empresa").val(data.financeira.id_emp);
            $("#financeiraModal #tipo-baixa").val(
                data.financeira.tipo_de_baixa
            );
            $("#financeiraModal #prazo").val(data.financeira.prazo);
            $("#financeiraModal #taxa-padrao").val(data.financeira.taxa_padrao);
            $("#financeiraModal").modal("show");

            data.taxas.forEach((taxa) => {
                rede = taxa.rede_adquirente;
                n_parcela = taxa.max_parcela;
                taxa = taxa.taxa;

                html = "<tr>";
                html += "   <td data-rede='" + rede + "' width='70%'>";
                if (rede == "D") html += "Débito";
                else html += "Crédito";
                html += "   </td>";
                html +=
                    "   <td data-parcela='" +
                    n_parcela +
                    "' width='10%'>";
                html += n_parcela;
                html += "   </td>";
                html +=
                    "   <td data-taxa='" +
                    taxa +
                    "' class='text-right' width='10%'>";
                html += taxa;
                html += "   </td>";
                html += "   <td class='text-right' width='10%'>";
                html +=
                    "       <img onclick='$(this).parent().parent().remove()' style='height: 20px; width: 20px; cursor: pointer' src='http://vps.targetclient.com.br/saude-beta/img/lixeira.png'>";
                html += "   </td>";
                html += "</tr>";
                $("#financeiraModal #table-taxas-parcelas tbody").append(html);
            });
        }
    );
}

function addTaxaParcelaLista() {
    rede = $("#financeiraModal #rede-adquirente").val();
    n_parcela = $("#financeiraModal #n-max-parcela").val();
    taxa = $("#financeiraModal #taxa").val();

    html = "<tr>";
    html += "   <td data-rede='" + rede + "' width='70%'>";
    if (rede == 1) html += "ON - MORUMBI";
    else html += "ON - IBIRAPUERA";
    html += "   </td>";
    html += "   <td data-parcela='" + n_parcela + "' width='10%'>";
    html += n_parcela;
    html += "   </td>";
    html += "   <td data-taxa='" + taxa + "' class='text-right' width='10%'>";
    html += taxa;
    html += "   </td>";
    html += "   <td class='text-right' width='10%'>";
    html +=
        "       <img onclick='$(this).parent().parent().remove()' style='height: 20px; width: 20px; cursor: pointer' src='http://vps.targetclient.com.br/saude-beta/img/lixeira.png'>";
    html += "   </td>";
    html += "</tr>";
    $("#financeiraModal #table-taxas-parcelas tbody").append(html);
}



function controlVisualizacaoFaturamentoCockpit($obj) {
    if ($obj.prop('checked')) {
        $.get(
            '/saude-beta/cockpit/exibir-finalizacao/' + $('#cockpitModal #value').val() + '/' + $("#periodo-cockpit").val(),
            function (data, status) {
                console.log(data + ' | ' + status)
                $("#cockpitModal #table-cockpit").empty();
                html = '<table class="table table-hover">'
                html += '   <thead>'
                html += '       <th width="5%" class="text-left">Contrato</th>'
                html += '       <th width="35%" class="text-left">Paciente</th>'
                html += '       <th width="15%" class="text-left">Plano</th>'
                html += '       <th width="15%" class="text-right">Finalização</th>'
                html += '       <th width="15%" class="text-right">Fim</th>'
                html += '       <th width="15%" class="text-right">Valor</th>'
                html += '   </thead>'
                html += '   <tbody></tbody></table>'
                $("#cockpitModal #table-cockpit").append(html);
                a = data
                control = 0
                console.log(data)
                data.forEach(el => {
                    if (el.Contrato == null || el.Contrato == 0) el.Contrato = '-----------'
                    if (el.Paciente == null) el.Paciente = '-----------'
                    if (el.Plano == null) el.Plano = '-----------'
                    if (el.Inicio == null) el.Inicio = '-----------'
                    if (el.Fim == null) el.Fim = '-----------'
                    if (el.Valor == null) el.Valor = '-----------'
                    if (el.Caixa == null) el.Caixa = '-----------'



                    el.Inicio = el.Inicio.substr(0, 10).replace('-', '/').replace('-', '/')
                    el.Inicio = el.Inicio.substr(8) + el.Inicio.substr(4, 3) + '/' + el.Inicio.substr(0, 4)

                    el.Fim = el.Fim.substr(0, 10).replace('-', '/').replace('-', '/')
                    el.Fim = el.Fim.substr(8) + el.Fim.substr(4, 3) + '/' + el.Fim.substr(0, 4)


                    let data1 = new Date(el.data_nascimento)
                    let data2 = new Date()

                    console.log(parseInt(el.Inicio.substr(3, 2)))
                    console.log(parseInt($('#periodo-cockpit').val().substr(3, 2)))
                    console.log(parseInt(el.Inicio.substr(3, 2)) > parseInt(formatDataBr($('#periodo-cockpit').val()).substr(3, 2)) && control == 0)
                    if (parseInt(el.Inicio.substr(3, 2)) > parseInt(formatDataBr($('#periodo-cockpit').val()).substr(3, 2)) && control == 0) {
                        html = '<table class="table table-hover">'
                        html += '   <thead>'
                        html += '       <th width="5%" class="text-left">Contrato</th>'
                        html += '       <th width="35%" class="text-left">Paciente</th>'
                        html += '       <th width="15%" class="text-left">Plano</th>'
                        html += '       <th width="15%" class="text-right">Finalização</th>'
                        html += '       <th width="15%" class="text-right">Fim</th>'
                        html += '       <th width="15%" class="text-right">Valor</th>'
                        html += '   </thead>'
                        html += '   <tbody>'
                        html += ' <div class="d-flex col-12" style="justify-content: space-between;height: 50px;padding: 15px 15px 0px 15px;"> '
                        html += '     <h3 style="color: black;" id="titulo-cockpit-modal">RETROATIVO</h3> '
                        html += ' </div> '
                        $("#cockpitModal #table-cockpit").append(html)
                        control = 1
                    }
                    idade = data2.getFullYear() - data1.getFullYear()
                    html = '<tr>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-left" width="5%">' + el.Contrato + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-left" width="35%">' + el.Paciente + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-left" width="15%">' + el.Plano + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-right" width="15%">' + el.Inicio + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-right" width="15%">' + el.Fim + '</td>'
                    html += '       <td id="valor_contrato" style="font-size: 13px;" class="up-txt text-right" width="10%">' + el.Valor.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }) + '</td>'
                    html += '</tr>'
                    $($("#cockpitModal #table-cockpit > table > tbody")[control]).append(html)
                });
                $("#cockpitModal").modal("show")
            }
        )
    }
    else {
        abrirModalCockpit($('#cockpitModal #value').val())
    }
}





function encontrarContratosLote(finalizar) {
    id = $("#agendamentosEmLoteModal #paciente_id").val()
    console.log('/saude-beta/pedido/listar-contratos-pessoa/' + id + '/' + false + '/' + retornarDataAtual(1))
    $.get('/saude-beta/pedido/listar-contratos-pessoa/' + id + '/' + false + '/' + retornarDataAtual(1), function (data) {
        $('#agendamentosEmLoteModal #id_contrato').empty()
        console.log(data)
        $('#agendamentosEmLoteModal #id_contrato').append('<option value="0">Selecionar contrato...</option>')
        data.forEach(contratos => {
            if (!data.data_validade) {
                let datac = contratos.data
                datac = datac[8] + datac[9] + '/' + datac[5] + datac[6] + '/' + datac[0] + datac[1] + datac[2] + datac[3];
                html = '<option value="' + contratos.id + '">'
                html += datac + ' | ' + contratos.descr
                html += '</option>'

                $('#agendamentosEmLoteModal #id_contrato').append(html);
            }
        });
    });
}
function encontrarPlanosContratoLote(id_contrato) {
    console.log('teste')
    $.get('/saude-beta/pedido/listar-planos-pedido', {
        data: retornarDataAtual(1),
        id_contrato: id_contrato
    }, function (data) {
        $('#agendamentosEmLoteModal #id_plano').empty()
        $('#agendamentosEmLoteModal #id_plano').append('<option value="0">Selecionar plano...</option>')
        console.log(data)
        a = data
        data.forEach(plano => {
            html = '<option value="' + plano.id + '">'
            html += plano.descr + ' (Restam ' + (parseInt(plano.agendaveis) - parseInt(plano.agendados)) + ' atividades)'
            html += '</option>'
            $('#agendamentosEmLoteModal #id_plano').append(html);
        })

        // for(i = 0; i < data.planos_id.length; i++){

        //     html += data.planos_descr[i] + '   (' + data.agendados[i] + '/' + data.agendaveis[i] + ')'
        //     html += '</option>'
        //     $('#criarAgendamentoModal #id_plano').append(html);
        // }
    })
}