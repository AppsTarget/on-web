<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Salas;
use App\MovConta;
use App\TitulosReceber;
use Illuminate\Http\Request;

class AluguelController extends Controller {
    public function index() {
        $query = "
            SELECT
                salas.id,
                salas.descr,
                salas.valor,

                titulo.id_pessoa,
                titulo.ndoc,

                valor_antigo.val AS valor_real,

                DATE_FORMAT(titulo.d_vencimento, '%d') AS vencimento,

                geral.parcelas,

                CASE
                    WHEN titulo.id_sala IS NOT NULL THEN pessoa.nome_fantasia
                    ELSE ''
                END AS alugado_por
            
            FROM salas

            LEFT JOIN (
                SELECT
                    id,
                    id_sala,
                    id_pessoa,
                    ndoc,
                    d_vencimento

                FROM titulos_receber
                
                WHERE titulos_receber.lixeira = 'N'
                  AND titulos_receber.valor_total_pago < valor_total
                  AND (
                    DATE_FORMAT(titulos_receber.d_vencimento, '%Y') > ".date("Y")." OR (
                      DATE_FORMAT(titulos_receber.d_vencimento, '%Y') = ".date("Y")." AND
                      DATE_FORMAT(titulos_receber.d_vencimento, '%m') >= ".date("m")."
                    )
                  )
            ) AS titulo ON titulo.id_sala = salas.id

            LEFT JOIN (
                SELECT
                    id_sala,
                    ndoc,
                    COUNT(parcela) AS parcelas
                FROM titulos_receber
                WHERE titulos_receber.lixeira = 'N'
                GROUP BY
                    id_sala,
                    ndoc
            ) AS geral ON geral.id_sala = salas.id AND geral.ndoc = titulo.ndoc

            LEFT JOIN (
                SELECT
                    id,
                    valor_total AS val

                FROM titulos_receber

                JOIN (
                    SELECT
                        ndoc,
                        MAX(id) AS id_titulo

                    FROM titulos_receber

                    GROUP BY ndoc
                ) AS aux ON id_titulo = titulos_receber.id
            ) AS valor_antigo ON valor_antigo.id = titulo.id

            LEFT JOIN pessoa
                ON pessoa.id = titulo.id_pessoa
            
            WHERE salas.lixeira = 0
              AND salas.id_emp = ".getEmpresa()."
              AND (valor_antigo.val IS NOT NULL OR titulo.id IS NULL)
  
            GROUP BY
                salas.id,
                salas.descr,
                salas.valor,
                titulo.id_pessoa,
                titulo.ndoc,
                pessoa.id,
                vencimento,
                alugado_por,
                valor_real

            ORDER BY salas.descr
        ";
        $data = DB::select(DB::raw($query));
        $mostrar_nomes = 0;
        foreach ($data as $sala) {
            if (strlen($sala->alugado_por) > $mostrar_nomes) $mostrar_nomes = strlen($sala->alugado_por);
        }
        $documentos = DB::select(DB::raw("
            SELECT
                id_pessoa,
                GROUP_CONCAT(ndoc) AS numeros
            
            FROM titulos_receber
            
            WHERE origem = 'Aluguel'
              AND lixeira = 'N'
            
            GROUP BY id_pessoa
        "));
        $aux = DB::select(DB::raw("
            SELECT MAX(CONVERT(titulos_receber.ndoc, UNSIGNED INTEGER)) AS contagem
            FROM titulos_receber
            WHERE id_sala > 0
              AND lixeira = 'N'
        "));
        $qtd = ($aux[0]->contagem != null) ? $aux[0]->contagem : '0';
        return view("salas", compact("data", "mostrar_nomes", "documentos", "qtd"));
    }

    public function gravarSala(Request $request) {
        if ($request->id_sala > 0) {
            DB::statement("UPDATE salas
                SET
                    descr = '".strtoupper($request->descr)."',
                    valor = $request->valor
                
                WHERE id = ".$request->id_sala
            );
            if ($request->retroativo == "S") {
                DB::statement("UPDATE titulos_receber AS main
                    JOIN (
                        SELECT
                            id,
                            ndoc,
                            id_pessoa
                        
                        FROM titulos_receber
                        
                        WHERE id_sala = ".$request->id_sala."
                    ) AS aux ON aux.id = main.id

                    SET valor_total = ".$request->valor."

                    WHERE main.ndoc = aux.ndoc
                      AND main.id_pessoa = aux.id_pessoa
                      AND d_vencimento > CURDATE()
                      AND valor_total_pago = 0
                      AND lixeira = 'N'
                      AND obs <> 'Contrato cancelado'
                ");
            }
        } else {
            $data = new Salas;
            $data->id_emp = getEmpresa();
            $data->descr = strtoupper($request->descr);
            $data->valor = $request->valor;
            $data->save();
        }
        return redirect("/financeiro/alugueis");
    }

    public function excluirSala(Request $request) {
        DB::statement("update salas set lixeira = 1 where id = ".$request->id);
        return redirect("/financeiro/alugueis");
    }

    public function alugar(Request $request) {
        $id_forma_pag = 7;
        $forma_pag = DB::table('forma_pag')->where('id', $id_forma_pag)->value('descr');
        $id_profissional = Auth::user()->id_profissional;
        $profissional = DB::table('pessoa')->where('id', $id_profissional)->value('nome_fantasia');
        $valor = DB::table('salas')->where('id', $request->id_sala)->value('valor');

        $mes = idate('m');
        $ano = idate('Y');
        if (idate('d') > $request->venc) $mes++;
        if ($mes > 12) {
            $mes = 1;
            $ano++;
        }
        
        for ($i = 1; $i <= $request->parc; $i++) {
            $data = new TitulosReceber;
            $data->descr     = "Contrato de aluguel";
            $data->obs       = "Contrato de aluguel";
            $data->origem    = "Aluguel";
            $data->pago      = "N";
            $data->movimento = "N";
            
            $data->id_caixa            = 0;
            $data->id_conta            = 0;
            $data->id_pedido           = 0;
            $data->id_historico        = 0;
            $data->id_financeira       = 0;
            $data->id_forma_pag_pago   = 0;
            $data->id_pedido_forma_pag = 0;
            $data->taxa_financeira     = 0;
            $data->valor_total_pago    = 0;

            $data->ndoc      = $request->doc;
            $data->id_sala   = $request->id_sala;
            $data->id_pessoa = $request->membro;

            $data->parcela     = $i;
            $data->valor_total = $valor;

            $data->id_forma_pag     = $id_forma_pag;
            $data->forma_pag        = $forma_pag;
            $data->created_by       = $id_profissional;
            $data->created_by_descr = $profissional;
            $data->updated_by       = $id_profissional;
            $data->updated_by_descr = $profissional;

            $data->d_entrada = date('Y-m-d');
            $data->d_emissao = date('Y-m-d');
            $data->h_entrada = date('H:i:s');

            $data->d_vencimento = $ano.'-'.$mes.'-'.$request->venc;
            $mes++;
            if ($mes > 12) {
                $mes = 1;
                $ano++;
            }

            $data->save();
        }
        return redirect("/financeiro/alugueis");
    }

    public function dtVenc(Request $request) {
        DB::statement("
            UPDATE titulos_receber
            SET d_vencimento = CONCAT(DATE_FORMAT(d_vencimento, '%Y-%m'), '-".$request->venc."')
            WHERE ndoc = ".$request->doc."
              AND id_sala = ".$request->id_sala."
              AND id_pessoa = ".$request->membro
        );
        return redirect("/financeiro/alugueis");
    }

    public function encerrar(Request $request) {
        $where = "id_sala = ".$request->id_sala."
            AND valor_total_pago < valor_total
            AND d_vencimento > '".date("Y-m-d")."'
            AND lixeira = 'N'
            AND id_pessoa = ".$request->id_pessoa."
            AND ndoc = ".$request->ndoc;

        $id_conta = DB::table('contas_bancarias')->where('id_emp', getEmpresa())->value('id');
        
        $pago_por_descr = DB::table('pessoa')->where('id', Auth::user()->id_profissional)->value('nome_fantasia');

        $titulos = DB::select(DB::raw("SELECT id, valor_total FROM titulos_receber WHERE ".$where));
        foreach ($titulos as $titulo) {
            $data = new MovConta;
            $data->desconto         = 0;
            $data->acrescimo        = 0;
            $data->tipo             = 'E';
            $data->id_conta         = $id_conta;
            $data->created_by_descr = $pago_por_descr;
            $data->id_titulo        = $titulo->id;
            $data->valor            = $titulo->valor_total;            
            $data->save();
        }

        DB::statement("UPDATE titulos_receber
            SET
                id_conta = ".$id_conta.",
                valor_total_pago = valor_total,
                d_pago = '".date("Y-m-d")."',
                h_pago = '".date("H:i:s")."',
                id_forma_pag_pago = 103,
                pago = 'S',
                pago_por = ".Auth::user()->id_profissional.",
                pago_por_descr = '".$pago_por_descr."',
                obs = 'Contrato cancelado'

                WHERE ".$where
        );

        return redirect("/financeiro/alugueis");
    }
}