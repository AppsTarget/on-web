<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OldModalidades extends Model
{
    protected $table = "old_modalidades";
    protected $fillable = [ 
        "id", 
        "id_novo", 
        "descr", 
        "descritivo", 
        "area_modalidade", 
        "quantidade_pessoas", 
        "ativo", 
        "tipo_comiss", 
        "ate1", 
        "valor1", 
        "de2", 
        "ate2", 
        "valor2", 
        "de3", 
        "ate3", 
        "valor3", 
        "de4", 
        "ate4", 
        "valor4", 
        "de5", 
        "valor5"
    ];
}
