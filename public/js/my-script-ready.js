function liberaEnd() {
    $("input#cidade").val("");
    $("input#uf").val("");
    $("input#endereco").val("");
    $("input#bairro").val("");

    $("input#cidade").attr("disabled", false);
    $("input#uf").attr("disabled", false);
    $("input#endereco").attr("disabled", false);
    $("input#bairro").attr("disabled", false);
}

$(document).ready(function() {
    $("#floco").hover(function(){
        $(this).prop("src", "http://vps.targetclient.com.br/saude-beta/img/floco-de-neve (2).png");
        }, function(){
        $(this).prop("src", "http://vps.targetclient.com.br/saude-beta/img/floco-de-neve (1).png");
    });
    $("input#cep").keyup(function() {
        var value = $(this).val();
        if (value.length > 8) {
            var url = "https://viacep.com.br/ws/" + value + "/json";
            $.get(url, function(data) {
                try {
                    $("input#cidade").val(data.localidade);
                    $("input#uf").val(data.uf);
                    $("input#endereco").val(data.logradouro);
                    $("input#bairro").val(data.bairro);
                    $("input#complemento").val(data.complemento);

                    if (data.localidade.trim() != "")
                        $("input#cidade").attr("readonly", true);
                    if (data.uf.trim() != "")
                        $("input#uf").attr("readonly", true);
                    if (data.logradouro.trim() != "")
                        $("input#endereco").attr("readonly", true);
                    if (data.bairro.trim() != "")
                        $("input#bairro").attr("readonly", true);
                    if (data.logradouro.trim() != "")
                        $("input#endereco").attr("readonly", true);
                    if (data.bairro.trim() != "")
                        $("input#bairro").attr("readonly", true);
                } catch(err) {
                    $("input#cep").val('');
                    liberaEnd();
                }
            });
        } else liberaEnd();
    });

    $("input#resp-cep").keyup(function() {
        var value = $(this).val();
        if (value.length > 8) {
            var url = "https://viacep.com.br/ws/" + value + "/json";
            $.get(url, function(data) {
                if (data != "Erro") {
                    $("input#resp-cidade").val(data.localidade);
                    $("input#resp-uf").val(data.uf);
                    $("input#resp-endereco").val(data.logradouro);
                    $("input#resp-bairro").val(data.bairro);
                    $("input#resp-complemento").val(data.complemento);

                    if (data.localidade.trim() != "")
                        $("input#resp-cidade").attr("readonly", true);
                    if (data.uf.trim() != "")
                        $("input#resp-uf").attr("readonly", true);
                    if (data.logradouro.trim() != "")
                        $("input#resp-endereco").attr("readonly", true);
                    if (data.bairro.trim() != "")
                        $("input#resp-bairro").attr("readonly", true);
                    if (data.logradouro.trim() != "")
                        $("input#resp-endereco").attr("readonly", true);
                    if (data.bairro.trim() != "")
                        $("input#resp-bairro").attr("readonly", true);
                }
            });
        } else {
            $("input#resp-cidade").val("");
            $("input#resp-uf").val("");
            $("input#resp-endereco").val("");
            $("input#resp-bairro").val("");

            $("input#resp-cidade").removeAttr("readonly");
            $("input#resp-uf").removeAttr("readonly");
            $("input#resp-endereco").removeAttr("readonly");
            $("input#resp-bairro").removeAttr("readonly");
        }
    });

    $(".summernote").each(function() {
        $(this).summernote({
            lang: "pt-BR", // default: 'en-US'
            height: 175,
            placeholder: "...",
            toolbar: [
                // [groupName, [list of button]]
                ["style", ["bold", "italic", "underline", "clear"]],
                ["font", ["strikethrough", "superscript", "subscript"]],
                ["fontsize", ["fontname", "fontsize"]],
                ["color", ["color"]],
                ["para", ["ul", "ol", "paragraph", "height"]],
                ["help", ["help"]],
                ["etc", ["undo", "redo", "fullscreen"]]
            ]
        });
    });

    $('#evolucaoPedidoModal #diagnostico').summernote({
        width: 150,   //don't use px
      });


    // KEYCODE: 38 - CIMA | 40 - BAIXO
    $(".autocomplete").each(function() {
        if ($(this).attr('id') == 'agenda_profissional'){
            $(this).keyup(function(e) {
                if (!e.ctrlKey && !(e.ctrlKey && e.keyCode == 32) && e.keyCode != 9 && e.keyCode != 13 && e.keyCode != 16 && e.keyCode != 17 && e.keyCode != 18 && e.keyCode != 38 && e.keyCode != 40) {
                    autocomplete_agenda($(this));
                }
            });
        }
        else {
            $(this).keyup(function(e) {
                if (!e.ctrlKey && !(e.ctrlKey && e.keyCode == 32) && e.keyCode != 9 && e.keyCode != 13 && e.keyCode != 16 && e.keyCode != 17 && e.keyCode != 18 && e.keyCode != 38 && e.keyCode != 40) {
                    autocomplete($(this));
                }
            });
        }

        $(this).keydown(function(e) {
            // 9 - TAB | 13 - ENTER | 38 = CIMA | 40 = BAIXO
            if (e.keyCode == 9 || e.keyCode == 13 || e.keyCode == 38 || e.keyCode == 40) {
                if (e.keyCode == 13) e.preventDefault();
                seta_autocomplete(e, $(this));
            }
        });
    });

    if ($(".mini-calendar").length) {
        alterar_calendario();
    }
    
    if ($(".selecao-pessoa").length) {
        $(".selecao-pessoa li").click(function() {
            $(this)
                .parent()
                .find(".selected")
                .removeClass("selected");
            $(this).addClass("selected");
            mostrar_agendamentos();
            mostrar_agendamentos_semanal();
        });
        if (!$(".selecao-pessoa li.selected").length) $(".selecao-pessoa li").first().addClass('selected');
    }

    if ($("#selecao-profissional").length) {
        $("#selecao-profissional > [data-id_profissional]").click(function() {
            $(this)
                .parent()
                .find(".selected")
                .removeClass("selected");
            $(this).addClass("selected");
            mostrar_agendamentos();
            mostrar_agendamentos_semanal();
        });
        if (!$("#selecao-profissional > [data-id_profissional].selected").length) $("#selecao-profissional > [data-id_profissional]").first().addClass('selected');
    }

    if ($(".agendamentos-dia").length) {
        $('#agenda-semanal').hide();
        $('#agenda-diaria').show();
        mostrar_agendamentos();
        mostrar_fila_espera();
    }

    

    if ($("#criarAgendamentoModal").length > 0) {
        $("#criarAgendamentoModal #paciente_id").change(function() {
            console.log($(this).val());
            if ($(this).val() != 0 && $(this).val() != '' && $(this).val() != undefined) {
                $.get("/saude-beta/pessoa/mostrar/" + $(this).val(), function(data) {
                    data = $.parseJSON(data);
                    $("#celular").val(data.celular1);
                    if (data.telefone1 != 'NULL' && data.telefone1 != null) $("#telefone").val(data.telefone1);
                    else                                                    $("#telefone").val('');
                    if (data.email != 'NULL' && data.email != null) $("#email").val(data.email);
                    else                                            $("#email").val('');

                    $('#criarAgendamentoModal #convenio_id').empty();
                    $('#criarAgendamentoModal #convenio_id').append("<option value='0'>Sem convênio...</option>")
                    data.convenio_pessoa.forEach(convenio => {
                        $('#criarAgendamentoModal #convenio_id').append(
                            '<option value="' + convenio.id_convenio + '">' +
                            convenio.descr_convenio +
                            '</option>'
                        );
                    });
                });
            } else {
                $.get('/saude-beta/convenio/listar',
                    function(data) {
                        data = $.parseJSON(data);
                        $('#criarAgendamentoModal #convenio-id').empty();
                        data.forEach(convenio => {
                            $('#criarAgendamentoModal #convenio-id').append(
                                '<option value="' + convenio.id + '">' +
                                convenio.descr +
                                '</option>'
                            );
                        });
                    }
                );


            }
        });

        
        $("#criarAgendamentoModal #id_contrato").change(function() {
            if ($("#criarAgendamentoModal #id_contrato").val() != 0){
                document.querySelector("#criarAgendamentoModal #planos_por_contrato").style.display = 'block'
                $("#planos_por_contrato").prop('required','true');
                encontrarPlanosContrato(() => {
                    console.log(false)
                })
            }
            else { 
                document.querySelector("#criarAgendamentoModal #planos_por_contrato").style.display = 'none'
                $("#planos_por_contrato").removeAttr('required');
                encontrarPlanosContrato(() => {
                    console.log(false)
                })
            }
        });

        paciente_id
        $("#paciente_id").change(function() {
            //$.get("/saude-beta/tipo-procedimento/mostrar/" + $(this).val(), function(data) {
            //    data = $.parseJSON(data);
            //    if (data.tempo_procedimento != 0) $("#tempo-procedimento").val(data.tempo_procedimento);
            //    else                              $("#tempo-procedimento").val('');
            //});
            //alert('passou');
        });

        $('#criarAgendamentoModal #data, #criarAgendamentoModal #hora').change(function() {
            if ($('#data').val() != '' && $('#hora').val() != '') {
                $.get(
                    '/saude-beta/agenda/verificar-grade',
                    {
                        id_profissional : $('#selecao-profissional > .selected').data().id_profissional,
                        data : $('#data').val(),
                        hora : $('#hora').val()
                    },
                    function(data) {
                        data = $.parseJSON(data);
                        if (data.grade_exist) {
                            bloquear_grade_agendamento = "";
                            $('#criarAgendamentoModalLabel > .invalid-feedback').html('');
                            $('#criarAgendamentoModalLabel > .invalid-feedback').hide('fast');
                            $('#criarAgendamentoModal #data, #criarAgendamentoModal #hora').removeClass('is-invalid');
                        } else {
                            bloquear_grade_agendamento = ' * Não há cadastro de uma grade válida para agendamentos às ' +
                            $('#hora').val() + ' em ' + get_dia_semana(data.dia_semana) + 's.';
                            $('#criarAgendamentoModalLabel > .invalid-feedback').html(bloquear_grade_agendamento);
                            $('#criarAgendamentoModalLabel > .invalid-feedback').show('fast');
                            $('#criarAgendamentoModal #data, #criarAgendamentoModal #hora').addClass('is-invalid');
                        }
                    }
                );
            }
        });
    }

    if ($("#criarAgendamentoAntigoModal").length) {
        $("#criarAgendamentoAntigoModal #paciente_id").change(function() {
            $("#criarAgendamentoAntigoModal #modalidade_id").empty().append('<option value="">Buscando atividades disponíveis...</option>')
            $.get('/saude-beta/agenda-antiga/listar-modalidades-disponiveis/' + $("#criarAgendamentoAntigoModal #paciente_id").val(),
            function(data, status){
                console.log(data + ' | ' + status)
                if (data.length > 0){
                    $("#criarAgendamentoAntigoModal #modalidade_id").empty().removeAttr('disabled', true)
                    data.forEach(modalidade => {
                        html = '<option value="'+modalidade.id + '">'+modalidade.descr+' (restam '+modalidade.atv_rest +' atvs.)' +'</option>'
                        $("#criarAgendamentoAntigoModal #modalidade_id").append(html)
                    })
                }
                else {
                    $("#criarAgendamentoAntigoModal #modalidade_id").prop('disabled', true)
                    $("#criarAgendamentoAntigoModal #modalidade_id").empty()
                    $("#criarAgendamentoAntigoModal #modalidade_id").append('<option>Paciente sem atividades disponíveis</option>')
                }
            })
        });
    }

    if ($("#menu-prontuario").length) {
        $("#menu-prontuario li[onclick]").click(function() {
            $("#menu-prontuario li").removeClass("selected");
            $("#prontuario > div[id]").removeClass("selected");
            $(this).addClass("selected");
            $($(this).data().id).addClass("selected");
        });
        $("#menu-prontuario li[onclick]").trigger('click');
        $("#menu-prontuario li[onclick]:first").trigger('click');
    }

    if ($("#filtro-grid").length) {
        $("#filtro-grid #btn-filtro").click(function() {
            filtrar_grid();
        });
        $("#filtro-grid input").keyup(function(e) {
            if(e.keyCode == 13)
            {
                filtrar_grid();
            }
        });
    }
    if ($("#filtro-grid-procedimento").length) {
        $("#filtro-especialidade").change(function() {
            filtrar_grid_procedimento();
        });
        $("#filtro-grid-procedimento #btn-filtro").click(function() {
            filtrar_grid_procedimento();
        });
        $("#filtro-grid-procedimento input").keyup(function(e) {
            if(e.keyCode == 13)
            {
                filtrar_grid_procedimento();
            }
        });
    }

    if ($("#filtro-grid-paciente").length) {
        $("#filtro-grid-paciente #btn-filtro").click(function() {
            filtrar_grid_paciente();
        });
        $("#filtro-grid-paciente input").keyup(function(e) {
            if(e.keyCode == 13)
            {
                filtrar_grid_paciente();
            }
        });
    }

    if ($("#filtro-grid-pedido").length) {
        $("#filtro-grid-pedido #btn-filtro").click(function() {
            filtrar_grid_pedido();
        });
        $("#filtro-grid-pedido input").keyup(function(e) {
            if(e.keyCode == 13){
                filtrar_grid_pedido();
            }
        });
    }

    if ($('#criar-agendamento-antigo-form').length) {
        $('#criar-agendamento-antigo-form').submit(function(event) {
            event.preventDefault();
            var hora = $(this).find('#hora').val(),
                prosseguir = true,
                perm_tempo_excedido = true;

            $(this).find('#id-profissional').val($('#selecao-profissional > .selected').data().id_profissional);
            $.get(
                '/saude-beta/agenda/verificar-grade',
                {
                    id_profissional : $(this).find('#id-profissional').val(),
                    paciente_id: $(this).find('#paciente_id').val(),
                    paciente_nome: $(this).find('#paciente_nome').val(),
                    data : $(this).find('#data').val(),
                    tempo_procedimento: $(this).find('#tempo-procedimento').val(),
                    hora : hora
                },
                function(data){
                    console.log(data);
                    data = $.parseJSON(data);

                    if (data.pessoa_exist == 'N') {
                        prosseguir = confirm('Esse paciente não existe, deseja criar automaticamente?');
                    } else if (data.pessoa_exist == 'E') {
                        alert('Selecionar um paciente válido!')
                        prosseguir = false;
                    }
                    if (prosseguir) {
                        if (data.grade_exist) {
                            if (data.horario_disponivel) {
                                if (data.tempo_excedido) perm_tempo_excedido = confirm('O tempo do procedimento é maior que o tempo disponível na grade.\nAgendar mesmo assim?');
                                if (perm_tempo_excedido) {
                                    $('#criarAgendamentoAntigoModal #id-grade-horario').val(data.id_grade_horario);
                                    setTimeout(() => {
                                        salvar_agendamento_antigo()
                                    }, 500)
                                }
                            } else {
                                alert('Aviso!\nNão foi possível agendar, pois não há horário disponível para o tempo de procedimento e o período informado.');
                            }
                        } else {
                            alert(
                                'Não há cadastro de uma grade válida para agendamentos às ' +
                                hora + ' em ' + get_dia_semana(data.dia_semana) + 's.'
                            );
                        }
                    }
                }
            );
        });
    }

    // if($('.filtro-valor').length > 0){
    //     $('.filtro-valor').mask('#.##0,00', {reverse: true});
    // }

    if ($('#criar-agendamento-form').length) {
        $('#criar-agendamento-form').submit(function(event) {
            event.preventDefault();
            var hora = $(this).find('#hora').val(),
                prosseguir = true,
                perm_tempo_excedido = true,
                profissional =  $(this).find('#id-profissional').val(),
                id_paciente =  $(this).find('#paciente_id').val(),
                nome_paciente = $(this).find('#paciente_nome').val(),
                tempo_procedimento = 0,
                date = $(this).find('#data').val()
            
                

        
            $.get(
                '/saude-beta/agenda/verificar-grade',
                {
                    id_profissional : profissional,
                    paciente_id: id_paciente,
                    paciente_nome: nome_paciente,
                    data : date,
                    hora : hora
                },
                function(data){
                    console.log(data);
                    data = $.parseJSON(data);
                    if (prosseguir) {
                        // if (data.grade_exist) {
                            // if (data.horario_disponivel) {
                            $('#id-grade-horario').val(data.id_grade_horario);
                            validar_atv_semana();
                        //     } 
                        //     else {
                        //         alert('Aviso!\nNão foi possível agendar, pois não há horário disponível para o tempo de procedimento e o período informado.');
                        //     }
                        // } 
                        // else {
                        //     alert(
                        //         'Não há cadastro de uma grade válida para agendamentos às ' +
                        //         hora + ' em ' + get_dia_semana(data.dia_semana) + 's.'
                        //     );
                        // }
                    }
                }
            );
        });
    }

    if($('#menu-prontuario > li')){
        $('#menu-prontuario > li').click(function() {
            if (detectar_mobile()) recolher_menu()
        })
    }


    $(document).mouseup(function(e) {
        var container = $('.form-fila-espera.open');
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            container.removeClass('open');
        }
    });

    $('.modal').on("hidden.bs.modal", function () {
        $(this).find('input:not([name="_token"])').val('');
        $(this).find('textarea').val('');
    });

    $('#criarAgendamentoModal [name="convenio"]').change(function(e) {
        console.log(e.target.id);
        if (e.target.id.includes('convenio')) $('#convenio-descr').show();
        else                                  $('#convenio-descr').hide();
    });

    $('#semanal-diaria').change(function(e) {
        if (e.target.checked) {
            $('[for="semanal-diaria"]').html('Semanal');
            $('#agenda-diaria').hide();
            $('#agenda-semanal').show();
            mostrar_agendamentos_semanal();
        } else {
            $('[for="semanal-diaria"]').html('Diária');
            $('#agenda-semanal').hide();
            $('#agenda-diaria').show();
            mostrar_agendamentos();
        }
    });

    $('#resumo-filtro-profissional').keyup(function(e) {
        if(e.keyCode == 13)
        {
            resumir_filtro();
        }
    });

    $('#resumo-filtro input').change(function() {
        resumir_filtro();
    });

    $('#semana-filtro input').change(function() {
        var toShow = $('#semana-filtro input:checked'),
            toHide = $('#semana-filtro input:not(:checked)');

        toShow.each(function() { $('#agenda-semanal .agendamentos-dia[data-dia_semana="' + $(this).data().filtro + '"]').show(); });
        toHide.each(function() { $('#agenda-semanal .agendamentos-dia[data-dia_semana="' + $(this).data().filtro + '"]').hide(); });
    });

    $('#filtro-semana.dropdown-menu, .agenda-diaria-body .dropdown-menu').click(function(e) {
        e.stopPropagation();
    });


    $('#btn-esconder-duracao').click(function() {
        $('#duracao-consulta').toggle();
        if ($('#duracao-consulta').is(":hidden")) {
            $('#btn-esconder-duracao').html(
                '<i class="my-icon far fa-clock mr-2 my-auto"></i>' +
                '<span class="my-auto">Exibir Duração</span>'
            );
        } else {
            $('#btn-esconder-duracao').html(
                '<i class="my-icon far fa-clock mr-2 my-auto"></i>' +
                '<span class="my-auto">Ocultar Duração</span>'
            );
        }
    });

    $('#iniciar_atendimento').click(function() {
        if ($(this).data().em_atendimento == 'N') {
            $(this).removeClass('btn-success');
            $(this).addClass('btn-danger');
            $(this).html('<i class="my-icon fas fa-stop mr-2"></i> __:__:__');
            $(this).data('em_atendimento', 'S');
            if ($('#cbx-video-chamada').prop('checked')) link_video_chamada($(this).data().id_paciente);
            else                                     comecar_atendimento($(this).data().id_paciente);
        } else {
            $(this).removeClass('btn-danger');
            $(this).addClass('btn-success');
            // $(this).html('<i class="my-icon fas fa-play mr-2"></i> Iniciar Atendimento');
            if ($(this).html().includes('__:__:__')) $(this).html('<i class="my-icon fas fa-play mr-2"></i> Iniciar Atendimento');
            else                                     $(this).find('.my-icon').removeClass('fa-stop').addClass('fa-play');
            $(this).data('em_atendimento', 'N');
            parar_atendimento($(this).data().id_paciente)
        }
    });

    if ($("#duracao-consulta").length) {
        $.get('/saude-beta/atendimento/paciente-em-aberto/' + $("#duracao-consulta").data().id_paciente,
            function(data) {
                if (data != null) {
                    data = $.parseJSON(data);

                    hour = moment().diff(moment(data.data_inicio + ' ' + data.hora_inicio), 'hours');
                    min = moment().diff(moment(data.data_inicio + ' ' + data.hora_inicio), 'minutes');
                    sec = moment().diff(moment(data.data_inicio + ' ' + data.hora_inicio), 'seconds');

                    $('#iniciar_atendimento').attr('data-em_atendimento', 'S');
                    $('#iniciar_atendimento').removeClass('btn-success');
                    $('#iniciar_atendimento').addClass('btn-danger');
                    $('#iniciar_atendimento').html('<i class="my-icon fas fa-stop mr-2"></i> Encerrar Atendimento');
                    $("#duracao-consulta").html(
                        ("0" + hour).slice(-2) + ":" + ("0" + min).slice(-2) + ":" + ("0" + sec).slice(-2)
                    );

                    timer = setInterval(function() {
                        sec++;
                        if (sec == 60) {
                            sec = 0;
                            min++;
                            if (min == 60) {
                                min = 0;
                                hour++;
                            }
                        }
                        $("#duracao-consulta").html(
                            ("0" + hour).slice(-2) + ":" + ("0" + min).slice(-2) + ":" + ("0" + sec).slice(-2)
                        );
                    }, 1000);
                }
            }
        );
    }

    if ($('#mudar-visualizacao').length) {
        $('#mudar-visualizacao [data-visualizacao]').click(function() {
            $('#agenda-visualizacao').find('.active').removeClass('active');
            $('#mudar-visualizacao').find('.active').removeClass('active');

            $(this).addClass('active');
            $('#agenda-visualizacao').find($(this).data().visualizacao).addClass('active');
        });
        mostrar_agendamentos();
        mostrar_agendamentos_semanal(true);
    }

    if ($('#mudar-data-visualizacao').length) {
        $('#mudar-data-visualizacao [data-function]').click(function() {
            var dias_soma = 1
            if ($('#agenda-visualizacao > .active').attr('id') == 'agenda-semanal') dias_soma = 7;

            if ($(this).data().function == '+')          alterar_calendario(dias_soma, 'D');
            else if ($(this).data().function == 'today') alterar_calendario();
            else                                         alterar_calendario(-dias_soma, 'D');

            mostrar_agendamentos();
            mostrar_agendamentos_semanal();
        });
    }

    if ($('#buscar-agendamento').length) {
        $('#buscar-agendamento').keyup(function(e) {
            if(e.keyCode == 13)
            {
                pesquisar_agendamento();
            }
        });
    }


    if ($('#precosModal').length) {
        $('#procedimento_nome, #valor').keyup(function(e) {
            if (e.keyCode == 13) add_preco();
        });
    }

    if ($('#convenioModal').length) {
        $('#quem-paga').change(function() {
            $('#cliente_nome').val('');
            $('#cliente_id').val('');
            console.log($(this).prop('checked'));
            if ($(this).prop('checked')) {
                $('#cliente_nome').hide('fast');
                $('#cliente_nome').removeAttr("required");
                $(this).parent().parent().removeClass('form-search');
            } else {
                $('#cliente_nome').show('fast');
                $('#cliente_nome').prop("required", true);
                $(this).parent().parent().addClass('form-search');
            }
        });
    }

    if ($('#pessoaModal').length) {
        $('#resp_localizacao').change(function() {
            console.log($(this).prop('checked'));
            if ($(this).prop('checked')) {
                $('#responsavel-localização').hide();
            } else {
                $('#responsavel-localização').show();
            }
        });

        $.get('/saude-beta/convenio/listar',
            function(data) {
                data = $.parseJSON(data);
                $('#pessoaModal #convenio').html('<option value="">Selecionar Convênio...</option>');
                data.forEach(convenio => {
                    $('#pessoaModal #convenio').append('<option value="' + convenio.id + '">' + convenio.descr + '</option>');
                });
            }
        );

        $('#pessoaModal').on('shown.bs.modal', function() {
            $.get('/saude-beta/pessoa/max-cod-interno', function(data) {
                if ($('#pessoaModal #cod_interno').val() == '') $('#pessoaModal #cod_interno').val(data);
            });
        });
    }

    if ($('#orcamentoModal').length) {
        $('#orcamentoModal #paciente_id').change(function() {
            var _paciente_id = $(this).val(), html;
            if (_paciente_id != '') {
                $.get(
                    '/saude-beta/pessoa/mostrar/' + _paciente_id,
                    function(data) {
                        data = $.parseJSON(data);
                        html = '<option value="">Selecionar Convênio...</option>';
                        $('#orcamentoModal #id_convenio').html(html);

                        data.convenio_pessoa.forEach(convenio => {
                            html  = '<option data-n_carteira="' + convenio.num_convenio + '" value="' + convenio.id_convenio + '">';
                            html += convenio.descr_convenio;
                            html += '</option>';
                            $('#orcamentoModal #id_convenio').append(html);
                        });
                        $('#orcamentoModal #id_convenio').unbind('change');
                        $('#orcamentoModal #id_convenio').change(function() {
                            console.log($(this).val());
                            $('#orcamentoModal #procedimento_descr')
                            .data('filter', $(this).val())
                            .attr('data-filter', $(this).val());
                        });
                    }
                );
            }
        });

        $('#orcamentoModal #id_convenio').change(function() {
            $('#orcamentoModal #procedimento_id')
            .data('filter', $(this).val())
            .attr('data-filter', $(this).val());
        });

        $('#orcamentoModal #procedimento_id').change(function() {
            if ($(this).val() != '') {
                $.get(
                    '/saude-beta/procedimento/verificar-convenio',
                    {
                        id_procedimento: $(this).val(),
                        id_convenio: $('#orcamentoModal #id_convenio').val()
                    },
                    function(data) {
                        data = $.parseJSON(data);
                        if (data != null) {
                            $('#valor').val(data.valor.toString().replace('.', ','));
                            $('#valor').data('valor_minimo', data.valor_minimo);
                            $('#valor').attr('data-valor_minimo', data.valor_minimo);

                            $('#valor_prazo').val(data.valor_prazo.toString().replace('.', ','));
                            $('#valor_prazo').data('valor_minimo', data.valor_minimo);
                            $('#valor_prazo').attr('data-valor_minimo', data.valor_minimo);

                            if (data.dente_regiao == '1') $('#dente_regiao').parent().show();
                            else                          $('#dente_regiao').parent().hide();

                            if (data.face == '1') $('#dente_face').parent().show();
                            else                  $('#dente_face').parent().hide();

                            $('#dente_regiao').val('');
                            $('#dente_face').val('');
                        } else {
                            $('#valor').val('');
                            $('#valor').data('valor_minimo', '');
                            $('#valor').removeAttr('data-valor_minimo');

                            $('#valor_prazo').val('');
                            $('#valor_prazo').data('valor_minimo', '');
                            $('#valor_prazo').removeAttr('data-valor_minimo');
                            $('#dente_regiao').parent().show();
                            $('#dente_face').parent().show();
                            $('#dente_regiao').val('');
                            $('#dente_face').val('');
                        }
                        if ($('#quantidade').val() == '' || $('#quantidade').val() == 0) {
                            $('#quantidade').val(1);
                        }
                    }
                );
            }
        });

        $('#orcamentoModal #forma_pag_tipo').change(function() {
            $.get('/saude-beta/forma-pag/listar/' + $(this).val(), function(data) {
                data = $.parseJSON(data);
                var html = '';
                data.forEach(forma_pag => {
                    if (forma_pag.id != 102) {
                        html += '<option value="' + forma_pag.id + '">';
                        html += forma_pag.descr;
                        html += '</option>';    
                    }
                });
                $('#forma_pag').html(html).trigger('change');
            });
            if ($(this).val() == 'V') {
                $('#forma_pag_parcela').val(1);
                $('#forma_pag_parcela').parent().hide();
                $('#forma_pag_vista').parent().hide();
                $('#forma_pag_valor_ent').val(0);
                $('#forma_pag_valor_ent').parent().hide();
                $('#forma_pag_valor').val($(this).data().preco_vista);
            } else if ($(this).val() == 'E') {
                $.get('/saude-beta/forma-pag/listar/V', function(data) {
                    data = $.parseJSON(data);
                    var html = '';
                    data.forEach(forma_pag => {
                        if (forma_pag.id != 102) {
                            html += '<option value="' + forma_pag.id + '">';
                            html += forma_pag.descr;
                            html += '</option>';    
                        }
                    });
                    $('#forma_pag_parcela').parent().show();
                    $('#forma_pag_parcela').val(1);
                    $('#forma_pag_vista').html(html).trigger('change');
                    $('#forma_pag_vista').parent().show();
                    $('#forma_pag_valor_ent').val(0);
                    $('#forma_pag_valor_ent').parent().show();
                });
                $('#forma_pag_valor').val($(this).data().preco_prazo);
            } else {
                $('#forma_pag_parcela').parent().show();
                $('#forma_pag_parcela').val(1);
                $('#forma_pag_vista').parent().hide();
                $('#forma_pag_valor_ent').val(0);
                $('#forma_pag_valor_ent').parent().hide();
                $('#forma_pag_valor').val($(this).data().preco_prazo);
            }
        });

        $('#orcamentoModal #forma_pag').change(function() {
            $.get('/saude-beta/forma-pag/mostrar/' + $(this).val(), function(data) {
                data = $.parseJSON(data);

                if (data.financeiras.length > 0) {
                    $('#financeira_id').parent().show();
                    var html = '';
                    data.financeiras.forEach(financeira => {
                        html += '<option value="' + financeira.id + '">';
                        html += financeira.descr;
                        html += '</option>';
                    });
                    $('#financeira_id').html(html);
                } else {
                    $('#financeira_id').html('<option value="0">Selecionar Financeira...</option>');
                    $('#financeira_id').parent().hide();
                }
            });
        });
    }

    if ($('#orcamentoConversaoModal').length) {
        $('#conv_forma_pag').change(function() {
            $.get('/saude-beta/forma-pag/mostrar/' + $(this).val(), function(data) {
                data = $.parseJSON(data);

                if (data.financeiras.length > 0) {
                    $('#conv_financeira_id').parent().show();
                    var html = '';
                    data.financeiras.forEach(financeira => {
                        html += '<option value="' + financeira.id + '">';
                        html += financeira.descr;
                        html += '</option>';
                    });
                    $('#conv_financeira_id').html(html);
                } else {
                    $('#conv_financeira_id').html('<option value="0">Selecionar Financeira...</option>');
                    $('#conv_financeira_id').parent().hide();
                }
            });
        });

        $('#conv_forma_pag_valor').focusin(function() {
            if ($(this).val() == '') {
                var vPendente = $('#table-conv-orcamento-forma-pag [data-total_pag_pendente]').data().total_pag_pendente -
                                $('#table-conv-orcamento-forma-pag [data-total_pag_valor]').data().total_pag_valor;
                if (vPendente < 0) vPendente = 0;
                $(this).val(parseFloat(vPendente).toFixed(2).toString().replace('.', ','));
            }
        });
    }


    
    if ($('#pedidoAntigoModal').length) {
        $('#pedidoAntigoModal #pedido_forma_pag_valor').focusin(function() {
            if ($(this).val() == '') {
                var vPendente = $('#pedidoAntigoModal #table-pedido-forma-pag [data-total_pag_pendente]').data().total_pag_pendente -
                                $('#pedidoAntigoModal #table-pedido-forma-pag [data-total_pag_valor]').data().total_pag_valor;
                if (vPendente < 0) vPendente = 0;
                $(this).val(parseFloat(vPendente).toFixed(2).toString().replace('.', '.'));
            }
        })

        $('#pedidoAntigoModal #pedido_paciente_id').on("change", function (e, id_convenio) {
            var _paciente_id = $(this).val(), html;
            if (_paciente_id != '') {
                $.get(
                    '/saude-beta/pessoa/mostrar/' + _paciente_id,
                    function(data) {
                        data = $.parseJSON(data);
                        html = '<option value="0">Selecionar Convênio...</option>';
                        $('#pedidoAntigoModal #pedido_id_convenio').html(html);
                        data.convenio_pessoa.forEach(convenio => {
                            html  = '<option data-n_carteira="' + convenio.num_convenio + '" value="' + convenio.id_convenio + '">';
                            html += convenio.descr_convenio;
                            html += '</option>';
                            $('#pedidoAntigoModal #pedido_id_convenio').append(html);
                        });
                        $('#pedidoAntigoModal #pedido_id_convenio').unbind('change');
                        $('#pedidoAntigoModal #pedido_id_convenio').change(function() {
                            console.log($(this).val());
                            $('#pedidoAntigoModal #procedimento_descr')
                            .data('filter', $(this).val())
                            .attr('data-filter', $(this).val());
                        });
                        $('#pedidoAntigoModal #pedido_id_convenio')
                        .val(0)
                        .trigger('change');
                    }
                );
            }
        });

        $('#pedidoAntigoModal #pedido_id_convenio').change(function() {
            $('#pedidoAntigoModal #procedimento_id')
            .data('filter', $(this).val())
            .attr('data-filter', $(this).val());
        });

        $('#pedidoAntigoModal #procedimento_id').change(function() {
            if ($(this).val() != '') {
                $.get(
                    '/saude-beta/procedimento/verificar-convenio',
                    {
                        id_procedimento: $(this).val(),
                        id_convenio: $('#pedidoAntigoModal #pedido_id_convenio').val()
                    },
                    function(data) {
                        data = $.parseJSON(data);
                        if (data != null) {
                            $('#pedidoAntigoModal #valor').val(data.valor.toString().replace('.', ','));
                            $('#pedidoAntigoModal #valor').data('valor_minimo', data.valor_minimo);
                            $('#pedidoAntigoModal #valor').attr('data-valor_minimo', data.valor_minimo);

                            $('#pedidoAntigoModal #valor_prazo').val(data.valor_prazo.toString().replace('.', ','));
                            $('#pedidoAntigoModal #valor_prazo').data('valor_minimo', data.valor_minimo);
                            $('#pedidoAntigoModal #valor_prazo').attr('data-valor_minimo', data.valor_minimo);

                            if (data.dente_regiao == '1') $('#pedidoAntigoModal #dente_regiao').parent().show();
                            else                          $('#pedidoAntigoModal #dente_regiao').parent().hide();

                            if (data.face == '1') $('#pedidoAntigoModal #dente_face').parent().show();
                            else                  $('#pedidoAntigoModal #dente_face').parent().hide();

                            $('#pedidoAntigoModal #dente_regiao').val('');
                            $('#pedidoAntigoModal #dente_face').val('');
                        } else {
                            $('#pedidoAntigoModal #valor').val('');
                            $('#pedidoAntigoModal #valor').data('valor_minimo', '');
                            $('#pedidoAntigoModal #valor').removeAttr('data-valor_minimo');

                            $('#pedidoAntigoModal #valor_prazo').val('');
                            $('#pedidoAntigoModal #valor_prazo').data('valor_minimo', '');
                            $('#pedidoAntigoModal #valor_prazo').removeAttr('data-valor_minimo');
                            $('#pedidoAntigoModal #dente_regiao').parent().show();
                            $('#pedidoAntigoModal #dente_face').parent().show();
                            $('#pedidoAntigoModal #dente_regiao').val('');
                            $('#pedidoAntigoModal #dente_face').val('');
                        }
                        if ($('#pedidoAntigoModal #quantidade').val() == '' || $('#pedidoAntigoModal #quantidade').val() == 0) {
                            $('#pedidoAntigoModal #quantidade').val(1);
                        }
                    }
                );
            }
        });
    }

    if ($('#pedidoModal').length) {
        $('#pedidoModal #pedido_forma_pag_valor').focusin(function() {
            if ($(this).val() == '') {
                var vPendente = $('#table-pedido-forma-pag [data-total_pag_pendente]').data().total_pag_pendente -
                                $('#table-pedido-forma-pag [data-total_pag_valor]').data().total_pag_valor;
                if (vPendente < 0) vPendente = 0;
                $(this).val(parseFloat(vPendente).toFixed(2).toString().replace('.', '.'));
            }
        })

        $('#pedidoModal #pedido_paciente_id').on("change", function (e, id_convenio) {
            var _paciente_id = $(this).val(), html;
            if (_paciente_id != '') {
                $.get(
                    '/saude-beta/pessoa/mostrar/' + _paciente_id,
                    function(data) {
                        data = $.parseJSON(data);
                        html = '<option value="0">Selecionar Convênio...</option>';
                        $('#pedidoModal #pedido_id_convenio').html(html);
                        data.convenio_pessoa.forEach(convenio => {
                            html  = '<option data-n_carteira="' + convenio.num_convenio + '" value="' + convenio.id_convenio + '">';
                            html += convenio.descr_convenio;
                            html += '</option>';
                            $('#pedidoModal #pedido_id_convenio').append(html);
                        });
                        $('#pedidoModal #pedido_id_convenio').unbind('change');
                        $('#pedidoModal #pedido_id_convenio').change(function() {
                            console.log($(this).val());
                            $('#pedidoModal #procedimento_descr')
                            .data('filter', $(this).val())
                            .attr('data-filter', $(this).val());
                        });
                        $('#pedidoModal #pedido_id_convenio')
                        .val(0)
                        .trigger('change');
                    }
                );
            }
        });

        $('#pedidoModal #pedido_id_convenio').change(function() {
            $('#pedidoModal #procedimento_id')
            .data('filter', $(this).val())
            .attr('data-filter', $(this).val());
        });

        $('#pedidoModal #procedimento_id').change(function() {
            if ($(this).val() != '') {
                $.get(
                    '/saude-beta/procedimento/verificar-convenio',
                    {
                        id_procedimento: $(this).val(),
                        id_convenio: $('#pedidoModal #pedido_id_convenio').val()
                    },
                    function(data) {
                        data = $.parseJSON(data);
                        if (data != null) {
                            $('#pedidoModal #valor').val(data.valor.toString().replace('.', ','));
                            $('#pedidoModal #valor').data('valor_minimo', data.valor_minimo);
                            $('#pedidoModal #valor').attr('data-valor_minimo', data.valor_minimo);

                            $('#pedidoModal #valor_prazo').val(data.valor_prazo.toString().replace('.', ','));
                            $('#pedidoModal #valor_prazo').data('valor_minimo', data.valor_minimo);
                            $('#pedidoModal #valor_prazo').attr('data-valor_minimo', data.valor_minimo);

                            if (data.dente_regiao == '1') $('#pedidoModal #dente_regiao').parent().show();
                            else                          $('#pedidoModal #dente_regiao').parent().hide();

                            if (data.face == '1') $('#pedidoModal #dente_face').parent().show();
                            else                  $('#pedidoModal #dente_face').parent().hide();

                            $('#pedidoModal #dente_regiao').val('');
                            $('#pedidoModal #dente_face').val('');
                        } else {
                            $('#pedidoModal #valor').val('');
                            $('#pedidoModal #valor').data('valor_minimo', '');
                            $('#pedidoModal #valor').removeAttr('data-valor_minimo');

                            $('#pedidoModal #valor_prazo').val('');
                            $('#pedidoModal #valor_prazo').data('valor_minimo', '');
                            $('#pedidoModal #valor_prazo').removeAttr('data-valor_minimo');
                            $('#pedidoModal #dente_regiao').parent().show();
                            $('#pedidoModal #dente_face').parent().show();
                            $('#pedidoModal #dente_regiao').val('');
                            $('#pedidoModal #dente_face').val('');
                        }
                        if ($('#pedidoModal #quantidade').val() == '' || $('#pedidoModal #quantidade').val() == 0) {
                            $('#pedidoModal #quantidade').val(1);
                        }
                    }
                );
            }
        });
    }

    $('body').on('hidden.bs.modal', function () {
        if($('.modal.show').length > 0)
        {
            $('body').addClass('modal-open');
        }
    });

    $('#btn-buscar-agendamento').on('click', function() {

        $('#pesquisa-agendamentos .dropdown-menu').on('click', function (e) {
            e.stopPropagation();
        });

        $('#agendaPesquisaModal').modal('show');
        $('#pesquisa-agendamentos').html('');
    });

    $("#fila-espera.hide").fadeTo('fast', 0);
    $('#btn-switch-fila-espera').click(function() {
        _fila_espera = $('#fila-espera');
        if (_fila_espera.hasClass('hide')) {
           $('#fila-espera').removeClass('hide');
           sleep(300).then(() => {
               $("#fila-espera").fadeTo('fast', 1);
            });
        } else {
           $("#fila-espera").fadeTo('fast', 0, function() {
               sleep(300).then(() => {
                   $('#fila-espera').addClass('hide');
                });
           });
        }
    });

    $('.crud-section').click(function() {
        if ($($(this).data().id_hide).is(':visible')) {
            $($(this).data().id_hide).hide('fast');
            $(this).find('.indicator').html('<i class="my-icon fas fa-plus"></i>');
        } else {
            $($(this).data().id_hide).show('fast');
            $(this).find('.indicator').html('<i class="my-icon fas fa-minus"></i>');
        }
    })

    $('.crud-section').each(function() {
        $($(this).data().id_hide).hide();
    });

    $('#criarEvolucaoModal, #evolucaoPedidoModal').on('shown.bs.modal', function() {
        var evolucao_modal = $(this);
        $.get('/saude-beta/evolucao-tipo/listar',
            function(data) {
                data = $.parseJSON(data);
                evolucao_modal.find('#id_evolucao_tipo').empty();
                data.forEach(evolucao_tipo => {
                    evolucao_modal.find('#id_evolucao_tipo').append(
                        '<option value="' + evolucao_tipo.id + '">' +
                        evolucao_tipo.descr +
                        '</option>'
                    );
                });
            }
        );

        $.get('/saude-beta/pessoa/listar-corpo',
            function(data) {
                data = $.parseJSON(data);
                evolucao_modal.find('#id_parte_corpo').empty();
                data.forEach(parte_corpo => {
                    evolucao_modal.find('#id_parte_corpo').append(
                        '<option value="' + parte_corpo.id + '">' +
                        parte_corpo.descr +
                        '</option>'
                    );
                });
            }
        );
    });

    $('#criarDocumentoModal').on('shown.bs.modal', function() {
        $.get('/saude-beta/documento-modelo/listar',
            function(data) {
                data = $.parseJSON(data);
                $('#id_doc_modelo').html('<option value="" checked>Selecionar Modelo de Documento...</option>');
                data.forEach(modelo => {
                    $('#id_doc_modelo').append('<option value="' + modelo.id + '">' + modelo.titulo + '</option>');
                });
            }
        );
    });

    $('#criarDocumentoModal #id_doc_modelo').change(function() {
        $.get('/saude-beta/documento-modelo/mostrar/' + $(this).val(),
            function(data) {
                data = $.parseJSON(data);
                $('#corpo').summernote('code', data.corpo);
            }
        );
    });

    $('#selecaoAnamneseModal').on('shown.bs.modal', function() {
        $.get('/saude-beta/anamnese/listar',
            function(data) {
                data = $.parseJSON(data);
                $('#selecao-anamnese').empty();
                data.forEach(anamnese => {
                    $('#selecao-anamnese').append(
                        '<h6 class="m-0" onclick="mostrar_questionario_anamnese(' + anamnese.id + ')">' +
                        anamnese.descr +
                        '</h6>'
                    );
                });
            }
        );
    });
    $('#selecaoIECModal').on('shown.bs.modal', function() {
        $.get('/saude-beta/IEC/listar',
            function(data) {
                data = $.parseJSON(data);
                $('#selecao-iec').empty();
                data.forEach(anamnese => {
                    $('#selecao-iec').append(
                        '<h6 class="m-0" onclick="mostrar_questionario_iec(' + anamnese.id + ')">' +
                        anamnese.descr +
                        '</h6>'
                    );
                });
            }
        );
    });
    

    if ($('#criarDocModeloModal').length) {
        var mod  = 'VENHO POR MEIO DESTE DECLARAR AOS DEVIDOS FINS, QUE O(A) SR(A) PACIENTE FORA ';
        mod += 'SUBMETIDO(A) A ATENDIMENTO MÉDICO AMBULATORIAL DE CARDIOLOGIA. O(A) QUAL DEVERÁ FICAR ';
        mod += 'AFASTADO(A). DE SUAS procedimentoS LABORAIS PELO PERÍODO DA MANHÃ.';
        mod += '<br><br><br>';
        mod += 'CID: ';
        mod += '<br>';
        mod += 'EU, __________________________________________________________<br>';
        mod += 'Autorizo, que seja inserido no atestado o CID.';

        $('#criarDocModeloModal #corpo').summernote("code", mod);
        $('#criarDocModeloModal').on('hidden', function() {
            $('#criarDocModeloModal #corpo').summernote("code", mod);
        });
    }

    if ($('.inputs-anamnese').length) {
        $('.inputs-anamnese > img').click(function() {
            add_input_anamnese($(this).data().input);
        })
    }
    
    $('#video-chamada').draggable({
        start: function(event, ui){
            $(this).data('dragging', true);
        },
        stop: function(event, ui){
            setTimeout(function(){
                $(event.target).data('dragging', false);
            }, 1);
        }
    });

    $('#toggle-video-chamada').click(function() {
        if($('#video-chamada').data('dragging')) return;
        if ($('#video-link').hasClass('show'))  {
            $('#toggle-video-chamada').html(
                '<i class="my-icon fal fa-video"></i>'
            );
            $('#video-link').removeClass('show');
        } else {
            $('#toggle-video-chamada').html(
                '<i class="my-icon fal fa-times"></i>'
            );
            $('#video-link').addClass('show');
        }
    });

    $('#resumo-filtro-pai').on('hide.bs.dropdown', function (e) {
        if (e.clickEvent) {
            e.preventDefault();
        }
    });

    $('.sortable-columns > th')
    .each(function(){
        var th = $(this),
            thIndex = th.index(),
            inverse = false,
            table = $($(this).parent().attr('for'));

        th.click(function() {
            $(this).parent().find('.text-dark').removeClass('text-dark');
            $(this).parent().find('.my-icon').remove();
            $(this).addClass('text-dark')
            if (inverse) $(this).append('<i class="my-icon ml-2 fad fa-sort-up"></i>');
            else         $(this).append('<i class="my-icon ml-2 fad fa-sort-down"></i>');

            table.find('td')
            .filter(function() {
                return $(this).index() === thIndex;
            })
            .sortElements(function(a, b) {
                return $.text([a]) > $.text([b]) ? inverse ? -1 : 1 : inverse ? 1 : -1;
            }, function() {
                return this.parentNode;
            });
            inverse = !inverse;
        });
    });

    $(document).mouseup(function(e)
    {
        var container = $('#resumo-filtro-pai');

        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0)
        {
            container.find('#resumo-filtro').removeClass('show');
        }
    });

    $('#table-prontuario-vitruviano').each(function() {
        $.get('/saude-beta/pessoa/listar-corpo', 
            function(data) {
                data = $.parseJSON(data);
                data.forEach(parte_corpo => {
                    $.get('/saude-beta/pessoa/status-evolucao/' + $('#id_pessoa_prontuario').val() + '/' + parte_corpo.id,
                        function(data) {
                            data = $.parseJSON(data);
                            if(data.length > 0) {
                                data.forEach(data => {
                                    $('#' + data.obj).addClass('regiao-vitruviano2');
                                });
                            }
                        }
                    );
                })
            }
        );
    });

    if (detectar_mobile()) {
        $('#agenda-context-menu > [data-function="novo_agendamento').hide()
        $('#agenda-context-menu > [data-function="novo_agendamento_antigo"]').hide()
    }


    if ($('.sort-down-caixa').length > 0){
        $('.sort-down-caixa').click(function() {
            ul = $($(this).parent().parent().parent().parent().find('.detalhes-foma-pag-caixa'))
            card = $($(this).parent().parent().parent().parent())
            console.log(ul)
            console.log(card)
            $(this).css('transition', 'rotate 0.3s')
            $(this).css('cursor', 'pointer')

            if (parseInt(ul.css('max-height').replaceAll('px', '')) == 0) {
                ul.css('max-height', '100%')
                card.css('max-height', '100%')
                $(this).css('transform', 'rotate(180deg)')
            }
            else {
                ul.css('max-height', '0px')
                card.css('max-height', '65px')
                $(this).css('transform', 'rotate(0deg)')
            }
        })
    }
    
    customCustomBar();

    $(window).resize(function() {
        customCustomBar();
    })
});

function customCustomBar() {
    if ($($(".custom-scrollbar")[0]).prop("scrollHeight") > $($(".custom-scrollbar")[0]).prop("clientHeight")) {
        $($(".custom-scrollbar")[0]).mouseenter(function() {
            $(this).css("margin-right", "-3px");
        });
        $($(".custom-scrollbar")[0]).mouseleave(function() {
            $(this).css("margin-right", "");
        });
    } else {
        $($(".custom-scrollbar")[0]).unbind("mouseenter");
        $($(".custom-scrollbar")[0]).unbind("mouseleave");
    }
}
