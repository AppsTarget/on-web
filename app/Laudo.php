<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Laudo extends Model
{
    protected $table = "laudo";
    protected $fillable = [
        'id',
        'id_pessoa',
        'id_prof',
        'grafico',
        'diagnostico',
        'dump',
        'created_at',
        'updated_at'
    ];
}