<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersApp extends Model
{
    protected $table = "usersApp";
    protected $fillable = [
        'id',
        'id_emp',
        'id_pessoa',
        'email',
        'senha'
    ];
}
