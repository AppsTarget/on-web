<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GradeHorario extends Model
{
    protected $table = "grade_horario";
    protected $fillable = [
        'id',
        'id_grade',
        'hora',
        'dia_semana'
    ];
}
