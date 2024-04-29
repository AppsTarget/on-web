<?php

namespace App\Http\Controllers;

use DB;
use App\AgendaConfirmacao;
use Illuminate\Http\Request;

class AgendaConfirmacaoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            if (!$request->id) $agenda_confirm = new AgendaConfirmacao;
            else               $agenda_confirm = AgendaConfirmacao::find($request->id);

            $agenda_confirm->id_emp = getEmpresa();
            $agenda_confirm->descr = $request->descr;
            $agenda_confirm->save();

            return redirect('/agenda-confirmacao');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $agenda_confirm = AgendaConfirmacao::find($request->id);
            $agenda_confirm->lixeira = true;
            $agenda_confirm->save();

            return $agenda_confirm;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar() {
        try {
            $agenda_confirm = DB::table('agenda_confirmacao')
                            ->where('id_emp', getEmpresa())
                            ->where('lixeira', false)
                            ->get();
            
            return view('agenda_confirmacao', compact('agenda_confirm'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar_json() {
        try {
            return json_encode(
                DB::table('agenda_confirmacao')
                ->where('id_emp', getEmpresa())
                ->where('lixeira', false)
                ->get()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function mostrar($id) {
        try {
            return json_encode(
                DB::table('agenda_confirmacao')
                ->where('id_emp', getEmpresa())
                ->where('id', $id)
                ->first()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
