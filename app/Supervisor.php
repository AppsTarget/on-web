<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    protected $table = "supervisor";
    protected $fillable = [
        "id",
        "id_profissional",
        "id_emp",
        "lixeira"
    ];
}
