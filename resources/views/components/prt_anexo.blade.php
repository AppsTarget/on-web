<div class="container-fluid card p-3">
    <h5 class="w-100 btn-link-target">Imagens e Anexos</h5>
     <div class="d-flex" style="justify-content: end; margin-top: -40px">
        <div class="col-3" style="margin: 5px -13px 5px 0px;">
            <select id="select-pasta" class="custom-select" onchange="anexos_por_pessoa($('#id_pessoa_prontuario').val())">
                <option value="0">Todos</option>
                <option value="1">Laboratório</option>
                <option value="2">Imagem</option>
                <option value="3">Testes IEC Avançados</option>
                <option value="4">Cardápios</option>
                <option value="5">Encaminhamentos</option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div id="table-prontuario-anexo">
            </div>
        </div>
    </div>
</div>

{{--<button class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#criarPastaModal" style = "bottom:4.5rem" title = "Nova pasta">
    <svg class="svg-inline--fa fa-folder fa-w-16 my-icon mr-2" aria-hidden="true" focusable="false" data-prefix="fal" data-icon="folder" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="" style = "margin-right:0 !important;scale:1.3">
        <path fill="currentColor" d="M194.74 96l54.63 54.63c6 6 14.14 9.37 22.63 9.37h192c8.84 0 16 7.16 16 16v224c0 8.84-7.16 16-16 16H48c-8.84 0-16-7.16-16-16V112c0-8.84 7.16-16 16-16h146.74M48 64C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V176c0-26.51-21.49-48-48-48H272l-54.63-54.63c-6-6-14.14-9.37-22.63-9.37H48z"></path>
    </svg>
</button>--}}
<button onclick="$('#criarAnexoModal #id_paciente').val($('#id_pessoa_prontuario').val())" class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#criarAnexoModal" title = "Novo anexo">
    <i class="my-icon fas fa-plus"></i>
</button>
@include('.modals.prt_anexo_modal')
@include('.modals.prt_anexo_visualizar')
@include('.modals.prt_pasta_modal')
<script>
</script>