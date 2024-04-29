<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinanceiraTaxas extends Model
{
    protected $table = "financeira_taxas";
    protected $fillable = [
        'id',
        'id_financeira',
        'rede_adquirente',
        'max_parcela',
        'taxa'
    ];
}
