<style>
    p {
        margin-left: 130px;
        margin-right: 130px;
    }
</style>
<h1 align='center'>Contrato de Prestação de Serviços</h1>

<p>Pelo presente instrumento particular, que entre si fazem, de um lado como CONTRATADA, ON Morumbi Clínica Médica, situado na Avenida Jorge João Saad, 122, na cidade São Paulo- SP, representada legalmente neste ato por Marta Marques Quaggio Sabeh, RG 30.622.870-1, CPF 226.684.568-35, e de outro lado como <b>CONTRATANTE: {{ $pessoa->nome_fantasia }}, CPF: {{ $pessoa->cpf_cnpj }}, RG: {{ $pessoa->rg_ie }}</b>, residente e domiciliado na {{ $pessoa->endereco }}, nº {{ $pessoa->numero }}, bairro: {{ $pessoa->bairro }}, cidade: {{ $pessoa->cidade }}, estado: {{ $pessoa->estado }}, têm entre si justo e contratado na melhor forma de direito as cláusulas seguintes: <p>

<p><b>Cláusula 1</b> – Objeto: prestação de serviços, por parte da CONTRATADA, de sessões e tratamentos para evolução do corpo, de acordo com as condições do plano de serviços contratado indicado na clausula 6.</p>

<p><b>Cláusula 2</b> – Horários: O CONTRATANTE e o CONTRATADO deverão estar prontos no local da sessão, para que não haja atrasos no horário estabelecido. Haverá uma tolerância de no MÁXIMO 15 MINUTOS para comparecimento de ambos no local estabelecido. Caso seja expirada essa tolerância pelo CONTRATANTE, a sessão será considerada ministrada.</p>

<p><b>Paragrafo 1º:</b> Caso haja atraso pelo CONTRATADO, de no máximo 15 minutos para o comparecimento no local da sessão, o CONTRATANTE poderá realizar a sessão com outro profissional disponível no horário ou remarcar a sessão para outro dia e horário. </p>

<p><b>Parágrafo 2º:</b> Caso haja atraso no horário estabelecido, mas o CONTRATANTE compareça para a sessão, o tempo de atraso será computado como tempo de sessão ministrada. </p>

<p><b>Cláusula 3</b> – Reposição de Aulas: Se o CONTRATANTE efetuar o cancelamento da sessão em até 4 horas antes do horário estabelecido para início da sessão, o CONTRATADO fará reposição do atendimento, em dia e horário a ser combinado durante o período do mês vigente para os planos de Sessões, light (2x), modelo (4x) e full (todos os dias). O cancelamento não comunicado no plano vigente, a sessão será considerada ministrada, assim como quando cancelada com menor período de antecedência como citado acima. Apenas será considerada como hora válida, o horário de funcionamento da ON, ou seja, por exemplo: uma sessão agendada para ás 7h só poderá ser cancelada até as 20:30h do dia anterior. O horário de funcionamento, será de 2a feira á 6a feira, dás 7h ás 21h e ao sábados 8h ás 12h, salvo feriados e emendas.</p>

<p><b>Parágrafo Primeiro:</b> No plano trimestral será concedida uma licença de afastamento (interrupção do plano) de até 10 dias,  no semestral de 20 dias e no anual de 30 dias, com direito a reposição do período no final do contrato. Esta licença deverá ser formalizada por escrito e com pelo menos 10 dias de antecedência.</p>

<p><b>Parágrafo Segundo:</b> O trancamento é uma liberalidade da CONTRATADA que dá a você o direito de utilizar os serviços contratados por períodos adicionais, acrescidos ao final do seu contrato. Tais períodos não dão direito a qualquer indenização ou reembolso no caso de cancelamento do plano.</p>

<p><b>Cláusula 4 </b>– <u>Atestado Médico</u> – O CONTRATANTE deverá apresentar o atestado médico na primeira sessão, podendo ser realizado pelo médico da ON, ou por um médico de confiança do contratante.</p>

<p><b>Cláusula 5 </b>– Sessão: Todas as sessões terão média de duração de 60 minutos.</p>

<p><b>Cláusula 5 </b>– Sessão: Todas as sessões terão média de duração de {{ $plano->tempo }} minutos.</p>

<p><b>Cláusula 6 </b>– Plano contratado: {{ $plano->descr }}</p>

<p><b>Cláusula 7 </b>– <u>Pagamento</u>: O pagamento do plano optado na cláusula acima será feito da seguinte forma:</p>

<p><b>Cláusula 8 </b>– Atraso: Em caso de atraso no pagamento o CONTRATADO suspenderá imediatamente as aulas, não isentando o CONTRATANTE do seu pagamento e sem o direito à reposição. A aula ministrada pelo CONTRATADO que não incluem o plano fixado com o CONTRATANTE será cobrada sessão avulsa. </p>

<p><b>Parágrafo 1º</b>. O não pagamento das parcelas assumidas neste contrato sujeitará ao CONTRATANTE o pagamento de multa de 2% (dois por cento), mais juros de 1% (um por cento) ao mês, atualização monetária pelo IGPM/FGV, recaindo sobre o total dos débitos atualizados, além de despesas processuais e honorários advocatícios de 20% (vinte por cento), mesmo em caso de cobrança administrativa. </p>

<p><b>Parágrafo 2º</b>. Na hipótese de pagamento pelo plano por cartão recorrente, e caso a administradora do cartão de crédito não autorize a liberação da quantia devida, você deverá comparecer na unidade da ON para pagar o débito em aberto até o dia imediatamente anterior ao próximo débito. Após 30 (trinta) dias de inadimplência, a ON poderá rescindir o plano sem aviso prévio e sem prejuízo da aplicação da multa prevista por cancelamento e eventuais perdas e danos.</p>

<p><b>Parágrafo 3º</b>. Havendo atraso do(s) pagamentos contratados, igual ou superior a 90 (noventa) dias do vencimento, desde que esgotadas todas as formas de cobrança amigáveis, a CONTRATADA fica desde já autorizada a, simultaneamente: incluir o nome do CONTRATANTE no SPC; emitir títulos de créditos contra o ALUNO; ajuizar Ação Moratória ou Ação de Execução; </p>

<p><b>Parágrafo 4º</b>. Caso qualquer disposição deste Contrato seja considerada nula, ilegal ou inexequível em qualquer aspecto, a validade, legalidade ou exequibilidade das disposições restantes não serão afetadas ou prejudicadas. </p>

<p><b>Cláusula 9 </b>– Renovação: Após o período discriminado na cláusula acima, este contrato poderá ser
    renovado automaticamente, bastando para tanto que o CONTRATANTE efetue o (s) pagamento (s) referentes aos meses seguintes, como descrito na cláusula 7 e assim sucessivamente. 
</p>    

<p><b>Cláusula 10 </b>- <u>Rescisão</u>: A prestação de serviços poderá ser cancelada pelo CONTRATANTE, a qualquer momento, desde que solicitado com 30 dias de antecedência, na qual será cobrada uma multa de 20% (vinte por cento) do valor das mensalidades subseqüentes. No caso de cancelamento por iniciativa da ON, esta multa nao sera aplicada.</p>

<p><b>Parágrafo Primeiro</b>.  No caso de rescisão será assegurada a devolução do valor equivalente aos meses remanescentes para o término do prazo, não sendo considerado o cálculo pró rata de dias não usados, períodos acrescentados ao contrato em razão do trancamento previsto na cláusula 3 ou períodos promocionais. Em qualquer hipótese de cancelamento dos planos, por livre iniciativa ou por iniciativa da ON, nos casos de inadimplência e justa causa, será cobrada a multa no valor de 20% sobre o montante residual a ser devolvido, ficando desde já acordado que o pagamento de tal penalidade é condição para que a ON realize o cancelamento.</p>

<p><b>Paragrafo Segundo</b>. Caso o plano de pagamento informado na clausula 7 seja no formato Recorrente pelo cartão de credito, o plano somente poderá ser cancelado após a efetivação do terceiro debito no cartão de credito. O cancelamento por qualquer razão, antes da data do terceiro debito, sossega feito após o pagamento da multa equivalente a 20% sobre o valor que faltar para completar os 3 débitos. O pedido de cancelamento do plano pago de forma recorrente so terá efeito 2 (dois) dias após sua solicitação, ficando o CONTRATANTE obrigado a fazer o pagamento das mensalidades respectivas que porventura vencerem nestes dias independente se será usufruída ou do serviço. Lembrando que o cancelamento do plano pago neste formato não haverá devolução dos valores ja pagos mesmo que não tenha sido utilizado.</p>

<p><b>Cláusula 11 </b>– Foro: Fica eleito o foro da comarca de São Paulo, com prevalência sobre qualquer outro, por mais privilegiado que seja, para dirimir todas as dúvidas que possam advir de quaisquer cláusulas do presente contrato. E por estarem assim justo e contratados de pleno acordo com todas as cláusulas e condições estipuladas, assinam o presente instrumento particular em 2 (duas) vias de igual teor e forma. </p>
