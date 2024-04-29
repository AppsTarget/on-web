<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificacaoVisualizacoes extends Model
{
    protected $table = "notificacao_visualizacoes";
    protected $fillable = [
        'id',
        'id_user',
        'id_notificacao',
        'visualizado',
        'lixeira'
    ];
}
