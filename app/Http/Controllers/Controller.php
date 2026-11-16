<?php

namespace App\Http\Controllers;

use App\Exceptions\SwapiException;
use App\Http\Requests\ImportPersonajeRequest;
use App\Http\Requests\PersonajeRequest;
use App\Models\Personaje;
use App\Repositories\PersonajeRepositoryInterface;
use App\Services\PersonajeService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PersonajesController extends Controller
{
    public function __construct(private readonly PersonajeService $service) 
    {
    }

    public function index(Request $request){
        $buscar = trim((string) $request->query('buscar', ''));

        return Inertia::render('personajes/index', [
            'personajes' => $this->service->listar($buscar),
            'filtros' => ['buscar' => $buscar],
        ]);
    }

}
