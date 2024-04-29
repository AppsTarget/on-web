<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IECPessoaResp extends Model
{
    protected $table = "IEC_pessoa_resp";
    protected $fillable = [
        'id',
        'id_pergunta',
        'descr',
        'created_at',
        'updated_at',
        'value'
    ];
}