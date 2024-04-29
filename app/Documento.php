<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = "documento";
    protected $fillable = [
        'id',
        'id_emp',
        'id_paciente',
        'id_profissional',
        'id_doc_modelo',
        'corpo',
        'pasta'
    ];
}
