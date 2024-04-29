<style>
    #lista {
        display: block;
        width: 45%;
        padding: 5px 0px 0px 7px;
        overflow-y: auto;
        height: 515px;
    }
    #lista h3 {
        color: black;
        font-size: 22px;
        margin: -5px 0px 20px 0px;
    }
    #lista .item div {
        width: 100%;
        display: flex;
        justify-content: space-between;
    }
    .sel-dado-saude {
        background-color: #0000001f !important;    
    }
    #grafico1, #grafico2 {
        height: 400px !important,
        
    }
    .item{
        display: flex;
        flex-wrap: wrap;
        background-color: #0000000d;
        padding: 15px 15px 5px 15px;
        margin-bottom: 13px;
        border-radius: 5px 5px 40px 5px;
        width: 90%;
        cursor: pointer
    }
    .item p {
        font-size: 17px;
    }
    .item h5 {
        font-size: 25px !important;
        color: #000000bd;
        margin-top: -5px;
    }
    .item span{
        font-size: 10px;
        margin-left: 7px;
    }
</style>    
<div class="container-fluid card py-4 p-3">
    {{-- <h5 class="w-100 mb-3 btn-link-target">Saúde</h5> --}}
    <div id="grafico-saude-mobile-list" class="row">
        <div id="lista" style="display:block">
            <div>
                <h3>Consumo Energético (Hoje)</h3>
            </div>

            <div class="item sel-dado-saude" data-tipo_dado="1">
                <div>
                    <p style="color: #ed3a35;">Energia Ativa</p>
                    <img style="width: 40px; height: 40px" src="{{ asset('img/calorias.png') }}">
                </div>
                <h5 id="energia-ativa">
                    51.00 <span>cal</span>
                </h5>
            </div>
            <div class="item" data-tipo_dado="2">
                <div>
                    <p style="color: #ed3a35;">Energia de Repouso</p>
                    <img style="width: 40px; height: 40px" src="{{ asset('img/calorias.png') }}">
                </div>
                <h5 id="energia-repouso">
                    51.00 <span>cal</span>
                </h5>
            </div>

            <div>
                <h3>Saúde (Hoje)</h3>
            </div>

            <div class="item" data-tipo_dado="3">
                <div>
                    <p style="color: #993399">Horas em pé</p>
                    <img style="width: 40px; height: 40px" src="{{ asset('img/tenis-de-corrida.png') }}">
                </div>
                <h5 id="minutos_em_pe">
                    51.00 <span>h</span>
                </h5>
            </div>
            <div class="item" data-tipo_dado="4">
                <div>
                    <p style="color: #ed3a35;">Percentual de gordura</p>
                    <img style="width: 40px; height: 40px" src="{{ asset('img/calorias.png') }}">
                </div>
                <h5 id="percentual-gordura">
                    51.00 <span>%</span>
                </h5>
            </div>
            <div class="item" data-tipo_dado="5">
                <div>
                    <p style="color: #ff0000;">Batimentos</p>
                    <img style="width: 40px; height: 40px" src="{{ asset('img/Batimento.png') }}">
                </div>
                <h5 id="batimento">
                    51.00 <span>bpm</span>
                </h5>
            </div>
            <div class="item" data-tipo_dado="6">
                <div>
                    <p style="color: #00a7fd">Frequência Respiratória</p>
                    <img style="width: 40px; height: 40px" src="{{ asset('img/pulmao.png') }}">
                </div>
                <h5 id="frequencia-respiratoria">
                    51.00 <span>respirações/min</span>
                </h5>
            </div>
            <div class="item" data-tipo_dado="7">
                <div>
                    <p style="color: #00a7fd">Sono</p>
                    <img style="width: 40px; height: 40px" src="{{ asset('img/cama.png') }}">
                </div>
                <h5 id="sono">
                    51.00 <span>min/dia</span>
                </h5>
            </div>

            <div>
                <h3>Movimento (Hoje)</h3>
            </div>

            <div class="item" data-tipo_dado="8">
                <div>
                    <p style="color: #993399">Passos</p>
                    <img style="width: 40px; height: 40px" src="{{ asset('img/tenis-de-corrida.png') }}">
                </div>
                <h5 id="passos">
                    51.00 <span>Passos</span>
                </h5>
            </div>
            <div class="item" data-tipo_dado="9">
                <div>
                    <p style="color: #993399">Distância a Pé + Correndo</p>
                    <img style="width: 40px; height: 40px" src="{{ asset('img/corrida.png') }}">
                </div>
                <h5 id="correndo">
                    51.00 <span>km/dia</span>
                </h5>
            </div>
            <div class="item" data-tipo_dado="10">
                <div>
                    <p style="color: #993399">Distância de Bicicleta</p>
                    <img style="width: 40px; height: 40px" src="{{ asset('img/bicicleta.png') }}">
                </div>
                <h5 id="bicicleta">
                    3 <span>km/dia</span>
                </h5>
            </div>
            <div class="item" data-tipo_dado="11">
                <div>
                    <p style="color: #993399">Distância Nadando</p>
                    <img style="width: 40px; height: 40px" src="{{ asset('img/touca-de-natacao.png') }}">
                </div>
                <h5 id="nadando">
                    5 <span>m/dia</span>
                </h5>
            </div>
        </div>
        <div class="col" style='display: flex; justify-content: center'>
            <div class="graficos-mobile col" style="max-width: 90%">
                <h3 align='center'>Últimos 30 dias</h3>
                <canvas id="grafico-atv-semanal" width="400" height="400" style="max-height: 500px"></canvas>
            </div>
            {{-- <div style='min-width: 50%;max-width:50%'>
                <h3 align='center'>Gráficos de Atividade Semestral</h3>
                <canvas id="grafico-evolucao-iec" width="400" height="400"></canvas>
            </div> --}}
        </div>







        <div id="chartsSaude" style="width: 100%;
                    margin-top: 50px;
                    margin-left: 5px;
                    padding: 0px 0px 0px 15px;">
            <div>
                <div class="d-flex" style="opacity: .9; width: 90%">
                    <h2 id="titulo" style="color: #161313;">
                        
                    </h2>
                    <img id="titulo-img" style = "width: 40px;height: 40px;margin-left: 15px;margin-top: -8px;" src="">
                </div>
                <div>
                    <p id="descricao" style="color: #2b2b2b;margin-top: 5px;line-height: 1.2;">
                        
                    </p>
                </div>
            </div>
            <div class="col" style='display: flex; justify-content: center'>
                <div id="div-grafico-1" class="graficos-mobile col" style="max-width: 90%">
                    <h3 align='center'>Atividade Semestral</h3>
                    <canvas id="grafico-atv-semanal" width="400" height="400" style="max-height: 500px"></canvas>
                </div>
                <div id="div-grafico-2" style='min-width: 50%;max-width:50%'>
                    <h3 align='center'>Gráficos de Atividade Semestral</h3>
                    <canvas id="grafico-evolucao-iec" width="400" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/chart.min.js')            }}"></script>
{{-- <script src="{{ asset('js/my-styles.js')            }}"></script> --}}
<script>
    const ctx = document.getElementById('grafico-atv-semanal').getContext('2d')
        //   ctx2 = document.getElementById("grafico-evolucao-iec").getContext("2d");
    window.addEventListener('load', () => {

        $.get(
            '/saude-beta/agenda/atividade-semanal/' + $('#id_pessoa_prontuario').val(),
            function(data,status){
                console.log(data + ' | ' + status)
                data = $.parseJSON(data);

                let agendamentos = [],
                    ids = [],
                    opcores = ['rgba(255, 99, 132, 0.4)',
                        'rgba(54, 162, 235, 0.4)',
                        'rgba(255, 206, 86, 0.4)',
                        'rgba(75, 192, 192, 0.4)',
                        'rgba(153, 102, 255, 0.4)',
                        'rgba(255, 159, 64, 0.4)',
                        'rgb(139,69,19, 0.4)',
                        'rgb(0,255,255, 0.4)',
                        'rgb(119,136,153, 1)',
                        'rgba(153, 102, 255, 0.4)',
                        'rgba(255, 159, 64, 0.4)',
                        'rgb(139,69,19, 0.4)',
                        'rgb(0,255,255, 0.4)',
                        'rgb(119,136,153, 0.4)',

                    ]
                    opbordas = ['rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgb(139,69,19, 1)',
                        'rgb(0,255,255, 1)',
                        'rgb(119,136,153, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgb(139,69,19, 1)',
                        'rgb(0,255,255, 1)',
                        'rgb(119,136,153, 1)',

                    ]
                    nomes = [],
                    valores = [],
                    cores = [],
                    bordas = [],
                    control_cores = 0;
                data.forEach(agendamento => {
                    if (nomes.filter(x => x === agendamento.descr_modalidade).length == 0){
                        nomes.push(agendamento.descr_modalidade)
                    }
                    agendamentos.push(agendamento.descr_modalidade)
                    ids.push(agendamento.id_procedimento)
                    cores.push(opcores[control_cores]);
                    bordas.push(opbordas[control_cores]);
                    control_cores++;







                });
                nomes.forEach(nome => {
                    valores.push(agendamentos.filter(x => x === nome).length)
                })
                const myChart1 = new Chart(ctx, {
                    type: 'bar',
                    data:{
                        labels: nomes,
                        datasets: [{
                            label: 'Atividades',
                            data: valores,
                            backgroundColor: cores,
                            borderColor: bordas,
                            borderWidth: 1, 
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                })
            }
        )
        if (detectar_mobile()) {
            setTimeout(() => {
                document.querySelector('.sel-dado-saude').className = 'item'
            }, 1000); 
        }


        document.querySelectorAll('.item').forEach(el => {
            el.addEventListener('click', () => {
                // console.log(opcao)
                if (!detectar_mobile()){
                    mostrar_dado_saude(el.dataset.tipo_dado)
                }
                else {
                    mostrar_dado_saude_mobile(el.dataset.tipo_dado)
                }
                })
        })



        














        // $.get('/saude-beta/IEC/listar-pessoa-grafico/' + $('#id_pessoa_prontuario').val(),
        //     function(data,status){
        //         console.log(data + status)
        //         let opcores = ['rgba(255, 99, 132, 0.4)',
        //                 'rgba(54, 162, 235, 0.4)',
        //                 'rgba(255, 206, 86, 0.4)',
        //                 'rgba(75, 192, 192, 0.4)',
        //                 'rgba(153, 102, 255, 0.4)',
        //                 'rgba(255, 159, 64, 0.4)',
        //                 'rgb(139,69,19, 0.4)',
        //                 'rgb(0,255,255, 0.4)',
        //                 'rgb(119,136,153, 1)',
        //                 'rgba(153, 102, 255, 0.4)',
        //                 'rgba(255, 159, 64, 0.4)',
        //                 'rgb(139,69,19, 0.4)',
        //                 'rgb(0,255,255, 0.4)',
        //                 'rgb(119,136,153, 0.4)',
        //             ]
        //             opbordas = ['rgba(255, 99, 132, 1)',
        //                 'rgba(54, 162, 235, 1)',
        //                 'rgba(255, 206, 86, 1)',
        //                 'rgba(75, 192, 192, 1)',
        //                 'rgba(153, 102, 255, 1)',
        //                 'rgba(255, 159, 64, 1)',
        //                 'rgb(139,69,19, 1)',
        //                 'rgb(0,255,255, 1)',
        //                 'rgb(119,136,153, 1)',
        //                 'rgba(153, 102, 255, 1)',
        //                 'rgba(255, 159, 64, 1)',
        //                 'rgb(139,69,19, 1)',
        //                 'rgb(0,255,255, 1)',
        //                 'rgb(119,136,153, 1)',
        //             ],
        //             lista_geral = [],
        //             lista_group = [],
        //             ldatas = [],
        //             fColor = [],
        //             sColor = [],
        //             pColor = [],
        //             psColor = [],
        //             phfColor = [],
        //             phsColor = [],
        //             valores = []

        //             i = 0
        //         data.forEach(IEC => {
        //             lista_geral.push(IEC.descr_iec)
        //             ldatas.push(IEC.updated_at)
        //             if (!lista_group.includes(IEC_pessoa.id_questionario)){
        //                 lista_group.push(IEC.descr_iec)
        //                 fColor.push(opcores[i])
        //                 sColor.push(opbordas[i])
        //                 pColor.push(opbordas[i])
        //                 psColor.push(opbordas[i])
        //                 phfColor.push(#fff)
        //                 phsColor.push(opbordas[i])
        //             }
        //             i++;
        //         })
        //         var data-evolucao-IEC
        //     }
        // )
            
        // const myChart1 = new Chart(ctx, {
        //             type: 'line',
        //             data:
        //         })


        // var myNewChart = new Chart(ctx2).Line({
        //     labels: ["January", "February", "March", "April", "May", "June", "July"],
        //     datasets: [{
        //         label: "My First dataset",
        //         fillColor: "rgba(220,220,220,0.2)",
        //         strokeColor: "rgba(220,220,220,1)",
        //         pointColor: "rgba(220,220,220,1)",
        //         pointStrokeColor: "#fff",
        //         pointHighlightFill: "#fff",
        //         pointHighlightStroke: "rgba(220,220,220,1)",
        //         data: [65, 59, 80, 81, 56, 55, 40]
        //     }, {
        //         label: "My Second dataset",
        //         fillColor: "rgba(151,187,205,0.2)",
        //         strokeColor: "rgba(151,187,205,1)",
        //         pointColor: "rgba(151,187,205,1)",
        //         pointStrokeColor: "#fff",
        //         pointHighlightFill: "#fff",
        //         pointHighlightStroke: "rgba(151,187,205,1)",
        //         data: [28, 48, 40, 19, 86, 27, 90]
        //     }]
        //     };);
    })

    function mostrar_dado_saude(opcao){

        $('.sel-dado-saude').attr('class', 'item')

        $('[data-tipo_dado="'+ opcao +'"').addClass('sel-dado-saude')

        console.log(opcao)
        var     teste1, teste2
                var label, title, conteudo,
                    url = 'http://vps.targetclient.com.br/saude-beta/img/',
                    cor,
                    cor_texto = ''
        
                switch(opcao){
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


                $('#div-grafico-1').empty()
                           .append("<h3 style='color:"+cor+";margin: 5px 0px 0px 0px;font-size: 25px;'>Hoje</h3>")
                           .append('<canvas id="grafico1" width="400" height="400"></canvas>')
                $('#div-grafico-2').empty()
                                .append("<h3 style='color:"+cor+";margin: 5px 0px 0px 0px;font-size: 25px;''>Últimos Sete Dias</h3>")
                                .append('<canvas id="grafico2" width="400" height="400"></canvas>')
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


                var ctx1 = document.getElementById('grafico1').getContext('2d'),
                    ctx2 = document.getElementById('grafico2').getContext('2d')


                $.get(
                    "/saude-beta/api/gerar-grafico1/"+opcao+"/"+$('#id_pessoa_prontuario').val(),
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
                    "/saude-beta/api/gerar-grafico2/"+opcao+"/"+$('#id_pessoa_prontuario').val(),
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
    }










    function mostrar_dado_saude_mobile(opcao){
        $('#mostrarSaudeMobileModal').modal('show')

        $('#div-grafico-3').empty()
                           .append('<canvas id="grafico3" width="400" height="400"></canvas>')
        $('#div-grafico-4').empty()
                           .append('<canvas id="grafico4" width="400" height="400"></canvas>')

        var ctx3 = document.getElementById('grafico3').getContext('2d'),
            ctx4 = document.getElementById('grafico4').getContext('2d'),
            teste1, teste2
        var label, title, conteudo,
            url = 'http://vps.targetclient.com.br/saude-beta/img/',
            cor,
            cor_texto = ''
        
        switch(opcao){
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
                label = 'bpm'
                title = 'Batimento'
                conteudo = "Refere-se a quantas vezes seu coração bate por minuto, e podem ser um indicador de saúde cardiovascular."
                url += 'Batimento.png'
                cor = "#ff0000"
                cor_texto = "#760603"
                break;
            case '4':
                label = 'respirações/min'
                title = 'Frequência Respiratório'
                conteudo = "Refere-se a quantas vezes você respira por minuto"
                url += 'pulmao.png'
                cor = "#00a7fd"
                cor_texto = "rgb(1 115 174)"
                break;
            case '5':
                label = 'min/dia'
                title = 'Sono'
                conteudo = "Tempo de sono"
                url += 'cama.png'
                cor = "#00a7fd"
                cor_texto = "rgb(1 115 174)"
                break;
            case '6':
                label = 'passos'
                title = 'Passos'
                conteudo = "A contagem de passos é o número de passos que você percorre ao longo do dia."
                url += "tenis-de-corrida.png"
                cor = "#993399"
                cor_texto = "rgb(98 20 98)"
                break;
            case '8':
                label = 'km/dia'
                title = 'Distância a Pé + Correndo'
                conteudo = "Distância em km que você caminhou e correu no dia."
                url += "corrida.png"
                cor = "#993399"
                cor_texto = "rgb(98 20 98)"
                break;
            case '9':
                label = 'km/dia'
                title = 'Distância de Bicicleta'
                conteudo = "Distância em km que você pedalou no dia"
                url += 'bicicleta.png'
                cor = "#993399"
                cor_texto = "rgb(98 20 98)"
                break;
            case '10':
                label = 'm/dia'
                title = 'Distância Nadando'
                conteudo = "Distiância em m que você nadou no dia"
                url += "touca-de-natacao.png"
                cor = "#993399"
                cor_texto = "rgb(98 20 98)"
                break;
        }
        $('#mostrarSaudeMobileModal #titulo').html(title).css('color', cor)
        $('#mostrarSaudeMobileModal #descricao').html(conteudo)
        $('#mostrarSaudeMobileModal #titulo-img').attr('src', url)
        $('#mostrarSaudeMobileModal .titleCharts').css('color', cor)

        if ($('#opcao').val() == 1 || $('#opcao').val() == 2){
            $('#mostrarSaudeMobileModal #titulo-img').attr('style', "width: 67px;height: 67px;margin-top: -25px;position: relative;left: 15px;")
        }
        else if ($('#opcao').val() == 3){
            $('#mostrarSaudeMobileModal #titulo-img').attr('style', "width: 56px;height: 56px;margin-top: -11px;")
        }
        else if ($('#opcao').val() == 4) {
            $('#mostrarSaudeMobileModal #titulo-img').attr('style', "width: 56px;height: 56px;margin-top: -11px;position: relative;left: 15px;")
        }
        else if ($('#opcao').val() == 5) {
            $('#mostrarSaudeMobileModal #titulo-img').attr('style', "width: 60px;height: 60px;margin-top: -11px;position: relative;left: 15px;")
        }
        else if ($('#opcao').val() == 6) {
            $('#mostrarSaudeMobileModal #titulo-img').attr('style', "width: 70px;height: 70px;margin-top: -22px;position: relative;left: 15px;")
        }
        else if ($('#opcao').val() == 8) {
            $('#mostrarSaudeMobileModal #titulo-img').attr('style', "width: 80px;height: 80px;margin-top: -22px;position: relative;left: 15px;")
        }
        else if ($('#opcao').val() == 9) {
            $('#mostrarSaudeMobileModal #titulo-img').attr('style', "width: 60px;height: 60px;margin-top: -15px;position: relative;left: 15px;")
        }
        else {
            $('#mostrarSaudeMobileModal #titulo-img').attr('style', "width: 67px;height: 67px;margin-top: -15px;position: relative;left: 15px;")
        }
        $.get(
            "/saude-beta/api/gerar-grafico1/"+opcao+"/"+$('#id_pessoa_prontuario').val(),
            function(data, status){
                console.log(data + ' | ' + status)
                data = $.parseJSON(data)
                teste1 = data

                var stackedLine1 = new Chart(ctx3, {
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
            "/saude-beta/api/gerar-grafico2/"+opcao+"/"+$('#id_pessoa_prontuario').val(),
            function(data, status){
                data = $.parseJSON(data)
                teste2 = data
                var stackedLine1 = new Chart(ctx4, {
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
    }

    var testando
    setTimeout(() => {
        $.get(
            "/saude-beta/api/getHealthResume", {
                iPersonID: $('#id_pessoa_prontuario').val()
            },
            function(data, status) {
                console.log("aqui " + JSON.stringify(data));
                data = $.parseJSON(data)
                console.log(data);
                $('#energia-ativa').html(data[0].sValue1 + "<span>cal</span")
                $('#energia-repouso').html(data[1].sValue1 + "<span>cal</span")
                $('#minutos_em_pe').html((parseFloat(data[2].sValue1) / 60).toFixed(0) + "<span>h</span")
                $('#percentual-gordura').html((data[3].sValue1 * 100) + "<span>%</span")
                $('#batimento').html(data[4].sValue1 + "<span>bpm</span")
                $('#frequencia-respiratoria').html(data[5].sValue1 + "<span>respirações/min</span")
                $('#sono').html((parseFloat(data[6].sValue1).toFixed(0) / 60) + "<span>h</span")
                $('#passos').html(data[7].sValue1 + "<span>passos/dia</span")
                $('#correndo').html(data[8].sValue1 + "<span>km/dia</span")
                $('#bicicleta').html(data[9].sValue1 + "<span>km/dia</span")
                $('#nadando').html(data[10].sValue1 + "<span>m/dia</span")
            }
        )
    }, 800);
        
            
    </script>

@include('modals.mostrar_saude_mobile_modal')


