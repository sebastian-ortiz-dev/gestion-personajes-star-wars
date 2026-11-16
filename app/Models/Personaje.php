<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personaje extends Model
{
    protected $fillable = ['nombre', 'altura', 'masa', 'color_cabello', 'color_ojos', 'genero', 'planeta_origen'];

    protected function cast(): array {
        return[
            'created_at' => 'datetime',
            'update_at' => 'datetime',
        ];
    }
}
