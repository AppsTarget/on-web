<?php

namespace App\Http\Controllers;

use DB;
use App\AgendaStatus;
use Illuminate\Http\Request;

class AgendaStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function salvar(Request $request) {
        try {
            if (!$request->id) $agenda_status = new AgendaStatus;
            else               $agenda_status = AgendaStatus::find($request->id);

            $agenda_status->id_emp = getEmpresa();
            $agenda_status->descr = $request->descr;
            $agenda_status->cor = $request->cor;
            $agenda_status->cor_letra = $request->cor_letra;
            $agenda_status->permite_editar      = ($request->permite_editar      == 'on');
            $agenda_status->permite_fila_espera = ($request->permite_fila_espera == 'on');
            $agenda_status->permite_reagendar   = ($request->permite_reagendar   == 'on');
            $agenda_status->caso_reagendar      = ($request->caso_reagendar      == 'on');
            $agenda_status->caso_confirmar      = ($request->caso_confirmar      == 'on');
            $agenda_status->caso_cancelar       = ($request->caso_cancelar       == 'on');
            $agenda_status->save();

            return redirect('/agenda-status');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function deletar(Request $request) {
        try {
            $agenda_status = AgendaStatus::findOrFail($request->id);
            $agenda_status->delete();

            return redirect('/agenda-status');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar() {
        try {
            $agenda_status = DB::table('agenda_status')
                            ->where('id_emp', getEmpresa())
                            ->get();
            
            return view('agenda_status', compact('agenda_status'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listar_json() {
        try {
            return json_encode(
                DB::table('agenda_status')
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
                DB::table('agenda_status')
                ->where('id_emp', getEmpresa())
                ->where('id', $id)
                ->first()
            );
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}