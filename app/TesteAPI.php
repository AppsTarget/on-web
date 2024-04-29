<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TesteAPI extends Model
{
    protected $table = "testeAPI";
    protected $fillable = [
        "id",
        "text"
    ];
}
