<?php

namespace App\Services;

use App\Models\Personaje;
use App\Repositories\PersonajeRepositoryInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class PersonajeService 
{
    public function __construct(private readonly PersonajeRepositoryInterface $personajes)
    {   
    }

    public function listar(string $buscar){
        return $this->personajes->paginar($buscar);
    }

}
