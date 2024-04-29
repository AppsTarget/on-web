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
use App\TitulosReceber;
use App\FormaPag;
use App\MovConta;
use Illuminate\Http\Request;

class TitulosRecebercontroller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        return $this->pagina("", "");
    }

    public function from_cockpit($filtro, $data) {
        return $this->pagina($filtro, $data);
    }

    public function pagina($filtro, $data) {
        $titulos_receber = [];
        
        $empresas = DB::table('empresa')
                    ->get();
        
        $formas_pag = DB::table('forma_pag')
                        ->where('lixeira', 0)
                        ->get();
        
        $contas = DB::table('contas_bancarias')
                    ->where('id_emp', getEmpresa())
                    ->get();
            
        return view('titulos_receber', compact('titulos_receber', 'empresas', 'formas_pag', 'contas', 'filtro', 'data'));
    }

    public function pesquisar(Request $request) {
        if ($request->datainicial != '') $filtroi = new \DateTime($request->datainicial);
        else                             $filtroi = '';
        if ($request->datafinal != '')   $filtrof = new \DateTime($request->datafinal);
        else                             $filtrof = '';

        $consulta = DB::table('titulos_receber')
                ->select('titulos_receber.pago AS pago',
                        'titulos_receber.id_pedido as id_pedido',
                        'pessoa.nome_fantasia AS pessoa',
                        DB::raw("CASE WHEN (id_forma_pag = 102) THEN convenio.descr
                                ELSE forma_pag.descr END AS pagamento"),
                        "titulos_receber.parcela AS parcela",
                        "titulos_receber.valor_total",
                        "titulos_receber.valor_total_pago",
                        "titulos_receber.d_entrada AS dt_lanc",
                        "titulos_receber.d_vencimento AS dt_vencimento",
                        "titulos_receber.id AS id")
                ->leftjoin('pedido', 'pedido.id', 'titulos_receber.id_pedido')
                ->leftjoin('pessoa', 'pessoa.id', 'titulos_receber.id_pessoa')
                ->leftjoin('convenio', 'convenio.id', 'pedido.id_convenio')
                ->leftjoin('forma_pag', 'forma_pag.id', 'titulos_receber.id_forma_pag')
                ->where('titulos_receber.lixeira', 'N')
                ->where('valor_total', '>', 0)
                ->whereRaw('(((pedido.id is null AND titulos_receber.id_pedido = 0) or pedido.lixeira = 0) AND
                            titulos_receber.id_forma_pag not in (8,11,100,101,103))')
                ->where(function($sql) use($request, $filtroi, $filtrof){
                    if ($request->contrato != ''){
                        $sql->where('titulos_receber.id_pedido', $request->contrato);
                    }
                    if ($request->associado != ''){
                        $sql->where('titulos_receber.id_pessoa', $request->associado);
                    }
                    if ($request->empresa != 0){
                        $sql->where('titulos_receber.id_emp', $request->empresa);
                    }
                    if ($request->venc_ou_lanc != '' && $request->venc_ou_lanc ==  'vencimento'){
                        if ($filtroi != '') $sql->where('titulos_receber.d_vencimento', '>=', $filtroi->format('Y-m-d'));
                        if ($filtrof != '') $sql->where('titulos_receber.d_vencimento', '<=', $filtrof->format('Y-m-d'));
                    }
                    if ($request->venc_ou_lanc != '' && $request->venc_ou_lanc ==  'lancamento'){
                        if ($filtroi != '') $sql->where('titulos_receber.d_entrada', '>=', $filtroi->format('Y-m-d'));
                        if ($filtrof != '') $sql->where('titulos_receber.d_entrada', '<=', $filtroi->format('Y-m-d'));
                    }
                    if ($request->valor_inicial != '' && $request->valor_inicial != 0){
                        $sql->where('titulos_receber.valor_total', ">=" , $request->valor_inicial);
                    }
                    if ($request->valor_final != '' && $request->valor_final != 0){
                        $sql->where('titulos_receber.valor_total',"<=", $request->valor_final);
                    }
                    if ($request->forma_pag != 0){
                        $sql->where('titulos_receber.id_forma_pag', $request->forma_pag);
                    }
                    if ($request->liquidados == 'N'){
                        $sql->where('titulos_receber.pago', 'N');
                    }
                })
                ->get();
        if ($request->analitico == "N") {
            $total = 0;
            foreach($consulta as $aux) {
                $total += $aux->valor_total - $aux->valor_total_pago;
            }
            $resultado = new \Stdclass;
            $resultado->total = $total;
            $resultado->id = $request->id;
            return json_encode($resultado);
        } else return $consulta;
    }

    public function salvar(Request $request) {
        switch($request->tipo) {
            case 'parcelado':
                for($i=0; $i < sizeof($request->parcelas); $i++){
                    $titulo_pagar = new TitulosReceber;
                    $titulo_pagar->ndoc = $request->nDoc;
                    $titulo_pagar->id_pedido = 0;
                    
                    if (getCaixa()) $titulo_pagar->id_caixa = getCaixa()->id;
                    else            $titulo_pagar->id_caixa = 0;

                    $titulo_pagar->descr = $request->descr;
                    $titulo_pagar->origem = "Cadastro Manual";
                    $titulo_pagar->parcela = $request->parcelas[$i];
                    // $titulo_pagar->id_forma_pag = $request->forma_pag;
                    // $titulo_pagar->forma_pag = FormaPag::find($request->forma_pag)->descr;
                    $titulo_pagar->id_pedido = 0;
                    $titulo_pagar->id_pessoa = $request->id_pessoa;
                    $titulo_pagar->d_entrada = date('Y-m-d');
                    $titulo_pagar->h_entrada = date('H:i:s');
                    $titulo_pagar->d_emissao = date('Y-m-d', strtotime($request->emissao));
                    $titulo_pagar->d_vencimento = date('Y-m-d', strtotime($request->vencimentos[$i]));
                    $titulo_pagar->created_by = Auth::user()->id_profissional;
                    $titulo_pagar->created_by_descr = Pessoa::find(Auth::user()->id_profissional)->nome_fantasia;
                    $titulo_pagar->updated_by = Auth::user()->id_profissional;
                    $titulo_pagar->updated_by_descr = Pessoa::find(Auth::user()->id_profissional)->nome_fantasia;
                    $titulo_pagar->valor_total = $request->valores[$i];

                    $titulo_pagar->obs = "";
                    $titulo_pagar->lixeira = "N";

                    $titulo_pagar->save();
                }
                break;
            case 'titulo-unico':
                $titulo_pagar = new TitulosReceber;
                $titulo_pagar->ndoc = $request->nDoc;
                $titulo_pagar->id_pedido = 0;

                if (getCaixa()) $titulo_pagar->id_caixa = getCaixa()->id;
                else            $titulo_pagar->id_caixa = 0;

                $titulo_pagar->descr = $request->descr;
                $titulo_pagar->origem = "Cadastro Manual";
                $titulo_pagar->parcela = $request->parcela;

                $titulo_pagar->id_pessoa = $request->id_pessoa;
                $titulo_pagar->d_entrada = date('Y-m-d');
                $titulo_pagar->h_entrada = date('H:i:s');
                $titulo_pagar->d_emissao = date('Y-m-d', strtotime($request->emissao));
                $titulo_pagar->d_vencimento = date('Y-m-d', strtotime($request->vencimento));
                $titulo_pagar->created_by = Auth::user()->id_profissional;
                $titulo_pagar->created_by_descr = Pessoa::find(Auth::user()->id_profissional)->nome_fantasia;
                $titulo_pagar->updated_by = Auth::user()->id_profissional;
                $titulo_pagar->updated_by_descr = Pessoa::find(Auth::user()->id_profissional)->nome_fantasia;
                $titulo_pagar->valor_total = $request->valor_total;

                $titulo_pagar->obs = "";
                $titulo_pagar->lixeira = "N";

                // return $request->valor_total;
                // return $titulo_pagar;
                $titulo_pagar->save();
                break;
        }
    }

    public function exibir($id) {
        if (TitulosReceber::find($id)->id_pedido == TitulosReceber::find($id)->ndoc) {
            return json_encode(
                DB::table('titulos_receber')
                ->select('pessoa.nome_fantasia')
                ->where('titulos_receber.id', $id)
                ->get()
            );
        }
    }

    public function abrir_modal_baixa($id){
        $data = new \StdClass;

        $data->valor = TitulosReceber::find($id)->valor_total - TitulosReceber::find($id)->valor_total_pago; 
        $data->data = date('d/m/Y');
        
        return json_encode($data);
    }

    public function salvar_baixa_receber(Request $request) {
        $data_ = new \DateTime($request->data_baixa);

        $titulo = TitulosReceber::find($request->id);

        $titulo->id_conta = $request->conta;
        $titulo->valor_total_pago = $request->valor_total;
        $titulo->d_pago = $data_->format('Y-m-d');
        $titulo->h_pago = date('H:i:s');
        $titulo->id_forma_pag_pago = $request->forma_pag;
        $titulo->pago = 'S';
        $titulo->pago_por = Auth::user()->id_profissional;
        $titulo->pago_por_descr = Pessoa::find(Auth::user()->id_profissional)->nome_fantasia;

        $titulo->save();

        $mov_conta = new MovConta;
        $mov_conta->id_conta         = $request->conta;
        $mov_conta->id_titulo        = $request->id;
        $mov_conta->tipo             = 'E';
        $mov_conta->valor            = $request->valor_total + $request->acrescimo - $request->desconto;
        $mov_conta->desconto         = $request->desconto;
        $mov_conta->acrescimo        = $request->acrescimo;
        $mov_conta->created_by_descr = Pessoa::find(Auth::user()->id_profissional)->nome_fantasia;
        $mov_conta->save();

        return 'true';


    }

    public function titulos_receber_abertos() {
        $empresas = DB::table('empresa')
                    ->selectRaw("id, CONCAT(descr, ' - ', cidade) AS descr")
                    ->get();

        return view('titulos-receber-liquidados', compact('empresas'));
    }

    public function titulos_receber_pendentes() {
        $empresas = DB::table('empresa')
                    ->selectRaw("id, CONCAT(descr, ' - ', cidade) AS descr")
                    ->get();

        return view('titulos-receber-pendentes', compact('empresas'));
    }

    public function visualizar($id) {
        $titulo = DB::table('titulos_receber')
                ->select('titulos_receber.ndoc',
                         'titulos_receber.descr AS descricao',
                         'titulos_receber.pago AS pago',
                         'titulos_receber.d_pago',
                         'pessoa.nome_fantasia AS fornecedor',
                         'titulos_receber.created_by_descr AS criado_por',
                         'titulos_receber.pago_por_descr AS pago_por',
                         'titulos_receber.d_entrada AS entrada',
                         'titulos_receber.d_emissao AS emissao',
                         'titulos_receber.d_vencimento As vencimento'
                )
                ->leftjoin('pessoa','pessoa.id','titulos_receber.id_pessoa')
                ->leftjoin('contas_bancarias', 'contas_bancarias.id', 'titulos_receber.id_conta')
                ->where('titulos_receber.id', $id)
                ->first();    
        
        $titulo->parcelas = DB::table('titulos_receber')
                          ->select('titulos_receber.parcela',
                                   'titulos_receber.valor_total',
                                   'titulos_receber.valor_total_pago',
                                   'titulos_receber.d_entrada',
                                   'titulos_receber.h_entrada',
                                   'titulos_receber.d_emissao',
                                   'titulos_receber.d_vencimento',
                                   'titulos_receber.d_pago',
                                   'titulos_receber.pago')
                          ->where('titulos_receber.ndoc', $titulo->ndoc)
                          ->orderBy('titulos_receber.parcela')
                          ->get();
        $aux = 0;
        $titulo->pago = 'S';
        foreach($titulo->parcelas AS $parcela) {
            $aux += $parcela->valor_total;
            if ($parcela->pago == 'N') $titulo->pago = 'N';
        }
        $titulo->valor_total = $aux;
        return json_encode($titulo);
    }
}