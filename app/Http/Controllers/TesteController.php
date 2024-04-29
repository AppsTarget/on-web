<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Preco;
use App\Pedido;
use App\Pessoa;
use App\TabelaPrecos;
use App\Modalidades_por_plano;
use App\Comissao_exclusiva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TesteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // public function testarsaida() {
    //     $agenda =  DB::table('old_mov_atividades')
    //         ->select('data', 'hora')
    //         ->get();
    //     $dias_semana = array();
    //     foreach($agenda as $agendamento) {
    //         array_push($dias_semana, DB::table('grade')
    //                                  ->where('grade.hora', ))
    //     }
    // }

    public function obterAnexos() {
        $ids = array();
        $consulta = DB::select(DB::raw("
            SELECT
                id,
                titulo
            FROM anexos
            WHERE titulo IS NOT NULL
              AND lixeira = 0
        "));
        foreach ($consulta as $linha) {
            if (!File::exists(realpath(public_path('anexos/'.$linha->titulo)))) array_push($ids, $linha->id);
        }
        $query = "UPDATE anexos SET lixeira = 1 WHERE id IN (".implode(",", $ids).")";
        
        return $query;
        //DB::statement($query);
    }
}