<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Etiqueta extends Model
{
    protected $table = "etiqueta";
    protected $fillable = [
        'id',
        'id_emp',
        'descr',
        'cor'
    ];
}