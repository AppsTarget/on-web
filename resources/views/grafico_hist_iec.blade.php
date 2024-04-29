<html>
	<head>
		<style type = "text/css">
			#chart_update {
				height: 400px;
				width: 100%;
				margin-top: 1em;
				position: relative;
				animation-duration: 0.3s;
			}
		</style>
		<script type = "text/javascript" language = "JavaScript" src = "{{ asset('js/oi.linechart.min.js') }}"></script>
		<script type = "text/javascript" language = "JavaScript" src = "{{ asset('js/oi.formatexamples.js') }}"></script>
		<script type = "text/javascript" language = "JavaScript" src = "{{ asset('js/highlight.pack.js') }}"></script>
		<script type = "text/javascript" language = "JavaScript" src = "{{ asset('js/luxon.min.js') }}"></script>
		<link rel="stylesheet" href="{{ asset('css/highlight.css') }}" type="text/css" />
		<link rel="stylesheet" href="{{ asset('css/style.css') }}" type="text/css" />
		@php
			$json = json_decode($json, true);
			echo '<script type = "text/javascript" language = "JavaScript">';
			echo 'function pontos() {';
			echo 'var pontosArr = new Array();';
			foreach ($json as $ponto) {
				echo 'pontosArr.push({x:'.$ponto["x"].',y:'.$ponto["y"].'});';
			}
			echo 'return pontosArr;';
			echo '}';
			echo '</script>';
		@endphp
		<script type = "text/javascript" language = "JavaScript">
			var ano = new Date();
			ano = ano.getFullYear();

			function lblX() {
				var diasPorMes = [31, (ano % 4 === 0 && ano % 100 !== 0) || ano % 400 === 0 ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
				var mudancasMes = [0];
				for (var i = 1; i < diasPorMes.length; i++) mudancasMes.push(mudancasMes[i - 1] + diasPorMes[i - 1]);
				var resultado = {};
				for (var i = 0; i < mudancasMes.length; i++) {
					resultado[mudancasMes[i]] = {
						label : [
							"Janeiro",
							"Fevereiro",
							"Março",
							"Abril",
							"Maio",
							"Junho",
							"Julho",
							"Agosto",
							"Setembro",
							"Outubro",
							"Novembro",
							"Dezembro"
						][i]
					}
				}
				return resultado;
			}

			function lblY() {
				var resultado = {};
				for (var i = 0; i < 4; i++) {
					resultado[i] = {
						label : [
							"Péssimo",
							"Ruim",
							"Bom",
							"Excelente"
						][i]
					}
				}
				return resultado;
			}

			OI.ready(function(){
				chart_update = OI.linechart(document.getElementById('chart_update'),{
					top    :  40,
					right  :  10,
					bottom :  50,
					left   : 100,
					axis : {
						x : {
							line : {
								"stroke-width" : 2
							},
							title : {
								label : "Data"
							},
							labels : lblX()
						},
						y : {
							min: 0,
							max: 3,
							line : {
								"stroke-width" : 2
							},
							grid : {
								show : true,
								stroke : "silver"
							},
							title : {
								label : "Resposta"
							},
							labels : lblY()
						}
					}
				});
				chart_update.addSeries(pontos(), {
					points : {
						size  : 6,
						color : function(d) {
							return [
								"#dc3545",
								"#e6e629",
								"#28a745",
								"#238DFC"
							][d.data.y];
						}
					},
					line : {
						show  : true,
						color : "#888",
						"stroke-width" : 2
					},
					tooltip : {
						label : function(d) {
							const data = luxon.DateTime.fromObject({ year: ano }).plus({ days: d.data.x }).toFormat('dd/MM');
							return data;
						}
					}
				});
				chart_update.draw();
				/*var lista = document.getElementsByTagName("path");
				for (var i = 0; i < lista.length; i++) lista[i].style.stroke = "silver";
				lista = document.getElementsByTagName("line");
				for (var i = 0; i < lista.length; i++) lista[i].style.stroke = "silver";*/
			});
		</script>
	</head>
	<body style = "overflow:hidden">
		<div id = "chart_update" style = "padding-right:20px"></div>
	</body>
</html>