<!-- Modal -->
<div class="modal fade" id="criarAnexoModal" aria-labelledby="criarAnexoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title header-color header-color" id="criarAnexoModalLabel">Anexar Arquivo</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-add-anexo" action="/saude-beta/anexos/salvar" method="POST" enctype="multipart/form-data" onsubmit="salvar_anexo(event); return false;">
                    @csrf
                    <div class="container">
                        <input type="hidden" id="id_paciente" name="id_paciente"     value="{{ $pessoa->id }}">
                        <input type="hidden" name="id_profissional" value="{{ Auth::user()->id_profissional }}">
                        <div class="row">
                            <div class="form-group">
                                <label for="arquivo">Arquivo</label>
                                <input id="arquivo" name="arquivo" class="form-control-file" type="file" required>
                            </div>

                            <div class="form-group" style = "position:absolute;right:2rem;top:2.6rem">
                                <table>
                                    <tr>
                                        <td>Pasta:&nbsp;&nbsp;</td>
                                        <td>
                                            <select name="pasta" class="custom-select">
                                                <option value="1">Laboratório</option>
                                                <option value="2">Imagem</option>
                                                <option value="3">Testes IEC Avançados</option>
                                                <option value="4">Cardápios</option>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group w-100">
                                <label for="obs" class="custom-label-form">Descrição</label>
                                <textarea id="obs" name="obs" class="form-control" type="text" placeholder="..." required></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <button class="btn btn-target my-3 mx-auto px-5" type="submit">Anexar</button>
                        </div>
                    </div> 
                </form>
            </div>
        </div>
    </div>
</div>