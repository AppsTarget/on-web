@extends('layouts.app')

@section('content')
<body>
    <input id="opcao" type="hidden" value="{{ $op }}">
    <input id="iPersonID" type="hidden" value="{{ $iPersonID }}">
    <div style="width:100%;margin-top: -50px;margin-left: 5px;">
        <div>
            <div class="d-flex" style="opacity: .9; width: 90%">
                <h2 id="titulo" style="color: #161313;">
                    
                </h2>
                <img id="titulo-img" style = "width: 40px;height: 40px;margin-left: 15px;margin-top: -8px;" src="">
            </div>
            {{-- <div style="width: 97%;height: 1px;background: black;margin-top: -8px;opacity: .7;"></div> --}}
            <div>
                <p id="descricao" style="color: #2b2b2b;margin-top: 5px;line-height: 1.2;">
                    
                </p>
            </div>
        </div>
        <div>
            <h4 class="titleCharts" style="color: #2b2b2b;margin-top: 20px;font-size: 20px;">
                Hoje
            </h4>
        </div>
        <div id="div-grafico-1" style="max-width: 90%">
            <canvas id="grafico1" width="400" height="300"></canvas>
        </div>
        <div>
            <h4 class="titleCharts" style="color: #2b2b2b;margin-top: 20px;font-size: 20px;">
                Últimos 7 dias
            </h4>
        </div>
        <div id="div-grafico-2" style="max-width: 90%">
            <canvas id="grafico2" width="400" height="300"></canvas>
        </div>
        <div style="height: 100px"></div>
    </div>
</body>
<style>
    @font-face {
        font-family: Dosis-Light;
        src: url(http://vps.targetclient.com.br/saude-beta/fonts/Dosis-Medium.ttf);
    }
    #loading-mobile {
        display: none !important
    }
    * {
        font-family: Dosis-Light;
    }
</style>
<script src="{{ asset('js/chart.min.js') }}"></script>
<script>
    window.addEventListener('load', () => {
        $('#div-grafico-1').empty()
                           .append('<canvas id="grafico1" width="400" height="400"></canvas>')
        $('#div-grafico-2').empty()
                           .append('<canvas id="grafico2" width="400" height="400"></canvas>')

        var ctx1 = document.getElementById('grafico1').getContext('2d'),
            ctx2 = document.getElementById('grafico2').getContext('2d'),
            teste1, teste2
        var label, title, conteudo,
            url = 'http://vps.targetclient.com.br/saude-beta/img/',
            cor,
            cor_texto = ''
        
        switch($("#opcao").val()){
            case '1':
                label = 'cal'
                title = 'Energia Ativa'
                conteudo = "Esta é uma estimativa da energia queimada e acima do seu uso de Energia de Repouso."
                url += 'calorias.png'
                cor = "#f91800d9"
                cor_texto = '#760603'
                break;
            case '2':
                label  = 'cal'
                title = 'Energia de Repouso'
                conteudo = "Isto é uma estimativa da energia que seu corpo usa a cada dia, enquanto minimamente ativo."
                url += "calorias.png"
                cor = "#f91800d9"
                cor_texto = '#760603'
                break;
            
            case '3':
                label = 'min'
                title = 'Minutos em pé'
                conteudo = "Refere-se aos minutos que você se mantém em pé por dia"
                url += 'Batimento.png'
                cor = "#ff0000"
                cor_texto = "#760603"
                break;
            case '4':
                label = '%'
                title = 'Percentual de gordura'
                conteudo = "Refere-se ao percentual de gordura corporal"
                url += 'Batimento.png'
                cor = "#ff0000"
                cor_texto = "#760603"
                break;
            case '5':
                label = 'bpm'
                title = 'Batimentos'
                conteudo = "Refere-se a quantas vezes seu coração bate por minuto, e pode ser um indicador de saúde cardiovascular."
                url += 'Batimento.png'
                cor = "#ff0000"
                cor_texto = "#760603"
                break;
            case '6':
                label = 'respirações/min'
                title = 'Frequência Respiratória'
                conteudo = "Refere-se a quantas vezes você respira por minuto"
                url += 'pulmao.png'
                cor = "#00a7fd"
                cor_texto = "rgb(1 115 174)"
                break;
            case '7':
                label = 'min/dia'
                title = 'Sono'
                conteudo = "Tempo de sono"
                url += 'cama.png'
                cor = "#00a7fd"
                cor_texto = "rgb(1 115 174)"
                break;
            case '8':
                label = 'passos'
                title = 'Passos'
                conteudo = "A contagem de passos é o número de passos que você percorre ao longo do dia."
                url += "tenis-de-corrida.png"
                cor = "#993399"
                cor_texto = "rgb(98 20 98)"
                break;
            case '9':
                label = 'km/dia'
                title = 'Distância a Pé + Correndo'
                conteudo = "Distância em km que você caminhou e correu no dia."
                url += "corrida.png"
                cor = "#993399"
                cor_texto = "rgb(98 20 98)"
                break;
            case '10':
                label = 'km/dia'
                title = 'Distância de Bicicleta'
                conteudo = "Distância em km que você pedalou no dia"
                url += 'bicicleta.png'
                cor = "#993399"
                cor_texto = "rgb(98 20 98)"
                break;
            case '11':
                label = 'm/dia'
                title = 'Distância Nadando'
                conteudo = "Distância em m que você nadou no dia"
                url += "touca-de-natacao.png"
                cor = "#993399"
                cor_texto = "rgb(98 20 98)"
                break;
        }
        $('#titulo').html(title).css('color', cor)
        $('#descricao').html(conteudo)
        $('#titulo-img').attr('src', url)
        $('.titleCharts').css('color', cor)

        if ($('#opcao').val() == 1 || $('#opcao').val() == 2){
            $('#titulo-img').attr('style', "width: 45px;height: 45px;margin-top: -4px;")
        }
        else if ($('#opcao').val() == 3){
            $('#titulo-img').attr('style', "width: 56px;height: 56px;margin-top: -11px;")
        }
        else if ($('#opcao').val() == 4) {
            $('#titulo-img').attr('style', "width: 56px;height: 56px;margin-top: -11px;position: relative;left: 15px;")
        }
        else if ($('#opcao').val() == 5) {
            $('#titulo-img').attr('style', "width: 60px;height: 60px;margin-top: -11px;position: relative;left: 15px;")
        }
        else if ($('#opcao').val() == 6) {
            $('#titulo-img').attr('style', "width: 70px;height: 70px;margin-top: -22px;position: relative;left: 15px;")
        }
        else if ($('#opcao').val() == 8) {
            $('#titulo-img').attr('style', "width: 80px;height: 80px;margin-top: -22px;position: relative;left: 15px;")
        }
        else if ($('#opcao').val() == 9) {
            $('#titulo-img').attr('style', "width: 60px;height: 60px;margin-top: -15px;position: relative;left: 15px;")
        }
        else {
            $('#titulo-img').attr('style', "width: 67px;height: 67px;margin-top: -15px;position: relative;left: 15px;")
        }
        $.get(
            "/saude-beta/api/gerar-grafico1/"+$("#opcao").val()+"/"+$('#iPersonID').val(),
            function(data, status){
                console.log(data + ' | ' + status)
                data = $.parseJSON(data)
                teste1 = data

                var stackedLine1 = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: label,
                        data: data.values,
                        fill: false,
                        borderColor: cor,
                        tension: 0
                    }]
                }
            });
            }
        )

        $.get(
            "/saude-beta/api/gerar-grafico2/"+$("#opcao").val()+"/"+$('#iPersonID').val(),
            function(data, status){
                data = $.parseJSON(data)
                teste2 = data
                var stackedLine1 = new Chart(ctx2, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: label,
                            data: data.values,
                            fill: false,
                            borderColor: cor,
                            tension: 0
                        }]
                    }
                });
            }
        )
    })

</script>

@endsection
