{{-- <div class="row">
    <div class="col-4">
        <h3 class="text-right">Créditos Restantes:</h3>
        <h4 class="text-right">R$ 100,00</h4>
    </div>
    <div class="col-4">
        <h3 class="text-right">Convertidos no mês:</h3>
        <h4 class="text-right">R$ 100,00</h4>
    </div>
    <div class="col-4">
        <h3 class="text-right">Total Convertido:</h3>
        <h4 class="text-right">R$ 100,00</h4>
    </div>
</div>  --}}
<div class="card">
    <div class=col-12 style="margin-top: 10px;color: #383333;">
        <h2>Total: R$ <span id='valor-total-creditos'></span></h2>
    </div>
    <div class='col-12 form-group d-flex' style="margin-bottom: 15px;margin-top: 15px;justify-content: end;margin-left: -1.5%;">
        <div class="col-4">
            <label for="data-inicial-credito" class="custom-label-form">Data Inicial*</label>
            <input id="data-inicial-credito" name="data-inicial" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____">
        </div>
        <div class="col-4">
            <label for="data-final-credito" class="custom-label-form">Data Final*</label>
            <input id="data-final-credito" name="data-final" class="form-control date" autocomplete="off" type="text" placeholder="__/__/____">
        </div>
        <div class="col-2" style="padding: 25px 0px 0px 0px;">
            <button id="botao-lote-agendamento-modal" class="btn btn-target m-auto px-5" type="button" onclick="creditos_por_pessoa($('#id_pessoa_prontuario').val())">Confirmar</button>
        </div>
    </div>
    <div class="table-header-scroll">
        <table>
            <thead>
                <tr>
                    <th width="8%" class="text-left">Cód</th>
                    <th width="8%" class="text-left">Contrato</th>
                    <th width="32%" class="text-left">Atividades/Plano</th>
                    <th width="8%" class="text-right">Data</th>
                    <th width="8%" class="text-right">Hora</th>
                    <th width="10%" class="text-right">Valor</th>
                    <th width="10%" class="text-right">Tipo</th>
                    <th width="12%" class="text-right">Responsável</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="table-body-scroll custom-scrollbar">
        <table id="table-mov-credito" class="table table-hover">
            <tbody>
               
            </tbody>
        </table>
    </div>
</div>

