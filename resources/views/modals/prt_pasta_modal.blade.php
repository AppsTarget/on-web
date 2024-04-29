<!-- Modal -->
<div class="modal fade" id="criarPastaModal" aria-labelledby="criarPastaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="criarPastaModalLabel">Criar Nova Pasta</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/saude-beta/pastas/criar" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="container">
                        <input type="hidden" id = "pasta_id_paciente"     name="id_paciente"     value="{{ $pessoa->id }}">
                        <input type="hidden" id = "pasta_id_profissional" name="id_profissional" value="{{ Auth::user()->id_profissional }}">
                        <div class="row">
                            <div class="form-group w-100">
                                <label for="nome_pasta" class="custom-label-form">Nome</label>
                                <input id="nome_pasta" name="nome" class="form-control" style = "text-transform:none !important" type="text" placeholder="Nova pasta" required />
                            </div>
                        </div>
                        <div class="row">
                            <button class="btn btn-target my-3 mx-auto px-5" type="submit">Criar</button>
                        </div>
                    </div> 
                </form>
            </div>
        </div>
    </div>
</div>