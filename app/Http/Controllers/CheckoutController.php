<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Preco;
use App\Pedido;
use App\Pessoa;
use App\TabelaPrecos;
use App\Modalidades_por_plano;
use App\Procedimento;
use App\Comissao_exclusiva;
use App\OldModalidades;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index() {
        $empresas = DB::table('empresa')
                    ->selectRaw("id, CONCAT(descr, ' - ', cidade) AS descr")
                    ->get();

        return view("checkout", compact('empresas'));
    }

    public function imprimir($id_emp, $dinicial, $dfinal, $membro){
        $filtroi = new \DateTime($dinicial);
        $filtrof = new \DateTime($dfinal);

        $data_inicial = $filtroi->format('d/m/Y');
        $data_final   = $filtrof->format('d/m/Y');

        

        $checkouts = DB::select(
            DB::raw("
                select
                    encaminhamento.id,
                    procedimento.descr,
                    DATE_FORMAT(agenda.data, '%d/%m/%Y') AS data,
                    SUBSTRING(agenda.hora, 1, 5) AS hora,
                    encaminhamento.sucess,
                    agenda.id_pedido
                from
                    encaminhamento
                    left join agenda on agenda.id = encaminhamento.id_agendamento
                    left join procedimento on procedimento.id = agenda.id_modalidade
                where
                    encaminhamento.id_emp = ". $id_emp ." AND
                    encaminhamento.created_at >= '". $filtroi->format('Y-m-d'). " 00:00:00' AND
                    encaminhamento.created_at <= '". $filtrof->format('Y-m-d'). " 23:59:59' AND
                    encaminhamento.id_profissional = ". $membro)
        );


        foreach($checkouts AS $checkout) {
            $checkout->habilitacao = DB::select(
                DB::raw("select * from encaminhamento_detalhes where id_encaminhamento = " . $checkout->id . " AND tipo = 'habilitacao'")
            );
            $checkout->reabilitacao = DB::select(
                DB::raw("select * from encaminhamento_detalhes where id_encaminhamento = " . $checkout->id . " AND tipo = 'reabilitacao'")
            );
            if ($checkout->sucess == 1) {
                $checkout->sucesso = DB::select(
                    DB::raw("
                        select
                            pedido.id,
                            pedido.total,
                            GROUP_CONCAT(tabela_precos.descr) as descr
                        from
                            pedido
                            left join pedido_planos on pedido_planos.id_pedido = pedido.id
                            left join tabela_precos on tabela_precos.id        = pedido_planos.id_plano
                        where
                            pedido.id = ". $checkout->id_pedido ."
                        group by
                            pedido.id,
                            pedido.total
                    ")
                );
            }
        }
        // return $checkouts;
        
        return view('.reports.impresso_checkout', compact('data_inicial', 'data_final', 'id_emp' ,'checkouts', 'filtroi', 'filtrof', 'idempresa'));
    }
}
