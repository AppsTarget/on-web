@extends('layouts.app')

@section('content')
    @include('components.main-toolbar')
    <ul id="agendamento-context-menu">
        <li data-function="editar_agendamento">
            <i class="my-icon fas fa-pen-square"></i>
            <span>Editar</span>
        </li>

        <li data-function="deletar_agendamento">
            <i class="my-icon far fa-trash-alt"></i>
            <span>Deletar</span>
        </li>
        <li data-function="adicionar_agendamento">
            <i class="my-icon far fa-plus"></i>
            <span>Adicionar agendamento</span>
        </li>
    </ul>

    <div class="container-fluid h-100 px-3 py-4">
        <div class="row">
            <h3 class="col header-color mb-3">
                Plano de Contas
            </h3>
        </div>
        <div role="main" class="main">
            <div class="treeview">
                <ul class="tree" id="tree">
                    <li>
                        <a href="#flora">
                            Adicionar
                            <img style="width: 20px;height: 20px;margin: 0px 0px 0px 5px;display: none" src="http://vps.targetclient.com.br/saude-beta/img/adicionar.png">
                        </a>
                    <li>
                        {{-- <ul class="children">
                            <li><a href="#ervas">Ervas</a>
                                <ul class="children">
                                    <li><a href="#ervadoce">Erva Doce</a></li>
                                    <li><a href="#urtiga">Urtiga</a></li>
                                    <li><a href="#boldo">Boldo</a>
                                        <ul class="children">
                                            <li><a href="#africano">Africano</a></li>
                                            <li><a href="#indigena">Indígena</a></li>
                                            <li><a href="#miudo">Miúdo</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                            <li><a href="#flores">Flores</a>
                                <ul class="children">
                                    <li><a href="#margarida">Margarida</a></li>
                                    <li><a href="#cravo">Cravo</a></li>
                                    <li><a href="#rosa">Rosa</a></li>
                                </ul>
                            </li>
                        </ul> --}}
                    {{-- </li> --}}
                    {{-- <li><a href="#fauna">Fauna</a>
                        <ul class="children">
                            <li><a href="#mamiferos">Mamíferos</a>
                                <ul class="children">
                                    <li><a href="#onca">Onça pintada</a></li>
                                    <li><a href="#mico">Mico-leão-dourado</a></li>
                                    <li><a href="#capivara">Capivara</a></li>
                                </ul>
                            </li>
                            <li><a href="#repteis">Répteis</a>
                                <ul class="children">
                                    <li><a href="#lagarto">Lagarto</a>
                                        <ul class="children">
                                            <li><a href="#calango">Calango-verde</a></li>
                                            <li><a href="#teiu">Teiú</a></li>
                                            <li><a href="#teju">Lagarto-teju</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="#cobra">Cobra</a></li>
                                    <li><a href="#tartaruga">Tartaruga</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li> --}}
                </ul>
            </div>
        </div>
    </div>



    <style>
        header,
        nav,
        footer {
            display: block;
        }

        #siteName {
            border: none;
            margin: 0;
            padding: 0;
        }

        ul {
            padding-left: 20px;
        }

        li {
            list-style-type: none;
            outline: none;
            padding: 5px;
        }

        li ul {
            margin-top: 5px;
        }

        .visually-hidden {
            position: absolute;
            left: -999em;
        }

        .tree {
            padding: 10px;
            margin-bottom: 2em;
            border: 1px solid #999;
        }

        .hasChildren {
            position: relative;
        }

        .tree li ul {
            display: none;
        }

        .tree a {
            padding: 2px 5px 2px 0;
            /* position: relative;
            z-index: 10; */
        }

        /* .tree a:focus {
            outline: 2px dotted #f00;
        } */

        .tree li li a {
            background-image: url("img/raquo-blue.png");
            background-repeat: no-repeat;
            background-position: 8px 0.5em;
            padding-left: 40px;
        }

        .tree .hasChildren a {
            background-image: none;
            padding-left: 30px;
        }

        .tree li .noChildren a {
            background-image: url("img/raquo-blue.png");
            padding-left: 25px;
        }

        .toggle {
            background-position: left top;
            background-repeat: no-repeat;
            cursor: pointer;
            height: 14px;
            width: 14px;
            position: absolute;
            left: 10px;
            top: 0.4em;
        }

        .tree .expanded {
            background-image: url(http://vps.targetclient.com.br/saude-beta/img/botao-de-subtracao.png);
            background-size: 19px;
            width: 19px;
            height: 19px;
            opacity: .8;
            margin-left: -3px;
        }

        .tree .collapsed {
            background-image: url(http://vps.targetclient.com.br/saude-beta/img/botao-adicionar.png);
            background-size: 22px;
            width: 22px;
            height: 22px;
            opacity: .8;
            margin-left: -5px;
        }

        /* .tree .expanded.hover {
            background-image: url("img/minus-blue-hover.png");
        }

        .tree .collapsed.hover {
            background-image: url("img/plus-blue-hover.png");
        } */

        #footer {
            clear: both;
        }
    </style>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            

            atualizarArvore()

        });





        function atualizarArvore() {
            $.get('/saude-beta/plano-de-contas/montar-arvore', {},
            function(data, status){
                console.log(data + ' | ' + status)
                data = $.parseJSON(data)
                $('#tree').empty()
                if (data.inicial.length == 0) {
                    html =  ' <li> '
                    html += '     <a href="#flora" data-name=""> '
                    html += '         Adicionar '
                    html += '         <img onclick="abrirModalPlanoDeContas($(this))" style="width: 20px;height: 20px;margin: 0px 0px 0px 5px;display: none" src="http://vps.targetclient.com.br/saude-beta/img/adicionar.png"> '
                    html += '         <img onclick="editarPlanoDeContas($(this))" style="width: 20px;height: 20px;margin: 0px 0px 0px 5px;display: none" src="http://vps.targetclient.com.br/saude-beta/img/lapis.png"> '
                    html += '         <img onclick="excluirPlanoDeContas($(this))" style="width: 20px;height: 20px;margin: 0px 0px 0px 5px;display: none" src="http://vps.targetclient.com.br/saude-beta/img/lixeira.png"> '
                    html += '     </a> '
                    html += ' <ul class="children"></ul>'
                    html += ' </li> '
                    
                    $('#tree').append(html)
                }
                else {
                    data.inicial.forEach(historico => {
                        html =  ' <li id="'+ historico.id +'"> '
                        html += '     <a href="#flora" data-name="'+ historico.descr +'"> '
                        html += historico.descr
                        html += '         <img onclick="abrirModalPlanoDeContas($(this))" style="width: 20px;height: 20px;margin: 0px 0px 0px 5px;display: none" src="http://vps.targetclient.com.br/saude-beta/img/adicionar.png"> '
                        html += '         <img onclick="editarPlanoDeContas($(this))" style="width: 20px;height: 20px;margin: 0px 0px 0px 5px;display: none" src="http://vps.targetclient.com.br/saude-beta/img/lapis.png"> '
                        html += '         <img onclick="excluirPlanoDeContas($(this))" style="width: 20px;height: 20px;margin: 0px 0px 0px 5px;display: none" src="http://vps.targetclient.com.br/saude-beta/img/lixeira.png"> '
                        html += '     </a> '
                        html += ' <ul class="children"></ul>'
                        html += ' </li> '
                        $('#tree').append(html)
                    })

                    data.final.forEach(historico => {
                        html =  ' <li id="'+ historico.id +'"> '
                        html += '     <a href="#flora" data-name="'+ historico.descr +'"> '
                        html += historico.descr
                        html += '         <img onclick="abrirModalPlanoDeContas($(this))" style="width: 20px;height: 20px;margin: 0px 0px 0px 5px;display: none" src="http://vps.targetclient.com.br/saude-beta/img/adicionar.png"> '
                        html += '         <img onclick="editarPlanoDeContas($(this))" style="width: 20px;height: 20px;margin: 0px 0px 0px 5px;display: none" src="http://vps.targetclient.com.br/saude-beta/img/lapis.png"> '
                        html += '         <img onclick="excluirPlanoDeContas($(this))" style="width: 20px;height: 20px;margin: 0px 0px 0px 5px;display: none" src="http://vps.targetclient.com.br/saude-beta/img/lixeira.png"> '
                        html += '     </a> '
                        html += '     <ul class="children"></ul>'
                        html += ' </li> '
                        console.log($('#'+historico.id_pai + ' .children')[0])
                        $($('#'+historico.id_pai + ' .children')[0]).append(html)
                    })
                }





                $('.tree a').hover(
                    function () {
                        console.log($(this).find('img'))
                        $(this).find('img').show()
                    },
                    function () {
                        console.log($(this).find('img'))
                        $(this).find('img').hide()
                    }
                )
                ativarArvore()
            })
        }

        function ativarArvore(){
            if ($('.treeview').length) {
                //atribui a primeira lista não ordenada que estiver dentro do div 
                //com a classe treeview, pois é a árvore
                var $tree = $('.treeview ul:first');
                $tree.attr({
                    'role': 'tree'
                });
                //variáveis que mantém o controle sobre os nós expandidos ou contraídos da árvore
                var $allNodes = $('li:visible', $tree); //lista de nós visíveis da árvore
                var lastNodeIdx = $allNodes.length - 1; //o índice do último nó visível da lista
                var $lastNode = $allNodes.eq(lastNodeIdx); //último nó visível da lista

                //expande ou contrai um grupo de nós
                function toggleGroup($node) {
                    $toggle = $('> div', $node);
                    $childList = $('> ul', $node);

                    //expande ou contraí os nós do grupo com efeito visual slide
                    $childList.slideToggle('fast', function() {
                        //atualiza as variáveis de controle sobre os nós expandidos ou contraídos
                        $allNodes = $('li:visible', $tree);
                        lastNodeIdx = $allNodes.length - 1;
                        $lastNode = $allNodes.eq(lastNodeIdx);
                    });
                    //ajuste de estilo e propriedades wai-aria da contração ou expansão do grupo
                    if ($toggle.hasClass('collapsed')) {
                        //ajuste de estilo visual para expandido
                        $toggle.removeClass('collapsed').addClass('expanded');
                        //indica que um elemento está expandido (semanticamente e não visualmente)
                        $('> a', $node).attr({
                            'aria-expanded': 'true',
                            'tabindex': '0'
                        }).focus();
                    } else {
                        //ajuste de estilo visual para contraído
                        $toggle.removeClass('expanded').addClass('collapsed');
                        //indica que um elemento está contraído (semanticamente e não visualmente)
                        $('> a', $node).attr({
                            'aria-expanded': 'false',
                            'tabindex': '0'
                        }).focus();
                    }
                }

                //obtém o próximo nó da árvore
                function nextNodeLink($el, dir) {
                    var thisNodeIdx = $allNodes.index($el.parent());
                    if (dir == 'up' || dir == 'parent') {
                        var endNodeIdx = 0;
                        var operand = -1;
                    } else {
                        var endNodeIdx = lastNodeIdx;
                        var operand = 1;
                    }
                    if (thisNodeIdx == endNodeIdx) { //se o nós atual for o último
                        return false; //não faz nada
                    }

                    if (dir == 'parent') {
                        var parentNodeIdx = $allNodes.index($el.parent().parent().parent());
                        var $nextEl = $('> a', $allNodes.eq(parentNodeIdx));
                    } else {
                        var $nextEl = $('> a', $allNodes.eq(thisNodeIdx + operand));
                    }

                    $el.attr('tabindex', '-1');
                    $nextEl.attr('tabindex', '0').focus();

                }

                //para cada link que houver na árvore
                $('li > a', $tree).each(function() {
                    var $el = $(this);
                    var $node = $el.parent();
                    $el.attr({
                        'role': 'treeitem',
                        'aria-selected': 'false',
                        'tabindex': "-1",
                        'aria-label': $el.text()
                    });
                    $node.attr('role', 'presentation');
                    //se o nó tem nós filhos
                    if ($node.has('ul > li').length) {
                        $node.addClass('hasChildren');
                        $childList = $('ul', $node);
                        $childList.attr({
                            'role': 'group'
                        }).hide();
                        //adiciona o elemento para expandir/contrair e define 
                        //aria-expanded no link
                        $('<div aria-hidden="true" class="toggle collapsed">').insertBefore($el);
                        $el.attr('aria-expanded', 'false');
                    } else { //caso o nó não tenha nós filhos
                        $node.addClass('noChildren');
                    }
                    //define os eventos de teclado
                    $el.on('keydown', function(e) {
                            if (!(e.shiftKey || e.ctrlKey || e.altKey || e.metaKey)) {
                                switch (e.which) {
                                    case 38: //cima
                                        e.preventDefault();
                                        nextNodeLink($(this), 'up');
                                        break;
                                    case 40: //baixo
                                        e.preventDefault();
                                        nextNodeLink($(this), 'down');
                                        break;
                                    case 37: //esquerda
                                        if ($(this).attr('aria-expanded') == 'false' ||
                                            $node.is('.noChildren')) {
                                            nextNodeLink($(this), 'parent');
                                        } else {
                                            toggleGroup($node);
                                        }
                                        break;
                                    case 39: //direita
                                        if ($(this).attr('aria-expanded') == 'true') {
                                            nextNodeLink($(this), 'down');
                                        } else {
                                            toggleGroup($node);
                                        }
                                        break;
                                }
                            }
                        }
                        //atualiza aria-selected quando o estado de foco de um nós muda
                    ).on('focus', function() {
                        $('[aria-selected="true"]', $tree).attr('aria-selected', 'false');
                        $(this).attr('aria-selected', 'true');
                    });
                });

                //define tabindex="0" no primeiro link da árvore
                $('> li:first > a', $tree).attr('tabindex', '0');

                //adiciona evento click e estilo hover sobre o elemento com classe toggle
                $('.toggle').on('click',
                    function() {
                        toggleGroup($(this).parent());
                    }
                ).hover(
                    function() {
                        $(this).toggleClass('hover');
                    }
                );

            }
        }

        function editarPlanoDeContas($obj) {
            $.get('/saude-beta/plano-de-contas/abrir-modal', {
                id: $obj.parent().parent().attr('id')
            },function(data, status){
                console.log(data + ' | ' + status)
                data = $.parseJSON(data)
                console.log(data.id_pai == 'null' || data.id_pai == null)
                if (data.id_pai == 'null' || data.id_pai == null) {
                    $('#adicionarPlanoDeContasModal #id-pai').val(0)
                    $('#adicionarPlanoDeContasModal #id').val(data.id)
                    $('#adicionarPlanoDeContasModal #descr-pai').val(data.descr).removeAttr('disabled')
                    $('#adicionarPlanoDeContasModal #descr-filho').val('').attr('disabled', true)
                }
                else {
                    $('#adicionarPlanoDeContasModal #id-pai').val(data.id_pai)
                    $('#adicionarPlanoDeContasModal #id').val(data.id)
                    $('#adicionarPlanoDeContasModal #descr-pai').val(data.descr_pai).attr('disabled', true)
                    $('#adicionarPlanoDeContasModal #descr-filho').val(data.descr).removeAttr('disabled')
                }                                              

                $('#adicionarPlanoDeContasModal').modal('show')
            })
        }
        function excluirPlanoDeContas($obj) {
            if (window.confirm("Você está prestes a excluir '"+ $obj.parent().data().name +"'\nTodos os filhos serão apagados\nDeseja prosseguir?")){
                $.get('/saude-beta/plano-de-contas/deletar', {
                    _token: $('meta[name=csrf-token]').attr('content'),
                    id: $obj.parent().parent().attr('id')
                },function(data, status) {
                    console.log(data + ' | ' + status)
                    if (data == 'true') alert('deletado')
                    else                alert('erro')
                    atualizarArvore()
                })
            }
        }

        function abrirModalPlanoDeContas($obj) {
            console.log($obj.parent().parent().attr('id'))
            $.get('/saude-beta/plano-de-contas/abrir-modal',{
                id: $obj.parent().parent().attr('id')
            }, function(data, status){
                console.log(data + ' | ' + status)
                if (data == 'false') {
                    $('#adicionarPlanoDeContasModal #id').val(0)
                    $('#adicionarPlanoDeContasModal #id-pai').val(0)
                    $('#adicionarPlanoDeContasModal #descr-pai').val('').removeAttr('disabled')
                    $('#adicionarPlanoDeContasModal #descr-filho').val('').attr('disabled', true)

                    $('#adicionarPlanoDeContasModal').modal('show')
                }
                else {
                    ShowConfirmationBox(
                        'Qual tipo de plano deseja adicionar',
                        '',
                        true, true, false,
                        function () {
                            data = $.parseJSON(data)
                            $('#adicionarPlanoDeContasModal #id').val(0)
                            $('#adicionarPlanoDeContasModal #id-pai').val(data.id)
                            $('#adicionarPlanoDeContasModal #descr-pai').val(data.descr).attr('disabled', true)
                            $('#adicionarPlanoDeContasModal #descr-filho').val('').removeAttr('disabled')

                            $('#adicionarPlanoDeContasModal').modal('show')
                        },
                        function () {
                            data = $.parseJSON(data)
                            console.log(data.id_pai == 'null' || data.id_pai == null)
                            if (data.id_pai == 'null' || data.id_pai == null) {
                                $('#adicionarPlanoDeContasModal #id-pai').val(0)
                                $('#adicionarPlanoDeContasModal #descr-pai').val('').removeAttr('disabled')
                                $('#adicionarPlanoDeContasModal #descr-filho').val('').attr('disabled', true)
                            }
                            else {
                                $('#adicionarPlanoDeContasModal #id-pai').val(data.id_pai)
                                $('#adicionarPlanoDeContasModal #descr-pai').val(data.descr_pai).attr('disabled', true)
                                $('#adicionarPlanoDeContasModal #descr-filho').val('').removeAttr('disabled')
                            }                                              

                            $('#adicionarPlanoDeContasModal').modal('show')
                         },
                        'Filho',
                        'Irmão'
                    );
                }
            })
        }

        function salvarPlanoDeContas() {
            $.get('/saude-beta/plano-de-contas/salvar', {
                _token:      $('meta[name=csrf-token]').attr('content'),
                id:          $('#adicionarPlanoDeContasModal #id').val(),
                id_pai:      $('#adicionarPlanoDeContasModal #id-pai').val(),
                descr_pai:   $('#adicionarPlanoDeContasModal #descr-pai').val(),
                descr_filho: $('#adicionarPlanoDeContasModal #descr-filho').val()
            },function(data,status) {
                console.log(data + ' | ' + status)
                if (data == 'true'){
                    atualizarArvore()
                    $('#adicionarPlanoDeContasModal').modal('hide')
                }
            })
        }









        function context_plano_contas($obj){
            $('#agendamento-context-menu').show()
        }
    </script>
    @if (App\Pessoa::find(Auth::user()->id_profissional)->administrador != 'S')
    <script>
        window.addEventListener("load", function() {
            location.href = "/saude-beta/"
        });
    </script>
    @endif
    @include('.modals.adicionar_plano_de_contas_modal')
@endsection
