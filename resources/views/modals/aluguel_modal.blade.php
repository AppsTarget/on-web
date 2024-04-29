<!-- Modal -->
<div class="modal fade" id="aluguelModal" aria-labelledby="aluguelModalLabel" aria-modal="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="aluguelModalLabel">
                    Aluguel
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form id = "aluguelModalForm"
                      class = "container-fluid"
                      method = "POST"
                >
                    @csrf
                    <input class = "id_sala" name = "id_sala" type="hidden">
                    <div class="row">
                        <div class="col-2 form-group" style = "margin-bottom:0">
                            <label for = "doc" class = "custom-label-form">Documento:</label>
                            <input
                                id = "doc"
                                name = "doc"
                                type = "text"
                                autocomplete = "off"
                                class = "form-control text-right"
                            >
                        </div>
                        <div class="col-6 form-group" style = "margin-bottom:0">
                            <label for = "membro" class = "custom-label-form">Membro:</label>
                            <input id = "membro_nome"
                                class = "form-control autocomplete"
                                placeholder = "Digitar nome do profissional..."
                                data-input = "#membro_id"
                                data-table = "pessoa"
                                data-column = "nome_fantasia"
                                data-filter_col = "colaborador"
                                data-filter = "P"
                                type = "text"
                                autocomplete = "off"
                            >
                            <input id = "membro_id" name = "membro" type = "hidden">
                        </div>
                        <div class="col-2 form-group" style = "margin-bottom:0">
                            <label for="venc" class = "custom-label-form">Dia de venc.:</label>
                            <input
                                id = "venc"
                                name = "venc"
                                type = "text"
                                autocomplete = "off"
                                class = "form-control text-right"
                            >
                        </div>
                        <div class="col-2 form-group" style = "margin-bottom:0">
                            <label for="parc" class = "custom-label-form">Nº de parcelas:</label>
                            <input
                                id = "parc"
                                name = "parc"
                                type = "text"
                                autocomplete = "off"
                                class = "form-control text-right"
                            >
                        </div>
                    </div>
                    <div class="row">
                        <button
                            id="btnAluguel" 
                            class="btn btn-target my-3 mx-auto px-5"
                            type="button"
                            onclick = "alugar()"
                        >
                            Alugar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<style type = "text/css">
    #aluguelModalForm .autocomplete-result {
        width:92%
    }
</style>
<script type = "text/javascript" language = "JavaScript">
    window.addEventListener("load", function() {
        $("#aluguelModalForm .form-control").each(function() {
            $(this).keyup(function() {
                $(this).css("border", "");
            });
        });
        $("#aluguelModalForm .col-2 .form-control").each(function() {
            $(this).keydown(function(e) {
                if (e.keyCode == 38 || e.keyCode == 40) {
                    if (e.keyCode == 38) $(this).val(parseInt($(this).val()) + 1);
                    else $(this).val(parseInt($(this).val()) - 1);
                    $(this).trigger("keydown");
                }
            });
            $(this).keyup(function(e) {
                var inicial = $(this).attr("id") == "doc" ? totalDoc + 1 : 1;
                if ($(this).val() == "" || parseInt($(this).val()) < inicial) $(this).val(inicial);
                $(this).val(phoneInt($(this).val()));
                if (e.keyCode == 13) alugar();
            });
            $(this).blur(function() {
                $(this).trigger("keyup");
            })
        });
    });

    function alugar() {
        var erros = new Array();
        var campo = document.getElementById("membro_nome").value;
        if (campo.length == 0 || document.getElementById("membro_id").value == "") erros.push("membro_nome");
        campo = phoneInt(document.getElementById("venc").value);
        if (campo != "" && parseInt(campo) > 0 && parseInt(campo) <= 28) {
            if (parseInt(campo) == vencAnt) {
                $("#aluguelModal").modal("hide");
                return;
            }
        } else erros.push("venc");
        campo = phoneInt(document.getElementById("parc").value);
        if (campo == "" || parseInt(campo) == 0) erros.push("parc");
        campo = phoneInt(document.getElementById("doc").value);
        var erroNdoc = false;
        if (document.getElementById("btnAluguel").innerHTML == "Alugar") {
            try {
                if (documentos["p" + document.getElementById("membro_id").value].indexOf(parseInt(campo)) > -1) erroNdoc = true;
            } catch(err) {}
        }
        if (campo == "" || parseInt(campo) == 0 || erroNdoc) erros.push("doc");
        for (var i = 0; i < erros.length; i++) document.getElementById(erros[i]).style.border = "2px solid red";
        if (erros.length == 0) {
            $("#doc").removeAttr("disabled");
            $("#membro_nome").removeAttr("disabled");
            $("#parc").removeAttr("disabled");
            document.getElementById("aluguelModalForm").submit();
        }
    }
</script>