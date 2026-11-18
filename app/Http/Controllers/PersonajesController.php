<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportPersonajeRequest;
use App\Http\Requests\PersonajeRequest;
use App\Services\PersonajeService;
use App\Exceptions\Exception\PersonajesException;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PersonajesController extends Controller
{
    public function __construct(private readonly PersonajeService $service) 
    {}

    public function index(Request $request){
        $buscar = trim((string) $request->query('buscar', ''));
        $genero = trim((string) $request->query('genero', ''));
        $personajes = $this->service->listar($buscar, $genero);

        return Inertia::render('personajes/index', [
            'personajes' => $personajes,
            'filtros' => ['buscar' => $buscar, 'genero' => $genero],
        ]);
    }

    public function crear(){
        return Inertia::render('personajes/crear');
    }

    public function agregar(PersonajeRequest $request){
        try{
            $datos = $request->validated();
            $this->service->agregar($datos);
        }catch(PersonajesException $e){
            return to_route('personajes.index')->with("error", $e->getMessage());
        }

        return to_route('personajes.index')->with('success', 'Personaje creado correctamente.');
    }

    public function editar(int $id){
        $personaje = $this->service->obtenerPorId($id);

        if($personaje == null){
            return to_route('personajes.index')->with('error', 'No existe ese personaje');
        }

        return Inertia::render('personajes/editar',[
            'personaje' => $personaje,
        ]);
    }

    public function aplicarCambios(PersonajeRequest $request, $id){
        try{
            $datos = $request->validated();
        
            $this->service->actualizarPersonaje($datos,(int) $id);
        }catch(PersonajesException $e){
            return to_route('personajes.index')->with("error", $e->getMessage());
        }

        return to_route('personajes.index')->with('success', 'Personaje actualizado correctamente.');
    }

    public function borrar($id){
        try{
            $this->service-> eliminarPersonaje((int) $id);
        }catch(PersonajesException $e){
            return to_route('personajes.index')->with("error", $e->getMessage());
        }

        return to_route('personajes.index')->with('success', 'Personaje Eliminado.');
    }

    public function importar(ImportPersonajeRequest $request){
        try{
            $datos = $request->validated();
            $id = $datos['swapi_id'];

            $success = $this->service->importarSwapi($id);
        }catch(PersonajesException $e){
            return back()->with("error", $e->getMessage());
        }

        return to_route('personajes.index')->with('success', 'Personaje importado correctamente.');
    }
}
