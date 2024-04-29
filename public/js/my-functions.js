// const { data } = require("jquery");
function abreviacao(s) {
    return /^([A-Z]\.)+$/.test(s);
}

function numeralRomano(s) {
    return /^M{0,4}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})$/.test(s);
}

const handlePhone = (event) => {
    let input = event.target;
    input.value = phoneMask(input.value);
}

const phoneMask = (value) => {
    if (!value) return "";
    value = value.replace(/\D/g, "");
    if (value.length >= 8 && value.length <= 13) {
        if (value.length == 10 || value.length == 11) value = value.replace(/(\d{2})(\d)/, "($1) $2");
        else if (value.length == 12 || value.length == 13) value = value.replace(/(\d{2})(\d{2})(\d)/, "+$1 ($2) $3");
        value = value.replace(/(\d)(\d{4})$/, "$1-$2");
    }
    return value;
}

function phoneInt(val) {
    // if ($.isNumeric(valoresPlanoReal[x])) val = (val * 100).toString();
    if (val !== val.toString()) val = (val * 100).toString();
    const permitir = "0123456789";
    var final = "";
    console.log(val);
    for (var i = 0; i < val.length; i++) {
        if (permitir.indexOf(val[i]) > -1) final += val[i];
    }
    return final;
}

window.onload = function () {
    var td_telefone = document.getElementsByClassName("td_telefone");
    for (var i = 0; i < td_telefone.length; i++) td_telefone[i].innerHTML = phoneMask(td_telefone[i].innerHTML);
}

function captalize(texto) {
    let prepos = ["da", "do", "das", "dos", "a", "e", "de"];
    return texto.split(' ') // quebra o texto em palavras
        .map((palavra) => { // para cada palavra
            if (abreviacao(palavra) || numeralRomano(palavra)) {
                return palavra;
            }

            palavra = palavra.toLowerCase();
            if (prepos.includes(palavra)) {
                return palavra;
            }
            return palavra.charAt(0).toUpperCase() + palavra.slice(1);
        })
        .join(' '); // junta as palavras novamente
}
var from_autocomplete;
var indice_autocomplete, total_autocomplete;
function autocomplete(_this) {
    var table = _this.data().table,
        column = _this.data().column,
        input_id = _this.data().input,
        filter = _this.data().filter,
        filter_col = _this.data().filter_col,
        search = _this.val(),
        element = _this,
        div_result;

    if (table == "pessoa(filtro)" && !isLote) table = "pessoa";

    $(document).click(function (e) {
        if (e.target.id != element.prop('id')) {
            div_result.remove();
        }
    });

    if (element.parent().find('.autocomplete-result').length == 0) {
        div_result = $('<div class="autocomplete-result">');
        element.after(div_result);
        if (element[0].id.indexOf("pedido_enc") > - 1) element[0].nextSibling.style.width = "92.5%";
        else if (element[0].id.indexOf("pedido") > -1) element[0].nextSibling.style.width = "96%";
        else if (element[0].id == "enc_cid_nome") {
            if (location.href.indexOf("agenda") > -1) element[0].nextSibling.style.width = "94.5%";
            else if (location.href.indexOf("prontuario") > -1) element[0].nextSibling.style.width = "95.8%";
            else element[0].nextSibling.style.width = "97.5%";
        } else if (element[0].id == "responsavel") element[0].nextSibling.style.width = "97.25%";
        else if (["paciente_nome", "agenda_encaminhante_nome"].indexOf(element[0].id) > -1) element[0].nextSibling.style.width = "94.5%";
        else if (element[0].id == "prof_nome") element[0].nextSibling.style.width = "92%";
        else if (element[0].id == "enc_esp_nome") element[0].nextSibling.style.width = "94%";
        else if (element[0].id == "sol_enc_esp_nome") element[0].nextSibling.style.width = "90%";
        else if (element[0].id == "sol_encaminhante_nome") element[0].nextSibling.style.width = "93%";
    } else {
        div_result = element.parent().find('.autocomplete-result');
        div_result.empty();
    }

    $("#paciente_nome").keyup(function () {
        console.log(this.value);
        if (!this.value) {
            // div_result.empty();
            $('#celular').val('');
            $('#telefone').val('');
        }
    });

    if (search == '') $(input_id).val($(this).data().id).trigger('change');
    console.log(search)
    if (filter_col == "enc_esp") filter = $("#sol_encaminhante_id").val();
    $.get(
        "/saude-beta/autocomplete", {
        table: table,
        column: column,
        filter_col: filter_col,
        filter: filter,
        search: search
    },
        function (data) {
            console.log(data);
            var html = '';
            data = $.parseJSON(data);
            for (var i = 0; i < document.getElementsByClassName("autocomplete-result").length; i++) document.getElementsByClassName("autocomplete-result")[i].innerHTML = "";
            data.forEach(item => {
                html = '<div class="autocomplete-line" data-id="' + item.id + '">';
                html += item[column];
                if (table == 'pessoa' && filter_col != 'colaborador') {
                    if (item.data_nasc != undefined && item.data_nasc != '') {
                        html += ' | ' + moment().diff(item.data_nasc, 'years') + ' anos';
                    }
                    if (item.convenio) {
                        html += ' | ' + item.convenio;
                    }
                }
                html += '</div>';
                div_result.append(html);
            });
            $(input_id).val("");
            // try {
            //     if (data.length == 1) {
            //         el = element.parent().find(".autocomplete-line");
            //         $(input_id).val(el.data().id).trigger('change');
            //         element.val(el.html().toString().split('|')[0].trim());
            //         div_result.remove();
            //     }
            // } catch(err) {}
            element.parent().find(".autocomplete-line").each(function () {
                $(this).click(function () {
                    from_autocomplete = true;
                    $(input_id).val($(this).data().id).trigger('change');
                    element.val($(this).html().toString().split('|')[0].trim());
                    div_result.remove();
                });

                $(this).mouseover(function () {
                    //$(input_id).val($(this).data().id).trigger('change');
                    //element.val($(this).html().toString().split('|')[0].trim());
                    $(this).parent().find('.hovered').removeClass('hovered');
                    $(this).addClass('hovered');
                });
            });
            indice_autocomplete = -1;
            var lista = document.getElementsByClassName("autocomplete-line");
            var cont = 0;
            while (cont < lista.length) {
                lista[cont].id = "ac" + cont;
                cont++;
            }
            cont = 0;
            while (cont < lista.length) {
                lista[cont].addEventListener("mouseover", function () {
                    indice_autocomplete = parseInt(this.id.substring(2));
                });
                cont++;
            }
            total_autocomplete = cont;
        }
    );
}
function autocomplete_agenda(_this) {
    var table = _this.data().table,
        column = _this.data().column,
        input_id = _this.data().input,
        filter = _this.data().filter,
        filter_col = _this.data().filter_col,
        search = _this.val(),
        element = _this,
        div_result;

    $(document).click(function (e) {
        if (e.target.id != element.prop('id')) {
            div_result.remove();
        }
    });

    if (element.parent().find('.autocomplete-result').length == 0) {
        div_result = $('<div class="autocomplete-result">');
        element.after(div_result);
    } else {
        div_result = element.parent().find('.autocomplete-result');
        div_result.empty();
    }

    $("#paciente_nome").keyup(function () {
        console.log(this.value);
        if (!this.value) {
            // div_result.empty();
            $('#celular').val('');
            $('#telefone').val('');
        }
    });

    if (search == '') $(input_id).val($(this).data().id).trigger('change');
    div_result.empty();
    div_result.append('<img src="img/carregando-azul.gif" style="width:40px; height: 40px">');
    if (element.attr('id') == 'agenda_profissional') {
        $('.autocomplete-result').css('margin-top', '9.5%')
        $('.autocomplete-result').css('margin-left', '-0.6%')
        $('.autocomplete-result').css('display', 'flex')
        $('.autocomplete-result').css('justify-content', 'center')
    }
    $.get(
        "/saude-beta/autocompleteagenda", {
        table: table,
        column: column,
        filter_col: filter_col,
        filter: filter,
        search: search
    },
        function (data) {
            var html = '';
            data = $.parseJSON(data);
            for (var i = 0; i < document.getElementsByClassName("autocomplete-result").length; i++) document.getElementsByClassName("autocomplete-result")[i].innerHTML = "";
            contador = 0;
            data.profissionais.forEach(item => {
                html = '<div class="autocomplete-line" data-id="' + item.id + '" data-agendados="' + data.agendamentos[contador] + '">';
                html += '        <div style="margin-right: 10px;     display: inline-block;" class="user-photo-sm agenda-livre' + data.agendamentos[contador] + '"><img style="max-width:100%; max-height:100%;border-radius:100%;min-width:100%;min-height:100%;transform: rotate(315deg);object-fit: cover;"'
                html += ' src="/saude-beta/img/pessoa/' + item.id + '.jpg"';
                html += '            onerror="this.onerror=null;this.src=' + "'/saude-beta/img/paciente_default.png'" + '"></div>';
                html += '<span id="val">' + item[column] + '</span>';
                html += '</div>';
                $('.autocomplete-result').css('display', '')
                div_result.append(html);
                if (element.attr('id') == 'agenda_profissional') {
                    $('.autocomplete-result').css('margin-top', '9.5%')
                    $('.autocomplete-result').css('margin-left', '-0.6%')
                    $('.autocomplete-result').css('max-height', '420px')
                } contador++;
            });
            element.parent().find(".autocomplete-line").each(function () {
                $(this).click(function () {
                    $(input_id).val($(this).data().id).trigger('change');
                    if (element.attr('id') == 'agenda_profissional') {
                        $('#selecao-profissional').find('.selected').css('display', 'none')
                        $('#selecao-profissional').find('.selected').attr('class', document.querySelector('.selected').className.replace('selected', ''))
                        $('#selecao-profissional').find('[data-id_profissional=' + $(this).data().id + ']').attr('class', $('#selecao-profissional').find('[data-id_profissional=' + $(this).data().id + ']').attr('class') + ' selected')
                        $('#selecao-profissional').find('[data-id_profissional=' + $(this).data().id + ']').css('display', '')
                        mostrar_agendamentos()
                        mostrar_agendamentos_semanal();
                    }
                    element.val($(this).find('#val').html().toString().split('|')[0].trim());
                    div_result.remove();
                });

                $(this).mouseover(function () {
                    /*$(input_id).val($(this).data().id).trigger('change');
                    element.val($(this).find('#val').html().toString().split('|')[0].trim());*/
                    $(this).parent().find('.hovered').removeClass('hovered');
                    $(this).addClass('hovered');
                });
            });
            indice_autocomplete = -1;
            var lista = document.getElementsByClassName("autocomplete-line");
            var cont = 0;
            while (cont < lista.length) {
                lista[cont].id = "ac" + cont;
                cont++;
            }
            cont = 0;
            while (cont < lista.length) {
                lista[cont].addEventListener("mouseover", function () {
                    indice_autocomplete = parseInt(this.id.substring(2));
                });
                cont++;
            }
            total_autocomplete = cont;
        }
    );
}


function seta_autocomplete(evento, _this) {
    const direcao = evento.keyCode;
    try {
        if (document.getElementsByClassName("autocomplete-result")[0].innerHTML.length > 0 &&
            (direcao == 13 || direcao == 38 || direcao == 40)
        ) {
            evento.preventDefault();
            if (direcao == 38) {
                indice_autocomplete--;
                if (indice_autocomplete < 0) indice_autocomplete = total_autocomplete - 1;
            } else if (direcao == 40) {
                indice_autocomplete++;
                if (indice_autocomplete > total_autocomplete - 1) indice_autocomplete = 0;
            }
            var lista = document.getElementsByClassName("autocomplete-line");
            for (var i = 0; i < lista.length; i++) $(lista[i]).removeClass("hovered");
            $("#ac" + indice_autocomplete).addClass("hovered");
            if (direcao == 13) $("#ac" + indice_autocomplete).trigger("click");
        }
    } catch (err) { }
}

function redirect(url, bNew_Tab) {
    if (bNew_Tab) window.open(url, '_blank');
    else document.location.href = url;
}

function new_system_window(_url) {
    var _param = 'scrollbars=yes,toolbar=0,location=0,menubar=0,height=700,width=1000';
    window.open('http://vps.targetclient.com.br/saude-beta/' + _url, '_blank', _param)
}

function buscar_cid(_descr_cid) {
    window.open('https://www.cid10.com.br/buscadescr?query=' + _descr_cid, '_blank');
}

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

$("#filtro-grid input").on("keydown", function (e) {
    if (e.keyCode == 13) filtrar_grid();
});

function filtrar_grid() {
    var tabela = document.getElementById($("#filtro-grid").data().table.substring(1));
    $($("#filtro-grid").data().table + " tr").each(function() {
        var _filtro = $("#filtro-grid input").val();
        if ($(this).children()[1].innerHTML.toUpperCase().trim().indexOf(_filtro.toUpperCase()) == -1) $(this).css("display", "none");
        _filtro = _filtro.split(" ");
        for (var i = 0; i < _filtro.length; i++) {
            if ($(this).children()[1].innerHTML.toUpperCase().trim().indexOf(_filtro[i].toUpperCase()) > -1) $(this).css("display", "");
        }
    });
}

$("#filtro-grid-by0 input").on("keydown", function (e) {
    if (e.keyCode == 13) filtrar_grid_by0();
});

function filtrar_grid_by0() {
    var tabela = document.getElementById($("#filtro-grid-by0").data().table.substring(1));
    $($("#filtro-grid-by0").data().table + " tr").each(function() {
        var _filtro = $("#filtro-grid-by0 input").val();
        if ($(this).children()[0].innerHTML.toUpperCase().trim().indexOf(_filtro.toUpperCase()) == -1) $(this).css("display", "none");
        _filtro = _filtro.split(" ");
        for (var i = 0; i < _filtro.length; i++) {
            if ($(this).children()[0].innerHTML.toUpperCase().trim().indexOf(_filtro[i].toUpperCase()) > -1) $(this).css("display", "");
            if ($(this).children().length > 3 && $(this).children()[1].innerHTML.toUpperCase().trim().indexOf(_filtro[i].toUpperCase()) > -1) $(this).css("display", "");
        }
    });
}

$("#filtro-grid-procedimento input").on("keydown", function (e) {
    if (e.keyCode == 13) filtrar_grid_procedimento();
});

function filtrar_grid_procedimento() {
    var _filtro = $("#filtro-grid-procedimento input").val(),
        _filtro_esp = $("#filtro-especialidade").val();
    if (_filtro_esp == 0) {
        $($("#filtro-grid-procedimento").data().table + " tbody > tr:contains('" + _filtro + "')").show("fast");
        $($("#filtro-grid-procedimento").data().table + " tbody > tr:not(:contains('" + _filtro + "'))").hide("fast");
    } else {
        $($("#filtro-grid-procedimento").data().table + ' tbody > tr[data-id_especialidade="' + _filtro_esp + '"]:contains("' + _filtro + '")').show("fast");
        $($("#filtro-grid-procedimento").data().table + ' tbody > tr:not([data-id_especialidade="' + _filtro_esp + '"]:contains("' + _filtro + '"))').hide("fast");
    }
}

$("#filtro-grid-pedido input").on("keydown", function (e) {
    if (e.keyCode == 13) filtrar_grid_pedido();
});

function filtrar_grid_pedido() {
    var _filtro = $('#filtro-grid-pedido'),
        html = '';
    $.get(
        '/saude-beta/pedido/filtrar-pesquisa', {
        filtro: _filtro.find('input').val()
    }, function (data, status) {
        console.log(data, status)
        if (!data.error) {
            const cons = document.getElementById("consultando").value.trim() == "consulta";
            data = $.parseJSON(data);
            html += '<tbody>';
            $('#table-plano_tratamento').empty();
            data.forEach(contrato => {
                if (contrato.descr_convenio == null) contrato.descr_convenio = contrato.descr_forma_pag;
                html += ' <tr';
                if (cons) html += " onclick = 'openAgendamentoLote(" + contrato.id + ")'";
                html += '>';
                html += '        <td width="5%" class="text-center">' + contrato.id + '</td> ';
                html += '        <td width="15%"';
                if (!cons) html += ' onclick="verificar_cad_redirecionar(' + contrato.id_paciente + ')"';
                html += '>' + contrato.descr_paciente + '</td> ';
                var tam = cons ? 11 : 15;
                html += '        <td width="' + tam + '%">' + contrato.descr_prof_exa + '</td> ';
                tam = cons ? 12 : 10;
                html += '        <td width="' + tam + '%" class = "td_finan">' + contrato.descr_convenio + '</td> ';
                html += '        <td width="10%" class="text-right"';
                if (cons) html += ' style="padding-right:4%"';
                html += '>R$ ' + contrato.total.toFixed(2) + '</td> ';
                html += '        <td width="10%">' + formatDataBr(contrato.created_at) + '</td> ';
                html += '        <td width="10%">' + formatDataBr(contrato.data_validade) + '</td> ';
                tam = cons ? 12 : 14;
                html += '        <td width="' + tam + '%" style="font-size:0.75rem"> ';
                if (contrato.status == 'F' || contrato.status == '1') html += '<div class="tag-pedido-finalizado">Em Execução</div>';
                else if (contrato.status = 'C') {
                    html += '            <div class="tag-pedido-cancelado"> ';
                    html += '                    Cancelado ';
                    html += '            </div> ';
                }
                else if (contrato.status == 'E') html += '<div class="tag-pedido-aberto">Aprovação do Paciente</div>';
                else if (contrato.status == 'A') html += '<div class="tag-pedido-primary">Em Edição</div>';
                html += '        </td> ';
                if (!cons) {
                    html += '        <td width="15%" class="text-right btn-table-action"> ';
                    html += '            <img id="congelar-contrato" onclick="abrircongelarContrato(' + contrato.id + ')" src="http://vps.targetclient.com.br/saude-beta/img/proibido.png">  ';
                    html += '            <svg class="svg-inline--fa fa-file-times fa-w-12 my-icon" onclick="mudar_status_pedido(' + contrato.id + ',' + 'C' + ')" aria-hidden="true" focusable="false" data-prefix="far" data-icon="file-times" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" data-fa-i2svg=""><path fill="currentColor" d="M369.9 97.9L286 14C277 5 264.8-.1 252.1-.1H48C21.5 0 0 21.5 0 48v416c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V131.9c0-12.7-5.1-25-14.1-34zM332.1 128H256V51.9l76.1 76.1zM48 464V48h160v104c0 13.3 10.7 24 24 24h104v288H48zm231.7-89.3l-17 17c-4.7 4.7-12.3 4.7-17 0L192 337.9l-53.7 53.7c-4.7 4.7-12.3 4.7-17 0l-17-17c-4.7-4.7-4.7-12.3 0-17l53.7-53.7-53.7-53.7c-4.7-4.7-4.7-12.3 0-17l17-17c4.7-4.7 12.3-4.7 17 0L192 270l53.7-53.7c4.7-4.7 12.3-4.7 17 0l17 17c4.7 4.7 4.7 12.3 0 17L225.9 304l53.7 53.7c4.8 4.7 4.8 12.3.1 17z"></path></svg><!-- <i class="my-icon far fa-file-times" onclick="mudar_status_pedido(' + contrato.id + ', ' + 'C' + ')"></i> --> ';
                    html += '            <svg class="svg-inline--fa fa-print fa-w-16 my-icon" onclick="redirect(' + 'pedido/imprimir/' + contrato.id + '/1' + ', true)" aria-hidden="true" focusable="false" data-prefix="far" data-icon="print" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M400 264c-13.25 0-24 10.74-24 24 0 13.25 10.75 24 24 24s24-10.75 24-24c0-13.26-10.75-24-24-24zm32-88V99.88c0-12.73-5.06-24.94-14.06-33.94l-51.88-51.88c-9-9-21.21-14.06-33.94-14.06H110.48C93.64 0 80 14.33 80 32v144c-44.18 0-80 35.82-80 80v128c0 8.84 7.16 16 16 16h64v96c0 8.84 7.16 16 16 16h320c8.84 0 16-7.16 16-16v-96h64c8.84 0 16-7.16 16-16V256c0-44.18-35.82-80-80-80zM128 48h192v48c0 8.84 7.16 16 16 16h48v64H128V48zm256 416H128v-64h256v64zm80-112H48v-96c0-17.64 14.36-32 32-32h352c17.64 0 32 14.36 32 32v96z"></path></svg><!-- <i class="my-icon far fa-print" onclick="redirect(' + 'pedido/imprimir/' + contrato.id + '/' + contrato.sistema_antigo + ', true)"></i> --> ';
                    html += '            <svg class="svg-inline--fa fa-trash-alt fa-w-14 my-icon" onclick="deletar_pedido(' + contrato.id + ')" aria-hidden="true" focusable="false" data-prefix="far" data-icon="trash-alt" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg=""><path fill="currentColor" d="M268 416h24a12 12 0 0 0 12-12V188a12 12 0 0 0-12-12h-24a12 12 0 0 0-12 12v216a12 12 0 0 0 12 12zM432 80h-82.41l-34-56.7A48 48 0 0 0 274.41 0H173.59a48 48 0 0 0-41.16 23.3L98.41 80H16A16 16 0 0 0 0 96v16a16 16 0 0 0 16 16h16v336a48 48 0 0 0 48 48h288a48 48 0 0 0 48-48V128h16a16 16 0 0 0 16-16V96a16 16 0 0 0-16-16zM171.84 50.91A6 6 0 0 1 177 48h94a6 6 0 0 1 5.15 2.91L293.61 80H154.39zM368 464H80V128h288zm-212-48h24a12 12 0 0 0 12-12V188a12 12 0 0 0-12-12h-24a12 12 0 0 0-12 12v216a12 12 0 0 0 12 12z"></path></svg><!-- <i class="my-icon far fa-trash-alt" onclick="deletar_pedido(' + contrato.id + ')"></i> --> ';
                    html += '        </td> ';
                }
                html += '</tr> ';
            });
            $('#table-plano_tratamento').append(html);
            formataFinanceira();
        } else alert(data.error);
    });
}

window.addEventListener("load", function () {
    formataFinanceira();
});

function formataFinanceira() {
    var lista = document.getElementsByClassName("td_finan");
    for (var i = 0; i < lista.length; i++) {
        var financeiras = lista[i].innerHTML.split("@");
        var aux = new Array();
        for (var j = 0; j < financeiras.length; j++) {
            if (aux.indexOf(financeiras[j].trim()) == -1) aux[aux.length] = financeiras[j].trim();
        }
        lista[i].innerHTML = aux.join(",<br>");
    }
}

$("#filtro-grid-paciente input").on("keydown", function (e) {
    if (e.keyCode == 13) {
        filtrar_grid_paciente();
    }
});

function filtrar_grid_paciente() {
    $.get('/saude-beta/pessoa/listar-paciente', {
        filtro : $("#filtro-grid-paciente").find("input").val()
    }, function(data) {
        $($("#filtro-grid-paciente").data().table).find('tbody').empty();
        data = $.parseJSON(data);
        data.pessoas.forEach(pessoa => {
            html = '<tr>' +
                '<td width="5%">' +
                    '<img class="user-photo-sm" src="/saude-beta/img/pessoa/{{ $pessoa->id }}.jpg"' +
                        'onerror="this.onerror=null;this.src=' + "'/saude-beta/img/paciente_default.png'" + '">' +
                '</td>' +
                '<td width="65%" onclick="window.location.href =' + " '/saude-beta/pessoa/prontuario/" + pessoa.id + "'" + '"' + ">" +
                pessoa.nome_fantasia;
            if (pessoa.iec_atrasado == "S") {
                html += '<div style = "' +
                    'display:inline-block;' +
                    'margin-left:5px;' +
                    'width:10px;' +
                    'height:10px;' +
                    'background-color:#F00;' +
                    'border-color:#F00;' +
                    'border-radius:50%' +
                '" title = "IEC atrasado ou não existente"></div>';
            }
            html += '<td class="hide-mobile" width="10%">' + pessoa.cidade + '/' + pessoa.uf + '</td>';
            if (pessoa.associado == "N") {
                html += '<td class="text-center" width="13%"><div class="tag-pedido-cancelado" style="font-size:13px">' +
                    'Não Associado' +
                '</div></td>';
            } else html += '<td class="text-center" width="13%"><div class="tag-pedido-finalizado" style="font-size:13px">Associado</div></td>';
            html += '<td class="text-right btn-table-action hide-mobile">' +
                '<i class="my-icon far fa-edit" onclick="editar_pessoa(' + pessoa.id + ')"></i>';
            if (data.admin) html += '<i class="my-icon far fa-trash-alt" onclick="deletar_pessoa(' + pessoa.id + ')"></i>';
            html += '</td>';
            $($("#filtro-grid-paciente").data().table).find('tbody').append(html);
        });
    });
}

function filtrar_grid_paciente_old() {
    var _filtro = $("#filtro-grid-paciente"),
        html;
    $.get(
        '/saude-beta/pessoa/listar', {
        tipo_pessoa: 'paciente',
        tipo: 'S',
        apenas_pre_cadastro: $('#apenas_pre_cadastro').prop('checked'),
        filtro: _filtro.find('input').val()
    },
        function (data) {
            data = $.parseJSON(data);
            console.log(data);

            $(_filtro.data().table).find('tbody').empty();
            let cont = 0
            data.pessoas.forEach(pessoa => {
                html = '<tr>';
                html += '    <td width="5%">';
                html += '        <img class="user-photo-sm" src="/saude-beta/img/pessoa/' + pessoa.id + '.jpg"';
                html += '            onerror="this.onerror=null;this.src=' + "'/saude-beta/img/paciente_default.png'" + '">';
                html += '    </td>';
                // html += '    <td width="10%" class="text-right">';
                // if (data.mod_cod_interno) html += (pessoa.cod_interno ?? '');
                // else html += (pessoa.id ?? '');
                // html += '    </td>';
                html += '    <td width="55%"';
                // if (pessoa.paciente == 'S') {
                html += ' onclick="verificar_cad_redirecionar(' + pessoa.id + ')" ';
                // }
                html += ' >';
                html += pessoa.nome_fantasia.toUpperCase();
                html += '    </td>';
                if (pessoa.colaborador != 'N') {
                    html += '<td class="hide-mobile" width="10%">';
                    if (pessoa.descr_especialidade != null) html += pessoa.descr_especialidade;
                    else html += '—————';
                    html += '</td>';
                }
                else html += '<td class="hide-mobile" width="10%">—————</td>'
                // html += '    <td width="15%">' + pessoa.celular1 + '</td>';
                // html += '    <td width="20%">' + pessoa.email + '</td>';
                if (pessoa.paciente == 'S' || (pessoa.colaborador != 'P' && pessoa.colaborador != 'R' && pessoa.colaborador != 'A')) {
                    html += '<td class="hide-mobile" width="10%">' + pessoa.cidade + '/' + pessoa.uf + '</td>';
                } else {
                    html += '<td class="hide-mobile" width="10%">&nbsp;</td>';
                }


                if (data.asc[cont] == '0') {
                    html += ' <td class="text-center hide-mobile"  width="13%"><div class="tag-pedido-cancelado" style="font-size:13px">Não Associado</div></td> '
                }
                else html += ' <td class="text-center hide-mobile" width="13%"><div class="tag-pedido-finalizado" style="font-size:13px">Associado</div></td> '

                cont++;
                html += '    <td class="text-right btn-table-action hide-mobile">';
                if (pessoa.colaborador != 'N') {
                    html += '    <i class="my-icon far fa-calendar-alt"   title="Grades do Profissional" ';
                    if (pessoa.colaborador == 'P') {
                        html += ' onclick="abrir_grades_pessoa(' + pessoa.id + ')"';
                    } else {
                        html += ' style="cursor:default; opacity:0" ';
                    }
                    html += '></i>';
                    html += '    <i class="my-icon far fa-calendar-times"   title="Bloqueios de Grade" ';
                    if (pessoa.colaborador == 'P') {
                        html += ' onclick="bloquear_grades_pessoa(' + pessoa.id + ')"';
                    } else {
                        html += ' style="cursor:default; opacity:0" ';
                    }
                    html += '></i>';
                }
                html += '        <i class="my-icon far fa-edit"      onclick="editar_pessoa(' + pessoa.id + ')"></i>';
                if (data.admin) html += '        <i class="my-icon far fa-trash-alt" onclick="deletar_pessoa(' + pessoa.id + ')"></i>';
                html += '    </td>';
                html += '</tr>';
                $(_filtro.data().table).find('tbody').append(html);
            });
        }
    );
}
function criar_convenio() {
    $.post('/saude-beta/convenio/criar-convenio', {
        _token: $("meta[name=csrf-token]").attr('content'),
        descr: $("#convenioModal2 #descr2").val(),
        prazo: $("#convenioModal2 #prazo2").val(),
        quem_paga: $("#convenioModal2 #quem-paga").prop("checked"),
        cliente_id: $("#convenioModal2 #cliente_id").val()
    }, function (data, status) {
        console.log(data + ' | ' + status)
        if (data.error) {
            alert(data.error)
        }
        else {
            $("#convenioModal2").modal('hide')
            $("#convenioModal #id").val(data.id)
            $("#convenioModal #descr").val(data.descr)
            $("#convenioModal #prazo").val(data.prazo)
            if (data.quem_paga == 'E') {
                $("#convenioModal #quem-paga").prop("checked", true)
            }
            else {
                $("#convenioModal #quem-paga").prop("checked", false)
                $("#convenioModal #cliente_nome").val(data.nome_fantasia)
                $("#convenioModal #cliente_id").val(data.id_pessoa)
            }
            $("#convenioModal").modal('show')

        }
    })
}
function remover_preco_convenio($id) {
    $.post('/saude-beta/convenio/remover-preco-convenio', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: $id
    }, function (data, status) {
        console.log(data + ' | ' + status)
        if (data.error) alert(data.error)
        else {
            data = $.parseJSON(data);
            $("#convenioModal #tabela_precos").empty();
            data.forEach(plano => {
                html = ' <tr>'
                html += ' <td width="50%">' + plano.descr + '</td>'
                html += ' <td width="30%">' + plano.descr_empresa + '</td>'
                html += ' <td width="10%" class="text-right">' + plano.valor + '</td>'
                html += ' <td width="10%" onclick="remover_preco_convenio(' + plano.id + ')"><img style="    max-width: 25px;opacity: 0.9;" src="http://vps.targetclient.com.br/saude-beta/img/lixeira-de-reciclagem.png"></td>'
                html += ' </tr>'
                $("#convenioModal #tabela_precos").append(html)
            })
        }
    })
}
function add_plano_convenio() {
    $.post("/saude-beta/convenio/adicionar-valor-por-plano", {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_convenio: $("#convenioModal #id").val(),
        id_tabela_preco: $("#convenioModal #plano").val(),
        id_emp: $("#convenioModal #empresa").val(),
        valor: $("#convenioModal #valor").val()
    }, function (data, status) {
        console.log(data + ' | ' + status)
        data = $.parseJSON(data);
        $("#convenioModal #tabela_precos").empty();
        data.forEach(plano => {
            html = ' <tr>'
            html += ' <td width="50%">' + plano.descr + '</td>'
            html += ' <td width="30%">' + plano.descr_empresa + '</td>'
            html += ' <td width="10%" class="text-right">' + plano.valor + '</td>'
            html += ' <td width="10%" onclick="remover_preco_convenio(' + plano.id + ')"><img style="    max-width: 25px;opacity: 0.9;" src="http://vps.targetclient.com.br/saude-beta/img/lixeira-de-reciclagem.png"></td>'
            html += ' </tr>'
            $("#convenioModal #tabela_precos").append(html)
        })
    })
}

// function add_plano_convenio() {
//     if ($("#convenioModal #plano").val() == '' || $("#convenioModal #empresa").val() == '' ||
//         $("#convenioModal #valor").val() == '') alert('Para prosseguir, preencha os campos: Plano, Empresa e Valor')
//     html  = '<tr data-id_plano="' + $("#convenioModal #plano").val() + '"'
//     html += ' data-id_empresa="'+ $("#convenioModal #empresa").val() + '"'
//     html += ' data-valor="'+ $("#convenioModal #valor").val() + '">'
//     html += ' <td width="50%">'+ $("#convenioModal #plano").find(":selected").text()   +'</td>'
//     html += ' <td width="35%">'+ $("#convenioModal #empresa").find(":selected").text() +'</td>'
//     html += ' <td width="10%" class="text-right">R$ '+ $("#convenioModal #valor").val()   +'</td>'
//     html += ' <td width="5%"></td>'
//     html = html + $("#convenioModal #tabela_precos").html()
//     $("#convenioModal #tabela_precos").empty();
//     $("#convenioModal #tabela_precos").append(html)

//     $("#convenioModal #plano").val('')
//     $("#convenioModal #valor").val('')
// }
// function salvar_convenio() {
//     descricao = $("#convenioModal #descr")
//     prazo = $("#convenioModal #prazo")
//     ids_planos = []
//     ids_empresa = []
//     valores = []

// }
function verificar_cad_redirecionar(id_paciente) {
    $.get('/saude-beta/pessoa/verificar-pre-cadastro/' + id_paciente,
        function (data) {
            if (parseInt(data) == 0) {
                redirect('/saude-beta/pessoa/prontuario/' + id_paciente);
            } else {
                alert('Atenção!\nSó é possível abrir prontuários de pacientes com o cadastro devidamente preenchido.');
            }
        }
    );
}
function mostrar_agendamentos() {
    var html = '',
        hora_temp = ''
    if (detectar_mobile()) {
        function_mobile = ' onclick="abrirAgendaMobileModal($(this))" '
    };

    if ($('#selecao-profissional > .selected').length && $('.mini-calendar h6.selected').length) {
        $.get(
            '/saude-beta/agenda/listar-agendamentos', {
            id_profissional: $('#selecao-profissional > .selected').data().id_profissional,
            date_selected: $('.mini-calendar h6.selected').data().year + '-' +
                $('.mini-calendar h6.selected').data().month + '-' +
                $('.mini-calendar h6.selected').data().day
        },
            function (data) {
                data = $.parseJSON(data);
                var today,
                    nomes = data.profissional.nome.trim().split(' '),
                    date_selected = moment(
                        $('.mini-calendar h6.selected').data().year +
                        "-" +
                        pad($('.mini-calendar h6.selected').data().month, 2) +
                        "-" +
                        pad($('.mini-calendar h6.selected').data().day, 2)
                    );

                if ($('.mini-calendar h6.today').length) {
                    today = moment(
                        $('.mini-calendar h6.today').data().year +
                        "-" +
                        pad($('.mini-calendar h6.today').data().month, 2) +
                        "-" +
                        pad($('.mini-calendar h6.today').data().day, 2)
                    );
                } else {
                    today = null;
                }

                if ($('#selecao-profissional > .selected').data().nome_reduzido != 'null') {
                    $('#agenda_profissional').val($('#selecao-profissional > .selected').data().nome_reduzido);
                } else {
                    $('#agenda_profissional').val($('#selecao-profissional > .selected').data().nome_profissional);
                }

                $('#dia-selecionado').html(
                    get_dia_mes($('.mini-calendar h6.selected').data().month) +
                    " de " +
                    $('.mini-calendar h6.selected').data().year
                );

                html = '<div class="col-1"></div>';
                if (today != null && date_selected.isSame(today)) html += '<div class="col today">';
                else html += '<div class="col">';
                html += '    <p class="m-0 mt-1" style="font-size:12px">';
                html += get_dia_semana(date_selected.day() + 1).substring(0, 3) + '.';
                html += '    </p>';
                html += '    <p class="m-0 mb-1">';
                html += '       <span>' + pad($('.mini-calendar h6.selected').data().day, 2) + '</span>';
                html += '    </p>';
                html += '</div>';
                $('.agenda-diaria-header').html(html);
                $('.agenda-diaria-body').empty();

                if (data.grades.length > 0) {
                    data.grades.forEach(grade => {
                        if (grade.hora != hora_temp) {
                            html = '<div class="row m-0">';
                            html += '    <div id = "' + grade.id + '" class="col-1 btn-criar-agendamento p-0" data-id_grade_horario="' + grade.id + '" data-hora="' + grade.hora.substring(0, 5) + '">';
                            html += '       <div>' + grade.hora.substring(0, 5) + '</div>';
                            html += '    </div>';
                            html += '    <ul'
                            html += ' data-max_qtde_pacientes="' + grade.max_qtde_pacientes + '" class="col grade-existe" data-dia="' + date_selected.format('YYYY-MM-DD') + '"  data-id_grade_horario="' + grade.id + '"';
                            html += '        data-dia_semana="' + grade.dia_semana + '" data-horario="' + grade.hora + '" data-min_intervalo="' + grade.min_intervalo + '"';
                            html += '        style="background:' + grade_color(grade.cor) + '"></ul>';
                            html += '</div>';
                            $('.agenda-diaria-body').append(html);
                            hora_temp = grade.hora;
                        }
                    });
                } else {
                    $('.agenda-diaria-body').html('<h5 class="text-center p-5">Não há horários registrados para essa data e profissional.</h5>');
                }

                if (data.agendamentos.length > 0 || data.grade_bloqueios.length > 0) {
                    data.agendamentos.forEach(agendamento => {
                        if (agendamento.id != undefined && agendamento.id != null) {
                            html = '<li data-id_agendamento="' + agendamento.id + '"';
                            html += ' data-status="' + agendamento.id_status + '"';
                            html += ' data-status_s="' + agendamento.status + '"';
                            html += ' data-paciente="' + agendamento.nome_paciente + '"';
                            html += ' data-id_paciente="' + agendamento.id_paciente + '"';
                            html += ' data-procedimento="' + agendamento.descr_procedimento + '"';
                            html += ' data-convenio="' + agendamento.convenio_nome + '"';
                            html += ' data-permite_reagendar="' + agendamento.permite_reagendar + '"';
                            html += ' data-permite_fila_espera="' + agendamento.permite_fila_espera + '"';
                            html += ' data-permite_editar="' + agendamento.permite_editar + '"';
                            html += ' data-libera_horario="' + agendamento.libera_horario + '"';
                            html += ' data-caso_reagendar="' + agendamento.caso_reagendar + '" ';
                            html += ' data-modalidade="' + agendamento.descr_modalidade + '" ';
                            html += ' data-id_modalidade="' + agendamento.id_modalidade + '" ';
                            html += ' data-antigo="' + agendamento.sistema_antigo + '" ';
                            if (detectar_mobile()) {
                                html += ' onclick="abrirAgendaMobileModal($(this))" '
                            }
                            html += ' title="' + agendamento.descr_status + '"';
                            html += ' style="background:' + agendamento.cor_status + '; color: ' + agendamento.cor_letra + ';max-height:80px "important";';
                            // if ($('.agenda-diaria-body ul[data-horario="' + agendamento.hora + '"]').last().data().min_intervalo < agendamento.tempo && !agendamento.libera_horario) {
                            //     tamanho = agendamento.tempo / parseFloat($('.agenda-diaria-body ul[data-horario="' + agendamento.hora + '"]').last().parent().next().find('ul').data().min_intervalo);
                            //     html += 'position:absolute; width:100%; height:' + (34 * tamanho) + 'px;';
                            // }
                            html += '" >';

                            html += '    <div class="d-flex">';
                            html += '       <img class="foto-paciente-agenda my-auto" data-id_paciente="' + agendamento.id_paciente + '" src="/saude-beta/img/pessoa/' + agendamento.id_paciente + '.jpg" onerror="this.onerror=null;this.src=' + "'/saude-beta/img/paciente_default.png'" + '"'
                            if (!detectar_mobile()) {
                                html += ' onclick="verificar_cad_redirecionar(' + agendamento.id_paciente + "'" + ')"'
                            }
                            html += '>';
                            html += '       <div class="d-grid">';
                            html += '           <p class="col p-0 mt-auto">';
                            html += '               <span class="ml-0 my-auto" style="font-weight:600" '
                            if (!detectar_mobile()) {
                                'onclick="verificar_cad_redirecionar(' + agendamento.id_paciente + "'" + ')"';
                            }
                            html += '>'
                            html += agendamento.nome_paciente
                            html += '               </span>';
                            html += '           </p>';
                            /*html += '           <p class="tag-agenda mb-auto" style="font-weight:400">';
                            if (agendamento.retorno) html += 'Retorno: ';
                            if (agendamento.descr_procedimento != null) html += agendamento.descr_procedimento + ' | ';
                            if (agendamento.tipo_procedimento != null) html += agendamento.tipo_procedimento
                            html += '           </p>';*/
                            html += '           <p class="tag-agenda mb-auto" style="font-weight:400">Obs: ';
                            if (agendamento.obs.trim() != '' && agendamento.obs.trim() != null) html += agendamento.obs.trim();
                            else html += '...'
                            html += '           </p>';
                            html += '       </div>'
                            if (agendamento.espera != undefined && agendamento.permite_fila_espera) {
                                html += '   <span class="tag-em-espera encaixe my-auto mx-1" title="Em Espera">';
                                html += '      <small>Aguardando a ' + tempo_aguardando(agendamento.hora_chegada, false) + '</small>';
                                html += '   </span>';
                            }

                            html += '       <span class="tag-em-espera encaixe my-auto mx-1">';
                            html += '          <small>' + agendamento.descr_status + '</small>';
                            html += '       </span>';

                            html += '       <div class="tags">';
                            if (agendamento.id_confirmacao != null && agendamento.id_confirmacao != 0) {

                                switch (agendamento.id_confirmacao) {
                                    case 1:
                                        html += '   <span class="tag-confirmado mb-1" title="' + agendamento.descr_confirmacao + '">';
                                        html += '      <small class="m-auto">'
                                        html += 'P'
                                        break;
                                    case 2:
                                        html += '   <span style="background: red" class="tag-confirmado mb-1" title="' + agendamento.descr_confirmacao + '">';
                                        html += '      <small class="m-auto">'
                                        html += 'A'
                                        break;
                                    case 3:
                                        html += '   <span class="tag-confirmado mb-1" title="' + agendamento.descr_confirmacao + '">';
                                        html += '      <small class="m-auto">'
                                        html += 'F'
                                }

                                html += '</small>';
                                html += '   </span>';
                            }
                            html += '       </div>';

                            html += '   </div>';
                            html += '</li>';
                            $('.agenda-diaria-body ul[data-horario="' + agendamento.hora + '"]').last().append(html);

                            if (agendamento.libera_horario) {
                                $('.agenda-diaria-body .btn-criar-agendamento[data-hora="' + agendamento.hora.substring(0, 5) + '"]:last-of-type')
                                    .parent()
                                    .after(
                                        '<div class="row m-0">' +
                                        $('.agenda-diaria-body ul[data-horario="' + agendamento.hora + '"]:last-of-type').parent().html() +
                                        '</div>'
                                    );
                                $('.agenda-diaria-body ul[data-horario="' + agendamento.hora + '"]').last().empty();
                            }
                        }
                    });
                    $(".timing").mask("00:00");

                    if (data.grade_bloqueios.length > 0) {
                        var dia_temp, bloqueio_inicio, bloqueio_fim;
                        data.grade_bloqueios.forEach(bloqueio => {
                            $('.agenda-diaria-body ul[data-dia_semana="' + bloqueio.dia_semana + '"]').each(function () {
                                dia_temp = moment($(this).data().dia);
                                bloqueio_inicio = moment(bloqueio.data_inicial);
                                bloqueio_fim = moment(bloqueio.data_final);

                                if (verificar_horario($(this).data().horario, bloqueio.hora_inicial, bloqueio.hora_final) &&
                                    dia_temp.isSameOrAfter(bloqueio_inicio) && dia_temp.isSameOrBefore(bloqueio_fim)) {
                                    $(this).attr('title', 'Motivo: ' + bloqueio.obs)
                                        .append('<div id="grade-bloqueio-h" data-id_bloqueio="' + bloqueio.id + '" style="background-image: linear-gradient(45deg, #dfdfdf 4.55%, #fff 4.55%, #fff 50%, #dfdfdf 50%, #dfdfdf 54.55%, #fff 54.55%, #fff 100%);position:absolute;top:0px;width:120%;height:100%;background-size: 20px 20px !important;opacity: .5;margin-left: -15%"></div>')


                                }
                            });
                        });
                    }

                    if (data.pacientes.length > 0) {
                        data.pacientes.forEach(paciente => {
                            if (paciente.foto != null) {
                                $('.foto-paciente-agenda[data-id_paciente="' + paciente.id + '"]')
                                    .attr('src', 'data:image/jpg;base64,' + paciente.foto);
                            }
                        });
                    }
                }

                $('.btn-criar-agendamento').click(function () {
                    $('#id-grade-horario').val($(this).data().id_grade_horario);
                    $('#id-profissional').val($('#selecao-profissional > .selected').data().id_profissional);
                    $('#criarAgendamentoModal #data').val(pad($('.mini-calendar h6.selected').data().day, 2) + '/' +
                        pad($('.mini-calendar h6.selected').data().month, 2) + '/' +
                        $('.mini-calendar h6.selected').data().year);
                    $('#criarAgendamentoModal #hora').val($(this).data().hora.substring(0, 5));
                    criarModalAgendamento();
                });
                $.get('/saude-beta/pessoa/verificar-admin', {},
                    function (data, status) {
                        console.log(data + ' | ' + status)
                        if (data == 'true') {
                            $('.agenda-semanal-body ul[data-horario]:not(.horario-bloqueado):empty,' +
                                '.agenda-diaria-body ul[data-horario]:not(.horario-bloqueado):empty').dblclick(function () {
                                    if ($(this).html().trim() == '') {
                                        $('#id-profissional').val($('#selecao-profissional > .selected').data().id_profissional);
                                        $('#criarAgendamentoModal #data').val(moment($(this).data().dia).format('DD/MM/YYYY'));
                                        $('#criarAgendamentoModal #hora').val($(this).data().horario.substr(0, 5));
                                        criarModalAgendamento();
                                    }
                                });

                        }
                    })

                $('#agenda-diaria [data-dia][data-horario].grade-existe:empty').unbind('contextmenu rightclick').bind('contextmenu rightclick',
                    function (e) {
                        e.preventDefault();
                        var dia = $(this).data().dia,
                            hora = $(this).data().horario
                        context_menu_agenda(e, dia, hora)
                        return false;
                    });

                if (detectar_mobile()) {
                    $('#agenda-diaria [data-dia][data-horario].grade-existe:empty').click(
                        function (e) {
                            e.preventDefault();
                            var dia = $(this).data().dia,
                                hora = $(this).data().horario
                            context_menu_agenda(e, dia, hora)
                            return false;
                        });
                }

                // $('#agenda-diaria [data-id_agendamento]').dblclick(function () {
                //     if ($(this).data().permite_editar) {
                //         editar_agendamento($(this).data().id_agendamento, true);
                //     }
                // });

                // $('#agenda-semanal [data-id_agendamento]').dblclick(function () {
                //     if ($(this).data().permite_editar) {
                //         editar_agendamento($(this).data().id_agendamento, true);
                //     }
                // });

                $('#agenda-diaria [data-id_agendamento]').unbind('contextmenu rightclick').bind('contextmenu rightclick',
                    function (e) {
                        e.preventDefault();
                        context_menu_agendamento(e, $(this).data().max_qtde_pacientes, $(this).data().id_agendamento, $(this).data().permite_editar, $(this).data().permite_fila_espera, $(this).data().caso_reagendar, $(this).data().permite_reagendar, $(this), $(this).data().status_s, $(this).data().sistema_antigo, $(this).data().id_paciente);
                        return false;
                    });



                if (detectar_mobile()) {
                    $('#grade-bloqueio-h').unbind('contextmenu rightclick').bind('contextmenu rightclick',
                        function (e) {
                            e.preventDefault();
                            excluir_bloqueio_agenda(e, $(this).data().id_bloqueio);
                            return false;
                        });
                }

                if (detectar_mobile()) {
                    document.querySelector('.agenda-diaria-body.custom-scrollbar').addEventListener('scroll', () => {
                        $('#agenda-context-menu').hide()
                    })

                }
                mostrar_grades_cheias()
                if (detectar_mobile()) {
                    $('.btn-criar-agendamento').each(function () {
                        if ($(this).parent().find('li').length == 0) {
                            $(this).find('div').attr('style', 'font-weight: 100 !important')
                        }
                    })
                }
                $.get("/saude-beta/encaminhamento/especialidade/por-encaminhante", {
                    id : $('#selecao-profissional > .selected').data().id_profissional,
                    col : "id_pessoa"
                }, function(data) {
                    espEnc = $.parseJSON(data);
                });
            }
        );
        // mostrar_fila_espera();
    }
}

function excluir_bloqueio_agenda(e, $id) {
    e.preventDefault();
    ShowConfirmationBox(
        'Deseja desbloquear Horario?',
        '',
        true, true, false,
        function () { excluir_bloqueio($id) },
        function () { desistir_deletar_bloqueio($id); },
        'Sim',
        'Cancelar'
    );
}

function desistir_deletar_bloqueio() {
    mostrar_agendamentos_semanal()
    mostrar_agendamentos()
    $('#gradeBloqueioModal').modal('hide')
}
var modM = 0;
function mostrarModalidadesPorPlano($id) {
    $.get(
        '/saude-beta/agenda/listar-modalidades-por-plano/' + $id,
        function (data, status) {
            console.log(data + ' | ' + status)
            $("#criarAgendamentoModal #modalidade_id").empty()
            data.forEach(modalidade => {
                html = '<option value="' + modalidade.id + '">' + modalidade.descr + '</option>'
                $("#criarAgendamentoModal #modalidade_id").append(html)
            })
        }
    )
}
function salvarGradeBloqueio() {
    if ($("#gradeBloqueioModal #obs").val() != '' && $("#gradeBloqueioModal #obs").val() != null) {
        $.post(
            '/saude-beta/grade-bloqueio/salvar', {
            _token: $("meta[name=csrf-token]").attr("content"),
            id_profissional: $("#gradeBloqueioModal #id-profissional").val(),
            data_inicial: $("#gradeBloqueioModal #data-inicial").val(),
            data_final: $("#gradeBloqueioModal #data-final").val(),
            dia_semana: $("#gradeBloqueioModal #dia-semana").val(),
            hora_inicial: $("#gradeBloqueioModal #hora-inicial").val(),
            hora_final: $("#gradeBloqueioModal #hora-final").val(),
            obs: $("#gradeBloqueioModal #obs").val()
        }, function (data, status) {
            console.log(data + ' | ' + status)
            mostrar_agendamentos()
            mostrar_agendamentos_semanal()
            $("#gradeBloqueioModal").modal('hide')
            $("#agendaMobileModal").modal('hide')
        }
        )
    }
    else {
        alert('Campo de observação é obrigatório')
    }
}
var plsjdapoekdpalskd = '';
function mostrar_agendamentos_semanal(bset) {
    bset = bset || false;
    var html = '',
        hora_temp = '',
        _grade,
        i = 0;

    if ($('#selecao-profissional > .selected').length && $('.mini-calendar h6.selected').length) {
        $.get(
            '/saude-beta/agenda/listar-agendamentos-semanal', {
            id_profissional: $('#selecao-profissional > .selected').data().id_profissional,
            date_selected: $('.mini-calendar h6.selected').data().year + '-' +
                $('.mini-calendar h6.selected').data().month + '-' +
                $('.mini-calendar h6.selected').data().day
        },
            function (data) {
                if (plsjdapoekdpalskd === JSON.stringify(data)) return;
                plsjdapoekdpalskd = JSON.stringify(data);
                data = $.parseJSON(data);
                console.log(data);

                var today;
                if ($('.mini-calendar h6.today').length) {
                    today = moment(
                        $('.mini-calendar h6.today').data().year +
                        "-" +
                        pad($('.mini-calendar h6.today').data().month, 2) +
                        "-" +
                        pad($('.mini-calendar h6.today').data().day, 2)
                    );
                } else {
                    today = null;
                }

                console.log($('#selecao-profissional > .selected').data().nome_reduzido);
                if ($('#selecao-profissional > .selected').data().nome_reduzido != 'null') {
                    $('#profissional-selecionado').html($('#selecao-profissional > .selected').data().nome_reduzido);
                } else {
                    $('#profissional-selecionado').html($('#selecao-profissional > .selected').data().nome_profissional);
                }

                $('#dia-selecionado').html(
                    get_dia_mes($('.mini-calendar h6.selected').data().month) +
                    " de " +
                    $('.mini-calendar h6.selected').data().year
                );

                html = '<div class="p-0" style="width:80px"></div>';
                for (let i = 0; i < 7; i++) {
                    if (today != null && today.isSame(moment(data.begin_week.date).add(i, 'days'))) html += '<div class="col today p-0">';
                    else html += '<div class="col p-0">';
                    html += '    <p class="m-0 mt-1" style="font-size:12px">';
                    html += get_dia_semana(moment(data.begin_week.date).add(i, 'days').day() + 1).substring(0, 3) + '.';
                    html += '    </p>';
                    html += '    <p class="m-0 mb-1">';
                    html += '       <span ondblclick="direcionar_data_diaria(' + "'" + moment(data.begin_week.date).add(i, 'days').format('YYYY-MM-DD') + "'" + ')">';
                    html += pad(moment(data.begin_week.date).add(i, 'days').format('DD'), 2);
                    html += '       </span>';
                    html += '    </p>';
                    html += '</div>';
                }
                $('.agenda-semanal-header').html(html);
                $('.agenda-semanal-body').empty();

                if (data.grades.length > 0) {
                    data.grades.forEach(grade => {
                        if (grade.hora != hora_temp) {
                            html = '<div class="row m-0">';
                            html += '    <div class="btn-criar-agendamento p-0" data-horario="' + grade.hora + '">';
                            html += '       <div>' + grade.hora.substring(0, 5) + '</div>';
                            html += '    </div>';

                            for (let i = 1; i <= 7; i++) {
                                html += '<ul class="col" data-dia="' + moment(data.begin_week.date).add(i - 1, 'days').format('YYYY-MM-DD') + '" data-dia_semana="' + i + '" data-horario="' + grade.hora + '" data-max_qtde_pacientes="' + grade.max_qtde_pacientes + '"></ul>';
                            }
                            html += '</div>';
                            $('.agenda-semanal-body').append(html);
                            hora_temp = grade.hora;
                        }
                    });
                    data.grades.forEach(grade => {
                        _grade = $('.agenda-semanal-body ul[data-dia_semana="' + grade.dia_semana + '"][data-horario="' + grade.hora + '"]');
                        _grade.attr('data-id_grade_horario', grade.id);
                        _grade.attr('data-min_intervalo', grade.min_intervalo);
                        _grade.css('background', grade_color(grade.cor));
                        _grade.attr('title', grade.etiqueta_descr);
                        _grade.addClass('grade-existe');
                    });
                } else {
                    $('.agenda-semanal-body').html('<h5 class="text-center p-5">Não há horários registrados para essa data e profissional.</h5>');
                }

                if (data.agendamentos.length > 0 || data.grade_bloqueios.length > 0) {
                    $cont = 0;
                    data.agendamentos.forEach(agendamento => {
                        console.log('Agendamento Semanal:');
                        console.log(agendamento);
                        console.log('agendamento ' + agendamento.status)
                        if (agendamento.nome_paciente != null) {
                            if (agendamento.id != undefined) {
                                html = '<li data-id_agendamento="' + agendamento.id + '"';
                                html += ' ondblclick="abrir_prontuario(' + agendamento.id_paciente + ')"';
                                html += ' onclick="expandirAgendamento(' + agendamento.id + ',' + agendamento.sistema_antigo + ')"';
                                html += ' data-sistema_antigo="' + agendamento.sistema_antigo + '"';
                                html += ' data-status="' + agendamento.id_status + '"';
                                html += ' data-status_s="' + agendamento.status + '"';
                                html += ' data-paciente="' + agendamento.nome_paciente + '"';
                                html += ' data-id_paciente="' + agendamento.id_paciente + '"';
                                html += ' data-procedimento="' + agendamento.descr_procedimento + '"';
                                html += ' data-convenio="' + agendamento.convenio_nome + '"';
                                html += ' data-permite_fila_espera="' + agendamento.permite_fila_espera + '"';
                                html += ' data-permite_reagendar="' + agendamento.permite_reagendar + '"';
                                html += ' data-permite_editar="' + agendamento.permite_editar + '"';
                                html += ' data-libera_horario="' + agendamento.libera_horario + '"';
                                html += ' data-caso_reagendar="' + agendamento.caso_reagendar + '"';
                                html += ' title="' + agendamento.descr_status + '\n' + agendamento.obs + '"';
                                html += ' style="z-index: 2;background:' + agendamento.cor_status + '; color: ' + agendamento.cor_letra + ';';
                                html += '" >';
                                html += '       <p>';
                                html += '          <span>' + getFirstAndLastWords(agendamento.nome_paciente) + '</span>';

                                if (agendamento.espera != undefined && agendamento.permite_fila_espera) {
                                    html += '   <span class="tag-em-espera encaixe my-auto mx-1" title="Em Espera">';
                                    html += '      <small>' + tempo_aguardando(agendamento.hora_chegada, true) + '</small>';
                                    html += '   </span>';
                                }

                                html += '   <div class="tags">';
                                if (agendamento.id_confirmacao != null && agendamento.id_confirmacao != 0) {

                                    switch (agendamento.id_confirmacao) {
                                        case 1:
                                            html += '   <span class="tag-confirmado mb-1" title="Presença Confirmada">';
                                            html += '      <small class="m-auto">'
                                            html += 'P'
                                            break;
                                        case 2:
                                            html += '   <span style="background: red" class="tag-confirmado mb-1" title="Associado Ausente">';
                                            html += '      <small class="m-auto">'
                                            html += 'A'
                                            break;
                                        case 3:
                                            html += '   <span class="tag-confirmado mb-1" title="Finalizado">';
                                            html += '      <small class="m-auto">'
                                            html += 'F'
                                    }

                                    html += '</small>';
                                    html += '   </span>';
                                }
                                if (agendamento.encaixe) {
                                    html += '   <span class="tag-encaixe mb-1" title="Encaixe">';
                                    html += '      <small>E</small>';
                                    html += '   </span>';
                                }
                                html += '    </div>'

                                html += '</li>';
                                $('.agenda-semanal-body ul[data-dia_semana="' + agendamento.dia_semana + '"][data-horario="' + agendamento.hora + '"]').last().append(html);


                            }
                        }
                        $cont++;
                    });
                    $(".timing").mask("00:00");
                }
                if (data.grade_bloqueios.length > 0) {
                    var dia_temp, bloqueio_inicio, bloqueio_fim;
                    data.grade_bloqueios.forEach(bloqueio => {
                        $('.agenda-semanal-body ul[data-dia_semana="' + bloqueio.dia_semana + '"]').each(function () {
                            dia_temp = moment($(this).data().dia);
                            bloqueio_inicio = moment(bloqueio.data_inicial);
                            bloqueio_fim = moment(bloqueio.data_final);

                            if (verificar_horario($(this).data().horario, bloqueio.hora_inicial, bloqueio.hora_final) &&
                                dia_temp.isSameOrAfter(bloqueio_inicio) && dia_temp.isSameOrBefore(bloqueio_fim)) {
                                $(this).css('filter', 'brightness(0.5)');
                                $(this).append('<div title="' + bloqueio.obs + '"class="grade-agenda-cheia" style="background-image: linear-gradient(45deg, #dfdfdf 4.55%, #fff 4.55%, #fff 50%, #dfdfdf 50%, #dfdfdf 54.55%, #fff 54.55%, #fff 100%);position:absolute;top:0px;width:100%;height:100%;background-size: 20px 20px !important;opacity: .5;"></div>')
                            }
                        });
                    });
                }
                $('.btn-criar-agendamento').click(function () {
                    $('#id-grade-horario').val($(this).data().id_grade_horario);
                    $('#id-profissional').val($('#selecao-profissional > .selected').data().id_profissional);
                    $('#criarAgendamentoModal #data').val(pad($('.mini-calendar h6.selected').data().day, 2) + '/' +
                        pad($('.mini-calendar h6.selected').data().month, 2) + '/' +
                        $('.mini-calendar h6.selected').data().year);
                    $('#criarAgendamentoModal #hora').val($(this).data().horario.substring(0, 5));
                    criarModalAgendamento();
                });
                $.get('/saude-beta/pessoa/verificar-admin', {},
                    function (data, status) {
                        if (data == 'true') {
                            $('.agenda-semanal-body ul[data-horario]:not(.horario-bloqueado):empty,' +
                                '.agenda-diaria-body ul[data-horario]:not(.horario-bloqueado):empty').dblclick(function () {
                                    if ($(this).html().trim() == '') {
                                        $('#id-profissional').val($('#selecao-profissional > .selected').data().id_profissional);
                                        $('#criarAgendamentoModal #data').val(moment($(this).data().dia).format('DD/MM/YYYY'));
                                        $('#criarAgendamentoModal #hora').val($(this).data().horario.substr(0, 5));
                                        criarModalAgendamento();
                                    }
                                });
                        }
                    })

                $('#agenda-semanal [data-dia][data-horario].grade-existe').unbind('contextmenu rightclick').bind('contextmenu rightclick',
                    function (e) {
                        e.preventDefault();
                        var dia = $(this).data().dia,
                            hora = $(this).data().horario,
                            id = $(this).data().id_grade_horario
                        context_menu_agenda(e, dia, hora, id);

                        return false;
                    });

                $('#agenda-semanal [data-id_agendamento]').unbind('contextmenu rightclick').bind('contextmenu rightclick',
                    function (e) {
                        e.preventDefault();
                        context_menu_agendamento(e, $(this).data().max_qtde_pacientes, $(this).data().id_agendamento, $(this).data().permite_editar, $(this).data().permite_fila_espera, $(this).data().caso_reagendar, $(this).data().permite_reagendar, $(this), $(this).data().status_s);
                        return false;
                    });

                if (detectar_mobile()) {
                    $('#agenda-semanal [data-id_agendamento]').click(
                        function (e) {
                            e.preventDefault();
                            var dia = $(this).data().dia,
                                hora = $(this).data().horario
                            context_menu_agenda(e, dia, hora)
                            return false;
                        });
                }
                if (detectar_mobile()) {
                    document.querySelector('.agenda-diaria-body.custom-scrollbar').addEventListener('scroll', () => {
                        $('#agenda-context-menu').hide()
                    })

                }
                mostrar_grades_cheias()
                if (bset) {

                    mostrar_agendamentos_semanal()
                }
                if (detectar_mobile()) {
                    $('.btn-criar-agendamento').each(function () {
                        if ($(this).parent().find('li').length == 0) {
                            $(this).find('div').attr('style', 'font-weight: 100 !important')
                        }
                        else if ($(this).parent().find('li').length > 1) {
                            $(this).parent().find('li').each(function () {
                                $(this).attr('style', $(this).attr('style') + ';margin: 0px 0px 0px -50px !important; border-bottom: 1px solid #00000024 !important')
                            })
                            $($(this).parent().find('li')[$(this).parent().find('li').length - 1]).attr('style', $($(this).parent().find('li')[$(this).parent().find('li').length - 1]).attr('style') + ';margin: 0px 0px 10px -50px !important; border-bottom: 1px solid #00000024 !important')
                        }
                    })

                }
            }
        );
    }
}

function setar_profissional() {
    $.get('/saude-beta/pessoa/ver-usuario/', {},
        function (data, status) {
            console.log(data + ' | ' + status)
            if ((data.colaborador == 'A' || data.colaborador == 'P') && data.administrador == 'S') {
                $('#selecao-profissional').find('.selected').css('display', 'none')
                $('#selecao-profissional').find('.selected').attr('class', document.querySelector('.selected').className.replace('selected', ''))
                $('#selecao-profissional').find('[data-id_profissional=' + data.id + ']').attr('class', $('#selecao-profissional').find('[data-id_profissional=' + data.id + ']').attr('class') + ' selected')
                $('#selecao-profissional').find('[data-id_profissional=' + data.id + ']').css('display', '')
                $("#agenda_profissional").val(data.nome_fantasia)

                mostrar_agendamentos_semanal()
                mostrar_agendamentos()
            }
        })
}

function abrir_prontuario($id_profissional) {
    location.href = 'http://vps.targetclient.com.br/saude-beta/pessoa/prontuario/' + $id_profissional
}
function expandirAgendamento(id, sistema_antigo) {
    $.get(
        '/saude-beta/agenda/expandir-agendamento/' + id + '/' + sistema_antigo,
        (data, status) => {
            console.log(data + ' | ' + status)
            data = $.parseJSON(data)
            $data = data;
            if (data.error) {
                alert(data.error)
            }
            else {
                $('[data-id_agendamento="' + id + '"]').empty();
                teste = data
                html = '<p><span>' + getFirstAndLastWords(data.descr_pessoa) + '</span></p>'
                html += '<p><span>' + data.descr_tipo_procedimento + '</span></p>'

                if (data.id_tipo_procedimento == 1 || data.id_tipo_procedimento == '1') {
                    let datac = data.validade_contrato
                    datac = datac.substr(8, 2) + '/' + datac.substr(5, 2) + '/' + datac.substr(0, 4)
                    $('[data-id_agendamento="' + id + '"]').css('max-height', '140px')
                    $('[data-id_agendamento="' + id + '"]').css('height', '140px')
                    html += '<p><span>' + data.descr_procedimento + '</span></p>'
                    // html += '<p><span>' + data.descr_tabela_precos + '</span></p>'
                }
                else {
                    $('[data-id_agendamento="' + id + '"]').css('max-height', '140px')
                    $('[data-id_agendamento="' + id + '"]').css('height', '140px')
                    html += '<p><span style="text-transform: capitalize">' + data.descr_procedimento + '</span></p>'
                }
                $('[data-id_agendamento="' + id + '"]').append(html)

                window.addEventListener('click', function (e) {
                    if (!document.querySelector('[data-id_agendamento="' + id + '"]').contains(e.target)) {
                        $('[data-id_agendamento="' + id + '"]').empty();
                        $('[data-id_agendamento="' + id + '"]').append('<p><span>' + getFirstAndLastWords(data.descr_pessoa) + '</span></p>');
                        $('[data-id_agendamento="' + id + '"]').css('max-height', '45px')
                        $('[data-id_agendamento="' + id + '"]').css('height', '45px')
                    }
                });
            }
        }
    )
}


var teste
function mostrar_grades_cheias() {
    $("/saude-beta/pessoa/retornar-usuario", {},
        function (data) {
            if ((data.colaborador == 'R' || data.colaborador == 'A')) {
                lista = $(".agenda-semanal-body.custom-scrollbar").find('ul').filter('.grade-existe')
                for (i = 0; i <= lista.length - 1; i++) {
                    if (lista[i].dataset.max_qtde_pacientes == $(lista[i]).find('[data-status_s="A"]').length) {
                        // $(lista[i]).append('<img class="grade-existe" style="width: 35px;position: absolute;top: 1px;opacity: 0.5;left: 1px;" src="http://vps.targetclient.com.br/saude-beta/img/block.png">')
                        // lista[i].style.backgroundImage = 'linear-gradient(45deg, #dfdfdf 4.55%, #fff 4.55%, #fff 50%, #dfdfdf 50%, #dfdfdf 54.55%, #fff 54.55%, #fff 100%) !important;'
                        $(lista[i]).append('<div class="grade-agenda-cheia" style="background-image: linear-gradient(45deg, #dfdfdf 4.55%, #fff 4.55%, #fff 50%, #dfdfdf 50%, #dfdfdf 54.55%, #fff 54.55%, #fff 100%);position:absolute;top:0px;width:100%;height:100%;background-size: 20px 20px !important;opacity: .5;"></div>')
                    }

                    $(".grade-agenda-cheia").mouseover(function () {
                        $(this).parent().find('li').css('z-index', '2')
                    })
                    $(".grade-agenda-cheia").parent().mouseover(function () {
                        $(this).find('li').css('z-index', '2')
                    })
                    $(".grade-agenda-cheia").parent().mouseleave(function () {
                        $(this).find('li').css('z-index', '0')
                    })
                }
                lista2 = $(".agenda-diaria-body.custom-scrollbar").find('ul')
                for (i = 0; i <= lista2.length - 1; i++) {
                    if (lista2[i].dataset.max_qtde_pacientes == $(lista2[i]).find('[data-status_s="A"]').length) {
                        $(lista2[i]).append('<div class="grade-agenda-cheia" style="background-image: linear-gradient(45deg, #dfdfdf 4.55%, #fff 4.55%, #fff 50%, #dfdfdf 50%, #dfdfdf 54.55%, #fff 54.55%, #fff 100%);position:absolute;top:0px;width:100%;height:100%;background-size: 20px 20px !important;opacity: .5;"></div>')
                    }
                    $(".grade-agenda-cheia").mouseover(function () {
                        $(this).parent().find('li').css('z-index', '2')
                    })
                    $(".grade-agenda-cheia").parent().mouseover(function () {
                        $(this).find('li').css('z-index', '2')
                    })
                    $(".grade-agenda-cheia").parent().mouseleave(function () {
                        $(this).find('li').css('z-index', '0')
                    })
                }
            }
        })
}
function direcionar_data_diaria(data) {
    var mini_calendar = $('.mini-calendar');

    console.log(
        '[data-day="' + moment(data).format('DD') + '"]' +
        '[data-month="' + moment(data).format('MM') + '"]' +
        '[data-year="' + moment(data).format('YYYY') + '"]'
    );

    mini_calendar.find('.month-body h6.selected').removeClass('selected');
    mini_calendar.find(
        '[data-day="' + parseInt(moment(data).format('DD')) + '"]' +
        '[data-month="' + parseInt(moment(data).format('MM')) + '"]' +
        '[data-year="' + moment(data).format('YYYY') + '"]'
    ).addClass('selected');
    mostrar_agendamentos();
    mostrar_agendamentos_semanal();
    $('#mudar-visualizacao [data-visualizacao="#agenda-diaria"]').trigger('click');
}
function control_forma_pag() {
    console.log($("#pedidoModal #pedido_forma_pag"))
    if ($("#pedidoModal #pedido_forma_pag").val() == 11 || $("#pedidoModal #pedido_forma_pag").val() == 100) {
        $("#pedido_forma_pag_valor").val('0,00').prop('disabled', true)
    }
    else {
        $("#pedido_forma_pag_valor").val('').removeAttr('disabled')
    }
    control_forma_pag_pedido1()
}
function mudar_status(id_agendamento) {
    $.get('/saude-beta/agenda/agendamento-info/' + id_agendamento, function (data) {
        data = $.parseJSON(data);
        $('#mudarStatusModal #id_agendamento_status').val(id_agendamento);
        $('#mudarStatusModal #status_agendamento').val(data.id_status);
        $('#mudarStatusModal').modal('show');
        $('#btn-mudar-status').unbind('click').click(function () {
            $.post(
                '/saude-beta/agenda/mudar-status', {
                _token: $("meta[name=csrf-token]").attr("content"),
                id_agendamento: $('#mudarStatusModal #id_agendamento_status').val(),
                status: $('#mudarStatusModal #status_agendamento').val()
            },
                function (data, status) {
                    console.log(status + " | " + data);
                    if (data.error != undefined) {
                        alert(data.error);
                    } else {
                        data = $.parseJSON(data);
                        $('#mudarStatusModal').modal('hide');

                        mostrar_agendamentos();
                        mostrar_agendamentos_semanal();

                    }
                }
            );
        });
    });
}

function mudar_tipo_confirmação(id_agendamento) {
    $.get('/saude-beta/agenda/agendamento-info/' + id_agendamento, function (data) {
        data = $.parseJSON(data);
        $('#mudarTipoConfirmacaoModal').modal('show');
        $('#mudarTipoConfirmacaoModal #id_agendamento_confirmacao').val(id_agendamento);
        $('#mudarTipoConfirmacaoModal #tipo_confirmacao').val(data.id_confirmacao);
        $('#btn-mudar-tipo-confirmacao').click(function () {
            $.post(
                '/saude-beta/agenda/mudar-tipo-confirmacao', {
                _token: $("meta[name=csrf-token]").attr("content"),
                id_agendamento: $('#mudarTipoConfirmacaoModal #id_agendamento_confirmacao').val(),
                contato: $('#mudarTipoConfirmacaoModal #tipo_confirmacao').val()
            },
                function (data, status) {
                    console.log(status + " | " + data);
                    if (data.error != undefined) {
                        alert(data.error);
                    } else {
                        $('#mudarTipoConfirmacaoModal').modal('hide');
                        mostrar_agendamentos();
                        mostrar_agendamentos_semanal();
                    }
                }
            );
        });
    });
}

function grade_color(_color) {
    return (_color + '40');
}
function encontrarModalidadesPlano() {
    $.get('/saude-beta/tabela-precos/listar-modalidades/' + $("#criarAgendamentoModal #id"),
        function (data, status) {
            console.log(data + ' | ' + status)
            if (data.error) {
                alert(data.error)
            }
            else {
                data = $.parseJSON(data);
                $("#criarAgendamentoModal #modalidade_id").empty();
                data.forEach(plano => {
                    html = '<option value="' + plano.id + '">' + plano.descr + '</option>'
                    $("#criarAgendamentoModal #modalidade_id").append(html)
                })
            }
        })
}
var $data2




function editar_agendamento(id_agendamento, bEditar) {
    $.get('/saude-beta/agenda/editar_agendamento_/' + id_agendamento,
        function (data, status) {
            console.log(data + ' | ' + status)
            data = $.parseJSON(data);
            console.log(data + ' | ', status)
            $data2 = data;
            agendamento = data.agendamento;
            data = agendamento.data;
            data = data.substr(8, 2) + '/' + data.substr(5, 2) + '/' + data.substr(0, 4)
            $("#criarAgendamentoModal #id-grade-horario").val(agendamento.id_grade_horario)
            $("#criarAgendamentoModal #id-profissional").val(agendamento.id_profissional)
            $("#criarAgendamentoModal #id").val(agendamento.id)
            $("#criarAgendamentoModal #paciente_nome").val(agendamento.paciente_nome)
            $("#criarAgendamentoModal #paciente_id").val(agendamento.id_paciente)
            $("#criarAgendamentoModal #id_tipo_procedimento").val(agendamento.id_tipo_procedimento)
            encontrar_convenios_agendamento(() => {
                $("#criarAgendamentoModal #convenio_id").val(agendamento.id_convenio)
                $("#criarAgendamentoModal #data").val(data)
                $("#criarAgendamentoModal #hora").val(agendamento.hora.substr(0, 5))
                $("#criarAgendamentoModal #obs").val(agendamento.obs)
                encontrarContratos(!bEditar, () => {
                    $("#criarAgendamentoModal #id_contrato").val(agendamento.id_contrato)
                    encontrarPlanosPreAgendamento(() => {
                        encontrarPlanosContrato(() => {
                            $("#criarAgendamentoModal #id_plano").val(agendamento.id_plano)
                            $("#criarAgendamentoModal #procedimento_id").val(agendamento.id_plano_pre)
                            control_criar_agendamento()
                            setTimeout(() => {
                                if (bEditar) {
                                    document.querySelectorAll("#criarAgendamentoModal select").forEach(el => {
                                        $(el).removeAttr("disabled")
                                    })
                                    document.querySelectorAll("#criarAgendamentoModal input").forEach(el => {
                                        $(el).removeAttr("disabled")
                                    })
                                    $("#criarAgendamentoModal #enviar").show()
                                    $("#criarAgendamentoModal #confirmar").hide()
                                    $("#criarAgendamentoModal #bordero").hide()
                                }
                                else {
                                    document.querySelectorAll("#criarAgendamentoModal select").forEach(el => {
                                        $(el).attr('disabled', true)
                                    })
                                    document.querySelectorAll("#criarAgendamentoModal input").forEach(el => {
                                        $(el).attr('disabled', true)
                                    })
                                    $("#criarAgendamentoModal #enviar").hide()
                                    $("#criarAgendamentoModal #confirmar").show()
                                    $("#criarAgendamentoModal #bordero").show()
                                    $('#criarAgendamentoModal #bordero_b').removeAttr('disabled')
                                }

                                $("#agendamentosPendentesModal").modal("hide")
                                $("#criarAgendamentoModal #modalidade_id").val(agendamento.id_modalidade)
                                $("#criarAgendamentoModal").modal('show')

                                $("#enc_arquivo-btn").parent().css("display", "none");
                                $("#enc_label").css("display", "none");
                                $("#agenda_enc_esp").parent().removeClass("col-md-6");
                                $("#agenda_enc_esp").parent().addClass("col-md-12");
                                $("#agenda_enc_esp").parent().css("display", "block");
                                $("#agenda_sol").parent().css("display", "none");
                                document.getElementById("agenda_encaminhante_nome").parentElement.style = "display:block";
                                $.get('/saude-beta/encaminhamento/mostrar/' + agendamento.id_encaminhamento, function(data) {
                                    data = $.parseJSON(data);
                                    if (data.length) {
                                        console.log(data);
                                        data = data[0];
                                        $("#agenda_encaminhante_id").val(data.id_encaminhante);
                                        $("#agenda_encaminhante_nome").val(data.descr_encaminhante);
                                        try {
                                            $("#enc_cid_id").val(data.id_cid);
                                            $("#enc_cid_nome").val(data.descr_cid);
                                        } catch(err) {}
                                    }
                                });
                            }, 500)
                        })
                    })
                })
            })
        })
}

function formatDataBr(data) {
    return (data.substr(8, 2) + '/' + data.substr(5, 2) + '/' + data.substr(0, 4));
}

function encontrar_convenios_agendamento(callback) {

    if ($("#criarAgendamentoModal #paciente_id").val() != 0 && $("#criarAgendamentoModal #paciente_id").val() != '' && $("#criarAgendamentoModal #paciente_id").val() != undefined) {
        $.get("/saude-beta/pessoa/mostrar/" + $("#criarAgendamentoModal #paciente_id").val(), function (data) {
            data = $.parseJSON(data);
            $("#celular").val(data.celular1);
            if (data.telefone1 != 'NULL' && data.telefone1 != null) $("#telefone").val(data.telefone1);
            else $("#telefone").val('');
            if (data.email != 'NULL' && data.email != null) $("#email").val(data.email);
            else $("#email").val('');

            $('#criarAgendamentoModal #convenio_id').empty();
            $('#criarAgendamentoModal #convenio_id').append("<option value='0'>Sem convênio...</option>")
            data.convenio_pessoa.forEach(convenio => {
                $('#criarAgendamentoModal #convenio_id').append(
                    '<option value="' + convenio.id_convenio + '">' +
                    convenio.descr_convenio +
                    '</option>'
                );
            });
            callback()
        });
    } else {
        $.get('/saude-beta/convenio/listar',
            function (data) {
                data = $.parseJSON(data);
                $('#criarAgendamentoModal #convenio-id').empty();
                data.forEach(convenio => {
                    $('#criarAgendamentoModal #convenio-id').append(
                        '<option value="' + convenio.id + '">' +
                        convenio.descr +
                        '</option>'
                    );
                });
                callback()
            }
        );


    }
}
function detectar_mobile() {
    var check = false; //wrapper no check
    (function (a) { if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true })(navigator.userAgent || navigator.vendor || window.opera);
    return check;
}
function deletar_agendamento(id_agendamento) {
    if (window.confirm("Deseja realmente excluir esse agendamento?")) {
        $.get(
            '/saude-beta/agenda/deletar', {
            id: id_agendamento
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {
                    mostrar_agendamentos();
                    mostrar_agendamentos_semanal();
                    // mostrar_fila_espera();
                }
            }
        );
    }
}

function getFirstAndLastWords(text) {
    var text_arr = text.split(" ");
    if (text_arr.length == 1) return text_arr[0];
    else return text_arr[0] + " " + text_arr[text_arr.length - 1];
}

function verificar_horario(horario, hora_inicio, hora_fim) {
    var now = new Date();
    now.setHours(horario.substring(0, 2), horario.substring(3, 5), horario.substring(6, 8));
    var hour = now.getHours();
    var minutes = now.getMinutes();
    var timeOfDay = hour + (minutes / 100);

    hora_inicio = hora_inicio.substring(0, 5).replace(':', '.');
    hora_fim = hora_fim.substring(0, 5).replace(':', '.');

    return ((timeOfDay >= parseFloat(hora_inicio)) && (timeOfDay <= parseFloat(hora_fim)));
}

function get_dia_semana(dia_semana) {
    if (dia_semana == 1) return 'Domingo';
    else if (dia_semana == 2) return 'Segunda';
    else if (dia_semana == 3) return 'Terça';
    else if (dia_semana == 4) return 'Quarta';
    else if (dia_semana == 5) return 'Quinta';
    else if (dia_semana == 6) return 'Sexta';
    else if (dia_semana == 7) return 'Sábado';
}

function get_dia_mes(dia_mes) {
    if (dia_mes == 1) return 'Janeiro';
    else if (dia_mes == 2) return 'Fevereiro';
    else if (dia_mes == 3) return 'Março';
    else if (dia_mes == 4) return 'Abril';
    else if (dia_mes == 5) return 'Maio';
    else if (dia_mes == 6) return 'Junho';
    else if (dia_mes == 7) return 'Julho';
    else if (dia_mes == 8) return 'Agosto';
    else if (dia_mes == 9) return 'Setembro';
    else if (dia_mes == 10) return 'Outubro';
    else if (dia_mes == 11) return 'Novembro';
    else if (dia_mes == 12) return 'Dezembro';
}


var _id_agendamento_clipboard = 0;

function context_menu_agenda(e, _dia, _hora, id) {
    console.log(e);
    $.get(
        '/saude-beta/grade/verificar-grade-por-horario', {
        id_profissional: $('#selecao-profissional > .selected').data().id_profissional,
        dia: _dia,
        hora: _hora
    },
        function (data) {
            console.log(data);
            if (_id_agendamento_clipboard == 0) $('#agenda-context-menu > [data-function="colar_agendamento"]').hide();
            else $('#agenda-context-menu > [data-function="colar_agendamento"]').show();
            if (data == 0 || data == undefined || data == null) $('#agenda-context-menu > [data-function="excluir_divisao"]').hide();
            else $('#agenda-context-menu > [data-function="excluir_divisao"]').show();

            $.get(
                '/saude-beta/pessoa/verificar-admin-agenda2/', {},
                function (data, status) {
                    console.log(data + ' | ' + status)

                    if (data[2] == 'P') {
                        $('#agenda-context-menu > [data-function="novo_agendamento').hide()
                        $('#agenda-context-menu > [data-function="novo_agendamento_antigo"]').hide()
                        $('#agenda-context-menu').css('top', parseInt($('#agenda-context-menu').css('top')) + 25 + 'px')
                    }
                    else {
                        $('#agenda-context-menu > [data-function="novo_agendamento').show()
                        $('#agenda-context-menu > [data-function="novo_agendamento_antigo"]').show()
                    }

                    if (detectar_mobile()) {
                        $('#agenda-context-menu > [data-function="novo_agendamento').hide()
                        $('#agenda-context-menu > [data-function="novo_agendamento_antigo"]').hide()
                    }
                }
            )


            $('#agendamento-context-menu').hide();
            // $('#agenda-context-menu').css({ 'top': e.pageY, 'left': (e.pageX - 0) });
            if (detectar_mobile()) $('#agenda-context-menu').css({ 'top': e.pageY - 62, 'left': '75px' });
            else $('#agenda-context-menu').css({ 'top': e.pageY - 62, 'left': (e.pageX - 0) });
            $('#agenda-context-menu').show();
            $('#agenda-context-menu > li').unbind('click').click(function () {
                switch ($(this).data().function) {
                    case 'novo_agendamento':
                        $('#criarAgendamentoModal #data').val(moment(_dia).format('DD/MM/YYYY'));
                        $('#criarAgendamentoModal #hora').val(_hora.substring(0, 5));
                        $('#criarAgendamentoModal #id_tipo_procedimento').val('')
                        $('#criarAgendamentoModal #id_contrato').val('')
                        $('#criarAgendamentoModal #id_plano').val('')

                        criarModalAgendamento();
                        break;
                    case 'novo_agendamento_antigo':
                        $('#criarAgendamentoAntigoModal #data').val(moment(_dia).format('DD/MM/YYYY'));
                        $('#criarAgendamentoAntigoModal #hora').val(_hora.substring(0, 5));
                        $('#criarAgendamentoAntigoModal #id_tipo_procedimento').val('')
                        $('#criarAgendamentoAntigoModal #id_contrato').val('')
                        $('#criarAgendamentoAntigoModal #id_plano').val('')

                        criarModalAgendamentoAntigo();
                        break;
                    case 'colar_agendamento':
                        if (agendamento_antigo = 0) colar_agendamento_por_data(_id_agendamento_clipboard, _dia, _hora);
                        else colar_agendamento_por_data;
                        break;
                    case 'dividir_horario':
                        dividir_horario_por_data(_dia, _hora);
                        break;
                    case 'excluir_divisao':
                        excluir_divisao(data);
                        break;
                    case 'bloquear_desbloquear_grade':
                        bloquear_desbloquear_grade(_dia, _hora, id)

                        break;
                    default:
                        $('#agenda-context-menu').hide();
                }
                $('#agenda-context-menu').hide();
            });

            $(document).click(function (e) {
                if (e.target.id != '#agenda-context-menu') {
                    $('#agenda-context-menu').hide();
                }
            });
        }
    );
}
function bloquear_desbloquear_grade(_dia, _hora, id) {
    var today = moment(),
        data_inicial, data_final;
    console.log(_dia)
    console.log(_hora)
    console.log(id)
    $.get(
        '/saude-beta/grade/mostrar-grade-por-horario', {
        id_grade: id,
        dia: _dia,
        hora: _hora
    }, function (data, status) {
        console.log(data + ' | ' + status)
        data = $.parseJSON(data);
        $('#gradeBloqueioModal #id-profissional').val($('#selecao-profissional > .selected').data().id_profissional)
        $("#gradeBloqueioModal #dia-semana").val(data.dia_semana)
        $('#gradeBloqueioModal #data-inicial').val(moment(_dia).format('DD/MM/YYYY'))
        $('#gradeBloqueioModal #data-final').val(moment(_dia).format('DD/MM/YYYY'))
        $("#gradeBloqueioModal #hora-inicial").val(_hora.substring(0, 5));
        $("#gradeBloqueioModal #hora-final").val(_hora.substring(0, 5));
        $('#gradeBloqueioModal').modal('show');
        $.get('/saude-beta/grade-bloqueio/mostrar-pessoa/' + $(".selected").data().id_profissional, function (data) {
            data = $.parseJSON(data);
            $('#gradeBloqueioModal #id-profissional').val(data.profissional.id)
            $('#lista-grade-bloqueio').empty();
            data.bloqueios.forEach(bloqueio => {
                html = '<div class="card row" title="Click para ver as observações." style="flex-direction:row; cursor:pointer">';
                html += '    <div class="col-4 d-flex" data-toggle="collapse" data-target="#collapse' + bloqueio.id + '" aria-expanded="false" aria-controls="collapse' + bloqueio.id + '"> ';
                data_inicial = moment(bloqueio.data_inicial);
                data_final = moment(bloqueio.data_final);
                if (bloqueio.ativo && today.diff(data_final, 'days') <= 0) {
                    html += '<span class="semaforo color-validade-verde my-auto mr-3" data-message="Ativo (' + data_inicial.format('DD/MM/YYYY') + ' — ' + data_final.format('DD/MM/YYYY') + ')"></span>';
                } else if (today.diff(data_final, 'days') >= 0) {
                    html += '<span class="semaforo color-validade-laranja my-auto mr-3" data-message="Expirado no ' + data_final.format('DD/MM/YYYY') + '"></span>';
                } else {
                    html += '<span class="semaforo color-validade-vermelho my-auto mr-3" data-message="Inativo"></span>';
                }
                html += '        <h5 class="my-2 text-dark">';
                html += semana_descr(bloqueio.dia_semana);
                html += '        </h5>';
                html += '    </div>';
                html += '    <div class="col-8 d-grid">';
                html += '        <div class="ml-auto btn-table-action text-dark d-flex">';
                html += '            <h5 class="my-2 mr-3 text-dark">';
                html += data_inicial.format('DD/MM/YYYY') + ' até ' + data_final.format('DD/MM/YYYY') + '    -    '
                html += bloqueio.hora_inicial.substring(0, 5) + ' — ' + bloqueio.hora_final.substring(0, 5);
                html += '            </h5>';
                if (bloqueio.ativo) html += '<i class="my-icon my-auto fas fa-window-close" title="Desativar Grade" onclick="ativacao_bloqueio(' + bloqueio.id + ', false)"></i>';
                else html += '<i class="my-icon my-auto fas fa-check-square" title="Ativar Grade" onclick="ativacao_bloqueio(' + bloqueio.id + ', true)"></i>';
                html += '            <i class="my-icon my-auto fas fa-trash" title="Excluir" onclick="excluir_bloqueio(' + bloqueio.id + ')"></i>';
                html += '        </div>';
                html += '    </div>';

                html += '    <div id="collapse' + bloqueio.id + '" class="collapse" aria-labelledby="heading' + bloqueio.id + '" data-parent="#lista-grade-bloqueio">';
                html += '        <div class="card-body">';
                html += '            <b>OBS.:</b> ' + bloqueio.obs;
                html += '        </div>';
                html += '    </div>';
                html += '</div>';
                $('#lista-grade-bloqueio').append(html);
            });
            $('#gradeBloqueioModal').modal('show');
        });
    })
}
// function salvar_grade_bloqueio() {

//     id-profissional
//     data-inicial
//     data-final
//     dia-semana
//     hora-inicial
//     hora-final
//     obs
// }
function antigo_colar_agendamento_por_data(_id_agendamento_clipboard, _dia, _hora) {
    console.log(id_agendamento);
    console.log(_dia);
    console.log(_hora);
    $.post(
        '/saude-beta/agenda/copiar-agendamento', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_agendamento: id_agendamento,
        dia: _dia,
        hora: _hora
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                _id_agendamento_clipboard = 0;
                mostrar_agendamentos();
                mostrar_agendamentos_semanal();
                // mostrar_fila_espera();
            }
        }
    );
}
function excluir_divisao(id_grade) {
    $.post(
        '/saude-beta/grade/deletar', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: id_grade
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                mostrar_agendamentos();
                mostrar_agendamentos_semanal();
                // mostrar_fila_espera();
            }
        }
    );
}
function context_menu_agendamento(e, _max_qtde_pacientes, id_agendamento, _permissao_editar, _permissao_fila, _caso_reagendar, _permissao_reagendar, data, _status) {
    if (detectar_mobile()) return
    console.log(e);
    sistema_antigo = ($(data).data().sistema_antigo)
    id_paciente = ($(data).data().id_paciente)
    elemento = $('.agenda-semanal-body').find('[data-id_agendamento="' + id_agendamento + '"]')
    if (elemento.parent().data().max_qtde_pacientes > elemento.parent().find('[data-status_s="A"]').length ||
        elemento.parent().data().max_qtde_pacientes == null || elemento.parent().data().max_qtde_pacientes == 'null') {
        $('#agendamento-context-menu > [data-function="adicionar_agendamento"]').show();
    } else $('#agendamento-context-menu > [data-function="adicionar_agendamento"]').hide();

    if (_id_agendamento_clipboard == 0) $('#agendamento-context-menu > [data-function="colar_agendamento"]').hide();
    else $('#agendamento-context-menu > [data-function="colar_agendamento"]').show();

    if (_permissao_editar) $('#agendamento-context-menu > [data-function="editar_agendamento"]').show();
    else $('#agendamento-context-menu > [data-function="editar_agendamento"]').hide();

    if (_permissao_fila) $('#agendamento-context-menu > [data-function="add_fila_espera"]').show();
    else $('#agendamento-context-menu > [data-function="add_fila_espera"]').hide();

    if (!_permissao_reagendar || _caso_reagendar) $('#agendamento-context-menu > [data-function="repetir_agendamento"]').hide();
    else $('#agendamento-context-menu > [data-function="repetir_agendamento"]').show();

    if (_status == 'C' || _status == 'F') $('#agendamento-context-menu > [data-function="confirmar_agendamento"]').hide();
    else $('#agendamento-context-menu > [data-function="confirmar_agendamento"]').show();

    $.get(
        '/saude-beta/pessoa/verificar-admin-agenda/' + id_agendamento,
        function (data, status) {
            console.log(data + ' | ' + status)
            if (data[1] == 'S' && data[0] == 'N' && _status != 'C' || _status != 'F') {
                $('#agendamento-context-menu > [data-function="confirmar_agendamento"]').show();
            }
            else $('#agendamento-context-menu > [data-function="confirmar_agendamento"]').hide();
            if (data[2] == 'P' && data[1] != 'S') {
                $('#agendamento-context-menu > [data-function="colar_agendamento"]').hide();
                $('#agendamento-context-menu > [data-function="colar_agendamento"').hide();
                $('#agendamento-context-menu > [data-function="editar_agendamento"]').hide();
                $('#agendamento-context-menu > [data-function="add_fila_espera"]').hide();
                $('#agendamento-context-menu > [data-function="repetir_agendamento"]').hide();
                $('#agendamento-context-menu > [data-function="confirmar_agendamento"]').hide();
                $('#agendamento-context-menu > [data-function="adicionar_agendamento"]').hide();
                $('#agendamento-context-menu > [data-function="adicionar_agendamento_antigo"]').hide();
                $('#agendamento-context-menu > [data-function="mudar_status"]').hide();
                $('#agendamento-context-menu > [data-function="copiar_agendamento"]').hide();
                $('#agendamento-context-menu > [data-function="repetir_agendamento"]').hide();
                $('#agendamento-context-menu > [data-function="dividir_horario"]').hide();
                $('#agendamento-context-menu > [data-function="deletar_agendamento"]').hide();
                $('#agendamento-context-menu > [data-function="ver_historico_agenda"]').hide();
                $('#agendamento-context-menu > [data-function="ver_historico_confirmacao"]').hide();
                $('#agendamento-context-menu > [data-function="cancelar-agendamento"]').hide();
                document.querySelector('#agendamento-context-menu').style.top = (parseInt(document.querySelector('#agendamento-context-menu').style.top.replace('px', '')) + 240) + 'px'
            }

        }
    )

    $('#agenda-context-menu').hide();
    $('#agendamento-context-menu').css({ 'top': e.pageY - 265, 'left': (e.pageX - 0) });
    setTimeout(() => { $('#agendamento-context-menu').show(); }, 100)
    $('#agendamento-context-menu > li').unbind('click').click(function () {
        switch ($(this).data().function) {
            case 'add_fila_espera':
                if (sistema_antigo == 0) add_fila_espera(id_agendamento);
                else antigo_add_fila_espera(id_agendamento);
                break;
            case 'editar_agendamento':
                if (sistema_antigo == 0) editar_agendamento(id_agendamento, true);
                else antigo_editar_agendamento(id_agendamento, true);
                break;
            case 'deletar_agendamento':
                if (sistema_antigo == 0) deletar_agendamento(id_agendamento);
                else antigo_deletar_agendamento(id_agendamento);
                break;
            case 'confirmado_via':
                if (sistema_antigo == 0) mudar_tipo_confirmação(id_agendamento);
                else antigo_mudar_tipo_confirmação(id_agendamento);
                break;
            case 'mudar_status':
                if (sistema_antigo == 0) mudar_status(id_agendamento);
                else antigo_mudar_status(id_agendamento);
                break;
            case 'repetir_agendamento':
                if (sistema_antigo == 0) repetir_agendamento(id_agendamento);
                else antigo_repetir_agendamento(id_agendamento);
                break;
            case 'copiar_agendamento':
                _id_agendamento_clipboard = id_agendamento;
                break;
            case 'colar_agendamento':
                if (sistema_antigo == 0) colar_agendamento_por_id(_id_agendamento_clipboard, id_agendamento);
                else antigo_colar_agendamento_por_id(_id_agendamento_clipboard, id_agendamento);
                break;
            case 'dividir_horario':
                if (sistema_antigo == 0) dividir_horario_por_id(id_agendamento);
                else antigo_dividir_horario_por_id(id_agendamento);
                break;
            case 'ver_historico_agenda':
                if (sistema_antigo == 0) ver_historico_agenda(id_agendamento, 'agenda_status');
                else antigo_ver_historico_agenda(id_agendamento, 'agenda_status');
                break;
            case 'abrir_prontuario':
                abrir_prontuario(id_paciente);
                break;
            case 'ver_historico_confirmacao':
                if (sistema_antigo == 0) ver_historico_agenda(id_agendamento, 'agenda_confirmacao');
                else antigo_ver_historico_agenda(id_agendamento, 'agenda_confirmacao');
                break;
            case 'confirmar_agendamento':
                if (sistema_antigo == 0) editar_agendamento(id_agendamento, false);
                else antigo_editar_agendamento(id_agendamento, false);
                break
            case 'cancelar-agendamento':
                if (sistema_antigo == 0) cancelar_agendamento(id_agendamento)
                else antigo_cancelar_agendamento(id_agendamento)
                break;
            case 'adicionar_agendamento':
                adicionar_agendamento(id_agendamento);
                break;
            case 'adicionar_agendamento_antigo':
                antigo_adicionar_agendamento(id_agendamento)
                break;
            case 'bloquear_desbloquear_grade':
                $("#gradeBloqueioModal").modal('show')
                break;
            default:
                $('#agendamento-context-menu').hide();
        }
        $('#agendamento-context-menu').hide();
    });

    $(document).click(function (e) {
        if (e.target.id != '#agendamento-context-menu') {
            $('#agendamento-context-menu').hide();
        }
    });
}
function redirecionar(op) {
    if ((!detectar_mobile()) && op == 'cockpit') {
        redirect('/saude-beta/cockpit')
    }
}
function antigo_editar_agendamento(id_agendamento, bEditar) {
    $.get('/saude-beta/agenda-antiga/editar_agendamento_/' + id_agendamento,
        function (data, status) {
            console.log(data + ' | ' + status)
            data = $.parseJSON(data);
            console.log(data + ' | ', status)
            $data2 = data;
            agendamento = data.agendamento;
            $('#criarAgendamentoAntigoModal #id').val(agendamento.id);
            $('#criarAgendamentoAntigoModal #id-grade-horario').val(agendamento.id_grade_horario);
            $('#criarAgendamentoAntigoModal #id-profissional').val($('#selecao-profissional > .selected').data().id_profissional);
            $('#criarAgendamentoAntigoModal #data').val(agendamento.data.split('-')[2] + '/' + agendamento.data.split('-')[1] + '/' + agendamento.data.split('-')[0]);
            $('#criarAgendamentoAntigoModal #hora').val(agendamento.hora.split(':')[0] + ':' + agendamento.hora.split(':')[1]);
            $('#criarAgendamentoAntigoModal #paciente_id').val(agendamento.id_paciente);
            $('#criarAgendamentoAntigoModal #id-agenda-status').val(agendamento.id_status);
            $("#criarAgendamentoAntigoModal #id_modalidade")
            $('#criarAgendamentoAntigoModal #paciente_nome').val(agendamento.paciente_nome);
            $('#criarAgendamentoAntigoModal #id_tipo_procedimento').val(agendamento.id_tipo_procedimento);
            $('#criarAgendamentoAntigoModal #obs').val(agendamento.obs);
            $('#criarAgendamentoAntigoModal #criarAgendamentoAntigoModalLabel').html("Editar Agendamento");
            $('#criarAgendamentoAntigoModal #id_tipo_procedimento').css('display', 'block')
            $("#criarAgendamentoAntigoModal #paciente_id").change()
            setTimeout(() => {
                $("#criarAgendamentoAntigoModal #modalidade_id").append('<option value="' + data.atv.id + '">' + data.atv.descr + ' (' + data.atv.atv_rest + '/' + data.atv.total + ') </option>')
                $("#criarAgendamentoAntigoModal #modalidade_id").val(agendamento.id_atividade)
            }, 500)
            if (bEditar) {
                $('#criarAgendamentoAntigoModal #id_tipo_procedimento').attr('disabled', 'true')
                $("#criarAgendamentoAntigoModal #bordero").css('display', 'none')
                $('#criarAgendamentoAntigoModal #paciente_nome').removeAttr('disabled')
                $('#criarAgendamentoAntigoModal #celular').removeAttr('disabled')
                $('#criarAgendamentoAntigoModal #telefone').removeAttr('disabled')
                $('#criarAgendamentoAntigoModal #email').removeAttr('disabled')
                $('#criarAgendamentoAntigoModal #procedimento_id').removeAttr('disabled')
                $('#criarAgendamentoAntigoModal #id_contrato').removeAttr('disabled')
                $('#criarAgendamentoAntigoModal #id_plano').removeAttr('disabled')
                $('#criarAgendamentoAntigoModal #convenio_id').removeAttr('disabled')
                $('#criarAgendamentoAntigoModal #id-sala').removeAttr('disabled')
                $('#criarAgendamentoAntigoModal #tempo-procedimento').removeAttr('disabled')
                $('#criarAgendamentoAntigoModal #valor').removeAttr('disabled')
                $('#criarAgendamentoAntigoModal #data').removeAttr('disabled')
                $('#criarAgendamentoAntigoModal #hora').removeAttr('disabled')
                $('#criarAgendamentoAntigoModal #id-agenda-status').removeAttr('disabled')
                $('#criarAgendamentoAntigoModal #obs').removeAttr('disabled')
                $('#criarAgendamentoAntigoModal #modalidade_id').removeAttr('disabled')
                document.querySelector("#criarAgendamentoAntigoModal #enviar").style.display = 'inline'
                document.querySelector('#criarAgendamentoAntigoModal #confirmar').style.display = 'none'
            }
            else {
                $("#criarAgendamentoAntigoModal #bordero").css('display', 'block')
                $('#criarAgendamentoModal #bordero_b').removeAttr('disabled')
                $('#criarAgendamentoAntigoModal #paciente_nome').attr('disabled', 'true')
                $('#criarAgendamentoAntigoModal #celular').attr('disabled', 'true')
                $('#criarAgendamentoAntigoModal #telefone').attr('disabled', 'true')
                $('#criarAgendamentoAntigoModal #email').attr('disabled', 'true')
                $('#criarAgendamentoAntigoModal #id_tipo_procedimento').attr('disabled', 'true')
                $('#criarAgendamentoAntigoModal #procedimento_id').attr('disabled', 'true')
                $('#criarAgendamentoAntigoModal #id_contrato').attr('disabled', 'true')
                $('#criarAgendamentoAntigoModal #id_plano').attr('disabled', 'true')
                $('#criarAgendamentoAntigoModal #convenio_id').attr('disabled', 'true')
                $('#criarAgendamentoAntigoModal #id-sala').attr('disabled', 'true')
                $('#criarAgendamentoAntigoModal #tempo-procedimento').attr('disabled', 'true')
                $('#criarAgendamentoAntigoModal #valor').attr('disabled', 'true')
                $('#criarAgendamentoAntigoModal #modalidade_id').attr('disabled', 'true')
                $('#criarAgendamentoAntigoModal #data').attr('disabled', 'true')
                $('#criarAgendamentoAntigoModal #hora').attr('disabled', 'true')
                $('#criarAgendamentoAntigoModal #id-agenda-status').attr('disabled', 'true')
                $('#criarAgendamentoAntigoModal #obs').attr('disabled', 'true')
                document.querySelector("#criarAgendamentoAntigoModal #enviar").style.display = 'none'
                document.querySelector('#criarAgendamentoAntigoModal #confirmar').style.display = 'inline'
            }
            $('#agendamentosPendentesModal').modal('hide')
            $('#criarAgendamentoAntigoModal').modal('show')

            $('#criarAgendamentoAntigoModal').on("hidden.bs.modal", function () {
                $(this).find('input').val('');
                $(this).find('textarea').val('');
                $('#criarAgendamentoAntigoModal #agendamento-footer').show();
            });
        }
    );
}
function antigo_deletar_agendamento(id_agendamento) {
    if (window.confirm("Deseja realmente excluir esse agendamento?")) {
        $.get(
            '/saude-beta/agenda-antiga/deletar-agendamento', {
            id: id_agendamento
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {
                    mostrar_agendamentos();
                    mostrar_agendamentos_semanal();
                    // mostrar_fila_espera();
                }
            }
        );
    }
}
function antigo_mudar_tipo_confirmação(id_agendamento) {
    $.get('/saude-beta/agenda-antiga/agendamento-info/' + id_agendamento, function (data) {
        data = $.parseJSON(data);
        $('#mudarTipoConfirmacaoModal').modal('show');
        $('#mudarTipoConfirmacaoModal #id_agendamento_confirmacao').val(id_agendamento);
        $('#mudarTipoConfirmacaoModal #tipo_confirmacao').val(data.id_confirmacao);
        $('#btn-mudar-tipo-confirmacao').click(function () {
            $.post(
                '/saude-beta/agenda/mudar-tipo-confirmacao', {
                _token: $("meta[name=csrf-token]").attr("content"),
                id_agendamento: $('#mudarTipoConfirmacaoModal #id_agendamento_confirmacao').val(),
                contato: $('#mudarTipoConfirmacaoModal #tipo_confirmacao').val()
            },
                function (data, status) {
                    console.log(status + " | " + data);
                    if (data.error != undefined) {
                        alert(data.error);
                    } else {
                        $('#mudarTipoConfirmacaoModal').modal('hide');
                        mostrar_agendamentos();
                        mostrar_agendamentos_semanal();
                    }
                }
            );
        });
    });
}
function antigo_mudar_status(id_agendamento) {
    $.get('/saude-beta/agenda-antiga/agendamento-info/' + id_agendamento, function (data) {
        data = $.parseJSON(data);
        $('#mudarStatusModal').modal('show');
        $('#mudarStatusModal #id_agendamento_status').val(id_agendamento);
        $('#mudarStatusModal #status_agendamento').val(data.id_status);

        $('#btn-mudar-status').unbind('click').click(function () {
            if ($('#mudarStatusModal #status_agendamento').val() == 16) {
                alert('Não é permitido cancelar agendamentos nesta janela')
                return;
            }
            if ($('#mudarStatusModal #status_agendamento').val() == 13) {
                alert('Não é permitido finalizar agendamentos nesta janela')
                return;
            }
            $.post(
                '/saude-beta/agenda-antiga/mudar-status', {
                _token: $("meta[name=csrf-token]").attr("content"),
                id_agendamento: $('#mudarStatusModal #id_agendamento_status').val(),
                status: $('#mudarStatusModal #status_agendamento').val()
            },
                function (data, status) {
                    console.log(status + " | " + data);
                    if (data.error != undefined) {
                        alert(data.error);
                    } else {
                        if (data == 'true') {
                            $('#mudarStatusModal').modal('hide');
                            mostrar_agendamentos();
                            mostrar_agendamentos_semanal();

                        }
                    }
                }
            );
        });
    });
}
function antigo_repetir_agendamento(id_agendamento) {
    antigo_editar_agendamento(id_agendamento, false);
    setTimeout(() => {
        $("#criarAgendamentoAntigoModal #data").removeAttr('disabled')
        $("#criarAgendamentoAntigoModal #hora").removeAttr('disabled')
        $("#criarAgendamentoAntigoModal #bordero").css('display', 'none')
        $("#criarAgendamentoAntigoModal #enviar").css('display', 'inline')
        $('#criarAgendamentoAntigoModal #confirmar').css('display', 'none')
    }, 1200)
}
function antigo_colar_agendamento_por_id(id_agendamento) {
    console.log(id_agendamento);
    $.post(
        '/saude-beta/agenda-antigo/copiar-agendamento-id', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_agendamento_clipboard: id_agendamento_clipboard,
        id_agendamento: id_agendamento
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                _id_agendamento_clipboard = 0;
                mostrar_agendamentos();
                mostrar_agendamentos_semanal();
                // mostrar_fila_espera();
            }
        }
    );
}
function antigo_dividir_horario_por_id(id_agendamento) {
    $.post(
        '/saude-beta/agenda-antiga/dividir-horario-por-id', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_agendamento: id_agendamento
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                mostrar_agendamentos();
                mostrar_agendamentos_semanal();
                // mostrar_fila_espera();
            }
        }
    );
}
function antigo_ver_historico_agenda(id_agendamento) {
    var html;
    $.get(
        '/saude-beta/agenda-antiga/listar', {
        id_agenda: id_agendamento,
        campo: _campo
    },
        function (data) {
            data = $.parseJSON(data);
            if (_campo == 'agenda_status') $('#historicoAgendaModalLabel').html('Historico de Status do Agendamento');
            else $('#historicoAgendaModalLabel').html('Historico de Contatos do Agendamento');
            if (data.length > 0) {
                html = '<div class="card p-2">';
                html += '   <div class="row mb-1 mx-0">';
                html += '       <h5>Paciente: ';
                html += data[0].nome_paciente;
                html += '       </h5>';
                html += '       <h6>Tipo de procedimento: ';
                html += data[0].descr_tipo_procedimento;
                html += '       </h6>';
                html += '   </div>';
                html += '</div>';
                html += '<hr>';
                $('#lista-historico-agenda').html(html);
                data.forEach(historico => {
                    html = '<div class="card p-2">';
                    html += '   <div class="row mb-1 mx-0">';
                    if (_campo == 'agenda_status') html += '   <span class="col">' + historico.descr_status + '</span>';
                    else html += '   <span class="col">' + historico.descr_tipo_confirmacao + '</span>';
                    html += '       <span class="col text-right">';
                    html += historico.created_by + '<br>'
                    html += moment(historico.data).format('DD/MM/YYYY');
                    html += ' às ';
                    html += moment(historico.data).format('HH:mm');
                    html += '</span>';
                    html += '   </div>';
                    html += '</div>';
                    $('#lista-historico-agenda').append(html);
                });
            } else {
                html = '<div class="row card p-4">';
                if (_campo == 'agenda_status') html += '<h5>Não há histórico de Status para esse agendamento.</h5>';
                else html += '<h5>Não há histórico de Contatos para esse agendamento.</h5>';
                html += '</div>';
                $('#lista-historico-agenda').html(html);
            }
            $('#historicoAgendaModal').modal('show');
        }
    );

}

function antigo_cancelar_agendamento(id_agendamento) {
    $.get(
        '/saude-beta/agenda-antiga/agendamento-info/' + id_agendamento,
        function (data) {
            data = $.parseJSON(data);
            $('#cancelarAgendamentoAntigoModalLabel').html(
                'Cancelar Agendamento - ' + data.profissional_nome
            );
            $('#cancelarAgendamentoAntigoModal #id').val(id_agendamento);
            $('#cancelarAgendamentoAntigoModal #paciente').val(data.paciente_nome);
            $('#cancelarAgendamentoAntigoModal #data').val(moment(data.data).format('DD/MM/YYYY'));
            $('#cancelarAgendamentoAntigoModal #hora').val(data.hora.substring(0, 5));
            $('#cancelarAgendamentoAntigoModal').modal('show');
        }
    );
}
function antigo_adicionar_agendamento(id_agendamento) {
    elemento = $('.agenda-semanal-body').find('[data-id_agendamento="' + id_agendamento + '"]')
    dataux = elemento.parent().data().dia
    data = dataux.substr(8, 2) + '/' + dataux.substr(5, 2) + '/' + dataux.substr(0, 4)
    hora = elemento.parent().data().horario.substr(0, 5)
    $("#criarAgendamentoAntigoModal #data").val(data)
    $("#criarAgendamentoAntigoModal #hora").val(hora)
    $('#criarAgendamentoAntigoModal #id_tipo_procedimento').val('')
    $('#criarAgendamentoAntigoModal #id_contrato').val('')
    $('#criarAgendamentoAntigoModal #id_plano').val('')

    criarModalAgendamentoAntigo();
}
function adicionar_agendamento(id_agendamento) {
    elemento = $('.agenda-semanal-body').find('[data-id_agendamento="' + id_agendamento + '"]')
    dataux = elemento.parent().data().dia
    data = dataux.substr(8, 2) + '/' + dataux.substr(5, 2) + '/' + dataux.substr(0, 4)
    hora = elemento.parent().data().horario.substr(0, 5)
    $("#criarAgendamentoModal #data").val(data)
    $("#criarAgendamentoModal #hora").val(hora)
    $('#criarAgendamentoModal #id_tipo_procedimento').val('')
    $('#criarAgendamentoModal #id_contrato').val('')
    $('#criarAgendamentoModal #id_plano').val('')

    criarModalAgendamento();
}
function colar_agendamento_por_data(id_agendamento, _dia, _hora) {
    console.log(id_agendamento);
    console.log(_dia);
    console.log(_hora);
    $.post(
        '/saude-beta/agenda/copiar-agendamento', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_agendamento: id_agendamento,
        dia: _dia,
        hora: _hora
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                _id_agendamento_clipboard = 0;
                mostrar_agendamentos();
                mostrar_agendamentos_semanal();
                // mostrar_fila_espera();
            }
        }
    );
}

function colar_agendamento_por_id(id_agendamento_clipboard, id_agendamento) {
    console.log(id_agendamento);
    $.post(
        '/saude-beta/agenda/copiar-agendamento-id', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_agendamento_clipboard: id_agendamento_clipboard,
        id_agendamento: id_agendamento
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                _id_agendamento_clipboard = 0;
                mostrar_agendamentos();
                mostrar_agendamentos_semanal();
                // mostrar_fila_espera();
            }
        }
    );
}

function dividir_horario_por_data(_dia, _hora) {
    console.log(_dia);
    console.log(_hora);
    $.post(
        '/saude-beta/grade/dividir-horario', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_profissional: $('#selecao-profissional > .selected').data().id_profissional,
        dia: _dia,
        hora: _hora
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                2
                alert(data.error);
            } else {
                mostrar_agendamentos();
                mostrar_agendamentos_semanal();
                // mostrar_fila_espera();
            }
        }
    );
}

function dividir_horario_por_id(id_agendamento) {
    $.post(
        '/saude-beta/grade/dividir-horario-por-id', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_agendamento: id_agendamento
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                mostrar_agendamentos();
                mostrar_agendamentos_semanal();
                // mostrar_fila_espera();
            }
        }
    );
}

function ver_historico_agenda(id_agendamento, _campo) {
    var html;
    $.get(
        '/saude-beta/historico-agenda/listar', {
        id_agenda: id_agendamento,
        campo: _campo
    },
        function (data) {
            data = $.parseJSON(data);
            if (_campo == 'agenda_status') $('#historicoAgendaModalLabel').html('Historico de Status do Agendamento');
            else $('#historicoAgendaModalLabel').html('Historico de Contatos do Agendamento');
            if (data.length > 0) {
                html = '<div class="card p-2">';
                html += '   <div class="row mb-1 mx-0">';
                html += '       <h5>Paciente: ';
                html += data[0].nome_paciente;
                html += '       </h5>';
                html += '       <h6>Tipo de procedimento: ';
                html += data[0].descr_tipo_procedimento;
                html += '       </h6>';
                html += '   </div>';
                html += '</div>';
                html += '<hr>';
                $('#lista-historico-agenda').html(html);
                data.forEach(historico => {
                    html = '<div class="card p-2">';
                    html += '   <div class="row mb-1 mx-0">';
                    if (_campo == 'agenda_status') html += '   <span class="col">' + historico.descr_status + '</span>';
                    else html += '   <span class="col">' + historico.descr_tipo_confirmacao + '</span>';
                    html += '       <span class="col text-right">';
                    html += historico.created_by + '<br>'
                    html += moment(historico.data).format('DD/MM/YYYY');
                    html += ' às ';
                    html += moment(historico.data).format('HH:mm');
                    html += '</span>';
                    html += '   </div>';
                    html += '</div>';
                    $('#lista-historico-agenda').append(html);
                });
            } else {
                html = '<div class="row card p-4">';
                if (_campo == 'agenda_status') html += '<h5>Não há histórico de Status para esse agendamento.</h5>';
                else html += '<h5>Não há histórico de Contatos para esse agendamento.</h5>';
                html += '</div>';
                $('#lista-historico-agenda').html(html);
            }
            $('#historicoAgendaModal').modal('show');
        }
    );
}

function show_context_menu(e, id_fila_espera) {
    console.log(e);
    $('#fila-espera-context-menu').css({ 'top': e.pageY, 'left': (e.pageX - 140) });
    $('#fila-espera-context-menu').show();
    $('#fila-espera-context-menu > li').unbind('click').click(function () {
        switch ($(this).data().function) {
            case 'atender_fila':
                atender_fila(id_fila_espera);
                break;
            case 'desistir_fila':
                desistir_fila(id_fila_espera);
                break;
            default:
                $('#fila-espera-context-menu').hide();
        }
        $('#fila-espera-context-menu').hide();
    });

    $(document).click(function (e) {
        if (e.target.id != '#fila-espera-context-menu') {
            $('#fila-espera-context-menu').hide();
        }
    });
}

// function atender_fila(id_fila_espera) {
//     $.post(
//         "/saude-beta/fila-espera/atender-fila", {
//         _token: $("meta[name=csrf-token]").attr("content"),
//         id_fila_espera: id_fila_espera
//     },
//         function (data, status) {
//             console.log(status + " | " + data);
//             if (data.error != undefined) {
//                 alert(data.error);
//             } else {
//                 mostrar_agendamentos();
//                 mostrar_fila_espera();
//             }
//         }
//     );
// }

// function desistir_fila(id_fila_espera) {
//     $.post(
//         "/saude-beta/fila-espera/desistir-fila", {
//         _token: $("meta[name=csrf-token]").attr("content"),
//         id_fila_espera: id_fila_espera
//     },
//         function (data, status) {
//             console.log(status + " | " + data);
//             if (data.error != undefined) {
//                 alert(data.error);
//             } else {
//                 mostrar_agendamentos();
//                 mostrar_fila_espera();
//             }
//         }
//     );
// }

// function add_fila_espera(id_agendamento) {
//     var date = new Date();
//     $('#addfilaEsperaModal #id_agendamento').val(id_agendamento);
//     $('#addfilaEsperaModal #hora_chegada').val(
//         pad(date.getHours(), 2) + ':' + pad(date.getMinutes(), 2)
//     );
//     $('#addfilaEsperaModal').modal('show');
// }

// function abrir_form_fila_espera(id_agenda) {
//     var date = new Date();

//     $('.form-fila-espera').removeClass('open');
//     $('.form-fila-espera[data-id="' + id_agenda + '"]')
//     .addClass('open')
//     .find('#hora')
//     .val(date.getHours() + ':' + date.getMinutes());
// }

// function add_paciente_fila(id_agendamento, hora) {
//     $.post(
//         "/saude-beta/fila-espera/salvar", {
//         _token: $("meta[name=csrf-token]").attr("content"),
//         id_agendamento: id_agendamento,
//         hora: hora
//     },
//         function (data, status) {
//             console.log(status + " | " + data);
//             if (data.error != undefined) {
//                 alert(data.error);
//             } else {
//                 mostrar_agendamentos();
//                 mostrar_fila_espera();
//                 $('.form-fila-espera form #hora').val('');
//             }
//         }
//     );
// }



function finalizar_agendamento(id) {
    $.post(
        "/saude-beta/agenda/finalizar-agendamento", {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: id
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                mostrar_agendamentos();
                mostrar_agendamentos_semanal();
            }
        }
    );
}

function cancelar_agendamento(id) {
    $.get(
        '/saude-beta/agenda/agendamento-info/' + id,
        function (data) {
            data = $.parseJSON(data);
            $('#cancelarAgendamentoModalLabel').html(
                'Cancelar Agendamento - ' + data.profissional_nome
            );
            $('#cancelarAgendamentoModal #id').val(id);
            $('#cancelarAgendamentoModal #paciente').val(data.paciente_nome);
            $('#cancelarAgendamentoModal #data').val(moment(data.data).format('DD/MM/YYYY'));
            $('#cancelarAgendamentoModal #hora').val(data.hora.substring(0, 5));
            $('#cancelarAgendamentoModal').modal('show');
        }
    );
}
function desabilitarCamposAgendamento() {
    $('#criarAgendamentoModal #paciente_nome').prop('disabled', 'true');
    $('#criarAgendamentoModal #celular').prop('disabled', 'true')
    $('#criarAgendamentoModal #telefone').prop('disabled', 'true')
    $('#criarAgendamentoModal #email').prop('disabled', 'true')
    $('#criarAgendamentoModal #tipo_procedimento').prop('disabled', 'true')
    $('#criarAgendamentoModal #convenio-id').prop('disabled', 'true')
    $('#criarAgendamentoModal #tempo-procedimento').prop('disabled', 'true')
    $('#criarAgendamentoModal #data').prop('disabled', 'true')
    $('#criarAgendamentoModal #hora').prop('disabled', 'true')
    $('#criarAgendamentoModal #id-agenda-status').prop('disabled', 'true')
    $('#criarAgendamentoModal #obs').prop('disabled', 'true')
    $('#criarAgendamentoModal #id_plano').prop('disabled', 'true')
    $('#criarAgendamentoModal #id_contrato').prop('disabled', 'true')
}
function habilitarCamposAgendamento() {
    $('#criarAgendamentoModal #paciente_nome').removeAttr('disabled');
    $('#criarAgendamentoModal #celular').removeAttr('disabled')
    $('#criarAgendamentoModal #telefone').removeAttr('disabled')
    $('#criarAgendamentoModal #email').removeAttr('disabled')
    $('#criarAgendamentoModal #tipo_procedimento').removeAttr('disabled')
    $('#criarAgendamentoModal #convenio-id').removeAttr('disabled')
    $('#criarAgendamentoModal #tempo-procedimento').removeAttr('disabled')
    $('#criarAgendamentoModal #data').removeAttr('disabled')
    $('#criarAgendamentoModal #hora').removeAttr('disabled')
    $('#criarAgendamentoModal #id-agenda-status').removeAttr('disabled')
    $('#criarAgendamentoModal #obs').removeAttr('disabled')
    $('#criarAgendamentoModal #id_plano').removeAttr('disabled')
    $('#criarAgendamentoModal #id_contrato').removeAttr('disabled')
}
function repetir_agendamento($id) {
    editar_agendamento($id, false);
    setTimeout(() => {
        $("#criarAgendamentoModal #data").removeAttr('disabled')
        $("#criarAgendamentoModal #hora").removeAttr('disabled')
        $("#criarAgendamentoModal #bordero").css('display', 'none')
        $("#criarAgendamentoModal #enviar").css('display', 'inline')
        $('#criarAgendamentoModal #confirmar').css('display', 'none')
    }, 1200)
}
function editar_regra_associados(id) {
    $.get('/saude-beta/regras/exibir-regra-associados/' + id,
        function (data, status) {
            console.log(data + ' | ' + status)
            $("#regrasAssociadosModal").modal('show');
            $("#regrasAssociadosModal #id").val(data.id)
            $("#regrasAssociadosModal #dias").val(data.dias_pos_fim_contrato)
        })
}
function deletar_regra_associados(id) {
    $.get(
        '/saude-beta/regras/excluir-regra-associados/' + id,
        function (data, status) {
            console.log(data + ' | ' + status)
            if (!(isNaN(data))) {
                alert('Intervado de ' + data + ' dias foi excluído')
                location.reload(true);
            }
            else {
                alert('error');
            }
        }
    )
}
// function salvar_regras_associados(){
//     $.post(
//         '/saude-beta/regras/salvar-regra-associados', {
//             _token: $("meta[name=csrf-token]").attr("content"),
//             dias: $("#dias").val()
//         }, 
//         function(data, status){
//             console.log(data + ' | ' + status)
//             if (data == 'true'){
//                 alert('Regra salva com sucesso')
//                 redirect(location.href)
//             }
//             else {
//                 alert('ERRO')
//             }
//         }
//     )
// }
function abrirModalContato() {
    $.get(
        "/saude-beta/agenda-confirmacao/listar", {
        _token: $("meta[name=csrf-token]").attr('content')
    },
        function (data, status) {
            data = $.parseJSON(data);
            console.log(status + ' | ' + data);
            if (data.error != undefined) {
                alert(data.error)
            } else {
                data.forEach(el => {
                    html = ' <option value="' + el.id + '">' + el.descr + '</option>'
                    $("#id_contato").append(html);
                })
                $("#ConfirmacaoModal").modal('show');
            }
        }
    )
    $('#ConfirmacaoModal').modal('show')
}
function salvar_confirmacao($antigo) {
    if ($antigo == 0) {
        $.post(
            "/saude-beta/agenda/confirmar-agendamento-mobile", {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: $('#ConfirmacaoModal #id_agendamento').val(),
            id_confirmacao: $("#ConfirmacaoModal #id_contato").val()
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {
                    if (data.id_confirmacao != $("#ConfirmacaoModal #id_contato").val()) alert('erro')
                    mostrar_agendamentos();
                    mostrar_agendamentos_semanal();
                    $("#id_contato").empty()
                    $("#ConfirmacaoModal").modal('hide')
                    $('#criarAgendamentoModal').modal('hide')
                    $('#agendaMobileModal').modal('hide')
                }
            }
        );
    }
    else {
        $.post(
            "/saude-beta/agenda-antiga/confirmar-agendamento-mobile", {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: $('#ConfirmacaoModal #id_agendamento').val(),
            id_confirmacao: $("#ConfirmacaoModal #id_contato").val()
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {
                    if (data.id_confirmacao != $("#ConfirmacaoModal #id_contato").val()) alert('erro')
                    mostrar_agendamentos();
                    mostrar_agendamentos_semanal();
                    $("#id_contato").empty()
                    $("#ConfirmacaoModal").modal('hide')
                    $('#criarAgendamentoModal').modal('hide')
                    $('#agendaMobileModal').modal('hide')
                }
            }
        );
    }
}
function confirmar_agendamento(id_agendamento) {
    $.get(
        '/saude-beta/agenda/agendamento-info/' + id_agendamento,
        function (data) {
            data = $.parseJSON(data);
            $data = data.data

            $('#criarAgendamentoModal #id').val(id_agendamento);
            $('#criarAgendamentoModal #id-grade-horario').val(data.id_grade_horario);
            $('#criarAgendamentoModal #id-profissional').val($('#selecao-profissional > .selected').data().id_profissional);
            $('#criarAgendamentoModal #hora').val(data.hora.split(':')[0] + ':' + data.hora.split(':')[1]);
            $('#criarAgendamentoModal #paciente_id').val(data.id_paciente);
            $('#criarAgendamentoModal #paciente_nome').val(data.paciente_nome);
            $('#criarAgendamentoModal #procedimento_id').val(data.id_procedimento);
            $('#criarAgendamentoModal #procedimento_nome').val(data.procedimento);
            $('#criarAgendamentoModal #valor').val(data.valor);
            $('#criarAgendamentoModal #encaixe').prop("checked", data.encaixe);
            $('#criarAgendamentoModal #retorno').prop("checked", data.retorno);
            $('#criarAgendamentoModal #celular').val(data.celular);
            $('#criarAgendamentoModal #telefone').val(data.telefone);
            $('#criarAgendamentoModal #email').val(data.email);

            $('#criarAgendamentoModal #data').val($data[8] + $data[9] + '/' + $data[5] + $data[6] + '/' + $data[0] + $data[1] + $data[2] + $data[3]);
            $('#criarAgendamentoModal #id_tipo_procedimento').val(data.id_tipo_procedimento);
            $('#criarAgendamentoModal #tipo_procedimento').val(data.tipo_procedimento);
            $('#criarAgendamentoModal #tempo-procedimento').val(data.tempo);
            $('#criarAgendamentoModal #obs').val(data.obs);
            $('#criarAgendamentoModal #bordero').css('display', 'block')
            $('#criarAgendamentoModal #criarAgendamentoModalLabel').html("Agendamento");
            $('#criarAgendamentoModal').modal('show');

            desabilitarCamposAgendamento();

            document.querySelector('#criarAgendamentoModal #enviar').style.display = 'none'
            document.querySelector('#criarAgendamentoModal #confirmar').style.display = 'inline'
        }

    );
}
function mostrarAgendamento($id) {
    $.get(
        '/saude-beta/agenda/mostrar-agendamento/' + $id,
        function (data) {
            data = $.parseJSON(data);
            teste = data;
            $data = data[0].data
            $("#criarAgendamentoModal #id_contrato").empty()
            $("#criarAgendamentoModal #id_plano").empty()
            if (data[0].associar_contrato == true) {
                let datac = data[0].data_pedido
                datac = datac[8] + datac[9] + '/' + datac[5] + datac[6] + '/' + datac[0] + datac[1] + datac[2] + datac[3];

                document.querySelector("#criarAgendamentoModal #contratos").style.display = 'block'
                document.querySelector("#criarAgendamentoModal #planos_por_contrato").style.display = 'block'
                $("#criarAgendamentoModal #procedimento-nome-agenda").parent().css('display', 'none')
                $('#criarAgendamentoModal #id_tipo_procedimento').attr('disabled', 'true')


                $('#criarAgendamentoModal #id_contrato').append("<option value=''>Realizado em: " + datac + '</option>')
                $("#criarAgendamentoModal #id_plano").append("<option value=''>" + data[0].descr_tabela_precos + '</option>')

            }
            else if (data[0].associar_procedimento == true) {
                document.querySelector("#criarAgendamentoModal #contratos").style.display = 'none'
                document.querySelector("#criarAgendamentoModal #planos_por_contrato").style.display = 'none'
                $("#criarAgendamentoModal #procedimento-nome-agenda").parent().css('display', 'block')
                $("#criarAgendamentoModal #procedimento-nome-agenda").attr('disabled', 'true')
                $('#criarAgendamentoModal #id_tipo_procedimento').attr('disabled', 'true')

                $('#criarAgendamentoModal #procedimento-nome-agenda').val(data[0].descr_procedimento);

            }
            $('#criarAgendamentoModal #id').val($id);
            $('#criarAgendamentoModal #id-grade-horario').val(data[0].id_grade_horario);
            $('#criarAgendamentoModal #id-profissional').val($('#selecao-profissional > .selected').data().id_profissional);
            $('#criarAgendamentoModal #hora').val(data[0].hora.split(':')[0] + ':' + data[0].hora.split(':')[1]);
            $('#criarAgendamentoModal #paciente_id').val(data[0].id_paciente);
            $('#criarAgendamentoModal #paciente_nome').val(data[0].descr_paciente);
            $('#criarAgendamentoModal #id_tipo_procedimento').val(data[0].id_tipo_procedimento);
            $('#criarAgendamentoModal #valor').val(data[0].valor);
            $('#criarAgendamentoModal #encaixe').prop("checked", data[0].encaixe);
            $('#criarAgendamentoModal #retorno').prop("checked", data[0].retorno);
            $('#criarAgendamentoModal #celular').val(data[0].celular);
            $('#criarAgendamentoModal #telefone').val(data[0].telefone);
            $('#criarAgendamentoModal #email').val(data[0].email);
            $('#criarAgendamentoModal #data').val($data[8] + $data[9] + '/' + $data[5] + $data[6] + '/' + $data[0] + $data[1] + $data[2] + $data[3]);
            $('#criarAgendamentoModal #id-agenda-status').val(data[0].id_agenda_status)
            $('#criarAgendamentoModal #obs').val(data[0].obs)



            $('#criarAgendamentoModal #tempo-procedimento').val(data[0].tempo);
            $('#criarAgendamentoModal #criarAgendamentoModalLabel').html("Agendamento");
            $('#criarAgendamentoModal').modal('show');
            desabilitarCamposAgendamento();
            document.querySelector('#criarAgendamentoModal #enviar').style.display = 'none'
            document.querySelector('#criarAgendamentoModal #confirmar').style.display = 'none'
        }

    );
}
function retornarDataAtual(format) {
    var data = new Date();
    var dia = String(data.getDate()).padStart(2, '0');
    var mes = String(data.getMonth() + 1).padStart(2, '0');
    var ano = data.getFullYear();

    switch (format) {
        case 1:
            return dataAtual = ano + '-' + mes + '-' + dia;
        case 2:
            return dataAtual = dia + '/' + mes + '/' + ano;
    }
    return 'erro';
}

function novo_agendamento() {
    var data = new Date();
    var dia = String(data.getDate()).padStart(2, '0');
    var mes = String(data.getMonth() + 1).padStart(2, '0');
    var ano = data.getFullYear();

    dataAtual = dia + '/' + mes + '/' + ano;

    $('#criarAgendamentoModal #data').val(dataAtual);
    criarModalAgendamento();
}
var isLote, mod, gC, espEnc;
function criarModalAgendamento() {
    if (!detectar_mobile()) {
        $('#criarAgendamentoModal #paciente_nome').removeAttr('disabled');
        $('#criarAgendamentoModal #celular').removeAttr('disabled')
        $('#criarAgendamentoModal #celular').parent().css('display', 'block');
        $('#criarAgendamentoModal #id-agenda-status').removeAttr('disabled')
        $('#criarAgendamentoModal #telefone').removeAttr('disabled')
        $('#criarAgendamentoModal #email').removeAttr('disabled')
        $('#criarAgendamentoModal #id_tipo_procedimento').removeAttr('disabled')
        $('#criarAgendamentoModal #convenio_id').removeAttr('disabled')
        $('#criarAgendamentoModal #id_contrato').removeAttr('disabled')
        $('#criarAgendamentoModal #id_plano').removeAttr('disabled')
        $('#criarAgendamentoModal #procedimento_id').removeAttr('disabled')
        $('#criarAgendamentoModal #modalidade_id').removeAttr('disabled')
        $('#criarAgendamentoModal #id_agenda_status').removeAttr('disabled')
        $('#criarAgendamentoModal #data').removeAttr('disabled')
        $('#criarAgendamentoModal #hora').removeAttr('disabled')
        $('#criarAgendamentoModal #obs').removeAttr('disabled')
        $('#criarAgendamentoModal #convenio_id').parent().css('display', 'none')
        $('#criarAgendamentoModal #enviar').css("display", 'inline')

        $('#criarAgendamentoModal #paciente_nome').parent().css('display', 'block');
        $('#criarAgendamentoModal #telefone').parent().css('display', 'block')
        $('#criarAgendamentoModal #email').css('display', 'block')
        $('#criarAgendamentoModal #id_tipo_procedimento').parent().css('display', 'block')
        $('#criarAgendamentoModal #id_contrato').parent().css('display', 'none')
        $('#criarAgendamentoModal #id_plano').parent().css('display', 'none')
        $('#criarAgendamentoModal #procedimento_id').parent().css('display', 'none')
        $('#criarAgendamentoModal #modalidade_id').parent('display', 'block')
        $('#criarAgendamentoModal #id_agenda_status').parent().css('display', 'block')
        $('#criarAgendamentoModal #data').parent().parent().css('display', 'block')
        $('#criarAgendamentoModal #hora').parent().css('display', 'block')
        $('#criarAgendamentoModal #obs').parent().css('display', 'block')
        $('#criarAgendamentoModal #confirmar').css('display', 'none')
        $('#criarAgendamentoModal #bordero').css('display', 'none')
        $('#criarAgendamentoModal #modalidade-descr').css('display', 'block')

        $("#criarAgendamentoModal #id-profissional").val($(".selected").data().id_profissional)
        
        $.get("/saude-beta/encaminhamento/especialidade/por-encaminhante", {
            id : $('#selecao-profissional > .selected').data().id_profissional,
            col : "id_pessoa"
        }, function(data) {
            espEnc = $.parseJSON(data);
            control_criar_agendamento();
        });
    }
}
function criarModalAgendamentoLote() {
    $('#agendamentosEmLoteModal').modal()
}
function criarModalAgendamentoCall(lote) {
    gc = 0;
    modM = 0;
    isLote = lote;

}
function criarModalAgendamentoMain() {
    if (!detectar_mobile()) {
        $('#criarAgendamentoModal #celular').parent().css('display', 'block');
        $('#criarAgendamentoModal #convenio_id').parent().css('display', 'none')
        $('#criarAgendamentoModal #enviar').css("display", 'inline')

        $('#criarAgendamentoModal #paciente_nome').parent().css('display', 'block');
        $('#criarAgendamentoModal #telefone').parent().css('display', 'block')
        $('#criarAgendamentoModal #email').css('display', 'block')
        $('#criarAgendamentoModal #id_tipo_procedimento').parent().css('display', 'block')
        $('#criarAgendamentoModal #id_contrato').parent().css('display', 'none')
        $('#criarAgendamentoModal #id_plano').parent().css('display', 'none')
        $('#criarAgendamentoModal #procedimento_id').parent().css('display', 'none')
        $('#criarAgendamentoModal #modalidade_id').parent('display', 'block')
        $('#criarAgendamentoModal #modalidade_id').empty()
        $('#criarAgendamentoModal #modalidade_id').append('<option value="0">Selecionar modalidade...</option>');
        $('#criarAgendamentoModal #id_agenda_status').parent().css('display', 'block')
        $('#criarAgendamentoModal #data').parent().parent().css('display', 'block')
        $('#criarAgendamentoModal #hora').parent().css('display', 'block')
        $('#criarAgendamentoModal #obs').parent().css('display', 'block')
        $('#criarAgendamentoModal #confirmar').css('display', 'none')
        $('#criarAgendamentoModal #bordero').css('display', 'none')
        $('#criarAgendamentoModal #modalidade-descr').css('display', 'block')

        $("#criarAgendamentoModal #id-profissional").val($(".selected").data().id_profissional)
        control_criar_agendamento(() => {
            botoesEncAgenda();            
            $('#criarAgendamentoModal').modal('show');
            $("#criarAgendamentoModal .modal-body *").each(function () {
                $(this).attr("disabled", true);
            });
            $("#criarAgendamentoModal #paciente_nome").attr("disabled", false);
            $("#criarAgendamentoModal #id_tipo_procedimento").val(0);
        });
    }
}
function criarModalAgendamentoAntigo() {
    $("#criarAgendamentoAntigoModal #bordero").css('display', 'none')
    $('#criarAgendamentoAntigoModal #paciente_nome').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #celular').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #telefone').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #email').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #procedimento_id').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #id_contrato').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #id_plano').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #convenio_id').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #id-sala').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #tempo-procedimento').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #valor').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #data').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #hora').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #id-agenda-status').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #obs').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #modalidade_id').removeAttr('disabled')
    $("#criarAgendamentoAntigoModal #modalidade_id").empty();
    document.querySelector("#criarAgendamentoAntigoModal #enviar").style.display = 'inline'
    document.querySelector('#criarAgendamentoAntigoModal #confirmar').style.display = 'none'
    $("#criarAgendamentoAntigoModal #confirmar").css("display", 'none');
    $("#criarAgendamentoAntigoModal #id_tipo_procedimento").val(1).attr('disabled', true)
    $("#criarAgendamentoAntigoModal #id-profissional").val($(".selected").data().id_profissional)
    $('#criarAgendamentoAntigoModal').modal('show')
}
function criarModalReagendamentoAntigo($id) {
    $('#criarAgendamentoAntigoModal #paciente_nome').removeAttr('disabled');
    $('#criarAgendamentoAntigoModal #celular').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #telefone').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #email').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #tipo_procedimento').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #convenio-id').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #tempo-procedimento').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #data').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #hora').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #id-agenda-status').removeAttr('disabled')
    $('#criarAgendamentoAntigoModal #obs').removeAttr('disabled')
    document.querySelector('#criarAgendamentoAntigo #enviar').style.display = 'inline'
    document.querySelector('#criarAgendamentoAntigo #confirmar').style.display = 'none'


    document.querySelector('#criarAgendamentoAntigoModal #procedimentos').style.display = 'none'
    document.querySelector('#criarAgendamentoAntigoModal #convenio-descr').style.display = 'none'
    document.querySelector("#criarAgendamentoAntigoModal #agenda-status").style.display = 'none'
    document.querySelector("#contratos").style.display = 'none';
    document.querySelector("#planos_por_contrato").style.display = 'none'
    // document.querySelector("#checks").style.display = 'none'
    document.querySelector('#email').style.display = 'none'

    $('#criarAgendamentoAntigoModal #tipo_procedimento').removeAttr('required')
    $('#criarAgendamentoAntigoModal #convenio-id').removeAttr('required')
    $("#criarAgendamentoAntigoModal #id-agenda-status").removeAttr('required')
    $('#criarAgendamentoAntigoModal #criarAgendamentoAntigoModal').html("Reagendar");
    $("#contratos").removeAttr('required');
    encontrarContratos(false, () => {
        $("#planos_por_contrato").removeAttr('required');
        encontrarPlanosContrato(() => {
            $('#criarAgendamentoAntigoModal').modal('show');

        })
    });

}

function salvar_agendamento_antigo() {
    $.get('/saude-beta/agenda-antiga/salvar-agendamento-antigo', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: $("#criarAgendamentoAntigoModal #id").val(),
        id_profissional: $("#criarAgendamentoAntigoModal #id-profissional").val(),
        paciente_nome: $("#criarAgendamentoAntigoModal #paciente_nome").val(),
        id_grade_horario: $("#criarAgendamentoAntigoModal #id-grade-horario").val(),
        paciente_id: $("#criarAgendamentoAntigoModal #paciente_id").val(),
        id_tipo_procedimento: $("#criarAgendamentoAntigoModal #id_tipo_procedimento").val(),
        modalidade_id: $("#criarAgendamentoAntigoModal #modalidade_id").val(),
        id_agenda_status: $("#criarAgendamentoAntigoModal #id-agenda-status").val(),
        data: $("#criarAgendamentoAntigoModal #data").val(),
        hora: $("#criarAgendamentoAntigoModal #hora").val(),
        obs: $("#criarAgendamentoAntigoModal #obs").val()
    }, function (data, status) {
        console.log(data + ' | ' + status)
        mostrar_agendamentos_semanal();
        mostrar_agendamentos();
        $("#criarAgendamentoAntigoModal").modal('hide')
    })

}

function pad(str, max) {
    str = str.toString();
    str = str.length < max ? pad("0" + str, max) : str; // zero à esquerda
    str = str.length > max ? str.substr(0, max) : str; // máximo de caracteres
    return str;
}

function alterar_calendario(add, day_month) {
    var date_selected, date_selected_temp,
        today = moment(new Date()),
        mini_calendar = $(".mini-calendar"),
        html = '';

    if (add == undefined) {
        date_selected = today;

    } else if (day_month == 'M') {
        date_selected_temp = [$(".month-selected").data().year, $(".month-selected").data().month - 1, 01];
        date_selected = moment(date_selected_temp).add(add, "M");

    } else if (day_month == 'D') {
        if (add == 1 || add == -1) {
            date_selected_temp = [$(".month-selected").data().year, $(".month-selected").data().month - 1, $('.mini-calendar h6.selected').data().day];
            date_selected = moment(date_selected_temp).add(add, "d");
        } else {
            date_selected_temp = [$(".month-selected").data().year, $(".month-selected").data().month, $('.mini-calendar h6.selected').data().day];
            date_selected = new Date(date_selected_temp)
            date_selected = moment(date_selected.setDate(date_selected.getDate() + add));
        }
    }

    html += '<div class="month-control">';
    html += '    <i class="my-icon fas fa-caret-left month-minus" onclick="' + "alterar_calendario(-1, 'M')" + '"></i>';
    html += '    <h6 class="month-selected" data-month="' + date_selected.format('M') + '" data-year="' + date_selected.format('Y') + '">';
    html += date_selected.format('MMMM YYYY');
    html += '    </h6>';
    html += '    <i class="my-icon fas fa-caret-right month-plus" onclick="' + "alterar_calendario(1, 'M')" + '"></i>';
    html += '</div>';
    html += '<div class="month-body">';
    html += '    <div class="week-label">';
    html += '        <h6>D</h6>';
    html += '        <h6>S</h6>';
    html += '        <h6>T</h6>';
    html += '        <h6>Q</h6>';
    html += '        <h6>Q</h6>';
    html += '        <h6>S</h6>';
    html += '        <h6>S</h6>';
    html += '    </div>';
    html += '</div>';

    mini_calendar.html(html);
    date_selected = date_selected.startOf('month');
    date_selected = date_selected.startOf('week');
    html = '';

    for (let i = 1; i <= 6; i++) {
        html += '<div class="week-' + i + '">';
        for (let j = 1; j <= 7; j++) {
            html += '<h6 data-day="' + date_selected.format("D") +
                '" data-month="' + date_selected.format("M") +
                '" data-year="' + date_selected.format("Y") + '">' +
                date_selected.format("D") +
                '</h6>';
            date_selected = date_selected.add(1, 'days');
        }
        html += '</div>';
    }

    mini_calendar.find(".month-body").append(html);
    mini_calendar.find('.month-body > div:not(".week-label") h6').dblclick(
        function () {
            mini_calendar.find('.month-body h6.selected').removeClass('selected');
            $(this).addClass('selected');
            mostrar_agendamentos();
            mostrar_agendamentos_semanal();
            $('#filtro-semana').removeClass('show');
        }
    );
    // $('.month-minus').click(function () {
    //     alterar_calendario(-1, 'M');
    // });
    // $('.month-plus').click(function () {
    //     alterar_calendario(1, 'M');
    // });

    if (day_month == 'M') {
        date_selected_temp = moment(date_selected_temp);
        if (add == 1) date_selected_temp = date_selected_temp.add(1, "M");
        else date_selected_temp = date_selected_temp.subtract(1, "M");
    } else if (day_month == 'D') {
        if (add == 1) {
            date_selected_temp = moment(date_selected_temp);
            date_selected_temp = date_selected_temp.add(1, "d");
        } else if (add == -1) {
            date_selected_temp = moment(date_selected_temp);
            date_selected_temp = date_selected_temp.subtract(1, "d");
        } else {
            date_selected = new Date(date_selected_temp)
            date_selected_temp = moment(date_selected.setDate(date_selected.getDate() + add));
        }
    } else {
        date_selected_temp = moment(new Date());
    }

    mini_calendar.find(
        '[data-day="' + date_selected_temp.format("D") + '"]' +
        '[data-month="' + date_selected_temp.format("M") + '"]' +
        '[data-year="' + date_selected_temp.format("Y") + '"]'
    ).addClass("selected");

    today = moment(new Date());
    mini_calendar.find(
        '[data-day="' + today.format("D") + '"]' +
        '[data-month="' + today.format("M") + '"]' +
        '[data-year="' + today.format("Y") + '"]'
    ).addClass("today");
}

function abrir_grades_pessoa(id_pessoa) {
    if ($('#gradeModal #empresa').val() == '' || $('#gradeModal #empresa').val() == null) id_emp = 1
    else {
        id_emp = $('#gradeModal #empresa').val()
        var temp_emp = id_emp
    }
    console.log('/saude-beta/grade/mostrar-pessoa/' + id_pessoa + '/' + id_emp)
    $.get('/saude-beta/grade/mostrar-pessoa/' + id_pessoa + '/' + id_emp, function (data) {
        data = $.parseJSON(data);
        var html;
        $('#id_profissional').val(data.profissional.id)

        $('#gradeModal #empresa').empty()
        data.empresas.forEach(empresa => {
            html = '<option value="' + empresa.id + '">' + empresa.descr + '</option>'
            $('#gradeModal #empresa').append(html)
        })
        $('#gradeModal #empresa').val(temp_emp)

        $('.grade-semana').find('[data-dia_semana]').empty();
        data.grade.forEach(grade => {
            html = '<li data-id_grade="' + grade.id + '"'
            if (grade.obs != null) html += ' title="' + grade.obs + '"';
            if (!grade.ativo) html += ' class="grade-inativa"';
            html += '>';
            html += '   <p style="text-align-last:justify">';
            html += '   Das ' + grade.hora_inicial.substring(0, 5) + ' às ' + grade.hora_final.substring(0, 5) + '<br>';
            html += '   De ' + grade.min_intervalo + ' em ' + grade.min_intervalo + ' mins.<br>';
            if (grade.data_inicial != null && grade.data_final == null) {
                html += 'A partir de ' + moment(grade.data_inicial).format('DD/MM/YYYY');
            }
            else if (grade.data_inicial == null && grade.data_final != null) {
                html += 'Até ' + moment(grade.data_final).format('DD/MM/YYYY');
            }
            else if (grade.data_inicial != null & grade.data_final != null) {
                html += 'Vigência: ';
                html += moment(grade.data_inicial).format('DD/MM/YYYY');
                html += ' até ';
                html += moment(grade.data_final).format('DD/MM/YYYY');
            }

            else {
                html += 'Sem data de ínicio';
            }
            html += '       <br>';
            html += '   </p>';
            html += '   <p class="mt-1 text-right">';
            if (grade.ativo) html += '<i class="far fa-calendar-minus desativacao-grade" onclick="grade_ativar_desativar(' + grade.id + ', false)"></i>';
            else html += '<i class="far fa-calendar-check ativacao-grade" onclick="grade_ativar_desativar(' + grade.id + ', true)"></i>';
            html += '                 <i class="far fa-trash-alt lixeira-grade" onclick="deletar_grade(' + grade.id + ')"></i>';
            html += '   </p>';
            html += '</li>'

            $('.grade-semana')
                .find('[data-dia_semana="' + grade.dia_semana + '"]')
                .append(html);
        });
        $('#gradeModal').modal('show');
    });
}

function grade_ativar_desativar(id_grade, _ativacao) {
    $.post(
        '/saude-beta/grade/ativar-desativar', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_grade: id_grade,
        ativacao: _ativacao
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                if (_ativacao) alert('Grade ativada com sucesso!');
                else alert('Grade desativada com sucesso!');
                abrir_grades_pessoa(data);
            }
        }
    );
}

function verificar_grade_por_semana(e) {
    e.preventDefault();
    $.get(
        '/saude-beta/grade/verificar-grade-por-semana', {
        id_profissional: $('#id_profissional').val(),
        dia_semana: $('#dia-semana').val()
    },
        function (data) {
            if (data) {
                ShowConfirmationBox(
                    'Atenção!',
                    'Já existe uma grade nesse dia da semana.<br>Deseja mesclar?',
                    true, true, true,
                    function () {
                        $.post(
                            "/saude-beta/grade/salvar",
                            $('#cadastro-grade-form').serialize() + "&mesclar=" + true,
                            function (data, status) {
                                console.log(data);
                                // test_
                                console.log(status + " | " + data);
                                if (data.error != undefined) {
                                    alert(data.error);
                                } else {
                                    alert('Grade salva com sucesso!');
                                    abrir_grades_pessoa(data);
                                }
                            }
                        );
                    },
                    function () {
                        $.post(
                            "/saude-beta/grade/salvar",
                            $('#cadastro-grade-form').serialize() + "&mesclar=" + false,
                            function (data, status) {
                                console.log(data);
                                // test_
                                console.log(status + " | " + data);
                                if (data.error != undefined) {
                                    alert(data.error);
                                } else {
                                    alert('Grade salva com sucesso!');
                                    abrir_grades_pessoa(data);
                                }
                            }
                        );
                    }
                );
            } else {
                $.post(
                    "/saude-beta/grade/salvar",
                    $('#cadastro-grade-form').serialize() + "&mesclar=" + true,
                    function (data, status) {
                        console.log(data);
                        // test_
                        console.log(status + " | " + data);
                        if (data.error != undefined) {
                            alert(data.error);
                        } else {
                            alert('Grade salva com sucesso!');
                            abrir_grades_pessoa(data);
                        }
                    }
                );
            }
        }
    );
}


function deletar_grade(id_grade) {
    if (window.confirm("Deseja realmente excluir essa grade?")) {
        $.post(
            "/saude-beta/grade/deletar", {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: id_grade
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {
                    alert('Grade deletada com sucesso!');
                    abrir_grades_pessoa(data);
                }
            }
        );
    }
}

function deletar_etiqueta(id_etiqueta) {
    if (window.confirm("Deseja realmente excluir essa etiqueta?")) {
        $.post(
            "/saude-beta/etiqueta/deletar", {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: id_etiqueta
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {
                    alert('Etiqueta deletada com sucesso!');
                    document.location.reload(true);
                }
            }
        );
    }
}

function deletar_agenda_status(id_agenda_status) {
    if (window.confirm("Deseja realmente excluir esse status da agenda?")) {
        $.post(
            "/saude-beta/agenda-status/deletar", {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: id_agenda_status
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {
                    alert('Status da agenda deletado com sucesso!');
                    document.location.reload(true);
                }
            }
        );
    }
}

function bloquear_grades_pessoa(id_pessoa) {
    var today = moment(),
        data_inicial, data_final;

    $('#gradeBloqueioModal').modal('show');
    $.get('/saude-beta/grade-bloqueio/mostrar-pessoa/' + id_pessoa, function (data) {
        data = $.parseJSON(data);
        $('#gradeBloqueioModal #id-profissional').val(data.profissional.id)
        $('#lista-grade-bloqueio').empty();
        data.bloqueios.forEach(bloqueio => {
            html = '<div class="card row" title="Click para ver as observações." style="flex-direction:row; cursor:pointer">';
            html += '    <div class="col-4 d-flex" data-toggle="collapse" data-target="#collapse' + bloqueio.id + '" aria-expanded="false" aria-controls="collapse' + bloqueio.id + '"> ';
            data_inicial = moment(bloqueio.data_inicial);
            data_final = moment(bloqueio.data_final);
            if (bloqueio.ativo && today.diff(data_final, 'days') <= 0) {
                html += '<span class="semaforo color-validade-verde my-auto mr-3" data-message="Ativo (' + data_inicial.format('DD/MM/YYYY') + ' — ' + data_final.format('DD/MM/YYYY') + ')"></span>';
            } else if (today.diff(data_final, 'days') >= 0) {
                html += '<span class="semaforo color-validade-laranja my-auto mr-3" data-message="Expirado no ' + data_final.format('DD/MM/YYYY') + '"></span>';
            } else {
                html += '<span class="semaforo color-validade-vermelho my-auto mr-3" data-message="Inativo"></span>';
            }
            html += '        <h5 class="my-2 text-dark">';
            html += semana_descr(bloqueio.dia_semana);
            html += '        </h5>';
            html += '    </div>';
            html += '    <div class="col-8 d-grid">';
            html += '        <div class="ml-auto btn-table-action text-dark d-flex">';
            html += '            <h5 class="my-2 mr-3 text-dark">';
            html += data_inicial.format('DD/MM/YYYY') + ' até ' + data_final.format('DD/MM/YYYY') + '    -    '
            html += bloqueio.hora_inicial.substring(0, 5) + ' — ' + bloqueio.hora_final.substring(0, 5);
            html += '            </h5>';
            if (bloqueio.ativo) html += '<i class="my-icon my-auto fas fa-window-close" title="Desativar Grade" onclick="ativacao_bloqueio(' + bloqueio.id + ', false)"></i>';
            else html += '<i class="my-icon my-auto fas fa-check-square" title="Ativar Grade" onclick="ativacao_bloqueio(' + bloqueio.id + ', true)"></i>';
            html += '            <i class="my-icon my-auto fas fa-trash" title="Excluir" onclick="excluir_bloqueio(' + bloqueio.id + ')"></i>';
            html += '        </div>';
            html += '    </div>';

            html += '    <div id="collapse' + bloqueio.id + '" class="collapse" aria-labelledby="heading' + bloqueio.id + '" data-parent="#lista-grade-bloqueio">';
            html += '        <div class="card-body">';
            html += '            <b>OBS.:</b> ' + bloqueio.obs;
            html += '        </div>';
            html += '    </div>';
            html += '</div>';
            $('#lista-grade-bloqueio').append(html);
        });
        $('#gradeBloqueioModal').modal('show');
    });
}

function ativacao_bloqueio(id_bloqueio, _ativacao) {
    $.post(
        '/saude-beta/grade-bloqueio/ativar-desativar', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_bloqueio: id_bloqueio,
        ativacao: _ativacao
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                bloquear_grades_pessoa(data.id_profissional);
            }
        }
    );
}

function excluir_bloqueio(id_bloqueio) {
    $.post(
        '/saude-beta/grade-bloqueio/deletar', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_bloqueio: id_bloqueio
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {

                if (detectar_mobile()) {
                    mostrar_agendamentos()
                    mostrar_agendamentos_semanal()
                    $('#gradeBloqueioModal').modal('hide')
                }
                else {
                    bloquear_grades_pessoa(data.id_profissional);
                }
            }
        }
    );
}

function semana_descr(dia_semana) {
    if (dia_semana == 1) return 'Domingo';
    else if (dia_semana == 2) return 'Segunda';
    else if (dia_semana == 3) return 'Terça';
    else if (dia_semana == 4) return 'Quarta';
    else if (dia_semana == 5) return 'Quinta';
    else if (dia_semana == 6) return 'Sexta';
    else if (dia_semana == 7) return 'Sábado';
}

function editar_pessoa(id_pessoa) {
    $.get('/saude-beta/pessoa/mostrar/' + id_pessoa, function (data) {
        data = $.parseJSON(data);
        console.log(data);
        $('#foto-preview').on("error", function () {
            $(this).attr('src', '/saude-beta/img/foto_purple.png');
        });
        $('#foto-preview').attr('src', '/saude-beta/img/pessoa/' + data.id + '.jpg');
        $('#pessoaModal').find('#id').val(data.id);
        $('#pessoaModal').find('#cod_interno').val(data.cod_interno);
        $('#pessoaModal').find('#nome_fantasia').val(data.nome_fantasia);
        $('#pessoaModal').find('#nome_reduzido').val(data.nome_reduzido);
        $('#pessoaModal').find('#email').val(data.email);
        try {
            $('#pessoaModal').find('#psq').val(data.psq);
        } catch(err) {}

        if (data.tpessoa == 'J') {
            $('#pessoaModal').find('#razao_social').val(data.razao_social);
            $('#pessoaModal').find('#cnpj').val(data.cpf_cnpj);
            $('#pessoaModal').find('#ie').val(data.rg_ie);
            $('#pessoaModal').find('#rg').val('');
            $('#pessoaModal').find('#estado-civil').val('');
            $('#pessoaModal').find('#sexo').val('');
            $('#pessoaModal').find('#peso').val('');
            $('#pessoaModal').find('#altura').val('');
            $('#pessoaModal').find('#data_nasc').val('');

        } else {
            $('#pessoaModal').find('#razao_social').val('');
            $('#pessoaModal').find('#cpf').val(data.cpf_cnpj);
            $('#pessoaModal').find('#rg').val(data.rg_ie);
            $('#pessoaModal').find('#estado-civil').val(data.estado_civil);
            $('#pessoaModal').find('#sexo').val(data.sexo);
            $('#pessoaModal').find('#peso').val(data.peso);
            $('#pessoaModal').find('#altura').val(data.altura);
            $('#pessoaModal').find('#profissao').val(data.profissao);
            if (data.data_nasc != null) $('#pessoaModal').find('#data_nasc').val(moment(data.data_nasc).format('DD/MM/YYYY'));
            else $('#pessoaModal').find('#pessoaModal').find('#data_nasc').val('');
        }

        $('#pessoaModal').find('#crm_cro').val(data.crm_cro);
        $('#pessoaModal').find('#crm').val(data.crm);
        $('#pessoaModal').find('#uf-crm').val(data.uf_crm);
        $('#pessoaModal').find('#cref').val(data.cref);
        $('#pessoaModal').find('#uf-cref').val(data.uf_cref);
        $('#pessoaModal').find('#creft').val(data.creft);
        $('#pessoaModal').find('#uf-creft').val(data.uf_creft);
        $('#pessoaModal').find('#crn').val(data.crn);
        $('#pessoaModal').find('#uf-crn').val(data.uf_crn);
        $('#pessoaModal').find('#num_convenio').val(data.num_convenio);
        $('#pessoaModal').find('#resp-nome').val(data.resp_nome);
        $('#pessoaModal').find('#resp-cpf').val(data.resp_cpf);
        $('#pessoaModal').find('#resp-celular').val(data.resp_celular);
        $('#pessoaModal').find('#cep').val(data.cep);
        $('#pessoaModal').find('#cidade').val(data.cidade);
        $('#pessoaModal').find('#uf').val(data.uf);
        $('#pessoaModal').find('#endereco').val(data.endereco);
        $('#pessoaModal').find('#numero').val(data.numero);
        $('#pessoaModal').find('#bairro').val(data.bairro);
        $('#pessoaModal').find('#complemento').val(data.complemento);
        $('#pessoaModal').find('#celular1').val(data.celular1);
        $('#pessoaModal').find('#obs').val(data.obs);
        if (data.celular2 != 'NULL') $('#pessoaModal').find('#celular2').val(data.celular2);
        else $('#pessoaModal').find('#celular2').val('');
        if (data.telefone1 != 'NULL') $('#pessoaModal').find('#telefone1').val(data.telefone1);
        else $('#pessoaModal').find('#telefone1').val('');
        if (data.telefone2 != 'NULL') $('#pessoaModal').find('#telefone2').val(data.telefone2);
        else $('#pessoaModal').find('#telefone2').val('');
        $('#pessoaModal').find('#password').val(data.password);
        $('#pessoaModal').find('#password').removeAttr("required");
        $('#pessoaModal').find('#isAdministrador').prop("checked", (data.administrador == 'S'));
        $('#pessoaModal').find('#isPaciente').prop("checked", (data.paciente == 'S'));
        $('#pessoaModal').find('#isCliente').prop("checked", (data.cliente == 'S'));
        $('#pessoaModal').find('#isMedico').prop("checked", (data.colaborador == 'P'));
        $('#pessoaModal').find('#isRecepcao').prop("checked", (data.colaborador == 'R'));

        var html;
        if (data.convenio_pessoa.length != 0) {
            $('#pessoaModal').find('#lista-convenio-pessoa').empty();
            data.convenio_pessoa.forEach((convenio_pessoa, i) => {
                html = '<div class="row">';
                html += '    <div class="col-md-6 form-group">';
                if (i == 0) html += '<label for="convenio" class="custom-label-form">Convênio</label>';
                html += '        <select id="convenio" name="convenio[]" class="form-control custom-select">';
                html += '            <option value="0">Selecionar Convênio...</option>';
                data.convenios.forEach(convenio => {
                    html += '        <option value="' + convenio.id + '"';
                    if (convenio.id == convenio_pessoa.id_convenio) html += ' selected ';
                    html += '>' + convenio.descr + '</option>';
                });
                html += '        </select>';
                html += '    </div>';
                html += '    <div class="col-md-4 form-group">';
                if (i == 0) html += '<label for="num-convenio" class="custom-label-form">Nº Convênio</label>';
                html += '           <input id="num-convenio" name="num_convenio[]" class="form-control" autocomplete="off" type="text" value="';
                if (convenio_pessoa.num_convenio != null) html += convenio_pessoa.num_convenio;
                html += '" maxlength="45">';
                html += '    </div>';
                html += '    <div class="col-md-2 form-group d-flex">';
                html += '        <button class="btn btn-danger mt-auto mr-1" style="height:calc(1.5em + 0.75rem + 8px)" onclick="delete_convenio_pessoa($(this)); return false;">';
                html += '            <i class="my-icon fas fa-trash"></i>';
                html += '        </button>';
                html += '        <button class="btn btn-success mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_convenio_pessoa($(this)); return false;">';
                html += '            <i class="my-icon fas fa-plus"></i>';
                html += '        </button>';
                html += '    </div>';
                html += '</div>';
                $('#pessoaModal').find('#lista-convenio-pessoa').append(html);
            });
        } else {
            html = '<div class="row">';
            html += '    <div class="col-md-6 form-group">';
            html += '        <label for="convenio" class="custom-label-form">Convênio</label>';
            html += '        <select id="convenio" name="convenio[]" class="form-control custom-select">';
            html += '            <option value="0">Selecionar Convênio...</option>';
            data.convenios.forEach(convenio => {
                html += '        <option value="' + convenio.id + '">' + convenio.descr + '</option>';
            });
            html += '        </select>';
            html += '    </div>';
            html += '    <div class="col-md-4 form-group">';
            html += '        <label for="num-convenio" class="custom-label-form">Nº Convênio</label>';
            html += '        <input id="num-convenio" name="num_convenio[]" class="form-control" autocomplete="off" type="text" value="" maxlength="45">';
            html += '    </div>';
            html += '    <div class="col-md-2 form-group d-flex">';
            html += '        <button class="btn btn-danger mt-auto mr-1" style="height:calc(1.5em + 0.75rem + 8px)" onclick="delete_convenio_pessoa($(this)); return false;">';
            html += '            <i class="my-icon fas fa-trash"></i>';
            html += '        </button>';
            html += '        <button class="btn btn-success mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_convenio_pessoa($(this)); return false;">';
            html += '            <i class="my-icon fas fa-plus"></i>';
            html += '        </button>';
            html += '    </div>';
            html += '</div>';
            $('#lista-convenio-pessoa').html(html);
        }

        if (data.especialidade_pessoa.length != 0) {
            $('#pessoaModal').find('#lista-especialidade').empty();
            data.especialidade_pessoa.forEach((especialidade_pessoa, i) => {
                html = '<div class="row">';
                html += '    <div class="col-md-10 form-group">';
                if (i == 0) html += '<label for="especialidade" class="custom-label-form">Área da saúde *</label>';
                html += '            <select id="especialidade" name="especialidade[]" class="form-control custom-select">';
                html += '               <option value="' + especialidade_pessoa.id + '">' + especialidade_pessoa.descr + '</option>';
                data.especialidades.forEach(especialidade => {
                    html += '           <option value="' + especialidade.id + '"';
                    if (especialidade.id == especialidade_pessoa.id_especialidade) html += 'selected';
                    html += '>';
                    html += especialidade.descr;
                    html += '           </option>';
                });
                html += '        </select>';
                html += '    </div>';
                html += '    ';
                html += '    <div class="col-md-2 form-group d-flex">';
                html += '        <button class="btn btn-danger mt-auto mr-1" style="height:calc(1.5em + 0.75rem + 8px)" onclick="delete_especialidade_pessoa($(this)); return false;">';
                html += '            <i class="my-icon fas fa-trash"></i>';
                html += '        </button>';
                html += '        <button class="btn btn-success mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_especialidade_pessoa($(this)); return false;">';
                html += '            <i class="my-icon fas fa-plus"></i>';
                html += '        </button>';
                html += '    </div>';
                html += '</div>';
                $('#pessoaModal').find('#lista-especialidade').append(html);
            });
        } else {
            html = '<div class="row">';
            html += '    <div class="col-md-10 form-group">';
            html += '        <label for="especialidade" class="custom-label-form">Área da saúde *</label>';
            html += '        <select id="especialidade" name="especialidade[]" class="form-control custom-select">';
            html += '            <option value="0">Selecionar especialidade...</option>';
            data.especialidades.forEach(especialidade => {
                html += '        <option value="' + especialidade.id + '">';
                html += especialidade.descr;
                html += '        </option>';
            });
            html += '        </select>';
            html += '    </div>';
            html += '    ';
            html += '    <div class="col-md-2 form-group d-flex">';
            html += '        <button class="btn btn-danger mt-auto mr-1" style="height:calc(1.5em + 0.75rem + 8px)" onclick="delete_especialidade_pessoa($(this)); return false;">';
            html += '            <i class="my-icon fas fa-trash"></i>';
            html += '        </button>';
            html += '        <button class="btn btn-success mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_especialidade_pessoa($(this)); return false;">';
            html += '            <i class="my-icon fas fa-plus"></i>';
            html += '        </button>';
            html += '    </div>';
            html += '</div>';
            $('#pessoaModal').find('#lista-especialidade').html(html);
        }


        if (data.empresa_pessoa.length != 0) {
            $('#pessoaModal').find('#lista-empresa').empty();
            data.empresa_pessoa.forEach((empresa_pessoa, i) => {
                html = '<div class="row">';
                html += '    <div class="col-md-10 form-group">';
                if (i == 0) html += '<label for="empresa" class="custom-label-form">Empresa *</label>';
                html += '            <select id="empresa" name="empresa[]" class="form-control custom-select">';
                html += '               <option value="' + empresa_pessoa.id + '">' + empresa_pessoa.descr + '</option>';
                data.empresas.forEach(empresa => {
                    html += '           <option value="' + empresa.id + '"';
                    if (empresa.id == empresa_pessoa.empresa) html += 'selected';
                    html += '>';
                    html += empresa.descr;
                    html += '           </option>';
                });
                html += '        </select>';
                html += '    </div>';
                html += '    ';
                html += '    <div class="col-md-2 form-group d-flex">';
                html += '        <button class="btn btn-danger mt-auto mr-1" style="height:calc(1.5em + 0.75rem + 8px)" onclick="delete_empresa_pessoa($(this)); return false;">';
                html += '            <i class="my-icon fas fa-trash"></i>';
                html += '        </button>';
                html += '        <button class="btn btn-success mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_empresa_pessoa($(this)); return false;">';
                html += '            <i class="my-icon fas fa-plus"></i>';
                html += '        </button>';
                html += '    </div>';
                html += '</div>';
                $('#pessoaModal').find('#lista-empresa').append(html);
            });
        } else {
            html = '<div class="row">';
            html += '    <div class="col-md-10 form-group">';
            html += '        <label for="empresa" class="custom-label-form">Empresa *</label>';
            html += '        <select id="empresa" name="empresa[]" class="form-control custom-select">';
            html += '            <option value="0">Selecionar empresa...</option>';
            data.empresas.forEach(empresas => {
                html += '        <option value="' + empresas.id + '">';
                html += empresas.descr;
                html += '        </option>';
            });
            html += '        </select>';
            html += '    </div>';
            html += '    ';
            html += '    <div class="col-md-2 form-group d-flex">';
            html += '        <button class="btn btn-danger mt-auto mr-1" style="height:calc(1.5em + 0.75rem + 8px)" onclick="delete_empresa_pessoa($(this)); return false;">';
            html += '            <i class="my-icon fas fa-trash"></i>';
            html += '        </button>';
            html += '        <button class="btn btn-success mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_empresa_pessoa($(this)); return false;">';
            html += '            <i class="my-icon fas fa-plus"></i>';
            html += '        </button>';
            html += '    </div>';
            html += '</div>';
            $('#pessoaModal').find('#lista-empresa').html(html);
        }
        $('#pessoaModal').find('#nao-gerar-faturamento').prop("checked", data.gera_faturamento == 'N')
        $('#pessoaModal').find('#nao-gerar-faturamento').change()
        console.log(data.gera_faturamento == 'N')
        if (data.gera_faturamento == 'N') {
            console.log(formatDataBr(data.d_naofaturar))
            $('#pessoaModal').find('#data').val(formatDataBr(data.d_naofaturar));
            if (data.aplicar_desconto == "S") {
                $('#pessoaModal').find('#aplicar-desconto').val("S");
            }
            if (data.aplicar_desconto == "N") {
                $('#pessoaModal').find('#aplicar-desconto').val("N");
            }
            console.log(data.gera_faturamento)

        }
        else {
            $('#pessoaModal').find('#data').val('');
            $('#pessoaModal').find('#aplicar-desconto').val("");
        }




        $('#pessoaModal').modal('show');
        $('#pessoaModal').on('hidden.bs.modal', function () {
            $('#foto-preview').attr('src', '/saude-beta/img/foto_purple.png');
            $("[for=foto]").html("Escolher arquivo...");
            $('#password').prop("required", true);

            html = '<div class="row">';
            html += '    <div class="col-md-10 form-group">';
            html += '        <label for="especialidade" class="custom-label-form">Área da saúde *</label>';
            html += '        <select id="especialidade" name="especialidade[]" class="form-control custom-select">';
            html += '            <option value="0">Selecionar especialidade...</option>';
            data.especialidades.forEach(especialidade => {
                html += '        <option value="' + especialidade.id + '">';
                html += especialidade.descr;
                html += '        </option>';
            });
            html += '        </select>';
            html += '    </div>';
            html += '    ';
            html += '    <div class="col-md-2 form-group d-flex">';
            html += '        <button class="btn btn-danger mt-auto mr-1" style="height:calc(1.5em + 0.75rem + 8px)" onclick="delete_especialidade_pessoa($(this)); return false;">';
            html += '            <i class="my-icon fas fa-trash"></i>';
            html += '        </button>';
            html += '        <button class="btn btn-success mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_especialidade_pessoa($(this)); return false;">';
            html += '            <i class="my-icon fas fa-plus"></i>';
            html += '        </button>';
            html += '    </div>';
            html += '</div>';
            $('#lista-especialidade').html(html);

            html = '<div class="row">';
            html += '    <div class="col-md-6 form-group">';
            html += '        <label for="convenio" class="custom-label-form">Convênio</label>';
            html += '        <select id="convenio" name="convenio[]" class="form-control custom-select">';
            html += '            <option value="0">Selecionar Convênio...</option>';
            data.convenios.forEach(convenio => {
                html += '        <option value="' + convenio.id + '">' + convenio.descr + '</option>';
            });
            html += '        </select>';
            html += '    </div>';
            html += '    <div class="col-md-4 form-group">';
            html += '        <label for="num-convenio" class="custom-label-form">Nº Convênio</label>';
            html += '        <input id="num-convenio" name="num_convenio[]" class="form-control" autocomplete="off" type="text" value="" maxlength="45">';
            html += '    </div>';
            html += '    <div class="col-md-2 form-group d-flex">';
            html += '        <button class="btn btn-danger mt-auto mr-1" style="height:calc(1.5em + 0.75rem + 8px)" onclick="delete_convenio_pessoa($(this)); return false;">';
            html += '            <i class="my-icon fas fa-trash"></i>';
            html += '        </button>';
            html += '        <button class="btn btn-success mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_convenio_pessoa($(this)); return false;">';
            html += '            <i class="my-icon fas fa-plus"></i>';
            html += '        </button>';
            html += '    </div>';
            html += '</div>';
            $('#lista-convenio-pessoa').html(html);
        })
    });
}

function delete_convenio_pessoa(_this) {
    console.log(_this);
    if ($('#lista-convenio-pessoa > div').length > 1) {
        _this.parent().parent().remove();
    } else {
        $('#lista-convenio-pessoa #num-convenio').val('');
        $('#lista-convenio-pessoa #convenio').val(0);
    }
}


function add_convenio_pessoa(_this) {
    console.log(_this);
    $.get('/saude-beta/convenio/listar', function (data) {
        data = $.parseJSON(data);
        var html = '<div class="row">';
        html += '    <div class="col-md-6 form-group">';
        html += '        <select id="convenio" name="convenio[]" class="form-control custom-select">';
        html += '            <option value="0">Selecionar Convênio...</option>';
        data.forEach(convenio => {
            html += '        <option value="' + convenio.id + '">' + convenio.descr + '</option>';
        });
        html += '        </select>';
        html += '    </div>';
        html += '    <div class="col-md-4 form-group">';
        html += '        <input id="num-convenio" name="num_convenio[]" class="form-control" autocomplete="off" type="text" value="" maxlength="45">';
        html += '    </div>';
        html += '    <div class="col-md-2 form-group d-flex">';
        html += '        <button class="btn btn-danger mt-auto mr-1" style="height:calc(1.5em + 0.75rem + 8px)" onclick="delete_convenio_pessoa($(this)); return false;">';
        html += '            <i class="my-icon fas fa-trash"></i>';
        html += '        </button>';
        html += '        <button class="btn btn-success mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_convenio_pessoa($(this)); return false;">';
        html += '            <i class="my-icon fas fa-plus"></i>';
        html += '        </button>';
        html += '    </div>';
        html += '</div>';
        $('#lista-convenio-pessoa').append(html);
    });
}

function delete_especialidade_pessoa(_this) {
    console.log(_this);
    if ($('#lista-especialidade > div').length > 1) {
        _this.parent().parent().remove();
    } else {
        $('#lista-especialidade #especialidade').val(0);
    }
}

function delete_empresa_pessoa(_this) {
    try {
        console.log(document.getElementById($(':focus').attr('id')).tagName);
    } catch(err) {
        if ($('#lista-empresa > div').length > 2) {
            _this.parent().parent().remove();
            if ($('#lista-empresa > div').length == 2) {
                var conteudo = $($('#lista-empresa > div')[1]).find(".col-md-10").html();
                if (conteudo.indexOf("Empresa *") == -1) {
                    $($('#lista-empresa > div')[1]).find(".col-md-10").html(
                        '<label for="empresa" class="custom-label-form">Empresa *</label>' +
                        conteudo
                    );
                }
            }
        } else {
            $('#lista-empresa #empresa').val(0);
        }
    }
}

function add_especialidade_pessoa(_this) {
    console.log(_this);
    $.get('/saude-beta/especialidade/listar', function (data) {
        data = $.parseJSON(data);
        var html = '<div class="row">';
        html += '    <div class="col-md-10 form-group">';
        html += '        <select id="especialidade" name="especialidade[]" class="form-control custom-select">';
        html += '            <option value="0">Selecionar especialidade...</option>';
        data.forEach(especialidade => {
            html += '        <option value="' + especialidade.id + '">';
            html += especialidade.descr;
            html += '        </option>';
        });
        html += '        </select>';
        html += '    </div>';
        html += '    ';
        html += '    <div class="col-md-2 form-group d-flex">';
        html += '        <button class="btn btn-danger mt-auto mr-1" style="height:calc(1.5em + 0.75rem + 8px)" onclick="delete_especialidade_pessoa($(this)); return false;">';
        html += '            <i class="my-icon fas fa-trash"></i>';
        html += '        </button>';
        html += '        <button class="btn btn-success mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_especialidade_pessoa($(this)); return false;">';
        html += '            <i class="my-icon fas fa-plus"></i>';
        html += '        </button>';
        html += '    </div>';
        html += '</div>';
        $('#lista-especialidade').append(html);
    });
}

function add_empresa_plano(_this) {
    console.log(_this);
    $.get('/saude-beta/pessoa/listar-empresas', function (data) {
        data = $.parseJSON(data);
        var html = '<div class="row">';
        html += '    <div class="col-md-10 form-group">';
        html += '        <select id="empresa" name="empresa[]" class="form-control custom-select">';
        html += '            <option value="0">Selecionar empresa...</option>';
        data.forEach(empresas => {
            html += '        <option value="' + empresas.id + '">';
            html += empresas.descr;
            html += '        </option>';
        });
        html += '        </select>';
        html += '    </div>';
        html += '    ';
        html += '    <div class="col-md-2 form-group d-flex">';
        html += '        <button class="btn btn-danger mt-auto mr-1" style="height:calc(1.5em + 0.75rem + 8px)" onclick="delete_empresa_pessoa($(this)); return false;">';
        html += '            <i class="my-icon fas fa-trash"></i>';
        html += '        </button>';
        html += '        <button class="btn btn-success mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_empresa_plano($(this)); return false;">';
        html += '            <i class="my-icon fas fa-plus"></i>';
        html += '        </button>';
        html += '    </div>';
        html += '</div>';
        $(_this).parent().parent().parent().append(html);
    });
}

function add_empresa_pessoa(_this) {
    console.log(_this);
    $.get('/saude-beta/pessoa/listar-empresas', function (data) {
        data = $.parseJSON(data);
        var html = '<div class="row">';
        html += '    <div class="col-md-10 form-group">';
        html += '        <select id="empresa" name="empresa[]" class="form-control custom-select">';
        html += '            <option value="0">Selecionar empresa...</option>';
        data.forEach(empresas => {
            html += '        <option value="' + empresas.id + '">';
            html += empresas.descr;
            html += '        </option>';
        });
        html += '        </select>';
        html += '    </div>';
        html += '    ';
        html += '    <div class="col-md-2 form-group d-flex">';
        html += '        <button class="btn btn-danger mt-auto mr-1" style="height:calc(1.5em + 0.75rem + 8px)" onclick="delete_empresa_pessoa($(this)); return false;">';
        html += '            <i class="my-icon fas fa-trash"></i>';
        html += '        </button>';
        html += '        <button class="btn btn-success mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_empresa_pessoa($(this)); return false;">';
        html += '            <i class="my-icon fas fa-plus"></i>';
        html += '        </button>';
        html += '    </div>';
        html += '</div>';
        $(_this).parent().parent().parent().append(html);
    });
}
function add_empresa_pessoa2(_this) {
    console.log(_this);
    $.get('/saude-beta/pessoa/listar-empresas', function (data) {
        data = $.parseJSON(data);
        var html = '<div class="row">';
        html += '    <div class="col-md-10 form-group">';
        html += '        <select id="empresa" name="empresa[]" class="form-control custom-select">';
        html += '            <option value="0">Selecionar empresa...</option>';
        data.forEach(empresas => {
            html += '        <option value="' + empresas.id + '">';
            html += empresas.descr;
            html += '        </option>';
        });
        html += '        </select>';
        html += '    </div>';
        html += '    ';
        html += '    <div class="col-md-2 form-group d-flex">';
        html += '        <button class="btn btn-danger mt-auto mr-1" style="height:calc(1.5em + 0.75rem + 8px)" onclick="delete_empresa_pessoa($(this)); return false;">';
        html += '            <i class="my-icon fas fa-trash"></i>';
        html += '        </button>';
        html += '        <button class="btn btn-success mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_empresa_pessoa2($(this)); return false;">';
        html += '            <i class="my-icon fas fa-plus"></i>';
        html += '        </button>';
        html += '    </div>';
        html += '</div>';
        $(_this).parent().parent().parent().append(html);
    });
}

function editar_doc_modelo(id_doc_modelo) {
    $.get(
        '/saude-beta/documento-modelo/mostrar/' + id_doc_modelo,
        function (data) {
            data = $.parseJSON(data);
            $('#id').val(data.id);
            $('#titulo').val(data.titulo);
            $('#corpo').summernote("code", data.corpo);
            $('#criarDocModeloModalLabel').html('Editar Modelo de Documento - ' + data.id);
            $('#criarDocModeloModal').modal('show');
            $('#criarDocModeloModal').on('hidden.bs.modal', function () {
                $('#criarDocModeloModalLabel').html('Criar Modelo de Documento');
            });
        }
    );
}

function editar_forma_pag(id_forma_pag) {
    $.get('/saude-beta/forma-pag/mostrar/' + id_forma_pag, function (data) {
        data = $.parseJSON(data);
        $('#id').val(data.id);
        $('#descr').val(data.descr);
        $('#max_parcelas').val(data.max_parcelas);
        $('#dias_entre_parcela').val(data.dias_entre_parcela);
        $('#avista_prazo').val(data.avista_prazo);
        $('#formaPagModal').modal('show');
    });
}


function salvar_convenio() {
    $.post('/saude-beta/convenio/salvar', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: $("#convenioModal #id").val(),
        descr: $("#convenioModal #descr").val(),
        prazo: $("#convenioModal #prazo").val(),
        quem_paga: $("#convenioModal #quem-paga").val(),
        cliente_id: $("#convenioModal #cliente_id").val()
    }, function (data, status) {
        console.log(data + ' | ' + status)
        if (data.error) {
            alert(data.error)
        }
        else {
            alert('Alterações salvar com sucesso')
            location.reload();
            $("#convenioModal").modal('hide')
        }
    })
}
var klk
function editar_convenio(id_convenio) {
    $.get('/saude-beta/convenio/mostrar/' + id_convenio, function (data) {
        data = $.parseJSON(data);
        klk = data
        $('#id').val(data.convenio.id);
        $('#descr').val(data.convenio.descr);
        $('#id_tabela_preco').val(data.convenio.id_tabela_preco);
        $('#cliente_nome').val(data.convenio.cliente_nome);
        $('#cliente_id').val(data.convenio.id_pessoa);
        $('#convenioModal #prazo').val(data.convenio.prazo)
        $('#quem_paga').val((data.convenio.quem_paga == 'C'));
        $('#convenioModal').modal('show');
        $("#convenioModal #tabela_precos").empty()
        data.precos_por_convenio.forEach(plano => {
            html = ' <tr>'
            html += ' <td width="50%">' + plano.descr + '</td>'
            html += ' <td width="30%">' + plano.descr_empresa + '</td>'
            html += ' <td width="10%" class="text-right">' + plano.valor + '</td>'
            html += ' <td width="10%" onclick="remover_preco_convenio(' + plano.id + ')"><img style="    max-width: 25px;opacity: 0.9;" src="http://vps.targetclient.com.br/saude-beta/img/lixeira-de-reciclagem.png"></td>'
            html += ' </tr>'
            $("#convenioModal #tabela_precos").append(html)
        })
    });
}

function editar_evolucao_tipo(id_evolucao_tipo) {
    $.get('/saude-beta/evolucao-tipo/mostrar/' + id_evolucao_tipo, function (data) {
        data = $.parseJSON(data);
        $('#id').val(data.id);
        $('#descr').val(data.descr);
        $('#prioritario').prop('checked', data.prioritario);
        $('#evolucaoTipoModal').modal('show');
    });
}

function editar_procedimento(id_procedimento) {
    $.get('/saude-beta/procedimento/mostrar/' + id_procedimento, function (data) {
        data = $.parseJSON(data);
        $('#id').val(data.id);
        if (data.id_especialidade == null) $('#especialidade').val('');
        else $('#especialidade').val(data.id_especialidade);
        $('#cod-tuss').val(data.cod_tuss);
        $('#descr').val(data.descr);
        $('#descr-resumida').val(data.descr_resumida);
        $('#tempo-procedimento').val(data.tempo_procedimento);
        $('#obs').val(data.obs);
        $('#dente_regiao').prop('checked', (data.dente_regiao));
        $('#face').prop('checked', (data.face));
        $('#faturar_val').val(data.faturar);
        document.getElementById("faturar").checked = document.getElementById("faturar_val").value == 1;
        $('#procedimentoModal').modal('show');
    });
}

function editar_especialidade(id_especialidade) {
    $.get('/saude-beta/especialidade/mostrar/' + id_especialidade, function (data) {
        data = $.parseJSON(data);
        $('#id').val(data.id);
        $('#descr').val(data.descr);
        document.getElementById("externo").checked = data.externo;
        $('#especialidadeModal').modal('show');
    });
}

function editar_medicamento(id_medicamento) {
    $.get('/saude-beta/medicamento/mostrar/' + id_medicamento, function (data) {
        data = $.parseJSON(data);
        $('#id').val(data.id);
        $('#descr').val(data.descr);
        $('#ativo').prop('checked', data.ativo);
        $('#uso').val(data.uso);
        $('#tipo').val(data.tipo);
        $('#unidade').val(data.unidade);
        $('#posologia').val(data.posologia);
        $('#criarMedicamentoModal').modal('show');
    });
}
function abrir_desconto_modal() {
    $('#descontoGeralModal').modal('show')
    $.get(
        '/saude-beta/parametros/mostrar-param-atual', {},
        function (data, status) {
            console.log(data + ' | ' + status)
            data = $.parseJSON(data);
            $("#descontoGeralModal #desconto").val(data.desconto_geral)
        }
    )
}
function editar_tabela_precos(id_tabela_precos) {
    $.get('/saude-beta/tabela-precos/mostrar/' + id_tabela_precos, function (data) {
        data = $.parseJSON(data);
        if (data.desconto_associados === null) data.desconto_associados = 0;
        $('#tabelaPrecosModal #id').val(data.id);
        $('#tabelaPrecosModal #descr').val(data.descr);
        $('#tabelaPrecosModal #status').val(data.status);
        $('#tabelaPrecosModal #max_atv_semana').val(data.max_atv_semana);
        $('#tabelaPrecosModal #max_atv').val(data.max_atv)
        $("#tabelaPrecosModal #valor").val(data.valor);
        $("#tabelaPrecosModal #npessoas").val(data.n_pessoas);
        $("#tabelaPrecosModal #desc_associado").val(data.desconto_associados)
        $('#tabelaPrecosModal #descr_contrato').val(data.descr_contrato)
        testando = data
        switch (data.vigencia) {
            case 30:
                $("#tabelaPrecosModal #vigencia").val("M");
                break;
            case 60:
                $("#tabelaPrecosModal #vigencia").val("B");
                break;
            case 90:
                $("#tabelaPrecosModal #vigencia").val("T");
                break;
            case 180:
                $("#tabelaPrecosModal #vigencia").val("S");
                break;
            case 360:
                $("#tabelaPrecosModal #vigencia").val("A");
                break;
        }
        if (data.pre_agendamento == true) $("#tabelaPrecosModal #tipo_agendamento").val(1)
        if (data.reabilitacao == true) $("#tabelaPrecosModal #tipo_agendamento").val(2)
        if (data.habilitacao == true) $("#tabelaPrecosModal #tipo_agendamento").val(3)
        if (data.repor_som_mes == true) $("#tabelaPrecosModal #repor_som_mes").prop('checked', true)
        else $("#tabelaPrecosModal #repor_som_mes").prop('checked', false)
        if (data.desconto_geral == true) $("#tabelaPrecosModal #usar_desconto_padrao").prop('checked', true)
        else $("#tabelaPrecosModal #usar_desconto_padrao").prop('checked', false)
        if (data.contrato == 'S') $("#tabelaPrecosModal #gerar_contrato").prop('checked', true)
        else $("#tabelaPrecosModal #gerar_contrato").prop('checked', false)
        listar_empresas_por_plano(id_tabela_precos)
        listar_modalidade_por_plano(id_tabela_precos);

    });
}

function listar_empresas_por_plano($id) {
    $.get(
        '/saude-beta/tabela-precos/listar-empresas/' + $id,
        function (data, status) {
            console.log(data + ' | ' + status)
            data = $.parseJSON(data)
            if (data.empresa_plano.length != 0) {
                $('#tabelaPrecosModal').find('#lista-empresa').empty();
                data.empresa_plano.forEach((empresa_plano, i) => {
                    html = '<div class="row">';
                    html += '    <div class="col-md-10 form-group">';
                    if (i == 0) html += '<label for="empresa" class="custom-label-form">Empresa *</label>';
                    html += '            <select id="empresa" name="empresa[]" class="form-control custom-select">';
                    html += '            <option value="0">Selecionar empresa...</option>';
                    data.empresas.forEach(empresa => {
                        html += '           <option value="' + empresa.id + '"';
                        if (empresa.id == empresa_plano.id) html += 'selected';
                        html += '>';
                        html += empresa.descr;
                        html += '           </option>';
                    });
                    html += '        </select>';
                    html += '    </div>';
                    html += '    ';
                    html += '    <div class="col-md-2 form-group d-flex">';
                    html += '        <button class="btn btn-danger mt-auto mr-1" style="height:calc(1.5em + 0.75rem + 8px)" onclick="delete_empresa_pessoa($(this)); return false;">';
                    html += '            <i class="my-icon fas fa-trash"></i>';
                    html += '        </button>';
                    html += '        <button class="btn btn-success mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_empresa_pessoa($(this)); return false;">';
                    html += '            <i class="my-icon fas fa-plus"></i>';
                    html += '        </button>';
                    html += '    </div>';
                    html += '</div>';
                    $('#tabelaPrecosModal').find('#lista-empresa').append(html);
                });
            } else {
                html = '<div class="row">';
                html += '    <div class="col-md-10 form-group">';
                html += '        <label for="empresa" class="custom-label-form">Empresa *</label>';
                html += '        <select id="empresa" name="empresa[]" class="form-control custom-select">';
                html += '            <option value="0">Selecionar empresa...</option>';
                data.empresas.forEach(empresas => {
                    html += '        <option value="' + empresas.id + '">';
                    html += empresas.descr;
                    html += '        </option>';
                });
                html += '        </select>';
                html += '    </div>';
                html += '    ';
                html += '    <div class="col-md-2 form-group d-flex">';
                html += '        <button class="btn btn-danger mt-auto mr-1" style="height:calc(1.5em + 0.75rem + 8px)" onclick="delete_empresa_pessoa($(this)); return false;">';
                html += '            <i class="my-icon fas fa-trash"></i>';
                html += '        </button>';
                html += '        <button class="btn btn-success mt-auto" style="height:calc(1.5em + 0.75rem + 8px)" onclick="add_empresa_pessoa($(this)); return false;">';
                html += '            <i class="my-icon fas fa-plus"></i>';
                html += '        </button>';
                html += '    </div>';
                html += '</div>';
                $('#tabelaPrecosModal').find('#lista-empresa').html(html);
            }
        }
    )
}

function editar_etiqueta(id_etiqueta) {
    $.get('/saude-beta/etiqueta/mostrar/' + id_etiqueta, function (data) {
        data = $.parseJSON(data);
        $('#etiquetaModal #id').val(data.id);
        $('#etiquetaModal #descr').val(data.descr);
        $('#etiquetaModal #cor').val(data.cor);
        $('#etiquetaModal #colorpaletteselected').css('color', data.cor);
        $('#etiquetaModal').modal('show');
    });
}

function editar_agenda_status(id_agenda_status) {
    $.get('/saude-beta/agenda-status/mostrar/' + id_agenda_status, function (data) {
        data = $.parseJSON(data);
        $('#agendaStatusModal #id').val(data.id);
        $('#agendaStatusModal #descr').val(data.descr);
        $('#agendaStatusModal #cor').val(data.cor);
        $('#agendaStatusModal [data-input_id="#cor"] #colorpaletteselected').css('color', data.cor);
        $('#agendaStatusModal #cor_letra').val(data.cor_letra);
        $('#agendaStatusModal [data-input_id="#cor_letra"] #colorpaletteselected').css('color', data.cor_letra);
        $('#agendaStatusModal #permite_editar').prop('checked', (data.permite_editar));
        $('#agendaStatusModal #permite_reagendar').prop('checked', (data.permite_reagendar));
        $('#agendaStatusModal #permite_fila_espera').prop('checked', (data.permite_fila_espera));
        $('#agendaStatusModal #caso_reagendar').prop('checked', (data.caso_reagendar));
        $('#agendaStatusModal #caso_confirmar').prop('checked', (data.caso_confirmar));
        $('#agendaStatusModal #caso_cancelar').prop('checked', (data.caso_cancelar));
        $('#agendaStatusModal #libera_horario').prop('checked', (data.libera_horario));
        $('#agendaStatusModal').modal('show');
    });
}

function editar_agenda_confirmacao(id_agenda_confirmacao) {
    $.get('/saude-beta/agenda-confirmacao/mostrar/' + id_agenda_confirmacao, function (data) {
        data = $.parseJSON(data);
        $('#agendaConfirmModal #id').val(data.id);
        $('#agendaConfirmModal #descr').val(data.descr);
        $('#agendaConfirmModal').modal('show');
    });
}

function editar_tipo_procedimento(id_tipo_procedimento) {
    $.get('/saude-beta/tipo-procedimento/mostrar/' + id_tipo_procedimento, function (data) {
        data = $.parseJSON(data);
        $('#tipoprocedimentoModal').modal('show');
        $('#tipoprocedimentoModal #id').val(data.id);
        $('#tipoprocedimentoModal #descr').val(data.descr);
        $('#tipoprocedimentoModal #tempo-procedimento').val(data.tempo_procedimento);

    });
}

function deletar_agenda_confirmacao(id_agenda_confirm) {
    $.get("/saude-beta/agenda-confirmacao/mostrar/" + id_agenda_confirm, function (
        data
    ) {
        data = $.parseJSON(data);
        if (window.confirm("Deseja excluir tipo de confirmação '" + data.descr + "'?")) {
            $.post(
                "/saude-beta/agenda-confirmacao/deletar", {
                _token: $("meta[name=csrf-token]").attr("content"),
                id: data.id
            },
                function (data, status) {
                    console.log(status + " | " + data);
                    if (data.error != undefined) {
                        alert(data.error);
                    } else {
                        document.location.reload(true);
                    }
                }
            );
        }
    });
}

function deletar_tipo_procedimento(id_tipo_procedimento) {
    $.get("/saude-beta/tipo-procedimento/mostrar/" + id_tipo_procedimento, function (
        data
    ) {
        data = $.parseJSON(data);
        if (window.confirm("Deseja excluir tipo de procedimento '" + data.descr + "'?")) {
            $.post(
                "/saude-beta/tipo-procedimento/deletar", {
                _token: $("meta[name=csrf-token]").attr("content"),
                id_tipo_procedimento: data.id
            },
                function (data, status) {
                    console.log(status + " | " + data);
                    if (data.error != undefined) {
                        alert(data.error);
                    } else {
                        document.location.reload(true);
                    }
                }
            );
        }
    });
}
function procurar_modalidades(id_tabela_precos) {
    $.get(
        '/saude-beta/tabela-precos/procurar-modalidades/' + id_tabela_precos,
        (data, status) => {
            $('#modalidade-id').empty();
            console.log(data + ' | ' + status);
            data.forEach(modalidade => {
                html = ' <option value="' + modalidade.id + '">'
                html += modalidade.descr
                html += '</option>'
                $('#modalidade-id').append(html);
            })

        }
    )
}
function listar_precos(id_tabela_precos) {
    $.get('/saude-beta/comissao_exclusiva/listar_tabela/' + id_tabela_precos, function (data) {
        data = $.parseJSON(data);
        procurar_modalidades(id_tabela_precos)
        $("#precosModal #id-tabela-preco").val(id_tabela_precos)
        $('#procedimento-nome').val('');
        $('#procedimento-id').val('');
        $('#valor').val('');
        $('#valor_prazo').val('');
        $('#valor_minimo').val('');
        $('#id-tabela-preco').val(id_tabela_precos);
        $('#table-precos > tbody').empty();
        data.Comissao_exclusiva.forEach(comissao => {
            html = '<tr data-preco_id="' + comissao.id + '" data-id_especialidade="' + comissao.id_especialidade + '" data-descr_procedimento="' + comissao.descr + '">';
            html += '    <td width="40%">';
            html += '        <span>' + comissao.descr + '</span>';
            html += '        <input id="procedimento_nome"';
            html += '            name="procedimento_nome"  ';
            html += '            class="form-control autocomplete" ';
            html += '            placeholder="Digitar Nome do procedimento..."';
            html += '            data-input="#procedimento_id_' + comissao.id + '"';
            html += '            data-table="procedimento" ';
            html += '            data-column="descr" ';
            html += '            data-filter_col="id_especialidade" ';
            html += '            data-filter="' + comissao.id_especialidade + '" ';
            html += '            type="text" ';
            html += '            autocomplete="off"';
            html += '            style="display:none"';
            html += '            required>';
            html += '        <input id="procedimento_id_' + comissao.id + '" name="procedimento_id" type="hidden">';
            html += '    </td>';
            html += '    <td width="20%">';
            html += '        <span>' + comissao.descr_especialidade + '</span>';
            html += '        <select id="especialidade" name="especialidade[]" class="form-control custom-select" style="display:none">';
            data.especialidades.forEach(especialidade => {
                html += '        <option value="' + especialidade.id + '"';
                if (especialidade.id == comissao.id_especialidade) html += ' selected ';
                html += '>';
                html += especialidade.descr;
                html += '        </option>';
            });
            html += '        </select>';
            html += '    </td>';
            html += '    <td width="10%" class="text-right">';
            html += '        <span>' + comissao.de2 + '</span>';
            html += '        <input id="valor" name="valor" class="form-control money" autocomplete="off" type="text" style="display:none" required>';
            html += '    </td>';
            html += '    <td width="10%" class="text-right">';
            html += '        <span>';
            if (comissao.ate2 != null) html += comissao.ate2;
            else html += 'N/A';
            html += '        </span>';
            html += '        <input id="valor_prazo" name="valor_prazo" class="form-control money" autocomplete="off" type="text" style="display:none">';
            html += '    </td>';
            html += '    <td width="10%" class="text-right">';
            html += '        <span>';
            if (comissao.valor2 != null) html += 'R$ ' + comissao.valor2;
            else html += 'N/A';
            html += '        </span>';
            html += '        <input id="valor_minimo" name="valor_minimo" class="form-control money" autocomplete="off" type="text" style="display:none">';
            html += '    </td>';
            html += '    <td width="10%" class="text-center btn-table-action">';

            // html += '       <i class="my-icon far fa-edit"      onclick="editar_preco(' + comissao.id + ')"></i>';
            html += '       <i class="my-icon far fa-trash-alt" onclick="deletar_preco(' + comissao.id + ')"></i>';

            html += '       <i class="my-icon far fa-check"     onclick="salvar_edicao(' + comissao.id + ')" style="display:none"></i>';
            html += '       <i class="my-icon far fa-times"     onclick="cancelar_edicao(' + comissao.id + ')" style="display:none"></i>';
            html += '    </td>';
            html += '</tr>';
            $('#table-precos > tbody').append(html);
        });

        $('#table-precos .autocomplete').each(function () {
            $(this).keyup(function (e) {
                if (!e.ctrlKey && !(e.ctrlKey && e.keyCode == 32) && e.keyCode != 9 && e.keyCode != 13 && e.keyCode != 38 && e.keyCode != 40) {
                    autocomplete($(this));
                }
            });

            $(this).keydown(function (e) {
                // 9 - TAB | 13 - ENTER | 38 = CIMA | 40 = BAIXO
                if (e.keyCode == 9 || e.keyCode == 13 || e.keyCode == 38 || e.keyCode == 40) {
                    if (e.keyCode == 13) e.preventDefault();
                    seta_autocomplete(e.keyCode, $(this));
                }
            });
        });

        $('#precosModal #filtro-procedimento').keyup(function (e) {
            if (e.keyCode == 13) {
                filtrar_tabela_precos();
            }
        });

        $("#table-precos .money").mask("############.00", { reverse: true });

        if (!$('#precosModal').hasClass('show')) $('#precosModal').modal('show');
    });
}

function filtrar_tabela_precos() {
    var _fdescr = $('#precosModal #filtro-procedimento').val().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, ""),
        _fesp = $('#precosModal #filtro-especialidade').val();

    $('#table-precos tr[data-preco_id]').each(function () {
        console.log('Filtrar Tabela de Preços:');
        console.log(_fesp + ' | ' + $(this).data().id_especialidade);
        console.log(_fdescr + ' | ' + $(this).data().descr_procedimento);
        if ((_fesp == 0 || _fesp == $(this).data().id_especialidade) &&
            (_fdescr == '' || $(this).data().descr_procedimento.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, "").includes(_fdescr))) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

function clonar_tabela_precos(id_tabela_preco) {
    $.post(
        '/saude-beta/tabela-precos/clonar-precos', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: id_tabela_preco
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                document.location.reload(true);
            }
        }
    );

}

function editar_preco(preco_id) {
    var _this = $('#table-precos [data-preco_id="' + preco_id + '"]');

    _this.find('span').hide();
    _this.find('input').show();
    _this.find('select').show();
    _this.find('[onclick="editar_preco(' + preco_id + ')"]').hide();
    _this.find('[onclick="deletar_preco(' + preco_id + ')"]').hide();
    _this.find('[onclick="salvar_edicao(' + preco_id + ')"]').show();
    _this.find('[onclick="cancelar_edicao(' + preco_id + ')"]').show();
    _this.find('#especialidade').change(function () {
        _this.find('#procedimento_nome').val('');
        _this.find('#procedimento_id_' + preco_id).val('');
        _this.find('#procedimento_nome').data('filter', $(this).val());
    })

    $.get(
        '/saude-beta/preco/mostrar/' + preco_id,
        function (data) {
            data = $.parseJSON(data);
            _this.find('#procedimento_id_' + preco_id).val(data.id_procedimento);
            _this.find('#procedimento_nome').val(data.descr);
            _this.find('#valor').val(data.valor);
            _this.find('#valor_prazo').val(data.valor_prazo);
            _this.find('#valor_minimo').val(data.valor_minimo);
        }
    );
}

function salvar_edicao(preco_id) {
    var _this = $('#table-precos [data-preco_id="' + preco_id + '"]');
    $.post(
        '/saude-beta/preco/salvar', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: preco_id,
        id_tabela_preco: $('#id-tabela-preco').val(),
        id_procedimento: _this.find('#procedimento_id_' + preco_id).val(),
        valor: _this.find('#valor').val(),
        valor_prazo: _this.find('#valor_prazo').val(),
        valor_minimo: _this.find('#valor_minimo').val()
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                listar_precos($('#id-tabela-preco').val());
            }
        }
    );
}

function cancelar_edicao(preco_id) {
    var _this = $('#table-precos [data-preco_id="' + preco_id + '"]');
    _this.find('span').show();
    _this.find('input').hide();
    _this.find('select').hide();
    _this.find('[onclick="editar_preco(' + preco_id + ')"]').show();
    _this.find('[onclick="deletar_preco(' + preco_id + ')"]').show();
    _this.find('[onclick="salvar_edicao(' + preco_id + ')"]').hide();
    _this.find('[onclick="cancelar_edicao(' + preco_id + ')"]').hide();
}

function listar_financeira_taxas(id_financeira) {
    $.get('/saude-beta/financeira-taxas/listar/' + id_financeira, function (data) {
        data = $.parseJSON(data);

        $('#id_financeira').val(id_financeira);
        $('#num_min').val('');
        $('#num_max').val('');
        $('#taxa').val('');

        $('#table-financeira-taxas > tbody').empty();
        data.forEach(financeira_taxa => {
            html = '<tr>';
            html += '    <td width="35%" class="text-center">' + financeira_taxa.num_min + '</td>';
            html += '    <td width="35%" class="text-center">' + financeira_taxa.num_max + '</td>';
            html += '    <td width="20%" class="text-center">' + financeira_taxa.taxa + '</td>';
            html += '    <td width="10%" class="text-center btn-table-action">';
            html += '       <i class="my-icon far fa-trash-alt" onclick="deletar_financeira_taxa(' + financeira_taxa.id + ')"></i>';
            html += '    </td>';
            html += '</tr>';
            $('#table-financeira-taxas > tbody').append(html);
        });

        if (!$('#financeiraTaxasModal').hasClass('show')) $('#financeiraTaxasModal').modal('show');
    });
}

function listar_financeira_formas_pag(id_forma_pag) {
    $.get('/saude-beta/financeira-formas-pag/listar/' + id_forma_pag, function (data) {
        data = $.parseJSON(data);

        $('#id_forma_pag').val(id_forma_pag);
        $('#financeira_nome').val('');
        $('#financeira_id').val('');

        $('#table-financeira-formas-pag > tbody').empty();
        data.forEach(financeira => {
            html = '<tr>';
            html += '    <td width="90%">' + financeira.descr_financeira + '</td>';
            html += '    <td width="10%" class="text-center btn-table-action">';
            html += '       <i class="my-icon far fa-trash-alt" onclick="deletar_financeira_formas_pag(' + financeira.id + ')"></i>';
            html += '    </td>';
            html += '</tr>';
            $('#table-financeira-formas-pag > tbody').append(html);
        });

        if (!$('#financeiraFormasPagModal').hasClass('show')) $('#financeiraFormasPagModal').modal('show');
    });
}
function add_preco() {
    $.post(
        '/saude-beta/comissao_exclusiva/salvar', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_procedimento: $('#modalidade-id').val(),
        id_tabela_preco: $('#id-tabela-preco').val(),
        de2: $('#precosModal #de').val(),
        ate2: $('#precosModal #ate').val(),
        valor2: $('#precosModal #valor2').val()
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                listar_precos(data.id_tabela_preco);
                $('#precosModal #de').val($('#precosModal #ate').val())
            }
        }
    );



}



function add_financeira_formas_pag() {
    if ($('#financeira_nome').val() != '' &&
        $('#financeira_id').val() != '') {
        $.post(
            '/saude-beta/financeira-formas-pag/salvar', {
            _token: $("meta[name=csrf-token]").attr("content"),
            id_forma_pag: $('#id_forma_pag').val(),
            financeira_id: $('#financeira_id').val(),
            financeira_nome: $('#financeira_nome').val(),
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {
                    listar_financeira_formas_pag(data.id_forma_pag);
                }
            }
        );
    }
}

function deletar_preco(id_preco) {
    $.get("/saude-beta/comissao_exclusiva/mostrar/" + id_preco,
        function (data) {
            data = $.parseJSON(data);
            if (window.confirm("Deseja excluir preço de '" + data.descr + "'?")) {
                $.post(
                    "/saude-beta/comissao_exclusiva/deletar", {
                    _token: $("meta[name=csrf-token]").attr("content"),
                    id: data.id
                },
                    function (data, status) {
                        console.log(status + " | " + data);
                        if (data.error != undefined) {
                            alert(data.error);
                        } else {
                            listar_precos(data.id_tabela_preco);
                        }
                    }
                );
            }
        });
}

function deletar_tabela_precos(id_tabela_precos) {
    $.get("/saude-beta/tabela-precos/mostrar/" + id_tabela_precos, function (
        data
    ) {
        data = $.parseJSON(data);
        if (window.confirm("Deseja excluir tabela de preços '" + data.descr + "'?")) {
            $.post(
                "/saude-beta/tabela-precos/deletar", {
                _token: $("meta[name=csrf-token]").attr("content"),
                id: data.id
            },
                function (data, status) {
                    console.log(status + " | " + data);
                    if (data.error != undefined) {
                        alert(data.error);
                    } else {
                        document.location.reload(true);
                    }
                }
            );
        }
    });
}

function deletar_financeira_formas_pag(id_financeira_formas_pag) {
    $.get("/saude-beta/financeira-formas-pag/mostrar/" + id_financeira_formas_pag, function (
        data
    ) {
        data = $.parseJSON(data);
        if (window.confirm("Deseja excluir registro?")) {
            $.post(
                "/saude-beta/financeira-formas-pag/deletar", {
                _token: $("meta[name=csrf-token]").attr("content"),
                id: data.id
            },
                function (data, status) {
                    console.log(status + " | " + data);
                    if (data.error != undefined) {
                        alert(data.error);
                    } else {
                        listar_financeira_formas_pag(data.id_forma_pag);
                    }
                }
            );
        }
    });
}

function deletar_financeira_taxa(id_financeira_taxas) {
    $.get("/saude-beta/financeira-taxas/mostrar/" + id_financeira_taxas, function (data) {
        data = $.parseJSON(data);
        if (window.confirm("Deseja excluir taxa?")) {
            $.post(
                "/saude-beta/financeira-taxas/deletar", {
                _token: $("meta[name=csrf-token]").attr("content"),
                id: data.id
            },
                function (data, status) {
                    console.log(status + " | " + data);
                    if (data.error != undefined) {
                        alert(data.error);
                    } else {
                        listar_financeira_taxas(data.id_financeira);
                    }
                }
            );
        }
    });
}

function deletar_especialidade(id_especialidade) {
    $.get('/saude-beta/especialidade/mostrar/' + id_especialidade, function (data) {
        data = $.parseJSON(data);
        if (window.confirm("Deseja excluir especialidade '" + data.descr + "'?")) {
            $.post(
                "/saude-beta/especialidade/deletar", {
                _token: $("meta[name=csrf-token]").attr("content"),
                id_especialidade: data.id
            },
                function (data, status) {
                    console.log(status + " | " + data);
                    if (data.error != undefined) {
                        alert(data.error);
                    } else {
                        document.location.reload(true);
                    }
                }
            );
        }
    });
}

function deletar_procedimento(id_procedimento) {
    $.get('/saude-beta/procedimento/mostrar/' + id_procedimento, function (data) {
        data = $.parseJSON(data);
        if (window.confirm("Deseja excluir procedimento '" + data.descr + "'?")) {
            $.post(
                "/saude-beta/procedimento/deletar", {
                _token: $("meta[name=csrf-token]").attr("content"),
                id_procedimento: data.id
            },
                function (data, status) {
                    console.log(status + " | " + data);
                    if (data.error != undefined) {
                        alert(data.error);
                    } else {
                        document.location.reload(true);
                    }
                }
            );
        }
    });
}

function deletar_convenio(id_convenio) {
    $.get('/saude-beta/convenio/mostrar/' + id_convenio, function (data) {
        data = $.parseJSON(data);
        if (window.confirm("Deseja inativar convênio '" + data.convenio.descr + "'?")) {
            $.post(
                "/saude-beta/convenio/inativar", {
                _token: $("meta[name=csrf-token]").attr("content"),
                id_convenio: data.id
            },
                function (data, status) {
                    console.log(status + " | " + data);
                    if (data.error != undefined) {
                        alert(data.error);
                    } else {
                        document.location.reload(true);
                    }
                }
            );
        }
    });
}

function deletar_pessoa(id_pessoa) {
    $.get('/saude-beta/pessoa/mostrar/' + id_pessoa, function (data) {
        data = $.parseJSON(data);
        if (window.confirm("Deseja inativar '" + data.nome_fantasia + "'?")) {
            $.post(
                "/saude-beta/pessoa/inativar", {
                _token: $("meta[name=csrf-token]").attr("content"),
                id_pessoa: data.id
            },
                function (data, status) {
                    console.log(status + " | " + data);
                    if (data.error != undefined) {
                        alert(data.error);
                    } else {
                        document.location.reload(true);
                    }
                }
            );
        }
    });
}

function imprimir_prescricao(id) {
    var win = window.open('/saude-beta/prescricao/imprimir/' + id, '_blank');
    win.focus();
}

function deletar_prescricao(id) {
    $.post(
        "/saude-beta/prescricao/deletar", {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: id
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                prescricoes_por_pessoa($('#id_pessoa_prontuario').val());
            }
        }
    );
}

function deletar_anexo(id) {
    if (confirm("Tem certeza que deseja excluir esse anexo?")) {
        $.post(
            "/saude-beta/anexos/deletar", {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: id
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {
                    anexos_por_pessoa($('#id_pessoa_prontuario').val());
                }
            }
        );
    }
}

function deletar_evolucao(id) {
    $.post(
        '/saude-beta/evolucao/deletar', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: id
    },
        function (data, status) {
            console.log(status + ' | ' + data)
            if (data.error != undefined) {
                alert(data.error);
            } else {
                evolucoes_por_pessoa($('#id_pessoa_prontuario').val());
            }
        }
    )
}

function deletar_evolucao_tipo(id_evolucao_tipo) {
    $.get('/saude-beta/evolucao-tipo/mostrar/' + id_evolucao_tipo, function (data) {
        data = $.parseJSON(data);
        if (window.confirm("Deseja excluir tipo de evolução '" + data.descr + "'?")) {
            $.post(
                "/saude-beta/evolucao-tipo/deletar", {
                _token: $("meta[name=csrf-token]").attr("content"),
                id: data.id
            },
                function (data, status) {
                    console.log(status + " | " + data);
                    if (data.error != undefined) {
                        alert(data.error);
                    } else {
                        document.location.reload(true);
                    }
                }
            );
        }
    });
}

function deletar_forma_pag(id_forma_pag) {
    $.get('/saude-beta/forma-pag/mostrar/' + id_forma_pag, function (data) {
        data = $.parseJSON(data);
        if (window.confirm("Deseja excluir forma de pagamento '" + data.descr + "'?")) {
            $.post(
                "/saude-beta/forma-pag/deletar", {
                _token: $("meta[name=csrf-token]").attr("content"),
                id: data.id
            },
                function (data, status) {
                    console.log(status + " | " + data);
                    if (data.error != undefined) {
                        alert(data.error);
                    } else {
                        document.location.reload(true);
                    }
                }
            );
        }
    });
}

function deletar_financeira(id_financeira) {
    $.get('/saude-beta/financeira/mostrar/' + id_financeira, function (data) {
        data = $.parseJSON(data);
        if (window.confirm("Deseja excluir financeira '" + data.descr + "'?")) {
            $.post(
                "/saude-beta/financeira/deletar", {
                _token: $("meta[name=csrf-token]").attr("content"),
                id: data.id
            },
                function (data, status) {
                    console.log(status + " | " + data);
                    if (data.error != undefined) {
                        alert(data.error);
                    } else {
                        document.location.reload(true);
                    }
                }
            );
        }
    });
}

function deletar_medicamento(id_medicamento) {
    $.get('/saude-beta/medicamento/mostrar/' + id_medicamento, function (data) {
        data = $.parseJSON(data);
        if (window.confirm("Deseja excluir medicamento '" + data.descr + "'?")) {
            $.post(
                "/saude-beta/medicamento/deletar", {
                _token: $("meta[name=csrf-token]").attr("content"),
                id: data.id
            },
                function (data, status) {
                    console.log(status + " | " + data);
                    if (data.error != undefined) {
                        alert(data.error);
                    } else {
                        document.location.reload(true);
                    }
                }
            );
        }
    });
}

function deletar_orcamento(id_orcamento) {
    if (window.confirm("Deseja excluir orçamento?")) {
        $.post(
            "/saude-beta/orcamento/deletar", {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: id_orcamento
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {
                    if (window.location.pathname.includes('/pessoa/prontuario')) {
                        orcamentos_por_pessoa($('#id_pessoa_prontuario').val());
                    } else {
                        document.location.reload(true);
                    }
                }
            }
        );
    }
}

function deletar_pedido(id_pedido) {
    if (window.confirm("Deseja excluir Contrato?")) {
        $.post(
            "/saude-beta/pedido/deletar", {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: id_pedido
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {
                    if (window.location.pathname.includes('/pessoa/prontuario')) {
                        pedidos_por_pessoa($('#id_pessoa_prontuario').val());
                    } else {
                        document.location.reload(true);
                    }
                }
            }
        );
    }
}


function deletar_atestado(id_atestado) {
    if (window.confirm("Deseja excluir atestado?")) {
        $.post(
            "/saude-beta/atestado/deletar", {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: id_atestado
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {
                    document.location.reload(true);
                }
            }
        );
    }
}

function deletar_receita(id_receita) {
    if (window.confirm("Deseja excluir receita?")) {
        $.post(
            "/saude-beta/receita/deletar", {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: id_receita
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {
                    document.location.reload(true);
                }
            }
        );
    }
}

function deletar_documento(id_documento) {
    if (window.confirm("Deseja excluir documento?")) {
        $.post(
            "/saude-beta/documento/deletar", {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: id_documento
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {
                    documentos_por_pessoa($('#id_pessoa_prontuario').val())
                }
            }
        );
    }
}

function comecar_atendimento(id_paciente) {
    $.post(
        '/saude-beta/atendimento/comecar-atendimento', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_paciente: id_paciente
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                sec = 0;
                min = 0;
                hour = 0;
                timer = setInterval(function () {
                    sec++;
                    if (sec == 60) {
                        sec = 0;
                        min++;
                        if (min == 60) {
                            min = 0;
                            hour++;
                        }
                    }
                    $('#iniciar_atendimento').html(
                        '<i class="my-icon fas fa-stop mr-2"></i> ' +
                        ("0" + hour).slice(-2) + ":" + ("0" + min).slice(-2) + ":" + ("0" + sec).slice(-2)
                    );
                }, 1000);

                if ($('#cbx-video-chamada').prop('checked')) iniciar_video_chamada(id_paciente);
            }
        }
    );
}

function link_video_chamada(id_paciente) {
    $('#videoChamadaModal #link-video-chamada').val("https://gotalk.to/target-saude-" + id_paciente)
    $('#videoChamadaModal #cancelar_chamada').click(function () {
        $('#iniciar_atendimento').trigger('click')
        $('#videoChamadaModal').modal('hide');
    });
    $('#videoChamadaModal #iniciar_chamada').unbind('click').click(function () {
        comecar_atendimento(id_paciente);
        $('#videoChamadaModal').modal('hide');
    });
    $('#videoChamadaModal').modal({ backdrop: 'static', keyboard: false })
    $('#videoChamadaModal').modal('show');
}

function iniciar_video_chamada(id_paciente) {
    var URL = "https://gotalk.to/target-saude-" + id_paciente;
    $('#video-chamada').addClass('show');
    $('#video-link').attr('src', URL);

    var startTime = Date.now();
    var detectPermissionDialog = function (allowed) {
        if (Date.now() - startTime > timeThreshold) {
            // dialog was shown
        }
    };
    var successCallback = function (error) {
        detectPermissionDialog(true);
    };
    var errorCallback = function (error) {
        if ((error.name == 'NotAllowedError') ||
            (error.name == 'PermissionDismissedError')) {
            detectPermissionDialog(false);
        }
    };
    navigator.mediaDevices.getUserMedia(constraints)
        .then(successCallback, errorCallback);

    // $('#video-link').on('load', function () {
    //     $(this).find('#confirm-join-button').trigger('click');
    // });
}

function parar_atendimento(id_paciente) {
    $('#video-link').attr('src', '');
    $('#video-chamada').removeClass('show');
    clearInterval(timer);
    $.post(
        '/saude-beta/atendimento/parar-atendimento', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_paciente: id_paciente
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            }
        }
    );
}

function preview_photo(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $("#foto-preview").attr("src", e.target.result);
            $("[for=foto]").html(input.files[0].name);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function pesquisar_agendamento() {
    var _search = $('#buscar-agendamento').val(),
        dia_temp;
    $.get(
        '/saude-beta/agenda/pesquisar-agendamento', {
        id_profissional: $('#selecao-profissional > .selected').data().id_profissional,
        search: _search
    },
        function (data) {
            data = $.parseJSON(data);
            $('#pesquisa-agendamentos').empty();
            if (data.length > 0) {
                data.forEach(agendamento => {
                    console.log('Agendamento Semanal:');
                    console.log(agendamento);
                    if (agendamento.data != dia_temp) {
                        if (dia_temp != undefined) $('#pesquisa-agendamentos').append('<hr>');
                        html = '<h4 style="color:#212529">' + moment(agendamento.data).format('LL'); + '</h4>';
                        $('#pesquisa-agendamentos').append(html);
                        dia_temp = agendamento.data;
                    }

                    if (agendamento.id != undefined && agendamento.id != null) {
                        html = '<li data-id_agendamento="' + agendamento.id + '"';
                        html += ' data-status="' + agendamento.id_status + '"';
                        html += ' data-paciente="' + agendamento.nome_paciente + '"';
                        html += ' data-procedimento="' + agendamento.descr_procedimento + '"';
                        html += ' data-convenio="' + agendamento.convenio_nome + '"';
                        html += ' data-permite_fila_espera="' + agendamento.permite_fila_espera + '"';
                        html += ' data-permite_reagendar="' + agendamento.permite_reagendar + '"';
                        html += ' data-permite_editar="' + agendamento.permite_editar + '"';
                        html += ' data-libera_horario="' + agendamento.libera_horario + '"';
                        html += ' title="' + agendamento.descr_status + '\n' + agendamento.obs + '"';
                        html += ' style="background:' + agendamento.cor_status + '; color: ' + agendamento.cor_letra + ';max-height: 80px;" >';

                        html += '    <div class="my-1 mx-1 d-flex">';
                        html += '       <img class="foto-paciente-agenda" data-id_paciente="' + agendamento.id_paciente + '" src="/saude-beta/img/pessoa/' + agendamento.id_paciente + '.jpg" onerror="this.onerror=null;this.src=' + "'/saude-beta/img/paciente_default.png'" + '" onclick="verificar_cad_redirecionar(' + agendamento.id_paciente + ')">';
                        html += '       <div>';
                        html += '           <p class="col p-0">';
                        html += '               <span class="ml-0 my-auto" style="font-weight:600" onclick="verificar_cad_redirecionar(' + agendamento.id_paciente + ')">';
                        html += agendamento.hora.substring(0, 5) + '  -  ' + agendamento.nome_paciente.toUpperCase();
                        html += ' (' + agendamento.min_intervalo + ' min.)';
                        html += '               </span>';
                        html += '           </p>';
                        html += '           <p class="tag-agenda" style="font-weight:400">';
                        html += agendamento.nome_profissional + ' | ';
                        if (agendamento.retorno) html += 'Retorno: ';
                        if (agendamento.descr_procedimento != null) html += agendamento.descr_procedimento + ' | ';
                        if (agendamento.tipo_procedimento != null) html += agendamento.tipo_procedimento + ' | ';
                        if (agendamento.convenio_nome != null) html += agendamento.convenio_nome;
                        else html += 'Particular'
                        html += '           </p>';
                        html += '       </div>'
                        if (agendamento.espera != undefined && agendamento.permite_fila_espera) {
                            html += '   <span class="tag-em-espera encaixe my-auto mx-1" title="Em Espera">';
                            html += '      <small>Aguardando a ' + tempo_aguardando(agendamento.hora_chegada, false) + '</small>';
                            html += '   </span>';
                        }
                        html += '   <span class="tag-em-espera encaixe my-auto mx-1">';
                        html += '      <small>' + agendamento.descr_status + '</small>';
                        html += '   </span>';

                        html += '   <div class="tags">';
                        if (agendamento.primeira_vez) {
                            html += '   <span class="tag-primeira-vez my-auto mx-1" title="1ª Vez" onclick="editar_pessoa(' + agendamento.id_paciente + ')">';
                            html += '      <small>1ª Vez</small>';
                            html += '   </span>';
                        }
                        if (agendamento.permite_editar && agendamento.id_confirmacao != null && agendamento.id_confirmacao != 0) {
                            html += '   <span class="tag-confirmado mb-1" title="' + agendamento.descr_confirmacao + '">';
                            html += '      <small class="m-auto">'

                            switch (agendamento.id_confirmacao) {
                                case 1:
                                    html += 'PC'
                                    break;
                                case 2:
                                    html += 'F'
                                    break;
                                case 3:
                                    html += 'R'
                            }

                            html += '</small>';
                            html += '   </span>';
                        }
                        if (agendamento.encaixe) {
                            html += '   <span class="tag-encaixe my-auto mx-1" title="Encaixe">';
                            html += '      <small>Encaixe</small>';
                            html += '   </span>';
                        }
                        html += '   </div>';

                        html += '</div>';
                        html += '</li>';
                        $('#pesquisa-agendamentos').append(html);
                    }
                });
                $(".timing").mask("00:00");
            }

        }
    );
}
function mudar_status_pedido(id, _status) {
    var msg = '';
    if (_status == 'C') msg = 'Deseja cancelar esse contrato?';
    else if (_status == 'F') msg = 'Deseja finalizar plano de tratamento?';
    else if (_status == 'P') msg = 'Deseja congelar contrato?'
    if (confirm('Atenção!\n' + msg)) {
        if (_status == 'P') {
            $.post(
                '/saude-beta/pedido/congelar-descongelar',
                {
                    _token: $("meta[name=csrf-token]").attr("content"),
                    id: id,
                    status: 'P'
                }, function (data, status) {
                    console.log(data + '|' + status);

                }
            )
        }
        else if (_status == 'F') {
            $.post(
                '/saude-beta/pedido/congelar-descongelar',
                {
                    _token: $("meta[name=csrf-token]").attr("content"),
                    id: id,
                    status: 'F'
                }, function (data, status) {
                    console.log(data + '|' + status);

                }
            )
        }
        else if (_status == 'C') {
            $.post(
                '/saude-beta/pedido/mudar-status', {
                _token: $("meta[name=csrf-token]").attr("content"),
                id: id,
                status: 'C'
            }, function (data, status) {
                console.log(data + ' | ' + status)
            }
            )
        }
    }
}


function mudar_status_pedidoold(id, _status) {
    var msg = '';
    if (_status == 'C') msg = 'Deseja cancelar esse contrato?';
    else if (_status == 'F') msg = 'Deseja finalizar plano de tratamento?';
    else if (_status == 'P') msg = 'Deseja congelar contrato?'
    if (confirm('Atenção!\n' + msg)) {
        $.post(
            '/saude-beta/pedido/mudar-status', {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: id,
            status: _status
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {
                    data = $.parseJSON(data);
                    if (_status == 'F') new_system_window('pedido/imprimir/' + data.id + '/' + $("#select-contratos").val());
                    if (window.location.pathname.includes('/pessoa/prontuario')) {
                        pedidos_por_pessoa($('#id_pessoa_prontuario').val());
                    } else {
                        document.location.reload(true);
                    }
                }
            }
        );
    }
}

function resetar_modal_orcamento_conversao() {
    $('#orcamentoConversaoModal [data-etapa]').removeClass('selected').removeClass('success');
    $('#orcamentoConversaoModal [data-etapa="1"]').addClass('selected');
    $('#orcamentoConversaoModal #voltar-orcamento').removeClass('show');
    $('#orcamentoConversaoModal #voltar-orcamento').attr("disabled", true);
    $('#orcamentoConversaoModal #avancar-orcamento').addClass('show');
    $('#orcamentoConversaoModal #avancar-orcamento').attr("disabled", false);
    $('#orcamentoConversaoModal #avancar-orcamento').show();
    $('#orcamentoConversaoModal #salvar-orcamento').hide();
}

function converter_orcamento(id_orcamento) {
    resetar_modal_orcamento_conversao();
    $.get(
        '/saude-beta/orcamento/mostrar/' + id_orcamento,
        function (data) {
            data = $.parseJSON(data);
            $('#orcamentoConversaoModalLabel').html('Converter | Proposta de Tratamento | Nº #' + ("000000" + data.orcamento.num_pedido).slice(-6));

            $('#orcamentoConversaoModal #convert_orcamento_id').val(id_orcamento);
            $('#orcamentoConversaoModal #convert_forma_pag_tipo').val('');

            $('#orcamentoConversaoModal [data-paciente]')
                .data('paciente', data.orcamento.id_paciente)
                .attr('data-paciente', data.orcamento.id_paciente)
                .html(data.orcamento.descr_paciente);

            $('#orcamentoConversaoModal [data-paciente_convenio]')
                .data('paciente_convenio', data.orcamento.id_convenio)
                .attr('data-paciente_convenio', data.orcamento.id_convenio)
                .html(data.orcamento.descr_convenio);

            $('#orcamentoConversaoModal [data-validade]')
                .data('validade', data.orcamento.validade)
                .attr('data-validade', data.orcamento.validade)
                .html(moment(data.orcamento.data_validade).format('DD/MM/YYYY'));

            $('#orcamentoConversaoModal [data-profissional_exa]')
                .data('profissional_exa', data.orcamento.id_prof_exa)
                .attr('data-profissional_exa', data.orcamento.id_prof_exa)
                .html(data.orcamento.descr_prof_exa);

            $('#orcamentoConversaoModal [data-obs]')
                .data('obs', data.orcamento.obs)
                .attr('data-obs', data.orcamento.obs)
                .html(data.orcamento.obs);

            // Tela de Resumo
            $('#orcamentoConversaoModal [data-conv_resumo_paciente]')
                .data('conv_resumo_paciente', data.orcamento.id_paciente)
                .attr('data-conv_resumo_paciente', data.orcamento.id_paciente)
                .html(data.orcamento.descr_paciente);

            $('#orcamentoConversaoModal [data-conv_resumo_paciente_convenio]')
                .data('conv_resumo_paciente_convenio', data.orcamento.id_convenio)
                .attr('data-conv_resumo_paciente_convenio', data.orcamento.id_convenio)
                .html(data.orcamento.descr_convenio);

            $('#orcamentoConversaoModal [data-conv_resumo_validade]')
                .data('conv_resumo_validade', data.orcamento.validade)
                .attr('data-conv_resumo_validade', data.orcamento.validade)
                .html(moment(data.orcamento.data_validade).format('DD/MM/YYYY'));

            $('#orcamentoConversaoModal [data-conv_resumo_profissional_exa]')
                .data('conv_resumo_profissional_exa', data.orcamento.id_prof_exa)
                .attr('data-conv_resumo_profissional_exa', data.orcamento.id_prof_exa)
                .html(data.orcamento.descr_prof_exa);

            $('#orcamentoConversaoModal [data-conv_resumo_obs]')
                .data('conv_resumo_obs', data.orcamento.obs ?? '')
                .attr('data-conv_resumo_obs', data.orcamento.obs ?? '')
                .html(data.orcamento.obs ?? 'Sem observação');

            var total_vista = 0,
                total_prazo = 0;
            $('#orcamentoConversaoModal #table-conv-orcamento-procedimentos > tbody').empty();
            data.orc_procedimentos.forEach(function (servico, index) {
                index++;
                html = '<tr row_number="' + index + '">';
                html += '    <td width="5%" data-id="' + servico.id + '" class="text-center">';

                if (servico.autorizado == 'S') {
                    html += '       <i class="my-icon fas fa-check-circle"></i>';
                } else {
                    html += '       <div class="custom-control custom-checkbox">';
                    html += '           <input id="servico_' + servico.id + '" class="custom-control-input conv-orcamento-servico" type="checkbox" onchange="att_total_proc_conversao(' + "'#table-conv-orcamento-procedimentos'" + ')">';
                    html += '           <label for="servico_' + servico.id + '" class="custom-control-label"></label>';
                    html += '       </div>';
                }

                html += '    </td>';
                html += '    <td width="22.5%" data-procedimento_id="' + servico.id_procedimento + '" data-procedimento_obs="' + servico.obs + '">';
                html += servico.descr_procedimento;
                if (servico.obs != null && servico.obs != '') html += ' (' + servico.obs + ')';
                html += '    </td>';
                html += '    <td width="22.5%" data-profissional_exe_id="' + servico.id_prof_exe + '">' + servico.descr_prof_exe + '</td>';
                if (servico.dente_regiao != null) html += '<td width="10%" class="text-right" data-dente_regiao="' + servico.dente_regiao + '">' + servico.dente_regiao + '</td>';
                else html += '<td width="10%" class="text-right" data-dente_regiao=""></td>';
                if (servico.face != null) html += '<td width="10%" class="text-right" data-dente_face="' + servico.face + '">' + servico.face + '</td>';
                else html += '<td width="10%" class="text-right" data-dente_face=""></td>';
                html += '    <td width="15%" class="text-right" data-valor="' + servico.valor + '">' + servico.valor.toString().replace('.', ',') + '</td>';
                html += '    <td width="15%" class="text-right" data-valor_prazo="' + servico.valor_prazo + '">' + servico.valor_prazo.toString().replace('.', ',') + '</td>';
                html += '</tr>';
                $('#orcamentoConversaoModal #table-conv-orcamento-procedimentos > tbody').append(html);

                total_vista += parseFloat(servico.valor);
                total_prazo += parseFloat(servico.valor_prazo);
            });
            $('#orcamentoConversaoModal [data-total_vista]')
                .data('total_vista', total_vista)
                .attr('data-total_vista', total_vista)
                .html('R$ ' + parseFloat(total_vista).toFixed(2).toString().replace('.', ','));

            $('#orcamentoConversaoModal [data-total_prazo]')
                .data('total_prazo', total_prazo)
                .attr('data-total_prazo', total_prazo)
                .html('R$ ' + parseFloat(total_prazo).toFixed(2).toString().replace('.', ','));

            att_total_proc_conversao('#orcamentoConversaoModal #table-conv-orcamento-procedimentos');
            $('#orcamentoConversaoModal').modal('show');
        }
    );
}

function voltar_etapa_wo_converte() {
    var etapa_atual = $('#orcamentoConversaoModal .wizard-converte > .wo-etapa.selected').data().etapa;
    if (etapa_atual == 2) {
        $('#orcamentoConversaoModal #voltar-converte').removeClass('show');
        $('#orcamentoConversaoModal #voltar-converte').attr("disabled", true);
    }
    if (etapa_atual == 3) {
        $('#orcamentoConversaoModal #avancar-converte').addClass('show');
        $('#orcamentoConversaoModal #avancar-converte').attr("disabled", false);
        $('#orcamentoConversaoModal #avancar-converte').show();
        $('#orcamentoConversaoModal #salvar-converte').hide();
    }
    $('#orcamentoConversaoModal [data-etapa="' + etapa_atual + '"]').removeClass('selected');
    $('#orcamentoConversaoModal [data-etapa="' + (etapa_atual - 1) + '"]').removeClass('success');
    $('#orcamentoConversaoModal [data-etapa="' + (etapa_atual - 1) + '"]').addClass('selected');
    setTimeout(function () {
        $('#orcamentoConversaoModal [data-etapa="' + (etapa_atual - 1) + '"] input').first().focus();
    }, 50);
}

function avancar_etapa_wo_converte() {
    var etapa_atual = $('#orcamentoConversaoModal .wizard-converte > .wo-etapa.selected').data().etapa;

    $('#orcamentoConversaoModal #avancar-converte').show();
    $('#orcamentoConversaoModal #salvar-converte').hide();

    if (etapa_atual == 1 && $('#orcamentoConversaoModal .conv-orcamento-servico:checked').length == 0) {
        alert('Aviso!\nÉ preciso selecionar pelo menos um procedimento para prosseguir.');
        return;

    } else if (etapa_atual == 1) {
        ShowConfirmationBox(
            'Qual será o tipo da forma de pagamento?',
            '',
            true, true, false,
            function () { setar_tipo_forma_pag('V'); },
            function () { setar_tipo_forma_pag('P'); },
            'À Vista',
            'À Prazo'
        );

    } else if (etapa_atual == 2) {
        var vPendente = $('#orcamentoConversaoModal #table-conv-orcamento-forma-pag [data-total_pag_pendente]').data().total_pag_pendente -
            $('#orcamentoConversaoModal #table-conv-orcamento-forma-pag [data-total_pag_valor]').data().total_pag_valor;
        if (vPendente <= 0) {
            $('#orcamentoConversaoModal #avancar-converte').removeClass('show');
            $('#orcamentoConversaoModal #avancar-converte').attr("disabled", true);
            $('#orcamentoConversaoModal #avancar-converte').hide();
            $('#orcamentoConversaoModal #salvar-converte').show();
            montar_resumo_convert();
        } else {
            return;
        }
    }
    $('#orcamentoConversaoModal [data-etapa="' + etapa_atual + '"]').removeClass('selected');
    $('#orcamentoConversaoModal [data-etapa="' + etapa_atual + '"]').addClass('success');
    $('#orcamentoConversaoModal [data-etapa="' + (etapa_atual + 1) + '"]').addClass('selected');
    $('#orcamentoConversaoModal #voltar-converte').addClass('show');
    $('#orcamentoConversaoModal #voltar-converte').attr("disabled", false);

    setTimeout(function () {
        $('#orcamentoConversaoModal [data-etapa="' + (etapa_atual + 1) + '"] input').first().focus();
    }, 50);
}

function att_total_proc_conversao(_table) {
    var total_vista = 0,
        total_prazo = 0;
    $(_table).find('input[type="checkbox"]:checked').each(function () {
        total_vista += parseFloat($(this).parent().parent().parent().find('[data-valor]').data().valor.toString().replace(',', '.'));
        total_prazo += parseFloat($(this).parent().parent().parent().find('[data-valor_prazo]').data().valor_prazo.toString().replace(',', '.'));
    });
    $('#orcamentoConversaoModal [data-total_vista_selecionado]')
        .data('total_vista_selecionado', total_vista)
        .attr('data-total_vista_selecionado', total_vista)
        .html('R$ ' + parseFloat(total_vista).toFixed(2).toString().replace('.', ','));

    $('#orcamentoConversaoModal [data-total_prazo_selecionado]')
        .data('total_prazo_selecionado', total_prazo)
        .attr('data-total_prazo_selecionado', total_prazo)
        .html('R$ ' + parseFloat(total_prazo).toFixed(2).toString().replace('.', ','));
}

function selecionar_todos_cbx(_this, _table) {
    if (_this.prop('checked')) {
        $(_table).find('input[type="checkbox"]').prop('checked', true);
    } else {
        $(_table).find('input[type="checkbox"]').prop('checked', false);
    }
    att_total_proc_conversao(_table);
}

function setar_tipo_forma_pag(_tipo) {
    $.get('/saude-beta/forma-pag/listar/' + _tipo, function (data) {
        data = $.parseJSON(data);
        var html = '';
        data.forEach(forma_pag => {
            if (forma_pag.id != 102) {
                html += '<option value="' + forma_pag.id + '">';
                html += forma_pag.descr;
                html += '</option>';
            }
        });
        $('#orcamentoConversaoModal #conv_forma_pag').html(html).trigger('change');
    });

    var valor_pendente = 0;
    if (_tipo == 'V') {
        $('#orcamentoConversaoModal #conv_forma_pag_parcela').val(1);
        $('#orcamentoConversaoModal #conv_forma_pag_parcela').parent().hide();
        $('#orcamentoConversaoModal #conv_forma_pag_valor').val($(this).data().preco_vista);
        $('#orcamentoConversaoModal #conv_data_vencimento').val(moment().format('DD/MM/YYYY'));

        valor_pendente = $('#orcamentoConversaoModal [data-total_vista_selecionado]').data().total_vista_selecionado;
    } else {
        $('#orcamentoConversaoModal #conv_forma_pag_parcela').parent().show();
        $('#orcamentoConversaoModal #conv_forma_pag_parcela').val(1);
        $('#orcamentoConversaoModal #conv_forma_pag_valor').val($(this).data().preco_prazo);
        $('#orcamentoConversaoModal #conv_data_vencimento').val(moment().add(30, 'days').format('DD/MM/YYYY'));

        valor_pendente = $('#orcamentoConversaoModal [data-total_prazo_selecionado]').data().total_prazo_selecionado;
    }
    $('#orcamentoConversaoModal #table-conv-orcamento-forma-pag [data-total_pag_pendente]')
        .data('total_pag_pendente', valor_pendente)
        .attr('data-total_pag_pendente', valor_pendente)
        .html('Valor Total dos procedimentos - R$ ' + parseFloat(valor_pendente).toFixed(2).toString().replace('.', ','));

    $('#orcamentoConversaoModal #convert_forma_pag_tipo').val(_tipo);
    if ($('#orcamentoConversaoModal #convert_forma_pag_tipo').val() != _tipo) {
        $('#orcamentoConversaoModal #table-conv-orcamento-forma-pag > tbody').empty()
        $('#orcamentoConversaoModal #table-conv-orcamento-forma-pag-resumo > tbody').empty()
    }
    att_total_proc_pagamento();
}

function add_forma_pag_conv() {
    var row_number = ($('#orcamentoConversaoModal #table-conv-orcamento-forma-pag > tbody tr').length + 1),
        vValor = parseFloat($('#orcamentoConversaoModal #conv_forma_pag_valor').val().replace(',', '.')),
        html = '',
        vPendente = 0;

    if (vValor != 0) {
        vPendente = $('#table-conv-orcamento-forma-pag [data-total_pag_pendente]').data().total_pag_pendente -
            $('#table-conv-orcamento-forma-pag [data-total_pag_valor]').data().total_pag_valor;
        if (vPendente != 0) {
            if ((vPendente - vValor) >= 0) {
                html = '<tr row_number="' + row_number + '">';
                html += '    <td width="25%" data-forma_pag="' + $('#conv_forma_pag').val() + '">';
                html += $('#orcamentoConversaoModal #conv_forma_pag option:selected').text();
                html += '    </td>';
                html += '    <td width="25%" data-financeira_id="' + $('#orcamentoConversaoModal #conv_financeira_id').val() + '">';
                if ($('#orcamentoConversaoModal #conv_financeira_id').val() != 0) html += $('#orcamentoConversaoModal #conv_financeira_id option:selected').text();
                html += '    </td>';
                html += '    <td width="15%" data-forma_pag_parcela="' + $('#orcamentoConversaoModal #conv_forma_pag_parcela').val() + '"  class="text-right">';
                html += $('#orcamentoConversaoModal #conv_forma_pag_parcela').val() + 'x de R$ ' + (parseFloat($('#orcamentoConversaoModal #conv_forma_pag_valor').val().replace(',', '.')) / parseInt($('#orcamentoConversaoModal #conv_forma_pag_parcela').val())).toFixed(2).toString().replace('.', ',');
                html += '    </td>';
                html += '    <td width="15%" data-forma_pag_valor="' + $('#orcamentoConversaoModal #conv_forma_pag_valor').val() + '"  class="text-right">';
                html += '       R$ ' + $('#orcamentoConversaoModal #conv_forma_pag_valor').val();
                html += '    </td>';
                html += '    <td width="15%" data-conv_data_vencimento="' + $('#orcamentoConversaoModal #conv_data_vencimento').val() + '">';
                html += $('#orcamentoConversaoModal #conv_data_vencimento').val();
                html += '    </td>';
                html += '    <td width="5%">';
                html += '        <i class="my-icon far fa-trash-alt" onclick="deletar_pedido_grid(' + "'table-conv-orcamento-forma-pag'," + row_number + '); deletar_pedido_grid(' + "'table-conv-orcamento-forma-pag-resumo'," + row_number + '); att_total_proc_pagamento()"></i>'
                html += '    </td>';
                html += '</tr>';
                $('#orcamentoConversaoModal #table-conv-orcamento-forma-pag > tbody').append(html);

                html = '<tr row_number="' + row_number + '">';
                html += '    <td width="27.5%" data-forma_pag="' + $('#orcamentoConversaoModal #conv_forma_pag').val() + '">';
                html += $('#orcamentoConversaoModal #conv_forma_pag option:selected').text();
                html += '    </td>';
                html += '    <td width="27.5%" data-financeira_id="' + $('#orcamentoConversaoModal #conv_financeira_id').val() + '">';
                if ($('#orcamentoConversaoModal #conv_financeira_id').val() != 0) html += $('#orcamentoConversaoModal #conv_financeira_id option:selected').text();
                html += '    </td>';
                html += '    <td width="15%" data-forma_pag_parcela="' + $('#orcamentoConversaoModal #conv_forma_pag_parcela').val() + '"  class="text-right">';
                html += $('#orcamentoConversaoModal #conv_forma_pag_parcela').val() + 'x de R$ ' + (parseFloat($('#orcamentoConversaoModal #conv_forma_pag_valor').val().replace(',', '.')) / parseInt($('#orcamentoConversaoModal #conv_forma_pag_parcela').val())).toFixed(2).toString().replace('.', ',');
                html += '    </td>';
                html += '    <td width="15%" data-forma_pag_valor="' + $('#orcamentoConversaoModal #conv_forma_pag_valor').val() + '"  class="text-right">';
                html += '       R$ ' + $('#orcamentoConversaoModal #conv_forma_pag_valor').val();
                html += '    </td>';
                html += '    <td width="15%" data-conv_data_vencimento="' + $('#orcamentoConversaoModal #conv_data_vencimento').val() + '">';
                html += $('#orcamentoConversaoModal #conv_data_vencimento').val();
                html += '    </td>';
                html += '</tr>';
                $('#orcamentoConversaoModal #table-conv-orcamento-forma-pag-resumo > tbody').append(html);

                att_total_proc_pagamento();
            } else {
                alert('Aviso!\nValor Inválido.');
            }
        } else {
            alert('Aviso!\nAs forma de pagamento já cobrem os serviços adicionados.');
        }
    } else {
        alert('Aviso!\nNão se pode adicionar um forma de pagamento com o valor zerado');
    }
}

function att_total_proc_pagamento() {
    var total_parcelas = 0,
        total_valor = 0;
    $('#orcamentoConversaoModal #table-conv-orcamento-forma-pag > tbody tr').each(function () {
        total_parcelas += parseInt($(this).find('[data-forma_pag_parcela]').data().forma_pag_parcela.toString().replace(',', '.'));
        total_valor += parseFloat($(this).find('[data-forma_pag_valor]').data().forma_pag_valor.toString().replace(',', '.'));
    });

    $('#orcamentoConversaoModal #table-conv-orcamento-forma-pag [data-total_pag_parcela]')
        .data('total_pag_parcela', total_parcelas)
        .attr('data-total_pag_parcela', total_parcelas)
        .html(total_parcelas);

    $('#orcamentoConversaoModal #table-conv-orcamento-forma-pag [data-total_pag_valor]')
        .data('total_pag_valor', total_valor)
        .attr('data-total_pag_valor', total_valor)
        .html('R$ ' + parseFloat(total_valor).toFixed(2).toString().replace('.', ','));

    $('#orcamentoConversaoModal #table-conv-orcamento-forma-pag-resumo [data-total_pag_parcela]')
        .data('total_pag_parcela', total_parcelas)
        .attr('data-total_pag_parcela', total_parcelas)
        .html(total_parcelas);

    $('#orcamentoConversaoModal #table-conv-orcamento-forma-pag-resumo [data-total_pag_valor]')
        .data('total_pag_valor', total_valor)
        .attr('data-total_pag_valor', total_valor)
        .html('R$ ' + parseFloat(total_valor).toFixed(2).toString().replace('.', ','));

    var vPendente = $('#table-conv-orcamento-forma-pag [data-total_pag_pendente]').data().total_pag_pendente -
        $('#table-conv-orcamento-forma-pag [data-total_pag_valor]').data().total_pag_valor;
    if (vPendente < 0) vPendente = 0;
    $('#conv_forma_pag_valor').val(parseFloat(vPendente).toFixed(2).toString().replace('.', ','));
}

function montar_resumo_convert() {
    var html = '',
        i = 0,
        id,
        procedimento_id,
        procedimento_descr,
        procedimento_obs,
        profissional_exe_id,
        dente_regiao,
        dente_face,
        valor,
        valor_prazo,
        total = 0;

    $('#orcamentoConversaoModal #table-conv-orcamento-procedimentos-resumo > tbody').empty();
    $('#orcamentoConversaoModal #table-conv-orcamento-procedimentos').find('input[type="checkbox"]:checked').each(function () {
        id = $(this).parent().parent().parent().find('[data-id]').data().id;
        procedimento_id = $(this).parent().parent().parent().find('[data-procedimento_id]').data().procedimento_id;
        procedimento_descr = $(this).parent().parent().parent().find('[data-procedimento_id]').html();
        procedimento_obs = $(this).parent().parent().parent().find('[data-procedimento_obs]').data().procedimento_obs;
        profissional_exe_id = $(this).parent().parent().parent().find('[data-profissional_exe_id]').data().profissional_exe_id;
        profissional_exe_descr = $(this).parent().parent().parent().find('[data-profissional_exe_id]').html();
        dente_regiao = $(this).parent().parent().parent().find('[data-dente_regiao]').data().dente_regiao;
        dente_face = $(this).parent().parent().parent().find('[data-dente_face]').data().dente_face;
        valor = $(this).parent().parent().parent().find('[data-valor]').data().valor;
        valor_prazo = $(this).parent().parent().parent().find('[data-valor_prazo]').data().valor_prazo;

        i++;
        html = '<tr row_number="' + i + '" data-id="' + id + '">';
        html += '    <td width="25%" data-procedimento_id="' + procedimento_id + '" data-procedimento_obs="' + procedimento_obs + '">';
        html += procedimento_descr;
        if (procedimento_obs != '' && procedimento_obs != null) html += ' (' + procedimento_obs + ')';
        html += '    </td>';
        html += '    <td width="25%" data-profissional_exe_id="' + profissional_exe_id + '">' + profissional_exe_descr + '</td>';
        html += '    <td width="10%" class="text-right" data-dente_regiao="' + dente_regiao + '">' + dente_regiao + '</td>';
        html += '    <td width="10%" class="text-right" data-dente_face="' + dente_face + '">' + dente_face + '</td>';
        if ($('#orcamentoConversaoModal #convert_forma_pag_tipo').val() == 'V') {
            html += '<td width="15%" class="text-right" data-valor="' + valor + '">' + valor.toString().replace('.', ',') + '</td>';
            total += parseFloat(valor);
        } else {
            html += '<td width="15%" class="text-right" data-valor="' + valor_prazo + '">' + valor_prazo.toString().replace('.', ',') + '</td>';
            total += parseFloat(valor_prazo);
        }
        html += '</tr>';
        $('#orcamentoConversaoModal #table-conv-orcamento-procedimentos-resumo > tbody').append(html);
    });
    if ($('#orcamentoConversaoModal #convert_forma_pag_tipo').val() == 'V') {
        $('#orcamentoConversaoModal #table-conv-orcamento-procedimentos-resumo [data-total]')
            .data('total', total)
            .attr('data-total', total)
            .html(
                'Total À Vista - R$ ' + parseFloat(total).toFixed(2).toString().replace('.', ',')
            );
    } else {
        $('#orcamentoConversaoModal #table-conv-orcamento-procedimentos-resumo [data-total]')
            .data('total', total)
            .attr('data-total', total)
            .html(
                'Total À Prazo - R$ ' + parseFloat(total).toFixed(2).toString().replace('.', ',')
            );
    }
}

function finalizar_conversao_orcamento() {
    var id_orcamento = $('#convert_orcamento_id').val(),
        tipo_forma_pag = $('#convert_forma_pag_tipo').val(),
        id_paciente = $('#orcamentoConversaoModal [data-conv_resumo_paciente]').data().conv_resumo_paciente,
        id_convenio = $('#orcamentoConversaoModal [data-conv_resumo_paciente_convenio]').data().conv_resumo_paciente_convenio,
        id_profissional_exa = $('#orcamentoConversaoModal [data-conv_resumo_profissional_exa]').data().conv_resumo_profissional_exa,
        obs = $('#orcamentoConversaoModal [data-conv_resumo_obs]').data().conv_resumo_obs,
        procedimentos = [],
        formas_pag = [];

    $('#orcamentoConversaoModal #table-conv-orcamento-procedimentos-resumo tbody tr').each(function () {
        procedimentos.push({
            id_orcamento_servico: $(this).data().id,
            id_procedimento: $(this).find('[data-procedimento_id]').data().procedimento_id,
            procedimento_obs: $(this).find('[data-procedimento_obs]').data().procedimento_obs,
            id_exe_profissional: $(this).find('[data-profissional_exe_id]').data().profissional_exe_id,
            dente_regiao: $(this).find('[data-dente_regiao]').data().dente_regiao,
            dente_face: $(this).find('[data-dente_face]').data().dente_face,
            valor: $(this).find('[data-valor]').data().valor.toString().replace(',', '.')
        });
    });

    $('#orcamentoConversaoModal #table-conv-orcamento-forma-pag-resumo tbody tr').each(function () {
        formas_pag.push({
            id_forma_pag: $(this).find('[data-forma_pag]').data().forma_pag,
            id_financeira: $(this).find('[data-financeira_id]').data().financeira_id,
            parcela: $(this).find('[data-forma_pag_parcela]').data().forma_pag_parcela,
            forma_pag_valor: $(this).find('[data-forma_pag_valor]').data().forma_pag_valor.replace(',', '.'),
            data_vencimento: $(this).find('[data-conv_data_vencimento]').data().conv_data_vencimento
        });
    });

    $.post(
        '/saude-beta/orcamento/conversao-plano', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_orcamento: id_orcamento,
        tipo_forma_pag: tipo_forma_pag,
        id_paciente: id_paciente,
        id_convenio: id_convenio,
        id_profissional_exa: id_profissional_exa,
        obs: obs,
        procedimentos: procedimentos,
        formas_pag: formas_pag
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                data = $.parseJSON(data);
                // new_system_window('pedido/imprimir/' + data.id);
                if (window.location.pathname.includes('/pessoa/prontuario')) {
                    orcamentos_por_pessoa(id_paciente);
                    $('#orcamentoConversaoModal').modal('hide');
                } else {
                    document.location.reload(true);
                }
            }
        }
    );
}


function salvar_atestado(e) {
    e.preventDefault();
    $('#criarAtestadoModal #id_paciente').val();
    $('#criarAtestadoModal #cid').val();
    $('#criarAtestadoModal #data').val();

    var id_periodo = $('#criarAtestadoModal [name="periodo"]:checked').attr('id'),
        descr_periodo = '';
    if (id_periodo == 'periodo1' ||
        id_periodo == 'periodo2' ||
        id_periodo == 'periodo4' ||
        id_periodo == 'periodo6') {
        descr_periodo = $('#criarAtestadoModal [for="' + id_periodo + '"]').html();
    } else if (id_periodo == 'periodo3') { // DAS __:__ AS __:__ HORAS
        descr_periodo += ' Das ';
        descr_periodo += $('#criarAtestadoModal [for="' + id_periodo + '"] #hora_inicial').val();
        descr_periodo += ' às ';
        descr_periodo += $('#criarAtestadoModal [for="' + id_periodo + '"] #hora_final').val();
        descr_periodo += ' Horas ';
    } else if (id_periodo == 'periodo5') { // POR UM PERÍODO DE __ DIA(S)
        descr_periodo += ' Por Um Período de ';
        descr_periodo += $('#criarAtestadoModal [for="' + id_periodo + '"] #periodo_dias').val();
        descr_periodo += ' Dias(S) ';
    }

    $.post(
        '/saude-beta/atestado/salvar', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_paciente: $('#criarAtestadoModal #id_paciente').val(),
        cid: $('#criarAtestadoModal #cid').val(),
        data: $('#criarAtestadoModal #data').val(),
        periodo: descr_periodo.trim()
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                document.location.reload(true);
            }
        }
    );
}

function add_receita_medicamento() {
    var id_medicamento = $('#criarReceitaModal #medicamento_id').val(),
        descr_medicamento = $('#criarReceitaModal #medicamento_nome').val().toUpperCase();

    if (id_medicamento != '') {
        $.get('/saude-beta/medicamento/mostrar/' + id_medicamento, function (data) {
            data = $.parseJSON(data);
            var html;
            html = '<div class="row" data-id_medicamento="' + data.id + '" data-descr_medicamento="' + data.descr + '">';
            html += '    <div class="col-11 py-2">';
            html += '        <h6 class="m-0">' + data.descr + '</h6>';
            html += '    </div>';
            html += '    <div class="col-1 py-1">';
            html += '        <a href="#"><i class="my-icon far fa-trash-alt" onclick="deletar_receita_medicamento($(this))"></i></a>';
            html += '    </div>';
            html += '    <div class="col-12 pb-2">';
            html += '        <label for="posologia" class="custom-label-form">Posologia</label>';
            html += '        <textarea id="posologia" name="posologia" class="form-control" rows="1" required>' + data.posologia + '</textarea>';
            html += '    </div>';
            html += '</div>';
            $('#lista-receita-medicamentos').append(html);
        });
    } else if (descr_medicamento.length > 0) {
        var html;
        html = '<div class="row" data-id_medicamento="0" data-descr_medicamento="' + descr_medicamento + '">';
        html += '    <div class="col-11 py-2">';
        html += '        <h6 class="m-0">' + descr_medicamento + '</h6>';
        html += '    </div>';
        html += '    <div class="col-1 py-1">';
        html += '        <a href="#"><i class="my-icon far fa-trash-alt" onclick="deletar_receita_medicamento($(this))"></i></a>';
        html += '    </div>';
        html += '    <div class="col-12 pb-2">';
        html += '        <label for="posologia" class="custom-label-form">Posologia</label>';
        html += '        <textarea id="posologia" name="posologia" class="form-control" rows="1" placeholder="Forma de usar o medicamento..." required></textarea>';
        html += '    </div>';
        html += '</div>';
        $('#lista-receita-medicamentos').append(html);
    } else {
        alert('Campo Medicamento Preenchido Indevidamente!')
    }
}

function deletar_receita_medicamento(_this) {
    _this.parent().parent().parent().remove();
}

function salvar_receita(e) {
    e.preventDefault();
    var id_paciente = $('#id_pessoa_prontuario').val(),
        medicamentos = [];

    $('#lista-receita-medicamentos [data-id_medicamento]').each(function () {
        medicamentos.push({
            id: $(this).data().id_medicamento,
            descr: $(this).data().descr_medicamento,
            posologia: $(this).find('#posologia').val()
        })
    });

    $.post(
        '/saude-beta/receita/salvar', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_paciente: id_paciente,
        medicamentos: medicamentos
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                receitas_por_pessoa($('#id_pessoa_prontuario').val());
                $('#criarReceitaModal').modal('hide');
            }
        }
    );
}

function salvar_prescricao(e) {
    e.preventDefault();
    $.post(
        '/saude-beta/prescricao/salvar',
        $('#form-prescricao').serialize() + "&id_paciente=" + $('#id_pessoa_prontuario').val(),
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                prescricoes_por_pessoa($('#id_pessoa_prontuario').val());
                $('#criarPrescricaoModal').modal('hide');
            }
        }
    );
}

function editar_evolucao(id) {
    $.get(
        '/saude-beta/evolucao/mostrar/' + id,
        function (data, status) {
            console.log(data + ' | ' + status)
            data = $.parseJSON(data)
            $('#criarEvolucaoModal #id_evolucao').val(id)
            $('#criarEvolucaoModal #titulo-evolucao').val(data.titulo)
            $('#criarEvolucaoModal #id_evolucao_tipo').val(data.id_evolucao_tipo)
            $('#criarEvolucaoModal #especialidade').val(data.id_area)
            $('#criarEvolucaoModal #hora').val(data.hora)
            $('#criarEvolucaoModal #data').val(formatDataBr(data.data))
            $('#criarEvolucaoModal #id_parte_corpo').val(data.id_parte_corpo)
            $('#criarEvolucaoModal #cid').val(data.cid)
            $('#criarEvolucaoModal .note-editable').html(data.diagnostico)

            $('#criarEvolucaoModal').modal('show')
        }
    )
}

function insertZero(value, length) {
    value = value.toString()
    if (value.length < length) {
        $zeros = ''
        for (i = 0; i < (length - value.length); i++) {
            $zeros += '0'
        }
        return ($zeros + value.toString());
    }
    return value;
}

function salvar_evolucao(e) {
    e.preventDefault();
    $.post(
        '/saude-beta/evolucao/salvar',
        $('#form-salvar-evolucao').serialize() + "&id_paciente=" + $('#id_pessoa_prontuario').val(),
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                $('#criarEvolucaoModal').modal('hide');

                evolucoes_por_pessoa($('#id_pessoa_prontuario').val());
                //mostrarEncaminhamento();
            }
        }
    );
}

function salvar_documento(e) {
    e.preventDefault();
    $.post(
        '/saude-beta/documento/salvar',
        $('#form_documento').serialize() + "&id_paciente=" + $('#id_pessoa_prontuario').val(),
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                documentos_por_pessoa($('#id_pessoa_prontuario').val());
                $('#criarDocumentoModal').modal('hide');
            }
        }
    );
}



function allowDrop(ev) {
    ev.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("type", ev.srcElement.dataset.type);
}
function aviso_arrastar() {
    alert("Arraste")

}

function drop(ev) {
    ev.preventDefault();
    console.log(ev.dataTransfer);
    add_input_anamnese(ev.dataTransfer.getData("type"));
    $('.deletar-campo-anamnese').click(function () {
        console.log('teste de exclusão');
        $(this).parent().parent().remove();
    });
}

function excluir_campo() {
    $('.deletar-campo-anamnese').click(function () {
        console.log('teste de exclusão');
        $(this).parent().parent().remove();
    });
}

function add_input_anamnese(_input) {
    if (_input) {
        var html = '';
        html += '<div class="custom-card row p-2 mb-2" data-type="' + _input + '">';
        html += '    <div class="col">';
        html += '        <label for="pergunta" class="custom-label-form">Descrição</label>';
        html += '        <input id="pergunta" name="pergunta" class="form-control" type="text" required>';
        html += '    </div>';
        html += '    <div class="d-grid pr-4">';
        html += '        <i class="fas fa-trash-alt deletar-campo-anamnese" onclick="excluir_campo()"></i>';
        html += '    </div>';
        html += '    <div class="col-12">';
        html += '        <label for="obs" class="custom-label-form">Obs.: (Opcional)</label>';
        html += '        <textarea id="obs" name="obs" class="form-control" rows="2"></textarea>';
        html += '    </div>';

        if (_input == 'checkbox') {
            html += '<div class="col-12 anamnese-opcoes">';
            html += '    <label for="anamnese_opcao" class="custom-label-form">Respostas:</label>';
            html += '    <div class="row m-0">';
            html += '       <div class="col p-0">';
            html += '           <input name="anamnese_opcao[]" class="form-control form-control-sm anamnese-opcao" type="text" required>';
            html += '       </div>';
            html += '       <div class="my-auto ml-2" style="font-size:1.3rem">';
            html += '           <i class="far fa-plus-circle" onclick="add_anamnese_opcao($(this))"></i>';
            html += '           <i class="far fa-minus-circle" style="opacity:0"></i>';
            html += '       </div>';
            html += '    </div>';
            html += '</div>';
        }

        html += '    <div class="col-12 d-grid">';
        html += '        <span class="ml-auto">';
        html += '            <img class="p-1" src="/saude-beta/img/input-' + _input + '.png" width="40px">';
        if (_input == 'text') html += '<small>Campo Texto</small>';
        else if (_input == 'number') html += '<small>Campo Numérico</small>';
        else if (_input == 'positive-negative') html += '<small>Campo Positivo/Negativo</small>';
        else if (_input == 'checkbox') html += '<small>Campo Múltipla Escolha</small>';
        else if (_input == 'radio') html += '<small>Campo Sim/Não</small>';
        html += '        </span>';
        html += '    </div>';
        html += '</div>';
        $('#lista-criacao-anamnese')
            .append(html);
    }
    else alert("Arraste para a posição desejada")
}
function add_input_IEC() {
    var html = '',
        lista = ['Pessimo', 'Ruim', 'Bom', 'Excelente'],
        linha = document.querySelectorAll("#lista-criacao-anamnese > div").length;

    html += '<div class="custom-card row p-2 mb-2" data-type="' + 'checkbox' + '">';
    html += '    <div class="col">';
    html += '        <label for="pergunta" class="custom-label-form">Descrição</label>';
    html += '        <input id="pergunta" name="pergunta" class="form-control" type="text" required>';
    html += '    </div>';
    html += '    <div class="d-grid pr-4">';
    html += '        <i class="fas fa-trash-alt deletar-campo-anamnese" onclick="excluir_campo()"></i>';
    html += '    </div>';
    html += '    <div class="col-12">';
    html += '        <label for="obs" class="custom-label-form">Obs.: (Opcional)</label>';
    html += '        <textarea id="obs" name="obs" class="form-control" rows="2"></textarea>';
    html += '    </div>';

    for (i = 0; i < 4; i++) {
        html += '<div class="col-2 anamnese-opcoes">';
        html += '    <label for="anamnese_opcao" class="custom-label-form">' + lista[i] + '</label>';
        html += '    <div class="row m-0">';
        html += '       <div class="col p-0">';
        html += '    <input class="areas" id="areas-' + lista[i] + linha + '" type="hidden">'
        html += '           <input id="' + lista[i] + '" name="anamnese_opcao[]" class="form-control form-control-sm anamnese-opcao" type="text" required>';
        html += '       </div>';
        html += '       <div class="my-auto ml-2" style="font-size:1.3rem">';
        html += '       </div>';
        html += '    </div>';
        html += '</div> ';
        html += '<div style="cursor: pointer" onclick="'
        html += ' add_areas_iec(' + "'" + lista[i][0] + linha + "'" + ',' + "'" + '#areas-' + lista[i] + linha + "'" + ')";> '
        html += '   <img style="width: 36px;position: relative;top: 20px;right: 20px;"'
        html += '   src="http://vps.targetclient.com.br/saude-beta/img/ico-areas.png"> '
        html += '   <span id="' + lista[i] + linha + '"  class="i-areas">0</span></div>'
    }

    html += '    <div class="col-12 d-grid">';
    html += '        <span class="ml-auto">';
    html += '            <img class="p-1" src="/saude-beta/img/input-' + 'checkbox' + '.png" width="40px">';
    if ('checkbox' == 'text') html += '<small>Campo Texto</small>';
    else if ('checkbox' == 'number') html += '<small>Campo Numérico</small>';
    else if ('checkbox' == 'positive-negative') html += '<small>Campo Positivo/Negativo</small>';
    else if ('checkbox' == 'checkbox') html += '<small>Campo Múltipla Escolha</small>';
    else if ('checkbox' == 'radio') html += '<small>Campo Sim/Não</small>';
    html += '        </span>';
    html += '    </div>';
    html += '</div>';
    $('#lista-criacao-anamnese').append(html);
}
function add_areas_iec(resposta, id) {
    $("#areas-saude").empty()
    $('#addAreasIecModal #question').val(resposta)
    $('#addAreasIecModal').modal('show')
    let $list = $(id).val().split(',')
    $.get(
        '/saude-beta/especialidade/listar', {},
        function (data, status) {
            console.log(data + ' | ' + status)
            data = $.parseJSON(data);
            data.forEach(especialidade => {
                html = '<div class="col-6"> '
                html += '   <div class="custom-control custom-switch">'
                html += '   <input id="' + especialidade.descr + '" name="' + especialidade.descr + '" class="custom-control-input permissoes"'

                if ($list.includes(especialidade.id.toString())) html += ' type="checkbox" value="' + especialidade.id + '" checked>'
                else html += ' type="checkbox" value="' + especialidade.id + '">'

                html += '<label for="' + especialidade.descr + '" class="custom-control-label" style="width:120px">' + especialidade.descr + '</label>'
                html += '</div></div>'
                $("#areas-saude").append(html)
            })
        }
    )
}
function add_ids_modalidade() {
    let lista = [],
        linha = $('#addAreasIecModal #question').val()[1],
        op = $('#addAreasIecModal #question').val()[0]
    document.querySelectorAll("input:checked").forEach(el => {
        lista.push(el.value)
    })
    switch (op) {
        case "P":
            $('#Pessimo' + linha).html(lista.length)
            $('#areas-Pessimo' + linha).val('');
            $('#areas-Pessimo' + linha).val(lista.toString())
            $('#addAreasIecModal').modal('hide')
            break;
        case "R":
            $('#Ruim' + linha).html(lista.length)
            $('#areas-Ruim' + linha).val('');
            $('#areas-Ruim' + linha).val(lista.toString())
            $('#addAreasIecModal').modal('hide')
            break;
        case "B":
            $('#Bom' + linha).html(lista.length)
            $('#areas-Bom' + linha).val('');
            $('#areas-Bom' + linha).val(lista.toString())
            $('#addAreasIecModal').modal('hide')
            break;
        case "E":
            $("#Excelente" + linha).html(lista.length)
            $("#areas-Excelente" + linha).val('')
            $("#areas-Excelente" + linha).val(lista.toString())
            $('#addAreasIecModal').modal('hide')
            break;
    }
}
function salvar_IEC() {
    let perguntas = [],
        opcoes = [],
        areas = [];

    $('#lista-criacao-anamnese > [data-type]').each(function () {
        opcoes = [];
        modalidades = [];
        areas = [];
        $(this).find('.anamnese-opcoes .anamnese-opcao').each(function () {
            if ($(this).val().trim() != '') {
                areas.push($(this).parent().find(".areas").val())
                opcoes.push($(this).val());
                a = $(this);
            } else {
                alert('Todos os campos devem ser preenchidos');
                return false;
            }
        });

        perguntas.push({
            descr: $(this).find('#pergunta').val(),
            obs: $(this).find('#obs').val(),
            opcoes: opcoes,
            areas: areas
        });
    });
    console.log(perguntas)
    if (perguntas.length > 0) {
        $.post(
            '/saude-beta/IEC/salvar', {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: 0,
            descr: $('#descr').val(),
            perguntas: perguntas
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {

                    redirect('/saude-beta/IEC');
                }
            }
        );
    } else {
        alert('Não foi adicionado nenhuma pergunta nesse modelo de anamnese.');
    }
}
function editar_IEC() {
    let perguntas = [],
        opcoes = [],
        areas = [];

    $('#lista-criacao-anamnese > [data-type]').each(function () {
        opcoes = [];
        modalidades = [];
        areas = [];
        $(this).find('.anamnese-opcoes .anamnese-opcao').each(function () {
            if ($(this).val().trim() != '') {
                areas.push($(this).parent().find(".areas").val())
                opcoes.push($(this).val());
                a = $(this);
            } else {
                alert('Todos os campos devem ser preenchidos');
                return false;
            }
        });

        perguntas.push({
            descr: $(this).find('#pergunta').val(),
            obs: $(this).find('#obs').val(),
            opcoes: opcoes,
            areas: areas
        });
    });
    console.log(perguntas)
    if (perguntas.length > 0) {
        $.post(
            '/saude-beta/IEC/salvar', {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: $("#id-IEC").val(),
            descr: $('#descr').val(),
            perguntas: perguntas
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {

                    redirect('/saude-beta/IEC');
                }
            }
        );
    } else {
        alert('Não foi adicionado nenhuma pergunta nesse modelo de anamnese.');
    }
}
function excluir_IEC($id) {
    $.get(
        '/saude-beta/IEC/excluir/' + $id,
        function (data, status) {
            console.log(data + ' | ' + status);
            if (data == 'true') {
                alert('excluido com sucesso')
                location.reload()
            }
            else {
                alert('error')
            }
        }
    )

}
function ativar_IEC($id) {
    $.get(
        '/saude-beta/IEC/ativar/' + $id,
        function (data, status) {
            console.log(data, status)
            if (data == 'ativado') alert('IEC ativado com sucesso')
            else if (data == 'desativado') alert('IEC desativado com sucesso')
            else alert("ERROR")
            location.reload();
        }
    )
}
function add_anamnese_opcao(_this) {
    var html = '';
    html += '    <div class="row m-0">';
    html += '       <div class="col p-0">';
    html += '           <input name="anamnese_opcao[]" class="form-control form-control-sm anamnese-opcao" type="text" required>';
    html += '       </div>';
    html += '       <div class="my-auto ml-2" style="font-size:1.3rem">';
    html += '           <i class="far fa-plus-circle"  onclick="add_anamnese_opcao($(this))"></i>';
    html += '           <i class="far fa-minus-circle" onclick="del_anamnese_opcao($(this))"></i>';
    html += '       </div>'
    html += '    </div>';
    _this.parent().parent().parent().append(html);
}

function del_anamnese_opcao(_this) {
    alert(_this)
    _this.parent().parent().remove();
}

function criar_anamnese(e) {
    e.preventDefault();
    var perguntas = [],
        opcoes = [];

    $('#lista-criacao-anamnese > [data-type]').each(function () {
        if ($(this).data().type == 'checkbox') {
            opcoes = [];
            $(this).find('.anamnese-opcoes .anamnese-opcao').each(function () {
                if ($(this).val().trim() != '') {
                    opcoes.push($(this).val());
                } else {
                    alert('Todos os campos devem ser preenchidos');
                    return false;
                }
            });
        }
        perguntas.push({
            descr: $(this).find('#pergunta').val(),
            obs: $(this).find('#obs').val(),
            tipo: getInputTypeFlag($(this).data().type),
            opcoes: opcoes
        });
    });

    if (perguntas.length > 0) {
        $.post(
            '/saude-beta/anamnese/salvar', {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: 0,
            descr: $('#descr').val(),
            perguntas: perguntas,
            especialidade: $("#especialidade").val()
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {

                    redirect('/saude-beta/anamnese');
                }
            }
        );
    } else {
        alert('Não foi adicionado nenhuma pergunta nesse modelo de anamnese.');
    }
}
function editar_anamnese(e) {
    e.preventDefault();
    var perguntas = [],
        opcoes = [];

    $('#lista-criacao-anamnese > [data-type]').each(function () {
        if ($(this).data().type == 'checkbox') {
            opcoes = [];
            $(this).find('.anamnese-opcoes .anamnese-opcao').each(function () {
                if ($(this).val().trim() != '') {
                    opcoes.push($(this).val());
                } else {
                    alert('Todos os campos devem ser preenchidos');
                    return false;
                }
            });
        }
        perguntas.push({
            descr: $(this).find('#pergunta').val(),
            obs: $(this).find('#obs').val(),
            tipo: getInputTypeFlag($(this).data().type),
            opcoes: opcoes
        });
    });

    if (perguntas.length > 0) {
        $.post(
            '/saude-beta/anamnese/salvar', {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: $("#id-anamnese").val(),
            descr: $('#descr').val(),
            perguntas: perguntas,
            especialidade: $("#especialidade").val()
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {

                    redirect('/saude-beta/anamnese');
                }
            }
        );
    } else {
        alert('Não foi adicionado nenhuma pergunta nesse modelo de anamnese.');
    }
}

function getInputTypeFlag(sTipoName) {
    if (sTipoName == 'text') return 'T';
    else if (sTipoName == 'number') return 'N';
    else if (sTipoName == 'radio') return 'R';
    else if (sTipoName == 'positive-negative') return '+';
    else if (sTipoName == 'checkbox') return 'C';
}
function imprimir_anamnese(id_anamnese_pessoa) {
    location.href = '/saude-beta/anamnese/mostrar-resposta/' + id_anamnese_pessoa
}
function imprimir_IEC(id_IEC_pessoa) {
    location.href = '/saude-beta/IEC/mostrar-resposta/' + id_IEC_pessoa
}
function mostrar_questionario_anamnese(id_anamnese) {
    $.get('/saude-beta/anamnese/mostrar/' + id_anamnese,
        function (data) {
            console.log(data);
            data = $.parseJSON(data);
            var html = '';

            $('#anamneseModal #id_anamnese').val(id_anamnese);
            $('#anamneseModalLabel').html('Anamnese | ' + data.descr);
            $('#questionario-anamnese').empty();
            data.perguntas.forEach(pergunta => {
                html = '<div class="row" data-id_pergunta="' + pergunta.id + '" data-tipo="' + pergunta.tipo + '">';
                html += '    <div class="col-12">';
                html += '        <h4 class="m-0">';
                html += pergunta.pergunta;
                html += '        </h4>';
                html += '        <small>';
                html += '           Obs.: ' + pergunta.obs;
                html += '        </small>';
                html += '    </div>';
                html += '    <div class="col-12 d-grid">';
                html += '        <label for="resposta_' + pergunta.id + '" class="custom-label-form">Resposta:</label>';
                if (pergunta.tipo == 'T') {
                    html += '    <input id="resposta_' + pergunta.id + '" class="form-control" autocomplete="off" type="text" required>';

                } else if (pergunta.tipo == 'N') {
                    html += '    <input id="resposta_' + pergunta.id + '" class="form-control" autocomplete="off" type="number" required step="0.01">';

                } else if (pergunta.tipo == 'R') {
                    html += '<div id="resposta_' + pergunta.id + '">';
                    html += '   <div class="custom-control custom-radio custom-control-inline">';
                    html += '       <input class="custom-control-input" id="resposta_sim_' + pergunta.id + '" type="radio" value="Sim">';
                    html += '       <label class="custom-control-label" for="resposta_sim_' + pergunta.id + '">Sim</label>';
                    html += '   </div>';
                    html += '   <div class="custom-control custom-radio custom-control-inline">';
                    html += '       <input class="custom-control-input" id="resposta_nao_' + pergunta.id + '" type="radio" value="Não">';
                    html += '       <label class="custom-control-label" for="resposta_nao_' + pergunta.id + '">Não</label>';
                    html += '   </div>';
                    html += '</div>';

                } else if (pergunta.tipo == '+') {
                    html += '<div id="resposta_' + pergunta.id + '">';
                    html += '   <div class="custom-control custom-radio custom-control-inline">';
                    html += '       <input class="custom-control-input" id="resposta_positivo_' + pergunta.id + '" type="radio" value="Positivo">';
                    html += '       <label class="custom-control-label" for="resposta_positivo_' + pergunta.id + '">Positivo</label>';
                    html += '   </div>';
                    html += '   <div class="custom-control custom-radio custom-control-inline">';
                    html += '       <input class="custom-control-input" id="resposta_negativo_' + pergunta.id + '" type="radio" value="Negativo">';
                    html += '       <label class="custom-control-label" for="resposta_negativo_' + pergunta.id + '">Negativo</label>';
                    html += '   </div>';
                    html += '</div>';

                } else if (pergunta.tipo == 'C') {
                    html += '<div id="resposta_' + pergunta.id + '">';
                    pergunta.opcoes.forEach(opcao => {
                        html += '<div class="custom-control custom-checkbox custom-control-inline">';
                        html += '    <input id="opcao_' + opcao.id + '" class="custom-control-input" type="checkbox" value="' + opcao.descr + '">';
                        html += '    <label for="opcao_' + opcao.id + '" class="custom-control-label">' + opcao.descr + '</label>';
                        html += '</div>';
                    });
                    html += '</div>';
                }
                html += '    </div>';
                html += '</div>';
                $('#questionario-anamnese').append(html);
            });
            $('#selecaoAnamneseModal').modal('hide');
            $('#anamneseModal').modal('show');
        }
    );
    mostrar_respostas_anamnese(id_anamnese)
}
function mostrar_questionario_iec(id_iec) {
    $.get('/saude-beta/IEC/mostrar/' + id_iec,
        function (data) {
            console.log(data);
            data = $.parseJSON(data);
            var html = '';

            $('#iecModal #id_iec').val(id_iec);
            $('#iecModalLabel').html('IEC | ' + data.descr);
            $('#questionario-iec').empty();
            radio_control = 0
            data.perguntas.forEach(pergunta => {
                html = '<div class="row iec-quest" data-id_pergunta="' + pergunta.id + '" data-tipo="' + pergunta.tipo + '">';
                html += '    <div class="col-12">';
                html += '        <h4 class="m-0">';
                html += pergunta.pergunta;
                html += '        </h4>';
                html += '        <small>';
                html += '           Obs.: ' + pergunta.obs;
                html += '        </small>';
                html += '    </div>';
                html += '    <div class="col-12 d-grid">';
                html += '        <label for="resposta_' + pergunta.id + '" class="custom-label-form">Resposta:</label>';

                html += '<div id="resposta_' + pergunta.id + '">';
                lista = ['pessimo', 'ruim', 'bom', 'excelente']
                tamanhos = ['70%', '80%', '90%', '100%']
                cores = ['#f15151', '#e6e629', '#4ef94e', '#64b3ff']
                for (i = 0; i < 4; i++) {
                    html += '<div style="width: ' + tamanhos[i] + '!important; border:1px solid black;'
                    html += 'color:#000;font-weight: bolder;font-size: 1rem;'
                    html += 'background-color:' + cores[i] + '"'
                    html += 'class="format custom-control custom-checkbox custom-control-inline">';

                    html += '    <input class="check" name="' + radio_control + '" id="opcao_' + ((radio_control * 4) + i) + '" type="radio" value="' + i + '">';
                    html += '    <label for="opcao_' + ((radio_control * 4) + i) + '" style="font-weight: bolder;font-size: 1rem; font: inherit;margin-bottom:0;width:100%"">' + pergunta[lista[i]] + '</label>'
                    html += '<div class="indicador-iec"></div>'
                    html += '</div>';
                }
                html += '</div>';
                html += '    </div>';
                html += '</div>';
                radio_control++;
                $('#questionario-iec').append(html);
            });
            $('#selecaoIECModal').modal('hide');
            $('#iecModal').modal('show');
        }
    );
}



function salvar_anamnese(e) {
    var respostas = [],
        opcoes;
    e.preventDefault();
    var erro = false;
    $('#questionario-anamnese [data-id_pergunta]').each(function () {
        if ($(this).data().tipo == 'T' || $(this).data().tipo == 'N') {
            console.log($(this).html());
            opcoes = $(this).find('#resposta_' + $(this).data().id_pergunta).val();
        } else if ($(this).data().tipo == 'R' || $(this).data().tipo == '+') {
            opcoes = $(this).find('#resposta_' + $(this).data().id_pergunta + ' input:checked').val();
        } else if ($(this).data().tipo == 'C') {
            opcoes = [];
            $('#resposta_' + $(this).data().id_pergunta + ' input:checked').each(function () {
                opcoes.push($(this).val());
            });
        }

        respostas.push({
            id_pergunta: $(this).data().id_pergunta,
            tipo: $(this).data().tipo,
            resposta: opcoes
        });

        if (!opcoes.length) erro = true;
    });

    if (!erro) {
        $.post('/saude-beta/anamnese/responder-anamnese', {
            _token: $("meta[name=csrf-token]").attr("content"),
            id_anamnese: $('#anamneseModal #id_anamnese').val(),
            id_paciente: $('#id_pessoa_prontuario').val(),
            respostas: respostas
        }, function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                console.log(data);
                data = $.parseJSON(data);
                anamneses_por_pessoa($('#id_pessoa_prontuario').val());
                $('#anamneseModal').modal('hide');
            }
        });
    }
}

function deletar_anamnese(id_anamnese_pessoa) {
    $.post(
        '/saude-beta/anamnese-pessoa/deletar', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_anamnese_pessoa: id_anamnese_pessoa
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                anamneses_por_pessoa($('#id_pessoa_prontuario').val());
                $('#anamneseModal').modal('hide');
            }
        }
    );
}
function deletar_IEC(id_iec_pessoa) {
    console.log(id_iec_pessoa)
    if (window.confirm('Deseja mesmo deletar este registro?')) {
        $.post(
            '/saude-beta/IEC/deletar', {
            _token: $("meta[name=csrf-token]").attr("content"),
            id_iec_pessoa: id_iec_pessoa
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {
                    iec_por_pessoa($('#id_pessoa_prontuario').val());
                    $('#iecModal').modal('hide');
                    location.reload();
                }
            }
        );
    }
}
function salvar_procedimento(e) {
    e.preventDefault();
    $.post(
        '/saude-beta/procedimento/salvar', {
        _token: $('meta[name=csrf-token]').attr("content"),
        id: $('#id').val(),
        especialidade: $('#especialidade').val(),
        cod_tuss: $('#cod-tuss').val(),
        descr: $('#descr').val(),
        descr_resumida: $('#descr-resumida').val(),
        tempo_procedimento: $('#tempo-procedimento').val(),
        obs: $('#obs').val(),
        dente_regiao: $('#dente_regiao').prop('checked'),
        face: $('#face').prop('checked'),
        faturar: $('#faturar_val').val()
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined || data == 'Já existe um procedimento com esse código TUSS!') {
                if (data == 'Já existe um procedimento com esse código TUSS!') {
                    alert(data);
                } else {
                    alert(data.error);
                }
            } else {
                document.location.reload(true);
            }
        }
    );
}

function salvar_anexo(e) {
    e.preventDefault();
    var form = $('#form-add-anexo')[0];
    var data = new FormData(form);
    a = data
    $.ajax({
        type: "POST",
        enctype: 'multipart/form-data',
        url: "/saude-beta/anexos/salvar",
        data: data,
        processData: false,
        contentType: false,
        cache: false,
        timeout: 600000,
        success: function (data) {
            console.log(data);
            anexos_por_pessoa($('#id_pessoa_prontuario').val());
            $('#criarAnexoModal').modal('hide');
        },
        error: function (e) {
            console.log(e);
            anexos_por_pessoa($('#id_pessoa_prontuario').val());
            $('#criarAnexoModal').modal('hide');
        }
    });
}

function pessoa_fisica_juridica() {
    $('#pessoa-fisica').toggle();
    $('#pessoa-juridico').toggle();
}
function controlMembroGeraFaturamento($obj) {
    console.log($obj.prop('checked'))
    switch ($obj.prop('checked')) {
        case false:
            $('#pessoaModal #data').attr('disabled', 'true')
                .val('')

            $('#pessoaModal #faturamento').attr('disabled', 'true').val('')
            break;

        default:
            $('#pessoaModal #data').removeAttr('disabled')
                .val('')
            $('#pessoaModal #faturamento').removeAttr('disabled').val('')
            break;

    }
}
function tempo_aguardando(_hora_chegada, _min) {
    var data_chegada = new Date();
    data_chegada.setHours(
        _hora_chegada.substring(0, 2),
        _hora_chegada.substring(3, 5),
        0
    );
    if (_min) return timeDiffCalcMin(new Date(), data_chegada);
    else return timeDiffCalc(new Date(), data_chegada);
}

function timeDiffCalcMin(dateNow, dateFuture) {
    let diffInMilliSeconds = Math.abs(dateNow - dateFuture) / 1000;
    // calculate days
    const days = Math.floor(diffInMilliSeconds / 86400);
    diffInMilliSeconds -= days * 86400;
    console.log('Dias Calculados: ', days);
    // calculate hours
    const hours = Math.floor(diffInMilliSeconds / 3600) % 24;
    diffInMilliSeconds -= hours * 3600;
    console.log('Dias Horas: ', hours);
    // calculate minutes
    const minutes = Math.floor(diffInMilliSeconds / 60) % 60;
    diffInMilliSeconds -= minutes * 60;
    console.log('Dias Minutos: ', minutes);
    let difference = '';
    // if (days > 0) {
    //     difference += (days === 1) ? `${days} dia, ` : `${days} dias, `;
    // }
    difference += (hours === 0) ? `` : (hours === 1) ? `${hours}:` : `${hours}:`;
    difference += (minutes === 0 || hours === 1) ? `${minutes}` : `${minutes}`;
    if (hours === 0) difference += ' min.';
    return difference;
}

function timeDiffCalc(dateNow, dateFuture) {
    let diffInMilliSeconds = Math.abs(dateNow - dateFuture) / 1000;
    // calculate days
    const days = Math.floor(diffInMilliSeconds / 86400);
    diffInMilliSeconds -= days * 86400;
    console.log('Dias Calculados: ', days);
    // calculate hours
    const hours = Math.floor(diffInMilliSeconds / 3600) % 24;
    diffInMilliSeconds -= hours * 3600;
    console.log('Dias Horas: ', hours);
    // calculate minutes
    const minutes = Math.floor(diffInMilliSeconds / 60) % 60;
    diffInMilliSeconds -= minutes * 60;
    console.log('Dias Minutos: ', minutes);
    let difference = '';
    if (days > 0) {
        difference += (days === 1) ? `${days} dia, ` : `${days} dias, `;
    }
    difference += (hours === 0) ? `` : (hours === 1) ? `${hours} hora e ` : `${hours} horas e `;
    difference += (minutes === 0 || hours === 1) ? `${minutes} minuto` : `${minutes} minutos`;
    return difference;
}

// function confirmar_fila_espera(id_agenda) {
//     if (window.confirm("Confirmar Atendimento do Paciente?")) {
//         $.post(
//             '/saude-beta/fila-espera/confirmar', {
//             _token: $('meta[name=csrf-token]').attr("content"),
//             id_agenda: id_agenda
//         },
//             function (data, status) {
//                 console.log(status + " | " + data);
//                 if (data.error != undefined) {
//                     alert(data.error);
//                 } else {
//                     // document.location.reload(true);
//                 }
//             }
//         );
//     }
// }

function ShowConfirmationBox(sHeader, sMsg, bYes, bNo, bCancel, fYes, fNo, sYes, sNo) {
    var _confirmation_box = $('#confirmationModal');
    _confirmation_box.find('#confirmation_header').html(sHeader);
    _confirmation_box.find('#confirmation_msg').html(sMsg);

    _confirmation_box.find('#confirmation_yes').unbind('click').click(function () { _confirmation_box.modal('hide'); });
    _confirmation_box.find('#confirmation_no').unbind('click').click(function () { _confirmation_box.modal('hide'); });
    _confirmation_box.find('#confirmation_cancel').unbind('click').click(function () { _confirmation_box.modal('hide'); });

    if (sYes != undefined && sYes != '') _confirmation_box.find('#confirmation_yes').html(sYes);
    else _confirmation_box.find('#confirmation_yes').html('Sim');
    if (sNo != undefined && sNo != '') _confirmation_box.find('#confirmation_no').html(sNo);
    else _confirmation_box.find('#confirmation_no').html('Não');

    if (bYes) _confirmation_box.find('#confirmation_yes').show().click(fYes);
    else _confirmation_box.find('#confirmation_yes').hide();
    if (bNo) _confirmation_box.find('#confirmation_no').show().click(fNo);
    else _confirmation_box.find('#confirmation_no').hide();
    if (bCancel) _confirmation_box.find('#confirmation_cancel').show();
    else _confirmation_box.find('#confirmation_cancel').hide();

    _confirmation_box.modal('show');
}

// ————————————————————————————————————————————————————————————————————————————————————— //
// ————————————————————————————————————————————————————————————————————————————————————— //
// ——————————————————————————————— PROPOSTA DE TRATAMENTO —————————————————————————————— //
// ————————————————————————————————————————————————————————————————————————————————————— //
// ————————————————————————————————————————————————————————————————————————————————————— //
function resetar_modal_orcamento() {
    $('#orcamentoModal [data-etapa]').removeClass('selected').removeClass('success');
    $('#orcamentoModal [data-etapa="1"]').addClass('selected');
    $('#orcamentoModal #voltar-orcamento').removeClass('show');
    $('#orcamentoModal #voltar-orcamento').attr("disabled", true);
    $('#orcamentoModal #avancar-orcamento').addClass('show');
    $('#orcamentoModal #avancar-orcamento').attr("disabled", false);
    $('#orcamentoModal #avancar-orcamento').show();
    $('#orcamentoModal #salvar-orcamento').hide();
    $('#orcamentoModal #table-orcamento-forma-pag > tbody').empty();
    $('#orcamentoModal #table-orcamento-forma-pag-resumo > tbody').empty();
    $('#orcamentoModal #table-orcamento-procedimentos > tbody').empty();
    $('#orcamentoModal #table-resumo-orcamento-procedimentos > tbody').empty();
}

function abrir_orcamento() {
    resetar_modal_orcamento();
    $.get('/saude-beta/orcamento/gerar-num', function (data) {
        $('#orcamentoModalLabel').html('Proposta de Tratamento | Nº #' + ("000000" + data).slice(-6));
        $('#orcamentoModal #validade').val(moment().add(15, 'days').format('DD/MM/YYYY'));
        $('#orcamentoModal #id').val(0);
        $('#orcamentoModal #salvar-orcamento').html('Salvar');
        $('#orcamentoModal #status-orcamento')
            .html('Novo')
            .removeAttr('class')
            .addClass('tag-pedido-primary');

        $('#orcamentoModal').modal('show');
        setTimeout(function () {
            $("#orcamentoModal #paciente_nome").first().focus();
            $("#orcamentoModal #paciente_id").trigger('change');

            $('#orcamentoModal #paciente_nome').attr('disabled', false);
            $('#orcamentoModal #id_convenio').attr('disabled', false);
        }, 50);
    });
}

function editar_orcamento(_id_orcamento) {
    resetar_modal_orcamento();
    $.get(
        '/saude-beta/orcamento/mostrar/' + _id_orcamento,
        function (data) {
            data = $.parseJSON(data);
            $('#orcamentoModalLabel').html('Editar | Proposta de Tratamento | Nº #' + ("000000" + data.orcamento.num_pedido).slice(-6));
            if (data.orcamento.status == 'F') {
                $('#orcamentoModal #status-orcamento')
                    .html('Finalizado')
                    .removeAttr('class')
                    .addClass('tag-pedido-finalizado');
            } else if (data.orcamento.status == 'E') {
                $('#orcamentoModal #status-orcamento')
                    .html('Aprovação do Paciente')
                    .removeAttr('class')
                    .addClass('tag-pedido-aberto');
            } else if (data.orcamento.status == 'A') {
                $('#orcamentoModal #status-orcamento')
                    .html('Em Edição')
                    .removeAttr('class')
                    .addClass('tag-pedido-primary');
            } else {
                $('#orcamentoModal #status-orcamento')
                    .html('Cancelado')
                    .removeAttr('class')
                    .addClass('tag-pedido-cancelado');
            }
            $('#orcamentoModal #id').val(_id_orcamento);
            $('#orcamentoModal #salvar-orcamento').html('Salvar');
            $('#orcamentoModal #paciente_id').val(data.orcamento.id_paciente);
            $('#orcamentoModal #paciente_nome').val(data.orcamento.descr_paciente);
            $('#orcamentoModal #paciente_nome').attr('disabled', true);
            $('#orcamentoModal #profissional_exa_id').val(data.orcamento.id_prof_exa);
            $('#orcamentoModal #profissional_exa_nome').val(data.orcamento.descr_prof_exa);
            $('#orcamentoModal #validade').val(moment(data.orcamento.data_validade).format('DD/MM/YYYY'));
            $('#orcamentoModal #obs').val(data.orcamento.obs);

            var html = '<option value="0">Selecionar Convênio...</option>';
            data.convenio_paciente.forEach(convenio => {
                html += '<option value="' + convenio.id + '">';
                html += convenio.descr;
                html += '</option>';
            });
            $('#orcamentoModal #id_convenio').html(html);
            $('#orcamentoModal #id_convenio').val(data.orcamento.id_convenio);
            $('#orcamentoModal #id_convenio').attr('disabled', true);

            $('#orcamentoModal #table-orcamento-procedimentos > tbody').empty();
            $('#orcamentoModal #table-resumo-orcamento-procedimentos > tbody').empty();
            data.orc_procedimentos.forEach(function (servico, index) {
                index++;
                html = '<tr row_number="' + index + '">';
                html += '    <td width="30%" data-procedimento_id="' + servico.id_procedimento + '" data-procedimento_obs="' + servico.obs + '">';
                html += servico.descr_procedimento;
                if (servico.obs != null && servico.obs != '') html += ' (' + servico.obs + ')';
                html += '    </td>';
                if (servico.dente_regiao != null) html += '<td width="5%" class="text-right" data-dente_regiao="' + servico.dente_regiao + '">' + servico.dente_regiao + '</td>';
                else html += '<td width="5%" class="text-right" data-dente_regiao=""></td>';
                if (servico.face != null) html += '<td width="5%" class="text-right" data-dente_face="' + servico.face + '">' + servico.face + '</td>';
                else html += '<td width="5%" class="text-right" data-dente_face=""></td>';
                html += '    <td width="25%" data-profissional_exe_id="' + servico.id_prof_exe + '">' + servico.descr_prof_exe + '</td>';
                html += '    <td width="12.5%" class="text-right" data-valor="' + servico.valor + '">' + servico.valor.toString().replace('.', ',') + '</td>';
                html += '    <td width="12.5%" class="text-right" data-valor_prazo="' + servico.valor_prazo + '">' + servico.valor_prazo.toString().replace('.', ',') + '</td>';
                html += '    <td width="10%"  class="text-center btn-table-action">';
                html += '        <i class="my-icon far fa-edit"      onclick="editar_pedido_grid(' + index + ')"></i>';
                html += '        <i class="my-icon far fa-trash-alt" onclick="deletar_pedido_grid(' + "'table-orcamento-procedimentos'," + index + '); deletar_pedido_grid(' + "'table-resumo-orcamento-procedimentos'," + index + ')"></i>';
                html += '    </td>';
                html += '</tr>';
                $('#orcamentoModal #table-orcamento-procedimentos > tbody').append(html);

                html = '<tr row_number="' + index + '">';
                html += '    <td width="25%" data-procedimento_id="' + servico.id_procedimento + '" data-procedimento_obs="' + servico.obs + '">';
                html += servico.descr_procedimento;
                if (servico.obs != null && servico.obs != '') html += ' (' + servico.obs + ')';
                html += '    </td>';
                if (servico.dente_regiao != null) html += '<td width="10%" class="text-right" data-dente_regiao="' + servico.dente_regiao + '">' + servico.dente_regiao + '</td>';
                else html += '<td width="10%" class="text-right" data-dente_regiao=""></td>';
                if (servico.face != null) html += '<td width="10%" class="text-right" data-dente_face="' + servico.face + '">' + servico.face + '</td>';
                else html += '<td width="10%" class="text-right" data-dente_face=""></td>';
                html += '    <td width="25%" data-profissional_exe_id="' + servico.id_prof_exe + '">' + servico.descr_prof_exe + '</td>';
                html += '    <td width="15%" class="text-right" data-valor="' + servico.valor + '">' + servico.valor.toString().replace('.', ',') + '</td>';
                html += '    <td width="15%" class="text-right" data-valor_prazo="' + servico.valor_prazo + '">' + servico.valor_prazo.toString().replace('.', ',') + '</td>';
                html += '</tr>';
                $('#orcamentoModal #table-resumo-orcamento-procedimentos > tbody').append(html);
            });


            $('#orcamentoModal #table-orcamento-forma-pag > tbody').empty();
            $('#orcamentoModal #table-orcamento-forma-pag-resumo > tbody').empty();
            data.orc_formas_pag.forEach(function (forma_pag, index) {
                index++;
                html = '<tr row_number="' + index + '">';
                html += '    <td width="10%" data-forma_pag_tipo="' + forma_pag.tipo + '">';
                if (forma_pag.tipo == 'V') html += 'À Vista';
                else html += 'À Prazo';
                html += '    </td>';
                html += '    <td width="55%" data-forma_pag="' + forma_pag.id_forma_pag + '">';
                html += forma_pag.descr_forma_pag;
                html += '    </td>';
                html += '    <td width="15%" data-forma_pag_parcela="' + forma_pag.num_parcela + '">';
                html += '       Em até ' + forma_pag.num_parcela + 'x';
                html += '    </td>';
                html += '    <td width="15%" data-forma_pag_valor="' + forma_pag.valor + '"  class="text-right">';
                html += '       R$ ' + forma_pag.valor.toString().replace('.', ',');
                html += '    </td>';
                html += '    <td width="5%">';
                html += '        <i class="my-icon far fa-trash-alt" onclick="deletar_pedido_grid(' + "'table-orcamento-forma-pag'," + index + '); deletar_pedido_grid(' + "'table-orcamento-forma-pag-resumo'," + index + ')"></i>'
                html += '    </td>';
                html += '</tr>';
                $('#orcamentoModal #table-orcamento-forma-pag > tbody').append(html);

                html = '<tr row_number="' + index + '">';
                html += '    <td width="10%" data-forma_pag_tipo="' + forma_pag.tipo + '">';
                if (forma_pag.tipo == 'V') html += 'À Vista';
                else html += 'À Prazo';
                html += '    </td>';
                html += '    <td width="60%" data-forma_pag="' + forma_pag.id_forma_pag + '">';
                html += forma_pag.descr_forma_pag;
                html += '    </td>';
                html += '    <td width="15%" data-forma_pag_parcela="' + forma_pag.num_parcela + '">';
                html += '       Em até ' + forma_pag.num_parcela + 'x';
                html += '    </td>';
                html += '    <td width="15%" data-forma_pag_valor="' + forma_pag.valor + '"  class="text-right">';
                html += '       R$ ' + forma_pag.valor.toString().replace('.', ',');
                html += '    </td>';
                html += '</tr>';
                $('#orcamentoModal #table-orcamento-forma-pag-resumo > tbody').append(html);
            });
            att_totais_orcamento();
            $('#orcamentoModal').modal('show');
        }
    );
}

function voltar_etapa_wo_orcamento() {
    var etapa_atual = $('.wizard-orcamento > .wo-etapa.selected').data().etapa;
    if (etapa_atual == 2) {
        $('#voltar-orcamento').removeClass('show');
        $('#voltar-orcamento').attr("disabled", true);
    }
    if (etapa_atual == 4) {
        $('#avancar-orcamento').addClass('show');
        $('#avancar-orcamento').attr("disabled", false);
        $('#avancar-orcamento').show();
        $('#salvar-orcamento').hide();
    }
    $('[data-etapa="' + etapa_atual + '"]').removeClass('selected');
    $('[data-etapa="' + (etapa_atual - 1) + '"]').removeClass('success');
    $('[data-etapa="' + (etapa_atual - 1) + '"]').addClass('selected');
    setTimeout(function () {
        $('[data-etapa="' + (etapa_atual - 1) + '"] input').first().focus();
    }, 50);
}

var id_forma;

function avancar_etapa_wo_orcamento() {
    var etapa_atual = $('.wizard-orcamento > .wo-etapa.selected').data().etapa;

    $('#avancar-orcamento').show();
    $('#salvar-orcamento').hide();

    if (etapa_atual == 1) {
        if ($('#orcamentoModal #paciente_id').val() == '') {
            alert('Aviso!\nCampo paciente inválido.');
            return;
        }
        if ($('#orcamentoModal #id_convenio').val() == 0) {
            alert('Aviso!\nA escolha de um convênio é obrigatória.');
            return;
        }
        if ($('#orcamentoModal #profissional_exa_id').val() == '') {
            alert('Aviso!\nCampo profissional examinador inválido.');
            return;
        }
        if ($('#orcamentoModal #validade').val() == '' || $('#validade').val().length != 10) {
            alert('Aviso!\nCampo validade inválido.');
            return;
        }
    } else if (etapa_atual == 2 && $('#orcamentoModal #table-orcamento-procedimentos tbody tr').length == 0) {
        alert('Aviso!\nÉ preciso inserir pelo menos um procedimento para prosseguir.');
        return;
    } else if (etapa_atual == 2) {
        var vista = parseFloat($('[data-table="#table-orcamento-procedimentos"] [data-total_vista]').data().total_vista).toFixed(2).toString().replace('.', ','),
            prazo = parseFloat($('[data-table="#table-orcamento-procedimentos"] [data-total_prazo]').data().total_prazo).toFixed(2).toString().replace('.', ',');

        $('#orcamentoModal #forma_pag_tipo').data('preco_vista', vista).attr('data-preco_vista', vista);
        $('#orcamentoModal #forma_pag_tipo').data('preco_prazo', prazo).attr('data-preco_prazo', prazo);
        $('#orcamentoModal #forma_pag_tipo').trigger('change');

        var id_convenio = $('#orcamentoModal #id_convenio').val();

        $.get('/saude-beta/forma-pag/consulta_descr/Convênio', function (forma) {
            forma = $.parseJSON(forma);
            id_forma = forma.id;

            $.get('/saude-beta/convenio/mostrar/' + id_convenio, function (data) {
                data = $.parseJSON(data);
                if (data.id_pessoa != null) {
                    if (id_forma != null) {
                        $('#orcamentoModal #table-orcamento-forma-pag > tbody').html('');
                        $('#orcamentoModal #table-orcamento-forma-pag-resumo > tbody').html('');
                        add_forma_direta('P', id_forma, 'Convênio', 1, prazo);
                    }
                }
            });
        });
    } else if (etapa_atual == 3 && $('#orcamentoModal #table-orcamento-forma-pag tbody tr').length == 0) {
        alert('Aviso!\nÉ preciso inserir pelo menos um pagamento para prosseguir.');
        return;
    } else if (etapa_atual == 3) {
        var valor_parcelado;
        var tipo;
        var prazo;
        var valor;

        prazo = 'N';
        valor_parcelado = 0;

        $('#orcamentoModal #table-orcamento-forma-pag > tbody > tr').each(function () {
            valor_parcelado += parseFloat($(this).find('[data-forma_pag_valor]').data().forma_pag_valor.replace(',', '.'));
            tipo = $(this).find('[data-forma_pag_tipo]').data().forma_pag_tipo;

            if (tipo != 'V') {
                prazo = 'S';
            }

            if (prazo == 'S') {
                valor = parseFloat($('#orcamentoModal #forma_pag_tipo').data().preco_prazo.replace(',', '.'));
            } else {
                valor = parseFloat($('#orcamentoModal #forma_pag_tipo').data().preco_vista.replace(',', '.'));
            }
        });

        if (valor_parcelado != valor) {
            alert('Aviso!\nÉ preciso parcelar totalmente a proposta de tratamento.');
            return;
        }

        $('#avancar-orcamento').removeClass('show');
        $('#avancar-orcamento').attr("disabled", true);

        if ($.inArray($('#orcamentoModal #status-orcamento').text(), ['Finalizado', 'Em Aprovação', 'Cancelado']) == -1) {
            $('#avancar-orcamento').hide();
            $('#salvar-orcamento').show();
        }
        montar_resumo();
    }
    $('#orcamentoModal [data-etapa="' + etapa_atual + '"]').removeClass('selected');
    $('#orcamentoModal [data-etapa="' + etapa_atual + '"]').addClass('success');
    $('#orcamentoModal [data-etapa="' + (etapa_atual + 1) + '"]').addClass('selected');
    $('#voltar-orcamento').addClass('show');
    $('#voltar-orcamento').attr("disabled", false);

    setTimeout(function () {
        $('[data-etapa="' + (etapa_atual + 1) + '"] input').first().focus();
    }, 50);
}

function clear_orcamento_servicos() {
    $('#inputs-procedimentos #procedimento_descr').val('');
    $('#inputs-procedimentos #procedimento_id').val('');
    $('#inputs-procedimentos #profissional_exe_nome').val('');
    $('#inputs-procedimentos #profissional_exe_id').val('');
    $('#inputs-procedimentos #dente_regiao').val('');
    $('#inputs-procedimentos #dente_face').val('');
    $('#inputs-procedimentos #quantidade').val('');
    $('#inputs-procedimentos #valor').val('');
    $('#inputs-procedimentos #valor_prazo').val('');
    $('#inputs-procedimentos #procedimento_obs').val('');
    $('#inputs-procedimentos #procedimento_index_edit').val('');
    $('#inputs-procedimentos .limpar-edicao').hide();
}

function add_orcamento_servicos() {
    var row_number, dente_regiao_array, html;

    if ($('#orcamentoModal #profissional_exe_id').val() != '' &&
        $('#orcamentoModal #procedimento_id').val() != '' &&
        $('#orcamentoModal #quantidade').val() != '' &&
        $('#orcamentoModal #valor').val() != '') {

        if ($('#orcamentoModal #avista_prazo').val() == 'P' &&
            $('#orcamentoModal #valor').val().toString().replace(',', '.') == $('#valor').data().valor) {
            if (!window.confirm("Atenção!\nO valor declarado é referente a forma de pagamento à vista.\nDeseja continuar?")) {
                return;
            }
        } else if ($('#valor').data().valor_minimo != null &&
            $('#valor').data().valor_minimo != undefined && parseFloat($('#orcamentoModal #valor').val().toString().replace(',', '.')) < $('#valor').data().valor_minimo) {
            alert('O valor declarado não é permitido por ser abaixo do preço mínimo!')
            return;
        }

        if ($('#procedimento_index_edit').val() != '') {
            dente_regiao_array = $('#orcamentoModal #dente_regiao').val();
            dente_regiao_array = dente_regiao_array.split(";");

            if (dente_regiao_array.length > 1) {
                alert('Aviso!\nNão é possível adicionar Dente/Região no modo de edição.');
                return;
            }

            html += '<td width="30%" data-procedimento_id="' + $('#orcamentoModal #procedimento_id').val() + '" data-procedimento_obs="' + $('#orcamentoModal #procedimento_obs').val() + '">';
            html += $('#orcamentoModal #procedimento_descr').val().trim();
            if ($('#orcamentoModal #procedimento_obs').val() != '') html += ' (' + $('#orcamentoModal #procedimento_obs').val() + ')';
            html += '</td>';
            html = '<td width="5%" class="text-right" data-dente_regiao="' + $('#orcamentoModal #dente_regiao').val() + '">';
            html += $('#orcamentoModal #dente_regiao').val();
            html += '</td>';
            html += '<td width="5%" class="text-right" data-dente_face="' + $('#orcamentoModal #dente_face').val().toUpperCase() + '">';
            html += $('#orcamentoModal #dente_face').val().toUpperCase();
            html += '</td>';
            html += '<td width="25%" data-profissional_exe_id="' + $('#orcamentoModal #profissional_exe_id').val() + '">';
            html += $('#orcamentoModal #profissional_exe_nome').val();
            html += '</td>';
            html += '<td width="12.5%" class="text-right" data-valor="' + $('#orcamentoModal #valor').val() + '">';
            html += $('#orcamentoModal #valor').val();
            html += '</td>';
            html += '<td width="12.5%" class="text-right" data-valor_prazo="' + $('#orcamentoModal #valor_prazo').val() + '">';
            html += $('#orcamentoModal #valor_prazo').val();
            html += '</td>';
            html += '<td width="10%"  class="text-center btn-table-action">';
            html += '   <i class="my-icon far fa-edit"      onclick="editar_orc_grid(' + $('#procedimento_index_edit').val() + ')"></i>';
            html += '   <i class="my-icon far fa-trash-alt" onclick="deletar_orc_grid(' + "'table-orcamento-procedimentos'," + $('#procedimento_index_edit').val() + '); deletar_pedido_grid(' + "'table-resumo-orcamento-procedimentos'," + $('#procedimento_index_edit').val() + ')"></i>';
            html += '</td>';
            $('#table-orcamento-procedimentos > tbody tr[row_number="' + $('#procedimento_index_edit').val() + '"]').html(html);

            html += '<td width="30%" data-procedimento_id="' + $('#orcamentoModal #procedimento_id').val() + '" data-procedimento_obs="' + $('#orcamentoModal #procedimento_obs').val() + '">';
            html += $('#orcamentoModal #procedimento_descr').val().trim();
            if ($('#orcamentoModal #procedimento_obs').val() != '') html += ' (' + $('#orcamentoModal #procedimento_obs').val() + ')';
            html += '</td>';
            html = '<td width="10%" class="text-right" data-dente_regiao="' + $('#orcamentoModal #dente_regiao').val() + '">';
            html += $('#orcamentoModal #dente_regiao').val();
            html += '</td>';
            html += '<td width="10%" class="text-right" data-dente_face="' + $('#orcamentoModal #dente_face').val().toUpperCase() + '">';
            html += $('#orcamentoModal #dente_face').val().toUpperCase();
            html += '</td>';
            html += '<td width="25%" data-profissional_exe_id="' + $('#orcamentoModal #profissional_exe_id').val() + '">';
            html += $('#orcamentoModal #profissional_exe_nome').val();
            html += '</td>';
            html += '<td width="12.5%" class="text-right" data-valor="' + $('#orcamentoModal #valor').val() + '">';
            html += $('#orcamentoModal #valor').val();
            html += '</td>';
            html += '<td width="12.5%" class="text-right" data-valor_prazo="' + $('#orcamentoModal #valor_prazo').val() + '">';
            html += $('#orcamentoModal #valor_prazo').val();
            html += '</td>';
            $('#table-resumo-orcamento-procedimentos > tbody tr[row_number="' + $('#procedimento_index_edit').val() + '"]').html(html);
        } else {
            row_number = ($('#table-orcamento-procedimentos > tbody tr').length + 1);
            dente_regiao_array = $('#orcamentoModal #dente_regiao').val();
            dente_regiao_array = dente_regiao_array.split(";");
            html = '';


            for (let j = 0; j < dente_regiao_array.length; j++) {
                for (let i = 0; i < $('#orcamentoModal #quantidade').val(); i++) {
                    html = '<tr row_number="' + row_number + '">';
                    html += '    <td width="30%" data-procedimento_id="' + $('#orcamentoModal #procedimento_id').val() + '" data-procedimento_obs="' + $('#orcamentoModal #procedimento_obs').val() + '">';
                    html += $('#orcamentoModal #procedimento_descr').val().trim();
                    if ($('#orcamentoModal #procedimento_obs').val() != '') html += ' (' + $('#orcamentoModal #procedimento_obs').val() + ')';
                    html += '    </td>';
                    html += '    <td width="5%" class="text-right" data-dente_regiao="' + dente_regiao_array[j].toUpperCase() + '">';
                    html += dente_regiao_array[j].toUpperCase();
                    html += '    </td>';
                    html += '    <td width="5%" class="text-right" data-dente_face="' + $('#orcamentoModal #dente_face').val().toUpperCase() + '">';
                    html += $('#orcamentoModal #dente_face').val().toUpperCase();
                    html += '    </td>';
                    html += '    <td width="25%" data-profissional_exe_id="' + $('#orcamentoModal #profissional_exe_id').val() + '">';
                    html += $('#orcamentoModal #profissional_exe_nome').val();
                    html += '    </td>';
                    html += '    <td width="12.5%" class="text-right" data-valor="' + $('#orcamentoModal #valor').val() + '">';
                    html += $('#orcamentoModal #valor').val();
                    html += '    </td>';
                    html += '    <td width="12.5%" class="text-right" data-valor_prazo="' + $('#orcamentoModal #valor_prazo').val() + '">';
                    html += $('#orcamentoModal #valor_prazo').val();
                    html += '    </td>';
                    html += '    <td width="10%"  class="text-center btn-table-action">';
                    html += '        <i class="my-icon far fa-edit"      onclick="editar_orc_grid(' + row_number + ')"></i>';
                    html += '        <i class="my-icon far fa-trash-alt" onclick="deletar_orc_grid(' + "'table-orcamento-procedimentos'," + row_number + '); deletar_pedido_grid(' + "'table-resumo-orcamento-procedimentos'," + row_number + ')"></i>';
                    html += '    </td>';
                    html += '</tr>';
                    $('#table-orcamento-procedimentos > tbody').append(html);

                    html = '<tr row_number="' + row_number + '">';
                    html += '    <td width="30%" data-procedimento_id="' + $('#orcamentoModal #procedimento_id').val() + '" data-procedimento_obs="' + $('#orcamentoModal #procedimento_obs').val() + '">';
                    html += $('#orcamentoModal #procedimento_descr').val().trim();
                    if ($('#orcamentoModal #procedimento_obs').val() != '') html += ' (' + $('#orcamentoModal #procedimento_obs').val() + ')';
                    html += '    </td>';
                    html += '    <td width="10%" class="text-right" data-dente_regiao="' + dente_regiao_array[j].toUpperCase() + '">';
                    html += dente_regiao_array[j].toUpperCase();
                    html += '    </td>';
                    html += '    <td width="10%" class="text-right" data-dente_face="' + $('#orcamentoModal #dente_face').val().toUpperCase() + '">';
                    html += $('#orcamentoModal #dente_face').val().toUpperCase();
                    html += '    </td>';
                    html += '    <td width="25%" data-profissional_exe_id="' + $('#orcamentoModal #profissional_exe_id').val() + '">';
                    html += $('#orcamentoModal #profissional_exe_nome').val();
                    html += '    </td>';
                    html += '    <td width="12.5%" class="text-right" data-valor="' + $('#orcamentoModal #valor').val() + '">';
                    html += $('#orcamentoModal #valor').val();
                    html += '    </td>';
                    html += '    <td width="12.5%" class="text-right" data-valor_prazo="' + $('#orcamentoModal #valor_prazo').val() + '">';
                    html += $('#orcamentoModal #valor_prazo').val();
                    html += '    </td>';
                    html += '</tr>';
                    $('#table-resumo-orcamento-procedimentos > tbody').append(html);
                    row_number++;
                }
            }
        }
        // $('#orcamentoModal #profissional_exe_id').val('');
        // $('#orcamentoModal #profissional_exe_nome').val('');

        $('#orcamentoModal #procedimento_descr').val('').focus();
        $('#orcamentoModal #procedimento_id').val('');
        $('#orcamentoModal #dente_regiao').val('');
        $('#orcamentoModal #dente_face').val('');
        $('#orcamentoModal #quantidade').val('');
        $('#orcamentoModal #quantidade').prop('readonly', false);
        $('#orcamentoModal #valor').val('');
        $('#orcamentoModal #valor').data('valor_minimo', '');
        $('#orcamentoModal #valor').removeAttr('data-valor_minimo');
        $('#orcamentoModal #valor_prazo').val('');
        $('#orcamentoModal #valor_prazo').data('valor_minimo', '');
        $('#orcamentoModal #valor_prazo').removeAttr('data-valor_minimo');
        $('#orcamentoModal #procedimento_obs').val('');
        $('#orcamentoModal .limpar-edicao').hide();
        att_totais_orcamento();
    } else {
        alert('Favor preencher todos os campos.');
    }
}

function editar_orc_grid(row_number) {
    var _row = $('#table-orcamento-procedimentos tr[row_number=' + row_number + ']');
    $('#inputs-procedimentos #procedimento_descr').val(_row.find('[data-procedimento_id]').text());
    $('#inputs-procedimentos #procedimento_id').val(_row.find('[data-procedimento_id]').data().procedimento_id);
    $('#inputs-procedimentos #profissional_exe_nome').val(_row.find('[data-profissional_exe_id]').text());
    $('#inputs-procedimentos #profissional_exe_id').val(_row.find('[data-profissional_exe_id]').data().profissional_exe_id);
    $('#inputs-procedimentos #dente_regiao').val(_row.find('[data-dente_regiao]').data().dente_regiao);
    $('#inputs-procedimentos #dente_face').val(_row.find('[data-dente_face]').data().dente_face);
    $('#inputs-procedimentos #quantidade').val(1);
    $('#inputs-procedimentos #quantidade').prop('readonly', true);
    $('#inputs-procedimentos #valor').val(_row.find('[data-valor]').data().valor);
    $('#inputs-procedimentos #valor_prazo').val(_row.find('[data-valor_prazo]').data().valor_prazo);
    $('#inputs-procedimentos #procedimento_obs').val(_row.find('[data-procedimento_obs]').data().procedimento_obs);
    $('#inputs-procedimentos #procedimento_index_edit').val(row_number);
    $('.limpar-edicao').show();
}

function deletar_orc_grid(_table, row_number) {
    $('[id="' + _table + '"]').each(function () {
        $(this).find('[row_number="' + row_number + '"]').remove();
    })
    att_totais_orcamento();
}

function att_totais_orcamento() {
    var html,
        orca_valor = 0.0,
        orca_valor_prazo = 0.0;

    $('#orcamentoModal #table-orcamento-procedimentos > tbody > tr').each(function () {
        orca_valor += parseFloat($(this).find('[data-valor]').data().valor.toString().replace(',', '.'));
        orca_valor_prazo += parseFloat($(this).find('[data-valor_prazo]').data().valor_prazo.toString().replace(',', '.'));
    });

    html = '<tr>';
    html += '    <th width="75%" class="text-center" colspan="4"></th>';
    html += '    <th width="10%" class="text-right" data-total_vista="' + orca_valor + '">';
    html += parseFloat(orca_valor).toFixed(2).toString().replace('.', ',');
    html += '    </th>';
    html += '    <th width="10%" class="text-right" data-total_prazo="' + orca_valor_prazo + '">';
    html += parseFloat(orca_valor_prazo).toFixed(2).toString().replace('.', ',');
    html += '    </th>';
    html += '    <th width="5%"  class="text-center"></th>';
    html += '</tr>';
    $('#orcamentoModal [data-table="#table-orcamento-procedimentos"] tfoot').html(html);

    html = '<tr>';
    html += '    <th width="85%" class="text-center" colspan="4"></th>';
    html += '    <th width="15%" class="text-right" data-total_vista="' + orca_valor + '">';
    html += parseFloat(orca_valor).toFixed(2).toString().replace('.', ',');
    html += '    </th>';
    html += '    <th width="15%" class="text-right" data-total_prazo="' + orca_valor_prazo + '">';
    html += parseFloat(orca_valor_prazo).toFixed(2).toString().replace('.', ',');
    html += '    </th>';
    html += '</tr>';
    $('#orcamentoModal #table-resumo-orcamento-procedimentos tfoot').html(html);

    $('#orcamentoModal #table-orcamento-forma-pag      > tbody > tr > [data-forma_pag_tipo="V"],' +
        '#orcamentoModal #table-orcamento-forma-pag-resumo > tbody > tr > [data-forma_pag_tipo="V"]')
        .parent()
        .find('[data-forma_pag_valor]')
        .data('forma_pag_valor', parseFloat(orca_valor).toFixed(2).toString().replace('.', ','))
        .attr('data-forma_pag_tipo', parseFloat(orca_valor).toFixed(2).toString().replace('.', ','))
        .html('R$ ' + parseFloat(orca_valor).toFixed(2).toString().replace('.', ','));

    $('#orcamentoModal #table-orcamento-forma-pag      > tbody > tr > [data-forma_pag_tipo="P"],' +
        '#orcamentoModal #table-orcamento-forma-pag-resumo > tbody > tr > [data-forma_pag_tipo="P"]')
        .parent()
        .find('[data-forma_pag_valor]')
        .data('forma_pag_valor', parseFloat(orca_valor_prazo).toFixed(2).toString().replace('.', ','))
        .attr('data-forma_pag_tipo', parseFloat(orca_valor_prazo).toFixed(2).toString().replace('.', ','))
        .html('R$ ' + parseFloat(orca_valor_prazo).toFixed(2).toString().replace('.', ','));
}

function add_forma_pag() {
    var row_number = ($('#orcamentoModal #table-orcamento-forma-pag > tbody tr').length + 1),
        html = '';

    if ($('#orcamentoModal #forma_pag_tipo').val() != 'E') {
        html = '<tr row_number="' + row_number + '">';
        html += '    <td width="10%" data-forma_pag_tipo="' + $('#orcamentoModal #forma_pag_tipo').val() + '">';
        if ($('#orcamentoModal #forma_pag_tipo').val() == 'V') html += 'À Vista';
        else html += 'À Prazo';
        html += '    </td>';
        html += '    <td width="55%" data-forma_pag="' + $('#orcamentoModal #forma_pag').val() + '">';
        html += $('#orcamentoModal #forma_pag option:selected').text();
        html += '    </td>';
        html += '    <td width="15%" data-forma_pag_parcela="' + $('#orcamentoModal #forma_pag_parcela').val() + '">';
        html += '       Em até ' + $('#orcamentoModal #forma_pag_parcela').val() + 'x<br>de R$ ' + (parseFloat($('#orcamentoModal #forma_pag_valor').val().replace(',', '.')) / parseInt($('#orcamentoModal #forma_pag_parcela').val())).toFixed(2).toString().replace('.', ',');
        html += '    </td>';
        html += '    <td width="15%" data-forma_pag_valor="' + $('#orcamentoModal #forma_pag_valor').val() + '"  class="text-right">';
        html += '       R$ ' + $('#orcamentoModal #forma_pag_valor').val();
        html += '    </td>';
        html += '    <td width="5%">';
        html += '        <i class="my-icon far fa-trash-alt" onclick="deletar_pedido_grid(' + "'table-orcamento-forma-pag'," + row_number + '); deletar_pedido_grid(' + "'table-orcamento-forma-pag-resumo'," + row_number + ')"></i>'
        html += '    </td>';
        html += '</tr>';
        $('#orcamentoModal #table-orcamento-forma-pag > tbody').append(html);

        html = '<tr row_number="' + row_number + '">';
        html += '    <td width="10%" data-forma_pag_tipo="' + $('#orcamentoModal #forma_pag_tipo').val() + '">';
        if ($('#orcamentoModal #forma_pag_tipo').val() == 'V') html += 'À Vista';
        else html += 'À Prazo';
        html += '    </td>';
        html += '    <td width="60%" data-forma_pag="' + $('#orcamentoModal #forma_pag').val() + '">';
        html += $('#orcamentoModal #forma_pag option:selected').text();
        html += '    </td>';
        html += '    <td width="15%" data-forma_pag_parcela="' + $('#orcamentoModal #forma_pag_parcela').val() + '">';
        html += '       Em até ' + $('#orcamentoModal #forma_pag_parcela').val() + 'x<br>de R$ ' + (parseFloat($('#orcamentoModal #forma_pag_valor').val().replace(',', '.')) / parseInt($('#orcamentoModal #forma_pag_parcela').val())).toFixed(2).toString().replace('.', ',');
        html += '    </td>';
        html += '    <td width="15%" data-forma_pag_valor="' + $('#orcamentoModal #forma_pag_valor').val() + '"  class="text-right">';
        html += '       R$ ' + $('#orcamentoModal #forma_pag_valor').val();
        html += '    </td>';
        html += '</tr>';
        $('#orcamentoModal #table-orcamento-forma-pag-resumo > tbody').append(html);
    } else {
        if (parseFloat($('#orcamentoModal #forma_pag_valor').val().replace(',', '.')) > parseFloat($('#orcamentoModal #forma_pag_valor_ent').val().replace(',', '.'))) {
            var forma_vista = $('#orcamentoModal #forma_pag_vista').val();
            var texto_vista = $('#orcamentoModal #forma_pag_vista option:selected').text();
            var valor_vista = $('#orcamentoModal #forma_pag_valor_ent').val();

            var forma_prazo = $('#orcamentoModal #forma_pag').val();
            var texto_prazo = $('#orcamentoModal #forma_pag option:selected').text();
            var valor_prazo = parseFloat($('#orcamentoModal #forma_pag_valor').val().replace(',', '.')) - parseFloat($('#orcamentoModal #forma_pag_valor_ent').val().replace(',', '.'));
            var valor_prazo = valor_prazo.toFixed(2).toString().replace('.', ',');
            var parcelas = parseInt($('#orcamentoModal #forma_pag_parcela').val());

            add_forma_direta('V', forma_vista, texto_vista, 1, valor_vista);
            add_forma_direta('P', forma_prazo, texto_prazo, parcelas, valor_prazo);
        } else {
            alert("Não é possível dar uma entrada em um valor maior ou igual ao valor total.");
        }
    }
}

function add_forma_direta(tipo_pag, id_forma, forma, parcela, valor) {
    var row_number = ($('#orcamentoModal #table-orcamento-forma-pag > tbody tr').length + 1),
        html = '';

    html = '<tr row_number="' + row_number + '">';
    html += '    <td width="10%" data-forma_pag_tipo="' + tipo_pag + '">';
    if (tipo_pag == 'V') html += 'À Vista';
    else html += 'À Prazo';
    html += '    </td>';
    html += '    <td width="55%" data-forma_pag="' + id_forma + '">';
    html += forma;
    html += '    </td>';
    html += '    <td width="15%" data-forma_pag_parcela="' + parcela + '">';
    html += '       Em até ' + parcela + 'x<br>de R$ ' + (parseFloat(valor.replace(',', '.')) / parcela).toFixed(2).toString().replace('.', ',');
    html += '    </td>';
    html += '    <td width="15%" data-forma_pag_valor="' + valor + '"  class="text-right">';
    html += '       R$ ' + valor;
    html += '    </td>';
    html += '    <td width="5%">';
    html += '        <i class="my-icon far fa-trash-alt" onclick="deletar_pedido_grid(' + "'table-orcamento-forma-pag'," + row_number + '); deletar_pedido_grid(' + "'table-orcamento-forma-pag-resumo'," + row_number + ')"></i>'
    html += '    </td>';
    html += '</tr>';
    $('#orcamentoModal #table-orcamento-forma-pag > tbody').append(html);

    html = '<tr row_number="' + row_number + '">';
    html += '    <td width="10%" data-forma_pag_tipo="' + tipo_pag + '">';
    if (tipo_pag == 'V') html += 'À Vista';
    else html += 'À Prazo';
    html += '    </td>';
    html += '    <td width="60%" data-forma_pag="' + id_forma + '">';
    html += forma;
    html += '    </td>';
    html += '    <td width="15%" data-forma_pag_parcela="' + parcela + '">';
    html += '       Em até ' + parcela + 'x<br>de R$ ' + (parseFloat(valor.replace(',', '.')) / parcela).toFixed(2).toString().replace('.', ',');
    html += '    </td>';
    html += '    <td width="15%" data-forma_pag_valor="' + valor + '"  class="text-right">';
    html += '       R$ ' + valor;
    html += '    </td>';
    html += '</tr>';
    $('#orcamentoModal #table-orcamento-forma-pag-resumo > tbody').append(html);
}

function montar_resumo() {
    var paciente_id = $('#orcamentoModal #paciente_id').val();
    var paciente_nome = $('#orcamentoModal #paciente_nome').val();
    var id_convenio = $('#orcamentoModal #id_convenio').val();
    var descr_convenio = $('#orcamentoModal #id_convenio option:selected').text();
    var profissional_exa_id = $('#orcamentoModal #profissional_exa_id').val();
    var profissional_exa_nome = $('#orcamentoModal #profissional_exa_nome').val();
    var validade = $('#orcamentoModal #validade').val();
    var obs = $('#orcamentoModal #obs').val();

    $('#orcamentoModal [data-resumo_paciente]').data('resumo_paciente', paciente_id).attr('data-resumo_paciente', paciente_id);
    $('#orcamentoModal [data-resumo_paciente]').html(paciente_nome);
    $('#orcamentoModal [data-resumo_paciente_convenio]').data('resumo_paciente_convenio', id_convenio).attr('data-resumo_paciente_convenio', id_convenio);
    $('#orcamentoModal [data-resumo_paciente_convenio]').html(descr_convenio);
    $('#orcamentoModal [data-resumo_validade]').data('resumo_validade', validade).attr('data-resumo_validade', validade);
    $('#orcamentoModal [data-resumo_validade]').html(validade);
    $('#orcamentoModal [data-resumo_profissional_exa]').data('resumo_profissional_exa', profissional_exa_id).attr('data-resumo_profissional_exa', profissional_exa_id);
    $('#orcamentoModal [data-resumo_profissional_exa]').html(profissional_exa_nome);
    $('#orcamentoModal [data-resumo_obs]').data('resumo_obs', obs).attr('data-resumo_obs', obs);
    if (obs != '') $('#orcamentoModal [data-resumo_obs]').html(obs);
    else $('#orcamentoModal [data-resumo_obs]').html('Sem Observação');
}

function deletar_pedido_grid(_table, row_number) {
    $('[id="' + _table + '"]').each(function () {
        $(this).find('[row_number="' + row_number + '"]').remove();
    })
    att_totais_orcamento();
}

function salvar_orcamento() {
    var id = $('#orcamentoModal #id').val(),
        id_paciente = $('#orcamentoModal [data-resumo_paciente]').data().resumo_paciente,
        id_convenio = $('#orcamentoModal [data-resumo_paciente_convenio]').data().resumo_paciente_convenio,
        data_validade = $('#orcamentoModal [data-resumo_validade]').data().resumo_validade,
        id_profissional_exa = $('[data-resumo_profissional_exa]').data().resumo_profissional_exa,
        obs = $('#orcamentoModal [data-resumo_obs]').data().resumo_obs,
        procedimentos = [],
        formas_pag = [];

    if (confirm('Atenção!\nGostaria de encaminhar para aprovação?')) {
        _status = 'E';
    } else {
        _status = 'A';
    }

    $('#orcamentoModal #table-resumo-orcamento-procedimentos tbody tr').each(function () {
        procedimentos.push({
            profissional_exe_id: $(this).find('[data-profissional_exe_id]').data().profissional_exe_id,
            procedimento_id: $(this).find('[data-procedimento_id]').data().procedimento_id,
            dente_regiao: $(this).find('[data-dente_regiao]').data().dente_regiao,
            dente_face: $(this).find('[data-dente_face]').data().dente_face,
            valor: String($(this).find('[data-valor]').data().valor).replace(',', '.'),
            valor_prazo: String($(this).find('[data-valor_prazo]').data().valor_prazo).replace(',', '.'),
            obs: $(this).find('[data-procedimento_obs]').data().procedimento_obs
        });
    });

    $('#orcamentoModal #table-orcamento-forma-pag-resumo tbody tr').each(function () {
        formas_pag.push({
            tipo: $(this).find('[data-forma_pag_tipo').data().forma_pag_tipo,
            forma_pag: $(this).find('[data-forma_pag').data().forma_pag,
            parcela: $(this).find('[data-forma_pag_parcela').data().forma_pag_parcela,
            valor: $(this).find('[data-forma_pag_valor').data().forma_pag_valor.replace(',', '.')
        });
    });

    $.post(
        '/saude-beta/orcamento/salvar', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: id,
        id_paciente: id_paciente,
        id_convenio: id_convenio,
        data_validade: data_validade,
        id_profissional_exa: id_profissional_exa,
        status: _status,
        obs: obs,
        procedimentos: procedimentos,
        formas_pag: formas_pag
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                data = $.parseJSON(data);
                // if (_status == 'E') new_system_window('orcamento/imprimir/' + data.id);
                if (window.location.pathname.includes('/pessoa/prontuario')) {
                    orcamentos_por_pessoa(id_paciente);
                    $('#orcamentoModal').modal('hide');
                } else {
                    document.location.reload(true);
                }
            }
        }
    );
}

function mudar_status_orcamento(id, _status) {
    var msg = '';
    if (_status == 'C') msg = 'Deseja cancelar essa proposta de orçamento?';
    else if (_status == 'E') msg = 'Deseja encaminhar para aprovação do paciente?';
    else if (_status == 'F') msg = 'Deseja iniciar conversão da proposta do tratamento para um plano de tratamento?';
    if (confirm('Atenção!\n' + msg)) {
        $.post(
            '/saude-beta/orcamento/mudar-status', {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: id,
            status: _status
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {
                    data = $.parseJSON(data);
                    // if (_status == 'E') new_system_window('orcamento/imprimir/' + data.id);
                    if (window.location.pathname.includes('/pessoa/prontuario')) {
                        orcamentos_por_pessoa($('#id_pessoa_prontuario').val());
                    } else {
                        document.location.reload(true);
                    }
                }
            }
        );
    }
}
// ————————————————————————————————————————————————————————————————————————————————————— //
// ————————————————————————————————————————————————————————————————————————————————————— //
// ——————————————————————————————— PROPOSTA DE TRATAMENTO —————————————————————————————— //
// ————————————————————————————————————————————————————————————————————————————————————— //
// ————————————————————————————————————————————————————————————————————————————————————— //

// ------------------------------------------------------------------------------------- //
// ------------------------------------------------------------------------------------- //
// -------------------------------- PLANO DE TRATAMENTO -------------------------------- //
// ------------------------------------------------------------------------------------- //
// ------------------------------------------------------------------------------------- //



function att_pedido_total_proc_pagamento_antigo() {
    var total_parcelas = 0,
        total_valor = 0;
    $('#pedidoAntigoModal #table-pedido-forma-pag > tbody tr').each(function () {
        total_parcelas += parseInt($(this).find('[data-forma_pag_parcela]').data().forma_pag_parcela.toString().replace(',', '.'));
        total_valor += parseFloat($(this).find('[data-forma_pag_valor]').data().forma_pag_valor.toString().replace(',', '.'));
    });

    $('#pedidoAntigoModal #table-pedido-forma-pag [data-total_pag_parcela]')
        .data('total_pag_parcela', total_parcelas)
        .attr('data-total_pag_parcela', total_parcelas)
        .html(total_parcelas);

    $('#pedidoAntigoModal #table-pedido-forma-pag [data-total_pag_valor]')
        .data('total_pag_valor', total_valor)
        .attr('data-total_pag_valor', total_valor)
        .html('R$ ' + parseFloat(total_valor).toFixed(2).toString().replace('.', ','));

    $('#pedidoAntigoModal #table-pedido-forma-pag-resumo [data-total_pag_parcela]')
        .data('total_pag_parcela', total_parcelas)
        .attr('data-total_pag_parcela', total_parcelas)
        .html(total_parcelas);

    $('#pedidoAntigoModal #table-pedido-forma-pag-resumo [data-total_pag_valor]')
        .data('total_pag_valor', total_valor)
        .attr('data-total_pag_valor', total_valor)
        .html('R$ ' + parseFloat(total_valor).toFixed(2).toString().replace('.', ','));
}
function resetar_modal_pedido(id) {
    $('#pedidoModal [data-etapa]').removeClass('selected').removeClass('success');
    $('#pedidoModal [data-etapa="1"]').addClass('selected');
    $('#pedidoModal #voltar-pedido').removeClass('show');
    $('#pedidoModal #voltar-pedido').attr("disabled", true);
    $('#pedidoModal #avancar-pedido').addClass('show');
    $('#pedidoModal #avancar-pedido').attr("disabled", false);
    $('#pedidoModal #tabela-planos tbody').empty()
    $('#pedidoModal #tabela-planos2 tbody').empty()
    $('#pedidoModal #table-pedido-forma-pag tbody').empty()
    $('#pedidoModal #table-pedido-forma-pag-resumo tbody').empty()
    $('#pedidoModal #avancar-pedido').show();
    $('#pedidoModal #salvar-pedido').hide();
    alterouPlano = new Array();
    desc_sup = 0;
    desc_motivo = "";
    travarInsercao = false;
}
function resetar_modal_pedido_antigo(id) {
    $('#pedidoAntigoModal [data-etapa]').removeClass('selected').removeClass('success');
    $('#pedidoAntigoModal [data-etapa="1"]').addClass('selected');
    $('#pedidoAntigoModal #voltar-pedido').removeClass('show');
    $('#pedidoAntigoModal #voltar-pedido').attr("disabled", true);
    $('#pedidoAntigoModal #avancar-pedido').addClass('show');
    $('#pedidoAntigoModal #avancar-pedido').attr("disabled", false);
    $('#pedidoAntigoModal #tabela-planos tbody').empty()
    $('#pedidoAntigoModal #tabela-planos2 tbody').empty()
    $('#pedidoAntigoModal #table-pedido-forma-pag tbody').empty()
    $('#pedidoAntigoModal #table-pedido-forma-pag-resumo tbody').empty()
    $('#pedidoAntigoModal #avancar-pedido').show();
    $('#pedidoModal #salvar-pedido').hide();
}



function editar_pedido(_id_pedido) {
    $.get(
        '/saude-beta/pedido/mostrar/' + _id_pedido,
        function (data) {
            data = $.parseJSON(data);
            $('#pedidoModalLabel').html('Editar | Contrato | Nº #' + ("000000" + data.pedido.num_pedido).slice(-6));
            if (data.pedido.status == 'F') {
                $('#status-pedido')
                    .html('Finalizado')
                    .removeAttr('class')
                    .addClass('tag-pedido-finalizado');
            } else if (data.pedido.status == 'E') {
                $('#status-pedido')
                    .html('Aprovação do Paciente')
                    .removeAttr('class')
                    .addClass('tag-pedido-aberto');
            } else if (data.pedido.status == 'A') {
                $('#status-pedido')
                    .html('Em Edição')
                    .removeAttr('class')
                    .addClass('tag-pedido-primary');
            } else {
                $('#status-pedido')
                    .html('Cancelado')
                    .removeAttr('class')
                    .addClass('tag-pedido-cancelado');
            }
            $('#pedidoModal #pedido_id').val(_id_pedido);
            $('#pedidoModal #salvar-pedido').html('Editar');
            $('#pedidoModal #pedido_paciente_id').val(data.pedido.id_paciente);
            $('#pedidoModal #pedido_paciente_nome').val(data.pedido.descr_paciente);
            $('#pedidoModal #pedido_profissional_exa_id').val(data.pedido.id_prof_exa);
            $('#pedidoModal #pedido_profissional_exa_nome').val(data.pedido.descr_prof_exa);
            $('#pedidoModal #pedido_validade').val(moment(data.pedido.data_validade).format('DD/MM/YYYY'));
            $('#pedidoModal #pedido_obs').val(data.pedido.obs);

            var html = '<option value="0">Selecionar Convênio...</option>';
            data.convenio_paciente.forEach(convenio => {
                html += '<option value="' + convenio.id + '">';
                html += convenio.descr;
                html += '</option>';
            });
            $('#pedidoModal #pedido_id_convenio').html(html);

            $('#pedidoModal #table-pedido-procedimentos > tbody').empty();
            $('#pedidoModal #table-resumo-pedido-procedimentos > tbody').empty();
            data.ped_procedimentos.forEach(function (servico, index) {
                index++;
                html = '<tr row_number="' + index + '">';
                if (servico.dente_regiao != null) html += '<td width="10%" class="text-right" data-dente_regiao="' + servico.dente_regiao + '">' + servico.dente_regiao + '</td>';
                else html += '<td width="10%" class="text-right" data-dente_regiao=""></td>';
                if (servico.face != null) html += '<td width="10%" class="text-right" data-dente_face="' + servico.face + '">' + servico.face + '</td>';
                else html += '<td width="10%" class="text-right" data-dente_face=""></td>';
                html += '    <td width="25%" data-procedimento_id="' + servico.id_procedimento + '" data-procedimento_obs="' + servico.obs + '">';
                html += servico.descr_procedimento;
                if (servico.obs != null && servico.obs != '') html += ' (' + servico.obs + ')';
                html += '    </td>';
                html += '    <td width="25%" data-profissional_exe_id="' + servico.id_prof_exe + '">' + servico.descr_prof_exe + '</td>';
                if (data.pedido.tipo_forma_pag == 'V') {
                    html += '    <td width="12.5%" class="text-right" data-valor="' + servico.valor + '">' + servico.valor.toString().replace('.', ',') + '</td>';
                    html += '    <td width="12.5%" class="text-right" data-valor_prazo="' + servico.valor_prazo + '">' + servico.valor_prazo.toString().replace('.', ',') + '</td>';
                } else {
                    html += '    <td width="12.5%" class="text-right" data-valor="' + servico.valor_vista + '">' + servico.valor_vista.toString().replace('.', ',') + '</td>';
                    html += '    <td width="12.5%" class="text-right" data-valor_prazo="' + servico.valor + '">' + servico.valor.toString().replace('.', ',') + '</td>';
                }
                html += '    <td width="5%"  class="text-center btn-table-action">';
                html += '        <i class="my-icon far fa-trash-alt" onclick="deletar_pedido_grid(' + "'table-pedido-procedimentos'," + index + '); deletar_pedido_grid(' + "'table-resumo-pedido-procedimentos'," + index + ')"></i>';
                html += '    </td>';
                html += '</tr>';
                $('#pedidoModal #table-pedido-procedimentos > tbody').append(html);

                html = '<tr row_number="' + index + '">';
                if (servico.dente_regiao != null) html += '<td width="10%" class="text-right" data-dente_regiao="' + servico.dente_regiao + '">' + servico.dente_regiao + '</td>';
                else html += '<td width="10%" class="text-right" data-dente_regiao=""></td>';
                if (servico.face != null) html += '<td width="10%" class="text-right" data-dente_face="' + servico.face + '">' + servico.face + '</td>';
                else html += '<td width="10%" class="text-right" data-dente_face=""></td>';
                html += '    <td width="25%" data-procedimento_id="' + servico.id_procedimento + '" data-procedimento_obs="' + servico.obs + '">';
                html += servico.descr_procedimento;
                if (servico.obs != null && servico.obs != '') html += ' (' + servico.obs + ')';
                html += '    </td>';
                html += '    <td width="25%" data-profissional_exe_id="' + servico.id_prof_exe + '">' + servico.descr_prof_exe + '</td>';
                if (data.pedido.tipo_forma_pag == 'V') {
                    html += '    <td width="15%" class="text-right" data-valor="' + servico.valor + '">' + servico.valor.toString().replace('.', ',') + '</td>';
                    html += '    <td width="15%" class="text-right" data-valor_prazo="' + servico.valor_prazo + '">' + servico.valor_prazo.toString().replace('.', ',') + '</td>';
                } else {
                    html += '    <td width="15%" class="text-right" data-valor="' + servico.valor_vista + '">' + servico.valor_vista.toString().replace('.', ',') + '</td>';
                    html += '    <td width="15%" class="text-right" data-valor_prazo="' + servico.valor + '">' + servico.valor.toString().replace('.', ',') + '</td>';
                }
                html += '</tr>';
                $('#pedidoModal #table-resumo-pedido-procedimentos > tbody').append(html);
            });


            $('#pedidoModal #table-pedido-forma-pag > tbody').empty();
            $('#pedidoModal #table-pedido-forma-pag-resumo > tbody').empty();
            
            data.ped_formas_pag.forEach(function (forma_pag, index) {
                console.log(forma_pag.valor_total);
                console.log(forma_pag.num_total_parcela);
                index++;
                html = '<tr row_number="' + index + '">';
                html += '    <td width="25%" data-forma_pag="' + forma_pag.id + '">';
                html += forma_pag.descr_forma_pag;
                html += '    </td>';
                html += '    <td width="25%" data-financeira_id="' + forma_pag.id_financeira + '">';
                if (forma_pag.id_financeira != 0) html += forma_pag.descr_financeira;
                html += '    </td>';
                html += '    <td width="15%" data-forma_pag_parcela="' + forma_pag.num_total_parcela + '"  class="text-right">';
                html += forma_pag.num_total_parcela + 'x de R$ ' + (forma_pag.valor_total / forma_pag.num_total_parcela).toFixed(2).toString().replace('.', ',');
                html += '    </td>';
                html += '    <td width="15%" data-forma_pag_valor="' + parseFloat(forma_pag.valor_total).toFixed(2).toString().replace('.', ','); + '"  class="text-right">';
                html += '       R$ ' + parseFloat(forma_pag.valor_total).toFixed(2).toString().replace('.', ',');
                html += '    </td>';
                html += '    <td width="15%" data-pedido_data_vencimento="' + moment(forma_pag.data_vencimento).format('DD/MM/YYYY') + '">';
                html += moment(forma_pag.data_vencimento).format('DD/MM/YYYY');
                html += '    </td>';
                html += '    <td width="5%">';
                html += '        <i class="my-icon far fa-trash-alt" onclick="deletar_pedido_grid(' + "'table-pedido-forma-pag'," + index + '); deletar_pedido_grid(' + "'table-pedido-forma-pag-resumo'," + index + '); att_pedido_total_proc_pagamento()"></i>';
                html += '    </td>';
                html += '</tr>';
                $('#table-pedido-forma-pag > tbody').append(html);

                html = '<tr row_number="' + index + '">';
                html += '    <td width="27.5%" data-forma_pag="' + forma_pag.id + '">';
                html += forma_pag.descr_forma_pag;
                html += '    </td>';
                html += '    <td width="27.5%" data-financeira_id="' + forma_pag.id_financeira + '">';
                if (forma_pag.id_financeira != 0) html += forma_pag.descr_financeira;
                html += '    </td>';
                html += '    <td width="15%" data-forma_pag_parcela="' + forma_pag.num_total_parcela + '"  class="text-right">';
                html += forma_pag.num_total_parcela + 'x de R$ ' + (forma_pag.valor_total / forma_pag.num_total_parcela).toFixed(2).toString().replace('.', ',');
                html += '    </td>';
                html += '    <td width="15%" data-forma_pag_valor="' + parseFloat(forma_pag.valor_total).toFixed(2).toString().replace('.', ',') + '"  class="text-right">';
                html += '       R$ ' + parseFloat(forma_pag.valor_total).toFixed(2).toString().replace('.', ',');
                html += '    </td>';
                html += '    <td width="15%" data-pedido_data_vencimento="' + moment(forma_pag.data_vencimento).format('DD/MM/YYYY') + '">';
                html += moment(forma_pag.data_vencimento).format('DD/MM/YYYY');
                html += '    </td>';
                html += '</tr>';
                $('#table-pedido-forma-pag-resumo > tbody').append(html);
            });
            att_totais_pedido();
            $('#pedidoModal').modal('show');
            setTimeout(function () {
                $("#pedidoModal #pedido_paciente_nome").first().focus();
                $("#pedidoModal #pedido_paciente_id")
                    .trigger('change', data.pedido.id_convenio);
            }, 50);
        }
    );
}

function controlFormAgenda() {
    $("#agenda_profissional").css('filter', 'brightness(1)')
    $("#agenda_profissional").val('')
    autocomplete_agenda($("#agenda_profissional"))
    window.addEventListener('click', function (e) {
        if (!document.querySelector("#agenda_profissional").contains(e.target)) {
            $("#agenda_profissional").css('filter', 'brightness(0.9)')
        }
    }, true);
}
function setar_tipo_forma_pag_pedido_antigo(_tipo) {
    $.get('/saude-beta/forma-pag/listar/' + _tipo, function (data) {
        data = $.parseJSON(data);
        var html = '';
        data.forEach(forma_pag => {
            if (forma_pag.id != 102) {
                html += '<option value="' + forma_pag.id + '">';
                html += forma_pag.descr;
                html += '</option>';
            }
        });
        $('#pedidoAntigoModal #pedido_forma_pag').html(html).trigger('change');
    });

    var valor_pendente = 0;
    if (_tipo == 'V') {
        $('#pedidoAntigoModal #pedido_forma_pag_parcela').val(1);
        $('#pedidoAntigoModal #pedido_forma_pag_parcela').parent().hide();
        $('#pedidoAntigoModal #pedido_forma_pag_valor').val($(this).data().preco_vista);
        $('#pedidoAntigoModal #pedido_data_vencimento').val(moment().format('DD/MM/YYYY'));

        valor_pendente = document.querySelector("#pedidoAntigoModal #valor_total_planos").innerHTML;
    } else {
        $('#pedidoAntigoModal #pedido_forma_pag_parcela').parent().show();
        $('#pedidoAntigoModal #pedido_forma_pag_parcela').val(1);
        // $('#pedido_forma_pag_valor').val($(this).data().preco_prazo);
        $('#pedidoAntigoModal #pedido_data_vencimento').val(moment().add(30, 'days').format('DD/MM/YYYY'));

        valor_pendente = document.querySelector("#pedidoAntigoModal #valor_total_planos").innerHTML;
    }
    $.get(
        '/saude-beta/pessoa/verificar-associado/' + $("#pedidoAntigoModal #pedido_paciente_nome").val(),
        function (data, status) {
            console.log(data + ' | ' + status)
            if (data == 'true') {
                $.get(
                    '/saude-beta/pedido'
                )
            }
        }
    )
    $('#pedidoAntigoModal #table-pedido-forma-pag [data-total_pag_pendente]')
        .data('total_pag_pendente', valor_pendente)
        .attr('data-total_pag_pendente', valor_pendente)
        .html('Valor Total dos procedimentos - R$ ' + parseFloat(valor_pendente).toFixed(2).toString().replace('.', ','));

    $('#pedidoAntigoModal #pedido_forma_pag_tipo').val(_tipo);
    if ($('#pedido_forma_pag_tipo').val() != _tipo) {
        $('#table-pedido-forma-pag > tbody').empty()
        $('#table-pedido-forma-pag-resumo > tbody').empty()
    }
    att_pedido_total_proc_pagamento();
}
function setar_tipo_forma_pag_pedido(_tipo) {
    $.get('/saude-beta/forma-pag/listar/' + _tipo, function (data) {
        data = $.parseJSON(data);
        var html = '';
        data.forEach(forma_pag => {
            try {
                if (parseInt(document.querySelector("#criarAgendamentoModal #convenio_id").value) || forma_pag.id != 102) {
                    html += '<option value="' + forma_pag.id + '">';
                    html += forma_pag.descr;
                    html += '</option>';
                }    
            } catch(err) {
                if (forma_pag.id != 102) {
                    html += '<option value="' + forma_pag.id + '">';
                    html += forma_pag.descr;
                    html += '</option>';    
                }
            }
        });
        $('#pedido_forma_pag').html(html).trigger('change');
    });

    var valor_pendente = 0;
    if (_tipo == 'V') {
        $('#pedido_forma_pag_parcela').val(1);
        $('#pedido_forma_pag_parcela').parent().hide();
        $('#pedido_forma_pag_valor').val($(this).data().preco_vista);
        $('#pedido_data_vencimento').val(moment().format('DD/MM/YYYY'));

        valor_pendente = document.querySelector("#valor_total_planos").innerHTML;
    } else {
        $('#pedido_forma_pag_parcela').parent().show();
        $('#pedido_forma_pag_parcela').val(1);
        // $('#pedido_forma_pag_valor').val($(this).data().preco_prazo);
        $('#pedido_data_vencimento').val(moment().add(30, 'days').format('DD/MM/YYYY'));

        valor_pendente = document.querySelector("#valor_total_planos").innerHTML;
    }
    valor_pendente = parseInt(phoneInt(valor_pendente)) / 100;
    $.get(
        '/saude-beta/pessoa/verificar-associado/' + $("#pedido_paciente_nome").val(),
        function (data, status) {
            console.log(data + ' | ' + status)
            if (data == 'true') {
                $.get(
                    '/saude-beta/pedido'
                )
            }
        }
    )
    $('#table-pedido-forma-pag [data-total_pag_pendente]')
        .data('total_pag_pendente', valor_pendente)
        .attr('data-total_pag_pendente', valor_pendente)
        .html('Valor Total dos procedimentos - R$ ' + parseFloat(valor_pendente).toFixed(2).toString().replace('.', ','));

    $('#pedido_forma_pag_tipo').val(_tipo);
    if ($('#pedido_forma_pag_tipo').val() != _tipo) {
        $('#table-pedido-forma-pag > tbody').empty()
        $('#table-pedido-forma-pag-resumo > tbody').empty()
    }
    att_pedido_total_proc_pagamento();
}

var $data;
function irpara_pedido(indice, focar) {
    var etapa_atual = $('.wizard-pedido > .wo-etapa.selected').data().etapa;
    var etapa_atualAnt = etapa_atual;
    if (indice > etapa_atual) {
        do {
            var teste = avancar_etapa_wo_pedido(etapa_atualAnt, focar);
            etapa_atual = $('.wizard-pedido > .wo-etapa.selected').data().etapa;
        } while (teste && indice > etapa_atual)
        if (!teste) irpara_pedido(etapa_atualAnt, false);
    } else if (indice < etapa_atual) {
        do {
            voltar_etapa_wo_pedido(focar);
            etapa_atual = $('.wizard-pedido > .wo-etapa.selected').data().etapa;
        } while (indice < etapa_atual)
    }
}
var travarInsercao = false;
function voltar_etapa_wo_pedido(focar) {
    var etapa_atual = $('.wizard-pedido > .wo-etapa.selected').data().etapa;
    if (etapa_atual == 2) {
        $('#voltar-pedido').removeClass('show');
        $('#voltar-pedido').attr("disabled", true);
        travarInsercao = true;
    } else if (etapa_atual == 4) return;
    $('#pedidoModal [data-etapa="' + etapa_atual + '"]').removeClass('selected');
    $('#pedidoModal [data-etapa="' + (etapa_atual - 1) + '"]').removeClass('success');
    $('#pedidoModal [data-etapa="' + (etapa_atual - 1) + '"]').addClass('selected');
    if (focar) {
        setTimeout(function () {
            $('#pedidoModal [data-etapa="' + (etapa_atual - 1) + '"] input').first().focus();
        }, 50);
    }
}
function voltar_etapa_wo_pedido_antigo() {
    var etapa_atual = $('#pedidoAntigoModal .wizard-pedido > .wo-etapa.selected').data().etapa;
    if (etapa_atual == 2) {
        $('#pedidoAntigoModal #voltar-pedido').removeClass('show');
        $('#pedidoAntigoModal #voltar-pedido').attr("disabled", true);
    }
    if (etapa_atual == 4) {
        $('#pedidoAntigoModal #avancar-pedido').addClass('show');
        $('#pedidoAntigoModal #avancar-pedido').attr("disabled", false);
        $('#pedidoAntigoModal #avancar-pedido').show();
        $('#pedidoAntigoModal #salvar-pedido').hide();
    }
    $('#pedidoAntigoModal [data-etapa="' + etapa_atual + '"]').removeClass('selected');
    $('#pedidoAntigoModal [data-etapa="' + (etapa_atual - 1) + '"]').removeClass('success');
    $('#pedidoAntigoModal [data-etapa="' + (etapa_atual - 1) + '"]').addClass('selected');
    setTimeout(function () {
        $('#pedidoAntigoModal [data-etapa="' + (etapa_atual - 1) + '"] input').first().focus();
    }, 50);
}
function excluir_meta($id) {
    $.post('/saude-beta/procedimento/excluir-meta', {
        _token: $("meta[name=csrf-token]").attr('content'),
        id_meta: $id
    }, function (data, status) {
        console.log(data + ' | ' + status)
        if (data.error) {
            alert(data.error)
        }
        else {
            listar_metas($("#metasModal #modalidade").val())
        }
    })
}
function abrir_modal_recepcao(bAbrir) {
    document.querySelectorAll(".grades-view-recepcao").forEach(el => { el.innerHTML = '' })
    for (i = 1; i < 8; i++) {
        $(".title-dia h1").filter('[data-dia_semana="' + i + '"]').html('');
    }
    $(".grades-view-recepcao").filter("[data-dia_semana='1']").html('<div style="min-height:75px"><div style="position: absolute;width: 100%;background: white;height: 50%;margin-left: -41px;padding: 25px 0px 0px 0px;"><img src="img/carregando-azul.gif" style="width: 70px;height: 70px;"></div></div>')
    $("#colunas-dias").css('opacity', '.7')

    $.get('/saude-beta/agenda/listar-todos-agendamento-semanal', {
        date_selected: $('.mini-calendar h6.selected').data().year + '-' +
            $('.mini-calendar h6.selected').data().month + '-' +
            $('.mini-calendar h6.selected').data().day
    },
        function (data, status) {
            console.log(data + ' | ' + status)
            data = $.parseJSON(data);

            document.querySelectorAll(".grades-view-recepcao").forEach(el => { el.innerHTML = '' })
            for (i = 1; i < 8; i++) {
                $(".title-dia h1").filter('[data-dia_semana="' + i + '"]').html(data.dias[i - 1]);
            }
            $("#colunas-dias").css('opacity', '1')
            for (i = 0; i < data.profissionais.length; i++) {
                j = 1
                l = 0
                data.grade_existe[i].forEach(grade_existe => {
                    if (data.grade_existe[i].filter(x => x === 'S').length > 0) {
                        if (grade_existe === 'S') {
                            html = ' <li onclick="selecionar_profissional(' + data.profissionais[i].id + ')" ondblclick="expandirAgendamentoView(' + data.profissionais[i].id + ',' + j + ')"><div style="max-width:100%" >'
                            html += '         <p style="text-transform: capitalize;font-size: 15px;overflow: hidden;white-space: nowrap;display: inline-block;text-overflow: ellipsis;width: 167px;">' + data.profissionais[i].nome_fantasia.toLowerCase() + '</p> </div>'
                            html += '     <div class="barra-indicacao-grade-cheia"> '
                            html += '         <div style="width:' + data.grade_cheia[i][l] + '%">  '
                            html += '         </div> '
                            html += '     </div>'
                            html += '     <div data-id_profissional="' + data.profissionais[i].id + '" style="position: relative;height: 110px;width: 184%;top: 10px;left: -5px;background-color: #f2f2f2;border-left: 2px solid #dedede;border-right: 2px solid #dedede;border-bottom: 2px solid #dedede; display:none"></div>'
                            html += ' </li> '
                            $(".grades-view-recepcao").filter('[data-dia_semana="' + j + '"]').append(html)
                        }
                        else {
                            $(".grades-view-recepcao").filter('[data-dia_semana="' + j + '"]').append('<li></li>')
                        }
                        j++;
                        l++;
                    }
                })
            }
        })
    if (bAbrir) {
        setTimeout(() => {
            $("#recepcaoAgendaModal").modal('show')
        }, 500)
    }
}
function selecionar_profissional($id_profissional) {
    $('#selecao-profissional').find('.selected').css('display', 'none')
    $('#selecao-profissional').find('.selected').attr('class', document.querySelector('.selected').className.replace('selected', ''))
    $('#selecao-profissional').find('[data-id_profissional=' + $id_profissional + ']').attr('class', $('#selecao-profissional').find('[data-id_profissional=' + $id_profissional + ']').attr('class') + ' selected')
    $('#selecao-profissional').find('[data-id_profissional=' + $id_profissional + ']').css('display', '')
    setTimeout(() => {
        mostrar_agendamentos()
        mostrar_agendamentos_semanal();
    }, 500)
    $("#recepcaoAgendaModal").modal('hide')
}
function expandirAgendamentoView($id_profissional, $dia_semana) {
    $.get(
        '/saude-beta/agenda/expandir-agendamento-view', {
        id_profissional: $id_profissional,
        date_selected: $('.mini-calendar h6.selected').data().year + '-' +
            $('.mini-calendar h6.selected').data().month + '-' +
            $('.mini-calendar h6.selected').data().day,
        dia_semana: $dia_semana
    },
        function (data, status) {
            console.log(data + ' | ' + status)
            if (data.error) {
                alert(data.error)
            }
            else {
                data = $.parseJSON(data)
                $("[data-id_profissional='" + $id_profissional + "']").empty()
                data.grades.forEach(grade => {
                    html = '<div id="' + grade.id + '">'
                    html += '   <span>' + grade.horario + '</span>'
                    html += '</div>'
                    $("[data-id_profissional='" + $id_profissional + "']").append(html)
                })
            }
        }
    )
}
function listar_metas($id) {
    $.get('/saude-beta/procedimento/listar-metas/' + $id, function (data) {
        data = $.parseJSON(data)
        $("#metasModal #table-metas").empty();
        maior = 0
        cont = 0
        data.forEach(meta => {
            html = '<tr data-id="' + meta.id + '">'
            html += '   <td class="de" width="35%">' + meta.de2 + ' atividades</td>'
            html += '   <td class="ate" width="35%">' + meta.ate2 + ' atividades</td>'
            html += '   <td class="valor" width="20%">' + meta.valor2 + '</td>'

            if (cont < 1) {
                html += '<td width="10%">'
                html += '       <img src="img/lixeira-de-reciclagem.png" style="max-width: 45%;cursor: pointer;opacity: .8;" onclick="excluir_meta(' + meta.id + ')">'
                html += ' </td>'

            }
            cont = 1
            html += '</tr>'
            $("#table-metas").append(html)
            if (parseInt(meta.ate2) > maior) maior = parseInt(meta.ate2);
        })
        $("#metasModal #de").val(maior + 1)
        $("#metasModal #acima-de").val(maior + 1)
        $("#metasModal #ate").val('')
        $("#metasModal #valor").val('')
    })
}
function add_meta_modalidade() {
    if (parseInt($("#metasModal #ate").val()) < parseInt($("#metasModal #de").val())) {
        alert('valores incorretos!')
        return;
    }
    $.post('/saude-beta/procedimento/adicionar-metas', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_procedimento: $("#metasModal #modalidade").val(),
        de: $("#metasModal #de").val(),
        ate: $("#metasModal #ate").val(),
        valor: $("#metasModal #valor").val()
    }, function (data, status) {
        console.log(data + ' | ' + status)
        if (data.error) {
            alert(data.error)
        }
        else {
            listar_metas($("#metasModal #modalidade").val())
        }
    })
}
var isAdministrador
function adicionar_metas($id) {
    $.get('/saude-beta/procedimento/mostrar/' + $id, function (data) {
        data = $.parseJSON(data);
        $("#metasModal").modal('show')
        console.log(data)
        if (typeof $id != 'undefined') {
            $("#metasModal #id-modalidade").val($id)
            $('#metasModal #modalidade').val($id)
        }
        else {
            $('#metasModal #id-modalidade').val('')
            $('#metasModal #modalidade').val('')
        }

        if (typeof data.valor_total != 'undefined') {
            $('#metasModal #teto').val(data.valor_total)
        }
        else {
            $('#metasModal #teto').val('')
        }

        if (typeof data.tipo_de_comissao != 'undefined') {
            $('#metasModal #tipo_de_comissao').val(data.tipo_de_comissao)
        }
        else {
            $('#metasModal #tipo_de_comissao').val('')
        }
        listar_metas($id)
    })
}
function encontrarPlanosPreAgendamento(callback) {

    $("#criarAgendamentoModal #procedimento_id").empty();
    $("#criarAgendamentoModal #procedimento_id").append('<option>Buscando planos disponíveis...</option>');
    $('#criarAgendamentoModal #convenio_id').attr('disabled', true)
    setTimeout(function () {
        $.get('/saude-beta/agenda/listar-planos-desc2/' + $("#criarAgendamentoModal #paciente_id").val() + '/' + $("#criarAgendamentoModal #convenio_id").val() + '/' + $(".selected").data().id_profissional,
            function (data, status) {
                console.log(data + ' | ' + status)
                data = $.parseJSON(data);
                if (data.error) {
                    console.log(data.error)
                }
                else {
                    $data = data
                    if ($data.convenio == 'S' && $data.associado == 'S') {
                        $("#criarAgendamentoModal #procedimento_id").empty();
                        $data.tabela_precos.forEach(plano => {
                            html = '<option value="' + plano.id + '">'
                            html += plano.descr
                            if ((plano.valor_convenio != null && plano.desconto_associados != null) &&
                                (plano.valor_convenio != '' && plano.desconto_associados != '')) {
                                if (plano.valor_convenio < plano.desconto_associados) {
                                    html += ' ( R$ ' + plano.valor_convenio + ' ) (desc.convenio)'
                                }
                                else html += ' ( R$ ' + plano.desconto_associados + ' ) (desc. associado)'
                            }

                            else if ((plano.valor_convenio != null && plano.desconto_associados == null &&
                                plano.valor_convenio != '' && plano.desconto_associados == '')) {
                                html += ' ( R$ ' + plano.valor_convenio + ' ) (desc. convenio)'
                            }
                            else if ((plano.valor_convenio == null && plano.desconto_associados != null &&
                                plano.valor_convenio == '' && plano.desconto_associados != '')) {
                                html += ' ( R$ ' + plano.desconto_associados + ' ) (desc. associado)'
                            }
                            else html += ' ( R$ ' + plano.valor + ' )'
                            html += '</option>'
                            $("#criarAgendamentoModal #procedimento_id").append(html);
                        })
                        callback()
                        $('#criarAgendamentoModal #convenio_id').removeAttr('disabled')
                    }
                    else if ($data.convenio == 'S' && $data.associado == "N") {
                        $("#criarAgendamentoModal #procedimento_id").empty();
                        $data.tabela_precos.forEach(plano => {

                            html = '<option value="' + plano.id + '">'
                            html += plano.descr
                            if (plano.valor_convenio != null && plano.valor_convenio != '') {
                                html += ' ( R$ ' + plano.valor_convenio + ' ) (desc. convenio)'
                            }
                            else html += ' ( R$ ' + plano.valor + ' )'
                            html += '</option>'
                            $("#criarAgendamentoModal #procedimento_id").append(html);
                        })
                        callback()
                        $('#criarAgendamentoModal #convenio_id').removeAttr('disabled')
                    }
                    else if ($data.convenio == 'N' && $data.associado == "S") {
                        $("#criarAgendamentoModal #procedimento_id").empty();
                        $data.tabela_precos.forEach(plano => {
                            html = '<option value="' + plano.id + '">'
                            html += plano.descr
                            if (plano.desconto_associados != null && plano.valor_convenio != '') {
                                html += ' ( R$ ' + plano.desconto_associados + ' ) (desc. associado)'
                            }
                            else html += ' ( R$ ' + plano.valor + ' )'
                            html += '</option>'
                            $("#criarAgendamentoModal #procedimento_id").append(html);
                        })
                        callback()
                        $('#criarAgendamentoModal #convenio_id').removeAttr('disabled')

                    }

                    else {
                        $("#criarAgendamentoModal #procedimento_id").empty();
                        $data.tabela_precos.forEach(plano => {
                            html = '<option value="' + plano.id + '">'
                            html += plano.descr + ' ( R$ ' + plano.valor + ' ) (sem desconto)'
                            html += '</option>'
                            $("#criarAgendamentoModal #procedimento_id").append(html);
                        })
                        callback()
                        $('#criarAgendamentoModal #convenio_id').removeAttr('disabled')
                    }
                }
            })

    })
}
function inserir_planos_pedido_antigo(bUseProcedimento) {
    var $data
    $.get('/saude-beta/agenda/listar-planos-desc2/' + $("#pedidoAntigoModal #pedido_paciente_id").val() + '/' + $("#pedidoAntigoModal #pedido_id_convenio").val() + '/' + $(".selected").data().id_profissional,
        function (data, status) {
            console.log(data + ' | ' + status)
            data = $.parseJSON(data);
            if (data.error) {
                console.log(data.error)
            }
            else {
                $data = data
            }
        })
    console.log('jdoiajsda')
    $("#pedidoAntigoModal #id_plano").empty();
    $("#pedidoAntigoModal #id_plano").append('<option>Carregando planos...</option>');
    setTimeout(function () {
        $("#pedidoAntigoModal #id_plano").empty();
        $('#pedidoAntigoModal #planos-dataset').empty();
        if ($data.convenio == 'S' && $data.associado == 'S') {
            $data.tabela_precos.forEach(plano => {
                dataset = '<input  type="hidden"'
                dataset += ' data-descr_tabela_preco="' + plano.descr_tabela_preco + '"'
                dataset += ' data-id="' + plano.id + '"'
                dataset += ' data-vigencia="' + plano.vigencia + '"'
                dataset += ' data-n_pessoas="' + plano.n_pessoas + '"'
                dataset += ' data-desconto_associados="' + plano.desconto_associados + '"'
                dataset += ' data-valor="' + plano.valor + '"'
                dataset += ' data-valor_convenio="' + plano.valor_convenio + '"'
                dataset += ' data-associado="' + $data.associado + '"'
                dataset += ' data-convenio="' + $data.convenio + '">'
                $('#pedidoAntigoModal #planos-dataset').append(dataset)


                html = '<option value="' + plano.id + '">'
                html += plano.descr
                if (plano.valor_convenio != null && plano.desconto_associados != null) {
                    if (plano.valor_convenio < plano.desconto_associados) {
                        html += ' ( R$ ' + plano.valor_convenio + ' ) (desc.convenio)'
                    }
                    else html += ' ( R$ ' + plano.desconto_associados + ' ) (desc. associado)'
                }

                else if (plano.valor_convenio != null && plano.desconto_associados == null) {
                    html += ' ( R$ ' + plano.valor_convenio + ' ) (convenio)'
                }
                else if (plano.valor_convenio == null && plano.desconto_associados != null) {
                    html += ' ( R$ ' + plano.desconto_associados + ' ) (desc. associado)'
                }
                else html += ' ( R$ ' + plano.valor + ' )'
                html += '</option>'
                $("#pedidoAntigoModal #id_plano").append(html);
            })
        }

        else if ($data.convenio == 'S' && $data.associado == "N") {
            $data.tabela_precos.forEach(plano => {
                dataset = '<input  type="hidden"'
                dataset += ' data-descr_tabela_preco="' + plano.descr_tabela_preco + '"'
                dataset += ' data-id="' + plano.id + '"'
                dataset += ' data-vigencia="' + plano.vigencia + '"'
                dataset += ' data-n_pessoas="' + plano.n_pessoas + '"'
                dataset += ' data-desconto_associados="' + plano.desconto_associados + '"'
                dataset += ' data-valor="' + plano.valor + '"'
                dataset += ' data-valor_convenio="' + plano.valor_convenio + '"'
                dataset += ' data-associado="' + $data.associado + '"'
                dataset += ' data-convenio="' + $data.convenio + '">'
                $('#pedidoAntigoModal #planos-dataset').append(dataset)

                html = '<option value="' + plano.id + '">'
                html += plano.descr
                if (plano.valor_convenio != null) {
                    html += ' ( R$ ' + plano.valor_convenio + ' ) (desc. convenio)'
                }
                else html += ' ( R$ ' + plano.valor + ' )'
                html += '</option>'
                $("#pedidoAntigoModal #id_plano").append(html);
            })

        }

        else if ($data.convenio == 'N' && $data.associado == "S") {
            $data.tabela_precos.forEach(plano => {
                dataset = '<input  type="hidden"'
                dataset += ' data-descr_tabela_preco="' + plano.descr_tabela_preco + '"'
                dataset += ' data-id="' + plano.id + '"'
                dataset += ' data-vigencia="' + plano.vigencia + '"'
                dataset += ' data-n_pessoas="' + plano.n_pessoas + '"'
                dataset += ' data-desconto_associados="' + plano.desconto_associados + '"'
                dataset += ' data-valor="' + plano.valor + '"'
                dataset += ' data-valor_convenio="' + plano.valor_convenio + '"'
                dataset += ' data-associado="' + $data.associado + '"'
                dataset += ' data-convenio="' + $data.convenio + '">'
                $('#pedidoAntigoModal #planos-dataset').append(dataset)

                html = '<option value="' + plano.id + '">'
                html += plano.descr
                if (plano.desconto_associados != null) {
                    html += ' ( R$ ' + plano.desconto_associados + ' ) (desc. associado)'
                }
                else html += ' ( R$ ' + plano.valor + ' )'
                html += '</option>'
                $("#pedidoAntigoModal #id_plano").append(html);
            })

        }

        else {
            $data.tabela_precos.forEach(plano => {
                dataset = '<input  type="hidden"'
                dataset += ' data-descr_tabela_preco="' + plano.descr_tabela_preco + '"'
                dataset += ' data-id="' + plano.id + '"'
                dataset += ' data-vigencia="' + plano.vigencia + '"'
                dataset += ' data-n_pessoas="' + plano.n_pessoas + '"'
                dataset += ' data-desconto_associados="' + plano.desconto_associados + '"'
                dataset += ' data-valor="' + plano.valor + '"'
                dataset += ' data-valor_convenio="' + plano.valor_convenio + '"'
                dataset += ' data-associado="' + $data.associado + '"'
                dataset += ' data-convenio="' + $data.convenio + '">'
                $('#pedidoAntigoModal #planos-dataset').append(dataset)

                html = '<option value="' + plano.id + '">'
                html += plano.descr + ' ( R$ ' + plano.valor + ' ) (sem desconto)'
                html += '</option>'
                $("#pedidoAntigoModal #id_plano").append(html);
            })
        }
        // if(bUseProcedimento){
        //     $("#pedidoAntigoModal #id_plano").val($('#criarAgendamentoModal #procedimento_id').val())
        //     document.querySelector('[onclick="add_pedido_lista(); return false"]').onclick()
        //     $("#pedidoAntigoModal #id_plano").prop('disabled', true)
        //     $('[onclick="add_pedido_lista(); return false"]').prop('disabled', true)
        // }
    }, 1000)
}







function inserir_planos_pedido(bUseProcedimento) {
    var $data
    if (bUseProcedimento == true) {
        $.get('/saude-beta/agenda/listar-planos-desc2/' + $("#pedidoModal #pedido_paciente_id").val() + '/' + $("#pedido_id_convenio").val() + '/' + $(".selected").data().id_profissional,
            function (data, status) {
                console.log(data + ' | ' + status)
                data = $.parseJSON(data);
                if (data.error) {
                    console.log(data.error)
                }
                else {
                    inserindoPlanos(data, bUseProcedimento)
                }
            })
    }
    else {
        $.get('/saude-beta/pedido/listar-planos-desc/' + $("#pedido_paciente_id").val() + '/' + $("#pedido_id_convenio").val(),
            function (data, status) {
                console.log(data + ' | ' + status)
                data = $.parseJSON(data);
                if (data.error) {
                    console.log(data.error)
                }
                else {
                    inserindoPlanos(data, bUseProcedimento)
                }
            })
    }

}

function inserindoPlanos($data, bUseProcedimento) {
    $("#pedidoModal #id_plano").empty();
    $("#pedidoModal #id_plano").append('<option>Carregando planos...</option>');
    $("#pedidoModal #id_plano").empty();
    $('#pedidoModal #planos-dataset').empty();
    if ($("#pedidoModal #listar_valor_associado").prop("checked") == true) $data.associado = "S"
    if ($data.convenio == 'S' && $data.associado == 'S') {
        $data.tabela_precos.forEach(plano => {
            dataset = '<input  type="hidden"'
            dataset += ' data-descr_tabela_preco="' + plano.descr_tabela_preco + '"'
            dataset += ' data-id="' + plano.id + '"'
            dataset += ' data-vigencia="' + plano.vigencia + '"'
            dataset += ' data-n_pessoas="' + plano.n_pessoas + '"'
            dataset += ' data-desconto_associados="' + plano.desconto_associados + '"'
            dataset += ' data-valor="' + plano.valor + '"'
            dataset += ' data-valor_convenio="' + plano.valor_convenio + '"'
            dataset += ' data-associado="' + $data.associado + '"'
            dataset += ' data-convenio="' + $data.convenio + '">'
            $('#pedidoModal #planos-dataset').append(dataset)


            html = '<option value="' + plano.id + '">'
            html += plano.descr
            if (plano.valor_convenio != null && plano.desconto_associados != null) {
                if (plano.valor_convenio < plano.desconto_associados) {
                    valoresPlano["p" + plano.id] = plano.valor_convenio;
                    valoresPlanoReal["p" + plano.id] = plano.valor_convenio;
                    html += ' (R$ ' + plano.valor_convenio + ') (desc.convenio)'
                }
                else {
                    valoresPlano["p" + plano.id] = plano.desconto_associados;
                    valoresPlanoReal["p" + plano.id] = plano.desconto_associados;
                    html += ' (R$ ' + plano.desconto_associados + ') (desc. associado)'
                }
            }

            else if (plano.valor_convenio != null && plano.desconto_associados == null) {
                valoresPlano["p" + plano.id] = plano.valor_convenio;
                    valoresPlanoReal["p" + plano.id] = plano.valor_convenio;
                html += ' (R$ ' + plano.valor_convenio + ') (desc. convenio)'
            }
            else if (plano.valor_convenio == null && plano.desconto_associados != null) {
                valoresPlano["p" + plano.id] = plano.desconto_associados;
                    valoresPlanoReal["p" + plano.id] = plano.desconto_associados;
                html += ' (R$ ' + plano.desconto_associados + ') (desc. associado)'
            }
            else {
                valoresPlano["p" + plano.id] = plano.valor;
                valoresPlanoReal["p" + plano.id] = plano.valor;
                html += ' (R$ ' + plano.valor + ')'
            } 
            html += '</option>'
            $("#pedidoModal #id_plano").append(html);
        })
    }

    else if ($data.convenio == 'S' && $data.associado == "N") {
        $data.tabela_precos.forEach(plano => {
            dataset = '<input  type="hidden"'
            dataset += ' data-descr_tabela_preco="' + plano.descr_tabela_preco + '"'
            dataset += ' data-id="' + plano.id + '"'
            dataset += ' data-vigencia="' + plano.vigencia + '"'
            dataset += ' data-n_pessoas="' + plano.n_pessoas + '"'
            dataset += ' data-desconto_associados="' + plano.desconto_associados + '"'
            dataset += ' data-valor="' + plano.valor + '"'
            dataset += ' data-valor_convenio="' + plano.valor_convenio + '"'
            dataset += ' data-associado="' + $data.associado + '"'
            dataset += ' data-convenio="' + $data.convenio + '">'
            $('#pedidoModal #planos-dataset').append(dataset)

            html = '<option value="' + plano.id + '">'
            html += plano.descr
            if (plano.valor_convenio != null) {
                html += ' (R$ ' + plano.valor_convenio + ') (desc. convenio)'
                valoresPlano["p" + plano.id] = plano.valor_convenio;
                valoresPlanoReal["p" + plano.id] = plano.valor_convenio;
            }
            else {
                valoresPlano["p" + plano.id] = plano.valor;
                valoresPlanoReal["p" + plano.id] = plano.valor;
                html += ' (R$ ' + plano.valor + ')'
            }
            html += '</option>'
            $("#pedidoModal #id_plano").append(html);
        })

    }

    else if ($data.convenio == 'N' && $data.associado == "S") {
        $data.tabela_precos.forEach(plano => {
            dataset = '<input  type="hidden"'
            dataset += ' data-descr_tabela_preco="' + plano.descr_tabela_preco + '"'
            dataset += ' data-id="' + plano.id + '"'
            dataset += ' data-vigencia="' + plano.vigencia + '"'
            dataset += ' data-n_pessoas="' + plano.n_pessoas + '"'
            dataset += ' data-desconto_associados="' + plano.desconto_associados + '"'
            dataset += ' data-valor="' + plano.valor + '"'
            dataset += ' data-valor_convenio="' + plano.valor_convenio + '"'
            dataset += ' data-associado="' + $data.associado + '"'
            dataset += ' data-convenio="' + $data.convenio + '">'
            $('#pedidoModal #planos-dataset').append(dataset)

            html = '<option value="' + plano.id + '">'
            html += plano.descr
            if (plano.desconto_associados != null) {
                valoresPlano["p" + plano.id] = plano.desconto_associados;
                valoresPlanoReal["p" + plano.id] = plano.desconto_associados;
                html += ' (R$ ' + plano.desconto_associados + ') (desc. associado)'
            }
            else {
                valoresPlano["p" + plano.id] = plano.valor;
                valoresPlanoReal["p" + plano.id] = plano.valor;
                html += ' (R$ ' + plano.valor + ')'
            }
            html += '</option>'
            $("#pedidoModal #id_plano").append(html);
        })

    }

    else {
        $data.tabela_precos.forEach(plano => {
            dataset = '<input  type="hidden"'
            dataset += ' data-descr_tabela_preco="' + plano.descr_tabela_preco + '"'
            dataset += ' data-id="' + plano.id + '"'
            dataset += ' data-vigencia="' + plano.vigencia + '"'
            dataset += ' data-n_pessoas="' + plano.n_pessoas + '"'
            dataset += ' data-desconto_associados="' + plano.desconto_associados + '"'
            dataset += ' data-valor="' + plano.valor + '"'
            dataset += ' data-valor_convenio="' + plano.valor_convenio + '"'
            dataset += ' data-associado="' + $data.associado + '"'
            dataset += ' data-convenio="' + $data.convenio + '">'
            $('#pedidoModal #planos-dataset').append(dataset)

            html = '<option value="' + plano.id + '">'
            valoresPlano["p" + plano.id] = plano.valor;
                valoresPlanoReal["p" + plano.id] = plano.valor;
            html += plano.descr + ' (R$ ' + plano.valor + ') (sem desconto)'
            html += '</option>'
            $("#pedidoModal #id_plano").append(html);
        })
    }
    if (bUseProcedimento) {
        $("#pedidoModal #id_plano").val($('#criarAgendamentoModal #procedimento_id').val())
        document.querySelector('[onclick="add_pedido_lista(); return false"]').onclick()
        // if ($("#button-aceitar").val() == 1) {
        //     $("#pedidoModal #id_plano").prop('disabled', false)
        //     console.log($("#button-aceitar").val())
        //     $('[onclick="add_pedido_lista(); return false"]').prop('disabled', false)
        // }
        // if ($("#button-aceitar").val() == 0) {
        console.log($("#button-aceitar").val())
        $("#pedidoModal #id_plano").prop('disabled', true)
        $('[onclick="add_pedido_lista(); return false"]').prop('disabled', true)
        // }

    }
    atualizaValorPlano();
}

function atualizaValorPlano() {
    try {
        $("#desc_plan").val(getValorPlano() * 100);
        $("#desc_plan").trigger("keyup");    
    } catch(err) {}
}

// function avancar_etapa_wo_pedido() {
//     var etapa_atual = $('.wizard-pedido > .wo-etapa.selected').data().etapa;
//     var cadastroCompleto

//     $('#avancar-pedido').show();
//     $('#salvar-pedido').hide();

//     $.get(
//         '/saude-beta/pessoa/verificar-admin', {},
//         function(data,status){
//             isAdministrador = data

//         }
//     )

//     if (etapa_atual == 1) {
//         if ($('#pedidoModal #pedido_paciente_id').val() == '') {
//             alert('Aviso!\nCampo paciente inválido.');
//             return;
//         }
//         if ($('#pedidoModal #pedido_profissional_exa_id').val() == '') {
//             alert('Aviso!\nCampo profissional examinador inválido.');
//             return;
//         }
//         if ($("#carteira_convenio").parent().css('display') === 'block'){
//             $.get('/saude-beta/convenio/verificar-carteira', {
//                 id_convenio: $("#pedidoModal #pedido_id_convenio").val().trim(),
//                 id_paciente: $("#pedidoModal #pedido_paciente_id").val().trim(),
//                 num_carteira: $("#pedidoModal #carteira_convenio").val().trim()
//             },function(data, status) {
//                 console.log(data + ' | ' + status)
//                 if (data.error){
//                     alert(data.error)
//                 }
//                 else{
//                     if (data === 'false'){
//                         alert('Nº de carteira incorreto!')
//                         document.querySelector("#voltar-pedido").onclick()
//                         return;
//                     }
//                 }
//             })
//         }
//         if (location.href == 'http://vps.targetclient.com.br/saude-beta/agenda' || 
//         location.href == 'http://vps.targetclient.com.br/saude-beta/agenda#'){
//             inserir_planos_pedido(true);
//         }
//         else {
//             inserir_planos_pedido(false);
//         }
//     } else if (etapa_atual == 2 && $('#pedidoModal #tabela-planos tbody tr').length == 0) {
//         alert('Aviso!\nÉ preciso inserir pelo menos um procedimento para prosseguir.');
//         return;
//     } else if (etapa_atual == 2) {
//         ids = []
//         document.querySelectorAll("#pedidoModal #tabela-planos > tbody > tr > [data-plano_id]").forEach(el => {
//             ids.push(el.dataset.plano_id)
//         })
//         // (location.href == 'http://vps.targetclient.com.br/saude-beta/agenda' || 
//         //     location.href == 'http://vps.targetclient.com.br/saude-beta/agenda#') &&

//         if (so_convenio(ids)){
//                 setar_tipo_forma_pag_pedido('P')
//                 setTimeout(() => {
//                     $("#pedidoModal #pedido_forma_pag_valor").val($("#valor_total_planos").html())
//                     $("#pedidoModal #pedido_forma_pag").val(102)
//                     $("#pedidoModal [onclick='add_forma_pag_pedido(); return false']").click()
//                     $("#pedidoModal #avancar-pedido").click()
//                     //$("#pedidoModal #voltar-pedido").prop('disabled', true)
//                 }, 50)
//         }
//         else {
//             ShowConfirmationBox(
//                 'Qual será o tipo da forma de pagamento?',
//                 '',
//                 true, true, false,
//                 function () { setar_tipo_forma_pag_pedido('V'); },
//                 function () { setar_tipo_forma_pag_pedido('P'); },
//                 'À Vista',
//                 'À Prazo'   
//             );
//         }
//         $.get('/saude-beta/pedido/validar/' + $('#pedidoModal #pedido_paciente_id').val(),
//         function(data,status){
//             cadastroCompleto = data;
//         })

//         $('#pedidoModal #pedido_forma_pag_tipo').trigger('change');
//     } else if (etapa_atual == 3) {

//         var vPendente = $('#table-pedido-forma-pag [data-total_pag_pendente]').data().total_pag_pendente -
//                                 $('#table-pedido-forma-pag [data-total_pag_valor]').data().total_pag_valor;
//         console.log(isAdministrador)
//         if (isAdministrador != 'true' && $('#pedidoModal #pedido_forma_pag').val() != 11){
//             if (vPendente > 0){
//                 alert('Valor pendente = '+ vPendente)
//                 return;

//             }

//         }
//         if ($("#table-pedido-forma-pag > tbody > tr").length == 0) {
//             alert('Insira ao menos uma forma de pagamento!')
//             return;
//         }





//         $('#avancar-pedido').removeClass('show');
//         $('#avancar-pedido').attr("disabled", true);

//         if ($.inArray($('#status-pedido').text(), ['Finalizado', 'Em Aprovação', 'Cancelado']) == -1) {
//             $('#avancar-pedido').hide();
//             $('#salvar-pedido').show();
//         }
//         montar_resumo_pedido();
//     }
//     $('#pedidoModal [data-etapa="' + etapa_atual + '"]').removeClass('selected');
//     $('#pedidoModal [data-etapa="' + etapa_atual + '"]').addClass('success');
//     $('#pedidoModal [data-etapa="' + (etapa_atual + 1) + '"]').addClass('selected');
//     $('#voltar-pedido').addClass('show');
//     $('#voltar-pedido').attr("disabled", false);

//     setTimeout(function () {
//         $('[data-etapa="' + (etapa_atual + 1) + '"] input').first().focus();
//     }, 50);
// }
// function so_convenio(array_ids){
//     bool = true;
//     array_ids.forEach(id => {
//         if ($("#planos-dataset").find('[data-id="'+ id + '"]').data().convenio !== 'S') bool = false;
//     })
//     return bool;
// }

var alterouPlano = new Array();
var contPlanoGG = 1;
var permitiuPagamentoDiferente = false;
var montando_resumo = true;
function avancar_etapa_wo_pedido(exibir, focar) {
    var etapa_atual = $(".wizard-pedido > .wo-etapa.selected").data().etapa;
    var cadastroCompleto;
    exibir = exibir > 0 ? etapa_atual == exibir : true;
    $("#avancar-pedido").show();
    $("#salvar-pedido").hide();
    $.get(
        "/saude-beta/pessoa/verificar-admin", {},
        function (data, status) {
            isAdministrador = data;
        }
    );
    switch (etapa_atual) {
        case 1:
            alterouPlano = new Array();
            desc_sup = 0;
            desc_motivo = "";
            if ($("#pedidoModal #pedido_paciente_id").val() == "") {
                if (exibir) alert("Aviso!\nCampo paciente inválido.");
                return false;
            }
            if ($("#pedidoModal #pedido_profissional_exa_id").val() == "") {
                if (exibir) alert("Aviso!\nCampo profissional examinador inválido.");
                return false;
            }
            if ($("#carteira_convenio").parent().css("display") === "block") {
                $.get("/saude-beta/convenio/verificar-carteira", {
                    id_convenio: $("#pedidoModal #pedido_id_convenio").val().trim(),
                    id_paciente: $("#pedidoModal #pedido_paciente_id").val().trim(),
                    num_carteira: $("#pedidoModal #carteira_convenio").val().trim()
                }, function (data, status) {
                    console.log(data + " | " + status)
                    if (data.error) {
                        if (exibir) alert(data.error);
                    } else if (data === "false") {
                        if (exibir) alert("Nº de carteira incorreto!");
                        document.querySelector("#voltar-pedido").onclick();
                        return;
                    }
                });
            }
            inserir_planos_pedido(
                (
                    (
                        location.href.indexOf("agenda") > -1
                    ) && !travarInsercao
                )
            );
            $.get("/saude-beta/encaminhamento/mostrar-tabelaencaminhamento2/" + $("#pedidoModal #pedido_paciente_id").val(),
                function (data) {
                    if (data.length) {
                        document.getElementById("botaoVerEnc").style.display = "";
                        document.getElementById("botaoVerEnc").firstElementChild.addEventListener("click", function () {
                            openModalEncPorPessoa($("#pedidoModal #pedido_paciente_id").val());
                        });
                    }
                }
            );
            break;
        case 2:
            if ($("#pedidoModal #tabela-planos tbody tr").length > 0) {
                contPlanoGG = 1;
                $("#pedidoModal #tabela-planos tbody tr").each(function() {
                    
                    var elemento = ".linha";
                    if ($(".linha" + contPlanoGG + " #valor_plano").html() === undefined) elemento += "0";
                    elemento += contPlanoGG + " #valor_plano";
                    var valor = parseFloat($(elemento).html().replace("R$ ", "").replace(".", "").replace(",", "."));
                    var qtd = parseInt($(elemento.replace(" #valor_plano", " #n_pessoas")).html());

                    var valUn = valor / qtd;
                    var x = "p" + $(elemento.replace(" #valor_plano", " th:first-child")).data().plano_id;

                    valoresPlano[x] = valoresPlano[x].toString();
                    valoresPlanoReal[x] = valoresPlanoReal[x].toString();
                    if (!$.isNumeric(valoresPlano[x])) valoresPlano[x] = parseInt(phoneInt(valoresPlano[x])) / 100;
                    else valoresPlano[x] = parseFloat(valoresPlano[x]);
                    if (!$.isNumeric(valoresPlanoReal[x])) valoresPlanoReal[x] = parseInt(phoneInt(valoresPlanoReal[x])) / 100;
                    else valoresPlanoReal[x] = parseFloat(valoresPlanoReal[x]);
                    // valoresPlanoReal[x] /= qtd;
                    if ((valoresPlanoReal[x] / qtd) != valUn) alterouPlano.push(x.substring(1));
                    console.log("contPlanoGG: " + contPlanoGG);
                    console.log({
                        "alterouPlano" : alterouPlano,
                        "valoresPlano" : valoresPlano,
                        "valoresPlanoReal" : valoresPlanoReal,
                        "elemento" : elemento,
                        "valUn" : valUn,
                        "valor" : valor,
                        "qtd" : qtd,
                        "x" : x,
                        "contPlanoGG" : contPlanoGG
                    });
                    contPlanoGG++;
                });
                // for (x in valoresPlano) {
                //     valoresPlano[x] = valoresPlano[x].toString();
                //     valoresPlanoReal[x] = valoresPlanoReal[x].toString();
                //     if (phoneInt(valoresPlano[x]) != valoresPlano[x]) valoresPlano[x] = parseInt(phoneInt(valoresPlano[x])) / 100;
                //     else valoresPlano[x] = parseFloat(valoresPlano[x]);
                //     if (phoneInt(valoresPlanoReal[x]) != valoresPlanoReal[x]) valoresPlanoReal[x] = parseInt(phoneInt(valoresPlanoReal[x])) / 100;
                //     else valoresPlanoReal[x] = parseFloat(valoresPlanoReal[x]);
                //     if (valoresPlano[x] != (valoresPlanoReal[x])) alterouPlano.push(x.substring(1));
                // }
//                if (descontoAutorizado || !alteracao) {
                    let ids = [],
                        bRetorno = false,
                        planos = document.querySelectorAll("#pedidoModal #tabela-planos > tbody > tr > [data-plano_id]"),
                        bSemValor = false;
                    planos.forEach(el => {
                        ids.push(el.dataset.plano_id);
                    });
                    if (planos[0].innerHTML.toLowerCase().indexOf("retorno") != -1 && planos.length == 1) bRetorno = true;
                    if (!bRetorno && $("#valor_total_planos").html() == 0) bSemValor = true;
                    if (so_convenio(ids) || bRetorno || bSemValor) {
                        setar_tipo_forma_pag_pedido("P");
                        setTimeout(() => {
                            $("#pedidoModal #pedido_forma_pag_valor").val($("#valor_total_planos").html());
                            if (so_convenio(ids)) $("#pedidoModal #pedido_forma_pag").val(102);
                            if (bRetorno) $("#pedidoModal #pedido_forma_pag").val(100);
                            if (bSemValor) $("#pedidoModal #pedido_forma_pag").val(103);
                            $("#pedidoModal [onclick='add_forma_pag_pedido(); return false']").click();
                            $("#pedidoModal #avancar-pedido").click();
                            $("#pedidoModal #voltar-pedido").prop("disabled", true);
                        }, 200);
                    } else ShowConfirmationBox(
                        "Qual será o tipo da forma de pagamento?",
                        "",
                        true, true, false,
                        function () {
                            setar_tipo_forma_pag_pedido("V");
                        },
                        function () {
                            setar_tipo_forma_pag_pedido("P");
                        },
                        "À Vista",
                        "A Prazo"
                    );
                    $.get("/saude-beta/pedido/validar/" + $("#pedidoModal #pedido_paciente_id").val(),
                        function (data, status) {
                            cadastroCompleto = data;
                        }
                    );
                    $("#pedidoModal #pedido_forma_pag_tipo").trigger("change");   
                /*} else {
                    $("#supervisorModal").modal("show");
                    return false;
                }*/
            } else {
                if (exibir) alert("Aviso!\nÉ preciso inserir pelo menos um procedimento para prosseguir.");
                return false;
            }
            break;
        case 3:
            montando_resumo = true;
            var vPendente = $("#table-pedido-forma-pag [data-total_pag_pendente]").data().total_pag_pendente -
                $("#table-pedido-forma-pag [data-total_pag_valor]").data().total_pag_valor;
            if ($("#table-pedido-forma-pag > tbody > tr").length == 0) {
                if (exibir) alert("Insira ao menos uma forma de pagamento!")
                return false;
            }
            console.log(isAdministrador);
            if (isAdministrador != "true" && $("#pedidoModal #pedido_forma_pag").val() != 11 && vPendente > 0) {
                if (!permitiuPagamentoDiferente) {
                    $("#supervisorModal").modal("show");
                    return false;
                }
            }
            $("#avancar-pedido").removeClass("show");
            $("#avancar-pedido").attr("disabled", true);
            if ($.inArray($("#status-pedido").text(), ["Finalizado", "Em Aprovação", "Cancelado"]) == -1) {
                $("#avancar-pedido").hide();
                $("#salvar-pedido").show();
            }
            montando_resumo = false;
            montar_resumo_pedido();
            break;
    }
    $('#pedidoModal [data-etapa="' + etapa_atual + '"]').removeClass("selected");
    $('#pedidoModal [data-etapa="' + etapa_atual + '"]').addClass("success");
    $('#pedidoModal [data-etapa="' + (etapa_atual + 1) + '"]').addClass("selected");
    $("#voltar-pedido").addClass("show");
    $("#voltar-pedido").attr("disabled", false);
    if (focar) {
        setTimeout(function () {
            $('[data-etapa="' + (etapa_atual + 1) + '"] input').first().focus();
        }, 50);
    }
    return true;
}

function so_convenio(array_ids) {
    bool = true;
    array_ids.forEach(id => {
        if ($("#planos-dataset").find('[data-id="' + id + '"]').data().convenio !== 'S') bool = false;
    })
    return bool;
}
function completarCadastro(op) {
    if (op == 'S') {
        editar_pessoa($('#pedidoModal #pedido_paciente_id').val())
    }
    else $('#pedidoModal').modal('hide')
}

function avancar_etapa_wo_pedido_antigo() {
    var etapa_atual = $('#pedidoAntigoModal .wizard-pedido > .wo-etapa.selected').data().etapa;
    var cadastroCompleto

    $('#pedidoAntigoModal #avancar-pedido').show();
    $('#pedidoAntigoModal #salvar-pedido').hide();

    $.get(
        '/saude-beta/pessoa/verificar-admin', {},
        function (data, status) {
            isAdministrador = data

        }
    )

    if (etapa_atual == 1) {
        if ($('#pedidoAntigoModal #pedido_paciente_id').val() == '') {
            alert('Aviso!\nCampo paciente inválido.');
            return;
        }
        if ($('#pedidoAntigoModal #pedido_profissional_exa_id').val() == '') {
            alert('Aviso!\nCampo profissional examinador inválido.');
            return;
        }
        if ($("#pedidoAntigoModal #carteira_convenio").parent().css('display') === 'block') {
            $.get('/saude-beta/convenio/verificar-carteira', {
                id_convenio: $("#pedidoAntigoModal #pedido_id_convenio").val().trim(),
                id_paciente: $("#pedidoAntigoModal #pedido_paciente_id").val().trim(),
                num_carteira: $("#pedidoAntigoModal #carteira_convenio").val().trim()
            }, function (data, status) {
                console.log(data + ' | ' + status)
                if (data.error) {
                    alert(data.error)
                }
                else {
                    if (data === 'false') {
                        alert('Nº de carteira incorreto!')
                        document.querySelector("#pedidoAntigoModal #voltar-pedido").onclick()
                        return;
                    }
                }
            })
        }
        if (location.href == 'http://vps.targetclient.com.br/saude-beta/agenda' ||
            location.href == 'http://vps.targetclient.com.br/saude-beta/agenda#') {
            inserir_planos_pedido_antigo(true);
        }
        else {
            inserir_planos_pedido_antigo(false);
        }
    } else if (etapa_atual == 2 && $('#pedidoAntigoModal #tabela-planos tbody tr').length == 0) {
        alert('Aviso!\nÉ preciso inserir pelo menos um procedimento para prosseguir.');
        return;
    } else if (etapa_atual == 2) {
        id = $("#pedidoAntigoModal #tabela-planos > tbody > tr > th").data().plano_id
        // (location.href == 'http://vps.targetclient.com.br/saude-beta/agenda' || 
        //     location.href == 'http://vps.targetclient.com.br/saude-beta/agenda#') &&

        if ($("#pedidoAntigoModal #planos-dataset").find('[data-id="' + id + '"]').data().convenio === 'S') {
            setar_tipo_forma_pag_pedido_antigo('P')
            setTimeout(() => {
                $("#pedidoAntigoModal #pedido_forma_pag_valor").val($("#pedidoAntigoModal #valor_total_planos").html())
                $("#pedidoAntigoModal [onclick='add_forma_pag_pedido(); return false']").click()
                $("#pedidoAntigoModal #avancar-pedido").click()
                $("#pedidoAntigoModal #voltar-pedido").prop('disabled', true)
            }, 50)
        }
        else {
            ShowConfirmationBox(
                'Qual será o tipo da forma de pagamento?',
                '',
                true, true, false,
                function () { setar_tipo_forma_pag_pedido_antigo('V'); },
                function () { setar_tipo_forma_pag_pedido_antigo('P'); },
                'À Vista',
                'À Prazo'
            );
        }
        $.get('/saude-beta/pedido/validar/' + $('#pedidoAntigoModal #pedido_paciente_id').val(),
            function (data, status) {
                cadastroCompleto = data;
            })

        $('#pedidoAntigoModal #pedido_forma_pag_tipo').trigger('change');
    } else if (etapa_atual == 3) {

        var vPendente = $('#pedidoAntigoModal #table-pedido-forma-pag [data-total_pag_pendente]').data().total_pag_pendente -
            $('#pedidoAntigoModal #table-pedido-forma-pag [data-total_pag_valor]').data().total_pag_valor;
        console.log(isAdministrador)
        if (isAdministrador != 'true') {
            if (vPendente > 0) {
                alert('Valor pendente = ' + vPendente)
                return;

            }

        }
        if ($("#pedidoAntigoModal #table-pedido-forma-pag > tbody > tr").length == 0) {
            alert('Insira ao menos uma forma de pagamento!')
            return;
        }

        $('#pedidoAntigoModal #avancar-pedido').removeClass('show');
        $('#pedidoAntigoModal #avancar-pedido').attr("disabled", true);

        if ($.inArray($('#status-pedido').text(), ['Finalizado', 'Em Aprovação', 'Cancelado']) == -1) {
            $('#pedidoAntigoModal #avancar-pedido').hide();
            $('#pedidoAntigoModal #salvar-pedido').show();
        }
        montar_resumo_pedido_antigo();
    }
    $('#pedidoAntigoModal [data-etapa="' + etapa_atual + '"]').removeClass('selected');
    $('#pedidoAntigoModal [data-etapa="' + etapa_atual + '"]').addClass('success');
    $('#pedidoAntigoModal [data-etapa="' + (etapa_atual + 1) + '"]').addClass('selected');
    $('#pedidoAntigoModal #voltar-pedido').addClass('show');
    $('#pedidoAntigoModal #voltar-pedido').attr("disabled", false);

    setTimeout(function () {
        $('[data-etapa="' + (etapa_atual + 1) + '"] input').first().focus();
    }, 50);
}
function completarCadastro(op) {
    if (op == 'S') {
        editar_pessoa($('#pedidoAntigoModal #pedido_paciente_id').val())
    }
    else $('#pedidoAntigoModal').modal('hide')
}

function salvarPessoa() {
    $.post(
        '/saude-beta/pessoa/salvar', {
        _token: $("meta[name=csrf-token]").attr("content").val(),
        cod_interno: $('cod_interno').val(),
        nome_fantasia: $('nome_fantasia').val(),
        nome_reduzido: $('nome_reduzido').val(),
        email: $('email').val(),
        sexo: $('sexo').val(),
        estado_civil: $('estado-civil').val(),
        data_nasc: $('data_nasc').val(),
        cpf: $('cpf').val(),
        rg: $('rg').val(),
        profissao: $('profissao').val(),
        cep: $('cep').val(),
        cidade: $('cidade').val(),
        numero: $('numero').val(),
        bairro: $('bairro').val(),
        complemento: $('complemento').val(),
        celular1: $('celular1').val(),
        celular2: $('celular2').val(),
        telefone1: $('telefone1').val(),
        telefone2: $('telefone2').val(),
        resp_nome: $('resp-nome').val(),
        resp_grau_parente: $('resp-grau-parente').val(),
        resp_celular: $('resp-celular').val(),
        resp_cpf: $('resp-cpf').val(),
        resp_rg: $('resp-rg').val()

    }, function (data, status) {
        console.log(data + ' | ' + status)
    }
    )
}

function openModalEncPorPessoa(id_paciente) {
    var html = '';
    $.get("/saude-beta/pessoa/get-nome/" + $('#pedidoModal #pedido_paciente_id').val(),
        function (data) {
            var nome = data[0].nome_reduzido.split(" ")[0];
            $.get('/saude-beta/encaminhamento/mostrar-tabelaencaminhamento2/' + $('#pedidoModal #pedido_paciente_id').val(),
                function (data) {
                    $('#encaminhamentosLista_modal .row').empty();
                    data.forEach(resumo => {
                        html = '<li class="text-resume listaEnc" style = "margin:auto;margin-top:20px;padding:20px;border-radius:0.25rem;width:90%" ';
                        html += 'onclick = "callVerEncaminhamentoDetalhe(' + resumo.id + ',true)">';
                        html += ' <a style = "font-size:22px;font-weight:600;line-height:1;color:#dc3545">';
                        html += resumo.valor1;
                        html += ' </a>';
                        html += ' <p> ';
                        html += 'Atualizado ';
                        resumo.data = resumo.updated_at.substring(0, 10);
                        resumo.hora = resumo.updated_at.substring(11, 16);
                        if (moment(resumo.data).format('DD/MM/YYYY') == moment().format('DD/MM/YYYY')) {
                            html += ' hoje às ' + resumo.hora.substring(0, 5);
                        } else {
                            html += ' na ' + captalize(moment(resumo.data.substring(0, 10) + ' ' + resumo.hora, 'YYYY-MM-DD HH:mm:ss').format('LLLL'));
                        }
                        html += '    </p>';
                        html += '</li>';
                        $('#encaminhamentosLista_modal .row').append(html);
                    });
                    document.getElementById("resume-title").innerHTML = "Encaminhamentos de " + nome;
                    $("#encaminhamentosLista_modal").modal("show");
                }
            );
        }
    );
}

function callVerEncaminhamentoDetalhe(id, sugerir) {
    var titulo = "Encaminhamentos ";
    titulo += sugerir ? "sugeridos" : "feitos";
    document.getElementById("tituloModalEncDetalhe").innerHTML = titulo;
    verEncaminhamentoDetalhe(id);
}

function verEncaminhamentoDetalhe(id) {
    $.get('/saude-beta/encaminhamento/mostrar-tabelaencaminhamento/' + id,
        function (data) {
            console.log(data)
            var i = 0;
            $('#tbody-encaminhamento-habilitacoes').empty()
            $('#tbody-encaminhamento').empty()
            data.forEach(tipo => {

                if (tipo.tipo == 'habilitacao') {
                    ;
                    html = '<tr> '
                    html += '    <td data-vo2="' + tipo.valor1 + '" width="40%" class="text-left">' + tipo.valor1 + '</td> '
                    html += '    <td data-obs="' + tipo.valor2 + '" width="30%" class="text-left">' + tipo.valor2 + '</td> '
                    html += '    <td data-infoAdicional="' + tipo.valor3 + '" width="30%" class="text-left">' + tipo.valor3 + '</td> '
                    html += '</tr> '
                    $('#tbody-encaminhamento-habilitacoes').append(html);

                }

                if (tipo.tipo == 'reabilitacao') {

                    html = '<tr> '
                    html += '    <td data-area="' + tipo.valor1 + '" width="40%" class="text-left">' + tipo.valor1 + '</td> '
                    html += '    <td data-qtd_semana="' + tipo.valor2 + '" width="30%" class="text-left">' + tipo.valor2 + '</td> '
                    html += '    <td data-tempo="' + tipo.valor3 + '" width="30%" class="text-left">' + tipo.valor3 + '</td> '
                    html += '</tr> '
                    $('#tbody-encaminhamento').append(html)

                }
            })
            $("#tabelas_encaminhamento_modal").modal('show');
        }
    )
}

// function add_pedido_servicos() {
//     if (true) {

//         if ($('#pedidoModal #avista_prazo').val() == 'P' &&
//             $('#pedidoModal #valor').val().toString().replace(',', '.') == $('#valor').data().valor) {
//             if (!window.confirm("Atenção!\nO valor declarado é referente a forma de pagamento à vista.\nDeseja continuar?")) {
//                 return;
//             }
//         } else if ($('#valor').data().valor_minimo != null &&
//             $('#valor').data().valor_minimo != undefined && parseFloat($('#pedidoModal #valor').val().toString().replace(',', '.')) < $('#valor').data().valor_minimo) {
//             alert('O valor declarado não é permitido por ser abaixo do preço mínimo!')
//             return;
//         }

//         var row_number = ($('#table-pedido-procedimentos > tbody tr').length + 1),
//             dente_regiao_array = $('#pedidoModal #dente_regiao').val(),
//             html = '';

//         dente_regiao_array = dente_regiao_array.split(";");

//         for (let j = 0; j < dente_regiao_array.length; j++) {
//             for (let i = 0; i < $('#pedidoModal #quantidade').val(); i++) {
//                 html = '<tr row_number="' + row_number + '">';
//                 html += '    <td width="10%" class="text-right" data-dente_regiao="' + dente_regiao_array[j].toUpperCase() + '">';
//                 html += dente_regiao_array[j].toUpperCase();
//                 html += '    </td>';
//                 html += '    <td width="10%" class="text-right" data-dente_face="' + $('#pedidoModal #dente_face').val().toUpperCase() + '">';
//                 html += $('#pedidoModal #dente_face').val().toUpperCase();
//                 html += '    </td>';
//                 html += '    <td width="25%" data-procedimento_id="' + $('#pedidoModal #procedimento_id').val() + '" data-procedimento_obs="' + $('#pedidoModal #procedimento_obs').val() + '">';
//                 html += $('#pedidoModal #procedimento_descr').val().trim();
//                 if ($('#pedidoModal #procedimento_obs').val() != '') html += ' (' + $('#pedidoModal #procedimento_obs').val() + ')';
//                 html += '    </td>';
//                 html += '    <td width="25%" data-profissional_exe_id="' + $('#pedidoModal #profissional_exe_id').val() + '">';
//                 html += $('#pedidoModal #profissional_exe_nome').val();
//                 html += '    </td>';
//                 html += '    <td width="12.5%" class="text-right" data-valor="' + $('#pedidoModal #valor').val() + '">';
//                 html += $('#pedidoModal #valor').val();
//                 html += '    </td>';
//                 html += '    <td width="12.5%" class="text-right" data-valor_prazo="' + $('#pedidoModal #valor_prazo').val() + '">';
//                 html += $('#pedidoModal #valor_prazo').val();
//                 html += '    </td>';
//                 html += '    <td width="5%"  class="text-center btn-table-action">';
//                 html += '        <i class="my-icon far fa-trash-alt" onclick="deletar_pedido_grid(' + "'table-pedido-procedimentos'," + row_number + '); deletar_pedido_grid(' + "'table-resumo-pedido-procedimentos'," + row_number + ')"></i>';
//                 html += '    </td>';
//                 html += '</tr>';
//                 $('#table-pedido-procedimentos > tbody').append(html);

//                 html = '<tr row_number="' + row_number + '">';
//                 html += '    <td width="10%" class="text-right" data-dente_regiao="' + dente_regiao_array[j].toUpperCase() + '">';
//                 html += dente_regiao_array[j].toUpperCase();
//                 html += '    </td>';
//                 html += '    <td width="10%" class="text-right" data-dente_face="' + $('#pedidoModal #dente_face').val().toUpperCase() + '">';
//                 html += $('#pedidoModal #dente_face').val().toUpperCase();
//                 html += '    </td>';
//                 html += '    <td width="25%" data-procedimento_id="' + $('#pedidoModal #procedimento_id').val() + '" data-procedimento_obs="' + $('#pedidoModal #procedimento_obs').val() + '">';
//                 html += $('#pedidoModal #procedimento_descr').val().trim();
//                 if ($('#pedidoModal #procedimento_obs').val() != '') html += ' (' + $('#pedidoModal #procedimento_obs').val() + ')';
//                 html += '    </td>';
//                 html += '    <td width="25%" data-profissional_exe_id="' + $('#pedidoModal #profissional_exe_id').val() + '">';
//                 html += $('#pedidoModal #profissional_exe_nome').val();
//                 html += '    </td>';
//                 html += '    <td width="15%" class="text-right" data-valor="' + $('#pedidoModal #valor').val() + '">';
//                 html += $('#pedidoModal #valor').val();
//                 html += '    </td>';
//                 html += '    <td width="15%" class="text-right" data-valor_prazo="' + $('#pedidoModal #valor_prazo').val() + '">';
//                 html += $('#pedidoModal #valor_prazo').val();
//                 html += '    </td>';
//                 html += '</tr>';
//                 $('#table-resumo-pedido-procedimentos > tbody').append(html);
//                 row_number++;
//             }
//         }
//         // $('#pedidoModal #profissional_exe_id').val('');
//         // $('#pedidoModal #profissional_exe_nome').val('');

//         $('#pedidoModal #procedimento_descr').val('').focus();
//         $('#pedidoModal #procedimento_id').val('');
//         $('#pedidoModal #dente_regiao').val('');
//         $('#pedidoModal #dente_face').val('');
//         $('#pedidoModal #quantidade').val('');
//         $('#pedidoModal #valor').val('');
//         $('#pedidoModal #valor').data('valor_minimo', '');
//         $('#pedidoModal #valor').removeAttr('data-valor_minimo');
//         $('#pedidoModal #valor_prazo').val('');
//         $('#pedidoModal #valor_prazo').data('valor_minimo', '');
//         $('#pedidoModal #valor_prazo').removeAttr('data-valor_minimo');
//         $('#pedidoModal #procedimento_obs').val('');
//         att_totais_pedido();
//     } else {
//         alert('Favor preencher todos os campos.');
//     }
// }

function att_totais_pedido() {
    var html,
        orca_valor = 0.0,
        orca_valor_prazo = 0.0;

    $('#table-pedido-procedimentos > tbody > tr').each(function () {
        orca_valor += parseFloat($(this).find('[data-valor]').data().valor.toString().replace(',', '.'));
        orca_valor_prazo += parseFloat($(this).find('[data-valor_prazo]').data().valor_prazo.toString().replace(',', '.'));
    });

    html = '<tr>';
    html += '    <th width="75%" class="text-center" colspan="4"></th>';
    html += '    <th width="10%" class="text-right" data-total_vista="' + orca_valor + '">';
    html += parseFloat(orca_valor).toFixed(2).toString().replace('.', ',');
    html += '    </th>';
    html += '    <th width="10%" class="text-right" data-total_prazo="' + orca_valor_prazo + '">';
    html += parseFloat(orca_valor_prazo).toFixed(2).toString().replace('.', ',');
    html += '    </th>';
    html += '    <th width="5%"  class="text-center"></th>';
    html += '</tr>';
    $('[data-table="#table-pedido-procedimentos"] tfoot').html(html);

    html = '<tr>';
    html += '    <th width="85%" class="text-center" colspan="4"></th>';
    html += '    <th width="15%" class="text-right" data-total_vista="' + orca_valor + '">';
    html += parseFloat(orca_valor).toFixed(2).toString().replace('.', ',');
    html += '    </th>';
    html += '    <th width="15%" class="text-right" data-total_prazo="' + orca_valor_prazo + '">';
    html += parseFloat(orca_valor_prazo).toFixed(2).toString().replace('.', ',');
    html += '    </th>';
    html += '</tr>';
    $('#table-resumo-pedido-procedimentos tfoot').html(html);

    $('#table-pedido-forma-pag      > tbody > tr > [data-forma_pag_tipo="V"],' +
        '#table-pedido-forma-pag-resumo > tbody > tr > [data-forma_pag_tipo="V"]')
        .parent()
        .find('[data-forma_pag_valor]')
        .data('forma_pag_valor', parseFloat(orca_valor).toFixed(2).toString().replace('.', ','))
        .attr('data-forma_pag_tipo', parseFloat(orca_valor).toFixed(2).toString().replace('.', ','))
        .html('R$ ' + parseFloat(orca_valor).toFixed(2).toString().replace('.', ','));

    $('#table-pedido-forma-pag      > tbody > tr > [data-forma_pag_tipo="P"],' +
        '#table-pedido-forma-pag-resumo > tbody > tr > [data-forma_pag_tipo="P"]')
        .parent()
        .find('[data-forma_pag_valor]')
        .data('forma_pag_valor', parseFloat(orca_valor_prazo).toFixed(2).toString().replace('.', ','))
        .attr('data-forma_pag_tipo', parseFloat(orca_valor_prazo).toFixed(2).toString().replace('.', ','))
        .html('R$ ' + parseFloat(orca_valor_prazo).toFixed(2).toString().replace('.', ','));
}
function graficos_por_pessoa() {
    console.log('.')
}

function add_forma_pag_pedido_antigo() {
    var row_number = ($('#pedidoAntigoModal #table-pedido-forma-pag > tbody tr').length + 1),
        html = '';

    html = '<tr row_number="' + row_number + '">';
    html += '    <td width="25%" data-forma_pag="' + $('#pedidoAntigoModal #pedido_forma_pag').val() + '">';
    html += $('#pedidoAntigoModal #pedido_forma_pag option:selected').text();
    html += '    </td>';
    html += '    <td width="25%" data-financeira_id="' + $('#pedidoAntigoModal #financeira').val() + '">';
    if ($('#pedidoAntigoModal #financeira').val() != 0) html += $('#pedidoAntigoModal #financeira option:selected').text();
    html += '    </td>';
    html += '    <td width="15%" data-forma_pag_parcela="' + $('#pedidoAntigoModal #pedido_forma_pag_parcela').val() + '"  class="text-right">';
    html += $('#pedidoAntigoModal #pedido_forma_pag_parcela').val() + 'x de R$ ' + (parseFloat($('#pedidoAntigoModal #pedido_forma_pag_valor').val().replace(',', '.')) / parseInt($('#pedidoAntigoModal #pedido_forma_pag_parcela').val())).toFixed(2).toString().replace('.', ',');
    html += '    </td>';
    html += '    <td width="15%" data-forma_pag_valor="' + $('#pedidoAntigoModal #pedido_forma_pag_valor').val() + '"  class="text-right">';
    html += '       R$ ' + $('#pedidoAntigoModal #pedido_forma_pag_valor').val();
    html += '    </td>';
    html += '    <td width="15%" data-pedido_data_vencimento="' + $('#pedidoAntigoModal #pedido_data_vencimento').val() + '">';
    html += $('#pedidoAntigoModal #pedido_data_vencimento').val();
    html += '    </td>';
    html += '    <td width="5%">';
    html += '        <i class="my-icon far fa-trash-alt" onclick="deletar_pedido_grid(' + "'table-pedido-forma-pag'," + row_number + '); deletar_pedido_grid(' + "'table-pedido-forma-pag-resumo'," + row_number + '); att_pedido_total_proc_pagamento()"></i>'
    html += '    </td>';
    html += '</tr>';
    $('#pedidoAntigoModal #table-pedido-forma-pag > tbody').append(html);

    html = '<tr row_number="' + row_number + '">';
    html += '    <td width="27.5%" data-forma_pag="' + $('#pedidoAntigoModal #pedido_forma_pag').val() + '">';
    html += $('#pedidoAntigoModal #pedido_forma_pag option:selected').text();
    html += '    </td>';
    html += '    <td width="27.5%" data-financeira_id="' + $('#pedidoAntigoModal #financeira').val() + '">';
    if ($('#pedidoAntigoModal #financeira').val() != 0) html += $('#pedidoAntigoModal #financeira option:selected').text();
    html += '    </td>';
    html += '    <td width="15%" data-forma_pag_parcela="' + $('#pedidoAntigoModal #pedido_forma_pag_parcela').val() + '"  class="text-right">';
    html += $('#pedidoAntigoModal #pedido_forma_pag_parcela').val() + 'x de R$ ' + (parseFloat($('#pedidoAntigoModal #pedido_forma_pag_valor').val().replace(',', '.')) / parseInt($('#pedidoAntigoModal #pedido_forma_pag_parcela').val())).toFixed(2).toString().replace('.', ',');
    html += '    </td>';
    html += '    <td width="15%" data-forma_pag_valor="' + $('#pedidoAntigoModal #pedido_forma_pag_valor').val() + '"  class="text-right">';
    html += '       R$ ' + $('#pedidoAntigoModal #pedido_forma_pag_valor').val();
    html += '    </td>';
    html += '    <td width="15%" data-pedido_data_vencimento="' + $('#pedidoAntigoModal #pedido_data_vencimento').val() + '">';
    html += $('#pedidoAntigoModal #pedido_data_vencimento').val();
    html += '    </td>';
    html += '</tr>';
    $('#pedidoAntigoModal #table-pedido-forma-pag-resumo > tbody').append(html);

    $('#pedidoAntigoModal #pedido_forma_pag_parcela').val(0);
    $('#pedidoAntigoModal #pedido_forma_pag_valor').val(0);
    att_pedido_total_proc_pagamento_antigo();
}
function montar_resumo_pedido() {
    var paciente_id = $('#pedido_paciente_id').val();
    var paciente_nome = $('#pedido_paciente_nome').val();
    var id_convenio = $('#pedido_id_convenio').val();
    var descr_convenio = $('#pedido_id_convenio option:selected').text();
    var profissional_exa_id = $('#pedido_profissional_exa_id').val();
    var profissional_exa_nome = $('#pedido_profissional_exa_nome').val();
    var obs = $('#pedido_obs').val();

    document.querySelectorAll('.text-center > img').forEach(el => {
        el.style.display = 'none';
    })
    $('[data-resumo_paciente]').data('resumo_paciente', paciente_id).attr('data-resumo_paciente', paciente_id);
    $('[data-resumo_paciente]').html(paciente_nome);
    $('[data-resumo_paciente_convenio]').data('resumo_paciente_convenio', id_convenio).attr('data-resumo_paciente_convenio', id_convenio);
    $('[data-resumo_paciente_convenio]').html(descr_convenio);
    $('[data-resumo_profissional_exa]').data('resumo_profissional_exa', profissional_exa_id).attr('data-resumo_profissional_exa', profissional_exa_id);
    $('[data-resumo_profissional_exa]').html(profissional_exa_nome);
    $('[data-resumo_obs]').data('resumo_obs', obs).attr('data-resumo_obs', obs);
    if (obs != '') $('[data-resumo_obs]').html(obs);
    else $('[data-resumo_obs]').html('Sem Observação');

    planos = [];
    $('#tabela-planos > tbody tr').each(function () {
        planos.push({
            id: $(this).find('[data-plano_id]').data().plano_id,
            qtd: $(this).find('#n_pessoas').html()
        });
    })

    document.querySelector("#avancar-pedido").style.display = 'none'
    document.querySelector("#salvar-pedido").style.display = 'block'
    $.get(
        '/saude-beta/pedido/montar-resumo',
        {
            _token: $("meta[name=csrf-token]").attr("content"),
            planos: planos
        },
        function (data, status) {
            console.log(data + '|' + status)
            $data = data
            validade = $data[8] + $data[9] + '/' + $data[5] + $data[6] + '/' + $data[0] + $data[1] + $data[2] + $data[3]
            $('#data-resumo_validade').html(validade);

        }
    )
}
function montar_resumo_pedido_antigo() {
    var paciente_id = $('#pedidoAntigoModal #pedido_paciente_id').val();
    var paciente_nome = $('#pedidoAntigoModal #pedido_paciente_nome').val();
    var id_convenio = $('#pedidoAntigoModal #pedido_id_convenio').val();
    var descr_convenio = $('#pedidoAntigoModal #pedido_id_convenio option:selected').text();
    var profissional_exa_id = $('#pedidoAntigoModal #pedido_profissional_exa_id').val();
    var profissional_exa_nome = $('#pedidoAntigoModal #pedido_profissional_exa_nome').val();
    var obs = $('#pedidoAntigoModal #pedido_obs').val();

    document.querySelectorAll('#pedidoAntigoModal .text-center > img').forEach(el => {
        el.style.display = 'none';
    })
    $('#pedidoAntigoModal [data-resumo_paciente]')
        .data('resumo_paciente', paciente_id)
        .attr('data-resumo_paciente', paciente_id);
    $('#pedidoAntigoModal [data-resumo_paciente]')
        .html(paciente_nome);
    $('#pedidoAntigoModal [data-resumo_paciente_convenio]')
        .data('resumo_paciente_convenio', id_convenio)
        .attr('data-resumo_paciente_convenio', id_convenio);
    $('#pedidoAntigoModal [data-resumo_paciente_convenio]')
        .html(descr_convenio);
    $('#pedidoAntigoModal [data-resumo_profissional_exa]')
        .data('resumo_profissional_exa', profissional_exa_id)
        .attr('data-resumo_profissional_exa', profissional_exa_id);
    $('#pedidoAntigoModal [data-resumo_profissional_exa]')
        .html(profissional_exa_nome);
    $('#pedidoAntigoModal [data-resumo_obs]').data('resumo_obs', obs)
        .attr('data-resumo_obs', obs);
    if (obs != '') $('#pedidoAntigoModal [data-resumo_obs]').html(obs);
    else $('#pedidoAntigoModal [data-resumo_obs]').html('Sem Observação');

    planos = [];
    $('#pedidoAntigoModal #tabela-planos > tbody tr').each(function () {
        planos.push({
            id: $(this).find('[data-plano_id]').data().plano_id
        });
    })

    document.querySelector("#pedidoAntigoModal #avancar-pedido").style.display = 'none'
    document.querySelector("#pedidoAntigoModal #salvar-pedido").style.display = 'block'
    $.post(
        '/saude-beta/pedido/montar-resumo',
        {
            _token: $("meta[name=csrf-token]").attr("content"),
            planos: planos
        },
        function (data, status) {
            console.log(data + '|' + status)
            $data = data
            validade = $data[8] + $data[9] + '/' + $data[5] + $data[6] + '/' + $data[0] + $data[1] + $data[2] + $data[3]
            $('#pedidoAntigoModal #data-resumo_validade').html(validade);

        }
    )
}
function editar_pedido_grid(row_number) {
    var _row = $('#table-orcamento-procedimentos tr[row_number=' + row_number + ']');
    $('#inputs-procedimentos #procedimento_descr').val(_row.find('[data-procedimento_id]').text());
    $('#inputs-procedimentos #procedimento_id').val(_row.find('[data-procedimento_id]').data().procedimento_id);
    $('#inputs-procedimentos #profissional_exe_nome').val(_row.find('[data-profissional_exe_id]').text());
    $('#inputs-procedimentos #profissional_exe_id').val(_row.find('[data-profissional_exe_id]').data().profissional_exe_id);
    $('#inputs-procedimentos #dente_regiao').val(_row.find('[data-dente_regiao]').data().dente_regiao);
    $('#inputs-procedimentos #dente_face').val(_row.find('[data-dente_face]').data().dente_face);
    $('#inputs-procedimentos #quantidade').val(1);
    $('#inputs-procedimentos #quantidade').prop('readonly', true);
    $('#inputs-procedimentos #valor').val(_row.find('[data-valor]').data().valor);
    $('#inputs-procedimentos #valor_prazo').val(_row.find('[data-valor_prazo]').data().valor_prazo);
    $('#inputs-procedimentos #procedimento_obs').val(_row.find('[data-procedimento_obs]').data().procedimento_obs);
    $('#inputs-procedimentos #procedimento_index_edit').val(row_number);
    $('.limpar-edicao').show();
}

function deletar_pedido_grid(_table, row_number) {
    $('[id="' + _table + '"]').each(function () {
        $(this).find('[row_number="' + row_number + '"]').remove();
    })
    att_totais_pedido();
}
function control_cart_convenio() {
    if ($("#pedido_id_convenio").val() != 0 && $("#pedido_id_convenio").val() != null) {
        $("#carteira_convenio").parent().css('display', 'block')
        $("#carteira_convenio").css('display', 'block')
        $("#carteira_convenio").parent().find('label').css('display', 'block')
    } else {
        $("#carteira_convenio").parent().css('display', 'none')
        $("#carteira_convenio").css('display', 'none')
        $("#carteira_convenio").parent().find('label').css('display', 'none')
    }
}


function salvar_pedido_antigo() {
    var id = $('#pedidoAntigoModal #pedido_id').val(),
        tipo_forma_pag = $('#pedidoAntigoModal #pedido_forma_pag_tipo').val(),
        id_paciente = $('#pedidoAntigoModal [data-resumo_paciente]').data().resumo_paciente,
        id_convenio = $('#pedidoAntigoModal #pedidoAntigoModal #pedido_id_convenio').val(),
        data = $('#pedidoAntigoModal #data-resumo_validade').html(),
        id_profissional_exa = $('#pedidoAntigoModal [data-resumo_profissional_exa]').data().resumo_profissional_exa,
        obs = $('#pedidoAntigoModal [data-resumo_obs]').data().resumo_obs,
        planos = [],
        formas_pag = [];

    data_validade = data[6] + data[7] + data[8] + data[9] + '-' + data[3] + data[4] + '-' + data[0] + data[1];
    if (confirm('Atenção!\nDeseja gerar contrato já finalizado?')) {
        _status = 'F';
    } else {
        _status = 'A';
    }

    $('#pedidoAntigoModal #table-pedido-forma-pag-resumo tbody tr').each(function () {
        formas_pag.push({
            id_forma_pag: $(this).find('[data-forma_pag]').data().forma_pag,
            id_financeira: $(this).find('[data-financeira_id]').data().financeira_id,
            parcela: $(this).find('[data-forma_pag_parcela]').data().forma_pag_parcela,
            forma_pag_valor: String($(this).find('[data-forma_pag_valor]').data().forma_pag_valor).replace(',', '.'),
            data_vencimento: $(this).find('[data-pedido_data_vencimento]').data().pedido_data_vencimento
        });
    });
    $('#pedidoAntigoModal #tabela-planos > tbody tr').each(function () {
        planos.push({
            id_plano: $(this).find('[data-plano_id]').data().plano_id,
            list_id: $(this).find('[data-plano_id_pessoas]').val(),
            qtd: $(this).find('#n_pessoas').html(),
            valor: $(this).find('#valor_plano').html()
        });
    })
    console.log(planos)
    $.post(
        '/saude-beta/agenda-antiga/salvar', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: id,
        tipo_forma_pag: tipo_forma_pag,
        id_paciente: id_paciente,
        id_convenio: id_convenio,
        id_profissional_exa: id_profissional_exa,
        data_validade: data_validade,
        status: _status,
        obs: obs,
        formas_pag: formas_pag,
        planos: planos,
        id_agendamento: $("#pedidoAntigoModal #agenda_id").val()
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                data = $.parseJSON(data);
                console.log(data + ' | ' + status)
                // if (_status == 'F') new_system_window('pedido/imprimir/' + data.id);
                // if (window.location.pathname.includes('/pessoa/prontuario')) {
                //     pedidos_por_pessoa(id_paciente);
                // } else {
                // if (location.href === 'http://vps.targetclient.com.br/saude-beta/agenda' ||
                //     location.href === 'http://vps.targetclient.com.br/saude-beta/agenda#') {
                //     alert((location.href === 'http://vps.targetclient.com.br/saude-beta/agenda' ||
                //     location.href === 'http://vps.targetclient.com.br/saude-beta/agenda#'))
                //     $("#pedidoAntigoModal").modal('hide');
                //     $("#criarAgendamentoAntigoModal").modal('hide');
                //     mostrar_agendamentos_semanal();
                //     mostrar_agendamentos();
                // }
                // else {
                $("#pedidoAntigoModal").modal("hide")
                // }    
                // }
            }
        }
    );

}
// ------------------------------------------------------------------------------------- //
// ------------------------------------------------------------------------------------- //
// -------------------------------- PLANO DE TRATAMENTO -------------------------------- //
// ------------------------------------------------------------------------------------- //
// ------------------------------------------------------------------------------------- //


// ————————————————————————————————————————————————————————————————————————————————————— //
// ————————————————————————————————————————————————————————————————————————————————————— //
// ————————————————————————————————————— PRONTUÁRIO ———————————————————————————————————— //
// ————————————————————————————————————————————————————————————————————————————————————— //
// ————————————————————————————————————————————————————————————————————————————————————— //

function anexos_por_pessoa(id_pessoa) {
    console.log('/saude-beta/anexos/listar-pessoa/' + id_pessoa + "/" + document.getElementById("select-pasta").value)
    $.get('/saude-beta/anexos/listar-pessoa/' + id_pessoa + "/" + document.getElementById("select-pasta").value,
        function (data) {
            data = $.parseJSON(data);
            url = 'https://vps.targetclient.com.br/saude-beta/anexos/'
            $('#table-prontuario-anexo').empty();
            data.forEach(anexo => {
                dataType = "application/pdf"
                html = '<div id="anexo_' + anexo.id + '" class="row div-pre-anexo">';
                html += '    <div class="col-3 text-center divObject"> ';
                html += '        <div href="/saude-beta/anexos/baixar/' + anexo.id + '" title="Baixar Anexo" style="min-height: 100%"> ';
                html += '           <object data="' + url + anexo.titulo + '" type="' + dataType + '" class="obj-pre-anexos" width="100%" height:"100%"></object>'
                // html += '            <i class="my-icon fas fa-file-download" style="font-size:4rem"></i>';
                html += '        </div> ';
                html += '    </div>';
                html += '    <div class="col">';
                html += '        <div class="row">';
                html += '            <div class="col">';
                html += '                <h6 class="title-anexo m-0">' + anexo.titulo + '</h6>';
                html += '            </div>';
                html += '            <div class="col text-right hide-mobile"> ';
                html += '                <span class="data-anexo">';
                html += moment(anexo.created_at).format('DD/MM/YYYY — HH:mm:ss');
                html += '                </span> ';
                html += '            </div>';
                html += '        </div>';
                html += '        <div class="row mt-2">';
                html += '            <div class="col anexo-obs" style="word-break:break-all"> ';
                html += anexo.obs;
                html += '            </div>';
                html += '            <a href="/saude-beta/anexos/baixar/' + anexo.id + '" style="margin-right: 40px;">'
                html += '                <i class="material-icons mt-auto" style="color: #686868;margin-bottom:5px; cursor: pointer">cloud_download</i> ';
                html += '            </a>'
                html += '                <i onclick="visualizar_anexo(' + "'" + url + anexo.titulo + "'" + ')" class="material-icons mt-auto" style="color: #686868;margin-right: 40px;margin-bottom:5px; cursor: pointer">remove_red_eye</i> ';
                html += '            <div class="btn-deletar-anexo" title="Deletar" onclick="deletar_anexo(' + anexo.id + ')"> ';
                html += '                <i class="material-icons mt-auto">delete</i> ';
                html += '            </div>';
                html += '        </div>';
                html += '    </div>';
                html += '</div>';
                $('#table-prontuario-anexo').append(html);
            });
            $('[data-id="#prt-anexos"] .qtde-prontuario')
                .data('count', data.length)
                .attr('data-count', data.length)
                .find('small')
                .html(data.length);
        }
    );
}
function visualizar_anexo(_url) {
    $('#visualizarAnexoModal .container').empty()
    if (_url.indexOf(".pdf") > -1) html = '   <object data="' + _url + '" type="application/pdf" class="obj-pre-anexos" width="70%" height="70%"></object>'
    else html = '<img src="' + _url + '">'
    $('#visualizarAnexoModal .container').append(html)
    $('#visualizarAnexoModal').modal('show')
    if (_url.indexOf(".pdf") == -1) $($('#visualizarAnexoModal .container').find("img")[0]).css("max-width", "100%");
}
var b
function agendamentos_por_pessoa(id_pessoa) {
    var dia_temp;
    $('#table-prontuario-agendamento').empty();
    $('#table-prontuario-agendamento').append('<div class="d-flex" style="justify-content: center;align-items: center;height: 320px;"><img src="/saude-beta/img/carregando-azul.gif" style="width: 190px;height: 190px;opacity: .7"></div>');
    $.get('/saude-beta/agenda/agendamentos-pessoa/' + id_pessoa,
        function (data) {
            data = $.parseJSON(data);
            console.log(data);
            setTimeout(() => {
                $('#table-prontuario-agendamento').empty();
                if (data.length > 0) {
                    data.forEach(agendamento => {
                        console.log('Agendamento Semanal:');
                        console.log(agendamento);
                        if (agendamento.data != dia_temp) {
                            if (dia_temp != undefined) $('#table-prontuario-agendamento').append('<hr>');
                            html = '<h4 style="color:#212529">' + moment(agendamento.data).format('LL'); + '</h4>';
                            $('#table-prontuario-agendamento').append(html);
                            dia_temp = agendamento.data;
                        }

                        if (agendamento.id != undefined && agendamento.id != null) {
                            html = '<li data-id_agendamento="' + agendamento.id + '"';

                            html += ' data-status="' + agendamento.id_status + '"';
                            html += ' data-status_s="' + agendamento.status + '"';
                            html += ' data-paciente="' + agendamento.nome_paciente + '"';
                            html += ' data-procedimento="' + agendamento.descr_procedimento + '"';
                            html += ' data-convenio="' + agendamento.convenio_nome + '"';
                            html += ' data-permite_fila_espera="' + agendamento.permite_fila_espera + '"';
                            html += ' data-permite_reagendar="' + agendamento.permite_reagendar + '"';
                            html += ' data-permite_editar="' + agendamento.permite_editar + '"';
                            html += ' data-libera_horario="' + agendamento.libera_horario + '"';
                            html += ' style="background:' + agendamento.cor_status + '; color: ' + agendamento.cor_letra + ';max-height: 80px;" >';

                            html += '    <div class="my-1 mx-1 d-flex">';
                            html += '       <img class="foto-paciente-agenda" data-id_paciente="' + agendamento.id_profissional + '" src="/saude-beta/img/pessoa/' + agendamento.id_profissional + '.jpg" onerror="this.onerror=null;this.src=' + "'/saude-beta/img/paciente_default.png'" + '" onclick="verificar_cad_redirecionar(' + agendamento.id_paciente + ')">';
                            html += '       <div>';
                            html += '           <p class="col p-0">';
                            html += '               <span class="ml-0 my-auto" style="font-weight:600" onclick="verificar_cad_redirecionar(' + agendamento.id_paciente + ')">';
                            html += agendamento.nome_profissional;
                            if (agendamento.hora != null) html += ' (' + agendamento.hora.substr(0, 5) + ' hrs)';
                            html += '               </span>';
                            html += '           </p>';
                            html += '           <p class="tag-agenda" style="font-weight:400">';
                            if (agendamento.retorno) html += 'Retorno: ';
                            if (agendamento.descr_procedimento != null) html += agendamento.descr_procedimento + ' | ';
                            if (agendamento.tipo_procedimento != null) html += agendamento.tipo_procedimento + ' | ';
                            if (agendamento.convenio != null && agendamento.convenio != 0) html += agendamento.convenio_nome;
                            else html += 'Particular'
                            html += '           </p>';
                            html += '       </div>'
                            // if (agendamento.espera != undefined && agendamento.permite_fila_espera) {
                            //     html += '   <span class="tag-em-espera encaixe my-auto mx-1" title="Em Espera">';
                            //     html += '      <small>Aguardando a ' + tempo_aguardando(agendamento.hora_chegada, false) + '</small>';
                            //     html += '   </span>';
                            // }

                            html += '   <span class="tag-em-espera encaixe my-auto mx-1">';
                            html += '      <small>' + agendamento.descr_status + '</small>';
                            html += '   </span>';


                            // if (agendamento.primeira_vez) {
                            //     html += '   <span class="tag-primeira-vez my-auto mx-1" title="1ª Vez" onclick="editar_pessoa(' + agendamento.id_paciente + ')">';
                            //     html += '      <small>1ª Vez</small>';
                            //     html += '   </span>';
                            // }
                            // if (agendamento.permite_editar && agendamento.id_confirmacao != null && agendamento.id_confirmacao != 0) {
                            //     html += '   <span class="tag-confirmado my-auto mx-1" title="' + agendamento.descr_confirmacao + '">';
                            //     html += '      <small>' + agendamento.descr_confirmacao + '</small>';
                            //     html += '   </span>';
                            // }
                            // if (agendamento.encaixe) {
                            //     html += '   <span class="tag-encaixe my-auto mx-1" title="Encaixe">';
                            //     html += '      <small>Encaixe</small>';
                            //     html += '   </span>';
                            // }


                            html += '</div>';
                            html += '   <div class="buttonclose"'

                            // if (agendamento.sistema_antigo == 0)
                            html += 'onclick="deletar_agendamento_prontuario(' + agendamento.id + ')">';
                            // else
                            //  html += 'onclick="antigo_deletar_agendamento_prontuario(' + agendamento.id + ')">';
                            html += '   </div>';
                            html += '</li>';
                            // console.log(agendamento.sistema_antigo)
                            $('#table-prontuario-agendamento').append(html);
                        }
                    });
                }

            })

            $('[data-id="#prt-agendamento"] .qtde-prontuario')
                .data('count', data.length)
                .attr('data-count', data.length)
                .find('small')
                .html(data.length);
        }
    );
}

function receitas_por_pessoa(id_pessoa) {
    var receita_temp, i = 0;
    $.get('/saude-beta/receita/listar-pessoa/' + id_pessoa,
        function (data) {
            data = $.parseJSON(data);
            $('#table-prontuario-receita').empty();
            if (data.length > 0) {
                data.forEach(receita => {
                    if (receita_temp != receita.id) {
                        html = '<div class="card z-depth-0 bordered" style="border: 1px solid rgba(0, 0, 0, .125)">';
                        html += '    <div class="accordion-header w-100">';
                        html += '        <div class="row">';
                        html += '            <div class="col"> ';
                        html += '                <button class="btn btn-link" type="button" data-toggle="collapse"';
                        html += '                    data-target="#receita-' + receita.id + '" aria-expanded="true" aria-controls="collapse">';
                        html += receita.descr + ' por ' + receita.nome_profissional;
                        html += '                </button> ';
                        html += '            </div>';
                        html += '            <div class="col-4 d-grid text-right">';
                        html += '                <div class="my-auto ml-auto mr-4 d-flex">';
                        html += '                    <div class="btn-link mr-2">' + moment(receita.created_at).format('DD/MM/YYYY - HH:mm') + '</div>';
                        html += '                    <i class="my-icon my-auto mr-2 far fa-print"     title="Imprimir" onclick="new_system_window(' + "'receita/imprimir/" + receita.id + "'" + ')"></i>';
                        html += '                    <i class="my-icon my-auto mr-2 far fa-trash-alt" title="Deletar" onclick="deletar_receita(' + receita.id + ')"></i>';
                        html += '                </div>';
                        html += '            </div>';
                        html += '        </div>';
                        html += '    </div>';
                        html += '    <div class="collapse" id="receita-' + receita.id + '">';
                        html += '        <div class="card-body">';
                        html += '        </div>';
                        html += '    </div>';
                        html += '</div>';
                        $('#table-prontuario-receita').append(html);
                        receita_temp = receita.id;
                        i++;
                    }
                    html = '<div class="row form-group"> ';
                    html += '    <div class="col-6">';
                    html += '        <label class="custom-label-form">Medicamento</label>';
                    html += '        <input class="form-control" autocomplete="off" type="text" value="' + receita.descr_medicamento + '" readonly>';
                    html += '    </div>';
                    html += '    <div class="col-6">';
                    html += '        <label class="custom-label-form">Posologia</label>';
                    html += '        <input class="form-control" autocomplete="off" type="text" value="' + receita.posologia + '" readonly>';
                    html += '    </div>';
                    html += '</div>';
                    $('#table-prontuario-receita #receita-' + receita.id + ' > .card-body').append(html);
                });
            }
            $('[data-id="#prt-receita"] .qtde-prontuario')
                .data('count', i)
                .attr('data-count', i)
                .find('small')
                .html(i);
        }
    );
}

function prescricoes_por_pessoa(id_pessoa) {
    var html = '';
    $.get('/saude-beta/prescricao/listar-pessoa/' + id_pessoa,
        function (data) {
            data = $.parseJSON(data);
            $('#table-prontuario-prescricao').empty();
            data.forEach(prescricao => {
                html = '<div class="card z-depth-0 bordered" style="border: 1px solid rgba(0, 0, 0, .125)">';
                html += '    <div class="accordion-header w-100">';
                html += '        <div class="row">';
                html += '            <div class="col"> ';
                html += '                <button class="btn btn-link" type="button" data-toggle="collapse"';
                html += '                    data-target="#prescricao-' + prescricao.id + '" aria-expanded="true" aria-controls="collapse">';
                html += moment(prescricao.data).format('DD/MM/YYYY');
                html += '                </button>';
                html += '            </div>';
                html += '            <div class="col-2 d-grid text-right"> ';
                html += '                <div class="my-auto mx-4">';
                html += '                    <i class="my-icon fas fa-print"     title="Imprimir" onclick="new_system_window(' + "'prescricao/imprimir/" + prescricao.id + "'" + ')"></i>';
                html += '                    <i class="my-icon far fa-trash-alt" title="Deletar" onclick="deletar_prescricao(' + prescricao.id + ')"></i>';
                html += '                </div>';
                html += '            </div>';
                html += '        </div>';
                html += '    </div>';
                html += '    <div class="collapse" id="prescricao-' + prescricao.id + '">';
                html += '        <div class="card-body">';
                html += prescricao.corpo;
                html += '        </div>';
                html += '    </div>';
                html += '</div>';
                $('#table-prontuario-prescricao').append(html);
            });

            $('[data-id="#prt-prescricao"] .qtde-prontuario')
                .data('count', data.length)
                .attr('data-count', data.length)
                .find('small')
                .html(data.length);
        }
    );
}

function documentos_por_pessoa(id_pessoa) {
    var html = '';
    $.get('/saude-beta/documento/listar-pessoa/' + id_pessoa + '/' + $('#select-pasta-doc').val(),
        function (data) {
            data = $.parseJSON(data);
            $('#table-prontuario-documento').empty();
            data.forEach(documento => {
                html = '<div class="card z-depth-0 bordered" style="border: 1px solid rgba(0, 0, 0, .125)">';
                html += '    <div class="accordion-header w-100">';
                html += '        <div class="row">';
                html += '            <div class="col"> ';
                html += '                <button class="btn btn-link" type="button" data-toggle="collapse"';
                html += '                    data-target="#documento-' + documento.id + '" aria-expanded="true" aria-controls="collapse1">';
                html += documento.titulo;
                html += '                </button> ';
                html += '            </div>';
                html += '            <div class="col"> ';
                html += '                <button class="btn btn-link" type="button" data-toggle="collapse"';
                html += '                    data-target="#documento-' + documento.id + '" aria-expanded="true" aria-controls="collapse1">';
                html += documento.descr_paciente;
                html += '                </button> ';
                html += '            </div>';
                html += '            <div class="col-2 text-right"> ';
                html += '                <button class="btn btn-link" type="button" data-toggle="collapse"';
                html += '                    data-target="#documento-' + documento.id + '" aria-expanded="true" aria-controls="collapse1">';
                html += moment(documento.created_at).format('DD/MM/YYYY');
                html += '                </button> ';
                html += '            </div>';
                html += '            <div class="col-2 d-grid text-right">';
                html += '                <div class="my-auto mx-4">';
                html += '                    <i class="my-icon far fa-print"     title="Imprimir" onclick="new_system_window(' + "'documento/imprimir/" + documento.id + "'" + ', true)"></i>';
                html += '                    <i class="my-icon far fa-trash-alt" title="Deletar" onclick="deletar_documento(' + documento.id + ')"></i>';
                html += '                </div>';
                html += '            </div>';
                html += '        </div>';
                html += '    </div>';
                html += '    <div class="collapse" id="documento-' + documento.id + '">';
                html += '        <div class="card-body">';
                html += documento.corpo;
                html += '        </div>';
                html += '    </div>';
                html += '</div>';
                $('#table-prontuario-documento').append(html);
            });

            $('[data-id="#prt-documento"] .qtde-prontuario')
                .data('count', data.length)
                .attr('data-count', data.length)
                .find('small')
                .html(data.length);
        }
    );
}
function solicitacoes_por_pessoa(_id_pessoa) {
    $.get('/saude-beta/encaminhamento/solicitacao/listar', {
        id_pessoa : _id_pessoa,
        todos : "S"
    }, function(data) {
        data = $.parseJSON(data);
        $('#table-prontuario-solicitacoes > tbody').empty();
        console.log(data);
        data.forEach(solicitacao => {
            html = "<tr id = 'sol" + solicitacao.id + "'";
            for (x in solicitacao) html += " data-" + x + " = '" + solicitacao[x] + "'";
            html += ">";
            var cont = 0;
            var tamanhos = [10, 39, 39];
            for (x in solicitacao) {
                if (["data", "encaminhante", "para"].indexOf(x) > -1) {
                    html += "<td width = '" + tamanhos[cont] + "%' class = 'text-";
                    if (x == "data" || x == "0" || solicitacao[x] == "0") html += "center";
                    else if (x == "cid_codigo") html += "right";
                    else html += "left";
                    html += "' ";
                    if (x == "cid_codigo" && solicitacao[x] != "0") html += "title = '" + solicitacao.cid_codigo + " - " + solicitacao.cid_nome + "'"
                    html += " style = 'word-wrap: break-word'";
                    if (solicitacao[x] == "0") solicitacao[x] = "---";
                    html += ">" + solicitacao[x] + "</td>";
                    cont++;    
                }
            }
            if (solicitacao.situacao != "F") {
                html += "<td class = 'text-center btn-table-action'>" + 
                    "<i class = 'my-icon far fa-eye' onclick = 'idSol=" + solicitacao.id + ";infEnc()'></i>";
                if (solicitacao.permissao == "S") {
                    html += "<i class = 'my-icon far fa-edit' onclick = 'abrirSolicitacao(" + solicitacao.id + ")'></i>";
                    html += "<i class = 'my-icon far fa-trash-alt' onclick = 'excluirSolicitacao(" + solicitacao.id + ")'></i>";
                }
                html += "</td>";
            } else html += "<td class = 'text-center'>---</td>";
            html += "</tr>";
            $('#table-prontuario-solicitacoes > tbody').append(html);
        });
        $('[data-id="#prt-solicitacoes"] .qtde-prontuario')
            .data('count', data.length)
            .attr('data-count', data.length)
            .find('small')
            .html(data.length);
    })
}
function carregaEncDisponiveis() {
    $.get('/saude-beta/encaminhamento/solicitacao/listar', {
        id_pessoa : $('#pedido_paciente_id').val(),
        todos : "N"
    }, function(data) {
        $('#tab_solicitacoes').parent().css("display", "none");
        $('#tab_solicitacoes').parent().prev().css("display", "none");
        $('#tab_solicitacoes').parent().prev().prev().css("display", "none");
        $('#tab_solicitacoes > tbody').empty();
        if (data != "") {
            data = $.parseJSON(data);
            html = "<tr id = 'solTr0' " + 
                "data-enc_descr='' " +
                "data-enc_id='' " +
                "data-esp_descr='' " +
                "data-esp_id='' " +
                "data-cid_descr='' " +
                "data-cid_id=''" +
                "data-enc_dt=''" +
                "data-enc_para=''" +
            ">" +
                "<td onclick = 'marcaSol(0)' class = 'text-center' width = '6%'>" +
                    "<input type = 'radio' name = 'sol_pedido' value = '0' id = 'sol0' onchange = 'preencheEnc(0)' checked />" +
                "</td>" +
                "<td onclick = 'marcaSol(0)' colspan = 2 width = '94%'>Externo</td>" +
            "</tr>";
            $('#tab_solicitacoes > tbody').append(html);
            data.forEach(solicitacao => {
                html = "<tr id = 'solTr" + solicitacao.id + "' " +
                    "data-enc_descr='" + solicitacao.encaminhante + "' " +
                    "data-enc_id='" + solicitacao.id_de + "' " +
                    "data-esp_descr='" + solicitacao.descr_esp + "' " +
                    "data-esp_id='" + solicitacao.id_especialidade + "' " +
                    "data-cid_descr='" + solicitacao.cid_codigo + " - " + solicitacao.cid_nome + "' " +
                    "data-cid_id='" + solicitacao.id_cid + "'" +
                    "data-enc_dt='" + solicitacao.data + "'" +
                    "data-enc_para='" + solicitacao.id_para + "'" +
                ">" +
                    "<td onclick = 'marcaSol(" + solicitacao.id + ")' class = 'text-center' width = '6%'>" +
                        "<input type = 'radio' name = 'sol_pedido' value = '" + solicitacao.id + "' id = 'sol" + solicitacao.id + "' onchange = 'preencheEnc(" + solicitacao.id + ")'/>" +
                    "</td>" +
                    "<td onclick = 'marcaSol(" + solicitacao.id + ")' width = '47%' style = 'border-right:1px solid #ced4da'>" + solicitacao.encaminhante + "</td>" +
                    "<td onclick = 'marcaSol(" + solicitacao.id + ")' width = '47%'>" + solicitacao.para + "</td>" +
                "</tr>";
                $('#tab_solicitacoes > tbody').append(html);
                $('#tab_solicitacoes').parent().css("display", "block");
                $('#tab_solicitacoes').parent().prev().css("display", "block");
                $('#tab_solicitacoes').parent().prev().prev().css("display", "block");
            });
        }
        $(".limpaSol").each(function() {
            $(this).on("change", function() {
                if (!document.getElementById("sol0").checked) marcaSol(0);
            });
        })
    });
}
var idSol;
function marcaSol(id) {
    if (!document.getElementById("sol0").checked || id > 0) {
        $("input[type=radio]").each(function() {
            $(this).attr("checked", false);
        });
        $("#sol" + id).attr("checked", true);
        preencheEnc(id);
    }
    if (id == 0) {
        if (location.href.indexOf("agenda") == -1) {
            document.getElementById("enc_data").parentElement.parentElement.classList.remove("col-8");
            document.getElementById("enc_data").parentElement.parentElement.classList.add("col-4");    
        }
        document.getElementById("enc_cid_nome").parentElement.style.display = "block";
        document.getElementById("infEncBox").style.display = "none";
    } else {
        if (location.href.indexOf("agenda") == -1) {
            document.getElementById("enc_data").parentElement.parentElement.classList.remove("col-4");
            document.getElementById("enc_data").parentElement.parentElement.classList.add("col-8");
        }
        document.getElementById("enc_cid_nome").parentElement.style.display = "none";
        document.getElementById("infEncBox").style.display = "block";
    }
    idSol = id;
}
const especialidadesPorDepartamento = [
    {
        id    : 1,
        descr : "PILATES",
        fk    : "R"
    },
    {
        id    : 2,
        descr : "MEDICINA",
        fk    : "M"
    },
    {
        id    : 10,
        descr : "NUTRICAO",
        fk    : "N"
    },
    {
        id    : 11,
        descr : "FISIOTERAPIA",
        fk    : "R"
    },
    {
        id    : 12,
        descr : "IEC",
        fk    : "H"
    },
    {
        id    : 34,
        descr : "TERAPIA CELULAR",
        fk    : "M"
    }
];
function infEnc() {
    $.get("/saude-beta/encaminhamento/solicitacao/mostrar", {
        id : idSol
    }, function(data) {
        data = $.parseJSON(data);
        data = data[0];
        data.obs = $.parseJSON(data.obs);
        var departamento = '';
        for (var i = 0; i < especialidadesPorDepartamento.length; i++) {
            if (especialidadesPorDepartamento[i].id == data.id_especialidade) departamento = especialidadesPorDepartamento[i].fk == "H" ? "HABILITAÇÃO" : "REABILITAÇÃO";
        }
        var testes = new Array();
        for (var i = 0; i < data.obs.testes.length; i++) {
            switch(parseInt(data.obs.testes[i].substring(1))) {
                case 1:
                    testes.push("VO2 ESPECÍFICO");
                    break;
                case 2:
                    testes.push("VO2 BASAL");
                    break;
                case 3:
                    testes.push("VO2 SUBMÁXIMO");
                    break;
                case 4:
                    testes.push("TESTE DE FORÇA (DINAMOMETRIA)");
                    break;
                case 5:
                    testes.push("TESTE DE MOVIMENTO (CINEMÁTICA)");
                    break;
            }
        }
        var sTeste = '';
        for (var i = 0; i < testes.length; i++) {
            sTeste += testes[i];
            if (i == testes.length - 2) sTeste += " E ";
            else if (i < testes.length - 2) sTeste += ", ";
        }
        var resultado = "<p><b>PACIENTE:</b> " + data.paciente;
        if (data.solicitante != null) resultado += "<br><b>SOLICITANTE:</b> " + data.solicitante;
        if (data.solicitado_em != '') resultado += "<br><b>SOLICITADO EM:</b> " + data.solicitado_em;
        resultado += "</p><p>";
        if (departamento != '') resultado += "<b>DEPARTAMENTO:</b> " + departamento;
        if (data.especialidade != '') resultado += "<br><b>ESPECIALIDADE:</b> " + data.especialidade;
        if (data.procedimento != '' && data.procedimento != data.especialidade) resultado += "<br><b>PROCEDIMENTO:</b> " + data.procedimento;
        resultado += "</p><p>";
        if (data.atv_semana != 1) resultado += "<b>VEZES NA SEMANA:</b> " + data.atv_semana;
        if (data.id_especialidade == 11) {
            var ag_tot = parseInt(data.ag_tot);
            var ag_cont = parseInt(data.ag_cont);
            resultado += "<br><b>AGENDADOS:</b> " + ag_cont + "/" + ag_tot;
        }
        if (data.obs.duracao != '') resultado += "<br><b>DURAÇÃO PREVISTA:</b> " + data.obs.duracao;
        resultado += "</p><p>";
        if (sTeste != '') {
            resultado += "<b>TESTES:</b> " + sTeste;
            if (data.obs.esporte != '' && data.obs.testes.indexOf("c1") > -1) resultado += "<br><b>ESPORTE:</b> " + data.obs.esporte;
            if (data.obs.parte !== null && data.obs.testes.indexOf("c4") > -1 && data.obs.parte !== undefined) resultado += "<br><b>PARTE:</b> " + ((data.obs.parte == "sup") ? "SUPERIOR" : "INFERIOR");
        }
        resultado += "</p>";
        if (data.obs.obs != '' && [2, 10, 34].indexOf(data.id_especialidade) > -1) resultado += "<p><b>OBSERVAÇÕES:</b> " + data.obs.obs + "</p>"; 
        if (data.retorno != '') resultado += "<p><b>RETORNO:</b> " + data.retorno + "</p>";
        while (resultado.indexOf("<p></p>") > -1) resultado = resultado.replace("<p></p>","");
        while (resultado.indexOf("<p><br>") > -1) resultado = resultado.replace("<p><br>","<p>");
        $("#infSolModal").modal("show");
        $("#inf").html(resultado);
    });
}
function preencheEnc(id) {
    var from = location.href.indexOf("agenda") > -1 ? "agenda" : "pedido";
    $("#" + from + "_encaminhante_nome").val(nuloSeZero($("#solTr" + id).data().enc_descr));
    $("#" + from + "_encaminhante_id").val(nuloSeZero($("#solTr" + id).data().enc_id));
    muda_legenda_encaminhante(nuloSeZero($("#solTr" + id).data().enc_id));
    $("#" + from + "_enc_esp_nome").val(nuloSeZero($("#solTr" + id).data().esp_descr));
    $("#" + from + "_enc_esp_id").val(nuloSeZero($("#solTr" + id).data().esp_id));
    $("#enc_cid_nome").val(nuloSeZero($("#solTr" + id).data().cid_descr));
    $("#enc_cid_id").val(nuloSeZero($("#solTr" + id).data().cid_id));
    if (from == "agenda") {
        $("#agenda_sol").val(id);
        $("#agenda_enc_esp").val($("#solTr" + id).data().esp_id);
    } else $("#enc_data").val(nuloSeZero($("#solTr" + id).data().enc_dt));
}
function nuloSeZero(val) {
    return val == "0" ? "" : val;
}
function encaminhamentos_por_pessoa(id_pessoa) {
    $.get(
        '/saude-beta/encaminhamento/listar/' + id_pessoa,
        function (data) {
            data = $.parseJSON(data);
            $('#table-prontuario-checkout > tbody').empty();
            data.forEach(encaminhamento => {
                html = "<tr>";
                var tamanhos = [10, 32, 10, 8, 32, 8];
                var cont = 0;
                for (x in encaminhamento) {
                    if (x != "descr_cid") {
                        var auxC = "";
                        if (x.indexOf("data") > -1 || ["cod_cid", "anexo"].indexOf(x) > -1 || encaminhamento[x] == "0") {
                            if (x == "cod_cid" && encaminhamento[x] != "0") auxC += " title = '" + encaminhamento.descr_cid + "'";
                            auxC += " class = 'text-";
                            auxC += (encaminhamento[x] == 0 || x.indexOf("data") > -1 || x == "anexo") ? "center" : "right";
                            if (x == "anexo") auxC += " btn-table-action"
                            auxC += "'";
                        }
                        html += "<td width = '" + tamanhos[cont] + "%' " + auxC + " style = 'word-wrap: break-word'>";

                        if (encaminhamento[x] != "0") {
                            if (x == "anexo") {
                                var funcao = (encaminhamento.anexo.substring(encaminhamento.anexo.lastIndexOf(".") + 1) == "pdf") ? "visualizar_anexo" : "window.open";
                                html += '<svg class="svg-inline--fa fa-print fa-w-16 my-icon" onclick="' + funcao + '(' + "'/saude-beta/anexos/" + encaminhamento.anexo + "'" + ')" aria-hidden="true" focusable="false" data-prefix="far" data-icon="print" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">' +
                                    '<path fill="currentColor" d="M400 264c-13.25 0-24 10.74-24 24 0 13.25 10.75 24 24 24s24-10.75 24-24c0-13.26-10.75-24-24-24zm32-88V99.88c0-12.73-5.06-24.94-14.06-33.94l-51.88-51.88c-9-9-21.21-14.06-33.94-14.06H110.48C93.64 0 80 14.33 80 32v144c-44.18 0-80 35.82-80 80v128c0 8.84 7.16 16 16 16h64v96c0 8.84 7.16 16 16 16h320c8.84 0 16-7.16 16-16v-96h64c8.84 0 16-7.16 16-16V256c0-44.18-35.82-80-80-80zM128 48h192v48c0 8.84 7.16 16 16 16h48v64H128V48zm256 416H128v-64h256v64zm80-112H48v-96c0-17.64 14.36-32 32-32h352c17.64 0 32 14.36 32 32v96z"></path>' +
                                '</svg>';
                            } else html += encaminhamento[x];
                        } else html += "---";

                        html += "</td>";
                        cont++;
                    }
                }
                html += "</tr>";
                $('#table-prontuario-checkout > tbody').append(html);
            });
            $('[data-id="#prt-checkout"] .qtde-prontuario')
                .data('count', data.length)
                .attr('data-count', data.length)
                .find('small')
                .html(data.length);
        }
    );
}
function privar_evolucao($id) {
    $.post(
        '/saude-beta/evolucao/tornar-privado', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: $id
    },
        function (data, status) {
            console.log(data + ' | ' + status)
            if (data == 'privado') {
                evolucoes_por_pessoa($('#id_pessoa_prontuario').val(), false)
            }
        }
    )
}
function add_CID_evolucao($obj) {
    a = $($('.cid-evolucao-mobile #cid')[$('.cid-evolucao-mobile #cid').length - 1])
    $(a.parent()[0]).append(a.html())
}
function controlCidEvolucao($obj) {
    // $('#criarEvolucaoModal .note-editable').empty()
    $('#criarEvolucaoModal .note-editable').append('<p style="text-align: center">' + $obj.val() + '</p><p style="text-align: left;"><br></p>')
}
function publicar_evolucao($id) {
    $.post(
        '/saude-beta/evolucao/tornar-publico', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: $id
    },
        function (data, status) {
            console.log(data + '| ' + status)
            if (data == 'publico') {
                evolucoes_por_pessoa($('#id_pessoa_prontuario').val(), false)
            }
        }
    )
}
function enviarEmailRedefinição($id) {
    $('#enviandoEmailModal').modal('show')
    $('#enviandoEmailModal #msg-sucess').hide()
    $('#loading-email').attr('style', "opacity: 0.8;display: flex;justify-content: center;width: 100%;height: 500%;margin-top: -40px;")
    setTimeout(() => {
        $.get('/saude-beta/api/sendEmailPassword', {
            id: $id
        }, function (data, status) {
            console.log(data + ' | ' + status)
            data = $.parseJSON(data);
            testando = data;
            console.log(data.success)
            if (data.sucess == 'true') {
                $('#loading-email').attr('style', "opacity: 0.8;display: none;justify-content: center;width: 100%;height: 500%;margin-top: -40px;")
                $('#enviandoEmailModal #email').html(data.email)
                $('#enviandoEmailModal #msg-sucess').show()
            }
            else {
                alert(data.message);
                $('#enviandoEmailModal').modal('hide')
            }
        })
    }, 1000)
}
function evolucoes_por_pessoa(id_pessoa, bfiltro) {
    bfiltro = (typeof b !== 'undefined') ? bfiltro : true;
    var html = '',
        _new_e;
    if (bfiltro) criar_filtro();
    $.get('/saude-beta/evolucao/listar-pessoa/' + id_pessoa,
        function (data) {
            data = $.parseJSON(data);
            $('#table-prontuario-evolucao').empty();
            data.evolucoes.forEach(evolucao => {
                html = '<li value="' + evolucao.id_area + '" class="resumo-prioritario">';
                html += '    <div class="d-flex" style="margin-bottom: -10px;">';
                if (evolucao.id_area == 0 || evolucao.id_area == null) evolucao.id_area = 0
                html += '<img style="width: 41px;position: relative;bottom: 10px;margin-left: -40px;left: -35px;z-index: 6;border-radius:100%;" src="http://vps.targetclient.com.br/saude-beta/img/areas/' + evolucao.id_area + '.png">'
                html += '       <a style="font-size:22px; font-weight:600; line-height:1" href="#">';
                if (evolucao.descr_evolucao_tipo != null) html += evolucao.descr_evolucao_tipo
                else html += 'Evolução'
                if (evolucao.descr_profissional != undefined && evolucao.descr_profissional != null) html += ' | ' + evolucao.descr_profissional;
                html += ' - ';
                html += moment(evolucao.data).format('DD/MM/YYYY');
                html += ' - ';
                html += evolucao.hora.substring(0, 5);
                html += '       </a>';

                let dataInicio = new Date(evolucao.created_at);
                let dataFim = new Date();
                let diffMilissegundos = dataFim - dataInicio;
                let diffSegundos = diffMilissegundos / 1000;
                let diffMinutos = diffSegundos / 60;
                let diffHoras = diffMinutos / 60;
                let diffDias = diffHoras / 24;
                let diffMeses = diffDias / 30;



                if (evolucao.id_profissional == data.profissional) {
                    console.log(evolucao.id)
                    if (parseInt(diffHoras) < 24) html += '<i onclick="editar_evolucao(' + evolucao.id + ')"  style="position: absolute;right: 5.3%;z-index: 1;" class="my-icon far fa-edit click"></i>'
                    html += '<div class="acoes-evolucao-mobile" id="acoes-evolucao' + evolucao.id + '" style="position: absolute;right: 5%;width: 5%;justify-content: space-between;display: flex;">';
                    html += '<img class="button-encaminhar" style = "'
                    if (parseInt(diffHoras) >= 24) html += 'right:61px;'
                    html += 'display:none" onclick="mostrarEncaminhamento(' + evolucao.id + ')" src="http://vps.targetclient.com.br/saude-beta/img/botao-de-encaminhamento-de-correio.png"> ';
                    
                    var auxMargem = (parseInt(diffHoras) >= 24) ? " style = 'margin-left:44px'" : "";
                    if (evolucao.publico == 'S') html += '<i class="my-icon far fa-eye click" title="Tornar Privado" onclick="privar_evolucao(' + evolucao.id + ')"' + auxMargem + ' ></i>';
                    else html += ' <svg class="svg-inline--fa fa-eye-slash fa-w-20 my-icon click" title="Tornar Privado" ' + auxMargem + ' onclick="publicar_evolucao(' + evolucao.id + ')" aria-labelledby="svg-inline--fa-title-3km1CiWcB104" data-prefix="far" data-icon="eye-slash" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" data-fa-i2svg=""><title id="svg-inline--fa-title-3km1CiWcB104">Tornar Privado</title><path fill="currentColor" d="M634 471L36 3.51A16 16 0 0 0 13.51 6l-10 12.49A16 16 0 0 0 6 41l598 467.49a16 16 0 0 0 22.49-2.49l10-12.49A16 16 0 0 0 634 471zM296.79 146.47l134.79 105.38C429.36 191.91 380.48 144 320 144a112.26 112.26 0 0 0-23.21 2.47zm46.42 219.07L208.42 260.16C210.65 320.09 259.53 368 320 368a113 113 0 0 0 23.21-2.46zM320 112c98.65 0 189.09 55 237.93 144a285.53 285.53 0 0 1-44 60.2l37.74 29.5a333.7 333.7 0 0 0 52.9-75.11 32.35 32.35 0 0 0 0-29.19C550.29 135.59 442.93 64 320 64c-36.7 0-71.71 7-104.63 18.81l46.41 36.29c18.94-4.3 38.34-7.1 58.22-7.1zm0 288c-98.65 0-189.08-55-237.93-144a285.47 285.47 0 0 1 44.05-60.19l-37.74-29.5a333.6 333.6 0 0 0-52.89 75.1 32.35 32.35 0 0 0 0 29.19C89.72 376.41 197.08 448 320 448c36.7 0 71.71-7.05 104.63-18.81l-46.41-36.28C359.28 397.2 339.89 400 320 400z"></path></svg>'

                    if (parseInt(diffHoras) < 24) html += '<i class ="my-icon far fa-trash click" title = "Excluir" onclick = "excluir_evolucao(' + evolucao.id + ')" style = "margin-left:34px"></i>'
                    html += '</div>'
                }
                html += '    </div>';
                if (evolucao.titulo) html += '    <h4 class="title-evolucao-mobile" style="text-transform: capitalize">' + evolucao.titulo.substr(0, 1).toUpperCase() + evolucao.titulo.substr(1).toLowerCase() + '</h4>';
                if (evolucao.cid != null) html += '    <p><b>CID: </b> ' + evolucao.cid + ' </p>';
                html += '    <p><b>Diagnóstico: </b> ' + evolucao.diagnostico + ' </p>';
                html += '</li>';
                $('#evolucoes_avulsas_list > #table-prontuario-evolucao').append(html);
            });

            $('[data-id="#prt-evolucao"] .qtde-prontuario')
                .data('count', data.evolucoes.length)
                .attr('data-count', data.evolucoes.length)
                .find('small')
                .html(data.evolucoes.length);


            $.get('/saude-beta/pessoa/listar-corpo',
                function (data) {
                    data = $.parseJSON(data);
                    data.forEach(parte_corpo => {
                        $.get('/saude-beta/pessoa/status-evolucao/' + $('#id_pessoa_prontuario').val() + '/' + parte_corpo.id,
                            function (data) {
                                data = $.parseJSON(data);
                                if (data.length > 0) {
                                    data.forEach(data => {
                                        $('#' + data.obj).addClass('regiao-vitruviano2');
                                    });
                                }
                            }
                        );
                    })
                }
            );
        }
    );
}
function excluir_evolucao($id) {
    $.post(
        '/saude-beta/evolucao/deletar', {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: $id
        },
        function () {
            evolucoes_por_pessoa($('#id_pessoa_prontuario').val(), false)
        }
    )
}
function criar_filtro() {
    var html = '';
    $.get(
        '/saude-beta/especialidade/listar', {},
        function (data, status) {
            console.log(data + ' | ' + status)
            data = $.parseJSON(data);
            $("#filtro-areas-saude").empty()
            data.forEach(especialidade => {
                if (especialidade.id == null || especialidade.id == 0) especilidade = 0
                html = '<img class="area-filtro" style="width: 41px;position: relative;bottom: 10px;cursor:pointer;border-radius:100%"'
                html += ' onclick="filtrarAreasEvolucao(' + especialidade.id + ')" '
                html += ' src="http://vps.targetclient.com.br/saude-beta/img/areas/' + especialidade.id + '.png">'
                $('#filtro-areas-saude').append(html);
            })
        }
    )
}
function pedidos_por_pessoa(id_pessoa) {
    $.get(
        '/saude-beta/pedido/listar-pessoa/' + id_pessoa + '/0' + $("#select-contratos").val(),
        function (data) {
            data = $.parseJSON(data);
            $data = data
            console.log(data)
            $('#table-prontuario-pedido > tbody').empty();
            $cont = 0
            data.pedidos.forEach(pedido => {
                html = '<tr>';
                html += '    <td width="10%" class="text-center">';
                html += data.total_atividades[$cont] - data.atividades_consumidas[$cont]
                html += '    </td>';
                html += '    <td width="10%" class="text-left">'
                html += moment(pedido.data).format('DD/MM/YYYY')
                html += '    </td>';
                html += '    <td width="20%" class="text-center">';
                html += pedido.descr_emp
                html += '    </td>';
                html += '    <td width="10%" class="text-right">';
                html += 'R$ ' + pedido.total
                html += '    </td>';
                // html += '    <td width="25%">';
                // html += moment(pedido.created_at).format('DD/MM/YYYY') + ' por ' + pedido.created_by;
                // html += '    </td>';
                html += '    <td width="10%" class="text-center" style="padding:0">'
                html += moment(pedido.data_validade).format('DD/MM/YYYY');
                html += '    </td>';
                html += '    <td width="10%" style="font-size:0.75rem">';
                if (pedido.status == 'F' && (data.total_atividades[$cont] - data.atividades_consumidas[$cont]) > 0) html += '<div class="tag-pedido-finalizado">Em Execução</div>';
                else if (pedido.status == 'F' && (data.total_atividades[$cont] - data.atividades_consumidas[$cont]) == 0) html += '<div class="tag-pedido-cancelado">Finalizado</div>';
                //if (pedido.status == 'F') html += '<div class="tag-pedido-cancelado">Finalizado</div>';
                else if (pedido.status == 'E') html += '<div class="tag-pedido-aberto">Aprovação do Paciente</div>';
                else if (pedido.status == 'A') html += '<div class="tag-pedido-primary">Em Edição</div>';
                else html += '<div class="tag-pedido-cancelado">Cancelado</div>';
                html += '    </td>';
                html += '    <td width="20%" class="text-right btn-table-action">';

                if ((data.total_atividades[$cont] - data.atividades_consumidas[$cont]) > 0/* && pedido.status == 'A'*/) {
                    html += '<img src="http://vps.targetclient.com.br/saude-beta/img/money-exchange.png" '
                    html += '   style="width: 24px;opacity: .8;margin: -13px 5px 0px 0px;cursor: pointer;"'
                    html += '   onclick="abrirModalConversaoCredito(' + pedido.id + "," + $("#select-contratos").val() + ');">'
                }

                if (pedido.status != 'C' && pedido.status != 'S' && $("#select-contratos").val() == 0) {
                    html += '   <img id="congelar-contrato" onclick="abrircongelarContrato(' + pedido.id + ')"'
                    html += 'src="http://vps.targetclient.com.br/saude-beta/img/proibido.png">'
                    if (pedido.status != 'C') {
                        html += '    <i class="my-icon far fa-file-times" title="Cancelar" onclick="mudar_status_pedido(' + pedido.id + ',' + "'C'" + ')"></i>';
                        if (pedido.status != 'F') {
                            html += '    <i class="my-icon far fa-edit" title="Editar" onclick="editar_pedido(' + pedido.id + ')"></i>';
                        }
                    }
                }
                else if (pedido.status != 'C' && pedido.status == 'S') {
                    html += '<img onclick="descongelar_contrato(' + pedido.id + ')" src="http://vps.targetclient.com.br/saude-beta/img/desbloquear.png" style="vertical-align: middle;border-style: none;max-width: 23px;position: relative;top: -6px;right: 3px;opacity: 0.7;cursor: pointer">'
                }
                html += '    <i class="my-icon far fa-eye" title="Visualizar" '
                /*if (pedido.assinado == 'N') */html += ' onclick="new_system_window' + "(" + "'pedido/imprimir/" + pedido.id + "/" + $("#select-contratos").val() + "'" + ')"></i>';
                /*else*/
                if (pedido.signed_url != 'N') html += ' <i class="my-icon far fa-print" title="Imprimir" onclick="window.open(' + "'" + pedido.signed_url + "'" + ')"></i>'
                html += '        <i class="my-icon far fa-history" title="Histórico" onclick="log_contrato(' + pedido.id + ')"></i>';
                html += '        <i class="my-icon far fa-trash-alt" title="Deletar" onclick="deletar_pedido(' + pedido.id + ')"></i>';
                if ($("#select-contratos").val() == 0) {
                    if (pedido.assinado == 'N' && data.contratos[$cont] && pedido.signed_url == 'N') html += ' <i class="my-icon far fa-signature" title="Gerar contrato" onclick="abrirModalContratoZapSign(' + pedido.id + ',' + $('#id_pessoa_prontuario').val() + ')"></i>';
                    // else                                                html += ' <i class="my-icon far fa-signature" title="Assinar" onclick="window.open('+ "'" + pedido.signed_url + "'" +')"></i>'
                }
                html += '    </td>';
                html += '</tr>';
                $('#table-prontuario-pedido > tbody').append(html);
                $cont++
            });
            $('[data-id="#prt-plano-tratamento"] .qtde-prontuario')
                .data('count', data.length)
                .attr('data-count', data.length)
                .find('small')
                .html(data.length);
        }
    );
}

function log_contrato(contrato) {
    $("#logContratoModalLabel").html("Contrato " + contrato + " - Histórico de planos");
    $.get("/saude-beta/pedido/log/" + contrato, function(data) {
        data = $.parseJSON(data);
        let resultado = "";
        data.forEach((plano) => {
            if (resultado != "") resultado += "<hr>";
            resultado += "<p align = 'center'><b>" + plano.nome + "</b></p>" +
                "<table border = 1 class = 'tabela-historico' style = 'margin:auto'>" +
                    "<thead>" +
                        "<tr>" +
                            "<th>Mês</th>" +
                            "<th>Nome</th>" +
                            "<th>Total de atividades</th>" +
                            "<th>Atividades semanais</th>" +
                            "<th>Excluído?</th>" +
                        "</tr>" +
                    "</thead>" +
                    "<tbody>";
            
            plano.historico.forEach((linha) => {
                resultado += "<tr>" +
                    "<td>" + ["JANEIRO", "FEVEREIRO", "MARÇO", "ABRIL", "MAIO", "JUNHO", "JULHO", "AGOSTO", "SETEMBRO", "OUTUBRO", "NOVEMBRO", "DEZEMBRO"][parseInt(linha.mes) - 1] + "/23</td>" +
                    "<td>" + linha.descr + "</td>" +
                    "<td class = 'text-right'>" + linha.max_atv + "</td>" +
                    "<td class = 'text-right'>" + linha.max_atv_semana + "</td>" +
                    "<td class = 'text-center'>" + ((parseInt(linha.lixeira)) ? "SIM" : "NÃO") + "</td>" + 
                "</tr>";
            });

            resultado += "</tbody></table>";
        });
        document.querySelector("#logContratoModal .modal-body").innerHTML = resultado;
        $("#logContratoModal").modal("show");
    });
}

function atv_pessoa(id, nome) {
    $("#atvPessoaModalLabel").html(nome);
    $.get("/saude-beta/pessoa/atividades/" + id, function(data) {
        data = $.parseJSON(data);
        let resultado = "<table border = 1 class = 'tabela-historico' style = 'margin:auto'>" +
            "<thead>" +
                "<th class = 'text-right'>Contrato</th>" +
                "<th>Plano</th>" +
                "<th class = 'text-right'>Disponíveis</th>" +
                "<th>&nbsp;</th>" +
            "</thead>" +
            "</tbody>";
        data.forEach((plano) => {
            resultado += "<tr>" +
                "<td class = 'text-right'>" + plano.id_pedido + "</td>" +
                "<td>" + plano.nome + "</td>" +
                "<td class = 'text-right'>" + plano.disponivel + "</td>" +
                "<td>" +
                    "<div onclick = 'atv_plano(" + plano.id + ")'>" +
                        "<img id = 'olho-open-modal' src = '/saude-beta/img/olho.png' alt = 'Olho' style = 'margin:0'>" +
                    "</div>" +
                "</td>" +
            "</tr>";
        });
        resultado += "</tbody></table>";
        document.querySelector("#atvPessoaModal .modal-body").innerHTML = resultado;
        $("#atvPessoaModal").modal("show");
    });
}

function atv_plano(id) {
    $("#atvContratoModalLabel").html("Extrato de plano em contrato");
    $.get("/saude-beta/pedido/atividades/" + id, function(data) {
        data = $.parseJSON(data);
        console.log(data);
        let resultado = new Array();
        data.forEach((item) => {
            if (item.titulo.indexOf("Agendamento") > -1) {
                let hora = item.hora.split(":");
                let obs = item.obs_cancelamento;
                obs = obs != null ? "<br>" + obs : "";
                switch(item.status) {
                    case "F":
                        var div = "<div class = 'tag-pedido-finalizado' style='font-size:13px'>Finalizado";
                        break;
                    case "C":
                        var div = "<div class = 'tag-pedido-cancelado' style='font-size:13px'>Cancelado";
                        break;
                    default:
                        var div = "<div class = 'tag-pedido-cancelado' style='font-size:13px;background:var(--cyan)'>Aberto";
                }
                aux = "<table>" +
                    "<tr>" +
                        "<td style = 'padding-right:20px;vertical-align:top'><img src = '/saude-beta/areas/" + item.id_especialidade + ".png' style = 'height:50px'/>" +
                        "<td style = 'width:100%'>" + 
                            "<h5>Agendado para " + item.data + " às " + hora[0] + ":" + hora[1] + "</h5>" +
                            item.profissional + "<br />" +
                            "<font size = 1>Feito por " + item.created_by + " em " + item.dtlan + obs + "</font>" + 
                        "</td>" +
                        "<td style = 'vertical-align:top'>" +
                            div + "</div>" +
                        "</td>" +
                    "</tr>" +
                "</table>";
            } else aux = "<h5>" + item.titulo + "</h5>";
            resultado.push(aux);
        });
        document.querySelector("#atvContratoModal .modal-body").innerHTML = resultado.join("<hr />");
        $("#atvContratoModal").modal("show");
    });
}

// function atv_plano(id) {
//     $.get("/saude-beta/pedido/atividades/" + id, function(data) {
//         data = $.parseJSON(data);
//         let resultado = "<table border = 1 class = 'tabela-historico' style = 'margin:auto'>" +
//             "<thead>" +
//                 "<th>Tipo</th>" +
//                 "<th>Data</th>" +
//                 "<th>Observação</th>" +
//             "</thead>" +
//             "<tbody>";
//         data.forEach((contrato) => {
//             let obs = (contrato.obs.trim() == "") ? "---" : contrato.obs.trim();
//             resultado += "<tr>" + 
//                 "<td>" + contrato.acao + "</td>" +
//                 "<td>" + contrato.data + "</td>" +
//                 "<td" + (obs == "---" ? " class = 'text-center'" : "") + ">" + obs + "</td>" +
//             "</tr>";
//         });
//         resultado += "</tbody></table>";
//         document.querySelector("#atvContratoModal .modal-body").innerHTML = resultado;
//         $("#atvContratoModal").modal("show");
//     });
// }

function filtrarAreasEvolucao(value) {
    document.querySelectorAll('#table-prontuario-evolucao > li').forEach(el => {
        if (el.value != value) {
            el.style.display = 'none'
        }
        else {
            el.style.display = 'block'
        }
    })
}
function proc_aprovados_por_pessoa(id_pessoa) {
    var html = '';
    $.get('/saude-beta/pedido-servicos/listar-pessoa/' + id_pessoa,
        function (data) {
            data = $.parseJSON(data);
            $('#table-prontuario-procedimentos-aprovados > tbody').empty();
            data.forEach(function (procedimento, index) {
                index++;
                html = '<tr row_number="' + index + '">';
                html += '    <td width="10%" class="text-center" data-id_pedido="' + procedimento.id_pedido + '">' + procedimento.id_pedido.toString().padStart(6, '0').slice(-6) + '</td>';
                html += '    <td width="20%" data-procedimento_id="' + procedimento.id_procedimento + '" data-procedimento_obs="' + procedimento.obs + '">';
                html += procedimento.descr_procedimento;
                if (procedimento.obs != null && procedimento.obs != '') html += ' (' + procedimento.obs + ')';
                html += '    </td>';
                html += '    <td width="15%">'
                html += procedimento.descr_convenio;
                html += '    </td>';
                if (procedimento.dente_regiao != null) html += '<td width="8%" class="text-right" data-dente_regiao="' + procedimento.dente_regiao + '">' + procedimento.dente_regiao + '</td>';
                else html += '<td width="8%" class="text-right" data-dente_regiao=""></td>';
                if (procedimento.face != null) html += '<td width="8%" class="text-right" data-dente_face="' + procedimento.face + '">' + procedimento.face + '</td>';
                else html += '<td width="8%" class="text-right" data-dente_face=""></td>';
                if (procedimento.status == 'F') html += '<td width="15%" data-profissional_exe_id="' + procedimento.id_prof_finalizado + '">' + procedimento.descr_prof_finalizado + '</td>';
                else html += '<td width="15%" data-profissional_exe_id="' + procedimento.id_prof_exe + '">' + procedimento.descr_prof_exe + '</td>';
                // html += '    <td width="8%" class="text-right">'
                // html += 'R$ ' + procedimento.valor.toString().replace('.',',');
                // html += '    </td>';
                html += '    <td width="12%" class="text-center">'
                if (procedimento.status == 'F') {
                    html += '   <div class="tag-pedido-finalizado">Finalizado</div>';
                } else if (procedimento.status == 'C') {
                    html += '   <div class="tag-pedido-cancelado">Cancelado</div>';
                } else if (procedimento.qtde_evolucao > 0) {
                    html += '   <div class="tag-pedido-aberto">Em Execução</div>';
                } else {
                    html += '   <div class="tag-pedido-primary">Pendente</div>';
                }
                html += '    </td>';

                html += '    <td width="12%"  class="text-right btn-table-action">';
                if (procedimento.status == 'F') {
                    html += ' <div class="badge-item-table" title="Evoluções do procedimento.">'
                    html += '   <span>' + procedimento.qtde_evolucao + '</span>';
                    html += '   <i class="my-icon far fa-comment-alt-plus" onclick="add_pedido_evolucao(' + procedimento.id + ', false)"></i>';
                    html += ' </div>'
                    html += ' <i class="my-icon far fa-check text-success" title="procedimento Finalizado."></i>';
                } else if (procedimento.status == 'C') {
                    html += ' <i class="my-icon fal fa-times text-danger" title="procedimento Cancelado."></i>';
                } else {
                    html += ' <i class="my-icon far fa-check-circle" onclick="finalizar_procedimento_aprovado(' + procedimento.id + ')" title="Finalizar procedimento."></i>';
                    html += ' <div class="badge-item-table" title="Evoluções do procedimento.">'
                    html += '   <span>' + procedimento.qtde_evolucao + '</span>';
                    html += '   <i class="my-icon far fa-comment-alt-plus" onclick="add_pedido_evolucao(' + procedimento.id + ', true)"></i>';
                    html += ' </div>'
                    html += ' <i class="my-icon far fa-times" style="font-size:34px; padding-top:5px;" onclick="cancelar_procedimento_aprovado(' + procedimento.id + ')" title="Cancelar procedimento."></i>';
                }
                html += '    </td>';
                html += '</tr>';
                $('#table-prontuario-procedimentos-aprovados > tbody').append(html);
            });

            $('[data-id="#prt-procedimentos-aprovados"] .qtde-prontuario')
                .data('count', data.length)
                .attr('data-count', data.length)
                .find('small')
                .html(data.length);
        }
    );
}

function anamneses_por_pessoa(id_pessoa) {
    var html = '';
    $.get('/saude-beta/anamnese-pessoa/listar-pessoa/' + id_pessoa,
        function (data) {
            data = $.parseJSON(data);
            $('#table-prontuario-anamnese-pessoa').empty();
            data.forEach(anamnese_pessoa => {
                html = '<div class="card z-depth-0 bordered" style="border: 1px solid rgba(0, 0, 0, .125);min-height: 55px;">';
                html += '    <div class="accordion-header w-100">';
                html += '        <div class="row">';
                html += '            <div class="col"> ';
                html += '                <button class="btn-anamnese-mobile btn btn-link" type="button" data-toggle="collapse" onclick="visualizar_anamnese(';
                html += anamnese_pessoa.id + ');"'
                html += '                    data-target="#evolucao-' + anamnese_pessoa.id + '" aria-expanded="true" aria-controls="collapse">';
                html += anamnese_pessoa.descr_anamnese;
                html += '                </button> ';
                html += '            </div>';
                html += '            <div class="col-4 d-flex text-right"> ';
                html += '                <button class="btn-data-anamnese-mobile btn btn-link ml-auto" type="button" data-toggle="collapse"';
                html += '                    data-target="#evolucao-' + anamnese_pessoa.id + '" aria-expanded="true" aria-controls="collapse">';
                html += moment(anamnese_pessoa.data).format('DD/MM/YYYY');
                html += ' às ';
                html += anamnese_pessoa.hora.substring(0, 5);
                html += '                </button> ';
                html += '                <div class="my-auto mx-4 acoes-anamnese-mobile">';
                html += '                    <i class="my-icon far fa-print" onclick="imprimir_anamnese(' + anamnese_pessoa.id + ')"></i>';
                html += '                    <i class="my-icon far fa-trash-alt" onclick="deletar_anamnese(' + anamnese_pessoa.id + ')"></i>';
                html += '                </div>';
                html += '            </div>';
                html += '        </div>';
                html += '    </div>';
                html += '</div>';
                $('#table-prontuario-anamnese-pessoa').append(html);
            });

            $('[data-id="#prt-anamnese"] .qtde-prontuario')
                .data('count', data.length)
                .attr('data-count', data.length)
                .find('small')
                .html(data.length);
        }
    );
}
function visualizar_anamnese(id) {
    $("#visualizarAnamneseModal").modal('show');
    $('#table-anamnese-pessoa').empty();
    $.get(
        '/saude-beta/anamnese/visualizar-anamnese/' + id,
        function (data, status) {
            console.log(data + ' | ' + status)
            data = $.parseJSON(data)
            a = data;
            $("#table-anamnese-pessoa-titulo").html(data.descr)
            var tabela = new Array();
            console.log(data.perguntas);
            for (var i = 0; i < data.perguntas.length; i++) {
                for (var j = 0; j < data.respostas.length; j++) {
                    if (data.respostas[j].id_pergunta == data.perguntas[i].id) tabela.push([data.perguntas[i].pergunta, data.respostas[j].resposta]);
                }
            }
            for (var i = 0; i < tabela.length; i++) {
                if (i % 2 == 0) html = '<tr style="background-color: #d8d8d8;font-size: 15px;color:black;line-height:2">'
                else html = '<tr style="font-size: 15px;color:black;line-height:2">';
                html += '   <td style="color:black" width="50%;display:flex">' + tabela[i][0] + '</td>'
                html += '   <td style="color:black" width="50%;display:flex">' + tabela[i][1] + '</td></tr>'
                $("#table-anamnese-pessoa").append(html)
            }
        }
    )
}
var contCanvas = 0;
var a
function imprimir_laudo($id) {
    redirect('/saude-beta/IEC/imprimir-laudo/' + $id, true)
}
function tdNone(el,isN) {
    el.style.textDecoration = isN ? "none" : "underline";
}
function deletar_laudo($id) {
    if (window.confirm('Deseja mesmo deletar este registro?')) {
        $.post('/saude-beta/IEC/deletar-laudo', {
            _token: $('meta[name="csrf-token"]').attr('content'),
            id: $id
        }, function(data, status) {
            console.log(data + ' | ' + status)
            if (data == 'true') {
                alert('Deletado com sucesso')
                iec_por_pessoa($('#id_pessoa_prontuario').val())
            }
        })
    }
}
function iec_por_pessoa(id_pessoa, mudaNum) {
    var html = '';
    $.get('/saude-beta/IEC/listar-pessoa/' + id_pessoa + '/' + $('#select-iec-ativos-inativos').val(),
        function (data) {
            var lista_group = [];
            $('#table-prontuario-IEC-pessoa').empty();
            i = 0;
            var dadosGrafico = {
                labels : [],
                datasets : [{
                    data: [],
                    pointBackgroundColor : [],
                    pointBorderColor : [],
                    pointBorderWidth : [],
                    fill : false,
                    spanGaps: true
                }]
            };
            IECArr = new Array();
            data.forEach(IEC_pessoa => {
                var nomeLblaux = IEC_pessoa.descr_iec.split(" ");
                var nomeLbl = new Array();
                for (var i = 0; i < nomeLblaux.length; i++) {
                    if (nomeLblaux[i] != "-" && i % 3 == 0) nomeLbl[nomeLbl.length] = nomeLblaux[i];
                    else nomeLbl[nomeLbl.length - 1] += " " + nomeLblaux[i];
                }
                for (var i = 0; i < nomeLbl.length; i++) {
                    nomeLbl[i] = nomeLbl[i].trim();
                    if (nomeLbl[i].substring(nomeLbl[i].length - 1, nomeLbl[i].length) == "-") nomeLbl[i] = nomeLbl[i].substring(0, nomeLbl[i].length - 1)
                }
                if (IECArr.indexOf(IEC_pessoa.descr_iec) == -1) {
                    dadosGrafico.labels.push(nomeLbl);
                    dadosGrafico.datasets[0].data.push(IEC_pessoa.piores - 1);
                    dadosGrafico.datasets[0].pointBorderWidth.push(10);   
                }
                if (lista_group.includes(IEC_pessoa.id_questionario)) {
                    html = '<div class="card-iec card z-depth-0 bordered" style="display: none;border: 1px solid rgba(0, 0, 0, .125)">';
                } else {
                    html = '<div class="card z-depth-0 bordered" style="border: 1px solid rgba(0, 0, 0, .125)">';
                    lista_group.push(IEC_pessoa.id_questionario)
                }
                html += '    <div class="accordion-header w-100">';
                html += '        <div class="row opacity-hover">';
                html += '            <div class="col" style = "padding-top:3px"> ';
                html += '                <button class="btn btn-link" type="button"';
                if ($('#select-iec-ativos-inativos').val() != 'L') {
                    html += ' data-toggle="collapse"';
                    html += '  onclick="historico_IEC(' + IEC_pessoa.id + ',' + IEC_pessoa.id_paciente + ');" '
                    html += '                    data-target="#evolucao-' + IEC_pessoa.id + '" aria-expanded="true" aria-controls="collapse"';
                } else {
                    html += ' onclick = "tdNone(this,true)"; ';
                    html += ' onmouseover = "tdNone(this,false)"; ';
                    html += ' onmouseout = "tdNone(this,true)"; ';
                }
                html += 'style="display: flex;">';
                html += '<div style="width:30px; height:30px; border-radius:100%;min-width: 30px;'
                switch (IEC_pessoa.piores) {
                    case 1:
                        html += 'background-color: red;opacity: 1;margin-right: 10px;"></div>';
                        if (IECArr.indexOf(IEC_pessoa.descr_iec) == -1) {
                            dadosGrafico.datasets[0].pointBackgroundColor.push("red");
                            dadosGrafico.datasets[0].pointBorderColor.push("red");
                        }
                        break;
                    case 2:
                        html += ' background-color:#cccc00;opacity: 1;margin-right: 10px;"></div>';
                        if (IECArr.indexOf(IEC_pessoa.descr_iec) == -1) {
                            dadosGrafico.datasets[0].pointBackgroundColor.push("#cccc00");
                            dadosGrafico.datasets[0].pointBorderColor.push("#cccc00");
                        }
                        break;
                    case 3:
                        html += 'background-color:green;opacity: 1;margin-right: 10px;"></div>';
                        if (IECArr.indexOf(IEC_pessoa.descr_iec) == -1) {
                            dadosGrafico.datasets[0].pointBackgroundColor.push("green");
                            dadosGrafico.datasets[0].pointBorderColor.push("green");
                        }
                        break;
                    case 4:
                        html += 'background-color:blue;opacity: 1;margin-right: 10px;"></div>';
                        if (IECArr.indexOf(IEC_pessoa.descr_iec) == -1) {
                            dadosGrafico.datasets[0].pointBackgroundColor.push("blue");
                            dadosGrafico.datasets[0].pointBorderColor.push("blue");
                        }
                        break;
                    default:
                        html += '"></div>'
                }
                html += '<span class="descr_iec"'
                html += ' style="margin-top: 3px;">' + IEC_pessoa.descr_iec + '</span>';
                html += '                </button> ';
                html += '            </div>';
                html += '            <div class="col-4 d-flex text-right"> ';
                html += '                <button class="btn-data btn btn-link ml-auto" type="button" data-toggle="collapse"';
                html += ' onclick = "tdNone(this,true)"; ';
                html += ' onmouseover = "tdNone(this,false)"; ';
                html += ' onmouseout = "tdNone(this,true)"; ';
                html += '                    data-target="#evolucao-' + IEC_pessoa.id + '" aria-expanded="true" aria-controls="collapse">';
                html += moment(IEC_pessoa.updated_at).format('DD/MM/YYYY');
                html += ' às ';
                html += IEC_pessoa.updated_at.substring(11, 16);
                html += '                </button> ';
                html += '                <div class="acoes-iec my-auto mx-4">';
                if ($('#select-iec-ativos-inativos').val() != 'L') {
                    if (IEC_pessoa.destacar == "N") html += '                    <i class="my-icon far  far fa-star" onclick="favoritar_IEC(' + IEC_pessoa.id + ',' + IEC_pessoa.id_paciente + ')"></i>';
                    else html += '                    <i class="my-icon far  fas fa-star" onclick="favoritar_IEC(' + IEC_pessoa.id + ',' + IEC_pessoa.id_paciente + ')"></i>';
                    html += '                    <i class="my-icon far fa-print" onclick="imprimir_IEC(' + IEC_pessoa.id + ')"></i>';
                    html += '                    <br>';
                    html += '                    <i class="my-icon far fa-edit" onclick="editar_IEC_P(' + IEC_pessoa.id + ',' + IEC_pessoa.id_questionario + ')"></i>';
                    html += '                    <i class="my-icon far fa-trash-alt" onclick="deletar_IEC(' + IEC_pessoa.id + ')"></i>';
                }
                else {
                    html += '                    <i class="my-icon far fa-print" onclick="imprimir_laudo(' + IEC_pessoa.id + ')"></i>';
                    html += '                    <i class="my-icon far fa-trash-alt" onclick="deletar_laudo(' + IEC_pessoa.id + ')"></i>';
                }
                html += '                </div>';
                html += '            </div>';
                html += '        </div>';
                html += '    </div>';
                html += '</div>';
                $('#table-prontuario-IEC-pessoa').append(html);
                i += 1;
                IECArr.push(IEC_pessoa.descr_iec);
            });
            if (mudaNum !== undefined) {
                $('[data-id="#prt-IEC"] .qtde-prontuario')
                    .data('count', lista_group.length)
                    .attr('data-count', lista_group.length)
                    .find('small')
                    .html(lista_group.length);
            }
            if ($('#select-iec-ativos-inativos').val() == "S") {
                contCanvas++;
                var cresceu = false;
                while (dadosGrafico.labels.length < 5) {
                    dadosGrafico.labels.push("");
                    cresceu = true;
                }
                document.getElementById("chart-content-tudo").innerHTML = '<canvas id = "chart-content' + contCanvas + '" style = "margin-top:-5rem;margin-bottom:-5rem">';
                try {
                    var radarChart = new Chart(document.getElementById("chart-content" + contCanvas), {
                        type: 'radar',
                        data: dadosGrafico,
                        options: {
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            events: [],
                            scales: {
                                r: {
                                    beginAtZero: true,
                                    max: 3,
                                    ticks: {
                                        display: false // Hides the labels in the middel (numbers)
                                    }
                                }
                            },
                            scale: {
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    });
                } catch(err) {}
            }
            if (data.length) {
                if (cresceu) {
                    document.getElementById("chart-content-tudo").style.paddingTop = "70px";
                    document.getElementById("chart-content-tudo").style.paddingBottom = "56px";
                }
                document.getElementById("chart-content-tudo").style.height = "";
                document.getElementById("laudo_diagnostico").style.display = "";
                document.getElementById("laudoBtnModal").style.display = "";
                document.getElementById("laudo_idPessoa").value = data[0].id_paciente;
                document.getElementById("laudo_grafico").value = JSON.stringify(dadosGrafico);
                document.getElementById("laudo_endereco").value = location.href;
            } else {
                document.getElementById("chart-content-tudo").innerHTML = "<b style = 'margin:auto'>Nenhum dado encontrado</b>";
                document.getElementById("chart-content-tudo").style.height = "390px";
                document.getElementById("laudo_diagnostico").style.display = "none";
                document.getElementById("laudoBtnModal").style.display = "none";
                document.getElementById("laudo_idPessoa").value = "";
                document.getElementById("laudo_grafico").value = "";
                document.getElementById("laudo_endereco").value = "";
            }
        }
    );
}
function editar_IEC_P(id, id_questionario) {
    mostrar_questionario_iec(id_questionario);
    $.get('/saude-beta/IEC/carregar/' + id, function(data) {
        data = $.parseJSON(data);
        for (var i = 0; i < data.resp.length; i++) {
            $($("#resposta_" + data.resp[i].id_questao).children()[data.resp[i].resposta - 1]).find("input").attr("checked", "true");
        }
        $("#obs1").find("#obs").val(data.iec[0].obs);
    });
}
function changeTipoEvolucao() {
    let value = document.querySelector('#id_evolucao_tipo').value
    if (value == 1) {
        $('#id_parte_corpo').removeAttr('required')
        $('#id_parte_corpo').val(0);
        $('#criarEvolucaoModal #cid').parent().find('label').css('display', 'block')
        $('#criarEvolucaoModal #cid').parent().css('min-width', ' 86%').find('input').css('display', 'block')
        $('#criarEvolucaoModal #btns-cid').show()
        $('.cid-evolucao-mobile').removeClass('col-6').addClass('col-10')
    }
    else if (value == 4) {
        $('#id_parte_corpo').removeAttr('required')
        $('#id_parte_corpo').val(0);
        $('#criarEvolucaoModal #cid').parent().find('label').css('display', 'none')
        $('#criarEvolucaoModal #cid').parent().css('min-width', ' 53%').find('input').css('display', 'none')
        //$('#criarEvolucaoModal #btns-cid').hide()

        $('.cid-evolucao-mobile').removeClass('col-6').addClass('col-10')
    }
    else {
        // $('#id_parte_corpo').prop('required', 'true');
        $('#id_parte_corpo').val(0);
        $('#criarEvolucaoModal #cid').parent().find('label').css('display', 'block')
        $('#criarEvolucaoModal #cid').parent().css('min-width', ' 53%').find('input').css('display', 'block')
        $('#criarEvolucaoModal #btns-cid').show()

        $('.cid-evolucao-mobile').removeClass('col-10').addClass('col-6').css('padding-top', '1.7%')
    }
    document.querySelector("#div-id-corpo").style.display = ['1', '4', '5', '6'].indexOf(value) > -1 ? "none" : "block";
    if (parseInt(value) > 2) {
        $("#cid_id").parent().css("display", "none");
        $('.cid-evolucao-mobile').css('display', 'none');
        $("#btns-cid").css("margin-top", "16px");
    } else {
        $("#cid_id").parent().css("display", "block");
        $('.cid-evolucao-mobile').css('display', 'block');
    }
    if (value == 7) $("#div-id-corpo").removeClass("lll").removeClass("col-4").addClass("col-10");
    else $("#div-id-corpo").addClass("lll").addClass("col-4").removeClass("col-10");
    if (['4', '5', '6'].indexOf(value) > -1) $("#btns-cid").removeClass("col-md-2").addClass("col-md-12");
    else $("#btns-cid").removeClass("col-md-12").addClass("col-md-2");
}
function abrir_iec(id, id_paciente) {
    $('#table-historico-IEC-pessoa').empty();
    document.querySelector('.modal-dialog.modal-xl').className = 'modal-dialog modal-lg'
    $.get(
        '/saude-beta/IEC/visualizar-resposta/' + id,
        function (data, status) {
            console.log(data + ' | ' + status)
            i = 0
            altura = 140
            data = $.parseJSON(data)
            a = data
            $('#table-historico-IEC-pessoa').append('<h3 align="center" style="margin-bottom:15px">' + data.descr + '</h3>');
            data.perguntas.forEach(questao => {

                html = ' <p style="line-height: 2;">P:' + questao.pergunta + '</p>'
                html += '   <div style="display: flex; margin-top: -15px;">'
                html += '       <p style="line-height: 2;">R:</p> '
                html += '<p class="resposta" style="'
                if (data.valores[i] == 1) {
                    html += 'background-color: #dc3545;'
                }
                else if (data.valores[i] == 2) {
                    html += 'background-color: #e6e629;'
                }
                else if (data.valores[i] == 3) {
                    html += 'background-color: #28a745;'
                }
                else {
                    html += 'background-color: #238DFC;'
                }
                html += 'color: white;border-radius: 20px;line-height: 2;width: 70%;padding: 0px 18px;">'

                html += data.respostas[i] + '</p>'
                html += '<div style="margin: 0px 0px -30px 10px;">'
                data.id_areas_sugeridas[i].forEach(area => {
                    if (area.id_area != '') {
                        html += ' <img style="width: 41px;position: relative;bottom: 5px;" src="http://vps.targetclient.com.br/saude-beta/img/areas/'
                        html += area.id_area + '.png"> '
                    }
                })
                html += ' </div></div></div>'
                $('#table-historico-IEC-pessoa').append(html);
                i++;
            })
            html = '<h4 style = "margin-top:20px">Observações</h4>'
            html += ' <div style="margin: 0 0px 50px 0px;">'
            html += data.obs != null ? data.obs : "NADA CONSTA";
            html += '</div>'
            $('#table-historico-IEC-pessoa').append(html);

            $('#table-historico-IEC-pessoa').append('<div style="display: flex;justify-content: space-evenly;margin-top: 20px;"><button type="button" onclick="historico_IEC(' + id + ',' + id_paciente + ')" class="btn btn-target px-5" id="voltar" style="display: inline;">Voltar</button></div>')
            if (detectar_mobile()) {
                $('.resposta').css('width', '100%')
            }
        }
    )
}
function favoritar_IEC(id, id_paciente, el) {
    $.get(
        '/saude-beta/IEC/favoritar/' + id,
        function (data, status) {
            console.log(data, status)
            if (data == 'true') {
                iec_por_pessoa(id_paciente)
                if (el !== undefined) historico_IEC(id, id_paciente);
            }
            else {
                console.log('error');
            }
        }
    )
}
function historico_IEC(id, id_paciente) {
    var html = '';
    $.get('/saude-beta/IEC/historico/' + id,
        function (data) {
            $('#historicoIECModal #table-historico-IEC-pessoa').empty();
            var IEC_pessoa = data.IEC_pessoas[0];
            html = '<div class="card z-depth-0 bordered" style="border: 1px solid rgba(0, 0, 0, .125)">';
            html += '    <div class="accordion-header w-100">';
            html += '        <div class="row opacity-hover">';
            html += '            <div class="col" ';
            html += ' onclick="abrir_iec(' + IEC_pessoa.id + ',' + id_paciente + ');"> ';
            html += '                <button class="btn btn-link" type="button" data-toggle="collapse"';
            html += '                    data-target="#evolucao-' + IEC_pessoa.id + '" aria-expanded="true" aria-controls="collapse"style="display: flex;">';
            html += '<span class="descr_iec"'
            html += ' style="margin-top: 3px;">Ver último</span>';
            html += '                </button> ';
            html += '            </div>';
            html += '            <div class="col-4 d-flex text-right"> ';
            html += '                <button class="btn-data btn btn-link ml-auto" type="button" data-toggle="collapse"';
            html += '                    data-target="#evolucao-' + IEC_pessoa.id + '" aria-expanded="true" aria-controls="collapse">';
            html += moment(IEC_pessoa.created_at).format('DD/MM/YYYY');
            html += ' às ';
            html += IEC_pessoa.created_at.substring(11, 16);
            html += '                </button> ';
            html += '                <div class="acoes-iec my-auto mx-4">';
            if (IEC_pessoa.destacar == "N") html += '                    <i class="my-icon far fa-star" onclick="favoritar_IEC(' + IEC_pessoa.id + ',' + IEC_pessoa.id_paciente + ',this)"></i>';
            else html += '                    <i class="my-icon fas fa-star" onclick="favoritar_IEC(' + IEC_pessoa.id + ',' + IEC_pessoa.id_paciente + ',this)"></i>';

            html += '                    <i class="my-icon far fa-print" onclick="imprimir_IEC(' + IEC_pessoa.id + ')"></i>';
            html += '                    <i class="my-icon far fa-trash-alt" onclick="deletar_IEC(' + IEC_pessoa.id + ')"></i>';
            html += '                </div>';
            html += '            </div>';
            html += '        </div>';
            html += '    </div>';
            html += '</div>';
            $('#table-historico-IEC-pessoa').append(html);
            req = new Array();
            for (z in data.piores) {
                req.push({
                    x : parseInt(z.substring(1)) - 1,
                    y : data.piores[z] - 1
                });
            }
            if (req.length > 1) {
                req.reverse();
                req = JSON.stringify(req);
                $('#table-historico-IEC-pessoa').append("<iframe src = '/saude-beta/IEC/grafico-hist/" + req + "' style = 'border-width:0;width:100%;height:430px'></iframe>");
            }
            $('#historicoIECModalLabel').html("HISTÓRICO QUESTIONÁRIO IEC:&nbsp;<i>" + IEC_pessoa.descr_iec + "</i>");
        }
    );
    $("#historicoIECModal").modal('show')
    document.querySelector('.modal-dialog.modal-lg').className = 'modal-dialog modal-xl'
}


function resumo_por_pessoa(id_pessoa) {
    var html = '';
    $.get('/saude-beta/pessoa/resumo-pessoa/' + id_pessoa,
        function (data) {
            console.log(data);
            data = $.parseJSON(data);

            $('#table-prontuario-resumo').empty();
            data.forEach(resumo => {
                html = '<li class="evolucao-li" data-table="' + resumo.tabela + '" data-responsavel="' + resumo.responsavel + '" ';
                if (resumo.prioritario) html += 'class="resumo-prioritario"';
                html += '>';
                if (resumo.tabela == 'anexos') {
                    html += ' <a class="anexos-titulo-resumo" style="font-size:22px; font-weight:600; line-height:1" ';
                    html += '     href="/saude-beta/anexos/baixar/' + resumo.id + '"> ';
                    if (resumo.titulo != null) html += resumo.titulo;
                    else html += 'Documento'
                    html += '     <i class="my-icon fas fa-file-download"></i>';
                    html += ' </a>';
                } else {
                    html += ' <a class="resumo-titulo" style="font-size:22px; font-weight:600; line-height:1" href="#">';
                    if (resumo.tabela == 'agenda' && resumo.titulo == null) html += 'Agendamento'
                    else html += resumo.titulo
                    html += ' </a>';
                }
                html += ' <p> ';
                if (resumo.tabela == 'evolucao') html += 'Evolução Avulsa feita ';
                else if (resumo.tabela == 'evolucao_pedido') html += 'Último evolução desse procedimento feita ';
                else if (resumo.tabela == 'evolucao_pedido_finalizada') html += 'procedimento finalizado ';
                else {
                    html += 'Feito '
                }
                if (resumo.responsavel != '' && resumo.responsavel != null) {
                    var resp_final = resumo.responsavel.split(" ");
                    if (resp_final[0] == resp_final[1]) resp_final = resp_final[0];
                    else resp_final = resumo.responsavel;
                    html += ' por ' + resp_final;
                }
                if (moment(resumo.data).format('DD/MM/YYYY') == moment().format('DD/MM/YYYY')) {
                    html += ' hoje às ' + resumo.hora.substring(0, 5);
                } else {
                    html += ' na ' + moment(resumo.data.substring(0, 10) + ' ' + resumo.hora, 'YYYY-MM-DD HH:mm:ss').format('LLLL');
                }
                html += '    </p>';
                console.log(resumo.descricao);
                if (resumo.descricao != '' && resumo.descricao != null) html += '    <p>' + resumo.descricao + '</p>';
                html += '</li>';
                $('#table-prontuario-resumo').append(html);
            });
        }
    );
}


function atualizarAtendimento($obj) {
    html = '<option value="0">Selecionar tipo de contato...</option><option value="1">Confirmar Presença</option><option value="2">Associado Ausente</option><option value="3">Finalizado</option>'
    $('#ConfirmacaoModal #id_contato').empty()
    $('#ConfirmacaoModal #id_contato').append(html)
    $('#ConfirmacaoModal #id_agendamento').val($('#agendaMobileModal #id_agendamento').val())
    $('#ConfirmacaoModal').modal('show')
}


function orcamentos_por_pessoa(id_pessoa) {
    $.get(
        '/saude-beta/orcamento/listar-pessoa/' + id_pessoa,
        function (data) {
            data = $.parseJSON(data);
            $('#table-prontuario-orcamento > tbody').empty();
            data.forEach(orcamento => {
                html = '<tr>';
                html += '    <td width="8%" class="text-center">';
                html += orcamento.num_pedido.toString().padStart(6, '0').slice(-6);
                html += '    </td>';
                html += '    <td width="10%">'
                html += orcamento.descr_prof_exa;
                html += '    </td>';
                html += '    <td width="13%">'
                html += orcamento.descr_convenio;
                html += '    </td>';
                html += '    <td width="10%" class="text-right">'
                html += 'R$ ' + orcamento.valor.toString().replace('.', ',');
                html += '    </td>';
                html += '    <td width="10%" class="text-right">'
                html += 'R$ ' + orcamento.aprazo.toString().replace('.', ',');
                html += '    </td>';
                // html += '    <td width="25%">';
                // html += moment(orcamento.created_at).format('DD/MM/YYYY') + ' por ' + orcamento.created_by;
                // html += '    </td>';
                html += '    <td width="10%">'
                html += moment(orcamento.data_validade).format('DD/MM/YYYY');
                html += '    </td>';
                html += '    <td width="15%" class="text-center" style="font-size:0.75rem">';
                if (orcamento.status == 'F') html += '<div class="tag-pedido-finalizado">Aprovado</div>';
                else if (orcamento.status == 'P') html += '<div class="tag-pedido-primary">Aprovação Parcialmente</div>';
                else if (orcamento.status == 'E') html += '<div class="tag-pedido-aberto">Aprovação do Paciente</div>';
                else if (orcamento.status == 'A') html += '<div class="tag-pedido-primary">Em Edição</div>';
                else html += '<div class="tag-pedido-cancelado">Cancelado</div>';
                html += '    </td>';
                html += '    <td width="20%" class="text-right btn-table-action">';
                if (orcamento.status != 'C') {
                    if (orcamento.status == 'E' || orcamento.status == 'P') {
                        html += '<i class="my-icon far fa-user-check" title="Converter proposta para Plano de Tratamento" onclick="converter_orcamento(' + orcamento.id + ')"></i>';
                    } else if (orcamento.status == 'A') {
                        html += '<i class="my-icon far fa-user-clock" title="Enviar para aprovação" onclick="mudar_status_orcamento(' + orcamento.id + ",'E'" + ')"></i>';
                    }
                    html += '    <i class="my-icon far fa-edit" title="Editar" onclick="editar_orcamento(' + orcamento.id + ')"></i>';
                    html += '    <i class="my-icon far fa-file-times" title="Cancelar" onclick="mudar_status_orcamento(' + orcamento.id + ",'C'" + ')"></i>';
                    html += '    <i class="my-icon far fa-print" title="Imprimir" onclick="new_system_window(' + "'orcamento/imprimir/" + orcamento.id + "'" + ')"></i>';
                }
                html += '        <i class="my-icon far fa-trash-alt" title="Deletar" onclick="deletar_orcamento(' + orcamento.id + ')"></i>';
                html += '    </td>';
                html += '</tr>';
                $('#table-prontuario-orcamento > tbody').append(html);
            });
            $('[data-id="#prt-orcamento"] .qtde-prontuario')
                .data('count', data.length)
                .attr('data-count', data.length)
                .find('small')
                .html(data.length);
        }
    );
}


function contratos_por_pessoa(id_pessoa) {
    $.get(
        '/saude-beta/contratos/listar-pessoa/' + id_pessoa,
        function (data) {
            data = $.parseJSON(data);

            $('#table-prontuario-procedimentos-aprovados > tbody').empty();
            data.forEach(contrato => {

                html = '<tr>';

                html += '    <td width="10%" class="text-center">';
                html += contrato.id.toString().padStart(6, '0').slice(-6);
                html += '    </td>';

                html += '    <td width="20%">'
                html += contrato.descr_paciente;
                html += '    </td>';

                html += '    <td width="35%">'
                html += contrato.Responsavel;
                html += '    </td>';

                html += '<td width="10%" style="font-size:0.75rem">'
                if (contrato.Situacao == 'F') html += '<div class="tag-pedido-finalizado text-right">Em Execução'
                else if (contrato.Situacao == 'E') html += '<div class="tag-pedido-aberto text-right">Aprovação do Paciente'
                else if (contrato.Situacao == 'A') html += '<div class="tag-pedido-primary text-right">Em Edição'
                else html += '<div class="tag-pedido-cancelado">Cancelado'
                html += '</div> </td>'
                html += '</td>'
                // html += '<td width="35%"> </td>'

                html += '    <td width="10%" class="text-right">';
                html += 'R$ ' + contrato.Valor_contrato.toString().replace('.', ',');
                html += '    </td>';

                html += '    <td width="10%" class="text-right">'
                html += moment(contrato.Data_final).format('DD/MM/YYYY');
                html += '    </td>';
                html += '</tr>';
                // html += '    <td width="15%" style="font-size:0.75rem">';




                $('#table-prontuario-procedimentos-aprovados > tbody').append(html);
            });
            $('[data-id="#prt-procedimentos-aprovados"] .qtde-prontuario')
                .data('count', data.length)
                .attr('data-count', data.length)
                .find('small')
                .html(data.length);
        }
    );
}

// function contratos_por_pessoa(id_pessoa) {
//     $.get(
//         '/saude-beta/contratos/listar-pessoa/' + id_pessoa,
//         function (data) {
//             data = $.parseJSON(data);

//             $('#table-prontuario-procedimentos-aprovados > tbody').empty();
//             data.forEach(contrato => {
//                 let status

//                 html = '<tr>';

//                 html += '    <td width="10%" class="text-center">';
//                 html += contrato.id.toString().padStart(6, '0').slice(-6);
//                 html += '    </td>';

//                 html += '    <td width="20%">'
//                 html += contrato.descr_paciente;
//                 html += '    </td>';

//                 html += '    <td width="15%">'
//                 html += contrato.Responsavel;
//                 html += '    </td>';

//                 html += '<td width="35%"> </td>'

//                 html += '    <td width="10%" class="text-right">';
//                 html += 'R$ ' + contrato.Valor_contrato.toString().replace('.', ',');
//                 html += '    </td>';

//                 html += '<td width="10%" style="font-size:0.75rem">'

//                 if (contrato.Situacao == 'F'){
//                     html += '<div class="tag-pedido-finalizado">Em Execução</div>'
//                 }
//                 else if (contrato.Situacao == 'E'){
//                     html += '<div class="tag-pedido-aberto">Aprovação do Paciente </div>'
//                 }
//                 else if (contrato.Situacao == 'A'){
//                     html += '<div class="tag-pedido-primary">Em Edição </div>'
//                 }
//                 else{
//                     html =+ '<div class="tag-pedido-cancelado">Cancelado</div>'
//                 }   
//                 html += '</td>'          

//                 html += '    <td width="10%" class="text-right">'
//                 html += moment(contrato.Data_final).format('DD/MM/YYYY');
//                 html += '    </td>';
//                 html += '</tr>';
//                 // html += '    <td width="15%" style="font-size:0.75rem">';




//                 $('#table-prontuario-procedimentos-aprovados > tbody').append(html);
//             });
//             $('[data-id="#prt-procedimentos-aprovados"] .qtde-prontuario')
//                 .data('count', data.length)
//                 .attr('data-count', data.length)
//                 .find('small')
//                 .html(data.length);
//         }
//     );
// }

function abrir_evolucoes_pedido(_id_pedido) {
    var html = '',
        total, total_procedimento = 0.0;
    $.get('/saude-beta/pedido/mostrar/' + _id_pedido,
        function (data) {
            data = $.parseJSON(data);
            console.log(data);

            $('#pedidoEvolucaoModalLabel').html(
                'Contrato | Nº #' + pad(data.pedido.num_pedido, 6) + ' - ' + data.pedido.descr_prof_exa
            );
            $('#pedidoEvolucaoModal #pedido_evolucao_profissional_exa_id').val(data.pedido.id_prof_exa);
            $('#pedidoEvolucaoModal #pedido_evolucao_profissional_exa_nome').val(data.pedido.descr_prof_exa);
            $('#pedidoEvolucaoModal #pedido_evolucao_validade').val(moment(data.pedido.validade).format('DD/MM/YYYY'));
            $('#pedidoEvolucaoModal #pedido_evolucao_obs').val(data.pedido.obs);
            html = '<option value="0">Selecionar Convênio...</option>';
            data.convenio_paciente.forEach(convenio => {
                html += '<option value="' + convenio.id + '">';
                html += convenio.descr;
                html += '</option>';
            });
            $('#pedidoEvolucaoModal #pedido_evolucao_id_convenio').html(html);
            $('#pedidoEvolucaoModal #pedido_evolucao_id_convenio').val(data.pedido.id_convenio);

            $('#pedidoEvolucaoModal #table-pedido-evolucao-procedimentos > tbody').empty();
            data.ped_procedimentos.forEach(function (procedimento, index) {
                index++;
                html = '<tr row_number="' + index + '">';
                html += '    <td width="25%" data-procedimento_id="' + procedimento.id_procedimento + '" data-procedimento_obs="' + procedimento.obs + '">';
                html += procedimento.descr_procedimento;
                if (procedimento.obs != null && procedimento.obs != '') html += ' (' + procedimento.obs + ')';
                html += '    </td>';
                html += '    <td width="25%" data-profissional_exe_id="' + procedimento.id_prof_exe + '">' + procedimento.descr_prof_exe + '</td>';
                if (procedimento.dente_regiao != null) html += '<td width="10%" class="text-right" data-dente_regiao="' + procedimento.dente_regiao + '">' + procedimento.dente_regiao + '</td>';
                else html += '<td width="10%" class="text-right" data-dente_regiao=""></td>';
                if (procedimento.face != null) html += '<td width="10%" class="text-right" data-dente_face="' + procedimento.face + '">' + procedimento.face + '</td>';
                else html += '<td width="10%" class="text-right" data-dente_face=""></td>';
                html += '    <td width="15%" class="text-right" data-valor="' + procedimento.valor + '">' + procedimento.valor.toString().replace('.', ',') + '</td>';

                html += '    <td width="15%"  class="text-center btn-table-action">';
                if (procedimento.status == 'F') {
                    html += ' <i class="my-icon far fa-check"></i>';
                } else if (procedimento.status == 'C') {
                    html += ' <i class="my-icon far fa-times"></i>';
                } else {
                    // html += ' <i class="my-icon far fa-clipboard-check" onclick="finalizar_pedido_evolucao(' + procedimento.id + ')"></i>';
                    // html += ' <i class="my-icon far fa-file-times"      onclick="cancelar_pedido_evolucao(' + procedimento.id + ')"></i>';
                }
                html += '    </td>';
                html += '</tr>';
                total_procedimento = (total_procedimento + procedimento.valor);
                $('#pedidoEvolucaoModal #table-pedido-evolucao-procedimentos > tbody').append(html);
            });
            $('[data-table="#table-pedido-evolucao-procedimentos"] [data-total]')
                .data('total', total_procedimento)
                .attr('data-total', total_procedimento)
                .html('R$ ' + parseFloat(total_procedimento).toFixed(2).toString().replace('.', ','));

            $('#pedidoEvolucaoModal #table-pedido-evolucao-forma-pag > tbody').empty();
            data.ped_formas_pag.forEach(function (forma_pag, index) {
                index++;
                html = '<tr row_number="' + index + '">';
                html += '    <td width="27.5%" data-forma_pag="' + forma_pag.id + '">';
                html += forma_pag.descr_forma_pag;
                html += '    </td>';
                html += '    <td width="27.5%" data-financeira_id="' + forma_pag.id_financeira + '">';
                if (forma_pag.id_financeira != 0) html += forma_pag.descr_financeira;
                else html += '...';
                html += '    </td>';
                html += '    <td width="15%" data-forma_pag_parcela="' + forma_pag.num_total_parcela + '"  class="text-right">';
                html += forma_pag.num_total_parcela + 'x de R$ ' + (forma_pag.valor_total / forma_pag.num_total_parcela).toFixed(2).toString().replace('.', ',');
                html += '    </td>';
                html += '    <td width="15%" data-forma_pag_valor="' + parseFloat(forma_pag.valor_total).toFixed(2).toString().replace('.', ',') + '"  class="text-right">';
                html += '       R$ ' + parseFloat(forma_pag.valor_total).toFixed(2).toString().replace('.', ',');
                html += '    </td>';
                html += '    <td width="15%" data-pedido_data_vencimento="' + moment(forma_pag.created_at).format('DD/MM/YYYY') + '">';
                html += moment(forma_pag.created_at).format('DD/MM/YYYY');
                html += '    </td>';
                html += '</tr>';
                $('#pedidoEvolucaoModal #table-pedido-evolucao-forma-pag > tbody').append(html);
            });
            $('#pedidoEvolucaoModal').modal('show');
            listar_planos_pedido(_id_pedido);
        }
    );
}
function listar_planos_pedido(_id_pedido) {
    $.get('/saude-beta/pedido/listar-planos-pessoa/' + _id_pedido, function (data) {
        data = $.parseJSON(data);
        console.log(data)
        a = data
        $("#profissional_exe_nome").val('');
        $('#id_plano').val(0);
        html = '<tr style="background: white !important" id="linha' + $('#tabela-planos > tbody > tr').length + '">'
        html += '    <th width=<th width="45%" data-plano_id="' + data[0].id + '">' + data[0].descr + '</th>'
        html += '    <th width="25%" data-profissional_id="' + data[0].profissional_id + '">' + data[0].profissional + '</th>'
        html += '    <th width="12.5%" class="text-right">' + data[0].n_pessoas + '</th>'
        html += '    <th id="valor_plano" width="12.5%" class="text-right">' + data[0].valor + '</th>'
        html += '    <th width="5%"  class="text-center">'
        html += '.'
        html += '    </th>'
        html += '</tr>'
        $('#tabela-planos > tbody').append(html);

        atualizarValorTotal()
    });
}
function add_pedido_evolucao(id_pedido_servicos, bEdit) {
    var html = '';
    $('#form-salvar-evolucao-pedido #id_pedido_servicos').val(id_pedido_servicos);
    $('#form-salvar-evolucao-pedido #data').val(moment().format('DD/MM/YYYY'));
    $('#form-salvar-evolucao-pedido #hora').val(moment().format('HH:mm'));
    $('#form-salvar-evolucao-pedido #diagnostico').summernote('code', '');
    $('#evolucaoPedidoModal').find('.modal-dialog').removeClass('modal-lg').removeClass('modal-xl');
    if (bEdit) {
        $('#form-salvar-evolucao-pedido').parent().removeClass('d-none');
        $('#evolucaoPedidoModal').find('.modal-dialog').addClass('modal-xl');
    } else {
        $('#form-salvar-evolucao-pedido').parent().addClass('d-none');
        $('#evolucaoPedidoModal').find('.modal-dialog').addClass('modal-lg');
    }
    $.get('/saude-beta/evolucao-pedido/listar/' + id_pedido_servicos, function (data) {
        data = $.parseJSON(data);
        $('#lista-evolucao-servicos').empty();
        data.forEach(evolucao_pedido => {
            html = '<li data-table="evolucao" class="resumo-prioritario"> ';
            html += '    <a style="font-size:20px; font-weight:600; line-height:1" href="#">';
            html += moment(evolucao_pedido.data).format('DD/MM/YYYY') + ' às ' + moment(evolucao_pedido.data + ' ' + evolucao_pedido.hora).format('HH:mm');
            html += '    </a> ';
            // html += '    <a href="#" class="float-right">'
            // html += '       <small>' + moment(evolucao_pedido.data).format('DD/MM/YYYY') + ' às ' + moment(evolucao_pedido.data + ' ' + evolucao_pedido.hora).format('HH:mm') + '</small>';
            // html += '    </a>';
            html += '    <p>' + evolucao_pedido.diagnostico.replaceAll('<p></p>', '') + '</p>';
            html += '    <p class="d-flex">';
            html += '       <small><b>Feito por ' + evolucao_pedido.descr_profissional + '.</b></small>';
            if (bEdit) {
                html += '   <a class="ml-auto" href="#" style="color:#858796">';
                html += '       <i class="my-icon fal fa-trash-alt" onclick="deletar_evolucao_pedido(' + evolucao_pedido.id + ')"></i>';
                html += '   </a>';
            }
            html += '    </p>';
            html += '</li>';
            $('#lista-evolucao-servicos').append(html);
        });
        if (!$('#evolucaoPedidoModal').hasClass('show')) $('#evolucaoPedidoModal').modal('show');

    });
}

function salvar_evolucao_pedido(e) {
    e.preventDefault();
    $.post(
        '/saude-beta/evolucao-pedido/salvar',
        $('#form-salvar-evolucao-pedido').serialize() + "&id_paciente=" + $('#id_pessoa_prontuario').val(),
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                proc_aprovados_por_pessoa($('#id_pessoa_prontuario').val());
                add_pedido_evolucao($('#form-salvar-evolucao-pedido #id_pedido_servicos').val(), true);
            }
        }
    );
}

function deletar_evolucao_pedido(id_evolucao_pedido) {
    if (window.confirm("Deseja realmente excluir essa evolução?")) {
        $.post(
            '/saude-beta/evolucao-pedido/deletar', {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: id_evolucao_pedido
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {
                    proc_aprovados_por_pessoa($('#id_pessoa_prontuario').val());
                    add_pedido_evolucao($('#form-salvar-evolucao-pedido #id_pedido_servicos').val(), true);
                }
            }
        );
    }
}

function finalizar_procedimento_aprovado(id_pedido_servicos) {
    $.get('/saude-beta/pedido-servicos/mostrar/' + id_pedido_servicos, function (data) {
        data = $.parseJSON(data);
        $('#finalizarPedidoServicosModal #id_pedido_servicos').val(id_pedido_servicos);
        $('#finalizarPedidoServicosModal #id_profissional').val(data.id_prof_exe);
        $('#finalizarPedidoServicosModal #descr_profissional').val(data.descr_prof_exe);
        $('#finalizarPedidoServicosModal #data').val(moment().format('DD/MM/YYYY'));
        $('#finalizarPedidoServicosModal #hora').val(moment().format('HH:mm'));
        $('#finalizarPedidoServicosModal').modal('show');
        $('#finalizarPedidoServicosModal #form-finalizar-pedido-servicos').submit(function (e) {
            e.preventDefault();
            $.post(
                '/saude-beta/pedido-servicos/finalizar',
                $(this).serialize(),
                function (data, status) {
                    console.log(status + " | " + data);
                    if (data.error != undefined) {
                        alert(data.error);
                    } else {
                        proc_aprovados_por_pessoa($('#id_pessoa_prontuario').val());
                        $('#finalizarPedidoServicosModal').modal('hide');
                    }
                }
            );
        });
    });
}

function cancelar_procedimento_aprovado(id_evolucao_pedido) {
    if (window.confirm("Deseja realmente cancelar essa evolução?")) {
        $.post(
            '/saude-beta/pedido-servicos/cancelar', {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: id_evolucao_pedido
        },
            function (data, status) {
                console.log(status + " | " + data);
                if (data.error != undefined) {
                    alert(data.error);
                } else {
                    proc_aprovados_por_pessoa($('#id_pessoa_prontuario').val())
                }
            }
        );
    }
}

function resumir_filtro() {
    var toShow = $('#resumo-filtro input:checked'),
        toHide = $('#resumo-filtro input:not(:checked)'),
        responsavel = $('#resumo-filtro-profissional').val(),
        selectorShow = '',
        selectorHide = '',
        _e_split;
    i = 0;

    toShow.each(function (index, element) {
        array_filtro_split = $(this).data().filtro.split(';');
        for (i = 0; i < array_filtro_split.length; i++) {
            selectorShow += '.timeline [data-table="' + array_filtro_split[i] + '"]';
            if ((i + 1) != array_filtro_split.length) selectorShow += ',';
        }
        if (index != (toShow.length - 1)) selectorShow += ',';
    });

    if (responsavel == '') {
        $(selectorShow).show();
    } else {
        $(selectorShow).each(function () {
            if ($(this).data().responsavel.toUpperCase().normalize('NFD').replace(/[\u0300-\u036f]/g, "")
                .includes(responsavel.toUpperCase())) $(this).show();
            else $(this).hide();
        });
    }

    toHide.each(function (index, element) {
        array_filtro_split = $(this).data().filtro.split(';');
        for (i = 0; i < array_filtro_split.length; i++) {
            selectorHide += '.timeline [data-table="' + array_filtro_split[i] + '"]';
            if ((i + 1) != array_filtro_split.length) selectorHide += ',';
        }
        if (index != (toHide.length - 1)) selectorHide += ',';
    });
    $(selectorHide).hide();
}

function pesquisar_procedimentos_aprovados() {
    var _filtro = $("#pesquisa-procedimentos-aprovados").val();
    $("#table-prontuario-procedimentos-aprovados tbody > tr:contains('" + _filtro + "')").show("fast");
    $("#table-prontuario-procedimentos-aprovados tbody > tr:not(:contains('" + _filtro + "'))").hide("fast");
}

function informarStatusAgenda(status) {
    switch (status) {
        case 'travar':
            alert("Status só poderá ser escolhido no momento da criação do agendamento")
            break;
        case 'reagendado':
            alert("Define status como padrão para compromissos remarcados (Todos os agendamentos remarcados receberão esse status)")
    }

}


$(document).ready(function () {
    $('.money2').mask('R$ 000.000.000.000.000.00', { reverse: true });
});

// ————————————————————————————————————————————————————————————————————————————————————— //
// ————————————————————————————————————————————————————————————————————————————————————— //
// ————————————————————————————————————— PRONTUÁRIO ———————————————————————————————————— //
// ————————————————————————————————————————————————————————————————————————————————————— //
// ————————————————————————————————————————————————————————————————————————————————————— //

var partesDoCorpo = document.querySelectorAll(".partes > div")
var janela = document.querySelector("#info-corpo")
partesDoCorpo.forEach(element => {
    element.addEventListener('mouseover', () => {
        janela.style.display = "block"
    })
    element.addEventListener('mouseout', () => {
        janela.style.display = "none"
    })
})

var veri = 0;

function recolher_menu() {
    var menu = document.getElementById('menu-hidde');
    if (veri == 1) {
        menu.style.position = "inherit"
        menu.style.left = "0px";

        document.getElementById("menu-trigger").style.left = "331px"
        document.getElementById("info-corpo").style.right = "-500px"
        document.getElementById("info-corpo").style.width = "510px"
        document.getElementById("info-title").style.fontSize = "29px"

        document.querySelector("#menu-trigger > img").src = 'http://vps.targetclient.com.br/saude-beta/img/seta-esquerda.png'

        veri = 0;
    } else {
        menu.style.left = "-100%";
        menu.style.position = "absolute"

        document.getElementById("menu-trigger").style.left = "0px"
        document.getElementById("info-corpo").style.right = "-670px"
        document.getElementById("info-corpo").style.width = "665px"
        document.getElementById("info-title").style.fontSize = "35px"

        document.querySelector("#menu-trigger > img").src = 'http://vps.targetclient.com.br/saude-beta/img/seta-direita.png'

        veri = 1;
    }
}
function adicionarTitulo(id_corpo) {
    titulo = document.querySelector("#info-title");
    registros = document.querySelector("#info-header > p")
    switch (id_corpo) {
        case 1:
            titulo.innerHTML = "Mão direita em movimento"
            break;
        case 2:
            titulo.innerHTML = "Mão direita parada"
            break;
        case 3:
            titulo.innerHTML = "Mão esquerda parada"
            break;
        case 4:
            titulo.innerHTML = "Mão esquerda em movimento"
            break;
        case 5:
            titulo.innerHTML = "Cotovelo direito em movimento"
            break;
        case 6:
            titulo.innerHTML = "Cotovelo direito parado"
            break;
        case 7:
            titulo.innerHTML = "Cotovelo esquerdo em movimento"
            break;
        case 8:
            titulo.innerHTML = "Cotovelo esquerdo parado"
            break;
        case 9:
            titulo.innerHTML = "Joelho direito em movimento"
            break;
        case 10:
            titulo.innerHTML = "Joelho direito parado"
            break;
        case 11:
            titulo.innerHTML = "Joelho esquerdo em movimento"
            break;
        case 12:
            titulo.innerHTML = "Joelho esquerdo parado"
            break;
        case 13:
            titulo.innerHTML = "Pé direito em movimento"
            break;
        case 14:
            titulo.innerHTML = "Pé direito parado"
            break;
        case 15:
            titulo.innerHTML = "Pé esquerdo em movimento"
            break;
        case 16:
            titulo.innerHTML = "Pé esquerdo parado"
            break;
        case 17:
            titulo.innerHTML = 'Cardio-pulmonar'
            break;
        case 18:
            titulo.innerHTML = 'Coluna'
            break;
        case 19:
            titulo.innerHTML = 'Abdômen'
            break;
        case 20:
            titulo.innerHTML = "Quadril"
            break;
        case 21:
            titulo.innerHTML = "Ombro direito"
            break;
        case 22:
            titulo.innerHTML = "Ombro esquerdo"
            break;
        case 23:
            titulo.innerHTML = "Cabeça"
            break;
    }
}

function resumo_vitruviano(id_pessoa, id_corpo) {
    var html = '';
    $.get('/saude-beta/pessoa/resumo-vitruviano/' + $('#id_pessoa_prontuario').val() + '/' + id_corpo,
        function (data) {
            data = $.parseJSON(data);

            $('#info-vitruviano').empty();
            data.forEach(resumo => {
                html = '<li data-table="' + resumo.descr + '" data-responsavel="' + resumo.responsavel + '" ';
                if (resumo.prioritario) html += 'class="resumo-prioritario"';
                html += '>';
                html += ' <a class="resume-link" href="#">';
                html += resumo.descr;
                html += ' </a>';
                html += ' <p> ';
                html += 'Evolução Avulsa feita ';

                if (resumo.responsavel != '') html += ' por ' + resumo.responsavel;
                if (moment(resumo.data).format('DD/MM/YYYY') == moment().format('DD/MM/YYYY')) {
                    html += ' Hoje às ' + resumo.hora.substring(0, 5);
                } else {
                    html += ' na ' + captalize(moment(resumo.data.substring(0, 10) + ' ' + resumo.hora, 'YYYY-MM-DD HH:mm:ss').format('LLLL'));
                }
                html += '    </p>';
                html += '    <p>' + resumo.descricao + '</p>';
                html += '</li>';

            });
        }
    );
    adicionarTitulo(id_corpo)
}

function resumo_vitruviano_modal(id_corpo) {
    var html = '';
    $.get('/saude-beta/pessoa/resumo-vitruviano/' + $('#id_pessoa_prontuario').val() + '/' + id_corpo,
        function (data) {
            data = $.parseJSON(data);
            $('#resumoVitruvianoModal .row').empty();
            data.forEach(resumo => {
                html = '<li class="text-resume" style = "background:#f2f2f2;margin:auto;margin-top:20px;padding:20px;border-radius:0.25rem;width:90%" data-table="' + resumo.descr + '" data-responsavel="' + resumo.responsavel + '" ';
                if (resumo.prioritario) html += 'class="resumo-prioritario"';
                html += '>';
                html += ' <a style = "font-size:22px;font-weight:600;line-height:1;color:#dc3545">';
                html += resumo.descr;
                html += ' </a>';
                html += ' <p> ';
                html += 'Evolução Avulsa feita ';

                if (resumo.responsavel != '') html += ' por ' + resumo.responsavel;
                if (moment(resumo.data).format('DD/MM/YYYY') == moment().format('DD/MM/YYYY')) {
                    html += ' Hoje às ' + resumo.hora.substring(0, 5);
                } else {
                    html += ' na ' + captalize(moment(resumo.data.substring(0, 10) + ' ' + resumo.hora, 'YYYY-MM-DD HH:mm:ss').format('LLLL'));
                }
                html += '    </p>';
                html += '    <p class="resume">' + resumo.descricao + '</p>';
                html += '</li>';
                $('#resumoVitruvianoModal .row').append(html);
            });
            if (html != "") {
                adicionarTituloResume(id_corpo);
                $('#resumoVitruvianoModal').modal('show');
                $("#criarVitruviano").modal("hide");
            }
        }
    );
}

function retornaCorpo() {
    $('#resumoVitruvianoModal').modal('hide');
    $("#criarVitruviano").modal("show");
}

function adicionarTituloResume(id_corpo) {
    titulo = document.querySelector("#resume-title");
    switch (id_corpo) {
        case 1:
            titulo.innerHTML = "Mão direita em movimento"
            break;
        case 2:
            titulo.innerHTML = "Mão direita parada"
            break;
        case 3:
            titulo.innerHTML = "Mão esquerda parada"
            break;
        case 4:
            titulo.innerHTML = "Mão esquerda em movimento"
            break;
        case 5:
            titulo.innerHTML = "Cotovelo direito em movimento"
            break;
        case 6:
            titulo.innerHTML = "Cotovelo direito parado"
            break;
        case 7:
            titulo.innerHTML = "Cotovelo esquerdo em movimento"
            break;
        case 8:
            titulo.innerHTML = "Cotovelo esquerdo parado"
            break;
        case 9:
            titulo.innerHTML = "Joelho direito em movimento"
            break;
        case 10:
            titulo.innerHTML = "Joelho direito parado"
            break;
        case 11:
            titulo.innerHTML = "Joelho esquerdo em movimento"
            break;
        case 12:
            titulo.innerHTML = "Joelho esquerdo parado"
            break;
        case 13:
            titulo.innerHTML = "Pé direito em movimento"
            break;
        case 14:
            titulo.innerHTML = "Pé direito parado"
            break;
        case 15:
            titulo.innerHTML = "Pé esquerdo em movimento"
            break;
        case 16:
            titulo.innerHTML = "Pé esquerdo parado"
            break;
        case 17:
            titulo.innerHTML = 'Cardio-pulmonar'
            break;
        case 18:
            titulo.innerHTML = 'Coluna'
            break;
        case 19:
            titulo.innerHTML = 'Abdômen'
            break;
        case 20:
            titulo.innerHTML = "Quadril"
            break;
        case 21:
            titulo.innerHTML = "Ombro direito"
            break;
        case 22:
            titulo.innerHTML = "Ombro esquerdo"
            break;
        case 23:
            titulo.innerHTML = "Cabeça"
            break;
    }
}
window.addEventListener('load', () => {
    if (detectar_mobile()) {
        $('#carrossel').slick({
            dots: false,
            infinite: false,
            speed: 500,
            slidesToShow: 1,
            slidesToScroll: 1,
        });
    }
    else {
        $('#carrossel').slick({
            dots: false,
            infinite: false,
            speed: 500,
            slidesToShow: 3,
            slidesToScroll: 1,
        });

    }
})



// ————————————————————————————————————————————————————————————————————————————————————— //
// ————————————————————————————————————————————————————————————————————————————————————— //
// —————————————————————————————————————- CONTRATOS ———————————————————————————————————— //
// ————————————————————————————————————————————————————————————————————————————————————— //
// ————————————————————————————————————————————————————————————————————————————————————— //
// function resetar_modal_contrato() {
//     $('#contratoModal [data-etapa]').removeClass('selected').removeClass('success');
//     $('#contratoModal [data-etapa="1"]').addClass('selected');
//     $('#contratoModal #voltar-contrato').removeClass('show');
//     $('#contratoModal #voltar-contrato').attr("disabled", true);
//     $('#contratoModal #avancar-contrato').addClass('show');
//     $('#contratoModal #avancar-contrato').attr("disabled", false);
//     $('#contratoModal #avancar-contrato').show();
//     $('#contratoModal #salvar-contrato').hide();
// }


// function criar_contrato() {
//      resetar_modal_contrato();
//     $.get('/saude-beta/contratos/gerar-num', function (data) {
//         $('#contratoModalLabel').html('Contrato | Nº #' + ("000000" + data).slice(-6));
//         $('#contratoModal #contrato_validade').val(moment().add(15, 'days').format('DD/MM/YYYY'));
//         $('#contratoModal #contrato_id').val(0);
//         $('#contratoModal #salvar-contrato').html('Salvar');
//         $('#contratoModal #status-contrato')
//             .html('Novo')
//             .removeAttr('class')
//             .addClass('tag-contrato-primary');

//         $('#contratoModal').modal('show');
//         setTimeout(function () {
//             $("#contratoModal #pedido_paciente_nome").first().focus();
//             $("#contratoModal #pedido_paciente_id").trigger('change');
//         }, 50);
//     });
// }

// function avancar_etapa_wo_contrato() {
//     var etapa_atual = $('.wizard-contrato > .wo-etapa.selected').data().etapa;

//     $('#avancar-contrato').show();
//     $('#salvar-contrato').hide();

//     if (etapa_atual == 1) {
//         if ($('#contratoModal #contrato_paciente_id').val() == '') {
//             alert('Aviso!\nCampo paciente inválido.');
//             return;
//         }
//         // if ($('#pedidoModal #pedido_id_convenio').val() == 0) {
//         //     alert('Aviso!\nA escolha de um convênio é obrigatória.');
//         //     return;
//         // }
//         if ($('#contratoModal #contrato_profissional_exa_id').val() == '') {
//             alert('Aviso!\nCampo profissional examinador inválido.');
//             return;
//         }
//         if ($('#contratoModal #contrato_validade').val() == '' || $('#contrato_validade').val().length != 10) {
//             alert('Aviso!\nCampo validade inválido.');
//             return;
//         }

//     } else if (etapa_atual == 2 && $('#contratoModal #table-contrato-procedimentos tbody tr').length == 0) {
//         alert('Aviso!\nÉ preciso inserir pelo menos um procedimento para prosseguir.');
//         return;
//     } else if (etapa_atual == 2) {
//         alert("Etapa2")
//         ShowConfirmationBox(
//             'Qual será o tipo da forma de pagamento?',
//             '',
//             true, true, false,
//             function () { setar_tipo_forma_pag_contrato('V'); },
//             function () { setar_tipo_forma_pag_contrato('P'); },
//             'À Vista',
//             'À Prazo'
//         );

//         var vista = parseFloat($('[data-table="#table-contrato-procedimentos"] [data-total_vista]').data().total_vista).toFixed(2).toString().replace('.', ','),
//             prazo = parseFloat($('[data-table="#table-contrato-procedimentos"] [data-total_prazo]').data().total_prazo).toFixed(2).toString().replace('.', ',');

//         $('#contratoModal #contrato_forma_pag_tipo').data('preco_vista', vista).attr('data-preco_vista', vista);
//         $('#contratoModal #contrato_forma_pag_tipo').data('preco_prazo', prazo).attr('data-preco_prazo', prazo);
//         $('#pedidoModal #contrato_forma_pag_tipo').trigger('change');
//     } else if (etapa_atual == 3) {
//         alert("estapa 3")
//         $('#avancar-contrato').removeClass('show');
//         $('#avancar-contrato').attr("disabled", true);

//         if ($.inArray($('#status-contrato').text(), ['Finalizado', 'Em Aprovação', 'Cancelado']) == -1) {
//             $('#avancar-contrato').hide();
//             $('#salvar-contrato').show();
//         }
//         montar_resumo_contrato();
//     }
//     $('#contratoModal [data-etapa="' + etapa_atual + '"]').removeClass('selected');
//     $('#contratoModal [data-etapa="' + etapa_atual + '"]').addClass('success');
//     $('#contratoModal [data-etapa="' + (etapa_atual + 1) + '"]').addClass('selected');
//     $('#voltar-contrato').addClass('show');
//     $('#voltar-contrato').attr("disabled", false);

//     setTimeout(function () {
//         $('[data-etapa="' + (etapa_atual + 1) + '"] input').first().focus();
//     }, 50);
// }
// function salvar_contrato() {
//     var id = $('#id').val(),
//         tipo_forma_pag = $('#contrato_forma_pag_tipo').val(),
//         paciente_id = $('#contrato_paciente_id').val(),
//         id_convenio = $('[data-resumo_paciente_convenio]').data().resumo_paciente_convenio,
//         data_validade = $('[data-resumo_validade]').data().resumo_validade,
//         id_profissional_exa = $('[data-resumo_profissional_exa]').data().resumo_profissional_exa,
//         obs = $('[data-resumo_obs]').data().resumo_obs,
//         procedimentos = [],
//         formas_pag = [];
//         alert(paciente_id)
//     if (confirm('Atenção!\nDeseja gerar contrato já finalizado?')) {
//         _status = 'F';
//     } else {
//         _status = 'A';
//     }

//     $('#table-resumo-contrato-procedimentos tbody tr').each(function () {
//         procedimentos.push({
//             // id_exe_profissional: $(this).find('[data-profissional_exe_id]').data().profissional_exe_id,
//             id_procedimento: $(this).find('[data-procedimento_id]').data().procedimento_id,
//             acrescimo: $(this).find('[data-dente_regiao]').data().dente_regiao,
//             taxa_plano: $(this).find('[data-dente_face]').data().dente_face,
//             desconto_perc: String($(this).find('[data-valor]').data().valor).replace(',', '.'),
//             desconto: String($(this).find('[data-valor_prazo]').data().valor_prazo).replace(',', '.'),
//             obs: $(this).find('[data-procedimento_obs]').data().procedimento_obs
//         });
//     });

//     $('#table-contrato-forma-pag-resumo tbody tr').each(function () {
//         formas_pag.push({
//             id_forma_pag: $(this).find('[data-forma_pag]').data().forma_pag,
//             id_financeira: $(this).find('[data-financeira_id]').data().financeira_id,
//             parcela: $(this).find('[data-forma_pag_parcela]').data().forma_pag_parcela,
//             forma_pag_valor: String($(this).find('[data-forma_pag_valor]').data().forma_pag_valor).replace(',', '.'),
//             data_vencimento: $(this).find('[data-contrato_data_vencimento]').data().contrato_data_vencimento
//         });
//     });

//     $.post(
//         '/saude-beta/contratos/salvar', {
//         _token: $("meta[name=csrf-token]").attr("content"),
//         id: id,
//         tipo_forma_pag: tipo_forma_pag,
//         id_paciente: id_paciente,
//         id_convenio: id_convenio,
//         id_profissional_exa: id_profissional_exa,
//         data_validade: data_validade,
//         status: _status,
//         obs: obs,
//         procedimentos: procedimentos,
//         formas_pag: formas_pag
//     },
//         function (data, status) {
//             console.log(status + " | " + data);
//             if (data.error != undefined) {
//                 alert(data.error);
//             } else {
//                 data = $.parseJSON(data);
//                 // if (_status == 'F') new_system_window('pedido/imprimir/' + data.id);
//                 if (window.location.pathname.includes('/pessoa/prontuario')) {
//                     contratos_por_pessoa(id_paciente);
//                 } else {
//                     document.location.reload(true);
//                 }
//             }
//         }
//     );
// }

// function deletar_contrato(id_contrato) {
//     if (window.confirm("Deseja cancelar o Contrato selecionado?")) {
//         $.post(
//             "/saude-beta/contratos/deletar", {
//             _token: $("meta[name=csrf-token]").attr("content"),
//             id: id_contrato
//         },
//             function (data, status) {
//                 console.log(status + " | " + data);
//                 if (data.error != undefined) {
//                     alert(data.error);
//                 } else {
//                     if (window.location.pathname.includes('/pessoa/prontuario')) {
//                         contratos_por_pessoa($('#id_pessoa_prontuario').val());
//                     } else {
//                         document.location.reload(true);
//                     }
//                 }
//             }
//         );
//     }
// }


// function add_contrato_servicos() {
//     let procedimento_descr = $(' #procedimento_descr').val();
//     let profissional =       $(" #profissional_exe_nome").val();    
//     let valor_a_vista =      $(" #valor").val();
//     let valor_a_prazo =      $(" #valor_prazo").val();

//     let html = '';

//     html += '<tr>'
//     html += '   <td width="45%">'
//     html += procedimento_descr  
//     html += '   </td>'

//     html += '   <td width="25%">'
//     html += profissional
//     html += '   </td>'

//     html += '   <td width="12.5%">'
//     html += valor_a_vista
//     html += '   </td>'

//     html += '   <td width="12.5%" class="text-right">'
//     html += valor_a_prazo
//     html += '   </td>'

//     html += '<td width="5% class="text-right"></td>'
//     html += '</tr>'

//     $('#table-contrato-procedimentos > tbody').append(html);
// }


function desativar_anamnese(id) {
    $.post(
        '/saude-beta/anamnese/desativar', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: id,
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                data = $.parseJSON(data);
                alert('Anamnese desativada com sucesso')
                location.reload();
            }
        }
    );
}

function ativar_anamnese(id) {
    $.post(
        '/saude-beta/anamnese/ativar', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: id,
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                data = $.parseJSON(data);
                alert('Anamnese ativada com sucesso')
                location.reload();
            }
        }
    );
}

function listar_modalidade_por_plano(id_tabela_precos) {
    $.get('/saude-beta/tabela-precos/listar_tabela/' + id_tabela_precos, function (data) {
        data = $.parseJSON(data);

        $('#procedimento-nome').val('');
        $('#procedimento-id').val('');
        // $('#valor').val('');
        // $('#valor_prazo').val('');
        // $('#valor_minimo').val('');
        $('#id-tabela-preco').val(id_tabela_precos);
        $('#table-modalidades > tbody').empty();
        data.Modalidades_por_plano.forEach(modalidade => {
            html = '<tr data-preco_id="' + modalidade.id + '" data-id_especialidade="' + modalidade.id_especialidade + '" data-descr_procedimento="' + modalidade.descr + '">';
            html += '    <td width="40%">';
            html += '        <span>' + modalidade.descr + '</span>';
            html += '        <input id="procedimento_nome"';
            html += '            name="procedimento_nome"  ';
            html += '            class="form-control autocomplete" ';
            html += '            placeholder="Digitar Nome do procedimento..."';
            html += '            data-input="#procedimento_id_' + modalidade.id + '"';
            html += '            data-table="procedimento" ';
            html += '            data-column="descr" ';
            html += '            data-filter_col="id_especialidade" ';
            html += '            data-filter="' + modalidade.id_especialidade + '" ';
            html += '            type="text" ';
            html += '            autocomplete="off"';
            html += '            style="display:none"';
            html += '            required>';
            html += '        <input id="procedimento_id_' + modalidade.id + '" name="procedimento_id" type="hidden">';
            html += '    </td>';
            html += '    <td width="20%">';
            html += '        <span>' + modalidade.descr_especialidade + '</span>';
            html += '        <select id="especialidade" name="especialidade[]" class="form-control custom-select" style="display:none">';
            data.especialidades.forEach(especialidade => {
                html += '        <option value="' + especialidade.id + '"';
                if (especialidade.id == modalidade.id_especialidade) html += ' selected ';
                html += '>';
                html += especialidade.descr;
                html += '        </option>';
            });
            html += '        </select>';
            html += '    </td>';

            html += '    <td width="5%" class="text-center btn-table-action">';

            // html += '       <i class="my-icon far fa-edit"      onclick="editar_preco(' + modalidade.id + ')"></i>';
            html += '       <i class="my-icon far fa-trash-alt" onclick="deletar_modalidade(' + modalidade.id + ')"></i>';

            html += '       <i class="my-icon far fa-check"     onclick="salvar_edicao(' + modalidade.id + ')" style="display:none"></i>';
            html += '       <i class="my-icon far fa-times"     onclick="cancelar_edicao(' + modalidade.id + ')" style="display:none"></i>';
            html += '    </td>';
            html += '</tr>';
            $('#table-modalidades > tbody').append(html);
        });

        $('#table-modalidades .autocomplete').each(function () {
            $(this).keyup(function (e) {
                if (!e.ctrlKey && !(e.ctrlKey && e.keyCode == 32) && e.keyCode != 9 && e.keyCode != 13 && e.keyCode != 38 && e.keyCode != 40) {
                    autocomplete($(this));
                }
            });

            $(this).keydown(function (e) {
                // 9 - TAB | 13 - ENTER | 38 = CIMA | 40 = BAIXO
                if (e.keyCode == 9 || e.keyCode == 13 || e.keyCode == 38 || e.keyCode == 40) {
                    if (e.keyCode == 13) e.preventDefault();
                    seta_autocomplete(e.keyCode, $(this));
                }
            });
        });

        // $('#precosModal #filtro-procedimento').keyup(function (e) {
        //     if (e.keyCode == 13) {
        //         filtrar_tabela_precos();
        //     }
        // });

        // $("#table-precos .money").mask("############.00", { reverse: true });

        if (!$('#tabelaPrecosModal').hasClass('show')) $('#tabelaPrecosModal').modal('show');
    });
}
function add_modalidade() {

    $.post(
        '/saude-beta/tabela-precos/salvarModalidade', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_procedimento: $('#procedimento-id').val(),
        id_tabela_preco: $('#id-tabela-preco').val()
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                $("#procedimento_nome-agenda").val('');
                listar_modalidade_por_plano(data.id_tabela_preco);
            }
        }
    );
}
function deletar_modalidade(id_modalidade) {
    $.get("/saude-beta/tabela-precos/mostrarModalidade/" + id_modalidade,
        function (data) {
            console.log(status + " | " + data)
            data = $.parseJSON(data);
            if (window.confirm("Deseja excluir preço de '" + data.descr + "'?")) {
                $.post(
                    "/saude-beta/tabela-precos/deletarModalidade", {
                    _token: $("meta[name=csrf-token]").attr("content"),
                    id: data.id
                },
                    function (data, status) {
                        console.log(status + " | " + data);
                        if (data.error != undefined) {
                            alert(data.error);
                        } else {
                            listar_modalidade_por_plano(data.id_tabela_preco);
                        }
                    }
                );
            }
        });
}

function criar_plano() {
    var lista_empresas = []
    document.querySelectorAll("#tabelaPrecosModal2 #lista-empresa select").forEach(element => {
        lista_empresas.push(element.value)
    });
    $.post(
        "/saude-beta/tabela-precos/salvar", {
        _token: $("meta[name=csrf-token]").attr("content"),
        descr: $('#tabelaPrecosModal2 #descr2').val(),
        status: $('#tabelaPrecosModal2 #status2').val(),
        valor: $('#tabelaPrecosModal2 #valor2').val(),
        vigencia: $('#tabelaPrecosModal2 #vigencia2').val(),
        max_atv_semana: $('#tabelaPrecosModal2 #max_atv_semana2').val(),
        max_atv: $("#tabelaPrecosModal2 #max_atv2").val(),
        repor_som_mes: $('#tabelaPrecosModal2 #repor_som_mes:checked').val(),
        desc_associado: $('#tabelaPrecosModal2 #desc_associado').val().replace(',', '.'),
        npessoas: $("#tabelaPrecosModal2 #npessoas2").val(),
        tipo_agendamento: $("#tabelaPrecosModal2 #tipo_agendamento2").val(),
        empresas: lista_empresas,
    },
        function (data, status) {
            console.log(status + " | " + data);
            $('#id-tabela-preco').val(data.id);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                $('#tabelaPrecosModal2').modal('hide');
                if (!isNaN(data.id)) {
                    setTimeout(() => {
                        editar_tabela_precos(data.id);
                    }, 500)
                }
                else alert("Cadastro incorreto");
            }
        }
    );
}
function abrir_tabela_precos() {
    $('#id').val('')
    $('#descr').val('')
    $('#status').val('A')
    $('#tabelaPrecosModal2').modal('show');
}

function criar_tipo_agendamento() {
    $.post(
        "/saude-beta/tipo-procedimento/salvar", {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: $('#id').val(),
        assossiar_especialidade: $("#assossiar_especialidade").prop('checked'),
        assossiar_contrato: $("#assossiar_contrato").prop('checked'),
        descr: $('#descr').val(),
        tempo_procedimento: $('#tempo-procedimento').val(),
    },
        function (data, status) {
            console.log(status + " | " + data);
            console.log(data.id)
            if (data.error != undefined) {
                alert(data.error);
            }
            else {
                $('#tipoprocedimentoModal').modal('hide')
            }
        }
    );
}
// function add_pedido_servicos(){
//     $.post(
//         "/saude-beta/pedido/adicionar-plano",{
//             _token: $("meta[name=csrf-token]").attr("content"),
//             id: $('#pedidoModal #pedido_id').val(),
//             id_profissional: $("#profissional_exe_id").val(),
//             id_plano:        $("#id_plano").val()
//         },
//         function (data, status) {
//             console.log(status + " | " + data);
//             if (data.error != undefined) {
//                 alert(data.error);
//             }
//             else{
//                 add_pedido_lista(data.id_pedido)
//             }
//         }

//     );
// }
function atualizarValorTotal() {
    var valores = document.querySelectorAll("#valor_plano"),
        numAux = 0
    var el = valores[valores.length - 1];
    var conteudo = el.innerHTML;
    var centavos = conteudo.indexOf(".") > -1 ? conteudo.substring(conteudo.indexOf(".") + 1) : "00";
    var inicio = conteudo;
    var texto_final = (parseFloat(conteudo) * 100).toString();
    if (texto_final.indexOf(".") > -1) texto_final = texto_final.substring(0, texto_final.indexOf("."));
    for (var i = 0; i < valores.length; i++) {
        if (valores[i].innerHTML == inicio) {
            if (texto_final == "") valores[i].innerHTML = "R$ 0,00";
            texto_final = moneyAux(texto_final);
            var inteiros = texto_final.substring(0, texto_final.indexOf(','));
            if (centavos.length == 1) centavos = centavos + "0";
            valores[i].innerHTML = inteiros.toLocaleString('pt-BR') + "," + centavos;
        }
    }

    valores.forEach(el => {
        numAux += (parseFloat(phoneInt($(el).html())) / 200)
    })
    numAux = numAux.toFixed(2);
    numAux = parseFloat(numAux.substring(0, numAux.indexOf("."))).toLocaleString("pt-BR") + "," + numAux.substring(numAux.indexOf(".") + 1);
    $('#valor_total_planos').html('R$ ' + numAux);
    document.getElementById("plan_tot").style.width = document.getElementById("tabela-planos").offsetWidth + "px";
    if (valores.length == 2) document.getElementById("plan-div").style.height = document.getElementById("plan-div").offsetHeight + "px";
    $('#pedidoModal #desc_plan').val('');
}
function atualizarValorTotalAntigo() {
    var valores = document.querySelectorAll("#pedidoAntigoModal #valor_plano"),
        numAux = 0
    valores.forEach(el => {
        numAux += (parseFloat($(el).html())) / 2
        console.log(parseFloat($(el).html()))
        console.log($(el).parent().find('#n_pessoas').html())
    })

    $('#pedidoAntigoModal #valor_total_planos').html('')
    $('#pedidoAntigoModal #valor_total_planos').append(numAux.toFixed(2));
}

var valoresPlano = new Array();
var valoresPlanoReal = new Array();

function mudaValorPlano() {
    valoresPlano["p" + $("#pedidoModal #id_plano").val()] = document.getElementById("desc_plan").value;
    valoresPlanoReal["p" + $("#pedidoModal #id_plano").val()] = document.getElementById("desc_plan").value;
}

function getValorPlano() {
    desconto_associados = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().desconto_associados
    valor = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().valor
    valor_convenio = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().valor_convenio
    associado = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().associado
    convenio = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().convenio
    if (convenio == "S") {
        if (associado == "S") {
            if (
                (valor_convenio != null && desconto_associados != null) &&
                (valor_convenio != '' && desconto_associados != '')
            ) {
                if (valor_convenio < desconto_associados) resultado = valor_convenio
                else resultado = desconto_associados
            } else if (
                (valor_convenio != null && desconto_associados == null) &&
                (valor_convenio != '' && desconto_associados == '')
            ) resultado = valor_convenio
            else if (
                (valor_convenio == null && desconto_associados != null) &&
                (plano.valor_convenio == '' && desconto_associados != '')
            ) resultado = desconto_associados
            else resultado = valor
        } else {
            if (valor_convenio != null && valor_convenio != '') resultado = valor_convenio
            else resultado = valor
        }
    } else {
        if (associado == "S") {
            if (desconto_associados != null && valor_convenio != '') resultado = desconto_associados
            else resultado = valor
        } else resultado = valor
    }
    return resultado
}

function add_pedido_lista() {
    aux = $('#pedidoModal #id_plano').val()
    id = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().id
    descr = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().descr
    descr_tabela_preco = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().descr_tabela_preco
    id = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().id
    vigencia = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().vigencia
    n_pessoas = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().n_pessoas
    desconto_associados = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().desconto_associados
    valor = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().valor
    valor_convenio = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().valor_convenio
    associado = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().associado
    convenio = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().convenio
    if ($("#pedidoModal #contador").val() == 'NaN' || $("#pedidoModal #contador").val() == '') $("#pedidoModal #contador").val("0")
    contador = parseInt($("#pedidoModal #contador").val()) + 1
    profissional = $("#profissional_exe_nome").val();
    profissional_id = $("#profissional_exe_id").val();
    $("#pedidoModal #contador").val(contador)
    $("#pedidoModal #profissional_exe_nome").val('');
    html = '<tr style="background: white !important" class="linha' + contador + '" id="linha">'
    html += '   <th width="50%" data-plano_id="' + id + '">' + descr_tabela_preco + '</th>'
    switch (vigencia) {
        case 30:
            html += '    <th  width="5%" class="text-left">Mensal</th>'
            break;
        case 60:
            html += '    <th  width="5%" class="text-left">Bimestral</th>'
            break;
        case 90:
            html += '    <th  width="5%" class="text-left">Trimestral</th>'
            break;
        case 180:
            html += '    <th  width="5%" class="text-left">Semestral</th>'
            break;
        case 360:
            html += '    <th  width="5%" class="text-left">Anual</th>'
            break;
    }
    html += ' <input class="lista_id_pessoas" data-plano_id_pessoas id="pessoas" type="hidden">'

    html += '    <th id="n_pessoas" width="10%" class="n_pessoas text-right"'

    html += ' onclick="abrirModalPessoasPlano(' + 0 + ',' + "'#linha" + $("#tabela-planos > tbody > tr").length + "'" + ', ' + n_pessoas + ')">' + n_pessoas + '</th>'

    html += '    <th id="valor_plano" width="10%" class="text-right">'
    try {
        html += valoresPlano["p" + aux];
    } catch(err) {
        html += valoresPlanoReal["p" + aux];
        valoresPlano["p" + aux] = valoresPlanoReal["p" + aux];
    }
    html += '</th>'
    html += '    <th width="10%"  class="text-center">'
    html += '       <img src="img/lixeira-de-reciclagem.png" style="max-width: 65%;cursor: pointer;" onclick="excluir_pedido_lista(' + "'.linha" + contador + "'" + ')">'
    html += '    </th>'
    html += '</tr>'
    $('#pedidoModal #tabela-planos > tbody').append(html);




    html2 = '<tr style="background: white !important" class="linha' + contador + '" id="linha">'
    html2 += '    <th width=<th width="45%" >' + descr_tabela_preco + '</th>'
    switch (vigencia) {
        case 30:
            html2 += '    <th  width="12.5%" class="text-center">Mensal</th>'
            break;
        case 60:
            html2 += '    <th  width="12.5%" class="text-center">Bimestral</th>'
            break;
        case 90:
            html2 += '    <th  width="12.5%" class="text-center">Trimestral</th>'
            break;
        case 180:
            html2 += '    <th  width="12.5%" class="text-center">Semestral</th>'
            break;
        case 360:
            html2 += '    <th  width="12.5%" class="text-center">Anual</th>'
            break;
    }
    html2 += ' <input class="lista_id_pessoas" data-plano_id_pessoas id="pessoas" type="hidden">'

    html2 += '    <th id="n_pessoas" width="10%" class="n_pessoas text-right"'

    html2 += ' onclick="abrirModalPessoasPlano(' + id + ',' + "'#linha" + $("#tabela-planos > tbody > tr").length + "'" + ', ' + n_pessoas + ')">' + n_pessoas + '</th>'
    html2 += '    <th id="valor_plano" width="10%" class="text-right">'
    
    try {
        html2 += valoresPlano["p" + aux];
    } catch(err) {
        html2 += valoresPlanoReal["p" + aux];
    }
    html2 += '</th>'
    html2 += '    <th width="5%"  class="text-center">'
    html2 += '       <img src="img/lixeira-de-reciclagem.png" style="max-width: 65%;cursor: pointer;">'
    html2 += '    </th>'
    html2 += '</tr>'
    $('#pedidoModal #tabela-planos2 > tbody').append(html2);
    $('#pedidoModal #id_plano').val(0);
    atualizarValorTotal()
}
function add_pedido_antigo_lista2() {
    aux = $('#pedidoAntigoModal #id_plano').val()
    id = $("#pedidoAntigoModal #planos-dataset").find('[data-id="' + $("#pedidoAntigoModal #id_plano").val() + '"]').data().id
    descr = $("#pedidoAntigoModal #planos-dataset").find('[data-id="' + $("#pedidoAntigoModal #id_plano").val() + '"]').data().descr
    descr_tabela_preco = $("#pedidoAntigoModal #planos-dataset").find('[data-id="' + $("#pedidoAntigoModal #id_plano").val() + '"]').data().descr_tabela_preco
    id = $("#pedidoAntigoModal #planos-dataset").find('[data-id="' + $("#pedidoAntigoModal #id_plano").val() + '"]').data().id
    vigencia = $("#pedidoAntigoModal #planos-dataset").find('[data-id="' + $("#pedidoAntigoModal #id_plano").val() + '"]').data().vigencia
    n_pessoas = $("#pedidoAntigoModal #count_plano").val();
    $("#pedidoAntigoModal #count_plano").val('1');
    desconto_associados = $("#pedidoAntigoModal #planos-dataset").find('[data-id="' + $("#pedidoAntigoModal #id_plano").val() + '"]').data().desconto_associados
    valor = $("#pedidoAntigoModal #planos-dataset").find('[data-id="' + $("#pedidoAntigoModal #id_plano").val() + '"]').data().valor
    valor_convenio = $("#pedidoAntigoModal #planos-dataset").find('[data-id="' + $("#pedidoAntigoModal #id_plano").val() + '"]').data().valor_convenio
    associado = $("#pedidoAntigoModal #planos-dataset").find('[data-id="' + $("#pedidoAntigoModal #id_plano").val() + '"]').data().associado
    convenio = $("#pedidoAntigoModal #planos-dataset").find('[data-id="' + $("#pedidoAntigoModal #id_plano").val() + '"]').data().convenio
    contador = parseInt($("#pedidoAntigoModal #contador").val()) + 1
    profissional = $("#pedidoAntigoModal #profissional_exe_nome").val();
    profissional_id = $("#pedidoAntigoModal #profissional_exe_id").val();
    $("#pedidoAntigoModal #contador").val(contador)
    $("#pedidoAntigoModal #profissional_exe_nome").val('');
    $('#pedidoAntigoModal #id_plano').val(0);
    html = '<tr style="background: white !important" class="linha' + contador + '" id="linha">'
    html += '   <th width="50%" data-plano_id="' + id + '">' + descr_tabela_preco + '</th>'
    switch (vigencia) {
        case 30:
            html += '    <th  width="5%" class="text-center">Mensal</th>'
            break;
        case 60:
            html += '    <th  width="5%" class="text-center">Bimestral</th>'
            break;
        case 90:
            html += '    <th  width="5%" class="text-center">Trimestral</th>'
            break;
        case 180:
            html += '    <th  width="5%" class="text-center">Semestral</th>'
            break;
        case 360:
            html += '    <th  width="5%" class="text-center">Anual</th>'
            break;
    }
    html += ' <input class="lista_id_pessoas" data-plano_id_pessoas id="pessoas" type="hidden">'

    html += '    <th id="n_pessoas" width="10%" class="n_pessoas text-right"'

    html += ' onclick="abrirModalPessoasPlano(' + 0 + ',' + "'#linha" + $("#pedidoAntigoModal #tabela-planos > tbody > tr").length + "'" + ', ' + n_pessoas + ')">' + n_pessoas + '</th>'

    html += '    <th id="valor_plano" width="10%" class="text-right">'



    if (convenio == 'S' && associado == 'S') {

        if (valor_convenio != null && desconto_associados != null) {
            if (parseInt(valor_convenio) < parseInt(desconto_associados)) {
                html += valor_convenio * parseInt(n_pessoas)
            }
            else html += desconto_associados * parseInt(n_pessoas)
        }

        else if (valor_convenio != null && desconto_associados == null) {
            html += valor_convenio * parseInt(n_pessoas)
        }
        else if (valor_convenio == null && desconto_associados != null) {
            html += desconto_associados * parseInt(n_pessoas)
        }
        else html += valor * parseInt(n_pessoas)
    }

    else if (convenio == 'S' && associado == "N") {
        if (valor_convenio != null) {
            html += valor_convenio * parseInt(n_pessoas)
        }
        else html += valor * parseInt(n_pessoas)

    }

    else if (convenio == 'N' && associado == "S") {
        if (desconto_associados != null) {
            html += desconto_associados * parseInt(n_pessoas)
        }
        else html += valor * parseInt(n_pessoas)
    }

    else {
        html += valor * parseInt(n_pessoas)
    }



    html += '</th>'
    html += '    <th width="10%"  class="text-center">'
    html += '       <img src="img/lixeira-de-reciclagem.png" style="max-width: 65%;cursor: pointer;" onclick="excluir_pedido_lista(' + "'.linha" + contador + "'" + ')">'
    html += '    </th>'
    html += '</tr>'
    $('#pedidoAntigoModal #tabela-planos > tbody').append(html);




    html2 = '<tr style="background: white !important" class="linha' + contador + '" id="linha">'
    html2 += '    <th width=<th width="45%" >' + descr_tabela_preco + '</th>'
    switch (vigencia) {
        case 30:
            html2 += '    <th  width="12.5%" class="text-center">Mensal</th>'
            break;
        case 60:
            html2 += '    <th  width="12.5%" class="text-center">Bimestral</th>'
            break;
        case 90:
            html2 += '    <th  width="12.5%" class="text-center">Trimestral</th>'
            break;
        case 180:
            html2 += '    <th  width="12.5%" class="text-center">Semestral</th>'
            break;
        case 360:
            html2 += '    <th  width="12.5%" class="text-center">Anual</th>'
            break;
    }
    html2 += ' <input class="lista_id_pessoas" data-plano_id_pessoas id="pessoas' + contador + '" type="hidden">'

    html2 += '    <th id="n_pessoas" width="10%" class="n_pessoas text-right"'

    html2 += ' onclick="abrirModalPessoasPlano(' + id + ',' + "'#linha" + $("#pedidoAntigoModal #tabela-planos > tbody > tr").length + "'" + ', ' + n_pessoas + ')">' + n_pessoas + '</th>'
    html2 += '    <th id="valor_plano" width="10%" class="text-right">'



    if (convenio == 'S' && associado == 'S') {

        if (valor_convenio != null && desconto_associados != null) {
            if (parseInt(valor_convenio) < parseInt(desconto_associados)) {
                html2 += valor_convenio
            }
            else html2 += desconto_associados
        }

        else if (valor_convenio != null && desconto_associados == null) {
            html2 += valor_convenio
        }
        else if (valor_convenio == null && desconto_associados != null) {
            html2 += desconto_associados
        }
        else html2 += valor
    }

    else if (convenio == 'S' && associado == "N") {
        if (valor_convenio != null) {
            html2 += valor_convenio
        }
        else html2 += valor

    }

    else if (convenio == 'N' && associado == "S") {
        if (desconto_associados != null) {
            html2 += desconto_associados
        }
        else html2 += valor
    }

    else {
        html2 += valor
    }



    html2 += '</th>'
    html2 += '    <th width="5%"  class="text-center">'
    html2 += '       <img src="img/lixeira-de-reciclagem.png" style="max-width: 65%;cursor: pointer;">'
    html2 += '    </th>'
    html2 += '</tr>'
    $('#pedidoAntigoModal #tabela-planos2 > tbody').append(html2);
    atualizarValorTotalAntigo()
}


function add_pedido_lista2() {
    aux = $('#pedidoModal #id_plano').val()
    id = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().id
    descr = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().descr
    descr_tabela_preco = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().descr_tabela_preco
    id = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().id
    vigencia = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().vigencia
    n_pessoas = $("#pedidoModal #count_plano").val();
    $("#pedidoModal #count_plano").val('1');
    desconto_associados = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().desconto_associados
    valor = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().valor
    valor_convenio = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().valor_convenio
    associado = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().associado
    convenio = $("#planos-dataset").find('[data-id="' + $("#pedidoModal #id_plano").val() + '"]').data().convenio
    if ($("#pedidoModal #contador").val() == 'NaN') $("#pedidoModal #contador").val("0")
    contador = parseInt($("#pedidoModal #contador").val()) + 1
    profissional = $("#profissional_exe_nome").val();
    profissional_id = $("#profissional_exe_id").val();
    $("#pedidoModal #contador").val(contador)
    $("#pedidoModal #profissional_exe_nome").val('');
    $('#pedidoModal #id_plano').val(0);
    html = '<tr style="background: white !important" class="linha' + contador + '" id="linha">'
    html += '   <th width="50%" data-plano_id="' + id + '">' + descr_tabela_preco + '</th>'
    switch (vigencia) {
        case 30:
            html += '    <th  width="5%" class="text-center">Mensal</th>'
            break;
        case 60:
            html += '    <th  width="5%" class="text-center">Bimestral</th>'
            break;
        case 90:
            html += '    <th  width="5%" class="text-center">Trimestral</th>'
            break;
        case 180:
            html += '    <th  width="5%" class="text-center">Semestral</th>'
            break;
        case 360:
            html += '    <th  width="5%" class="text-center">Anual</th>'
            break;
    }
    html += ' <input class="lista_id_pessoas" data-plano_id_pessoas id="pessoas" type="hidden">'

    html += '    <th id="n_pessoas" width="10%" class="n_pessoas text-right"'

    html += ' onclick="abrirModalPessoasPlano(' + 0 + ',' + "'#linha" + $("#tabela-planos > tbody > tr").length + "'" + ', ' + n_pessoas + ')">' + n_pessoas + '</th>'

    html += '    <th id="valor_plano" width="10%" class="text-right">'
    if ($('#pedidoModal #listar_valor_associado').prop('checked') == true) associado == 'S'
    if (convenio == 'S' && associado == 'S') {

        if (valor_convenio != null && desconto_associados != null) {
            if (parseInt(valor_convenio) < parseInt(desconto_associados)) {
                valoresPlanoReal["p" + aux] = valor_convenio * parseInt(n_pessoas)
            }
            else valoresPlanoReal["p" + aux] = desconto_associados * parseInt(n_pessoas)
        }

        else if (valor_convenio != null && desconto_associados == null) {
            valoresPlanoReal["p" + aux] = valor_convenio * parseInt(n_pessoas)
        }
        else if (valor_convenio == null && desconto_associados != null) {
            valoresPlanoReal["p" + aux] = desconto_associados * parseInt(n_pessoas)
        }
        else valoresPlanoReal["p" + aux] = valor * parseInt(n_pessoas)
    }

    else if (convenio == 'S' && associado == "N") {
        if (valor_convenio != null) {
            valoresPlanoReal["p" + aux] = valor_convenio * parseInt(n_pessoas)
        }
        else valoresPlanoReal["p" + aux] = valor * parseInt(n_pessoas)

    }

    else if (convenio == 'N' && associado == "S") {
        if (desconto_associados != null) {
            valoresPlanoReal["p" + aux] = desconto_associados * parseInt(n_pessoas)
        }
        else valoresPlanoReal["p" + aux] = valor * parseInt(n_pessoas)
    }

    else {
        valoresPlanoReal["p" + aux] = valor * parseInt(n_pessoas)
    }
    try {
        html += (parseInt(phoneInt(valoresPlano["p" + aux])) * parseInt(n_pessoas)) / 100;
    } catch(err) {
        html += valoresPlanoReal["p" + aux];
    }



    html += '</th>'
    html += '    <th width="10%"  class="text-center">'
    html += '       <img src="img/lixeira-de-reciclagem.png" style="max-width: 65%;cursor: pointer;" onclick="excluir_pedido_lista(' + "'.linha" + contador + "'" + ')">'
    html += '    </th>'
    html += '</tr>'
    $('#pedidoModal #tabela-planos > tbody').append(html);




    html2 = '<tr style="background: white !important" class="linha' + contador + '" id="linha">'
    html2 += '    <th width=<th width="45%" >' + descr_tabela_preco + '</th>'
    switch (vigencia) {
        case 30:
            html2 += '    <th  width="12.5%" class="text-center">Mensal</th>'
            break;
        case 60:
            html2 += '    <th  width="12.5%" class="text-center">Bimestral</th>'
            break;
        case 90:
            html2 += '    <th  width="12.5%" class="text-center">Trimestral</th>'
            break;
        case 180:
            html2 += '    <th  width="12.5%" class="text-center">Semestral</th>'
            break;
        case 360:
            html2 += '    <th  width="12.5%" class="text-center">Anual</th>'
            break;
    }
    html2 += ' <input class="lista_id_pessoas" data-plano_id_pessoas id="pessoas' + contador + '" type="hidden">'

    html2 += '    <th id="n_pessoas" width="10%" class="n_pessoas text-right"'

    html2 += ' onclick="abrirModalPessoasPlano(' + id + ',' + "'#linha" + $("#tabela-planos > tbody > tr").length + "'" + ', ' + n_pessoas + ')">' + n_pessoas + '</th>'
    html2 += '    <th id="valor_plano" width="10%" class="text-right">'


    try {
        html2 += (parseInt(phoneInt(valoresPlano["p" + aux])) * parseInt(n_pessoas)) / 100;
    } catch(err) {
        html2 += valoresPlanoReal["p" + aux];
        valoresPlano["p" + aux] = valoresPlanoReal["p" + aux];
    }
    


    html2 += '</th>'
    html2 += '    <th width="5%"  class="text-center">'
    html2 += '       <img src="img/lixeira-de-reciclagem.png" style="max-width: 65%;cursor: pointer;">'
    html2 += '    </th>'
    html2 += '</tr>'
    $('#pedidoModal #tabela-planos2 > tbody').append(html2);
    atualizarValorTotal()
}



// function add_pedido_lista(id_plano_pedidos) {
//     $.get('/saude-beta/pedido/listar-planos/' + id_plano_pedidos, function (data) {
//         data = $.parseJSON(data);

//         $("#profissional_exe_nome").val('');
//         $('#id_plano').val(0);
//         $('#tabela-planos > tbody').empty();
//         data.forEach(plano => {
//             html  = '<tr style="background: white !important">'
//             html += '    <th width=<th width="45%">' + plano.descr + '</th>'
//             html += '    <th width="25%">' + plano.profissional + '</th>'
//             html += '    <th width="12.5%" class="text-right">' + plano.n_pessoas + '</th>'
//             html += '    <th id="valor_plano" width="12.5%" class="text-right">' + plano.valor + '</th>'
//             html += '    <th width="5%"  class="text-center">'
//             html += '       <img src="img/lixeira-de-reciclagem.png" style="max-width: 65%;cursor: pointer;" onclick="excluir_pedido_lista('+ plano.id + ',' + id_plano_pedidos + ')">'
//             html += '    </th>'
//             html += '</tr>'
//             $('#tabela-planos > tbody').append(html);
//         });
//     atualizarValorTotal()
//     });
//     atualizarValorTotal()
// }
function excluir_pedido_lista(id) {
    console.log($(this))
    if (window.confirm("Deseja excluir plano do contrato?")) {
        document.querySelectorAll(id).forEach(el => {
            $(id).remove();
        })
        $(id).remove();
        $(id).remove();
        atualizarValorTotal();
    }
}
function excluir_pessoa_lista(id) {
    if (window.confirm("Deseja remover pessoa do contrato?")) {
        $(id).remove()
    }
}
// function criar_agendamento() {
//     $.post(
//         "/saude-beta/agenda/validar-contrato", {
//             id_tipo_procedimento: $("#id_tipo_procedimento"),
//             paciente_id: $("#paciente_id")
//         },
//     )
//         $.post(
//             "/saude-beta/agenda/salvar", {
//             _token: $("meta[name=csrf-token]").attr("content"),
//             id_grade_horario:$("#id_grade_horario"),
//             id_sala: $("#id_sala"),
//             id_profissional: $("#id_profissional"),
//             paciente_id: $("#paciente_id"),
//             procedimento_id: $("procedimento_id"),
//             id_tipo_procedimento: $("#id_tipo_procedimento"),
//             id_pedido: $("#id_pedido"),
//             id_plano: $("#id_plano"),
//             id_agenda_status: $("#id_agenda_status"),
//             data: $("#data"),
//             hota: $("#hora"),
//             tempo_procedimento: $("#tempo_procedimento"),
//             obs: $("#obs"),
//             encaixe: $("#encaixe").prop('checked'),
//             retorno: $("#retorno").prop('checked'),
//             convenio: $("#convenio"),
//             valor: $("#valor"),
//             convenio_id: $("#convenio_id")
//         },
//             function (data, status) {
//                 console.log(status + " | " + data);
//                 console.log(data.id)
//                 if (data.error != undefined) {
//                     alert(data.error);
//                 }
//                 else{
//                     $('#tipoprocedimentoModal').modal('hide')
//                 }
//             }
//         );
// }
function validar_atv_semana() {
    if (campo_invalido("#criarAgendamentoModal #paciente_id", true) || campo_invalido("#criarAgendamentoModal #paciente_nome", false)) {
        alert('Campo "Associado" inválido!')
        return;
    }
    if (campo_invalido("#criarAgendamentoModal #id_tipo_procedimento", true)) {
        alert("Selecione um tipo de agendamento para prosseguir!")
        return;
    }
    if (campo_invalido("#criarAgendamentoModal #modalidade_id", true)) {
        alert("Selecione uma modalidade para prosseguir")
        return;
    }

    else if ($("#criarAgendamentoModal #id_tipo_procedimento").val() == 4) {

        if (campo_invalido("#criarAgendamentoModal #procedimento_id", true)) {
            alert("Selecione um plano para prosseguir!")
            return;
        }
        criar_agendamento();
    }
    else if ($("#criarAgendamentoModal #id_tipo_procedimento").val() == 1) {

        if (campo_invalido("#criarAgendamentoModal #id_contrato", true)) {
            alert("Selecionar um contrato é obrigatório para este tipo de agendamento")
            return;
        }
        if (campo_invalido("#criarAgendamentoModal #id_plano")) {
            alert("Selecione um plano do contrato")
            return;
        }

        dataaux = $('#criarAgendamentoModal #data').val();
        dataaux = data[6] + data[7] + data[8] + data[9] + '-' + data[3] + data[4] + '-' + data[0] + data[1]
        $.get(
            "/saude-beta/agenda/validar-plano", {
            id_pedido: $("#criarAgendamentoModal #id_contrato").val(),
            id_tabela_preco: $("#criarAgendamentoModal #id_plano").val(),
            data: dataaux,
            hora: $('#criarAgendamentoModal #hora').val(),
        },
            function (data, status) {
                console.log(data + status)
                if (data == "true") {
                    criar_agendamento()
                }
                else {
                    alert("Agendamentos excedidos para este plano")
                }
            }
        )
    }
    else if ($("#criarAgendamentoModal #id_tipo_procedimento").val() == 5) {

        if (campo_invalido("#criarAgendamentoModal #modalidade_id", true)) {
            alert("Selecione uma modalidade para prosseguir!")
            return;
        }
        criar_agendamento();
    }


    // if ($("#id_tipo_procedimento").val() != ''){
    //     $.get("/saude-beta/tipo-procedimento/mostrar/" + $("#id_tipo_procedimento").val(), function(data) {
    //         data = $.parseJSON(data);
    //         if ($("#criarAgendamentoModal #id_tipo_procedimento").val() == 1 ||
    //             $("#criarAgendamentoModal #id_tipo_procedimento").val() == '1'){
    //             if($("#criarAgendamentoModal #id_contrato").val() == 0   ||
    //                $("#criarAgendamentoModal #id_contrato").val() == '0' ||
    //                $("#criarAgendamentoModal #id_plano").val()    == 0   ||
    //                $("#criarAgendamentoModal #id_plano").val()    == '0'){
    //                 alert('É obrigatório selecionar um contrato e um plano para agendar procedimentos de Habilitação e Reabilitação')
    //             }
    //             else {
    //                 criar_agendamento();
    //             }
    //         }
    //         else {
    //             data = $('#criarAgendamentoModal #data').val();
    //             data = data[6]+data[7]+data[8]+data[9]+'-'+data[3]+data[4]+'-'+data[0]+data[1]
    //             $.get(
    //                 "/saude-beta/agenda/validar-plano",{
    //                     id_pedido: $("#criarAgendamentoModal #id_contrato").val(),
    //                     id_tabela_preco: $("#criarAgendamentoModal #id_plano").val(),
    //                     data: data,
    //                     hora: $('#criarAgendamentoModal #hora').val(),
    //                 },
    //                 function (data, status) {
    //                     console.log(status + " | " + data);
    //                     if (data.error != undefined) {
    //                         alert(data.error);
    //                     }
    //                     else{
    //                         if (data == 'true'){
    //                             // // if (window.confirm('Deseja agendar todas as atividades do contrato automaticamente?')){
    //                             // if (false){
    //                             //     criar_agendamento(1)
    //                             // }else 
    //                             criar_agendamento()
    //                         }
    //                         else if(data == 'false'){
    //                             alert('Agendamentos por semana excedidos para este plano')
    //                         }
    //                         else alert("ERRO")
    //                     }
    //                 }

    //             );
    //         }
    //     });
    // }
    // else criar_agendamento();

}
function campo_invalido(campo, numeric) {
    bool = false
    if (campo.indexOf("id_plano") > -1) {
        if ($(campo).val() === null || $(campo).val() == '' || isNaN($(campo).val())) {
            bool = true
        }
    }
    else if (numeric) {
        if ($(campo).val() == 0 || $(campo).val() === null || $(campo).val() == '' || isNaN($(campo).val())) {
            bool = true
        }
    }
    else {
        if ($(campo).val() == 0 || $(campo).val() === null || $(campo).val() == '') {
            bool = true
        }
    }
    return bool
}
function salvar_metas_modalidade() {
    $.post('/saude-beta/procedimento/salvar-metas-modalidade', {
        _token: $("meta[name=csrf-token]").attr('content'),
        id: $("#metasModal #id-modalidade").val(),
        valor_total: $('#metasModal #teto').val(),
        tipo_de_comissao: $('#metasModal #tipo_de_comissao').val(),
        total_agendamentos_metas: $('#metasModal #acima-de').val()
    }, function (data, status) {
        console.log(data + ' | ' + status)
        if (data.error) {
            alert(data.error)
        }
        else {
            if (data == 'true') {
                location.reload();
            }
        }
    })
}

var bloquear_grade_agendamento = "";
function criar_agendamento($agendamento_automatico){
    if(bloquear_grade_agendamento){
        alert(bloquear_grade_agendamento)
    } else{
        criar_agendamento_main($agendamento_automatico)
    }
}
function criar_agendamento_main($agendamento_automatico) {
    $agendamento_automatico = $agendamento_automatico || 0;

    tipo_procedimento = $('#criarAgendamentoModal #id_tipo_procedimento').val()

    plano_pre = $("#criarAgendamentoModal #procedimento_id").val()
    id_contrato = $("#criarAgendamentoModal #id_contrato").val()
    plano = $("#criarAgendamentoModal #id_plano").val()
    convenio = $("#criarAgendamentoModal #convenio_id").val()
    switch (parseInt(tipo_procedimento)) {
        case 4:
            id_contrato = 0
            plano = plano_pre
            break;
        case 5:
            id_contrato = 0
            plano = 0
            break;
    }

    $.post(
        '/saude-beta/agenda/salvar', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_grade_horario: $('#criarAgendamentoModal #id-grade-horario').val(),
        id_profissional: $('#criarAgendamentoModal #id-profissional').val(),
        id: $('#criarAgendamentoModal #id').val(),
        id_paciente: $('#criarAgendamentoModal #paciente_id').val(),
        id_tipo_procedimento: tipo_procedimento,
        id_convenio: convenio,
        id_pedido: id_contrato,
        id_tabela_preco: plano,
        modalidade_id: $("#criarAgendamentoModal #modalidade_id").val(),
        data: $('#criarAgendamentoModal #data').val(),
        hora: $('#criarAgendamentoModal #hora').val(),
        obs: $('#criarAgendamentoModal #obs').val(),
        agenda_encaminhante_id : $('#criarAgendamentoModal #agenda_encaminhante_id').val(),
        agenda_enc_esp : $('#criarAgendamentoModal #agenda_enc_esp').val(),
        enc_cid_id : $("#criarAgendamentoModal #enc_cid_id").val(),
        agenda_sol : $("#criarAgendamentoModal #agenda_sol").val()
    },
        function (data, status) {
            console.log(data)
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                if (!isNaN(data)) {
                    if ($agendamento_automatico == 1) {
                        console.log(data.id)
                        abrirAgendamentoLoteModal(data.id);
                        $('#criarAgendamentoModal').modal('hide');
                    }
                    else {
                        $('#criarAgendamentoModal').modal('hide');
                        alert("Agendamento salvo com sucesso")
                        mostrar_agendamentos();
                        mostrar_agendamentos_semanal();
                        pesquisarAgendamentosPendentes()
                        listaAuxA = [1483000000, 1476000000, 447000000, 1483000000, 444000000, 28480002089]
                        if (listaAuxA.indexOf($('.selected').val()) != -1) {
                            return;
                        }
                        anexos_enc_agenda(parseInt(data), $('#criarAgendamentoModal #paciente_id').val(), $('#criarAgendamentoModal #id-profissional').val());
                        ShowConfirmationBox(
                            "Agendamento criado com sucesso!",
                            "Deseja avisar os participantes via whatsapp?",
                            true, true, true,
                            function () {
                                notificar_agendamento_whatsapp(data, 1)
                            },
                            function () {
                                notificar_agendamento_whatsapp(data, 2)
                            },
                            "Avisar Associado e Membro",
                            "Avisar Associado"
                        );
                    }
                }
                else alert("Erro")
            }
        }
    );
}
function notificar_agendamento_whatsapp($id, $value) {
    $.get(
        '/saude-beta/agenda/notificar_participantes', {
        _token: $('meta[name="csrf-token"]').attr('content'),
        id_agendamento: $id,
        opcao: $value
    },
        function (data, status) {
            console.log(data + ' | ' + status)
            if (data == 'true') {
                switch ($value) {
                    case 1:
                        msg = 'Mensagens enviadas com sucesso'
                        break;
                    case 2:
                        msg = 'Mensagem enviada com sucesso'

                }
                alert(msg)
            }
        }
    )
}







var agLote, agLoteNum;
function abrirAgendamentoLoteModal(paciente, contrato, plano) {
    $.get(
        '/saude-beta/agenda/modal-agendamento-lote/' + paciente + "/" + contrato + "/" + plano,
        function (data, status) {
            console.log(data + ' | ' + status)
            data = $.parseJSON(data);
            //$("#botao-lote-agendamento-modal").attr('onclick', 'gerarAgendamentosEmLote();')
            var datac = data.contrato.data
            datac = datac[8] + datac[9] + '/' + datac[5] + datac[6] + '/' + datac[0] + datac[1] + datac[2] + datac[3];
            $('#agendamentoLoteModal #CRlote').html('Contrato realizado em: ' + datac)
            $('#agendamentoLoteModal #ASlote').html('Associado: ' + data.associado.nome_fantasia)
            $('#agendamentoLoteModal #SEMlote').html(data.plano.max_atv_semana + ' atividades por semana')
            $('#agendamentoLoteModal2 #CRlote2').html('Contrato realizado em: ' + datac)
            $('#agendamentoLoteModal2 #ASlote2').html('Associado: ' + data.associado.nome_fantasia)
            $('#agendamentoLoteModal2 #SEMlote2').html(data.plano.max_atv_semana + ' atividades por semana')
            $('#numAgTotal').html(data.plano.max_atv_semana)

            /*$('#agendamentoLoteModal #id_agendamento_lote').val(data.agenda.id)
            $('#agendamentoLoteModal #data-inicial').val(data.data).prop('disabled', true)
            $("#agendamentoLoteModal #conteudo-lote-agenda").empty();*/
            $("#agendamentoLoteModal").modal('show');
            $("#criarAgendamentoModal").modal("hide");
            agLoteNum = 0;
            agLote = {
                _token: $('meta[name="csrf-token"]').attr('content'),
                paciente: null,
                contrato: null,
                plano: null,
                inicio: null,
                fim: null,
                sessoes: new Array(),
                dataC: datac,
                validade: data.contrato.data_validade.split(" ")[0],
                associado: data.associado.nome_fantasia
            }
            agLote.paciente = paciente;
            agLote.contrato = contrato;
            agLote.plano = plano;
            /*console.log(data.plano.max_atv_semana);
            for (i = 0; i < data.plano.max_atv_semana; i++) {
                //AQUI
                html = '<div class="col-3 dias-semana">'
                html += '   <label for="dia_semana_lote' + i + '" class="custom-label-form">Dia da semana</label>    '
                html += '   <select id="dia_semana_lote' + i + '" class="custom-select" onchange="control_lote(' + i + '");">   '
                html += '       <option value="0" disabled selected >Selecionar...</option>  '
                html += '       <option value="1">Domingo</option>  '
                html += '       <option value="2">Segunda</option>  '
                html += '       <option value="3">Terça  </option>  '
                html += '       <option value="4">Quarta </option>  '
                html += '       <option value="5">Quinta </option>  '
                html += '       <option value="6">Sexta  </option>  '
                html += '       <option value="7">Sábado </option>  '
                html += '   </select>   '
                html += '</div>  '
                html += '<div class="col-3 horarios">    '
                html += '   <label for="horario_id' + i + '" class="custom-label-form">Horario*</label> '
                html += '   <select id="horario_id' + i + '" class="custom-select" onchange="control_lote(' + i + '");">   '

                html += '   </select> '
                html += '</div> '


                html += '<div class="col-6 membros">    '
                html += '   <label for="profissional_id' + i + '" class="custom-label-form">Membro*</label> '
                html += '   <select id="profissional_id' + i + '" class="custom-select" disabled=""> '
                html += '   </select> '
                html += '</div> '

                $("#agendamentoLoteModal #conteudo-lote-agenda").append(html);
            }*/
        }
    )
}
function compararData(el) {
    var valor = el.value.split("/");
    valor.reverse();
    valor = valor.join("-");
    valor = new Date(valor);
    validade = new Date(agLote.validade);
    dataC = agLote.dataC.split("/");
    dataC.reverse();
    dataC = dataC.join("-");
    dataC = new Date(dataC);
    if (valor > validade) {
        alert("Essa data excede a validade do contrato.")
        if (el.id == "data-final") {
            validade = agLote.validade.split("-");
            validade.reverse();
            el.value = validade.join("/");
        }
    } else if (valor < dataC) {
        alert("Essa data antecede o início do contrato.")
        dataC = agLote.dataC.split("-");
        dataC.reverse();
        el.value = dataC.join("/");
    }
}
function selecionarAgendamentoLote() {
    var erro = false;
    var inicio = $("#agendamentoLoteModal #data-inicial").val();
    var fim = $("#agendamentoLoteModal #data-final").val();
    if (inicio != "" && fim != "") {
        var teste = [inicio, fim];
        for (var i = 0; i < teste.length; i++) {
            teste[i] = teste[i].split("/");
            teste[i].reverse();
            teste[i] = teste[i].join("-");
            teste[i] = new Date(teste[i]);
        }
        if (teste[1] <= teste[0]) {
            alert("Preencha as datas corretamente para continuar");
            erro = true;
        }
    } else {
        alert("Preencha as datas para continuar");
        erro = true;
    }
    if (!erro) {
        agLote.inicio = $("#agendamentoLoteModal #data-inicial").val();
        agLote.fim = $("#agendamentoLoteModal #data-final").val();
        $("#agendamentoLoteModal").modal("hide");
        $("#agendamentoLoteModal2").modal("show");
        $.get('/saude-beta/procedimento/listar', {},
            function (data, status) {
                console.log(data + ' | ' + status)
                data = $.parseJSON(data);
                $('#agendamentoLoteModal2 #loteModalidade').html("<option value='0' disabled selected>Selecione a modalidade...</option>");
                data.forEach(modalidade => {
                    html = '<option value="' + modalidade.id + '">'
                    html += modalidade.descr
                    html += '</option>'
                    $('#agendamentoLoteModal2 #loteModalidade').append(html)
                })
                agendamentoLoteCriaSessao(false, false)
            }
        )
    }
}
var listaLote;
function empresasPorProfissional() {
    setTimeout(function () {
        $('#agendamentoLoteModal2 #prof_nome').css("border-color", "");
        var id = $("#agendamentoLoteModal2 #prof_id").val();
        if (id != "") {
            $('#agendamentoLoteModal2 #loteEmpresa').attr("disabled", false);
            var inicioJS = agLote.inicio.split("/");
            inicioJS = inicioJS[2] + "-" + inicioJS[1] + "-" + inicioJS[0];
            var finalJS = agLote.fim.split("/");
            finalJS = finalJS[2] + "-" + finalJS[1] + "-" + finalJS[0];
            $.get("/saude-beta/pessoa/listar-membros-e-horarios", {
                idProf: id,
                inicio: inicioJS,
                final: finalJS
            }, function (data) {
                listaLote = data;
                var empresasID = new Array();
                var empresasDESCR = new Array();
                for (var i = 0; i < data.length; i++) {
                    if (empresasID.indexOf(data[i].id_emp) == -1) {
                        empresasID[empresasID.length] = data[i].id_emp;
                        empresasDESCR[empresasDESCR.length] = data[i].descr;
                    }
                }
                document.getElementById("loteEmpresa").innerHTML = '<option value = "0" disabled selected>Selecione a empresa...</option>';
                for (var i = 0; i < empresasID.length; i++) document.getElementById("loteEmpresa").innerHTML += "<option value = '" + empresasID[i] + "'>" + empresasDESCR[i] + "</option>";
            });
        } else $('#agendamentoLoteModal2 #loteEmpresa').attr("disabled", true);
        $('#agendamentoLoteModal2 #loteData').val(0)
        $('#agendamentoLoteModal2 #loteData').attr("disabled", true)
        $('#agendamentoLoteModal2 #loteHora').val(0)
        $('#agendamentoLoteModal2 #loteHora').attr("disabled", true)
    }, 500);
}
function datasPorEmpresa(id) {
    if (id > 0) {
        $('#agendamentoLoteModal2 #loteEmpresa').css("border-color", "");
        $('#agendamentoLoteModal2 #loteData').attr("disabled", false);
        var dias = new Array();
        for (var i = 0; i < listaLote.length; i++) {
            if (dias.indexOf(listaLote[i].dia_semana) == -1 && listaLote[i].id_emp == id) dias[dias.length] = listaLote[i].dia_semana;
        }
        dias.sort();
        document.getElementById("loteData").innerHTML = '<option value = "0" disabled selected>Selecione o dia da semana...</option>';
        for (var i = 0; i < dias.length; i++) document.getElementById("loteData").innerHTML += "<option value = '" + dias[i] + "'>" + gj.core.messages["pt-br"].weekDays[dias[i] - 1] + "</option>";
    } else $('#agendamentoLoteModal2 #loteData').attr("disabled", true);
    $('#agendamentoLoteModal2 #loteHora').val(0)
    $('#agendamentoLoteModal2 #loteHora').attr("disabled", true)
}
function horasPorData(id) {
    if (id != "") {
        $('#agendamentoLoteModal2 #loteData').css("border-color", "");
        $('#agendamentoLoteModal2 #loteHora').attr("disabled", false);
        var horas = new Array();
        for (var i = 0; i < listaLote.length; i++) {
            if (
                horas.indexOf(listaLote[i].hora) == -1 &&
                listaLote[i].dia_semana == id &&
                document.getElementById("loteEmpresa").value == listaLote[i].id_emp
            ) horas[horas.length] = {
                id: listaLote[i].id_horario,
                label: listaLote[i].hora
            };
        }
        horas.sort(sort_by("label", false));
        document.getElementById("loteHora").innerHTML = '<option value = "0" disabled selected>Selecione o horário...</option>';
        listAux1 = []
        for (var i = 0; i < horas.length; i++) {
            // if (listAux1.indexOf(horas[i].label) == - 1) {
                listAux1.push(horas[i].label)
                document.getElementById("loteHora").innerHTML += "<option value = '" + horas[i].id + "'>" + horas[i].label + "</option>";
            // }
        }
    } else $('#agendamentoLoteModal2 #loteHora').attr("disabled", true);
}
const sort_by = (field, reverse) => {

    const key = function (x) {
        return x[field]
    };

    reverse = !reverse ? 1 : -1;

    return function (a, b) {
        return a = key(a), b = key(b), reverse * ((a > b) - (b > a));
    }
}
function listarDatas(inicio, fim, dia_semana) {
    var lista = new Array();
    inicio = inicio.split("-");
    fim = fim.split("-");
    dia_semana = indices(dia_semana);
    var dataAtual = inicio;
    do {
        dataAtual = avancar(dataAtual[0], dataAtual[1], dataAtual[2]);
        var d = new Date(dataAtual.join("-"));
        if (dia_semana == d.getDay()) lista[lista.length] = dataAtual.join("-");
    } while (dataAtual.join("-") != fim.join("-"));
    return lista;
}

function indices(valor) {
    var lim = [-5 - (7 * Math.abs(valor))];
    for (var i = 0; i < Math.pow(Math.abs(valor), 2); i++) lim[lim.length] = lim[lim.length - 1] + 7;
    lim.reverse();
    var feito = false;
    for (var i = 0; i < lim.length; i++) {
        if (!feito && valor >= lim[i]) {
            valor -= lim[i];
            feito = true;
        }
    }
    return valor;
}

function avancar(ano, mes, dia) {
    if (
        (
            mes != 2 &&
            (
                (mes < 8 && ((mes % 2 == 1 && dia == 31) || (mes % 2 == 0 && dia == 30))) ||
                (mes >= 8 && ((mes % 2 == 0 && dia == 31) || (mes % 2 == 1 && dia == 30)))
            )
        ) ||
        (
            mes == 2 &&
            (
                (ano % 4 == 0 && ano % 100 > 0 && dia == 29) ||
                (!(ano % 4 == 0 && ano % 100 > 0) && dia == 28)
            )
        )
    ) {
        dia = 1;
        if (mes == 12) {
            mes = 1;
            ano++;
        } else mes++;
    } else dia++;
    return [addZero(ano), addZero(mes), addZero(dia)];
}

function addZero(val) {
    val = val.toString();
    if (val.length < 2) val = "0" + val;
    return val;
}
function salvarLote(lista, limite) {
    var valHora;
    $("#loteHora").find("option").each(function () {
        if (this.value == $("#loteHora").val()) valHora = this.innerHTML;
    });
    for (var i = 0; i < limite; i++) {
        if ($('#agendamentoLoteModal2 #loteModalidade').val() != null &&
            $('#agendamentoLoteModal2 #prof_id').val() != '' &&
            $('#agendamentoLoteModal2 #loteEmpresa').val() != '' &&
            $('#agendamentoLoteModal2 #loteData').val() != '' &&
            $('#agendamentoLoteModal2 #loteHora').val()) {
            agLote.sessoes[agLote.sessoes.length] = {
                modalidade: $('#agendamentoLoteModal2 #loteModalidade').val(),
                profissional: $('#agendamentoLoteModal2 #prof_id').val(),
                empresa: $('#agendamentoLoteModal2 #loteEmpresa').val(),
                dia_semana: $('#agendamentoLoteModal2 #loteData').val(),
                grade_horario: $('#agendamentoLoteModal2 #loteHora').val(),
                data: lista[i],
                hora: valHora
            };
        }
    }
}
function agendamentoLoteCriaSessao(concluir, salvando) {
    var erro = new Array();
    var campos = [
        '#agendamentoLoteModal2 #loteModalidade',
        '#agendamentoLoteModal2 #prof_id',
        '#agendamentoLoteModal2 #prof_nome',
        '#agendamentoLoteModal2 #loteEmpresa',
        '#agendamentoLoteModal2 #loteData',
        '#agendamentoLoteModal2 #loteHora'
    ];
    for (var i = 0; i < campos.length; i++) {
        if ($(campos[i]).val() == "" || $(campos[i]).val() == 0 || $(campos[i]).val() == null) erro[erro.length] = campos[i];
    }
    if (erro.length && salvando && !concluir) {
        alert("Preencha todos os campos");
        for (var i = 0; i < erro.length; i++) $(erro[i]).css("border-color", "red");
    } else {
        if (salvando) {
            var comeco = agLote.inicio.split("/");
            comeco = comeco.reverse();
            comeco = comeco.join("-");
            var final = agLote.fim.split("/");
            final = final.reverse();
            final = final.join("-");
            var lista = listarDatas(comeco, final, $('#agendamentoLoteModal2 #loteData').val());
            if (lista.length > agLotePlanos[agLote.plano]) {
                ShowConfirmationBox(
                    agLote.associado.split(" ")[0] + ' possui apenas ' + agLotePlanos[agLote.plano] + " atividades restantes.",
                    'Deseja criar as ' + agLotePlanos[agLote.plano] + ' primeiras atividades?',
                    true, true, false,
                    salvarLote(lista, agLotePlanos[agLote.plano]),
                    $('#agendamentoLoteModal2').modal('hide'),
                    'Sim',
                    'Não'
                );
            } else salvarLote(lista, lista.length);
        }
        var numAgTotal = parseInt(document.getElementById("numAgTotal").innerHTML);
        document.getElementById("porc").style.width = ((agLoteNum * 100) / numAgTotal) + "%";
        $("#agendamentoLoteModal2 #numAg").html(++agLoteNum);
        $('#agendamentoLoteModal2 #loteModalidade').val(0)
        $('#agendamentoLoteModal2 #loteEmpresa').val(0)
        $('#agendamentoLoteModal2 #loteEmpresa').attr("disabled", true)
        $('#agendamentoLoteModal2 #loteData').val(0)
        $('#agendamentoLoteModal2 #loteData').attr("disabled", true)
        $('#agendamentoLoteModal2 #loteHora').val(0)
        $('#agendamentoLoteModal2 #loteHora').attr("disabled", true)
        $('#agendamentoLoteModal2 #prof_id').val("")
        $('#agendamentoLoteModal2 #prof_nome').val("")

        $('#agendamentoLoteModal2 #loteModalidade').css("border-color", "")
        $('#agendamentoLoteModal2 #loteEmpresa').css("border-color", "")
        $('#agendamentoLoteModal2 #loteData').css("border-color", "")
        $('#agendamentoLoteModal2 #loteHora').css("border-color", "")
        $('#agendamentoLoteModal2 #prof_id').css("border-color", "")
        $('#agendamentoLoteModal2 #prof_nome').css("border-color", "")
        $("#loteContinuar").css("display", agLoteNum == numAgTotal ? "none" : "");
        if (agLoteNum > numAgTotal || concluir) {
            console.log(agLote)

            $.post("/saude-beta/agenda/salvar-lote", agLote, function (data, status) {
                console.log(data + ' | ' + status)
                data = $.parseJSON(data);
                testandobrabo = data
                ShowConfirmationBox(
                    'Foram gerados ' + data.length + ' agendamentos.',
                    'Deseja listá-los?',
                    true, true, false,
                    function () { visualizar_agendamentos_em_lote(data) },
                    function () { location.reload(true); },
                    'Sim',
                    'Não'
                );
            });
        }
    }
}

function visualizar_agendamentos_em_lote(data) {
    $("#visualizarAgendamentosLoteModal > div").attr('class', 'modal-dialog modal-xl')
    $('#agendamentoLoteModal2').modal('hide'); $('#agendamentoLoteModal').modal('hide');
    $("#botao-lote-agendamento-modal").html('FECHAR')
    $("#visualizarAgendamentosLoteModal #conteudo-lote-agenda").empty();
    html = '<table class="table table-hover"> '
    html += '   <thead style="font-size:13px"> '
    html += '       <th width="10%" class="text-left">Dia da semana</th> '
    html += '       <th width="10%" class="text-left">Data</th> '
    html += '       <th width="10%" class="text-left">Hora</th> '
    html += '       <th width="20%" class="text-left">Plano</th> '
    html += '       <th width="25%" class="text-left">Associado</th> '
    html += '       <th width="25%" class="text-left">Membro</th> '
    html += '   </thead> '
    html += '   <tbody style="font-size:13px"> '
    $("#visualizarAgendamentosLoteModal #conteudo-lote-agenda").append(html);
    $zebr = 0
    data.forEach(agendamento => {
        date = agendamento.data
        date = date.substr(8, 2) + '/' + date.substr(5, 2) + '/' + date.substr(0, 4)
        if ($zebr % 2 == 0) html = ' <tr style="background-color: gainsboro;"> '
        else html = ' <tr>'
        $zebr++;
        console.log(agendamento.dia_semana)
        switch (agendamento.dia_semana.toString()) {
            case '1':
                html += ' <td width=10% class="text-left">Domingo</td>'
                break;
            case '2':
                html += ' <td width=10% class="text-left">Segunda</td>'
                break;
            case '3':
                html += ' <td width=10% class="text-left">Terça</td>'
                break;
            case '4':
                html += ' <td width=10% class="text-left">Quarta</td>'
                break;
            case '5':
                html += ' <td width=10% class="text-left">Quinta</td>'
                break;
            case '6':
                html += ' <td width=10% class="text-left">Sexta</td>'
                break;
            case '7':
                html += ' <td width=10% class="text-left">Sabado</td>'
                break;
        }
        html += '   <td width="10%" class="text-left">' + date + '</td> '
        html += '   <td width="10%" class="text-left">' + agendamento.hora.substr(0, 5) + '</td> '
        html += '   <td width="30%" class="text-left">' + agendamento.descr_plano + '</td> '
        html += '   <td width="25%" class="text-left">' + agendamento.descr_associado + '</td> '
        html += '   <td width="25%" class="text-left">' + agendamento.descr_profissional + '</td> '
        html += ' </tr> '
        $("#visualizarAgendamentosLoteModal #conteudo-lote-agenda > table > tbody").append(html);
    })
    html = '   </tbody> '
    html += ' </table> '
    $("#visualizarAgendamentosLoteModal #conteudo-lote-agenda").append(html)
    $("#conteudo-lote-agenda").append('<p style="text-align: end;width: 100%;">' + $("#conteudo-lote-agenda > table > tbody > tr").length + ' agendamentos </p>')
    $('#visualizarAgendamentosLoteModal').modal()
}

/*function gerarAgendamentosEmLote() {
    var dias_semana = $("#conteudo-lote-agenda").find('.dias-semana').find('select'),
        membros_id = $("#conteudo-lote-agenda").find('.membros').find('select'),
        horarios = $("#conteudo-lote-agenda").find('.horarios').find('select')
    dias_semana_ar = [],
        membros_id_ar = [],
        horarios_ar = [],
        dinicial = $('#agendamentoLoteModal #data-inicial').val(),
        dfinal = $('#agendamentoLoteModal #data-final').val()
    for (i = 0; i < dias_semana.length; i++) {
        if (($(dias_semana[i]).val() != null && $(dias_semana[i]).val() != 0) || $(membros_id[i]).val() != null || $(horarios[i]).val() != null) {
            dias_semana_ar.push($(dias_semana[i]).val())
            membros_id_ar.push($(membros_id[i]).val())
            horarios_ar.push($(horarios[i]).val())
        }
    }
    $.post(
        '/saude-beta/agenda/gerar-agendamentos-em-lote', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: $('#id_agendamento_lote').val(),
        dias_semana: dias_semana_ar,
        membros_id: membros_id_ar,
        horarios: horarios_ar,
        dinicial: dinicial,
        dfinal: dfinal
    }, function (data, status) {
        console.log(data + ' | ' + status)
        data = $.parseJSON(data)
        if (data.error) {
            alert(data.error)
        }
        else {
            ShowConfirmationBox(
                'Foram criados ' + data.agendados + ' agendamentos.',
                'Deseja conferir?',
                true, true, false,
                function () {
                    mostrar_agendamentos()
                    mostrar_agendamentos_semanal()

                    $("#agendamentoLoteModal > div").attr('class', 'modal-dialog modal-xl')
                    $("#botao-lote-agendamento-modal").attr('onclick', 'fecharAgendamentoLoteModal();')
                    $("#botao-lote-agendamento-modal").html('FECHAR')
                    $("#agendamentoLoteModal #conteudo-lote-agenda").empty();
                    html = '<table class="table table-hover"> '
                    html += '   <thead style="font-size:13px"> '
                    html += '       <th width="10%" class="text-left">Dia da semana</th> '
                    html += '       <th width="10%" class="text-left">Data</th> '
                    html += '       <th width="10%" class="text-left">Hora</th> '
                    html += '       <th width="20%" class="text-left">Plano</th> '
                    html += '       <th width="25%" class="text-left">Associado</th> '
                    html += '       <th width="25%" class="text-left">Membro</th> '
                    html += '   </thead> '
                    html += '   <tbody style="font-size:13px"> '
                    $("#agendamentoLoteModal #conteudo-lote-agenda").append(html);
                    $zebr = 0
                    data.agendamentos.forEach(agendamento => {
                        date = agendamento.data
                        date = date.substr(8, 2) + '/' + date.substr(5, 2) + '/' + date.substr(0, 4)
                        if ($zebr % 2 == 0) html = ' <tr style="background-color: gainsboro;"> '
                        else html = ' <tr>'
                        $zebr++;
                        console.log(agendamento.dia_semana)
                        switch (agendamento.dia_semana.toString()) {
                            case '1':
                                html += ' <td width=10% class="text-left">Domingo</td>'
                                break;
                            case '2':
                                html += ' <td width=10% class="text-left">Segunda</td>'
                                break;
                            case '3':
                                html += ' <td width=10% class="text-left">Terça</td>'
                                break;
                            case '4':
                                html += ' <td width=10% class="text-left">Quarta</td>'
                                break;
                            case '5':
                                html += ' <td width=10% class="text-left">Quinta</td>'
                                break;
                            case '6':
                                html += ' <td width=10% class="text-left">Sexta</td>'
                                break;
                            case '7':
                                html += ' <td width=10% class="text-left">Sabado</td>'
                                break;
                        }
                        html += '   <td width="10%" class="text-left">' + date + '</td> '
                        html += '   <td width="10%" class="text-left">' + agendamento.hora.substr(0, 5) + '</td> '
                        html += '   <td width="30%" class="text-left">' + agendamento.descr_plano + '</td> '
                        html += '   <td width="25%" class="text-left">' + agendamento.descr_associado + '</td> '
                        html += '   <td width="25%" class="text-left">' + agendamento.descr_profissional + '</td> '
                        html += ' </tr> '
                        $("#agendamentoLoteModal #conteudo-lote-agenda > table > tbody").append(html);
                    })
                    html = '   </tbody> '
                    html += ' </table> '
                    $("#agendamentoLoteModal #conteudo-lote-agenda").append(html)
                    $("#conteudo-lote-agenda").append('<p style="text-align: end;width: 100%;">' + $("#conteudo-lote-agenda > table > tbody > tr").length + ' agendamentos </p>')
                    mostrar_agendamentos();
                    mostrar_agendamentos_semanal();
                },
                function () {
                    $('#agendamentoLoteModal').modal('hide')
                    $('#criarAgendamntoModal').modal('hide')
                    mostrar_agendamentos();
                    mostrar_agendamentos_semanal();
                },
                'Sim',
                'Não'
            );
        }
    }
    )
}*/
function fecharBuscaAgenda(e) {
    if (!document.querySelector("#agenda_profissional").contains(e.target)) {
        console.log('s')
        $("#nome-membro-agenda-semanal").css('display', 'flex');
        $("#agenda_profissional").css('display', 'none');
        // window.removeEventListener('click', fecharBuscaAgenda);
    }
}
function controle_agenda_semanal() {
    if ($("#nome-membro-agenda-semanal").css('display') == 'flex') {
        $("#nome-membro-agenda-semanal").css('display', 'none')
        $("#agenda_profissional").css('display', '')
        autocomplete_agenda($("#agenda_profissional"))
        window.addEventListener('click', fecharBuscaAgenda, true);
    }
    else {
        $("#nome-membro-agenda-semanal").css('display', 'flex')
        $("#agenda_profissional").css('display', 'none')
    }
}
function control_membro_lote($i, $id_agendamento) {
    $("#profissional_id" + $i).empty();
    $("#profissional_id" + $i).append('<option value="">Buscando membros disponíveis...</option>');
    $.get(
        '/saude-beta/agenda/buscar-profissionais-disponiveis/' + $("#dia_semana_lote" + $i).val() + '/' + $id_agendamento,
        function (data, status) {
            console.log(data + ' | ' + status)
            data = $.parseJSON(data)
            a = data
            console.log(data)
            if (data.error) {
                alert(data.error)
            }
            else {
                if (data.descr.length == 0) {
                    $("#profissional_id" + $i).empty()
                    $("#profissional_id" + $i).append('Nenhum membro disponível')
                }
                else {
                    $("#profissional_id" + $i).empty();
                    for (i = 0; i < data.descr.length; i++) {
                        $("#profissional_id" + $i).append('<option value="' + data.id[i] + '">' + data.descr[i].toUpperCase() + '</option>')
                    }
                    $("#profissional_id" + $i).removeAttr('disabled')
                }
            }
        }
    )
}
function fecharAgendamentoLoteModal() {
    $("#botao-lote-agendamento-modal").attr('onclick', 'gerarAgendamentosEmLote();')
    $("#botao-lote-agendamento-modal").html('CONFIRMAR')
    $("#agendamentoLoteModal").modal('hide')
    $("#criarAgendamentoModal").modal('hide')
}
function control_hora_lote($i, $id_agendamento) {
    $('#horario_id' + $i).empty()
    $('#horario_id' + $i).append('<option value="">Buscando horários disponíveis</option>')
    $.get(
        '/saude-beta/agenda/buscar-hrs-por-profissional/' + $id_agendamento + '/' + $('#profissional_id' + $i).val() + '/' + $('#dia_semana_lote' + $i).val(),
        function (data, status) {
            console.log(data + ' | ' + status)
            data = $.parseJSON(data)
            if (data.error) {
                alert(data.error)
            }
            else {
                if (data.descr.length == 0) {
                    $("#horario_id" + $i).empty()
                    $("#horario_id" + $i).append('Nenhum membro disponível')
                }
                else {
                    $("#horario_id" + $i).empty();
                    for (i = 0; i < data.descr.length; i++) {
                        $("#horario_id" + $i).append('<option value="' + data.descr[i] + '">' + data.descr[i].toUpperCase() + '</option>')
                    }
                    $("#horario_id" + $i).removeAttr('disabled')
                }
            }
        }
    )
}
function control_lote($i) {
    if ($('#dia_semana_lote' + $i).val() == '0' || $('#dia_semana_lote' + $i).val() == null) {
        $('#horario_id' + $i).removeAttr('disabled')
        $('#profissional_id' + $i).attr('disabled', 'true')
        $("#profissional_id" + $i).empty();
    }
    else if ($("#horario_id" + $i).val() == '0' || $("#horario_id" + $i).val() == null) {
        $.get('/saude-beta/grade/listar-todos-horarios', {},
            function (data, status) {
                $('#horario_id' + $i).empty();
                data.forEach(horario => { $("#horario_id" + $i).append('<option value="' + horario.hora + '">' + horario.hora + '</option>') })
                $('#profissional_id' + $i).attr('disabled', 'true')
                $("#profissional_id" + $i).empty();
            })
    }
    else {
        $("#profissional_id" + $i).empty()
        $("#profissional_id" + $i).append("<option value='0'>Buscando membros...</option>")
        if ($("#agendamentoLoteModal #data-final").val() == '') data_final = 0
        else data_final = $("#agendamentoLoteModal #data-final").val()
        /*$.get(
            '/saude-beta/agenda/listar_profissionais_lote', {
            dia_semana: $("#dia_semana_lote" + $i).val(),
            hora: $("#horario_id" + $i).val(),
            id_agendamento: $id_agendamento,
            data_inicial: $('#agendamentoLoteModal #data-inicial').val(),
            data_final: data_final
        }, function (data, status) {
            console.log(data + ' | ' + status)
            if (data.error) {
                alert(data.error)
            }
            else {
                data = $.parseJSON(data)
                le = data;
                if (data.profissionais.length == 0) {
                    $("#profissional_id" + $i).attr('disabled', 'true')
                    $("#profissional_id" + $i).empty();
                }
                else {
                    $("#profissional_id" + $i).empty();
                    $("#profissional_id" + $i).removeAttr('disabled')
                    for (i = 0; i < data.profissionais.length; i++) {
                        html = '<option '

                        if (data.profissionais_ativos[i] == 'N') html += 'style="opacity: .7;color: #ff000091;" disabled="true"'

                        html += 'value="' + data.profissionais[i].id_profissional + '">' + data.profissionais[i].descr_profissional + '</option>'
                        $('#profissional_id' + $i).append(html)
                    }

                }
            }
        }
        )*/
    }
}
var le;
function encontrarContratos(finalizar, callback) {
    id = $("#criarAgendamentoModal #paciente_id").val()
    console.log('/saude-beta/pedido/listar-contratos-pessoa/' + id + '/' + finalizar + '/' + formatDataUniversal($('#criarAgendamentoModal #data').val()))
    $.get('/saude-beta/pedido/listar-contratos-pessoa/' + id + '/' + finalizar + '/' + formatDataUniversal($('#criarAgendamentoModal #data').val()), function (data) {
        $('#criarAgendamentoModal #id_contrato').empty()
        console.log(data)
        $('#criarAgendamentoModal #id_contrato').append('<option value="0">Selecionar contrato...</option>')
        data.forEach(contratos => {
            if (!data.data_validade) {
                let datac = contratos.data
                datac = datac[8] + datac[9] + '/' + datac[5] + datac[6] + '/' + datac[0] + datac[1] + datac[2] + datac[3];
                html = '<option value="' + contratos.id + '">'
                html += datac + ' | ' + contratos.descr
                html += '</option>'

                $('#criarAgendamentoModal #id_contrato').append(html);
            }
        });
        callback();
    });
}
function encontrarModalidades(callback) {
    if ($("#criarAgendamentoModal #id_tipo_procedimento").val() == 1) {
        encontrarModalidadesPlano()
    }
    $.get(
        '/saude-beta/agenda/listar-modalidades/'
    )
}
function encontrarPlanosContrato(callback) {

    id_contrato = $("#criarAgendamentoModal #id_contrato").val()
    data1 = $('#criarAgendamentoModal #data').val();
    data1 = data1[6] + data1[7] + data1[8] + data1[9] + '-' + data1[3] + data1[4] + '-' + data1[0] + data1[1]
    $.get('/saude-beta/pedido/listar-planos-pedido', {
        data: data1,
        id_contrato: id_contrato
    }, function (data) {
        $('#criarAgendamentoModal #id_plano').empty()
        $('#criarAgendamentoModal #id_plano').append('<option value="0">Selecionar plano...</option>')
        console.log(data)
        a = data
        data.forEach(plano => {
            html = '<option value="' + plano.id + '">'
            html += plano.descr + ' (Restam ' + (parseInt(plano.agendaveis) - parseInt(plano.agendados)) + ' atividades)'
            html += '</option>'
            $('#criarAgendamentoModal #id_plano').append(html);
        })

        // for(i = 0; i < data.planos_id.length; i++){

        //     html += data.planos_descr[i] + '   (' + data.agendados[i] + '/' + data.agendaveis[i] + ')'
        //     html += '</option>'
        //     $('#criarAgendamentoModal #id_plano').append(html);
        // }
        callback()
    })
}










// function encontrarContratos(finalizar, callback) {
//     id = $("#criarAgendamentoModal #paciente_id").val()
//     //if ($("#id_contrato").val() > 0 || isLote) {
//         $.get('/saude-beta/pedido/listar-contratos-pessoa/' + id + '/' + finalizar + '/' + formatDataUniversal($('#criarAgendamentoModal #data').val()), function (data) {
//             $('#id_contrato').empty()
//             $('#id_contrato').append('<option value="0" disabled selected>Selecionar contrato...</option>')
//             console.log(data);
//             data.forEach(contratos => {
//                 if (!data.data_validade && (contratos.max_atv_semana > 0 || !isLote)) {
//                     let datac = contratos.data
//                     datac = datac[8] + datac[9] + '/' + datac[5] + datac[6] + '/' + datac[0] + datac[1] + datac[2] + datac[3];
//                     html = '<option value="' + contratos.id + '">'
//                     html += datac + ' | ' + contratos.descr
//                     html += '</option>'

//                     $('#id_contrato').append(html);
//                 }
//             });
//             if (gC > 0) {
//                 $("#id_contrato").val(gC);
//                 setTimeout("control_criar_agendamento();", 100);
//             }
//             callback();
//         });
//     //}
// }
// function encontrarModalidades(callback) {
//     if ($("#criarAgendamentoModal #id_tipo_procedimento").val() == 1) {
//         encontrarModalidadesPlano()
//     }
//     $.get(
//         '/saude-beta/agenda/listar-modalidades/'
//     )
// }
var agLotePlanos = new Array();
// function encontrarPlanosContrato(callback) {

//     id_contrato = $("#criarAgendamentoModal #id_contrato").val()
//     data1 = $('#criarAgendamentoModal #data').val();
//     data1 = data1[6] + data1[7] + data1[8] + data1[9] + '-' + data1[3] + data1[4] + '-' + data1[0] + data1[1]
//     $.get('/saude-beta/pedido/listar-planos-pedido', {
//         data: data1,
//         id_contrato: id_contrato
//     }, function (data) {
//         $('#criarAgendamentoModal #id_plano').empty()
//         $('#criarAgendamentoModal #id_plano').append('<option value="0" disabled>Selecionar plano...</option>')
//         console.log(data)
//         a = data
//         data.forEach(plano => {
//             agLotePlanos[plano.id] = (parseInt(plano.agendaveis) - parseInt(plano.agendados))
//             html = '<option value="' + plano.id + '">'
//             html += plano.descr + ' (Restam ' + (parseInt(plano.agendaveis) - parseInt(plano.agendados)) + ' atividades)'
//             html += '</option>'
//             $('#criarAgendamentoModal #id_plano').append(html);
//         })

//         // for(i = 0; i < data.planos_id.length; i++){

//         //     html += data.planos_descr[i] + '   (' + data.agendados[i] + '/' + data.agendaveis[i] + ')'
//         //     html += '</option>'
//         //     $('#criarAgendamentoModal #id_plano').append(html);
//         // }
//         $('#criarAgendamentoModal #id_plano').val(0)
//         callback()
//     })
// }
function avancar_etapa_wo_consulta() {
    var etapa_atual = $('.wizard-pedido > .wo-etapa.selected').data().etapa;
    $('#avancar-pedido').show();
    $('#salvar-pedido').hide();

    if (etapa_atual == 1) {
        if ($('#consultaModal #pedido_paciente_id').val() == '') {
            alert('Aviso!\nCampo paciente inválido.');
            return;
        }
        // if ($('#pedidoModal #pedido_id_convenio').val() == 0) {
        //     alert('Aviso!\nA escolha de um convênio é obrigatória.');
        //     return;
        // }
        if ($('#consultaModal #pedido_profissional_exa_id').val() == '') {
            alert('Aviso!\nCampo "Consultor de vendas" inválido.');
            return;
        }
        // if ($('#pedidoModal #pedido_validade').val() == '' || $('#pedido_validade').val().length != 10) {
        //     alert('Aviso!\nCampo validade inválido.');
        //     return;
        // }
        ShowConfirmationBox(
            'Qual será o tipo da forma de pagamento?',
            '',
            true, true, false,
            function () { setar_tipo_forma_pag_consulta('V'); },
            function () { setar_tipo_forma_pag_consulta('P'); },
            'À Vista',
            'À Prazo'
        );

        $('#pedidoModal #pedido_forma_pag_tipo').trigger('change');

        document.querySelector("#avancar-consulta").style.display = 'block'
        document.querySelector("#salvar-consulta").style.display = 'none'

    } else if (etapa_atual == 2) {
        $('#avancar-pedido').removeClass('show');
        $('#avancar-pedido').attr("disabled", true);

        if ($.inArray($('#status-pedido').text(), ['Finalizado', 'Em Aprovação', 'Cancelado']) == -1) {
            $('#avancar-pedido').hide();
            $('#salvar-pedido').show();
        }
        montar_resumo_consulta();
    }
    $('#consultaModal [data-etapa="' + etapa_atual + '"]').removeClass('selected');
    $('#consultaModal [data-etapa="' + etapa_atual + '"]').addClass('success');
    $('#consultaModal [data-etapa="' + (etapa_atual + 1) + '"]').addClass('selected');
    $('#voltar-consulta').addClass('show');
    $('#voltar-consulta').attr("disabled", false);

    setTimeout(function () {
        $('[data-etapa="' + (etapa_atual + 1) + '"] input').first().focus();
    }, 50);
}

function montar_resumo_consulta() {
    var paciente_id = $('#pedido_paciente_id').val();
    var paciente_nome = $('#pedido_paciente_nome').val();
    var id_convenio = $('#pedido_id_convenio').val();
    var descr_convenio = $('#pedido_id_convenio option:selected').text();
    var profissional_exa_id = $('#pedido_profissional_exa_id').val();
    var profissional_exa_nome = $('#pedido_profissional_exa_nome').val();
    var obs = $('#pedido_obs').val();

    const now = new Date();
    let dia = now.getDay(),
        mes = now.getMonth(),
        ano = now.getFullYear();
    validade = dia.toString() + '/' + mes.toString() + '/' + ano.toString()

    document.querySelector("#avancar-consulta").style.display = 'none'
    document.querySelector("#salvar-consulta").style.display = 'block'

    document.querySelectorAll('.text-center > img').forEach(el => {
        el.style.display = 'none';
    })
    $('[data-resumo_paciente]').data('resumo_paciente', paciente_id).attr('data-resumo_paciente', paciente_id);
    $('[data-resumo_paciente]').html(paciente_nome);
    $('[data-resumo_paciente_convenio]').data('resumo_paciente_convenio', id_convenio).attr('data-resumo_paciente_convenio', id_convenio);
    $('[data-resumo_paciente_convenio]').html(descr_convenio);
    $('[data-resumo_profissional_exa]').data('resumo_profissional_exa', profissional_exa_id).attr('data-resumo_profissional_exa', profissional_exa_id);
    $('[data-resumo_profissional_exa]').html(profissional_exa_nome);
    $('[data-resumo_obs]').data('resumo_obs', obs).attr('data-resumo_obs', obs);
    if (obs != '') $('[data-resumo_obs]').html(obs);
    else $('[data-resumo_obs]').html('Sem Observação');
    $('#data-resumo_validade').html(validade);
    planos = [];
    $('#tabela-planos > tbody tr').each(function () {
        planos.push({
            id: $(this).find('[data-plano_id]').data().plano_id
        });
    })

}

function criarConsultaAntigoModal(id_agendamento) {
    if ($("#criarAgendamentoAntigoModal #id_tipo_procedimento").val() == 1) {
        if (window.confirm('Deseja confirmar o agendamento?')) {
            $.post(
                '/saude-beta/agenda-antiga/confirmar-agendamento', {
                _token: $("meta[name=csrf-token]").attr("content"),
                id_agendamento: $("#criarAgendamentoAntigoModal #id").val()
            }, function (data, status) {
                console.log(data + ' | ' + status)
                alert('Agendamento Confirmado!')
                $("#criarAgendamentoAntigoModal").modal('hide')
                mostrar_agendamentos_semanal()
                mostrar_agendamentos();
                pesquisarAgendamentosPendentes();
            }
            )
        }
    }
    else {
        resetar_modal_pedido_antigo()
        $("#pedidoAntigoModal #pedido_paciente_nome").val($("#criarAgendamentoAntigoModal #paciente_nome").val())
        $("#pedidoAntigoModal #pedido_paciente_id").val($("#criarAgendamentoAntigoModal #paciente_id").val())
        $("#pedidoAntigoModal #agenda_id").val(id_agendamento)

        $("#pedidoAntigoModal #pedido_id_convenio").empty()
        $("#pedidoAntigoModal #pedido_paciente_id").change()
        $("#pedidoAntigoModal #pedido_paciente_nome").attr("disabled", 'true')
        $("#pedidoAntigoModal").modal('show')
        $("#pedidoAntigoModal #pedido_id_convenio").append('<option value="0">Sem convênio...</option>')
    }
}

function criarConsultaModal(id_agendamento) {
    console.log(id_agendamento)
    if (campo_invalido("#criarAgendamentoModal #paciente_id", true) || campo_invalido("#criarAgendamentoModal #paciente_nome", false)) {
        alert('Campo "Associado" inválido!')
        return;
    }
    if (campo_invalido("#criarAgendamentoModal #id_tipo_procedimento", true)) {
        alert("Selecione um tipo de agendamento para prosseguir!")
        return;
    }
    if (campo_invalido("#criarAgendamentoModal #modalidade_id", true)) {
        alert("Selecione uma modalidade para prosseguir")
        return;
    }

    else if ($("#criarAgendamentoModal #id_tipo_procedimento").val() == 4) {

        if (campo_invalido("#criarAgendamentoModal #procedimento_id", true)) {
            alert("Selecione um plano para prosseguir!")
            return;
        }
    }
    else if ($("#criarAgendamentoModal #id_tipo_procedimento").val() == 1) {

        if (campo_invalido("#criarAgendamentoModal #id_contrato", true)) {
            alert("Selecionar um contrato é obrigatório para este tipo de agendamento")
            return;
        }
        if (campo_invalido("#criarAgendamentoModal #id_plano")) {
            alert("Selecione um plano do contrato")
            return;
        }
    }
    else if ($("#criarAgendamentoModal #id_tipo_procedimento").val() == 5) {

        if (campo_invalido("#criarAgendamentoModal #modalidade_id", true)) {
            alert("Selecione uma modalidade para prosseguir!")
            return;
        }
    }

    $.post(
        '/saude-beta/agenda/salvar_op_bordero', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_agendamento: $("#criarAgendamentoModal #id").val(),
        bordero: $("#criarAgendamentoModal #bordero_b").prop('checked')
    }, function (data, status) {
        console.log(data + ' | ' + status)
    }
    )
    $.get(
        '/saude-beta/agenda/faturar/' + id_agendamento,
        function (data, status) {
            data = $.parseJSON(data);
            console.log(data + ' | ' + status)
            if ($("#criarAgendamentoModal #id_tipo_procedimento").val() == 4) {

                $("#pedidoModal #pedido_paciente_nome").val(data.descr_pessoa)
                $("#pedidoModal #pedido_paciente_id").val(data.id_pessoa)
                $("#pedidoModal #agenda_id").val(id_agendamento)

                $("#pedidoModal #pedido_id_convenio").empty()
                if (data.descr_convenio != null) {
                    $("#pedidoModal #pedido_id_convenio").append('<option value="' + data.id_convenio + '">' + data.descr_convenio + '</option>')
                }
                else {
                    $("#pedidoModal #pedido_id_convenio").append('<option value="0">Sem convênio...</option>')
                }

                $("#pedidoModal #pedido_paciente_nome").attr("disabled", 'true')
                $("#pedidoModal #pedido_id_convenio").attr('disabled', 'true')

                if (data.id_encaminhamento != 0) callVerEncaminhamentoDetalhe(84, false);
                $.get(
                    "/saude-beta/caixa/verificar-situacao",
                    {},
                    function (data, status) {
                        console.log(data + " | " + status);
                        data = $.parseJSON(data);
                        auxiliar = data;
                        if (data.situacao == "X") {
                            alert("Seu usuário não está vinculado ao caixa desta empresa!");
                        }
                        else {
                            if (data.situacao == "A" && data.abrir_fechar == false) {

                                resetar_modal_pedido()
                                $("#pedidoModal").modal('show')
                            }
                            else {
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
                )
            }
            else {
                if (window.confirm('Deseja finalizar agendamento?')) {
                    console.log(id_agendamento)
                    $.post(
                        '/saude-beta/agenda/finalizar-agendamento', {
                        _token: $("meta[name=csrf-token]").attr("content"),
                        id: id_agendamento,
                    }, function (data, status) {
                        a = data;
                        console.log(data + ' | ' + status)
                        if (!isNaN(data)) {
                            alert('Agendamento confirmado');
                            $("#criarAgendamentoModal").modal('hide')
                            mostrar_agendamentos();
                            mostrar_agendamentos_semanal();
                            pesquisarAgendamentosPendentes()
                        }
                        else alert('erro')
                    }
                    )
                }
            }
        })
}

// function criarConsultaModal(id_agendamento){
//     console.log(id_agendamento) 
//         $.get('/saude-beta/encaminhamento/listar-tabelaencaminhamento/' + id_agendamento,
//         function (data, status){
//         var id_encaminhamento = data;

//         if(id_encaminhamento != 0){

//             $("#tabelas_encaminhamento_modal").modal('show');

//             $.get('/saude-beta/encaminhamento/mostrar-tabelaencaminhamento/' + id_encaminhamento,
//             function (data){
//                 console.log(data)
//                 var i = 0;
//                 $('#tbody-encaminhamento-habilitacoes').empty()
//                 $('#tbody-encaminhamento').empty()
//                 data.forEach(tipo=>{

//                 if(tipo.tipo == 'habilitacao'){
//                     ;
//                     html  = '<tr> '
//                     html += '    <td data-vo2="'+ tipo.valor1 +'" width="40%" class="text-left">'+ tipo.valor1 +'</td> '
//                     html += '    <td data-obs="'+ tipo.valor2 +'" width="30%" class="text-left">'+ tipo.valor2 +'</td> '
//                     html += '    <td data-infoAdicional="'+ tipo.valor3 +'" width="30%" class="text-left">'+ tipo.valor3 +'</td> '
//                     html += '</tr> '
//                     $('#tbody-encaminhamento-habilitacoes').append(html);

//                 }

//                 if(tipo.tipo == 'reabilitacao'){

//                     html  = '<tr> '
//                     html += '    <td data-area="'+ tipo.valor1 +'" width="40%" class="text-left">'+ tipo.valor1 +'</td> '
//                     html += '    <td data-qtd_semana="'+ tipo.valor2 +'" width="30%" class="text-left">'+ tipo.valor2 +'</td> '
//                     html += '    <td data-tempo="'+ tipo.valor3 +'" width="30%" class="text-left">'+ tipo.valor3 +'</td> '
//                     html += '</tr> '
//                     $('#tbody-encaminhamento').append(html)

//                 }



//                 })


//             }



//                     //document.querySelectorAll('#tbody-encaminhamento > tr').forEach(el => {
//                     //    tipo.push('reabilitacao')
//                     //    valor1.push($(el).find('[data-area]').data().area)
//                     //    valor2.push($(el).find('[data-qtd_semana]').data().qtd_semana)
//                    //     valor3.push($(el).find('[data-tempo]').data().tempo)

//                   //  })

//                     //document.querySelectorAll('#tbody-encaminhamento-habilitacoes > tr').forEach(el =>{
//                    //     tipo.push('habilitacao')
//                    //     valor1.push($(el).find('[data-vo2]').data().vo2)
//                   //      valor2.push($(el).find('[data-obs]').data().obs)
//                     //    valor3.push($(el).find('[data-infoadicional]').data().infoadicional)
//                   //  })
//                 )
//         }

//                 if(campo_invalido("#criarAgendamentoModal #paciente_id", true) || campo_invalido("#criarAgendamentoModal #paciente_nome", false)){
//                     alert('Campo "Associado" inválido!')
//                     return;
//                 }
//                 if(campo_invalido("#criarAgendamentoModal #id_tipo_procedimento", true)) {
//                     alert("Selecione um tipo de agendamento para prosseguir!")
//                     return;
//                 }
//                 if(campo_invalido("#criarAgendamentoModal #modalidade_id", true)){
//                     alert("Selecione uma modalidade para prosseguir")
//                     return;
//                 }

//                 else if ($("#criarAgendamentoModal #id_tipo_procedimento").val() == 4){

//                     if (campo_invalido("#criarAgendamentoModal #procedimento_id", true)){
//                         alert("Selecione um plano para prosseguir!")
//                         return;
//                     }
//                 }
//                 else if ($("#criarAgendamentoModal #id_tipo_procedimento").val() == 1){

//                     if(campo_invalido("#criarAgendamentoModal #id_contrato", true)){
//                         alert("Selecionar um contrato é obrigatório para este tipo de agendamento")
//                         return;
//                     }
//                     if(campo_invalido("#criarAgendamentoModal #id_plano")){
//                         alert("Selecione um plano do contrato")
//                         return;
//                     }
//                 }
//                 else if ($("#criarAgendamentoModal #id_tipo_procedimento").val() == 5){

//                     if (campo_invalido("#criarAgendamentoModal #modalidade_id", true)){
//                         alert("Selecione uma modalidade para prosseguir!")
//                         return;
//                     }
//                 }

//                 $.post(
//                     '/saude-beta/agenda/salvar_op_bordero', {
//                         _token: $("meta[name=csrf-token]").attr("content"),
//                         id_agendamento: $("#criarAgendamentoModal #id").val(),
//                         bordero: $("#criarAgendamentoModal #bordero_b").prop('checked')
//                     },function(data,status){
//                         console.log(data + ' | ' + status)
//                     }
//                 )
//                 $.get(
//                     '/saude-beta/agenda/faturar/'+ id_agendamento,
//                     function(data,status){
//                         data = $.parseJSON(data);
//                         console.log(data + ' | ' + status)

//                         if($("#criarAgendamentoModal #id_tipo_procedimento").val() == 4 && id_encaminhamento == 0){
//                             $("#pedidoModal #pedido_paciente_nome").val(data.descr_pessoa)
//                             $("#pedidoModal #pedido_paciente_id").val(data.id_pessoa)
//                             $("#pedidoModal #agenda_id").val(id_agendamento)

//                             $("#pedidoModal #pedido_id_convenio").empty()
//                             if(data.descr_convenio != null){
//                                 $("#pedidoModal #pedido_id_convenio").append('<option value="'+ data.id_convenio +'">'+ data.descr_convenio +'</option>')
//                             }
//                             else{
//                                 $("#pedidoModal #pedido_id_convenio").append('<option value="0">Sem convênio...</option>')
//                             }

//                             $("#pedidoModal #pedido_paciente_nome").attr("disabled", 'true')
//                             $("#pedidoModal #pedido_id_convenio").attr('disabled', 'true')

//                           //  resetar_modal_pedido()
//                             $("#pedidoModal").modal('show')


//                         }
//                         else if(data==0) {
//                             if(window.confirm('Deseja finalizar agendamento?')) {
//                                 console.log(id_agendamento)
//                                 $.post(
//                                     '/saude-beta/agenda/finalizar-agendamento', {
//                                         _token: $("meta[name=csrf-token]").attr("content"),
//                                         id: id_agendamento,
//                                     },function(data,status){
//                                         a = data;
//                                         console.log(data + ' | ' + status)
//                                         if (!isNaN(data)){
//                                             alert('Agendamento confirmado');
//                                             $("#criarAgendamentoModal").modal('hide')
//                                             mostrar_agendamentos();
//                                             mostrar_agendamentos_semanal();
//                                             pesquisarAgendamentosPendentes()
//                                         }
//                                         else alert('erro')
//                                     }
//                                 )
//                             }
//                         }
//                     })
//       //  document.querySelectorAll('#tbody-encaminhamento > tr').forEach(el => {
//         //    tipo.get(tipo)
//        //     valor1.get($(el).data().area)
//         //    valor2.get($(el).data().qtd_semana)
//          //   valor3.get($(el).data().tempo)
//           //      })
//            }


//         )

//     }
function salvar_tabela_precos1() {
    var lista_empresas = []
    document.querySelectorAll("#tabelaPrecosModal #lista-empresa select").forEach(element => {
        lista_empresas.push(element.value)
    });
    $.post('/saude-beta/tabela-precos/salvar', {
        _token: $("meta[name=csrf-token]").attr('content'),
        id: $("#tabelaPrecosModal #id").val(),
        descr: $("#tabelaPrecosModal #descr").val(),
        status: $("#tabelaPrecosModal #status").val(),
        vigencia: $("#tabelaPrecosModal #vigencia").val(),
        max_atv_semana: $("#tabelaPrecosModal #max_atv_semana").val(),
        max_atv: $("#tabelaPrecosModal #max_atv").val(),
        valor: $("#tabelaPrecosModal #valor").val(),
        npessoas: $("#tabelaPrecosModal #npessoas").val(),
        desc_associado: $("#tabelaPrecosModal #desc_associado").val(),
        repor_som_mes: $("#tabelaPrecosModal #repor_som_mes").prop('checked'),
        usar_desconto_padrao: $("#tabelaPrecosModal #usar_desconto_padrao").prop('checked'),
        gerar_contrato: $('#tabelaPrecosModal #gerar_contrato').prop('checked'),
        tipo_agendamento: $("#tabelaPrecosModal #tipo_agendamento").val(),
        descr_contrato: $('#tabelaPrecosModal #descr_contrato').val(),
        empresas: lista_empresas,
    },
        function (data, status) {
            console.log(data + ' | ' + status)
            if (data.error) {
                alert(data.error)
            }
            else {
                $("#tabelaPrecosModal").modal("hide")
                location.reload(true)
            }
        })
}

function salvar_consulta() {
    var id = $('#pedido_id').val(),
        tipo_forma_pag = $('#pedido_forma_pag_tipo').val(),
        id_paciente = $('[data-resumo_paciente]').data().resumo_paciente,
        id_convenio = $('[data-resumo_paciente_convenio]').data().resumo_paciente_convenio,
        data = $('#data-resumo_validade').html(),
        id_profissional_exa = $('[data-resumo_profissional_exa]').data().resumo_profissional_exa,
        obs = $('[data-resumo_obs]').data().resumo_obs,
        planos = [],
        formas_pag = [];

    data_validade = data[6] + data[7] + data[8] + data[9] + '-' + data[3] + data[4] + '-' + data[0] + data[1];

    $('#table-pedido-forma-pag-resumo tbody tr').each(function () {
        formas_pag.push({
            id_forma_pag: $(this).find('[data-forma_pag]').data().forma_pag,
            id_financeira: $(this).find('[data-financeira_id]').data().financeira_id,
            parcela: $(this).find('[data-forma_pag_parcela]').data().forma_pag_parcela,
            forma_pag_valor: String($(this).find('[data-forma_pag_valor]').data().forma_pag_valor).replace(',', '.'),
            data_vencimento: $(this).find('[data-pedido_data_vencimento]').data().pedido_data_vencimento
        });
    });

    $.post(
        '/saude-beta/agenda/salvar-faturamento', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: id,
        tipo_forma_pag: tipo_forma_pag,
        id_paciente: id_paciente,
        id_convenio: id_convenio,
        id_profissional_exa: id_profissional_exa,
        data_validade: data_validade,
        obs: obs,
        formas_pag: formas_pag,
        planos: planos
    },
        function (data, status) {
            console.log(status + " | " + data);
            if (data.error != undefined) {
                alert(data.error);
            } else {
                location.reload(true)
            }
        }
    );
}


function voltar_etapa_wo_consulta() {
    var etapa_atual = $('.wizard-pedido > .wo-etapa.selected').data().etapa;
    if (etapa_atual == 2) {
        $('#voltar-consulta').removeClass('show');
        $('#voltar-consulta').attr("disabled", true);
    }
    if (etapa_atual == 4) {
        $('#avancar-consulta').addClass('show');
        $('#avancar-consulta').attr("disabled", false);
        $('#avancar-consulta').show();
        $('#salvar-consulta').hide();
    }
    $('#consultaModal [data-etapa="' + etapa_atual + '"]').removeClass('selected');
    $('#consultaModal [data-etapa="' + (etapa_atual - 1) + '"]').removeClass('success');
    $('#consultaModal [data-etapa="' + (etapa_atual - 1) + '"]').addClass('selected');
    setTimeout(function () {
        $('#consultaModal [data-etapa="' + (etapa_atual - 1) + '"] input').first().focus();
    }, 50);
}
function setar_tipo_forma_pag_consulta(_tipo) {
    $.get('/saude-beta/forma-pag/listar/' + _tipo, function (data) {
        data = $.parseJSON(data);
        var html = '';
        data.forEach(forma_pag => {
            if (forma_pag.id != 102) {
                html += '<option value="' + forma_pag.id + '">';
                html += forma_pag.descr;
                html += '</option>';    
            }
        });
        $('#pedido_forma_pag').html(html).trigger('change');
    });

    var valor_pendente = 0;
    if (_tipo == 'V') {
        $('#pedido_forma_pag_parcela').val(1);
        $('#pedido_forma_pag_parcela').parent().hide();
        $('#pedido_forma_pag_valor').val($(this).data().preco_vista);
        $('#pedido_data_vencimento').val(moment().format('DD/MM/YYYY'));

    } else {
        $('#pedido_forma_pag_parcela').parent().show();
        $('#pedido_forma_pag_parcela').val(1);
        // $('#pedido_forma_pag_valor').val($(this).data().preco_prazo);
        $('#pedido_data_vencimento').val(moment().add(30, 'days').format('DD/MM/YYYY'));

    }
    $('#table-pedido-forma-pag [data-total_pag_pendente]')
        .data('total_pag_pendente', valor_pendente)
        .attr('data-total_pag_pendente', valor_pendente)
        .html('Valor Total dos procedimentos - R$ ' + parseFloat(valor_pendente).toFixed(2).toString().replace('.', ','));

    $('#pedido_forma_pag_tipo').val(_tipo);
    if ($('#pedido_forma_pag_tipo').val() != _tipo) {
        $('#table-pedido-forma-pag > tbody').empty()
        $('#table-pedido-forma-pag-resumo > tbody').empty()
    }
    att_pedido_total_proc_pagamento();
}
function abrircongelarContrato(id_contrato) {
    $('#congelarPedidoModal').modal('show')
    $('#congelarPedidoModal #id_pedido').val(id_contrato)
}
function congelarContrato() {
    var id_contrato = $('#congelarPedidoModal #id_pedido').val(),
        data = $('#congelarPedidoModal #data_congelar').val();

    data = data[6] + data[7] + data[8] + data[9] + '-' + data[3] + data[4] + '-' + data[0] + data[1];

    $.post(
        '/saude-beta/pedido/congelar', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id: id_contrato,
        data: data
    }, function (data, status) {
        console.log(data + ' | ' + status)
        console.log(data)
        if (data == "true") {
            location.reload(true);
        }
        else {
            alert('error')
        }
    }
    )
}
function abrirModalPessoasPlano(id_plano, id_linha, total_pessoas) {
    $('#pedidoPessoasModal').modal('show')
    $('#pedidoPessoasModal #id').val(id_plano)
    $('#pedidoPessoasModal #id_linha').val(id_linha)
    $('#pedidoPessoasModal #total_pessoas').val(total_pessoas)
}
function descongelar_contrato(id) {
    if (window.confirm('Deseja retomar este contrato?')) {
        $.post(
            '/saude-beta/pedido/descongelar', {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: id
        }, function (data, status) {
            console.log(data + ' | ' + status)
            if (data == "true") {
                alert('Contrato retomado')
                location.reload(true)
            }
        }
        )
    }
}
function add_pessoa_pedido_plano() {
    var descr_pessoa = $('#pedidoPessoasModal #paciente_nome').val(),
        id_pessoa = $('#pedidoPessoasModal #paciente_id').val(),
        total_pessoas = $('#pedidoPessoasModal #total_pessoas').val(),
        id_linha = $('#pedidoPessoasModal #id_linha').val(),
        linha = document.querySelector('#pedidoModal ' + id_linha + ' > .n_pessoas'),
        pessoas_linha = $('#pedidoModal ' + id_linha + ' > .lista_id_pessoas').val()


    if (document.querySelectorAll("#pedidoPessoasModal #table-pessoas-pedido > tbody > tr").length <= total_pessoas - 1) {
        if (document.querySelectorAll("#pedidoPessoasModal #table-pessoas-pedido > tbody > tr").length == total_pessoas - 1) { $("#adicionar-pessoas-plano").removeAttr('disabled') }
        html = '<tr style="background: white !important" id="lin' + $('#table-pessoas-pedido > tbody > tr').length + '">'
        html += ' <td width="88%">'
        html += descr_pessoa;
        html += '<input type="hidden" value="' + id_pessoa + '">'
        html += ' </td>'
        html += ' <td> '
        html += '       <img src="img/lixeira-de-reciclagem.png" style="max-width: 100%;cursor: pointer;" onclick="excluir_pessoa_lista(' + "'#lin" + $('#table-pessoas-pedido > tbody > tr').length + "'" + ')">'
        html += ' </td>'
        $('#pedidoPessoasModal #table-pessoas-pedido > tbody').append(html)

        let pessoas_selecionadas = document.querySelectorAll("#pedidoPessoasModal #table-pessoas-pedido > tbody > tr").length
        linha.innerHTML = pessoas_selecionadas + '/' + total_pessoas;
        console.log(linha)

        $('#pedidoPessoasModal #paciente_nome').val('')
        $('#pedidoPessoasModal #paciente_id').val('')
    }
    else {
        alert("A quantidade de pessoas por plano foi excedida")
    }
}
function gravarIdsPessoas() {
    lista = []
    id_linha = $('#pedidoPessoasModal #id_linha').val()
    document.querySelectorAll('#pedidoPessoasModal td > input').forEach(el => {
        lista.push(el.value)
    })
    $('#pedidoModal ' + id_linha + ' > .lista_id_pessoas').val(lista.toString());
    $("#pedidoPessoasModal").modal('hide')
    $('#pedidoPessoasModal #table-pessoas-pedido > tbody').empty()

}
var modAnt, modTmp, modT = 0;
function agPacienteChange(el) {
    if (isLote && el.value != "" && el.value != modAnt && mod < 10) {
        criarModalAgendamentoCall(true);
        modTmp = setInterval(function () {
            $("#criarAgendamentoModal #id_contrato").val(0);
            if (modT == 10) clearInterval(modTmp);
            else modT++;
        }, 100);
        return;
    }
    if (el.value != '' && mod < 2) {
        control_criar_agendamento(() => { console.log('a') });
        mod++;
        $('#criarAgendamentoModal #id_tipo_procedimento').val(0);
        if (isLote) $('#id_tipo_procedimento').val(1)
        setTimeout(function () {
            $("#criarAgendamentoModal #id_contrato").val(0);
        }, 100);
    }
}





function control_criar_agendamento_lote() {
    tipo_procedimento = $("#criarAgendamentoModal #id_tipo_procedimento").val()
    $("#id_tipo_procedimento").attr("disabled", $("#criarAgendamentoModal #paciente_id").val() == "");
    $("#id_tipo_procedimento *").each(function () {
        $(this).attr("disabled", $("#criarAgendamentoModal #paciente_id").val() == "");
    });
    if (isLote && $("#criarAgendamentoModal #paciente_id").val() != "") {
        $("#id_tipo_procedimento").val(1);
    }
    contrato = $("#criarAgendamentoModal #id_contrato")
    plano = $("#criarAgendamentoModal #id_plano")
    plano_pre = $("#criarAgendamentoModal #procedimento_id")
    convenio = $("#criarAgendamentoModal #convenio_id")

    if (campo_invalido("#criarAgendamentoModal #convenio_id")) {
        setTimeout(function () {
            $("#criarAgendamentoModal #paciente_id").change();
        }, 5);
    }
    if (!campo_invalido("#criarAgendamentoModal #paciente_id", true)) {
        if (campo_invalido("#criarAgendamentoModal #id_tipo_procedimento", true) && !isLote) {
            contrato.val('').empty().parent().hide()
            plano.val('').empty().parent().hide()
            plano_pre.val('').empty().parent().hide()
            convenio.val('').empty().parent().hide()
        }
        switch (parseInt(tipo_procedimento)) {
            case 1:
                plano_pre.val('').empty().parent().hide()
                convenio.val('').empty().parent().hide()

                contrato.parent().show()
                $("#criarAgendamentoModal #id_contrato").attr("disabled", false)
                $("#criarAgendamentoModal #id_contrato *").each(function () {
                    $(this).attr("disabled", false);
                });

                if (!campo_invalido("#criarAgendamentoModal #id_contrato", true)) {
                    if (campo_invalido("#criarAgendamentoModal #id_plano", true)) {
                        encontrarPlanosContrato(() => {
                            console.log(false)
                            if (isLote) plano.attr("disabled", false)
                        })
                    }
                    plano.parent().show()
                } else {
                    encontrarContratos(false, () => {
                        if (!isLote) plano.val('').empty().parent().hide();
                    })
                }
                if (!campo_invalido("#criarAgendamentoModal #id_contrato", true)) {
                    mostrarModalidadesPorPlano(plano.val(), () => {
                        $("#criarAgendamentoModal #modalidade_id").attr("disabled", true);
                        $("#criarAgendamentoModal #modalidade_id").val(0);
                    });
                }
                break;

            case 4:
                plano_pre.parent().show()
                convenio.parent().show()
                const lista = ["#criarAgendamentoModal #procedimento_id", "#criarAgendamentoModal #convenio_id"];
                for (var i = 0; i < lista.length; i++) {
                    $(lista[i]).attr("disabled", false)
                    $(lista[i] + " *").each(function () {
                        $(this).attr("disabled", false);
                    });
                }

                if (campo_invalido("#criarAgendamentoModal #procedimento_id", true)) {
                    encontrarPlanosPreAgendamento(() => {
                        console.log(false)
                    })
                }
                plano.val('').empty().parent().hide()
                contrato.val('').empty().parent().hide()
                mostrarModalidadesPorPlano(plano_pre.val(), () => {
                    $("#criarAgendamentoModal #modalidade_id").attr("disabled", true)
                    $("#criarAgendamentoModal #modalidade_id").val(0);
                })
                break;
            case 5:
                plano_pre.val('').empty().parent().hide()
                plano.val('').empty().parent().hide()
                contrato.val('').empty().parent().hide()
                convenio.val('').empty().parent().hide()
                $("#criarAgendamentoModal #modalidade_id").empty()
                $("#criarAgendamentoModal #modalidade_id").attr("disabled", false)
                $("#criarAgendamentoModal #modalidade_id *").each(function () {
                    $(this).attr("disabled", false);
                });
                $('#criarAgendamentoModal #modalidade_id').append("<option value='0' disabled='disabled' selected>Selecionar modalidade...</option>")
                $.get('/saude-beta/procedimento/listar', {},
                    function (data, status) {
                        console.log(data + ' | ' + status)
                        data = $.parseJSON(data);
                        $('#criarAgendamentoModal #modalidade_id').empty()
                        data.forEach(modalidade => {
                            html = '<option value="' + modalidade.id + '">'
                            html += modalidade.descr
                            html += '</option>'
                            $('#criarAgendamentoModal #modalidade_id').append(html)
                        })
                    }
                )
                break;

            default:
                plano_pre.val('').empty().parent().hide()
                plano.val('').empty().parent().hide()
                contrato.val('').empty().parent().hide()
                convenio.val('').empty().parent().hide()
                $("#criarAgendamentoModal #modalidade_id").empty()
                $('#criarAgendamentoModal #modalidade_id').append("<option value='0' disabled='disabled' selected>Selecionar modalidade...</option>")
                $("#id_tipo_procedimento *").each(function () {
                    $(this).attr("disabled", $("#criarAgendamentoModal #paciente_id").val() == "");
                });
        }

    } else if (!isLote) {
        contrato.val('').empty().parent().hide()
        plano.val('').empty().parent().hide()
        plano_pre.val('').empty().parent().hide()
    }
    $("#criarAgendamentoModal select").each(function () {
        if ($(this)[0][0] === undefined) {
            switch ($(this).prop("id")) {
                case "id_contrato":
                    $(this).append('<option value="0" disabled>Selecionar contrato...</option>');
                    break;
                case "id_plano":
                    $(this).append('<option value="0" disabled>Selecionar plano...</option>');
                    break;
                case "procedimento_id":
                    $(this).append('<option value="0" disabled>Selecionar plano...</option>');
                    break;
            }
        } else $($(this)[0][0]).attr("disabled", true);
    });
    $("#criarAgendamentoModal #modalidade_id").val(0);
    if (tipo_procedimento != 5) habMod();
    if (isLote) {
        $("#criarAgendamentoModal #id_plano").val(0);
        /*try {
            $("#criarAgendamentoModal #id_contrato").val($("#criarAgendamentoModal #id_contrato").children()[1].value);
        } catch(err) {}*/
        $(".agendamentoLote").each(function () {
            $(this).css("display", "block");
        });
        /*setTimeout(function() {
            $("#criarAgendamentoModal #id_contrato").val(0);
        }, 100)*/
    }
}















function control_criar_agendamento(callback) {
    tipo_procedimento = $("#criarAgendamentoModal #id_tipo_procedimento").val()

    contrato = $("#criarAgendamentoModal #id_contrato")
    plano = $("#criarAgendamentoModal #id_plano")
    plano_pre = $("#criarAgendamentoModal #procedimento_id")
    convenio = $("#criarAgendamentoModal #convenio_id")

    if (campo_invalido("#criarAgendamentoModal #convenio_id")) {
        $("#criarAgendamentoModal #paciente_id").change()
    }
    if (!campo_invalido("#criarAgendamentoModal #paciente_id", true)) {
        if (campo_invalido("#criarAgendamentoModal #id_tipo_procedimento", true)) {
            contrato.val('').empty().parent().hide()
            plano.val('').empty().parent().hide()
            plano_pre.val('').empty().parent().hide()
            convenio.val('').empty().parent().hide()
        }
        $(".infoEnc").each(function() {
            $(this).css("display", "none");
        })
        switch (parseInt(tipo_procedimento)) {
            case 1:
                plano_pre.val('').empty().parent().hide()
                convenio.val('').empty().parent().hide()

                contrato.parent().show()
                if (!campo_invalido("#criarAgendamentoModal #id_contrato", true)) {
                    if (campo_invalido("#criarAgendamentoModal #id_plano", true)) {
                        encontrarPlanosContrato(() => {
                            console.log(false)
                        })
                    }
                    plano.parent().show()
                }
                else {
                    encontrarContratos(false, () => {
                        plano.val('').empty().parent().hide()
                    })
                }
                mostrarModalidadesPorPlano(plano.val(), callback)
                break;

            case 4:
                plano_pre.parent().show()
                convenio.parent().show()
                if (campo_invalido("#criarAgendamentoModal #procedimento_id", true)) {
                    encontrarPlanosPreAgendamento(() => {
                        console.log(false)
                    })
                }
                plano.val('').empty().parent().hide()
                contrato.val('').empty().parent().hide()
                plano
                mostrarModalidadesPorPlano(plano_pre.val(), callback)
                carregaEncDisponiveis2();
                $(".infoEnc").each(function() {
                    $(this).css("display", "block");
                });
                break;
            case 5:
                plano_pre.val('').empty().parent().hide()
                plano.val('').empty().parent().hide()
                contrato.val('').empty().parent().hide()
                convenio.val('').empty().parent().hide()
                $("#criarAgendamentoModal #modalidade_id").empty()
                $.get('/saude-beta/procedimento/listar', {},
                    function (data, status) {
                        console.log(data + ' | ' + status)
                        data = $.parseJSON(data);
                        $('#criarAgendamentoModal #modalidade_id').empty()
                        data.forEach(modalidade => {
                            html = '<option value="' + modalidade.id + '">'
                            html += modalidade.descr
                            html += '</option>'
                            $('#criarAgendamentoModal #modalidade_id').append(html)
                        })
                    })
                break;

            default:
                plano_pre.val('').empty().parent().hide()
                plano.val('').empty().parent().hide()
                contrato.val('').empty().parent().hide()
                convenio.val('').empty().parent().hide()
                $("#criarAgendamentoModal #modalidade_id").empty()
        }

    }
    else {
        contrato.val('').empty().parent().hide()
        plano.val('').empty().parent().hide()
        plano_pre.val('').empty().parent().hide()
    }
    var data = espEnc;
    var resultado = "";
    for (var i = 0; i < data.length; i++) resultado += "<option value = '" + data[i].id + "'>" + data[i].descr + "</option>";
    $('#agenda_enc_esp').html(resultado);
    $('#criarAgendamentoModal').modal('show');
    botoesEncAgenda();
    if (tipo_procedimento != 4) {
        setTimeout(function() {
            $("#infEncBox").css("display", "none");
            $(".infoEnc").each(function() {
                $(this).css("display", "none");
            });
            $(".infoEnc *").each(function() {
                $(this).removeAttr("disabled");
            });
        }, 200);
    }
}

function botoesEncAgenda() {
    $("#agenda_enc_esp").parent().addClass("col-md-6");
    $("#agenda_enc_esp").parent().removeClass("col-md-12");
    if (espEnc.length > 1) {
        $('#agenda_enc_esp').parent().css("display", "block");
        $('#enc_arquivo-btn').parent().addClass("col-md-6");
        $('#enc_arquivo-btn').parent().removeClass("col-md-12");
    } else {
        $('#agenda_enc_esp').parent().css("display", "none");
        $('#enc_arquivo-btn').parent().removeClass("col-md-6");
        $('#enc_arquivo-btn').parent().addClass("col-md-12");
    }
}
function carregaEncDisponiveis2() {
    $("#enc_label").css("display", "block");
    document.getElementById("agenda_encaminhante_nome").parentElement.style = "margin-bottom:0;display:block";
    var especialidades = new Array();
    for (var i = 0; i < espEnc.length; i++) especialidades.push(espEnc[i].id);
    $.get('/saude-beta/encaminhamento/solicitacao/listar', {
        id_pessoa : $('#paciente_id').val(),
        todos : "N",
        esp : especialidades.join(","),
        profissional : $('#selecao-profissional > .selected').data().id_profissional
    }, function(data) {
        $('#tab_solicitacoes').parent().css("display", "none");
        $('#tab_solicitacoes').parent().prev().css("display", "none");
        $('#tab_solicitacoes > tbody').empty();
        if (data != "") {
            data = $.parseJSON(data);
            html = "<tr id = 'solTr0' " + 
                "data-enc_descr='' " +
                "data-enc_id='' " +
                "data-esp_descr='' " +
                "data-esp_id='' " +
                "data-cid_descr='' " +
                "data-cid_id=''" +
                "data-enc_dt=''" +
                "data-enc_para=''" +
            ">" +
                "<td onclick = 'marcaSol(0)' class = 'text-center' width = '10%'>" +
                    "<input type = 'radio' name = 'sol_pedido' value = '0' id = 'sol0' onchange = 'preencheEnc(0)' checked />" +
                "</td>" +
                "<td onclick = 'marcaSol(0)' width = '90%'>Externo</td>" +
            "</tr>";
            $('#tab_solicitacoes > tbody').append(html);
            data.forEach(solicitacao => {
                html = "<tr id = 'solTr" + solicitacao.id + "' " +
                    "data-enc_descr='" + solicitacao.encaminhante + "' " +
                    "data-enc_id='" + solicitacao.id_de + "' " +
                    "data-esp_descr='" + solicitacao.descr_esp + "' " +
                    "data-esp_id='" + solicitacao.id_especialidade + "' " +
                    "data-cid_descr='" + solicitacao.cid_codigo + " - " + solicitacao.cid_nome + "' " +
                    "data-cid_id='" + solicitacao.id_cid + "'" +
                    "data-enc_dt='" + solicitacao.data + "'" +
                    "data-enc_para='" + solicitacao.id_para + "'" +
                ">" +
                    "<td onclick = 'marcaSol(" + solicitacao.id + ")' class = 'text-center' width = '10%'>" +
                        "<input type = 'radio' name = 'sol_pedido' value = '" + solicitacao.id + "' id = 'sol" + solicitacao.id + "' onchange = 'preencheEnc(" + solicitacao.id + ")'/>" +
                    "</td>" +
                    "<td onclick = 'marcaSol(" + solicitacao.id + ")' width = '90%' style = 'border-right:1px solid #ced4da'>" + solicitacao.encaminhante + "</td>" +
                "</tr>";
                $('#tab_solicitacoes > tbody').append(html);
                $('#tab_solicitacoes').parent().css("display", "block");
                $('#tab_solicitacoes').parent().prev().css("display", "block");
            });
        }
        $(".limpaSol").each(function() {
            $(this).on("change", function() {
                if (!document.getElementById("sol0").checked) marcaSol(0);
            });
        })
    });
}






function habMod() {
    $('#criarAgendamentoModal .deactHabMod').each(function () {
        $(this).attr('disabled', (
            ($('#criarAgendamentoModal #id_plano').val() == 0 || $('#criarAgendamentoModal #id_plano').val() == null)
            &&
            ($('#criarAgendamentoModal #procedimento_id').val() == 0 || $('#criarAgendamentoModal #procedimento_id').val() == null)
        ));
    })
    $("#criarAgendamentoModal #modalidade_id").val(0);
}

function convenio_control_agendamento() {
    encontrarPlanosPreAgendamento(() => {
        console.log(false)
        mostrarModalidadesPorPlano($('#criarAgendamentoModal #procedimento_id').val())
    })
}





// ******************  COCKPIT  ****************** \\
function atualizar_graficos_cockpit(callback) {
    $('#loading-cockpit').show()
    $('body').css('overflow', 'hidden')
    $('#div-grafico1').empty()
    $('#div-grafico2').empty()
    $('#div-grafico1').append('<h3 style="color: #2e434e;margin-top: 25px;font-size: 20px;text-align: center;">Evolução do Faturamento nos Últimos 6 meses</h3><canvas id="grafico1" width="650" height="350"></canvas>')
    $('#div-grafico2').append('<h3 style="color: #2e434e;margin-top: 25px;font-size: 20px;text-align: center;">Atendimentos Por Modalidade</h3><canvas id="grafico2" width="650" height="350"></canvas>')


    atualizar_grafico1(() => {
        atualizar_grafico2(() => {
            callback()
        })
    })
}
function abrirTitulosReceber(filtro) {
    var data = $('#periodo-cockpit').val();

    window.open("/saude-beta/financeiro/titulos-receber/cockpit/" + filtro + "/" + data, "_blank");
}
function atualizar_grafico1(callback) {
    var mes = $("#periodo-cockpit").val().split("-")[1];
    var ano = $("#periodo-cockpit").val().split("-")[0];
    var labels1 = new Array();
    for (var i = 0; i < 6; i++) {
        mes--;
        if (mes == -1) {
            ano--;
            mes = 11;
        }
        labels1.push(insereZero((mes + 1), 12) + "/" + ano);
    }
    var ctx1 = document.getElementById('grafico1').getContext('2d');
    $.get("/saude-beta/cockpit/grafico1/" + $("#periodo-cockpit").val(), function (data, status) {
        console.log(data + ' | ' + status)
        dados = data.reverse()
        var stackedLine1 = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: labels1.reverse(),
                datasets: [{
                    label: 'Faturamento',
                    data: dados,
                    fill: false,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0
                }]
            },
            options: {
                scales: {
                    y: {
                        ticks: {
                            callback: function(value, index, values) {
                                return value.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' })
                            }
                        },
                        beginAtZero: true
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            title: function (context) {
                                var ano = context[0].label.split("/")[1];
                                var mes = context[0].label.split("/")[0];
                                return ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"][parseInt(mes) - 1] + "/" + ano;
                            },
                            label: function (context) {
                                return context.parsed.y.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' });
                            }
                        },
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 12
                        },
                        displayColors: false
                    }
                }
            }
        });
        callback();
    })
}
function atualizar_grafico2(callback) {
    ctx2 = document.getElementById('grafico2').getContext('2d')
    $.get("/saude-beta/cockpit/grafico2/" + $("#periodo-cockpit").val(),
        function (data, status) {
            console.log(data);
            opCores = ['rgb(255, 99, 132, 1)',
                'rgb(54, 162, 235, 1)',
                'rgb(255, 206, 86, 1)',
                'rgb(75, 192, 192, 1)',
                'rgb(153, 102, 255, 1)',
                'rgb(255, 159, 64, 1)',
                'rgb(139,69,19, 1)',
                'rgb(0,255,255, 1)',
                'rgb(119,136,153, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgb(139,69,19, 1)',
                'rgb(0,255,255, 1)',
                'rgb(119,136,153, 1)',
                '#d275a8',
                '#86eee3',
                '#245b0d',
                '#573fa',
                '#536450',
                '#a47d49',
                '#baa1b6',
                '#2e2542',
                '#b67df0',
                '#82392a',
                '#1bbf95',
                '#d275a8']
            console.log(data + ' | ' + status)
            data = $.parseJSON(data)

            cores = []
            $i = 0
            data.labels.forEach(label => {
                cores.push(opCores[$i])
                $i++
            })
            var stackedLine2 = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Quantidade',
                        backgroundColor: cores,
                        data: data.values,
                    }]
                },
                options: {
                    legend: { display: false },
                }
            });
            callback();
        })
}
function obterData(filtro, tipo) {
    var inicial, final;
    var data = $("#periodo-cockpit").val().split("-");
    var dataObj = filtro == "m" ? new Date(parseInt(data[0]), parseInt(data[1]) - 1) : new Date();
    var ano = dataObj.getFullYear();
    var mm = String(dataObj.getMonth() + 1).padStart(2, '0');
    var dd = String(dataObj.getDate()).padStart(2, '0');
    inicial = filtro != "t" ? ano + "-" + mm + "-" + dd : "";
    if (filtro == "m") dd = [31, (ano % 4 === 0 && ano % 100 !== 0) || ano % 400 === 0 ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][mm - 1];
    final = ano + "-" + mm + "-" + dd;
    if (filtro == "w") {
        const day = dataObj.getDay(); // Obter o dia da semana da data fornecida
        const diff = dataObj.getDate() - day + (day === 0 ? -6 : 0); // Calcular a diferença para o domingo da mesma semana
        dataObj = new Date(dataObj.setDate(diff)); // Criar um novo objeto Date com a data do domingo
        //dataObj = new Date(dataObj.getTime() - (7 * 24 * 60 * 60 * 1000));
        ano = dataObj.getFullYear();
        mm = String(dataObj.getMonth() + 1).padStart(2, '0');
        dd = String(dataObj.getDate()).padStart(2, '0');
        inicial = ano + "-" + mm + "-" + dd;
    }
    if (tipo == "inicial") return inicial;
    else return final;
}
function obterTitulosReceber(_id) {
    var filtro;
    switch(_id.substring(9)) {
        case "atraso":
            filtro = "t";
            break;
        case "hoje":
            filtro = "d";
            break;
        case "semana":
            filtro = "w";
            break;
        case "mes":
            filtro = "m";
            break;
    }
    var req = {
        contrato: "",
        associado: "",
        empresa: "",
        venc_ou_lanc: "vencimento",
        datainicial: obterData(filtro, "inicial"),
        datafinal: obterData(filtro, "final"),
        valor_inicial: "",
        valor_final: "",
        forma_pag: "",
        liquidados: "N",
        analitico: "N",
        id : _id
    };
    $.get(
        "/saude-beta/financeiro/titulos-receber/pesquisar", req,
        function (data) {
            data = $.parseJSON(data);
            $(data.id).html(data.total.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }))
        }
    )
}
function filtrar_cockpit_data() {
    // ASSOCIADOS 
    $('#n-ativos').html(0)
    $('#n-novos').html(0)
    $('#n-renovados').html(0)
    $('#n-resgatados').html(0)
    $('#n-perdidos').html(0)
    $('#n-iecConv').html(0)
    $('#n-iecNConv').html(0)
    $('#n-td-cockpit').html(0)
    $('#n-totalIECs').html(0)
    // ATENDIMENTOS
    $('#agendamentos_dia').html(0)
    $('#agendamentos_cancelados_dia').html(0)
    $('#atend_dia').html(0)
    $('#atend_mes').html(0)
    $('#pessoas_atend').html(0)
    $('#pessoas_atend_cortesia').html(0)
    // FATURAMENTO
    $('#faturamento_dia').html(0)
    $('#faturamento_mes').html(0)
    $('#faturamento_semestre').html(0)

    $("#receber-atraso").html(0)
    $("#receber-hoje").html(0)
    $("#receber-semana").html(0)
    $("#receber-mes").html(0)

    $("#pagar-atraso").html(0)
    $("#pagar-hoje").html(0)
    $("#pagar-semana").html(0)
    $("#pagar-mes").html(0)
    atualizar_graficos_cockpit(() => {
        $.get(
            '/saude-beta/cockpit/filtrar-data/' + $("#periodo-cockpit").val(),
            function (data, status) {
                console.log(data + ' | ' + status)
                data = $.parseJSON(data);
                console.log(data)
                a = data
                $('#n-ativos').html(data.ativos)
                $('#n-novos').html(data.novos)
                $('#n-renovados').html(data.renovados)
                $('#n-resgatados').html(data.resgatados)
                $('#n-perdidos').html(data.perdidos)
                $('#n-iecConv').html(data.iecConv)
                $('#n-td-cockpit').html(data.iecPercent.toFixed(1) + '%')
                $('#n-totalIECs').html(data.total_iecs)
                $('#agendamentos_dia').html(data.agendamentos_dia)
                $('#agendamentos_cancelados_dia').html(data.agendamentos_canc_dia)
                $('#atend_dia').html(data.agendamentos_atend_dia)
                $('#atend_mes').html(data.agendamentos_atend_mes)
                $('#pessoas_atend').html(data.pessoas_atend_mes)
                $('#pessoas_atend_cortesia').html(data.atendimentos_cortesia)
                const listaFat = [
                    "aluguel_dia",
                    "aluguel_mes",
                    "aluguel_semestre",
                    "faturamento_dia",
                    "faturamento_mes",
                    "faturamento_semestre",
                    "faturamento_geral_dia",
                    "faturamento_geral_mes",
                    "faturamento_geral_semestre"
                ];
                for (var i = 0; i < listaFat.length; i++) {
                    if (data[listaFat[i]] == null) data[listaFat[i]] = 0;
                }

                $('#faturamento_dia').html(data.faturamento_dia.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }))
                $('#faturamento_mes').html(data.faturamento_mes.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }))
                $('#faturamento_semestre').html(data.faturamento_semestre.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }))
                
                const hab_reab = ["hab", "reab"];
                const tempo = ["dia", "mes", "semestre"];

                var valor = 0;
                for (var i = 0; i < hab_reab.length; i++) {
                    for (var j = 0; j < tempo.length; j++) $("#faturamento_" + hab_reab[i] + "_" + tempo[j]).html(valor.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }));
                }

                for (var i = 0; i < data.faturamento_geral_dia.length; i++) {
                    valor = data.faturamento_geral_dia[i].valTot;
                    if (data.faturamento_geral_dia[i].Hab == 1) $('#faturamento_hab_dia').html(valor.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }));
                    else $('#faturamento_reab_dia').html(valor.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }));
                }

                for (var i = 0; i < data.faturamento_geral_mes.length; i++) {
                    valor = data.faturamento_geral_mes[i].valTot;
                    if (data.faturamento_geral_mes[i].Hab == 1) $('#faturamento_hab_mes').html(valor.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }));
                    else $('#faturamento_reab_mes').html(valor.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }));
                }

                for (var i = 0; i < data.faturamento_geral_semestre.length; i++) {
                    valor = data.faturamento_geral_semestre[i].valTot;
                    if (data.faturamento_geral_semestre[i].Hab == 1) $('#faturamento_hab_semestre').html(valor.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }));
                    else $('#faturamento_reab_semestre').html(valor.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }));
                }

                $('#aluguel_dia').html(data.aluguel_dia.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }))
                $('#aluguel_mes').html(data.aluguel_mes.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }))
                $('#aluguel_semestre').html(data.aluguel_semestre.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }))

                obterTitulosReceber("#receber-atraso");
                obterTitulosReceber("#receber-hoje");
                obterTitulosReceber("#receber-semana");
                obterTitulosReceber("#receber-mes");

                $("#pagar-atraso").html(data.pagar_atraso.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }))
                $("#pagar-hoje").html(data.pagar_hoje.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }))
                $("#pagar-semana").html(data.pagar_semana.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }))
                $("#pagar-mes").html(data.pagar_mes.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }))
                $('#loading-cockpit').hide()
                $('body').css('overflow', '')
                if (document.querySelectorAll('#periodo-cockpit > option')[0].value != document.querySelector('#periodo-cockpit').value) {
                    $('#receber-hoje').parent().parent().hide();
                    $('#receber-semana').parent().parent().hide();
                } else {
                    $('#receber-hoje').parent().parent().show();
                    $('#receber-semana').parent().parent().show();
                }
                /*if (document.querySelectorAll('#periodo-cockpit > option')[0].value != document.querySelector('#periodo-cockpit').value) {
                    $('#n-ativos').parent().parent().hide();
                    $('#atend_dia').parent().parent().hide();
                    $('#agendamentos_cancelados_dia').parent().parent().hide();
                    $('#agendamentos_dia').parent().parent().hide();
                    $('#faturamento_dia').parent().parent().hide();
                }
                else {
                    $('#n-ativos').parent().parent().show();
                    $('#atend_dia').parent().parent().show();
                    $('#agendamentos_cancelados_dia').parent().parent().show();
                    $('#agendamentos_dia').parent().parent().show();
                    $('#faturamento_dia').parent().parent().show();
                }*/
            }
        )
    });
}
function percentToFloat(percent) {
    return parseFloat(percent.replace('%', ''))
}
function pxToFloat(px) {
    return parseFloat(px.replace('%', ''))
}
function widthPxToPercentFloat(str) {
    max = pxToFloat($(str).parent().css('width'))
    aux = pxToFloat($(str).css('width'))
    return ((aux * 100) / max)
}
function insereZero(num, tamanho) {
    const total = tamanho.toString().length;
    num = num.toString();
    for (var i = num.length; i < total; i++) num = "0" + num;
    return num;
}
var filtroCockpit;
function nivelarBotoesConfirmacao(ativo) {
    if (ativo) {
        $(".modal-confirmation-mobile .btn").each(function() {
            $(this).removeClass("btn-success");
            $(this).removeClass("btn-danger");
            $(this).addClass("btn-primary");
            $(this).css("width", "50%");
            $($(this).parent()).css("padding-left", "1.5rem");
        });
    } else {
        $(".modal-confirmation-mobile .btn").each(function() {
            $(this).removeClass("btn-primary");
            $(this).css("width", "");
            $($(this).parent()).css("padding-left", "");
        });
        $("#confirmation_yes").addClass("btn-success");
        $("#confirmation_no").addClass("btn-danger");
    }
}
function abrirModalCockpit(el, value) {
    var titulo = obterTituloCockpit(el, value).toLowerCase();
    if (["0", "0.0%", "R$&nbsp;0,00"].indexOf($(el).find("h1").html()) == -1) {
        if (["faturamento_dia", "faturamento_mes", "faturamento_semestre"].indexOf(value) > -1) {
            filtroCockpit = value.split("_")[1];
            var soma = (parseInt(phoneInt(document.getElementById("faturamento_hab_" + filtroCockpit).innerHTML)) + parseInt(phoneInt(document.getElementById("faturamento_reab_" + filtroCockpit).innerHTML)));
            var aluguel = parseInt(phoneInt(document.getElementById("aluguel_" + filtroCockpit).innerHTML));
            if (soma > 0 && aluguel > 0) {
                nivelarBotoesConfirmacao(true);
                ShowConfirmationBox(
                    'Qual detalhamento deseja ver?',
                    '',
                    true, true, false,
                    function () {
                        abrirModalCockpitMain($($("#aluguel_" + filtroCockpit).parent().parent()), "aluguel_" + filtroCockpit);
                    },
                    function () {
                        abrirModalCockpitMain($($("#faturamento_" + filtroCockpit).parent().parent()), "faturamento_" + filtroCockpit);
                    },
                    'Aluguéis',
                    'Habilitação e Reabilitação'
                );
            } else if (soma > 0) abrirModalCockpitMain(el, value);
            else abrirModalCockpitMain($("#aluguel_" + filtroCockpit).parent().parent(), "aluguel_" + filtroCockpit);
        } else abrirModalCockpitMain(el, value);
    } else if (value.indexOf("aluguel") > -1) alert("Não há " + titulo);
    else alert("Não há nada para exibir em " + titulo);
}
function abrirModalCockpitMain(el, value) {
    $("#cockpitModal #table-cockpit").empty()
    $("#cockpitModal").modal("show")
    $("#cockpitModal #loading-modal-cockpit").css('transition', 'width 3s ease 0s')
    $("#cockpitModal #loading-modal-cockpit").css('width', '1px')
    $('#cockpitModal #value').val(value)
    $('#cockpitModal #visualizacao-cockpit').prop('checked', false)
    setTimeout(() => {
        if (percentToFloat($('#cockpitModal #loading-modal-cockpit').css('width')) < 10 ||
            widthPxToPercentFloat('#cockpitModal #loading-modal-cockpit') < 10) {
            $("#cockpitModal #loading-modal-cockpit").css('width', '10%')
            setTimeout(() => {
                console.log($('#cockpitModal #loading-modal-cockpit').css('width'))
                console.log(widthPxToPercentFloat('#cockpitModal #loading-modal-cockpit'))
                console.log((percentToFloat($('#cockpitModal #loading-modal-cockpit').css('width')) < 15))
                if (percentToFloat($('#cockpitModal #loading-modal-cockpit').css('width')) < 15 ||
                    widthPxToPercentFloat('#cockpitModal #loading-modal-cockpit') < 15) {
                    $("#cockpitModal #loading-modal-cockpit").css('transition', 'width 20s ease 0s')
                    $("#cockpitModal #loading-modal-cockpit").css('width', '15%')
                    setTimeout(() => {
                        console.log($('#cockpitModal #loading-modal-cockpit').css('width'))
                        console.log(widthPxToPercentFloat('#cockpitModal #loading-modal-cockpit'))
                        console.log((percentToFloat($('#cockpitModal #loading-modal-cockpit').css('width')) < 60))
                        if (percentToFloat($('#cockpitModal #loading-modal-cockpit').css('width')) < 60 ||
                            widthPxToPercentFloat('#cockpitModal #loading-modal-cockpit') < 60) {
                            $("#cockpitModal #loading-modal-cockpit").css('transition', 'width 20s ease 0s')
                            $("#cockpitModal #loading-modal-cockpit").css('width', '60%')
                            setTimeout(() => {
                                if (percentToFloat($('#cockpitModal #loading-modal-cockpit').css('width')) < 80 ||
                                    widthPxToPercentFloat('#cockpitModal #loading-modal-cockpit') < 80) {
                                    $("#cockpitModal #loading-modal-cockpit").css('transition', 'width 10s ease 0s')
                                    $("#cockpitModal #loading-modal-cockpit").css('transition', 'width 10s ease 0s')
                                    $("#cockpitModal #loading-modal-cockpit").css('width', '80%')
                                }
                                $(".modal-confirmation-mobile .btn").each(function() {
                                    $(this).removeClass("btn-primary");
                                    $(this).css("width", "");
                                    $($(this).parent()).css("padding-left", "");
                                })
                            }, 3000)
                        }
                    }, 2000)
                }
            }, 3000)
        }
    }, 500)

    $("#filtro-faturamento").css("visibility", ([
        "ativos", "novos", "renovados", "resgatados", "perdidos", "iec", "iecConv",
        "agendamentos_dia", "agendamentos_canc_dia", "agendamentos_atend_dia", "agendamentos_atend_mes",
        "pessoas_atend_mes", "pessoas_atend_cortesia",
        "faturamento_dia", "faturamento_mes", "faturamento_semestre",
        "faturamento_hab_dia", "faturamento_hab_mes", "faturamento_hab_semestre",
        "faturamento_reab_dia", "faturamento_reab_mes", "faturamento_reab_semestre",
        "aluguel_dia", "aluguel_mes", "aluguel_semestre",
        "aluguel"
    ].indexOf(value) > -1) ? "hidden" : "");
    $("#cockpitModal #table-cockpit").empty();
    //$('#cockpitModal #botao-impressao-cockpit').data("dados", value);
    $.get(
        "/saude-beta/cockpit/mostrar/" + value + "/" + $("#periodo-cockpit").val(),
        function (data, status) {
            var tab;
            $("#cockpitModal #loading-modal-cockpit").css("transition", "width 0.2s ease 0s");
            $("#cockpitModal #loading-modal-cockpit").css("width", "100%");
            nivelarBotoesConfirmacao(false);
            if (["ativos", "novos", "renovados", "resgatados", "perdidos", "iec", "iecConv"].indexOf(value) > -1) {
                $("#cockpitModal #botao-impressao-cockpit").parent().show();
                tab = ["Nome", "Cidade", "Idade"];
                if (["perdidos", "iec", "iecConv"].indexOf(value) == -1) tab.push("Celular");
                if (value == "perdidos")    tab.push("Perdido em");
                else if (value == "ativos") tab.push("Vencimento");
                else if (["iec", "iecConv"].indexOf(value) > -1) tab.push("Realizado em");
                html = '<table class="table table-hover"><thead>';
                for (var i = 0; i < tab.length; i++) {
                    var alinhamento = i == 0 ? "left" : "center";
                    html += '<th class = "text-' + alinhamento + '" style = "white-space:nowrap">' + tab[i] + '</th>';
                }
                html += "</thead><tbody>";
                $("#cockpitModal #table-cockpit").append(html);
                indice = 0;
                data.forEach(el => {
                    indice++;
                    if (parseInt(el.idade) == el.idade) {
                        idade = el.idade + " ANO";
                        if (el.idade > 1) idade += "S";
                        else idade = "0" + idade;
                    } else idade = el.idade;
                    var cidade = el.cidade != "" ? el.cidade : "NÃO CADASTRADO";
                    var nome = value == "iec" ? el.nome : el.nome_fantasia;
                    tab = [
                        insereZero(indice, data.length) + ' - ' + nome,
                        cidade,
                        idade
                    ];
                    if (["perdidos", "iec", "iecConv"].indexOf(value) == -1) tab.push(el.telefone != "" ? phoneMask(phoneInt(el.telefone)) : "NÃO CADASTRADO");
                    if (["perdidos", "ativos"].indexOf(value) > -1) tab.push(el.vencimento);
                    else if (["iec", "iecConv"].indexOf(value) > -1) tab.push(el.realizado_em);
                    html = "<tr>";
                    for (var i = 0; i < tab.length; i++) {
                        var alinhamento;
                        switch(i) {
                            case 0:
                                alinhamento = "left";
                                break;
                            case tab.length - 1:
                                alinhamento = "right minw";
                                break;
                            default:
                                alinhamento = "center";
                        }
                        html += '<td class = "up-txt text-' + alinhamento + ' v-middle">' + tab[i] + '</td>';
                    }
                    html += "</tr>";
                    $("#cockpitModal #table-cockpit > table > tbody").append(html);
                });
                $("#cockpitModal #loading-modal-cockpit").css('width', '100%');
                $("#cockpitModal #table-cockpit").append('</table>');
                $("#cockpitModal").modal("show");
            }
            else if (value.indexOf("agendamentos") > -1) {
                //$('#cockpitModal #botao-impressao-cockpit').parent().hide()
                $("#cockpitModal #table-cockpit").empty();
                html = '<table class="table table-hover">'
                html += '   <thead>'
                html += '       <th class="text-center">Contrato</th>'
                if (value.indexOf("dia") == -1) html += '<th class="text-center">Data</th>'
                html += '       <th class="text-center">Horário</th>'
                html += '       <th class="text-left">Paciente</th>'
                html += '       <th class="text-left">Profissional</th>'
                tab = ['<th class="text-left">Modalidade</th>'];
                if (value == "agendamentos_dia") tab.push('<th class="text-center">Status</th>');
                else tab[0] = tab[0].replace("left", "center");
                for (var i = 0; i < tab.length; i++) html += tab[i];
                html += '   </thead>'
                html += '   <tbody>'
                $("#cockpitModal #table-cockpit").append(html);
                data.forEach((el) => {
                    if (el.Contrato == null || el.Contrato == 0) el.Contrato = '-----------'
                    else el.Contrato = "#" + insereZero(el.Contrato, "xxxxxx")
                    if (el.Data == null) el.Data = '-----------'
                    else {
                        var dt = el.Data.split("-");
                        dt.reverse();
                        el.Data = dt.join("/");
                    }
                    if (el.Horario == null) el.Horario = '-----------'
                    else el.Horario = el.Horario.substring(0, 5)
                    if (el.Paciente == null) el.Paciente = '-----------'
                    if (el.Profissional == null) el.Profissional = '-----------'
                    if (el.Modalidade == null) el.Modalidade = '-----------'
                    if (el.Status == null) el.Status = '-----------'

                    html = '<tr>';
                    html += '       <td style="font-size: 15px;" class="up-txt v-middle text-center minw">' + el.Contrato + '</td>'
                    if (value.indexOf("dia") == -1) html += '<td style="font-size: 15px;white-space:nowrap" class="up-txt v-middle text-center">' + el.Data + '</td>'
                    html += '       <td style="font-size: 15px;" class="up-txt v-middle text-center">' + el.Horario + '</td>'
                    html += '       <td style="font-size: 15px;" class="up-txt v-middle text-left">' + el.Paciente + '</td>'
                    html += '       <td style="font-size: 15px;" class="up-txt v-middle text-left">' + el.Profissional + '</td>'
                    tab = ['<td style="font-size: 15px;" class="up-txt v-middle text-left">' + el.Modalidade + '</td>'];
                    if (value == "agendamentos_dia") tab.push('<td style="font-size: 15px;" class="up-txt v-middle text-center minw">' + el.Status + '</td>');
                    else tab[0] = tab[0].replace("left", "left minw");
                    for (var i = 0; i < tab.length; i++) html += tab[i];
                    html += '</tr>'
                    $("#cockpitModal #table-cockpit > table > tbody").append(html)
                });
                $("#cockpitModal #loading-modal-cockpit").css('width', '100%')
                $("#cockpitModal #table-cockpit").append('</table>')
                $("#cockpitModal").modal("show")
            }
            else if (value.indexOf("pessoas_atend") > -1) {
                $("#cockpitModal #table-cockpit").empty();
                html = '<table class="table table-hover">'
                html += '   <thead>'
                html += '       <th class="text-left">Paciente</th>'
                var ultimo = value == "pessoas_atend_cortesia" ? "Data" : "Código"
                html += '       <th class="text-center minw">' + ultimo + '</th>'
                html += '   </thead>'
                html += '   <tbody>'
                $("#cockpitModal #table-cockpit").append(html);
                indice = 0;
                data.forEach((el) => {
                    indice++;
                    if (value == "pessoas_atend_mes") el.nome = insereZero(indice, data.length) + ' - ' + el.nome;
                    html = '<tr>';
                    html += '       <td class="up-txt">' + el.nome + '</td>'
                    ultimo = value == "pessoas_atend_cortesia" ? el.data : el.id_pessoa;
                    html += '       <td class="up-txt text-right minw">' + ultimo + '</td>'
                    html += '</tr>'
                    $("#cockpitModal #table-cockpit > table > tbody").append(html)
                });
                $("#cockpitModal #table-cockpit").append('</table>')
                $("#cockpitModal").modal("show")
            }
            else if (value.indexOf("faturamento") > -1) {
                console.log(data + ' | ' + status)
                console.log(data)
                $("#cockpitModal #table-cockpit").empty();
                html = '<table class="table table-hover">'
                html += '   <thead>'
                html += '       <th class="text-left">Contrato</th>'
                html += '       <th class="text-left">Paciente</th>'
                html += '       <th class="text-left">Plano(s)</th>'
                html += '       <th class="text-center">Inicio</th>'
                html += '       <th class="text-center">Fim</th>'
                html += '       <th class="text-center">Valor</th>'
                html += '   </thead>'
                html += '   <tbody>'
                $("#cockpitModal #table-cockpit").append(html);
                a = data
                data.forEach(el => {
                    if (el.Contrato == null || el.Contrato == 0) el.Contrato = '-----------'
                    else el.Contrato = "#" + insereZero(el.Contrato, "xxxxxx")
                    if (el.Paciente == null) el.Paciente = '-----------'
                    if (el.Plano == null) el.Plano = '-----------'
                    if (el.Inicio == null) el.Inicio = '-----------'
                    if (el.Fim == null) el.Fim = '-----------'
                    if (el.Caixa == null) el.Caixa = '-----------'

                    el.Inicio = el.Inicio.substr(0, 10).replace('-', '/').replace('-', '/')
                    el.Inicio = el.Inicio.substr(8) + el.Inicio.substr(4, 3) + '/' + el.Inicio.substr(0, 4)

                    el.Fim = el.Fim.substr(0, 10).replace('-', '/').replace('-', '/')
                    el.Fim = el.Fim.substr(8) + el.Fim.substr(4, 3) + '/' + el.Fim.substr(0, 4)

                    let data1 = new Date(el.data_nascimento)
                    let data2 = new Date()
                    idade = data2.getFullYear() - data1.getFullYear()
                    html = '<tr>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-left v-middle">' + el.Contrato + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-left v-middle">' + el.Paciente + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-left v-middle">' + el.Plano + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-center v-middle">' + el.Inicio + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-center v-middle">' + el.Fim + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-right v-middle valor">' + el.Valor.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }) + '</td>'
                    html += '</tr>'
                    $("#cockpitModal #table-cockpit > table > tbody").append(html)
                });
                $("#cockpitModal #table-cockpit").append('</table>')
                $("#cockpitModal").modal("show")
            }
            else if (value == "aluguel") {
                console.log(data + ' | ' + status)
                console.log(data)
                $("#cockpitModal #table-cockpit").empty();
                html = '<table class="table table-hover">'
                html += '   <thead>'
                html += '       <th class="text-center">Nº de doc.</th>'
                html += '       <th class="text-left">Situação</th>'
                html += '       <th class="text-left">Alugado por</th>'
                html += '       <th class="text-center">Alugado em</th>'
                html += '       <th class="text-center">Parcelas</th>'
                html += '       <th class="text-center">Valor por parcela</th>'
                html += '       <th class="text-center">Valor do contrato</th>'
                html += '       <th class="text-center">Subtotal recebido</th>'
                html += '   </thead>'
                html += '   <tbody>'
                $("#cockpitModal #table-cockpit").append(html);
                data.forEach(el => {
                    html = '<tr>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-right v-middle">' + el.ndoc + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-left v-middle">' + el.situacao + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-left v-middle">' + el.alugado_por + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-center v-middle">' + el.alugado_em + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-center v-middle">' + el.parcelas + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-right v-middle">' + el.valor.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }) + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-right v-middle">' + el.valor_total.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }) + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-right v-middle" valor>' + el.recebido.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }) + '</td>'
                    html += '</tr>'
                    $("#cockpitModal #table-cockpit > table > tbody").append(html)
                });
                $("#cockpitModal #table-cockpit").append('</table>')
                $("#cockpitModal").modal("show")
            }
            else if (value.indexOf("aluguel") > -1) {
                console.log(data + ' | ' + status)
                console.log(data)
                $("#cockpitModal #table-cockpit").empty();
                html = '<table class="table table-hover">'
                html += '   <thead>'
                html += '       <th class="text-left">Membro</th>'
                html += '       <th class="text-left">Sala</th>'
                html += '       <th class="text-center">Alugado em</th>'
                html += '       <th class="text-center">Venc. da próx. parcela</th>'
                html += '       <th class="text-center">Valor</th>'
                html += '   </thead>'
                html += '   <tbody>'
                $("#cockpitModal #table-cockpit").append(html);
                data.forEach(el => {
                    html = '<tr>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-left v-middle">' + el.membro + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-left v-middle">' + el.sala + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-center v-middle">' + el.alugado_em + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-center v-middle">' + el.venc_prox_parc + '</td>'
                    html += '       <td style="font-size: 13px;" class="up-txt text-right v-middle valor">' + el.valor.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' }) + '</td>'
                    html += '</tr>'
                    $("#cockpitModal #table-cockpit > table > tbody").append(html)
                });
                $("#cockpitModal #table-cockpit").append('</table>')
                $("#cockpitModal").modal("show")
            }
            var titulo = obterTituloCockpit(el, value);
            if (titulo.indexOf("Faturamento") > -1 || titulo.indexOf("Aluguéis") > -1 || value == "aluguel") {
                $('#titulo-cockpit-modal').parent().removeClass("d-flex");
                var legenda = value == "aluguel" ? " recebido" : "";
                $('#cockpitModal #titulo-cockpit-modal').html(
                    "<table style = 'width:100%'>" +
                        "<tr>" +
                            "<td>" + titulo + "</td>" +
                            "<td class = 'minw' style = '" +
                                "font-size: 0.81rem;" +
                                "color: #212529;" +
                                "font-weight: bold;" +
                            "'>Total" + legenda + ": " + somaCockpitModal() + "</td>" +
                        "</tr>" +
                    "</table>"
                );
            } else {
                $('#titulo-cockpit-modal').parent().addClass("d-flex");
                $('#cockpitModal #titulo-cockpit-modal').html(titulo);
            }
        }
    )
}
function obterTituloCockpit(el, value) {
    var titulo;
    if (value != "aluguel") {
        var titulo = $(el).find("span").html();
        if (value.indexOf("faturamento") > -1) {
            titulo = "Faturamento " + titulo.toLowerCase();
            if (value.indexOf("hab") > -1) titulo += " - Habilitação";
            else if (value.indexOf("reab") > -1) titulo += " - Reabilitação";
            else titulo += " - Habilitação e Reabilitação";
        } else if (value.indexOf("aluguel") > -1) titulo = "Aluguéis que vencem no " + titulo.split(" ")[1];
        else if (titulo.indexOf("conv.") > -1) titulo = "Associados convertidos pelo IEC";
    } else {
        $("#cockpitModalLabel").html("HISTÓRICO DE SALA");
        titulo = $($("#sala" + el).children()[0]).html();
    }
    return titulo;
}
function somaCockpitModal() {
    var elementos = document.getElementsByClassName("valor");
    var soma = 0;
    for (var i = 0; i < elementos.length; i++) soma += parseInt(phoneInt(elementos[i].innerHTML)) / 100;
    return soma.toLocaleString('pt-br', { style: 'currency', currency: 'BRL' });
}








function inputPrivadoNotificacao() {
    if (document.querySelector('#publico-notificacao:checked') == null) {
        document.querySelector('#profissional-notificacao').style.display = 'none'
    }
    else document.querySelector('#profissional-notificacao').style.display = 'block'
}
function inputPrivadoNotificacao2() {
    if (document.querySelector('#publico-notificacao2:checked') == null) {
        document.querySelector('#profissional-notificacao2').style.display = 'none'
    }
    else document.querySelector('#profissional-notificacao2').style.display = 'block'
}

















// ******************  NOTIFICAÇÕES  ****************** \\
function salvar_notificacao() {
    if ($('#publico-notificacao').prop('checked')) {
        if ($('#notificacao_profissional_id').val() == '') {
            alert('Todos os campos são obrigatórios!')
            console.log('1')
            return;
        }
    }
    else {
        if ($('#assunto-notificacao').val() == '' ||
            $('#notificacao_txt').val() == '') {
            alert('Todos os campos são obrigatórios!')
            console.log('2')
            return;
        }
    }
    if ($('#publico-notificacao').prop('checked')) pub = 'S';
    else pub = 'N';
    $.post(
        '/saude-beta/notificacao/salvar', {
        _token: $("meta[name=csrf-token]").attr("content"),
        assunto: $('#assunto-notificacao').val(),
        associado: $('#id_pessoa_prontuario').val(),
        publico: pub,
        profissional: $('#notificacao_profissional_id').val(),
        notificacao: $('#notificacao_txt').val()
    }, function (data, status) {
        console.log(data + ' | ' + status)
        if (data == 'true') {
            $("#criarNotificacaoModal").modal('hide')
            notificacao_por_pessoa($('#id_pessoa_prontuario').val())

        }
    }
    )
}
function salvar_notificacao2() {
    if ($('#publico-notificacao2').prop('checked')) {
        if ($('#notificacao_profissional_id2').val() == '') {
            alert('Todos os campos são obrigatórios!')
            console.log('1')
            return;
        }
    }
    else {
        if ($('#assunto-notificacao2').val() === '' ||
            $('#notificacao_txt2').val() === '') {
            alert('Todos os campos são obrigatórios!')
            console.log('2')
            return;
        }
    }
    if ($('#publico-notificacao2').prop('checked')) pub = 'S';
    else pub = 'N';
    $.post(
        '/saude-beta/notificacao/salvar', {
        _token: $("meta[name=csrf-token]").attr("content"),
        assunto: $('#assunto-notificacao2').val(),
        associado: $('#paciente_id_notificacao2').val(),
        publico: pub,
        profissional: $('#notificacao_profissional_id2').val(),
        notificacao: $('#notificacao_txt2').val()
    }, function (data, status) {
        console.log(data + ' | ' + status)
        if (data == 'true') {
            $("#criarNotificacaoModal2").modal('hide')
            buscarNotificacoes();
        }
    }
    )
    buscarNotificacoes()
}
function controle_janela_notificacao() {
    element = document.querySelector('#notificacao-navbar')
    btn = document.querySelector('#add-not-btn')
    switch (element.style.top) {
        case '-100%':
            element.style.top = '80px'
            btn.style.top = ''
            btn.style.bottom = '15px'
            break;
        case '80px':
            element.style.top = '-100%';
            btn.style.top = '-100%'
            btn.style.bottom = ''
            break;
    }
}
function listar_notificacoes() {
    controle_janela_notificacao()
    buscarNotificacoes();
    $.get('/saude-beta/notificacao/listar', {},
        function (data, status) {
            console.log(data + ' | ' + status)
        })

}
function notificacao_por_pessoa(id_paciente) {
    var html = '';
    $.get('/saude-beta/notificacao/listar-por-pessoa/' + id_paciente,
        function (data) {
            var lista_group = []
            $('#table-prontuario-notificacao-pessoa').empty();
            i = 0
            a = data;
            data.forEach(notificacao => {
                if (lista_group.includes(notificacao.id_questionario)) {
                    html = '<div class="card z-depth-0 bordered" style="display: none;border: 1px solid rgba(0, 0, 0, .125)">';
                }
                else html = '<div class="card z-depth-0 bordered" style="border: 1px solid rgba(0, 0, 0, .125)">';
                html += '    <div class="accordion-header w-100">';
                html += '        <div class="row opacity-hover">';
                html += '            <div class="col"> ';
                html += '                <button class="btn btn-link" type="button" data-toggle="collapse"';
                html += '  onclick="visualizar_notificacao_prontuario(' + notificacao.id_notificacao + ',' + notificacao.id_paciente + ');" '
                html += '                    " aria-expanded="true" aria-controls="collapse"style="display: flex;">';

                html += '           <img class="user-photo-sm" style="width: 35px;margin-right: 20px;" src="/saude-beta/img/pessoa/' + notificacao.created_by + '.jpg"'
                html += '            onerror="this.onerror=null;this.src=' + "'/saude-beta/img/paciente_default.png'" + '">';

                html += '<span class="title-notificacao"'
                html += ' style="margin-top: 3px;">' + notificacao.assunto + '</span>';
                html += '                </button> ';
                html += '            </div>';
                html += '            <div class="col-4 d-flex text-right div-data-notificacao"> ';
                html += '                <button class="btn btn-link ml-auto data-notificacao" type="button" data-toggle="collapse"';
                html += '                    data-target="#evolucao-' + notificacao.id_notificacao + '" aria-expanded="true" aria-controls="collapse">';
                html += moment(notificacao.created_at).format('DD/MM/YYYY');
                html += ' às ';
                html += notificacao.created_at.substring(11, 16);
                html += '                </button> ';
                html += '                <div class="my-auto mx-4 acoes-notificacao">';

                html += '                    <i class="my-icon far fa-print" onclick="imprimir_IEC(' + notificacao.id_notificacao + ')"></i>';
                html += '                    <i class="my-icon far fa-trash-alt" onclick="deletar_IEC(' + notificacao.id_notificacao + ')"></i>';
                html += '                </div>';
                html += '            </div>';
                html += '        </div>';
                html += '    </div>';
                html += '</div>';
                $('#table-prontuario-notificacao-pessoa').append(html);
                i += 1;
                lista_group.push(notificacao.id_questionario)
            });
            $('[data-id="#prt-IEC"] .qtde-prontuario')
                .data('count', data.length)
                .attr('data-count', data.length)
                .find('small')
                .html(data.length);
        }
    );

}
var $aux_notificacoes
window.addEventListener('load', () => {
    buscarNotificacoes()
    setInterval(() => {
        buscarNotificacoes()
    }, 15000);
})
var control_notificacoes = 0, testando_notificacoes
// function buscarNotificacoes() {
//     $.get(
//         '/saude-beta/notificacao/listar', {},
//         function (data, status) {
//             console.log(data + ' | ' + status)
//             data = $.parseJSON(data)
//             testando_notificacoes = data
//             if (JSON.stringify($aux_notificacoes) == JSON.stringify(data.notificacoes_ar)) return
//             else {
//                 if (control_notificacoes == 1) {
//                     var resumo = data.notificacoes_ar[0].assunto.substr(0, 46) + '...',
//                     b = data.notificacoes_ar[0].created_at
//                     tempo = b.substr(8, 2) + '/' + b.substr(5, 2) + '/' + b.substr(0, 4)
//                     html = ' <li class="li-not-bar" onclick="$(this).remove()" id="notificacoes-aparecendo" style="position: fixed;top: 10%;z-index: 100;right: 10px;border: 1px solid #b0b0b0;transition: 1.5s opacity;opacity: 0;"> '
//                     html += '   <div style="display: flex;cursor: pointer"> '
//                     html += '       <div class="img-not-bar"> '
//                     html += '           <img class="user-photo-sm" style="width: 35px;" src="/saude-beta/img/pessoa/' + data.notificacoes_ar[0].id_empresa + '/' + data.notificacoes_ar[0].created_by + '.jpg"'
//                     html += '            onerror="this.onerror=null;this.src=' + "'/saude-beta/img/paciente_default.png'" + '">';
//                     html += '       </div>'
//                     html += '       <div style="width:100%;padding: 0px 8px 0px 0px;">'
//                     html += '           <div class="div-nome-not-bar"><div class="notific-mobile" style="width: 325px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;">'
//                     html += '               <span id="n' + data.notificacoes_ar[0].id_notificacao + '" class="nome-not-bar"onclick="visualizar_notificacao(' + data.notificacoes_ar[0].id_notificacao + ',' + "'" + resumo + "'" + ')">' + data.notificacoes_ar[0].descr_paciente + '</span></div>'
//                     html += '               <span>'+ data.notificacoes_ar[0].descr_profissional +'</span>'
//                     html += '               <span>' + tempo + '</span>'
//                     html += '           </div>'
//                     html += '           <div id="not-txt-' + data.notificacoes_ar[0].id_notificacao + '" style="width: 85%;word-wrap: break-word;">'
//                     html += '               <p style="white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;">' + resumo + "</p>"
//                     html += '           </div>'
//                     html += '           <div class="">   <span onclick="excluirNotificacao(' + data.notificacoes_ar[0].id_notificacao + ')"></span></div>'
//                     html += '       </div>'
//                     html += '   </div>'
//                     html += '</li>'
//                     $('body').append(html)
//                     setTimeout(() => {
//                         $('#notificacoes-aparecendo').css('opacity', 1)                    
//                     }, 500);
//                 }
//             }
//             control_notificacoes = 1
//             $('#notificacao-navbar > ul').empty()
//             a = data
//             contador = 0;
//             data.notificacoes_ar.forEach(not => {
//                 var resumo = not.assunto.substr(0, 46) + '...',
//                     b = not.created_at
//                 tempo = b.substr(8, 2) + '/' + b.substr(5, 2) + '/' + b.substr(0, 4)


//                 html = '<li class="li-not-bar"> '
//                 html += '   <div style="display: flex;cursor: pointer"> '
//                 html += '       <div class="img-not-bar"> '
//                 console.log(data.notificacoes_n_visualizadas[contador])
//                 if (data.notificacoes_n_visualizadas[contador] != 1) {
//                     html += '<span id="not-nao-lida-i-' + not.id_notificacao + '" class="not-nao-lida-i" style="background-color: red"></span>'
//                 }
//                 html += '           <img class="user-photo-sm" style="width: 35px;" src="/saude-beta/img/pessoa/' + not.id_empresa + '/' + not.created_by + '.jpg"'
//                 html += '            onerror="this.onerror=null;this.src=' + "'/saude-beta/img/paciente_default.png'" + '">';
//                 html += '       </div>'
//                 html += '       <div style="width:100%;padding: 0px 8px 0px 0px;">'
//                 html += '           <div class="div-nome-not-bar"><div class="notific-mobile" style="width: 325px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;">'
//                 html += '               <span id="n' + not.id_notificacao + '" class="nome-not-bar"onclick="visualizar_notificacao(' + not.id_notificacao + ',' + "'" + resumo + "'" + ')">' + not.descr_paciente + '</span></div>'
//                 html += '               <span>'+ data.notificacoes_ar[0].descr_profissional +'</span>'
//                 html += '               <span>' + tempo + '</span>'
//                 html += '           </div>'
//                 html += '           <div id="not-txt-' + not.id_notificacao + '" style="width: 85%;word-wrap: break-word;">'
//                 html += '               <p style="white-space: nowrap;width: 100%;overflow: hidden;text-overflow: ellipsis;">' + resumo + "</p>"
//                 html += '           </div>'
//                 html += '           <div class="remover-notificacao">   <span onclick="excluirNotificacao(' + not.id_notificacao + ')"></span></div>'
//                 html += '       </div>'
//                 html += '   </div>'
//                 html += '</li>'
//                 $('#notificacao-navbar > ul').append(html)
//                 contador++;
//             })
//             $aux_notificacoes = data.notificacoes_ar
//             $(".qtde-notificacao").html(data.notificacoes_n_visualizadas.filter(x => x === false).length)
//         }
//     )
// }
function abrirModalNotificacao() {
    $('#assunto-notificacao').val('')
    $('#notificacao_profissional_nome').val('')
    $('#notificacao_profissional_id').val('')
    $('#notificacao_txt').val('')
    $('#criarNotificacaoModal2').modal('show')
}
var notAtiv = -1;
function visualizar_notificacao(id, notificacao) {
    if (notAtiv != id) {
        notAtiv = id;
        $.get('/saude-beta/notificacao/visualizar-notificacao/' + id,
            function (data, status) {
                console.log(data + ' | ' + status)
                qtd_letras = data.notificacao.length
                if (qtd_letras < 200) {
                    $("#not-txt-" + id).parent().parent().parent().css('max-height', '140px')
                    $("#not-txt-" + id).parent().parent().parent().css('height', '140px')
                }
                else {
                    $("#not-txt-" + id).parent().parent().parent().css('max-height', '240px')
                    $("#not-txt-" + id).parent().parent().parent().css('height', '240px')
                }
                $("#not-txt-" + id).parent().parent().parent().css('overflow', 'auto')
                $("#not-txt-" + id).parent().parent().parent().css('overflow-x', 'hidden')
                $("#not-txt-" + id).empty();
                $("#not-txt-" + id).append('<h5 style="text-transform: uppercase;">' + data.assunto + '</h5>')
                $("#not-txt-" + id).append('<p style="line-height: 1.5;">' + data.notificacao + '<p>')
                console.log(notificacao)
                console.log(id)
                $("#not-nao-lida-i-" + id).remove();
                console.log($("#not-txt-" + id).parent().prev());
                $("#not-txt-" + id).parent().prev().css("margin-top", '25px')
                $(".qtde-notificacao").each(function(){$(this).html(document.getElementsByClassName("not-nao-lida-i").length)})
                var _id = id,
                    _notificacao = notificacao;
                window.addEventListener('click', function (e) {
                    if (!document.querySelector("#not-txt-" + id).contains(e.target)) {
                        recolher_notificacao(_id, _notificacao)
                    }
                });
            }
        )    
    }
}
function recolher_notificacao(id, notificacao) {
    $("#not-txt-" + id).empty();
    $("#not-txt-" + id).append(notificacao);
    $("#not-txt-" + id).parent().parent().parent().css('max-height', '90px')
    $("#not-txt-" + id).parent().parent().parent().css('height', '90px')
    $("#not-txt-" + id).parent().parent().parent().css('overflow', 'hidden')
    $("#not-txt-" + id).parent().parent().parent().css('overflow-x', 'hidden')
    $("#not-txt-" + id).parent().parent().find('.remover-notificacao').css('margin-top', '-15px')
}
function excluirNotificacao(id_notificacao) {
    if (id_notificacao) {
        $.post(
            '/saude-beta/notificacao/excluir', {
            _token: $("meta[name=csrf-token]").attr("content"),
            id: id_notificacao
        }, function (data, status) {
            console.log(data + ' | ' + status)
            if (data == 'true') buscarNotificacoes();
            else alert('erro!')
        })
    }
    else {
        alert('Notificação não visualizada')
    }
}
function ajustar_modalidades() {
    $("#ajusteModalidadesModal").modal('show')

}
function abrirModalAgendamentosPendentes() {
    $('#agendamentosPendentesModal #conteudo-lote-agenda')
    $.get('/saude-beta/pessoa/listar-membros', {},
        function (data, status) {
            console.log(data + ' | ' + status)
            $("#agendamentosPendentesModal #membro").append('<option value="0">Todos</option>')
            data.forEach(membro => {
                html = '<option value="' + membro.id + '">' + membro.nome_fantasia + '</option>'
                $("#agendamentosPendentesModal #membro").append(html)
            })
            $('#agendamentosPendentesModal #conteudo-lote-agenda').empty()
            $('#agendamentosPendentesModal #btn-imprimir-agendamentos').hide()
            $("#agendamentosPendentesModal").modal('show')
        })
}
function callEditarAgendamento(el, btn, id, antigo) {
    if (btn == 0) {
        $(el).find(".tag-agenda").html("Carregando");
        if (antigo) antigo_editar_agendamento(id, 1);
        else editar_agendamento(id, 1);
    }
}
function pesquisarAgendamentosPendentes() {
    var dia_temp;
    $.get(
        '/saude-beta/agenda/agendamentos-pendentes', {
        data_inicial: $("#agendamentosPendentesModal #data-inicial").val().replaceAll('/', '-'),
        data_final: $("#agendamentosPendentesModal #data-final").val().replaceAll('/', '-'),
        id_membro: $("#agendamentosPendentesModal #membro").val(),
        status: $("#agendamentosPendentesModal #somente-finalizados").val(),
        completo_incompleto: $("#agendamentosPendentesModal #incompletos").val()
    }, function (data, status) {

        console.log(data + ' | ' + status)
        data = $.parseJSON(data);
        $('#agendamentosPendentesModal #conteudo-lote-agenda').empty()
        if (data.length > 0) {
            $('#agendamentosPendentesModal #btn-imprimir-agendamentos').show()
        }
        else $('#agendamentosPendentesModal #btn-imprimir-agendamentos').hide()
        data.forEach(agendamento => {
            console.log('Agendamento Semanal:');
            console.log(agendamento);
            if (agendamento.data != dia_temp) {
                if (dia_temp != undefined) $('#agendamentosPendentesModal #conteudo-lote-agenda').append('<hr>');
                html = '<h4 style="color:#212529">' + moment(agendamento.data).format('LL'); + '</h4>';
                $('#agendamentosPendentesModal #conteudo-lote-agenda').append(html);
                dia_temp = agendamento.data;
            }

            if (agendamento.id != undefined && agendamento.id != null) {
                html = '<li data-id_agendamento="' + agendamento.id + '"';
                html += ' data-status="' + agendamento.id_status + '"';
                html += ' data-paciente="' + agendamento.nome_paciente + '"';
                html += ' data-procedimento="' + agendamento.descr_procedimento + '"';
                html += ' data-convenio="' + agendamento.convenio_nome + '"';
                html += ' data-permite_fila_espera="' + agendamento.permite_fila_espera + '"';
                html += ' data-permite_reagendar="' + agendamento.permite_reagendar + '"';
                html += ' data-permite_editar="' + agendamento.permite_editar + '"';
                html += ' data-libera_horario="' + agendamento.libera_horario + '"';
                try {
                    if (document.getElementById("estaNaAgenda").value == "estaNaAgenda") {
                        if (agendamento.antigo == 0) {
                            html += ' ondblclick="editar_agendamento(' + agendamento.id + ',0)" '
                            html += ' onclick="callEditarAgendamento(this, event.button, ' + agendamento.id + ', false)" '
                        }
                        else {
                            html += ' ondblclick="antigo_editar_agendamento(' + agendamento.id + ',0)" '
                            html += ' onclick="callEditarAgendamento(this, event.button, ' + agendamento.id + ', true)" '
                        }
                        html += ' '    
                    }
                } catch (err) {}
                html += ' title="' + agendamento.descr_status + '\n' + agendamento.obs + '"';
                html += ' style="background:' + agendamento.cor_status + '; color: ' + agendamento.cor_letra + ';max-height: 94px;min-width: 100%;margin-bottom: 20px;cursor: pointer" >';

                html += '    <div class="my-1 mx-1 d-flex">';
                html += '       <img class="foto-paciente-agenda" data-id_paciente="' + agendamento.id_paciente + '" src="/saude-beta/img/pessoa/' + agendamento.id_paciente + '.jpg" onerror="this.onerror=null;this.src=' + "'/saude-beta/img/paciente_default.png'" + '" onclick="verificar_cad_redirecionar(' + agendamento.id_paciente + ')">';
                html += '       <div>';
                html += '           <p class="col p-0">';
                html += '               <span class="ml-0 my-auto" style="font-weight:600" onclick="verificar_cad_redirecionar(' + agendamento.id_paciente + ')">';
                html += agendamento.hora.substring(0, 5) + '  -  ' + agendamento.nome_paciente.toUpperCase();
                html += '               </span>';
                html += '           </p>';
                html += '           <p class="tag-agenda" style="font-weight:400">';
                html += agendamento.nome_profissional + ' | ';
                if (agendamento.retorno) html += 'Retorno: ';
                if (agendamento.descr_procedimento != null) html += agendamento.descr_procedimento + ' | ';
                if (agendamento.tipo_procedimento != null) html += agendamento.tipo_procedimento + ' | ';
                if (agendamento.convenio_nome != null) html += agendamento.convenio_nome;
                else html += 'Particular'
                html += '           </p>';
                html += '       </div>'

                html += '   <div class="tags">';
                html += '   </div>';

                html += '</div>';
                html += '</li>';
                $('#agendamentosPendentesModal #conteudo-lote-agenda').append(html);

            }
        })
    }
    )
}
function imprimirAgendamento() {
    let data_inicial = $("#agendamentosPendentesModal #data-inicial").val().replaceAll('/', '-'),
        data_final = $("#agendamentosPendentesModal #data-final").val().replaceAll('/', '-'),
        id_membro = $("#agendamentosPendentesModal #membro").val().replaceAll('/', '-'),
        status = $("#agendamentosPendentesModal #somente-finalizados").val(),
        completo_incompleto = $("#agendamentosPendentesModal #incompletos").val(),
        url = 'http://vps.targetclient.com.br/saude-beta/agenda/imprimir/'
    url += data_inicial + '/' + data_final + '/' + id_membro + '/' + status + '/' + completo_incompleto

    window.open(url, '_blank')

}








// CREDITOS
function abrirModalConversaoCredito(id_pedido, bAntigo) {
    $.get('/saude-beta/pedido/abrir-modal-conversao/' + id_pedido + '/' + bAntigo,
        function (data, status) {
            console.log(data + ' | ' + status)
            data = $.parseJSON(data)
            if (data === 'erro') {
                alert('erro')
                return;
            }
            else {
                $('#conversaoCreditoModal #id_pedido').val(id_pedido)
                $('#conversaoCreditoModal #bAntigo').val(bAntigo)
                $('#conversaoCreditoModal #data_contrato').html(data.contrato.data)
                $('#conversaoCreditoModal #data_validade').html(data.contrato.data_validade)
                $('#conversaoCreditoModal #table-conversao').empty()
                if (bAntigo == 1) {
                    data.planos.forEach(plano => {
                        html = ' <tr> '
                        html += '     <td width="100%%" class="text-left d-flex" data-id_para_conversao="' + plano.id_plano + '"> '
                        html += '         <div style="margin: -1px 10px 0px 0px;width: 5%;"> '
                        html += '             <input id="' + plano.id_plano + '" onclick="abrirModalQtdeConversao(' + plano.id_plano + ')" style="width:100%;height:100%" type="checkbox"> '
                        html += '         </div> '
                        html += plano.descr_plano
                        html += '     </td> '
                        html += '     <td id="qtd-conv-' + plano.id_plano + '" width="15%" class="text-right qtd_conv">' + 0 + '</td> '
                        html += '     <td data-qtd_rest="' + plano.qtde_restante + '" id="qtde-restante-' + plano.id_plano + '" width="15%" class="text-right">' + plano.qtde_restante + '</td> '
                        html += '     <td id="valor-und-' + plano.id_plano + '" width="15%" class="text-right valor_unitario">' + plano.valor_und.toFixed(2).replaceAll('.', ',') + '</td> '
                        html += '     <td class="valor-total text-right" id="valor-total-' + plano.id_plano + '" width="15%">' + plano.valor_total.toFixed(2).replaceAll('.', ',') + '</td> '
                        html += ' </tr> '
                        $('#conversaoCreditoModal #table-conversao').append(html)
                    })
                    $("#conversaoCreditoModal").modal('show');
                }
                else {
                    indice = 0;
                    data.planos.forEach(plano => {
                        html = ' <tr> '
                        html += '     <td width="100%%" class="text-left d-flex" data-id_para_conversao="' + plano.id_plano + '"> '
                        html += '         <div style="margin: -1px 10px 0px 0px;width: 5%;"> '
                        html += '             <input id="' + plano.id_plano + '" onclick="abrirModalQtdeConversao(' + plano.id_plano + ')" style="width:100%;height:100%" type="checkbox"> '
                        html += '         </div> '
                        html += plano.descr_plano
                        html += '     </td> '
                        html += '     <td id="qtd-conv-' + plano.id_plano + '" width="15%" class="text-right qtd_conv">' + 0 + '</td> '
                        html += '     <td data-qtd_rest="' + data.restantes_ar[indice] + '" id="qtde-restante-' + plano.id_plano + '" width="15%" class="text-right">' + data.restantes_ar[indice] + '</td> '
                        html += '     <td id="valor-und-' + plano.id_plano + '" width="15%" class="text-right valor_unitario">' + plano.valor_und.toFixed(2).replaceAll('.', ',') + '</td> '
                        html += '     <td class="valor-total text-right" id="valor-total-' + plano.id_plano + '" width="15%">' + plano.valor_total.toFixed(2).replaceAll('.', ',') + '</td> '
                        html += ' </tr> '
                        $('#conversaoCreditoModal #table-conversao').append(html)

                        indice++;
                    })
                    $("#conversaoCreditoModal").modal('show');
                }
            }
            atualizarValoresConversao()
        })

}
function abrirModalQtdeConversao($id_plano) {
    setTimeout(() => {
        console.log($id_plano)
        if ($('#' + $id_plano).prop('checked') === true) {
            restante = $('#conversaoCreditoModal #qtde-restante-' + $id_plano).html()
            qtde = $('#inserirQtdConvModal #qtd')
            console.log(restante)
            qtde.attr('max', restante)
            qtde.attr('min', 1)

            $('#inserirQtdConvModal #qtd').val(1)
            $('#inserirQtdConvModal #id_plano').val($id_plano)

            $('#inserirQtdConvModal').modal('show')
        }
        else {
            valor_und = parseFloat($('#conversaoCreditoModal #valor-und-' + $id_plano).html())
            restante = parseInt($('#conversaoCreditoModal #qtde-restante-' + $id_plano).data().qtd_rest)
            $('#conversaoCreditoModal #qtde-restante-' + $id_plano).html(restante)

            $('#conversaoCreditoModal #qtd-conv-' + $id_plano).html(0)
            $('#conversaoCreditoModal #valor-total-' + $id_plano).html((valor_und * restante).toFixed(2).replaceAll('.', ','))
        }
        atualizarValoresConversao()
    }, 200)
}


function atualizarValoresConversao() {
    aux1 = 0
    document.querySelectorAll('#conversaoCreditoModal .valor-total').forEach(el => {
        aux1 += parseFloat(el.innerHTML)
    })
    $("#conversaoCreditoModal #valor_total").html(aux1.toFixed(2).replaceAll('.', ','))

    aux2 = 0
    list1 = document.querySelectorAll('#conversaoCreditoModal .qtd_conv')
    list2 = document.querySelectorAll('#conversaoCreditoModal .valor_unitario')

    for (i = 0; i < list1.length; i++) {
        aux2 += parseInt(list1[i].innerHTML) * parseFloat(list2[i].innerHTML)
    }
    $("#conversaoCreditoModal #valor_total_conversao").html(aux2.toFixed(2).replaceAll('.', ','))
}

function inserir_qtde_conversao() {
    id_plano = $('#inserirQtdConvModal #id_plano').val()
    qtd_conv = $('#conversaoCreditoModal #qtd-conv-' + id_plano)
    qtd_rest = $('#conversaoCreditoModal #qtde-restante-' + id_plano)

    valor_und = $('#conversaoCreditoModal #valor-und-' + id_plano)
    valor_total = $('#conversaoCreditoModal #valor-total-' + id_plano)

    qtde = $('#inserirQtdConvModal #qtd')


    qtd_conv.html(qtde.val())
    valor_total.html((parseFloat(valor_total.html().replace(",", ".")) - (parseFloat(valor_und.html().replace(",", ".")) * qtde.val().replace(",", "."))).toFixed(2).replaceAll('.', ','))
    qtd_rest.html(parseInt(qtd_rest.html()) - parseInt(qtde.val()))
    $('#inserirQtdConvModal').modal('hide')
    atualizarValoresConversao()
}
function creditos_por_pessoa($id_pessoa) {
    dinicio = $("#data-inicial-credito").val()
    dfinal = $("#data-final-credito").val()

    if (dinicio === '') dinicio = 0;
    if (dfinal === '') dfinal = 0
    if ($("#data-inicial-credito").val() === '')
        $.get(
            '/saude-beta/pedido/listar-mov-credito/' + $id_pessoa + '/' + dinicio + "/" + dfinal,
            function (data, status) {
                console.log(data + status)
                data = $.parseJSON(data);
                if (data.creditos == 'null' || data.creditos == null) {
                    data.creditos = 0
                }
                $('#valor-total-creditos').html(data.creditos.toFixed(2).replace('.', ','))

                $('#table-mov-credito > tbody').empty()
                data.movimentacoes.forEach(mov => {
                    if (mov.tipo_transacao === 'E') mov.tipo_transacao = 'Entrada'
                    else mov.tipo_transacao === 'Saída'

                    html = ' <tr> '
                    html += '     <td width="8%" class="text-left">' + mov.id + '</td> '
                    html += '     <td width="8%" class="text-left">' + mov.id_pedido + '</td> '
                    html += '     <td width="32%" class="text-left">' + mov.planos + '</td> '
                    html += '     <td width="8%" class="text-right">' + formatDataBr(mov.created_at) + '</td> '
                    html += '     <td width="8%" class="text-right">' + mov.created_at.substr(11, 5) + '</td> '
                    html += '     <td width="10%" class="text-right">' + mov.valor.toFixed(2).replace('.', ',') + '</td> '
                    html += '     <td width="10%" class="text-right">' + mov.tipo_transacao + '</td> '
                    html += '     <td width="12%" class="text-right">' + mov.created_by + '</td> '
                    html += ' </tr> '

                    $('#table-mov-credito > tbody').append(html)
                })
            }
        )
}
function converter_creditos() {
    if (parseFloat($('#conversaoCreditoModal #valor_total_conversao').html()) === 0) {
        alert('Nenhum plano selecionado')
        return;
    }
    $ids = []
    $qtd_conv = []
    $total_conversao = $('#conversaoCreditoModal #valor_total_conversao').html().replace(',', '.')
    qtde_planos = 0
    document.querySelectorAll("#conversaoCreditoModal [data-id_para_conversao]").forEach(el => {
        $ids.push(el.dataset.id_para_conversao)
    })
    document.querySelectorAll("#conversaoCreditoModal .qtd_conv").forEach(el => {
        $qtd_conv.push(el.innerHTML)
        qtde_planos += parseInt(el.innerHTML)
    })
    if (window.confirm(qtde_planos + ' planos serão convertidos, deseja prosseguir?')) {
        $.get('/saude-beta/pedido/converter', {
            bAntigo: $('#conversaoCreditoModal #bAntigo').val(),
            ids: $ids,
            qtds: $qtd_conv,
            valor_total: $total_conversao,
            id_pessoa: $('#id_pessoa_prontuario').val(),
            id_contrato: $('#conversaoCreditoModal #id_pedido').val()
        }, function (data, status) {
            console.log(data + ' | ' + status)
            if (data === 'true') {
                alert('Convertido com sucesso')
                location.reload(true)
            }
        })
    }
    else {

    }
}



function atualizarValoresConversaoPedido() {
    creditos = $('#pedidoModal #creditos-pessoa')
    valor = $('#pedidoModal #pedido_forma_pag_valor')

    aux = 0
    $('#pedidoModal [data-forma_pag="101"]').each(function ($sql) {
        aux += parseFloat($(this).parent().find('[data-forma_pag_valor]').data().forma_pag_valor)
    })

    total_gasto = parseFloat(valor.val().replace(',', '.')) + (aux / 2)

    if (creditos.val().replace(',', '.') != 0 && !isNaN(valor.val().replace(',', '.')) && parseFloat(valor.val().replace(',', '.')) <= total_gasto) {
        creditos.val(parseFloat(creditos.data().creditos) - total_gasto)
    }
}




var temp


// MEMBROS MOBILE \\
function abrirAgendaMobileModal($obj) {
    $('#agendaMobileModal #nome-paciente').html(captalize($($obj).data().paciente.substr(0, 25)))
        .css("text-align", "center")
    $('#agendaMobileModal #id_agendamento').val($($obj).data().id_agendamento)
    $('#agendaMobileModal #id_paciente').val($($obj).data().id_paciente)
    $('#agendaMobileModal #antigo').val($($obj).data().antigo)

    temp = $obj
    $('#agendaMobileModal #bloquear-grade-agendamento-mobile').attr('onclick', "bloquear_desbloquear_grade('" + $obj[0].parentNode.dataset.dia + "'" + ',' + "'" + $obj[0].parentNode.dataset.horario + "'" + ',' + $obj[0].parentNode.dataset.id_grade_horario + ')')

    html = ' <div style="width:10%;margin: 0px 0px 0px -15%;"> '
    html += '   <img class="custom-image" style="min-height: 0% !important;" src="http://vps.targetclient.com.br/saude-beta/img/areas/'
    html += $($obj).data().id_modalidade + '.png'
    html += '"></div> '
    html += '<p style="padding: 1% 1% 1% 3%;font-size: 125%;color: #110355;font-weight: 600;font-family: system-ui;">'
    html += captalize($($obj).data().modalidade)
    html += '</p>'
    $('#agendaMobileModal #modalidade').empty()
    $('#agendaMobileModal #modalidade').append(html)
    html += ' <img class="foto-paciente-agenda my-auto" src="/saude-beta/img/pessoa/' + $($obj).data().id_paciente + '.jpg" onerror="this.onerror=null;this.src=' + "'/saude-beta/img/paciente_default.png'" + '">'
    $('#agendaMobileModal').modal('show')
}

function abrirModalVigenciaPlano($id) {
    $('#vigenciaPlanoModal #id_plano').val($id)
    listar_vigencias_plano($id)
    $('#vigenciaPlanoModal').modal('show')
}
function add_vigencia_plano() {
    if (parseInt($("#vigenciaPlanoModal #ate").val()) < parseInt($("#vigenciaPlanoModal #de").val())) {
        alert('valores incorretos!')
        return;
    }
    $.post('/saude-beta/tabela-precos/adicionar-vigencia-plano', {
        _token: $("meta[name=csrf-token]").attr("content"),
        id_plano: $("#vigenciaPlanoModal #id_plano").val(),
        de: $('#vigenciaPlanoModal #de').val(),
        ate: $("#vigenciaPlanoModal #ate").val(),
        vigencia: $("#vigenciaPlanoModal #vigencia").val()
    }, function (data, status) {
        console.log(data + ' | ' + status)
        if (data.error) {
            alert(data.error)
        }
        else {
            listar_vigencias_plano($("#vigenciaPlanoModal #id_plano").val())
        }
    })
}
function listar_vigencias_plano($id) {
    $.get('/saude-beta/tabela-precos/listar-vigencia-plano/' + $('#vigenciaPlanoModal #id_plano').val(), function (data) {
        $("#vigenciaPlanoModal #table-metas").empty();
        maior = 0
        cont = 0
        data.forEach(vigencia => {
            html = '<tr data-id="' + vigencia.id + '">'
            html += '   <td width="25%" class="ate">' + vigencia.de + ' planos</td>'
            html += '   <td width="25%" class="de">' + vigencia.ate + ' planos</td>'
            html += '   <td width="40%"class="vigencia">' + vigencia.vigencia + ' dias</td>'

            if (cont < 1) {
                html += '<td width="10%">'
                html += '       <img src="img/lixeira-de-reciclagem.png" style="max-width: 45%;cursor: pointer;opacity: .8;" onclick="excluir_vigencia_plano(' + vigencia.id + ')">'
                html += ' </td>'

            }
            else '<td width="10%"></td>'
            cont = 1
            html += '</tr>'
            $("#table-metas").append(html)
            if (parseInt(vigencia.ate) > maior) maior = parseInt(vigencia.ate);
        })
        $("#vigenciaPlanoModal #de").val(maior + 1)
        $("#vigenciaPlanoModal #ate").val(maior + 2).attr('min', maior + 2),
            $("#vigenciaPlanoModal #valor").val('')
    })
}
function excluir_vigencia_plano($id) {
    $.post(
        '/saude-beta/tabela-precos/excluir-vigencia-plano', {
        _token: $("meta[name=csrf-token]").attr('content'),
        id: $id
    }, function (data, status) {
        console.log(data + ' | ' + status)
        listar_vigencias_plano($id);
    }
    )
}
function abrirModalAlterarEmpresa() {
    $.get(
        '/saude-beta/pessoa/empresa', {},
        function (data, status) {
            data = $.parseJSON(data)
            console.log(data + ' | ' + status)
            $('#alterarEmpresaModal #empresa').empty()
            data.empresas.forEach(el => {
                $('#alterarEmpresaModal #empresa').append('<option value="' + el.id + '">' + el.descr + "</option>")
            })
            $('#alterarEmpresaModal #empresa').val(data.empresa);
        }
    )
    $('#alterarEmpresaModal').modal('show')
}

function atividades_por_pessoa() {
    $.get(
        'http://vps.targetclient.com.br/saude-beta/pedido/atividades-por-pessoa/' + $('#id_pessoa_prontuario').val(),
        function (data) {
            data = $.parseJSON(data)

            $('#total-atividades-prontuario').html('TOTAL: ' + data.total)
            $('#disponivel-atividades-prontuario').html('DISPONÍVEL: ' + data.disponivel)
            $('#agendados-atividades-prontuario').html('AGENDADOS: ' + data.agendados)
        }

    )
}
function agendamentos_atividades_modal() {
    var dia_temp = ''
    $.get(
        'http://vps.targetclient.com.br/saude-beta/pedido/agendamentos-por-pessoa/' + $('#id_pessoa_prontuario').val(),

        function (data, status) {
            console.log(data + ' | ' + status)
            data = $.parseJSON(data);
            $('#agendamentosAtividadesModal #conteudo-lote-agenda').empty()
            if (data.length > 0) {
                data.forEach(agendamento => {
                    console.log('Agendamento Semanal:');
                    console.log(agendamento);
                    if (agendamento.data != dia_temp) {
                        if (dia_temp != undefined) $('#agendamentosAtividadesModal #conteudo-lote-agenda').append('<hr>');
                        html = '<h4 style="color:#212529">' + moment(agendamento.data).format('LL'); + '</h4>';
                        $('#agendamentosAtividadesModal #conteudo-lote-agenda').append(html);
                        dia_temp = agendamento.data;
                    }

                    if (agendamento.id != undefined && agendamento.id != null) {
                        html = '<li data-id_agendamento="' + agendamento.id + '"';
                        html += ' data-status="' + agendamento.id_status + '"';
                        html += ' data-paciente="' + agendamento.nome_paciente + '"';
                        html += ' data-procedimento="' + agendamento.descr_procedimento + '"';
                        html += ' data-convenio="' + agendamento.convenio_nome + '"';
                        html += ' data-permite_fila_espera="' + agendamento.permite_fila_espera + '"';
                        html += ' data-permite_reagendar="' + agendamento.permite_reagendar + '"';
                        html += ' data-permite_editar="' + agendamento.permite_editar + '"';
                        html += ' data-libera_horario="' + agendamento.libera_horario + '"';
                        if (agendamento.antigo == 0) {
                            html += ' ondblclick="editar_agendamento(' + agendamento.id + ',0)" '
                            html += ' onclick="editar_agendamento(' + agendamento.id + ', 1)" '
                        }
                        else {
                            html += ' ondblclick="antigo_editar_agendamento(' + agendamento.id + ',0)" '
                            html += ' onclick="antigo_editar_agendamento(' + agendamento.id + ', 1)" '
                        }
                        html += ' '
                        html += ' title="' + agendamento.descr_status + '\n' + agendamento.obs + '"';
                        html += ' style="background:' + agendamento.cor_status + '; color: ' + agendamento.cor_letra + ';max-height: 94px;min-width: 100%;margin-bottom: 20px;cursor: pointer" >';

                        html += '    <div class="my-1 mx-1 d-flex">';
                        html += '       <img class="foto-paciente-agenda" data-id_paciente="' + agendamento.id_paciente + '" src="/saude-beta/img/pessoa/'+ agendamento.id_paciente + '.jpg" onerror="this.onerror=null;this.src=' + "'/saude-beta/img/paciente_default.png'" + '" onclick="verificar_cad_redirecionar(' + agendamento.id_paciente + ')">';
                        html += '       <div>';
                        html += '           <p class="col p-0">';
                        html += '               <span class="ml-0 my-auto" style="font-weight:600" onclick="verificar_cad_redirecionar(' + agendamento.id_paciente + ')">';
                        html += agendamento.hora.substring(0, 5) + '  -  ' + agendamento.nome_paciente.toUpperCase();
                        html += '               </span>';
                        html += '           </p>';
                        html += '           <p class="tag-agenda" style="font-weight:400">';
                        html += agendamento.nome_profissional + ' | ';
                        if (agendamento.retorno) html += 'Retorno: ';
                        if (agendamento.descr_procedimento != null) html += agendamento.descr_procedimento + ' | ';
                        if (agendamento.tipo_procedimento != null) html += agendamento.tipo_procedimento + ' | ';
                        if (agendamento.convenio_nome != null) html += agendamento.convenio_nome;
                        else html += 'Particular'
                        html += '           </p>';
                        html += '       </div>'

                        html += '   <div class="tags">';
                        html += '   </div>';

                        html += '</div>';
                        html += '</li>';
                        $('#agendamentosAtividadesModal #conteudo-lote-agenda').append(html);
                        $('#agendamentosAtividadesModal').modal('show')

                    }
                })
            }
        }
    );

}
function formatDataUniversal(date) {
    return date.substr(6) + '-' + date.substr(3, 2) + '-' + date.substr(0, 2)
}

var testando
function encontrarContratosRA() {
    var paciente = $('#paciente_id'),
        datainicial = $('#data-inicial'),
        datafinal = $('#data-final'),
        contrato = $('#contrato'),
        plano = $('#plano')
    if ($('#sistema-antigo').val() == 0) {
        url_api = '/saude-beta/relatorio-atividades/listar-contratos'
    }
    else {
        url_api = '/saude-beta/relatorio-atividades/listar-contratos-antigos'
    }
    if (datainicial.val() != '') dinicial = formatDataUniversal(datainicial.val())
    else dinicial = ''
    if (datafinal.val() != '') dfinal = formatDataUniversal(datafinal.val())
    else dfinal = ''
    $.get(url_api, {
        id_paciente: paciente.val(),
        datainicial: dinicial,
        datafinal: dfinal,
        contrato: contrato.val(),
        plano: plano.val()
    }, function (data, status) {
        testando = data
        console.log(data + ' | ' + status)


        if (data.length > 0) {
            contrato.empty().removeAttr('disabled', true)
            contrato.append('<option value="0" disabled>Selecionar contrato...</option>')
            data.forEach(c2 => {
                if (c2.descr == null) c2.descr = "Modalidade não encontrada"
                contrato.append('<option value="' + c2.id + '">Contrato realizado em: ' + formatDataBr(c2.data) + ' | ' + c2.descr + '</option>')
            })
        }
        else {
            contrato.empty().attr('disabled', true)
        }
    })
}
function encontrarPlanosRA() {
    if ($('#sistema-antigo').val() == 0) {
        url_api = '/saude-beta/relatorio-atividades/listar-planos'
    }
    else {
        url_api = '/saude-beta/relatorio-atividades/listar-planos-antigos'
    }

    $('#plano').empty()
    $('#plano').append('<option>Carregando...</option>')
    $.get(url_api + '/' + $('#contrato').val(),
        function (data, status) {
            console.log(data + ' | ' + status)
            if (data.length > 0) {
                $('#plano').empty()
                    .append('<option value="0">Todos os planos</option>')
                    .removeAttr('disabled')
                data.forEach(pl => {
                    $('#plano').append('<option value="' + pl.id + '">' + pl.descr + '</option>')
                })
            }
            else {
                $('#plano'.attr('disabled', true))
            }
        })
}

function disable_elements() {
    $('#contrato').attr("disabled", true)
        .empty()
    $('#plano').attr("disabled", true)
        .empty()
    $('#paciente_nome').val('')
    $('#paciente_id').val('')
    $('#data-inicial').val('')
    $('#data-final').val('')
}

function importAPIestados() {
    var html = '';
    $.get("https://servicodados.ibge.gov.br/api/v1/localidades/estados",
        function (data) {
            $("#pessoaModal #uf-crm").empty();
            $('#pessoaModal #uf-crm').append('<option value=""></option>')
            data.forEach(estado => {
                html = '<option value="' + estado.sigla + '">'
                html += estado.sigla + "</option>";
                $('#pessoaModal #uf-crm').append(html);
            })
            $("#pessoaModal #uf-cref").empty();
            $('#pessoaModal #uf-cref').append('<option value=""></option>')
            data.forEach(estado => {
                html = '<option value="' + estado.sigla + '">'
                html += estado.sigla + "</option>";
                $('#pessoaModal #uf-cref').append(html);
            })
            $("#pessoaModal #uf-creft").empty();
            $('#pessoaModal #uf-creft').append('<option value=""></option>')
            data.forEach(estado => {
                html = '<option value="' + estado.sigla + '">'
                html += estado.sigla + "</option>";
                $('#pessoaModal #uf-creft').append(html);
            })
            $("#pessoaModal #uf-crn").empty();
            $('#pessoaModal #uf-crn').append('<option value=""></option>')
            data.forEach(estado => {
                html = '<option value="' + estado.sigla + '">'
                html += estado.sigla + "</option>";
                $('#pessoaModal #uf-crn').append(html);
            })
        })
}

var possuiAgendamento = true;
function listarAgendamentosDiarios(id_evolucao) {
    var html = ''
    $.get(
        '/saude-beta/evolucao/listar-agendas/' + $('#id_pessoa_prontuario').val(),
        function (data, status) {
            $('#listaAgendamentosDiario #conteudo-lote-agenda').empty();
            if (data.length > 0) {
                data.forEach(agendamento => {
                    if (agendamento.id != undefined && agendamento.id != null) {
                        html = '<li data-id_agendamento="' + agendamento.id + '"';
                        html += ' onclick="preencherEncaminhamentoEvolucao(' + agendamento.id + ')"';
                        html += ' style="background:' + agendamento.cor_status + '; color: ' + agendamento.cor_letra + ';max-height: 94px;min-width: 100%;margin-bottom: 20px; cursor: pointer" >';

                        html += '    <div class="my-1 mx-1 d-flex">';
                        html += '       <img class="foto-paciente-agenda" data-id_paciente="' + agendamento.id_paciente + '" src="/saude-beta/img/pessoa/' + agendamento.id_paciente + '.jpg" onerror="this.onerror=null;this.src=' + "'/saude-beta/img/paciente_default.png'" + '" onclick="verificar_cad_redirecionar(' + agendamento.id_paciente + ')">';
                        html += '       <div>';
                        html += '           <p class="col p-0">';
                        html += '               <span class="ml-0 my-auto" style="font-weight:600">';
                        html += agendamento.hora.substring(0, 5) + '  -  ' + agendamento.nome_paciente.toUpperCase();
                        html += '               </span>';
                        html += '           </p>';
                        html += '           <p class="tag-agenda" style="font-weight:400">';
                        html += agendamento.nome_profissional + ' | ';
                        if (agendamento.retorno) html += 'Retorno: ';
                        if (agendamento.descr_procedimento != null) html += agendamento.descr_procedimento + ' | ';
                        if (agendamento.tipo_procedimento != null) html += agendamento.tipo_procedimento + ' | ';
                        if (agendamento.convenio_nome != null) html += agendamento.convenio_nome;
                        else html += 'Particular'
                        html += '           </p>';
                        html += '       </div>'

                        html += '   <div class="tags">';
                        html += '   </div>';

                        html += '</div>';
                        html += '</li>';
                        $('#listaAgendamentosDiario #conteudo-lote-agenda').append(html);
                        $('#listaAgendamentosDiario').modal('show');
                    }
                })
            }
            else possuiAgendamento = false;
        }
    );
    if (!possuiAgendamento) $('#encaminhamento_modal').modal('show');
    document.getElementById("id_evolucao").value = id_evolucao;
    duplicarFuncaoC++;
    if (duplicarFuncaoC == 3) {
        clearInterval(duplicarFuncao);
        clicado = true;
    }
}

function mostrarEncaminhamento(id_evolucao) {
    openModalEncaminhamento(id_evolucao);
}

function openModalCaixa() {
    $('#caixaModal').modal('show');
}

var duplicarFuncao, duplicarFuncaoC = 0, clicado = false;
function openModalEncaminhamento(id_evolucao) {
    if (!clicado) {
        duplicarFuncao = setInterval(function () {
            listarAgendamentosDiarios(id_evolucao);
        }, 200);
    } else listarAgendamentosDiarios(id_evolucao);
}

function preencherEncaminhamentoEvolucao($id) {
    $('#encaminhamento_modal').modal('show');
    $('#listaAgendamentosDiario').modal('hide')
    $('#encaminhamento_modal #id_agendamento').val($id)
}

function adicionar_encaminhamento() {
    area = $('#encaminhamento_modal #areaEncamin').val()
    qtd_semana = $('#encaminhamento_modal #qtdSemana').val()
    tempo = $('#encaminhamento_modal  #tempoPrevisto').val()


    if (area == null || qtd_semana == null || tempo == null) return
    html = '<tr> '
    html += '    <td data-area="' + area + '" width="40%" class="text-left">' + area + '</td> '
    html += '    <td data-qtd_semana="' + qtd_semana + '" width="30%" class="text-right">' + qtd_semana + '</td> '
    html += '    <td data-tempo="' + tempo + '" width="25%" class="text-right">' + tempo + '</td> '
    html += '    <td width="5%" ><img  style="cursor:pointer;" onclick="delete_reabilit($(this).parent().parent().remove())" src="http://vps.targetclient.com.br/saude-beta/img/lixeira.png"></td>'
    html += '</tr> '
    $('#tbody-encaminhamento').append(html)

    $('#button-add-habilit').css('top', (parseInt($('#button-add-habilit').css('top')) + 49) + 'px')
}

function delete_reabilit() {

    $('#button-add-habilit').css('top', (parseInt($('#button-add-habilit').css('top')) - 49) + 'px')
}

function opcoesEncaminhamento() {
    if (document.getElementById("testeEncamin").value == "VO2 Específico") {
        document.getElementById("esporteEncamin").style.display = "flex";
    } else {
        document.getElementById("esporteEncamin").style.display = "none";
    }

    if (document.getElementById("testeEncamin").value == "Teste de força") {
        document.getElementById("testeforcaCheck").style.display = "flex";
    } else {
        document.getElementById("testeforcaCheck").style.display = "none";
    }
}

function adicionar_encaminhamento_habilitacao() {

    var testeSuperior = document.querySelector('input[name="checkbox-superior"]:checked');
    var testeInferior = document.querySelector('input[name="checkbox-inferior"]:checked');

    if (testeSuperior != null) {
        testeSuperior = 'Superior'
    }
    if (testeInferior != null) {
        testeInferior = 'Inferior'
    }

    if (document.getElementById('esporteEncamin').style.display == "none") {
        infoAdicional = null;
    }

    if (document.getElementById("testeforcaCheck").style.display == "none") {
        testeSuperior = null;
        testeInferior = null;
    }

    vo2 = $('#encaminhamento_modal #testeEncamin').val()
    obs = $('#encaminhamento_modal #obs-encamin').val()
    infoAdicional = $('#encaminhamento_modal #esporte').val()


    html = '<tr> '
    html += '    <td data-vo2="' + vo2 + '" width="40%" class="text-left">' + vo2 + '</td> '
    html += '    <td data-obs="' + obs + '" width="30%" class="text-left" style="word-break: break-all;">' + obs + '</td> '
    if (testeSuperior != null && testeInferior != null) {
        infoAdicional = null;
        html += '     <td data-infoAdicional="' + testeSuperior + '/' + testeInferior + '"width="25%" class="text-left">' + testeSuperior + '/' + testeInferior + '</td>'
    }
    if (testeSuperior != null && testeInferior == null) {
        testeInferior = null;
        infoAdicional = null;
        html += '    <td data-infoAdicional="' + testeSuperior + ' " width="25%" class="text-left">' + testeSuperior + '</td> '
    }
    if (testeInferior != null && testeSuperior == null) {
        testeSuperior = null;
        infoAdicional = null;
        html += '    <td data-infoAdicional="' + testeInferior + '"width="25%" class="text-left">' + testeInferior + '</td>'
    }
    if (infoAdicional != null) {
        testeInferior = null;
        testeSuperior = null;
        html += '    <td data-infoAdicional="' + infoAdicional + '"width="25%" class="text-left">' + infoAdicional + '</td>'
    }
    html += '    <td width="5%" ><img  style="cursor:pointer;" onclick="$(this).parent().parent().remove()" src="http://vps.targetclient.com.br/saude-beta/img/lixeira.png"></td>'
    html += '</tr> '
    $('#tbody-encaminhamento-habilitacoes').append(html)
}

function salvar_encaminhamento() {
    valor1 = []
    valor2 = []
    valor3 = []
    tipo = []

    document.querySelectorAll('#tbody-encaminhamento > tr').forEach(el => {
        tipo.push('reabilitacao')
        valor1.push($(el).find('[data-area]').data().area)
        valor2.push($(el).find('[data-qtd_semana]').data().qtd_semana)
        valor3.push($(el).find('[data-tempo]').data().tempo)

    })

    document.querySelectorAll('#tbody-encaminhamento-habilitacoes > tr').forEach(el => {
        tipo.push('habilitacao')
        valor1.push($(el).find('[data-vo2]').data().vo2)
        valor2.push($(el).find('[data-obs]').data().obs)
        valor3.push($(el).find('[data-infoadicional]').data().infoadicional)
    })
    $.get('/saude-beta/encaminhamento/salvar-encaminhamento', {
        _token: $('meta[name=csrf-token]').attr('content'),
        id_agendamento: $('#encaminhamento_modal #id_agendamento').val(),

        tipo: tipo,
        valor1: valor1,
        valor2: valor2,
        valor3: valor3,
        id_paciente: $('#id_pessoa_prontuario').val(),
        id_evolucao: $("#id_evolucao").val()
    }, function (data, status) {
        console.log(data + ' | ' + status)
        console.log(tipo)
        console.log(valor1)
        console.log(valor2)
        console.log(valor3)
        alert('Encaminhamento feito com sucesso')
        location.reload(true)
    })
}

function fechaModalTabelaEncaminhamento() {
    $('#tabelas_encaminhamento_modal').modal('hide');
}

function desbloquearplanos(id_agendamento) {
    // console.log(id_agendamento)
    // $('#tabelas_encaminhamento_modal').modal('hide');

    // if (campo_invalido("#criarAgendamentoModal #paciente_id", true) || campo_invalido("#criarAgendamentoModal #paciente_nome", false)) {
    //     alert('Campo "Associado" inválido!')
    //     return;
    // }
    // if (campo_invalido("#criarAgendamentoModal #id_tipo_procedimento", true)) {
    //     alert("Selecione um tipo de agendamento para prosseguir!")
    //     return;
    // }
    // if (campo_invalido("#criarAgendamentoModal #modalidade_id", true)) {
    //     alert("Selecione uma modalidade para prosseguir")
    //     return;
    // }

    // else if ($("#criarAgendamentoModal #id_tipo_procedimento").val() == 4) {

    //     if (campo_invalido("#criarAgendamentoModal #procedimento_id", true)) {
    //         alert("Selecione um plano para prosseguir!")
    //         return;
    //     }
    // }
    // else if ($("#criarAgendamentoModal #id_tipo_procedimento").val() == 1) {

    //     if (campo_invalido("#criarAgendamentoModal #id_contrato", true)) {
    //         alert("Selecionar um contrato é obrigatório para este tipo de agendamento")
    //         return;
    //     }
    //     if (campo_invalido("#criarAgendamentoModal #id_plano")) {
    //         alert("Selecione um plano do contrato")
    //         return;
    //     }
    // }
    // else if ($("#criarAgendamentoModal #id_tipo_procedimento").val() == 5) {

    //     if (campo_invalido("#criarAgendamentoModal #modalidade_id", true)) {
    //         alert("Selecione uma modalidade para prosseguir!")
    //         return;
    //     }
    // }

    // $.post(
    //     '/saude-beta/agenda/salvar_op_bordero', {
    //     _token: $("meta[name=csrf-token]").attr("content"),
    //     id_agendamento: $("#criarAgendamentoModal #id").val(),
    //     bordero: $("#criarAgendamentoModal #bordero_b").prop('checked')

    // }, function (data, status) {
    //     console.log(data + ' | ' + status)
    //     data = $.parseJSON(data);
    //     console.log(data.id_encaminhamento)
    //     // $("#criarAgendamentoModal #id_encaminhamento").val(data.id_encaminhamento)

    // }
    // )
    // $.get(
    //     '/saude-beta/agenda/faturar/' + id_agendamento,
    //     function (data, status) {
    //         data = $.parseJSON(data);
    //         console.log(data + ' | ' + status)

    //         if ($("#criarAgendamentoModal #id_tipo_procedimento").val() == 4) {
    //             $("#pedidoModal #pedido_paciente_nome").val(data.descr_pessoa)
    //             $("#pedidoModal #pedido_paciente_id").val(data.id_pessoa)
    //             $("#pedidoModal #agenda_id").val(id_agendamento)

    //             $("#pedidoModal #pedido_id_convenio").empty()
    //             if (data.descr_convenio != null) {
    //                 $("#pedidoModal #pedido_id_convenio").append('<option value="' + data.id_convenio + '">' + data.descr_convenio + '</option>')
    //             }
    //             else {
    //                 $("#pedidoModal #pedido_id_convenio").append('<option value="0">Sem convênio...</option>')
    //             }

    //             $("#pedidoModal #pedido_paciente_nome").attr("disabled", 'true')
    //             $("#pedidoModal #pedido_id_convenio").attr('disabled', 'true')


    //             $('#pedidoModal #id_plano').removeAttr('disabled')
    //             $('#pedidoModal #button-aceitar').val('1')

    //             //resetar_modal_pedido()

    //             $("#pedidoModal").modal('show')


    //         }
    //         else if (data == 0) {
    //             if (window.confirm('Deseja finalizar agendamento?')) {
    //                 console.log(id_agendamento)
    //                 $.post(
    //                     '/saude-beta/agenda/finalizar-agendamento', {
    //                     _token: $("meta[name=csrf-token]").attr("content"),
    //                     id: id_agendamento,
    //                 }, function (data, status) {
    //                     a = data;
    //                     console.log(data + ' | ' + status)
    //                     if (!isNaN(data)) {
    //                         alert('Agendamento confirmado');
    //                         $("#criarAgendamentoModal").modal('hide')
    //                         mostrar_agendamentos();
    //                         mostrar_agendamentos_semanal();
    //                         pesquisarAgendamentosPendentes()
    //                     }
    //                     else alert('erro')
    //                 }
    //                 )
    //             }
    //         }
    //     })
}

function encaminhanteModal() {
    $("#encaminhanteModal").modal("show");
    $.get("/saude-beta/encaminhamento/especialidade/listar", function(data) {
        data = $.parseJSON(data);
        var resultado = "";
        for (var i = 0; i < data.length; i++) resultado += "<option value = '" + data[i].id + "'>" + data[i].descr + "</option>";
        document.getElementById("enc_esp").innerHTML = resultado;
        var from = location.href.indexOf("agenda") > -1 ? "agenda" : "pedido";
        if ($("#" + from + "_encaminhante_id").val() != "") {
            $.get("/saude-beta/encaminhamento/encaminhante/obter", {
                id : $("#" + from + "_encaminhante_id").val()
            }, function(data) {
                data = $.parseJSON(data);
                var especialidades = new Array();
                for (var i = 0; i < data.especialidades.length; i++) especialidades.push(data.especialidades[i].id.toString());
                console.log(especialidades);
                $("#enc_nome").val(data.encaminhante.nome_fantasia);
                $("#enc_tel").val(data.encaminhante.telefone);
                $("#enc_doc").val(data.encaminhante.documento);
                $("#enc_doc_uf").val(data.encaminhante.documento_estado);
                $("#enc_tpdoc").val(data.encaminhante.tpdoc);
                $("#enc_esp").val(especialidades);
                encaminhante_tamanho();
            });
        }
    });
}
function muda_legenda_encaminhante(val) {
    $("#enc_label").html(val != "" ?
        "<a href = 'javascript:encaminhanteModal();'>Editar encaminhante</a>"
    :
        "Encaminhante não cadastrado? Clique <a href = 'javascript:encaminhanteModal();'>aqui</a> para cadastrar"
    );
}