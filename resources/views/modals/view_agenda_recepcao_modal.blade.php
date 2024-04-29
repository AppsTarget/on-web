<!-- Modal -->
<div class="modal fade" id="recepcaoAgendaModal" aria-labelledby="recepcaoAgendaModalLabel" aria-modal="true">
    <div class="modal-dialog modal-xl" role="document" style="max-width: 100%;">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="recepcaoAgendaLabel">
                    Agenda
                </h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div id="modal-body-IEC" class="modal-body">
                <div style="display:flex; justify-content: end">
                    <div id="mudar-data-visualizacao" class="btn-group custom-card" role="group" aria-label="Mudar Data" style="height: 40px;margin-bottom: 15px;" onclick="abrir_modal_recepcao(false);">
                        <button type="button" class="btn btn-white" data-function="-" title="Retroceder">
                            <i class="my-icon fas fa-chevron-left"></i>
                        </button>
                        <button type="button" class="btn btn-white" data-function="today" title="Hoje">
                            <p>Hoje</p>
                        </button>
                        <button type="button" class="btn btn-white" data-function="+" title="Avançar" onclick="abrir_modal_recepcao(false);">
                            <i class="my-icon fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <div id='colunas-dias' style="display: flex;justify-content: space-between;">
                    <div class="coluna-dia">
                        <div class="title-dia">
                            <p>Domingo</p>
                            <h1 data-dia_semana="1">12</h1>
                        </div>
                        <ul class='grades-view-recepcao'  data-dia_semana='1'>
                        </ul>
                    </div>
                    <div class="coluna-dia">
                        <div class="title-dia">
                            <p>Segunda-feira</p>
                            <h1 data-dia_semana="2">13</h1>
                        </div>
                        <ul class='grades-view-recepcao'  data-dia_semana='2'>
                        </ul>
                    </div>
                    <div class="coluna-dia">
                        <div class="title-dia">
                            <p>Terça-feira</p>
                            <h1 data-dia_semana="3">14</h1>
                        </div>
                        <ul class='grades-view-recepcao' data-dia_semana='3'>
                        </ul>
                    </div>
                    <div class="coluna-dia" >
                        <div class="title-dia">
                            <p>Quarta-feira</p>
                            <h1 data-dia_semana="4">15</h1>
                        </div>
                        <ul class='grades-view-recepcao'data-dia_semana='4'>
                        </ul>
                    </div>
                    <div class="coluna-dia" >
                        <div class="title-dia">
                            <p>Quinta-feira</p>
                            <h1 data-dia_semana="5">16</h1>
                        </div>
                        <ul class='grades-view-recepcao' data-dia_semana='5'>
                        </ul>
                    </div>
                    <div class="coluna-dia">
                        <div class="title-dia">
                            <p>Sexta-feira</p>
                            <h1 data-dia_semana="6">17</h1>
                        </div>
                        <ul class='grades-view-recepcao' data-dia_semana='6'>
                        </ul>
                    </div>
                    <div class="coluna-dia">
                        <div class="title-dia">
                            <p>Sábado</p>
                            <h1 data-dia_semana="7">18</h1>
                        </div>
                        <ul class='grades-view-recepcao' data-dia_semana='7'>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .coluna-dia{
        min-height: 100%;
        padding: 5px 5px 15px 5px;
        width: 13.5%;
        border-radius: 15px 15px 0px 0px;
        text-align: center;
        color: #363636;
        background: #f2f2f2;
        font-weight: 600;
    }
    .grades-view-recepcao li{
        font-weight: 100;
        margin-bottom: 70px;
        min-height: 50px;
        cursor: pointer;
    }
    .grades-view-recepcao li p{
        margin-bottom: 0;
        text-align: initial;
    }
    .grades-view-recepcao li:hover{
        background-color: #c1c1c1;
    }
    .barra-indicacao-grade-cheia {
        height: 10px;
        border: 1px solid #dedede;
        background-color: #dedede;
        
    }
    .barra-indicacao-grade-cheia div{
        background: #008000bf;
        height: 100%;
    }
    .title-dia {
        margin-bottom: 20px;
    }
</style>