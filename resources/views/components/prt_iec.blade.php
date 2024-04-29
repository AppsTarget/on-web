<div class="container-fluid card p-3">
    <h5 class="w-100 btn-link-target">IEC</h5>
    <div class="d-flex" style="justify-content: end; margin-top: -40px">
        <div class="col-3" style="margin: 5px 0px 5px 0px;">
            <select id="select-iec-ativos-inativos" class="custom-select" onchange="iec_por_pessoa($('#id_pessoa_prontuario').val())">
                <option value="S">Ativos</option>
                <option value="N">Inativos</option>
                <option value="L">Laudos</option>
            </select>
        </div>
    </div>
    <div class="row">
        <div id="table-prontuario-IEC-pessoa" class="accordion w-100 px-3">
        </div>
    </div>
</div>
@if (Auth::user()->id_profissional <> 28480002313)
    <button class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#laudoIECModal" style = "bottom:4.5rem">
        <img src = "../../img/chart.png" style = "width:100%"/>
    </button>
    <button class="btn btn-primary custom-fab" type="button" data-toggle="modal" data-target="#selecaoIECModal">
        <i class="my-icon fas fa-plus"></i>
    </button>
@endif
@include('.modals.prt_laudo')
@include('.modals.prt_selecao_iec_modal')
@include('.modals.prt_exibir_hist_iec_modal')
@include('.modals.prt_iec_modal')