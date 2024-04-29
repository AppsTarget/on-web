<div class="modal fade" id="listarCaixasModal" aria-labelledby="listarCaixasModal" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 35%;font-size: 15px;">
        <div class="modal-content"
            style="display:flex; flex-direction:column;height:100%; width: 100%; position: relative;">
            <div class="modal-header">
                <div class="d-flex" style="margin-top: -5px">
                    {{-- <img style="width: 40px; height: 40px" src="{{ asset('img/logo_topo_limpo_on.png') }}"> --}}
                    <h3 style="color: #10518c;margin-left: 10px;margin-top: 12px;margin-bottom: -10px;">Caixas</h3>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height:100%; padding:0px 0px 16px 0px">
                <div class="table-header-scroll">
                    <table>
                        <thead>
                            <th>
                                Consultor
                            </th>
                            <th>
                            </th>
                        </thead>
                    </table>
                </div>
                <div class="table-body-scroll">
                    <table id="table-listar-caixa">
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
</style>
