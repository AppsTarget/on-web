<!-- Modal -->
<div class="modal fade" id="salaModal" aria-labelledby="salaModalLabel" aria-modal="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="salaModalLabel">
                    Sala
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id = "salaModalForm"
                      class = "container-fluid"
                      method = "POST"
                      action = "/saude-beta/financeiro/alugueis/sala/gravar"
                >
                    @csrf
                    <input class = "id_sala" name = "id_sala" type="hidden">
                    <input id = "retroativo" name = "retroativo" type="hidden">
                    <div class="row">
                        <div class="col-9 form-group" style = "margin-bottom:0">
                            <label for="descr" class = "custom-label-form">Descrição:</label>
                            <input id = "descr" name = "descr" class = "form-control" autocomplete = "off" type = "text">
                            <span id = "descrlen" class = "custom-label-form" style = "
                                text-align: right;
                                width: 100%;
                                display: block;
                            ">0/100</span>
                        </div>
                        <div class="col-3 form-group" style = "margin-bottom:0">
                            <label for="valor" class = "custom-label-form">Valor:</label>
                            <input id = "valor" name = "valor" class = "form-control money-brl2 text-right" value = "R$ 0,00" type = "text" autocomplete = "off">
                        </div>
                    </div>
                    <div class="row">
                        <button class="btn btn-target my-3 mx-auto px-5" type="button" onclick = "gravarSala()">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type = "text/javascript" language = "JavaScript">
    window.addEventListener("load", function() {
        document.getElementById("descr").addEventListener("keydown", function() {
            var valor = document.getElementById("descr").value;
            document.getElementById("descrlen").innerHTML = valor.length + "/100";
            document.getElementById("descr").value = valor.substring(0, 100);
        });
        $("#salaModalForm .form-control").each(function() {
            $(this).keyup(function(e) {
                $(this).css("border", "");
                if (e.keyCode == 13) gravarSala();
            });
        });
    });

    function gravarSala() {
        var erros = new Array();
        var campo = document.getElementById("descr").value;
        if (campo.length == 0) erros.push("descr");
        campo = document.getElementById("valor").value;
        if (phoneInt(campo) == "" || parseInt(phoneInt(campo)) == 0 || (parseInt(phoneInt(campo)) / 100) > 9999) erros.push("valor");
        for (var i = 0; i < erros.length; i++) document.getElementById(erros[i]).style.border = "2px solid red";
        if (erros.length == 0) {
            try {
                if (campo != valAnt && $("#sala" + $($(".id_sala")[0]).val()).data().pessoa != '') {
                    $(".modal-confirmation-mobile .btn").each(function() {
                        $(this).css("width", "50%");
                        $($(this).parent()).css("padding-left", "1.5rem");
                    });
                    $("#confirmation_cancel").removeClass("ml-4");
                    ShowConfirmationBox(
                        'Valor alterado',
                        'No momento, essa sala está alugada.<br />' +
                        'Deseja alterar o valor das parcelas do contrato vigente?',
                        true, true, true,
                        function () {
                            $("#retroativo").val("S");
                            gravarSalaMain();
                        },
                        function () {
                            gravarSalaMain();
                        },
                        'Sim',
                        'Não',
                        'Cancelar'
                    );
                } else if ($("#descr").val() != descrAnt || campo != valAnt) gravarSalaMain();
                else $("#salaModal").modal("hide");
            } catch(err) {
                gravarSalaMain();
            }
        }
    }

    function gravarSalaMain() {
        $("#valor").val(parseInt(phoneInt($("#valor").val())) / 100);
        $("#salaModalForm").submit();
    }
</script>