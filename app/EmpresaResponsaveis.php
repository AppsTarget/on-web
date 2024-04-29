<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmpresaResponsaveis extends Model {
    protected $table = "empresa_responsaveis";
    protected $fillable = [
        'id',
        'id_emp',
        'id_responsavel',
        'lixeira'
    ];
}