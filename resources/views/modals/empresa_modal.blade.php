<!-- Modal -->
<div class="modal fade" id="empresaModal" aria-labelledby="empresaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id = "empresaModal_form">
                @csrf
                <div class="modal-header">
                    <h6 class="modal-title header-color" id="empresaModalLabel"></h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input id="id_empresa" name = "id_empresa" type="hidden">
                    <input id="id_enc" name = "id_enc" type="hidden">
                    <div class="container">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label for="descr" class="custom-label-form">Descrição *</label>
                                <input id="descr" name="descr" class="form-control" autocomplete="off" type="text" required>
                            </div>
                            
                            <div class="col-md-10 form-group">
                                <label for="endereco" class="custom-label-form">Endereço *</label>
                                <input id="endereco" name="endereco" class="form-control" autocomplete="off" type="text" required>
                            </div>
                            <div class="col-md-2 form-group">
                                <label for="telefone" class="custom-label-form">Telefone *</label>
                                <input id="telefone" name="telefone" class="form-control" autocomplete="off" type="tel" onkeyup = "handlePhone(event)" onkeychange = "limitaTel()" onkeydown = "limitaTel()" required>
                            </div>

                            <div class="col-md-12 form-group">
                                <label for="responsavel" class="custom-label-form">Responsável *</label>
                                <input id="responsavel"
                                       name="responsavel"
                                       class="form-control autocomplete"
                                       data-input="#id_responsavel"
                                       data-table="pessoa"
                                       data-column="nome_fantasia"
                                       data-filter_col="colaborador"
                                       data-filter="A"
                                       type="text"
                                       autocomplete="off"
                                       required>
                                <input id="id_responsavel" name="id_responsavel" type="hidden">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row my-3">
                        <button class="btn btn-target m-auto px-5" type="button" id = "empresaModalBotao" onclick="salvar_empresa()">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style type = "text/css">
    .col-md-2{flex:0 0 18%;max-width:18%}
    .col-md-10{flex:0 0 82%;max-width:82%}
    .my-icon{cursor:pointer}
</style>

<script type = "text/javascript" language = "JavaScript">
    var empresaModalAnt = new Array();
    var botaoAtivo = true;
    const empresaCampos = ["descr", "endereco", "telefone", "responsavel", "id_responsavel"];

    for (var i = 0; i < empresaCampos.length; i++) {
        document.getElementById(empresaCampos[i]).addEventListener("keyup", function() {
            var diferenca = false;
            for (var j = 0; j < empresaCampos.length; j++) {
                if (document.getElementById(empresaCampos[j]).value != empresaModalAnt[empresaCampos[j]]) diferenca = true;
            }
            modalEmpresaAtivarBotao(diferenca);
        });
    }

    function modalEmpresaAtivarBotao(ativar) {
        botaoAtivo = ativar;
        if (!ativar) {
            document.getElementById("empresaModalBotao").style.background = "var(--gray)";
            document.getElementById("empresaModalBotao").style.borderColor = "var(--gray-dark)";
            document.getElementById("empresaModalBotao").style.color = "var(--gray-dark)";
        } else {
            document.getElementById("empresaModalBotao").style.background = "";
            document.getElementById("empresaModalBotao").style.borderColor = "";
            document.getElementById("empresaModalBotao").style.color = "";
        }
    }

    function openModalEmpresa(criar) {
        if (criar) document.getElementById("id_empresa").value = "";
        document.getElementById("empresaModalLabel").innerHTML = criar ? "Cadastrar empresa" : "Editar empresa";
        document.getElementById("empresaModal_form").action = "/saude-beta/empresa/";
        document.getElementById("empresaModal_form").action += criar ? "criar" : "editar";
        document.getElementById("empresaModal_form").method = criar ? "post" : "get";
        for (var i = 0; i < empresaCampos.length; i++) empresaModalAnt[empresaCampos[i]] = document.getElementById(empresaCampos[i]).value;
        modalEmpresaAtivarBotao(false);
        $("#empresaModal").modal("show");
    }

    function salvar_empresa() {
        if (botaoAtivo) {
            var erros = new Array();
            const campos = {
                "descr" : "Descrição",
                "endereco" : "Endereço",
                "telefone" : "Telefone",
                "responsavel" : "Responsável",
                "id_responsavel" : "Responsável"
            };
            for (x in campos) {
                if (document.getElementById(x).value == "") erros[erros.length] = [x, campos[x]];
            }
            if (erros.length) {
                alert('Aviso\nO campo "' + erros[0][1] + '" não foi preenchido corretamente');
                document.getElementById(erros[0][0]).focus();
            } else {
                document.getElementById("telefone").value = phoneInt(document.getElementById("telefone").value);
                document.getElementById("empresaModal_form").submit();
            }
        }
    }

    function editar_empresa(id) {
        document.getElementById("id_empresa").value = id;
        $.get(
            '/saude-beta/empresa/ver', {
            id_emp : id
        }, function (data, status) {
            data.forEach(empresa => {
                for (var i = 0; i < empresaCampos.length; i++) document.getElementById(empresaCampos[i]).value = empresa[empresaCampos[i]];
                document.getElementById("id_enc").value = empresa.id_enc;
            });
            document.getElementById("telefone").value = phoneMask(document.getElementById("telefone").value);
            openModalEmpresa(false);
        });
    }

    function deletar_empresa(id) {
        ShowConfirmationBox(
            'Deseja excluir essa empresa?',
            "", true, true, false,
            function () {
                $.get(
                    "/saude-beta/empresa/deletar", {
                        id_emp : id
                    },
                    location.reload()
                );
            },
            function () {}
        );
    }

    function limitaTel() {
        const max = 12;
        const numeros = "0123456789";
        do {
            var tel = document.getElementById("telefone").value;
            var cont = 0;
            for (var i = 0; i < tel.length; i++) {
                if (numeros.indexOf(tel.charAt(i)) > -1) cont++;
            }
            if (cont > max) tel = tel.substring(0, tel.length - 1);
            document.getElementById("telefone").value = tel;
        } while (cont > max)
    }

    window.onkeyup = function(e) {
        if (document.getElementById("empresaModal").style.display != "none" && e.keyCode == 13) salvar_empresa(); 
    }
</script>