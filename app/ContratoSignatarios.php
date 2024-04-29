<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContratoSignatarios extends Model
{
    protected $table = "contrato_signatarios";
    protected $fillable = [
        "id",
        "token",
        "sign_url",
        "status",
        "name",
        "email",
        "phone_country",
        "phone_number",
        "times_viewed",
        "last_viewed_at"
    ];
}
