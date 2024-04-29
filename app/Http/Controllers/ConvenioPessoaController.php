<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConvenioPessoaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function listar_convenios($id_paciente) {
        try {
            $convenios = DB::table('convenio_pessoa')
                        ->leftjoin('convenio', 'convenio.id', 'convenio_pessoa.id_convenio')
                        ->orWhere('convenio_pessoa.id_paciente', $id_paciente)
                        ->orWhere('convenio.quem_paga', 'E')
                        ->get();

            return $convenios;

        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
}
