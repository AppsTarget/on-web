<?php

namespace App\Http\Controllers;

use DB;
use auth;
use App\Encaminhamento;
use App\EncaminhamentoDetalhes;
use App\Agenda;
use Illuminate\Http\Request;

class EncaminhamentoController_old extends Controller
{
    public function salvarEncaminhamento(Request $request)
    {
            
            $encaminhamento = new Encaminhamento;
            
            $encaminhamento->id_emp = getEmpresa();
            $encaminhamento->id_agendamento = $request->id_agendamento;
            $encaminhamento->id_profissional = Auth::user()->id_profissional;
            $encaminhamento->id_paciente = $request->id_paciente;
            $encaminhamento->id_evolucao = $request->id_evolucao;
            $encaminhamento->sucess = 0;
            $encaminhamento->save();

            if ($request->id_agendamento) {
                $agenda = Agenda::find($request->id_agendamento);
                $agenda->id_encaminhamento = $encaminhamento->id;
                $agenda->save();
            }

            for($i=0; $i < sizeof($request->valor1); $i++){
                $encaminhamento_detalhes = new EncaminhamentoDetalhes;
                $encaminhamento_detalhes->tipo = $request-> tipo[$i];
                $encaminhamento_detalhes->valor1= $request-> valor1[$i];
                $encaminhamento_detalhes->valor2= $request-> valor2[$i];
                $encaminhamento_detalhes->valor3= $request-> valor3[$i];
                $encaminhamento_detalhes->id_encaminhamento = $encaminhamento->id;
                $encaminhamento_detalhes->save();
            }
    }

    public function listarTabelaEncaminhamento($id_agendamentos)
    {   
        return Agenda::find($id_agendamentos)->id_encaminhamento;
    }

    public function mostrarTabelaEncaminhamento($id_encaminhamento) {
        
        $encaminhamento = DB::table('encaminhamento_detalhes')
                ->select('tipo',
                        'valor1', 
                        'valor2', 
                        'valor3')
                ->where('id_encaminhamento', $id_encaminhamento)
                ->orderBy('encaminhamento_detalhes.tipo')
                ->get();
        return $encaminhamento;
    }

    public function mostrarTabelaEncaminhamentoPorPessoa($id_paciente) {
        return DB::table(DB::raw('encaminhamento_detalhes'))
                   ->select("encaminhamento_detalhes.id", "encaminhamento_detalhes.valor1", "encaminhamento_detalhes.updated_at")
                   ->whereRaw("encaminhamento_detalhes.id_encaminhamento in (select encaminhamento.id from encaminhamento where encaminhamento.id_paciente = ".$id_paciente.")")
                   ->get();
    }

    public function salvarSucess(Request $request){
                $id_agendamentos = $request->id_agendamento;
                $sucess = DB::table('encaminhamento')
                ->select('id',
                        'sucess')
                ->where('encaminhamento.id_agendamento', $id_agendamentos)
                ->first();

                
                if($sucess){
                    $encaminhamento = Encaminhamento::find($sucess->id);
                    $encaminhamento->sucess = 1;
                    $encaminhamento->save();
                }
    }
}
