<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Historico extends Model
{
    protected $table = 'historico';
    protected $fillable = [
        'id',
        'data',
        'acao',
        'created_by',
        'id_pessoa'
    ];
}