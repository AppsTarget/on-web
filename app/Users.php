<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = "users";
    protected $fillable = [
        'id',
        'id_emp',
        'id_profissional',
        'name',
        'email',
        'email_verified_at',
        'sa',
        'password',
        'remember_token'
    ];
}
