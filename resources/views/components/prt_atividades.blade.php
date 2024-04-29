<div class="custom-table card"> 
    <div class="d-flex" style="justify-content: end">
        <div class="col-3" style="margin: 5px 0px 5px 0px"> 
            <select id="select-atividades" class='custom-select' onChange="">
                <option value="0">Sistema atual</option>
                <option value="1">Sistema antigo</option>
            </select>
        </div>
    </div>
    <hr style="margin-top:0; margin-bottom:0;">
    <div class="box-atividades">

        <h2 id="total-atividades-prontuario">TOTAL:</h2>
    
        

        <hr class="line-atividade">


        <h2 id="disponivel-atividades-prontuario">DISPONÍVEL:


        </h2>
        

        <hr class="line-atividade">

        <h2 id="agendados-atividades-prontuario">AGENDADOS:</h2>    
        <div style="height:20px; widht:20px" onclick="agendamentos_atividades_modal()">
            <img id="olho-open-modal" src="/saude-beta/img/olho.png" alt="Olho">
        </div>
    </div>

</div>