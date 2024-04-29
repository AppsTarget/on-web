<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConvenioPessoa extends Model
{
    protected $table = "convenio_pessoa";
    protected $fillable = [
        'id',
        'id_paciente',
        'id_convenio',
        'num_convenio'
    ];
}
