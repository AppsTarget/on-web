<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgendaConfirmacao extends Model
{
    protected $table = "agenda_confirmacao";
    protected $fillable = [
        'id',
        'id_emp',
        'descr',
        'valor_total',
        'lixeira'
    ];
}