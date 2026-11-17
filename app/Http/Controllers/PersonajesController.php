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
    {}

    public function index(Request $request){
        $buscar = trim((string) $request->query('buscar', ''));

        $personajes = $this->service->listar($buscar);

        return Inertia::render('personajes/index', [
            'personajes' => $personajes,
            'filtros' => ['buscar' => $buscar],
        ]);
    }

    public function crear(){
        return Inertia::render('personajes/crear');
    }

    public function agregar(PersonajeRequest $request){
        $datos = $request->validated();
        
        $success = $this->service->agregar($datos);

        if($success === true){
            return to_route('personajes.index')->with('success', 'Personaje importado correctamente.');
        }else{
            return to_route('personajes.index')->with("error", $success);
        }
    }

    public function importar(ImportPersonajeRequest $request){
        $datos = $request->validated();
        $id = $datos['swapi_id'];

        $success = $this->service->importarSwapi($id);

        if($success === true){
            return to_route('personajes.index')->with('success', 'Personaje importado correctamente.');
        }else{
            return back()->with("error", $success);
        }
    }
}
