
	<div id="carrossel">  
        <div class="col-xs-12">
            <div class="rad-info-box rad-txt-success">
                <i style=" background: url(/saude-beta/img/ico-batimento.png); "></i>
                <span class="heading">Batimentos</span>                  
                <span class="value"><span>-</span></span>
            </div>  
        </div>

        <div class="col-xs-12">
            <div class="rad-info-box rad-txt-primary">
                <i style=" background: url(/saude-beta/img/ico-caloria.png); "></i>
                <span class="heading">Calorias (ativa+repouso)</span>
                <span class="value"><span>-</span></span>
            </div>
        </div>  

        <div class="col-xs-12">
            <div class="rad-info-box rad-txt-danger">
                <i style=" background: url(/saude-beta/img/ico-passo.png); "></i>
                <span class="heading">Passos</span>
                <span class="value"><span>-</span></span>
            </div>
        </div>

        <div class="col-xs-12">
            <div class="rad-info-box">
                <i style=" background: url(/saude-beta/img/ico-distancia.png); "></i>
                <span class="heading">Distancia</span>
                <span class="value"><span>-</span></span>
            </div>
        </div>

        <div class="col-xs-12">          
            <div class="rad-info-box rad-txt-primary">                  
                <i style=" background: url(/saude-beta/img/ico-idade.png); "></i>                  
                <span class="heading">Idade</span>                  
                <span class="value">              
                <span data-serveronclick="editar" data-onclickparam="14755">{{ $idade }}</span></span>         
            </div>
        </div>

        <div class="col-xs-12">          
            <div class="rad-info-box rad-txt-danger">
                <i style=" background: url(/saude-beta/img/ico-peso.png); "></i>                  
                <span class="heading">Peso</span>                  
                <span class="value">{{ $pessoa->peso }}<span data-serveronclick="editar" data-onclickparam="14755">
                    0 kg
                </span></span>          
            </div>  
        </div>

        <div class="col-xs-12">          
            <div class="rad-info-box">                  
                <i style=" background: url(/saude-beta/img/ico-altura.png); "></i>                  
                <span class="heading">Altura</span>                  
                <span class="value">              
                    <span data-serveronclick="editar" data-onclickparam="14755">
                        {{ $pessoa->altura }} m
                    </span></span>          
            </div>  
        </div>
	</div>
    <script>
        window.addEventListener('load', () => {
            $.get(
                '/saude-beta/IEC/listar-areas-recomendadas/' + $('#id_pessoa_prontuario').val(),
                function(data,status){
                    console.log(data + ' | ' + status)
                    data = $.parseJSON(data);
                    $('#areas-recomendadas-relogio').empty()
                    data.forEach(area => {
                        html = '<img class="area-filtro" style="width: 41px;position: relative;" src="http://vps.targetclient.com.br/saude-beta/img/areas/' + area.id_area + '.png">'
                        $('#areas-recomendadas-relogio').append(html)
                    }
                    )
                }
            )
        })
    </script>



     