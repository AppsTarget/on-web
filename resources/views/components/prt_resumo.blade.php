<div class="container card min-h-100 mb-5">
	<div class="row mt-3">
        <h5 class="col m-0 pt-2 pl-4 btn-link-target">Resumo</h5>

        <div class="col-4">
            <input id="resumo-filtro-profissional" class="form-control" autocomplete="off" type="text" placeholder="Pesquisar por Profissional">
        </div>

        <div id="resumo-filtro-pai" class="dropdown my-auto mr-4">
            <button class="btn btn-sm btn-link-target dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="my-icon fas fa-filter"></i>
            </button>
            <div id="resumo-filtro" class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <div class="custom-control custom-checkbox">
                    <input id="resumo-filtro-agenda" class="custom-control-input" type="checkbox" data-filtro="agenda" checked>
                    <label for="resumo-filtro-agenda" class="custom-control-label">Agenda</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input id="resumo-filtro-anexos" class="custom-control-input" type="checkbox" data-filtro="anexos" checked>
                    <label for="resumo-filtro-anexos" class="custom-control-label">Anexos</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input id="resumo-filtro-evolucao" class="custom-control-input" type="checkbox" data-filtro="evolucao" checked>
                    <label for="resumo-filtro-evolucao" class="custom-control-label">Evolução</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input id="resumo-filtro-evolucao-pedido" class="custom-control-input" type="checkbox" data-filtro="evolucao_pedido;evolucao_pedido_finalizado" checked>
                    <label for="resumo-filtro-evolucao-pedido" class="custom-control-label">Evolução do procedimento</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input id="resumo-filtro-plano-tratamento" class="custom-control-input" type="checkbox" data-filtro="pedido" checked>
                    <label for="resumo-filtro-plano-tratamento" class="custom-control-label">Contrato</label>
                </div>
                <div class="custom-control custom-checkbox">
                    <input id="resumo-filtro-prescricao" class="custom-control-input" type="checkbox" data-filtro="prescricao" checked>
                    <label for="resumo-filtro-prescricao" class="custom-control-label">Prescrição</label>
                </div>
            </div>
        </div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<ul id="table-prontuario-resumo" class="timeline">
			</ul>
		</div>
	</div>
</div>
