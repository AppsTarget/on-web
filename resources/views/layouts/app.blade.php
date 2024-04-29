<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <script>
       function detectar_mobile() {
            var check = false; //wrapper no check
            (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
            return check;
        }
        window.addEventListener('load', () => {
            if (detectar_mobile()){
                setTimeout(() => {
                    document.querySelector("#progress-loading-mobile").style.width = '100%'
                    setTimeout(() => {
                        document.querySelector('#loading-mobile').remove();
                    }, 2000)
                }, 100)
            }
            
            setTimeout(() => {
                setar_profissional();
            }, 1000)
        })
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Target | Saúde</title>

    <link rel="icon shortcut" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
    <link rel="icon"          href="{{ asset('img/favicon.ico') }}" type="image/x-icon">

    <link href="{{ asset('css/bootstrap.min.css')        }}" rel="stylesheet">
    <link href="{{ asset('css/my-style.css')             }}" rel="stylesheet">
    <link href="{{ asset('css/mystyle-mobile.css')             }}" rel="stylesheet">
    <link href="{{ asset('css/summernote.css')           }}" rel="stylesheet">
    <link href="{{ asset('css/datepicker.min.css')       }}" rel="stylesheet">
    <link href="{{ asset('css/colorpicker.css')          }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome-pro.min.css') }}" rel="stylesheet">

    <link href="{{ asset('css/slick.css')          }}" rel="stylesheet">
    <link href="{{ asset('css/slick-theme.css') }}" rel="stylesheet">
</head>
<body>
    <div id='loading-mobile' style="position: fixed;top: 1px;background: white;width: 100%;height: 100%;z-index: 9;opacity: 0.98; display: none">
        <div>
            <div style="margin-top: 50%;display: flex;justify-content: center;">
                <img style="width:50%" src="/saude-beta/img/logo_topo_limpo_on.png">
            </div>
            <div style="display: flex;justify-content: center;margin-top: 15px;">
                <div style="border: 1px solid #2e434e;height: 15px;width: 90%;border-radius: 10px;">
                    <div id="progress-loading-mobile" style="background: #334752;height: 100%;width: 6%;border-radius: 10px;transition: 2s width;"></div>
                </div>
            </div>
        </div>
    </div>
    <div id="app">
        <main>
            @yield('content')
        </main>
    </div>


    @include ('.components.nav_lista_notificacoes')
    @include ('.modals.criar_notificacao_modal2')
    @include('modals.caixa_modal')
    @include('modals.resumo_fechamento_modal')
    @include('modals.ajuste_saldo_caixa')
    
    @include('modals.alterar_empresa_modal')
    @include('modals.resumo_vendas_modal')
    @include('modals.extrato_caixa_modal')
    @include('modals.listar_caixas')
    @include('modals.agendamentos_pendentes_modal')
    
    @include('modals.zapsign_pessoa_modal')
    
    @include('components.confirmation_modal')
    
    

    
    <!-- Bibliotecas -->
    <script src="{{ asset('js/jquery.min.js')           }}"></script>
    <script src="{{ asset('js/popper.min.js')           }}"></script>
    <script src="{{ asset('js/jquery-ui.js')            }}"></script>
    <script src="{{ asset('js/jquery.mask.min.js')      }}"></script>
    <script src="{{ asset('js/jquery.sort-table.js')    }}"></script>
    <script src="{{ asset('js/moment.js')               }}"></script>
    <script src="{{ asset('lang/moment-pt-BR.js')       }}"></script>
    <script src="{{ asset('js/datepicker.min.js')       }}"></script>
    <script src="{{ asset('lang/datepicker.pt-br.js')   }}"></script>
    <script src="{{ asset('js/summernote.js')           }}"></script>
    <script src="{{ asset('lang/summernote-pt-BR.js')   }}"></script>
    <script src="{{ asset('js/colorpalettepicker.js')   }}"></script>
    <script src="{{ asset('js/font-awesome-pro.min.js') }}"></script>

    {{-- CHARTS --}}
    

    {{-- <script src="{{ asset('js/jquery-1.11.0.min.js') }}"></script> --}}
    <script src="{{ asset('js/jquery-migrate-1.2.1.min.js') }}"></script>
    <script src="{{ asset('js/slick.js') }}"></script>

    <script src="{{ asset('js/bootstrap.min.js')        }}"></script>
    
    <!-- Meus Scripts -->
    <script src="{{ asset('js/my-functions.js')        }}"></script>
    <script src="{{ asset('js/my-functions-cavani.js') }}"></script>
    <script src="{{ asset('js/my-script.js')           }}"></script>
    <script src="{{ asset('js/my-script-ready.js')     }}"></script>

</body>
</html>
