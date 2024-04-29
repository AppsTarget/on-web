<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentoModelo extends Model
{
    protected $table = "documento_modelo";
    protected $fillable = [
        'id',
        'id_emp', 
        'titulo',
        'corpo',
        'ativo'
    ];
}
