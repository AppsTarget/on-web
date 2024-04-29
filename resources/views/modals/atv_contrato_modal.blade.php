<!-- Modal -->
<div class="modal fade" id="atvContratoModal" aria-labelledby="atvContratoModalLabel" aria-modal="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="atvContratoModalLabel"></h6>
                <button type="button" class="close">
                    <i class="my-icon far fa-print" title="Imprimir" onclick="imprimir()"></i>
                </button>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style = "margin-left:0">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>
<style type = "text/css">
    #atvContratoModal button {
        outline:none
    }

    #atvContratoModal .my-icon {
        color:#6c757d;
        scale:.9
    }

    #atvContratoModal .my-icon:hover {
        color:#000
    }
</style>
<script type = "text/javascript" language = "JavaScript">
    function imprimir() {
        var content = "<html><head>" + $("head").html() + "</head><body>" + $("#atvContratoModal .modal-body").html() + "</body></html>";
        var myWindow = window.open("", "_blank");
        myWindow.document.write(content);
    }
</script>