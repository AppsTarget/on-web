<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (Auth::user()->sa) return redirect('administrador');
        else                  return redirect('agenda');
    }

    public function administrador()
    {
        return view('administrador');
    }

    public function parametros()
    {
        return view('parametros');
    }

    private function autocompleteAux($inicio, Request $request, $filtro) {
        $sql = array("(", "(");
        $fltr = explode(" ", $request->search);
        for ($i = 0; $i < count($sql); $i++) {
            for ($j = 0; $j < count($fltr); $j++) {
                if ($sql[$i] != "(") $sql[$i] .= " and ";
                $sql[$i] .= "pessoa.";
                $sql[$i] .= $i == 0 ? "nome_fantasia" : "nome_reduzido";
                $sql[$i] .= " like '" . $inicio . $fltr[$j] . "%'";
            }
            $sql[$i] .= ")";
        }
        switch($filtro) {
            case 1:
                return DB::table($request->table)
                        ->select(
                            'id',
                            DB::raw(
                                "CASE" .
                                "   WHEN colaborador <> 'N' THEN (CASE WHEN (nome_reduzido IS NOT NULL AND nome_reduzido <> '') THEN nome_fantasia ELSE nome_fantasia END)" .
                                "   ELSE nome_fantasia " .
                                "END AS " . $request->column
                            )
                        )
                        ->where('lixeira', false)
                        ->whereRaw("(
                            ".$sql[0]." OR ".$sql[1]."
                        )")
                        ->where(function($sql) use($request) {
                            if ($request->filter_col != '' && $request->filter != '') $sql->where($request->filter_col, $request->filter);
                        })
                        /*->where(function($sql) use($request) {
                            if ($request->filter_col != '' && $request->filter != '') {
                                if ($request->filter == 'R'){
                                    $sql->where($request->filter_col, 'R')
                                        ->orWhere($request->filter_col, 'A');
                                }
                                else if ($request->filter == 'P'){
                                    $sql->where($request->filter_col, 'P')
                                        ->orWhere($request->filter_col, 'A');
                                };
                            }
                        })*/
                        ->orderby($request->column)
                        ->take(6)
                        ->get();
                break;
            case 2:
                $query = "
                    SELECT pessoa.id, pessoa.nome_fantasia, aux.qtd, aux.qtd2 FROM pessoa    
                        JOIN pedido on pedido.id_paciente = pessoa.id
                        JOIN (
                            SELECT pedido_planos.id_pedido,
                                SUM(tabela_precos.max_atv_semana) AS qtd,
                                (SUM(pedido_planos.qtde * pedido_planos.qtd_total) - agendamentos.TOTAGEN) AS qtd2
                            FROM pedido_planos
                                LEFT JOIN tabela_precos on tabela_precos.id = pedido_planos.id_plano
                                LEFT JOIN (SELECT agenda.id_pedido, agenda.id_tabela_preco, COUNT(*) as TOTAGEN
                                           FROM agenda
                                           WHERE agenda.lixeira = 0 AND (agenda.status = 'F' or agenda.status = 'A')
                                           GROUP BY agenda.id_pedido, agenda.id_tabela_preco) AS agendamentos
                                        ON agendamentos.id_pedido = pedido_planos.id_pedido
                                        AND agendamentos.id_tabela_preco = pedido_planos.id_plano
                            GROUP BY
                                pedido_planos.id_pedido, agendamentos.TOTAGEN
                        ) as aux on aux.id_pedido = pedido.id
                    WHERE (aux.qtd > 0 and aux.qtd2 > 0 and pedido.data_validade >= '" . date('Y-m-d') . "' and (
                        ".$sql[0]." OR ".$sql[1]."
                    ))
                    GROUP BY pessoa.id, pessoa.nome_fantasia, aux.qtd, aux.qtd2
                    ORDER BY ".$request->column."
                    LIMIT 6
                ";
                return DB::select(DB::raw($query));
                break;
            case 3:
                return DB::table('pessoa')
                        ->where(function($sql) {
                                $sql->where('colaborador', 'P')
                                    ->orWhere('colaborador', 'A');
                        })
                        ->where('lixeira', '<>', 1)
                        ->where('id', '>', 1)
                        ->whereRaw("(
                            ".$sql[0]." OR ".$sql[1]."
                        )")
                        ->orderBy('pessoa.nome_fantasia')
                        ->get();
        }
    }

    public function autocomplete(Request $request) {
        try {
            if(strpos($request->table, "pessoa") === 0) {
                $indice = array("pessoa", "pessoa(filtro)", "pessoa(membro)");
                for ($i = 0; $i < sizeof($indice); $i++) {
                    if ($request->table == $indice[$i]) {
                        $retorno = $this->autoCompleteAux("", $request, $i + 1);
                        if (sizeof($retorno) < 1) $retorno = $this->autoCompleteAux("%", $request, $i + 1);
                        return json_encode($retorno);
                    }
                }
            }
            else if($request->table == 'cid'){
                return json_encode(
                    DB::table($request->table)
                    ->select('id', DB::raw('(CONCAT(codigo, " - ", nome)) as nome') )
                    ->where('codigo', 'LIKE', $request->search . '%')
                    ->orwhere('nome', 'LIKE', $request->search . '%')
                    ->take(6)
                    ->get()
                );
            } 
            else if ($request->table == 'procedimento') {
                return json_encode(
                    DB::table($request->table)
                    ->select(
                        'id',
                        DB::raw(
                            " CASE" .
                            "    WHEN procedimento.cod_tuss IS NOT NULL AND procedimento.cod_tuss <> '' THEN CONCAT(" . $request->column . ",' (',cod_tuss,')')" .
                            "    ELSE " . $request->column .
                            " END AS " . $request->column
                        )
                    )
                    // ->where('id_emp', getEmpresa())
                    ->where($request->column, "LIKE", '%'. $request->search . '%')
                    // ->where(function($sql) use($request) {
                    //     if ($request->filter_col != '' && $request->filter != '') {
                    //         $sql->where($request->filter_col, $request->filter);
                    //     }
                    // })
                    ->where(function($sql){
                        $sql->where('procedimento.oculto', 0)
                            ->orWhere('procedimento.oculto', null)
                            ->orWhere('procedimento.oculto', 'null');
                    })
                    ->groupby('id', $request->column)
                    ->orderby($request->column)
                    ->take(6)
                    ->get()
                );
            } 
            else if ($request->table == 'bancos') {
                return json_encode(
                    DB::table('bancos')
                    ->select('bancos.id', 'bancos.title')
                    ->where('bancos.title', 'LIKE', $request->search . '%')
                    ->take(6)
                    ->get()
                );
            }
            else if ($request->table == 'procedimento_preco') {
                if (getEmpresaObj()->mod_trava_proc_tabela_preco)  {
                    return json_encode(
                        DB::table('procedimento')
                        ->select(
                            'procedimento.id',
                            DB::raw(
                                " CASE" .
                                "    WHEN procedimento.cod_tuss IS NOT NULL AND procedimento.cod_tuss <> '' THEN CONCAT(procedimento." . $request->column . ",' (',cod_tuss,')')" .
                                "    ELSE procedimento." . $request->column .
                                " END AS " . $request->column
                            )
                        )
                        ->join('preco', 'preco.id_procedimento', 'procedimento.id')
                        ->join('tabela_precos', 'tabela_precos.id', 'preco.id_tabela_preco')
                        ->join('convenio', 'convenio.id_tabela_preco', 'tabela_precos.id')
                        ->where('convenio.lixeira', false)
                        ->where("procedimento." . $request->column, "LIKE", $request->search . '%')
                        ->where(function($sql) use($request) {
                            if ($request->filter_col != '' && $request->filter != '') {
                                $sql->where(DB::raw($request->filter_col), $request->filter);
                            }
                        })
                        ->where('procedimento.id_emp', getEmpresa())
                        ->groupby('id', "procedimento." . $request->column)
                        ->orderby("procedimento." . $request->column)
                        ->take(6)
                        ->get()
                    );
                } else {
                    return json_encode(
                        DB::table('procedimento')
                        ->select(
                            'id',
                            DB::raw(
                                " CASE" .
                                "    WHEN procedimento.cod_tuss IS NOT NULL AND procedimento.cod_tuss <> '' THEN CONCAT(" . $request->column . ",' (',cod_tuss,')')" .
                                "    ELSE " . $request->column .
                                " END AS " . $request->column
                            )
                        )
                        ->where('id_emp', getEmpresa())
                        ->where($request->column, "LIKE", $request->search . '%')
                        ->where(function($sql) use($request) {
                            if ($request->filter_col != '' && $request->filter != '') {
                                $sql->where($request->filter_col, $request->filter);
                            }
                        })
                        ->groupby('id', $request->column)
                        ->orderby($request->column)
                        ->take(6)
                        ->get()
                    );
                }
            } else if (in_array($request->filter_col, ['medicamento'])) {
                return json_encode(
                    DB::table($request->table)
                    ->select(
                        'id',
                        $request->column
                    )
                    ->where('id_emp', getEmpresa())
                    ->where('lixeira', false)
                    ->where($request->column, "LIKE", $request->search . '%')
                    ->where(function($sql) use($request) {
                        if ($request->filter_col != '' && $request->filter != '') {
                            $sql->where($request->filter_col, $request->filter);
                        }
                    })
                    ->groupby('id', $request->column)
                    ->orderby($request->column)
                    ->take(6)
                    ->get()
                );
            } else if ($request->filter_col == "enc_esp" && $request->filter != '') {
                return json_encode(DB::select(DB::raw("
                    SELECT
                        especialidade.id,
                        especialidade.descr
                    
                    FROM especialidade

                    JOIN enc2_encaminhantes_especialidade
                        ON enc2_encaminhantes_especialidade.id_especialidade = especialidade.id
                    
                    WHERE enc2_encaminhantes_especialidade.id_encaminhante = ".$request->filter
                )));
            } else {
                return json_encode(
                    DB::table($request->table)
                    ->select(
                        'id',
                        $request->column
                    )
                    ->where($request->column, "LIKE", $request->search . '%')
                    ->where(function($sql) use($request) {
                        if ($request->filter_col != '' && $request->filter != '') {
                            $sql->where($request->filter_col, $request->filter);
                        }
                    })
                    ->where(function($sql) use($request) {
                        if ($request->table != 'enc2_encaminhantes') {
                            $sql->where('id_emp', getEmpresa());
                        } else {
                            $sql->where('lixeira', 0);
                        }
                    })
                    ->groupby('id', $request->column)
                    ->orderby($request->column)
                    ->take(6)
                    ->get()
                );
            }
        } catch(\Exception $e) {
            return $e->getMessage();
        }
    }
    public function autocomplete_agenda(Request $request){
        $data = new \StdClass;
        $data->profissionais = DB::table($request->table)
                                ->select(
                                    'pessoa.id',
                                    DB::raw(
                                        "nome_fantasia AS " . $request->column
                                    ),
                                    DB::raw("CASE WHEN cliente = 'S' THEN '' ELSE data_nasc END AS data_nasc"),
                                    DB::raw(
                                        '(SELECT max(agenda.data)' .
                                        '   FROM agenda' .
                                        '   LEFT OUTER JOIN agenda_status AS status ON status.id = agenda.id_status' .
                                        '  WHERE agenda.id_paciente = pessoa.id' .
                                        '    AND status.libera_horario = 0' .
                                        '  LIMIT 1)' .
                                        'AS agenda'
                                    ),
                                    DB::raw(
                                        "(SELECT convenio.descr" .
                                        "   FROM convenio_pessoa" .
                                        "   LEFT OUTER JOIN convenio ON convenio.id = convenio_pessoa.id_convenio" .
                                        "  WHERE convenio_pessoa.id_paciente = pessoa.id" .
                                        "    AND convenio.quem_paga = 'E'" .
                                        "  ORDER BY convenio_pessoa.created_at DESC" .
                                        "  LIMIT 1) AS convenio"
                                    )
                                )
                                ->leftjoin('empresas_profissional', 'empresas_profissional.id_profissional', 'pessoa.id')
                                ->where('empresas_profissional.id_emp', getEmpresa())
                                ->where('lixeira', false)
                                ->where(function($subsql) use($request) {
                                    $subsql->where('nome_fantasia', 'LIKE', $request->search . '%')
                                        ->orWhere('nome_reduzido', 'LIKE', $request->search . '%');
                                })
                                ->where(function($sql){
                                    $sql->where('colaborador', 'P')
                                     ->orWhere('colaborador', 'A');
                                })
                                ->orderby($request->column)
                                ->get();
        $data->agendamentos = array();
        foreach($data->profissionais AS $profissional){
            $grades = DB::table('grade')
                      ->where('id_profissional', $profissional->id)
                      ->where('dia_semana', date('w') + 1)
                      ->where('ativo', true)
                      ->where('lixeira', 'N')
                      ->get();
            $total_agendamentos = 0;
            // foreach($grades as $grade) {
            //     $total_agendamentos  += ((intval(substr($grade->hora_final, 0, 2))) - (intval(substr($grade->hora_inicial, 0, 2)))) / ($grade->min_intervalo/60);
            // }
            $agendamentos = DB::table('agenda')
                                ->selectRaw('COUNT(agenda.id) as total')
                                ->where('id_profissional', $profissional->id)
                                ->where('data', date('Y-m-d'))
                                ->where('status', 'A')
                                ->where('lixeira', false)
                                ->value('total');
            if($total_agendamentos == $agendamentos){
                array_push($data->agendamentos, 5);
            }
            else if ($agendamentos >= $total_agendamentos * 0.75){
                array_push($data->agendamentos, 4);
            }
            else if ($agendamentos >= $total_agendamentos * 0.5){
                array_push($data->agendamentos, 3);
            }
            else if ($agendamentos >= $total_agendamentos * 0.25){
                array_push($data->agendamentos, 2);
            }
            else if ($agendamentos >= 1){
                array_push($data->agendamentos, 1);
            }
            else array_push($data->agendamentos, 0);
        }
        return json_encode($data);
    }

    public function csv(Request $request) {
        $conteudo = $request->conteudo;
        $nome = $request->titulo.'-'.date('YmdHis').'.csv';
        $caminhoArquivo = public_path('/arqcsv/'.$nome);
        file_put_contents($caminhoArquivo, $conteudo);
        if (file_exists($caminhoArquivo)) return $nome;            
        else return "false";
    }
}
