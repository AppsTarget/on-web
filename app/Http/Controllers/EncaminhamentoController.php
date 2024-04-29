<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Anexos;
use App\Encaminhantes;
use App\EncaminhantesEspecialidade;
use App\EncaminhamentoSolicitacao;
use App\especialidadePessoa;
use Illuminate\Http\Request;

class EncaminhamentoController extends Controller
{
    public function obterEncaminhante(Request $request) {
        $data = new \stdClass;
        $data->encaminhante = DB::table("enc2_encaminhantes")->where("id", $request->id)->where("lixeira", 0)->first();
        $data->especialidades = DB::select(DB::raw("
            SELECT especialidade.id
            FROM especialidade
            JOIN enc2_encaminhantes_especialidade
                ON enc2_encaminhantes_especialidade.id_especialidade = especialidade.id
            WHERE lixeira = 0
              AND id_encaminhante = ".$request->id."
        "));
        return json_encode($data);
    }

    public function criarEncaminhante(Request $request) {
        $data = new Encaminhantes;
        $data->nome_fantasia = $request->nome_fantasia;
        $data->documento = $request->documento;
        $data->documento_estado = $request->documento_estado;
        $data->tpdoc = $request->tpdoc;
        $data->telefone = $request->telefone;
        $data->save();
        $this->incluirEspecialidades($data->id, $request->esp);
    }

    public function editarEncaminhante(Request $request) {
        DB::statement("
            UPDATE enc2_encaminhantes SET
                nome_fantasia = '".$request->nome_fantasia."',
                documento = '".$request->documento."',
                documento_estado = '".$request->documento_estado."',
                tpdoc = '".$request->tpdoc."',
                telefone = '".$request->tel."'
            WHERE id = ".$request->id."
        ");
        $this->incluirEspecialidades($request->id, $request->esp);
    }

    private function incluirEspecialidades($encaminhante, $especialidades) {
        DB::statement("DELETE FROM enc2_encaminhantes_especialidade WHERE id_encaminhante = ".$encaminhante);
        $especialidades = explode(",", $especialidades);
        $internos = DB::table("enc2_encaminhantes")->where("id", $encaminhante)->where("lixeira", 0)->whereNotNull("id_pessoa")->get();
        if (sizeof($internos)) {
            $id_pessoa = $internos[0]->id_pessoa;
            DB::statement("DELETE FROM especialidade_pessoa WHERE id_profissional = ".$id_pessoa);
        } else $id_pessoa = 0;
        foreach ($especialidades as $especialidade) {
            $data = new EncaminhantesEspecialidade;
            $data->id_encaminhante = $encaminhante;
            $data->id_especialidade = $especialidade;
            $data->save();

            if ($id_pessoa > 0) {
                $data = new especialidadePessoa;
                $data->id_especialidade = $especialidade;
                $data->id_profissional = $id_pessoa;
                $data->save();
            }
        }
    }

    public function excluirEncaminhante(Request $request) {
        DB::statement("update enc2_encaminhantes set lixeira = 1 where id = ".$request->id);
    }

    public function anexar_de_pedido(Request $request) {
        return $this->anexar($request, Auth::user()->id_profissional, "pedido", $request->id_pedido);
    }

    public function anexar_de_agenda(Request $request) {
        return $this->anexar($request, $request->enc_profissional, "agenda", $request->enc_agendamento);
    }

    private function anexar(Request $request, $profissional, $tabela, $filtro) {
        try {
            if ($request->file('enc_arquivo') != null) {
                $anexo = new Anexos;
                $anexo->id_emp = getEmpresa();
                $anexo->id_paciente = $request->id_paciente;
                $anexo->id_profissional = $profissional;
                $anexo->obs = "";
                $anexo->pasta = 5;
                $anexo->created_at = date('Y-m-d H:i:s');
                $anexo->updated_at = date('Y-m-d H:i:s');
                $anexo->save();
            
                $path = $request->file('enc_arquivo')->getClientOriginalName();
                print_r($path);
                $request->file('enc_arquivo')
                        ->move(
                            public_path('anexos'),
                            $path
                        );
                $anexo->titulo = $path;
                $anexo->save();
                DB::statement("
                    UPDATE enc2_encaminhamentos
                    SET id_anexo = ".$anexo->id."
                    WHERE id IN (
                        SELECT id_encaminhamento
                        FROM ".$tabela."
                        WHERE id = ".$filtro."
                    )
                ");
            }
            return json_encode($anexo);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function listarEspecialidade() {
        return json_encode(DB::table("especialidade")->select("id", "descr")->where('lixeira', 0)->orderby("descr")->get());
    }
    public function espPorEnc(Request $request) {
        return json_encode(DB::select(DB::raw("
            SELECT
                especialidade.id,
                especialidade.descr
            FROM especialidade
            JOIN enc2_encaminhantes_especialidade
                ON enc2_encaminhantes_especialidade.id_especialidade = especialidade.id
            JOIN enc2_encaminhantes
                ON enc2_encaminhantes.id = enc2_encaminhantes_especialidade.id_encaminhante
            WHERE especialidade.lixeira = 0
              AND enc2_encaminhantes.lixeira = 0
              AND enc2_encaminhantes.".$request->col." = ".$request->id
        )));
    }

    public function mostrar($id) {
        return json_encode(DB::select(DB::raw("
            SELECT
                enc2_encaminhamentos.id_de AS id_encaminhante,
                aux_de.nome_fantasia AS descr_encaminhante,
                
                enc2_encaminhamentos.id_cid,
                TRIM(CONCAT(cid.codigo, ' - ', cid.nome)) AS descr_cid
            
            ".$this->selecao("enc2_encaminhamentos.id = ".$id)
        )));
    }

    public function listar($id) {
        return json_encode(DB::select(DB::raw("
            SELECT
                CASE
                    WHEN (agenda.id IS NOT NULL OR pedido.id IS NOT NULL) THEN DATE_FORMAT((CASE
                        WHEN (agenda.id IS NOT NULL AND pedido.id IS NOT NULL) THEN CASE
                            WHEN (DATE(agenda.created_at) < DATE(pedido.created_at)) THEN DATE(agenda.created_at)
                            ELSE DATE(pedido.created_at)
                        END
                        WHEN agenda.id IS NOT NULL THEN DATE(agenda.created_at)
                        ELSE DATE(pedido.created_at)
                    END), '%d/%m/%Y')
                    ELSE 0
                END AS data,
                aux_de.nome_fantasia AS encaminhante,
                CASE
                    WHEN (enc2_encaminhamentos.data IS NOT NULL AND enc2_encaminhamentos.data > '2000-01-01') THEN DATE_FORMAT(enc2_encaminhamentos.data, '%d/%m/%Y')
                    ELSE 0
                END AS data_doc,
                CASE
                    WHEN cid.id IS NOT NULL THEN cid.codigo
                    ELSE 0
                END AS cod_cid,
                CASE
                    WHEN cid.id IS NOT NULL THEN CONCAT(cid.codigo, ' - ', TRIM(cid.nome))
                    ELSE 0
                END AS descr_cid,
                CASE
                    WHEN aux_para.id IS NOT NULL THEN aux_para.nome_fantasia
                    WHEN especialidade.id IS NOT NULL THEN especialidade.descr
                    ELSE 0
                END AS destinatario,
                CASE
                    WHEN anexos.id IS NOT NULL THEN anexos.titulo
                    ELSE 0
                END AS anexo

            ".$this->selecao("enc2_encaminhamentos.id_paciente = ".$id)
        )));
    }

    private function selecao($filtro) {
        return "
            FROM enc2_encaminhamentos

            JOIN enc2_encaminhantes AS aux_de
                ON aux_de.id = enc2_encaminhamentos.id_de
                    
            LEFT JOIN pessoa AS encaminhante
                ON encaminhante.id = aux_de.id_pessoa
                    
            LEFT JOIN enc2_encaminhantes AS aux_para
                ON aux_para.id = enc2_encaminhamentos.id_para
                
            LEFT JOIN pessoa AS destinatario
                ON destinatario.id = aux_para.id_pessoa
                
            JOIN pessoa AS paciente
                ON paciente.id = enc2_encaminhamentos.id_paciente
            
            LEFT JOIN especialidade
                ON especialidade.id = enc2_encaminhamentos.id_especialidade
            
            LEFT JOIN cid
                ON cid.id = enc2_encaminhamentos.id_cid
            
            LEFT JOIN anexos
                ON anexos.id = enc2_encaminhamentos.id_anexo
            
            LEFT JOIN agenda
                ON agenda.id_encaminhamento = enc2_encaminhamentos.id
            
            LEFT JOIN pedido
                ON pedido.id_encaminhamento = enc2_encaminhamentos.id
                
            WHERE ".$filtro."
              AND aux_de.lixeira = 0
              AND paciente.lixeira = 0
              AND enc2_encaminhamentos.lixeira = 0
              AND (anexos.id IS NULL OR anexos.lixeira = 0)
              AND (agenda.id IS NULL OR agenda.lixeira = 0)
              AND (pedido.id IS NULL OR pedido.lixeira = 0)
              AND (aux_para.id IS NULL OR aux_para.lixeira = 0)
              AND (encaminhante.id IS NULL OR encaminhante.lixeira = 0)
              AND (destinatario.id IS NULL OR destinatario.lixeira = 0)
              AND (especialidade.id IS NULL OR especialidade.lixeira = 0)
              AND (enc2_encaminhamentos.data_validade IS NULL OR enc2_encaminhamentos.data_validade >= CURDATE())
              AND (enc2_encaminhamentos.id_especialidade IS NULL OR enc2_encaminhamentos.id_para IS NULL OR especialidade.id_emp IN (
                SELECT empresas_profissional.id_emp
                FROM empresas_profissional
                WHERE empresas_profissional.id_profissional = destinatario.id
            ))
        ";
    }

    public function gravarSolicitacao(Request $request) {
        $aux = DB::table("enc2_encaminhantes")->where("id_pessoa", Auth::user()->id_profissional)->get();
        $id_de = sizeof($aux) ? $aux[0]->id : 0;
        if ($request->id == 0) {
            $data = new EncaminhamentoSolicitacao;
            $data->id_de = $id_de;
            $data->id_especialidade = $request->sol_enc_esp;
            $data->id_procedimento = $request->sol_enc_prc;
            $data->id_paciente = $request->id_paciente;
            $data->atv_semana = $request->sol_enc_vzs;
            $data->retorno = $request->sol_enc_ret;
            $data->obs = $request->obs;
            $data->id_emp = getEmpresa();
            $data->save();
        } else {
            DB::statement("
                UPDATE enc2_solicitacao SET
                    id_de = ".$id_de.",
                    id_especialidade = ".$request->sol_enc_esp.",
                    id_paciente = ".$request->id_paciente.",
                    atv_semana = ".$request->sol_enc_vzs.",
                    id_procedimento = ".$request->sol_enc_prc.",
                    retorno = '".$request->sol_enc_ret."',
                    obs = '".$request->obs."'
                WHERE id = ".$request->id
            );
        }
        return $request->id_paciente;
    }

    public function mostrarSolicitacao(Request $request) {
        $query = "
            SELECT
                UPPER(pessoa.nome_fantasia)             AS paciente,
                UPPER(enc2_encaminhantes.nome_fantasia) AS solicitante,
                DATE_FORMAT(enc2_solicitacao.created_at, '%d/%m/%Y') AS solicitado_em,
                enc2_solicitacao.id_especialidade,
                UPPER(especialidade.descr) AS especialidade,
                UPPER(procedimento.descr) AS procedimento,
                enc2_solicitacao.atv_semana,
                enc2_solicitacao.obs,
                DATE_FORMAT(enc2_solicitacao.retorno, '%d/%m/%Y') AS retorno,
                IFNULL(((DATEDIFF(retorno, enc2_solicitacao.updated_at) / 7) * enc2_solicitacao.atv_semana), 0) AS ag_tot,
                IFNULL(aux_enc_ag.cont, 0) AS ag_cont
            
            FROM enc2_solicitacao
            
            JOIN pessoa
                ON pessoa.id = enc2_solicitacao.id_paciente
            
            LEFT JOIN enc2_encaminhantes
                ON enc2_encaminhantes.id = enc2_solicitacao.id_de
                
            JOIN especialidade
                ON especialidade.id = enc2_solicitacao.id_especialidade
                
            JOIN procedimento
                ON procedimento.id = enc2_solicitacao.id_procedimento

            LEFT JOIN (
                SELECT
                    id_solicitacao,
                    COUNT(*) AS cont
                FROM agenda
                JOIN enc2_encaminhamentos
                    ON enc2_encaminhamentos.id = agenda.id_encaminhamento
                JOIN enc2_solicitacao
                    ON enc2_solicitacao.id = enc2_encaminhamentos.id_solicitacao
                WHERE agenda.status <> 'C'
                  AND agenda.lixeira = 0
                  AND enc2_solicitacao.lixeira = 0
                  AND enc2_encaminhamentos.lixeira = 0
                GROUP BY id_solicitacao
            ) AS aux_enc_ag ON aux_enc_ag.id_solicitacao = enc2_solicitacao.id

            WHERE enc2_solicitacao.id = ".$request->id;
        return json_encode(DB::select(DB::raw($query)));
    }

    public function listarSolicitacao(Request $request) {
        $cond = "(
                (enc2_solicitacao.id_especialidade <> 11 AND (aux_enc_ag.cont = 0 OR aux_enc_ag.cont IS NULL)) OR
                (enc2_solicitacao.id_especialidade =  11 AND ((
                    aux_enc_ag.cont < ((DATEDIFF(retorno, enc2_solicitacao.updated_at) / 7) * enc2_solicitacao.atv_semana)
                ) OR aux_enc_ag.cont IS NULL))
            )
            AND aux_enc_pd.id_solicitacao IS NULL
        ";
        $query = "
            SELECT
                enc2_solicitacao.id,
                IFNULL(enc2_solicitacao.id_de, 0) AS id_de,
                IFNULL(enc2_solicitacao.id_para, 0) AS id_para,
                IFNULL(enc2_solicitacao.id_cid, 0) AS id_cid,
                IFNULL(enc2_solicitacao.id_especialidade, 0) AS id_especialidade,
                DATE_FORMAT(enc2_solicitacao.updated_at, '%d/%m/%Y') AS data,
                IFNULL(aux_de.nome_fantasia, 0) AS encaminhante,
                IFNULL(cid.codigo, 0) AS cid_codigo,
                IFNULL(TRIM(cid.nome), 0) AS cid_nome,
                aux_esp.descr AS descr_esp,
                aux_para.nome_fantasia AS descr_para,
                CASE
                    WHEN aux_para.id IS NOT NULL THEN aux_para.nome_fantasia
                    WHEN aux_esp.id IS NOT NULL THEN aux_esp.descr
                    ELSE 0
                END AS para,
                CASE
                    WHEN (".$cond.") THEN 'A'
                    ELSE 'F'
                END AS situacao,
                CASE
                    WHEN (".Auth::user()->id_profissional." = aux_de.id_pessoa) THEN 'S'
                    ELSE 'N'
                END AS permissao,
                id_procedimento,
                atv_semana,
                DATE_FORMAT(retorno, '%d/%m/%Y') AS retorno,
                obs
            
            FROM enc2_solicitacao
            
            LEFT JOIN enc2_encaminhantes AS aux_de
                ON aux_de.id = enc2_solicitacao.id_de
            
            LEFT JOIN (
                SELECT
                    enc2_encaminhantes.id,
                    pessoa.id AS id_pessoa,
                    pessoa.nome_fantasia
                FROM enc2_encaminhantes
                JOIN pessoa
                    ON pessoa.id = enc2_encaminhantes.id_pessoa
                WHERE enc2_encaminhantes.lixeira = 0
                  AND pessoa.lixeira = 0
                  AND pessoa.colaborador <> 'N'
            ) AS aux_para ON aux_para.id = enc2_solicitacao.id_para
            
            LEFT JOIN cid
                ON cid.id = enc2_solicitacao.id_cid
                
            LEFT JOIN (
                SELECT
                    especialidade.id,
                    especialidade.descr
                FROM especialidade
                JOIN enc2_encaminhantes_especialidade
                    ON enc2_encaminhantes_especialidade.id_especialidade = especialidade.id
                JOIN enc2_encaminhantes
                    ON enc2_encaminhantes.id = enc2_encaminhantes_especialidade.id_encaminhante
                JOIN pessoa
                    ON pessoa.id = enc2_encaminhantes.id_pessoa
                WHERE especialidade.id_emp IN (
                    SELECT id_emp
                    FROM empresas_profissional
                    WHERE empresas_profissional.id_profissional = pessoa.id
                ) AND especialidade.lixeira = 0
                  AND enc2_encaminhantes.lixeira = 0
                  AND pessoa.lixeira = 0
                  AND pessoa.colaborador <> 'N'
                GROUP BY
                    id,
                    descr
            ) AS aux_esp ON aux_esp.id = enc2_solicitacao.id_especialidade

            LEFT JOIN (
                SELECT id_solicitacao
                FROM enc2_encaminhamentos
                JOIN pedido
                    ON pedido.id_encaminhamento = enc2_encaminhamentos.id
                WHERE (pedido.id IS NULL OR pedido.lixeira = 0)
                  AND enc2_encaminhamentos.lixeira = 0
            ) AS aux_enc_pd ON aux_enc_pd.id_solicitacao = enc2_solicitacao.id

            LEFT JOIN (
                SELECT
                    id_solicitacao,
                    COUNT(*) AS cont
                FROM agenda
                JOIN enc2_encaminhamentos
                    ON enc2_encaminhamentos.id = agenda.id_encaminhamento
                JOIN enc2_solicitacao
                    ON enc2_solicitacao.id = enc2_encaminhamentos.id_solicitacao
                WHERE agenda.status <> 'C'
                  AND agenda.lixeira = 0
                  AND enc2_solicitacao.lixeira = 0
                  AND enc2_encaminhamentos.lixeira = 0
                GROUP BY id_solicitacao
            ) AS aux_enc_ag ON aux_enc_ag.id_solicitacao = enc2_solicitacao.id

            WHERE enc2_solicitacao.lixeira = 0
              AND enc2_solicitacao.id_paciente = ".$request->id_pessoa;
        if ($request->todos == "N") $query .= " AND ".$cond;
        if ($request->id_pessoa != "") {
            if (isset($request->profissional)) {
                if (strlen($request->esp)) {
                    $cond = sizeof(DB::select(DB::raw("
                        SELECT especialidade.id
                        FROM especialidade
                        JOIN enc2_encaminhantes_especialidade
                            ON enc2_encaminhantes_especialidade.id_especialidade = especialidade.id
                        JOIN enc2_encaminhantes
                            ON enc2_encaminhantes.id = enc2_encaminhantes_especialidade.id_encaminhante
                        JOIN pessoa
                            ON pessoa.id = enc2_encaminhantes.id_pessoa
                        WHERE especialidade.id_emp IN (
                            SELECT id_emp
                            FROM empresas_profissional
                            WHERE empresas_profissional.id_profissional = pessoa.id
                        ) AND especialidade.id IN (".$request->esp.")
                          AND especialidade.lixeira = 0
                          AND enc2_encaminhantes.lixeira = 0
                          AND pessoa.id = ".$request->profissional."
                        GROUP BY
                            id,
                            descr
                    "))) ? "1" : "0";
                } else $cond = "0";
                $query .= " AND (aux_para.id_pessoa = ".$request->profissional." OR ( 
                    aux_para.id_pessoa IS NULL AND ".$cond."
                ))";
                $solicitacoes = DB::select(DB::raw($query));
                $resultado = array();
                foreach($solicitacoes as $solicitacao) {
                    if (sizeof(DB::select(DB::raw("
                        SELECT especialidade.id
                        FROM especialidade
                        JOIN enc2_encaminhantes_especialidade
                            ON enc2_encaminhantes_especialidade.id_especialidade = especialidade.id
                        JOIN enc2_encaminhantes
                            ON enc2_encaminhantes.id = enc2_encaminhantes_especialidade.id_encaminhante
                        JOIN pessoa
                            ON pessoa.id = enc2_encaminhantes.id_pessoa
                        WHERE especialidade.id_emp IN (
                            SELECT id_emp
                            FROM empresas_profissional
                            WHERE empresas_profissional.id_profissional = pessoa.id
                        ) AND especialidade.id IN (".$request->esp.")
                          AND especialidade.id = ".$solicitacao->id_especialidade."
                          AND especialidade.lixeira = 0
                          AND enc2_encaminhantes.lixeira = 0
                          AND pessoa.id = ".$request->profissional."
                        GROUP BY
                            id,
                            descr
                    ")))) array_push($resultado, $solicitacao);
                }
                return json_encode($resultado);
            } else return json_encode(DB::select(DB::raw($query)));
        }
    }

    public function excluirSolicitacao(Request $request) {
        DB::statement("UPDATE enc2_solicitacao SET lixeira = 1 WHERE id = ".$request->id);
        return DB::table("enc2_solicitacao")->where("id", $request->id)->value("id_paciente");
    }
}
