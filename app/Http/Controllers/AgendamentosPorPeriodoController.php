<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\OldMovAtividades;
use App\OldAtividades;
use App\OldFinanreceber;
use App\Pedido;
use App\PedidoFormaPag;
use App\PedidoParcela;
use App\PedidoServicos;
use App\PedidoPlanos;
use App\PedidoPessoas;
use App\Agenda;
use App\Pessoa;
use App\GradeHorario;
use App\Helpers;
use App\TabelaPrecos;
use Illuminate\Http\Request;

class AgendamentosPorPeriodoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        $membros = DB::table("pessoa")
            ->where(function($sql){
                $sql->where('colaborador', 'R')
                ->orwhere('colaborador', 'A');
            })
            ->where('lixeira', '<>', 1)
            ->where('id', '<>', 1)
            ->orderby('nome_fantasia')
            ->get();
        $empresa = DB::table('empresa')
            ->selectRaw("id, CONCAT(descr, ' - ', cidade) AS descr")
            ->get();
            return view("agendamentos_por_periodo", compact('membros', 'empresas'));
    }
}

