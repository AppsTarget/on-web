<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Encaminhantes extends Model
{
    protected $table = 'enc2_encaminhantes';
    protected $fillable = [
        'id',
        'id_pessoa',
        'nome_fantasia',
        'documento',
        'documento_estado',
        'tpdoc',
        'telefone',
        'lixeira'
    ];
}