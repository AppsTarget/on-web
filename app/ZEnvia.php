<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZEnvia extends Model
{
    protected $table = "zenvia";
    protected $fillable = [
        "id",
        "id_agendamento",
        "text",
        "direction",
        "celular",
        "selected"
    ];
}
