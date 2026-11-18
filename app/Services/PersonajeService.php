<?php

namespace App\Services;

use App\Models\Personaje;
use App\Repositories\PersonajeRepositoryInterface;
use App\Helpers\Helpers;
use Exception;
use App\Exceptions\Exception\PersonajesException;
use Illuminate\Support\Facades\Http;

class PersonajeService 
{
    public function __construct(private readonly PersonajeRepositoryInterface $personajes, private readonly Helpers $helper)
    {   
    }

    public function listar(string $buscar, string $genero){
        return $this->personajes->paginar($buscar, $genero);
    }

    public function agregar(array $datos){
        try{
            $this->personajes->crear($datos);
            return;
        }catch(Exception $excep){
            throw new PersonajesException('No se pudo crear el personaje. Inténtalo nuevamente.');
        }
    }

    public function obtenerPorId(int $id){
        return $this->personajes->buscarPorId($id);
    } 

    public function actualizarPersonaje(array $datos, int $id){
        $afectado = $this->personajes->actualizar($id, $datos);

        if($afectado === 0){
            throw new PersonajesException("No se pudo guardar los cambios. Inténtalo nuevamente.");
        }

        return;
    }

    public function eliminarPersonaje(int $id){
        $afectado = $this->personajes->eliminar($id);

        if($afectado === 0){
            throw new PersonajesException("No se pudo eliminar el personaje. Recarga la página e inténtalo otra vez.");
        }

        return;
    }

    public function importarSwapi(int $id){
        $existe = $this->personajes->existeSwapiId($id);

        if($existe){
            throw new PersonajesException("Ese personaje ya está importado.");
        }

        try{
            $response = Http::timeout(10)->get("https://swapi.py4e.com/api/people/{$id}/");
        }catch(Exception $e){
            throw new PersonajesException("No se pudo conectar con SWAPI. Revisa tu conexión e inténtalo de nuevo.");
        }

        if($response->status() == 404){
            throw new PersonajesException("No existe un personaje con el ID {$id} en SWAPI. Verifica que esté entre 1 y 83.");
        }

        if($response->failed()){
            throw new PersonajesException('SWAPI respondió con un error. Inténtalo más tarde.');
        }

        $personajeApi = $response->json();

        $AlturaMasa = $this->helper->verificarAlturaMasa($personajeApi["height"], $personajeApi["mass"]);

        $mapaGenero = [
            'male'   => 'masculino',
            'female' => 'femenino',
            'n/a'  => 'n/a',
        ];

        $genero = $mapaGenero[strtolower($personajeApi['gender'] )] ?? 'n/a';

        $mapear = ["nombre" => $personajeApi['name'], "altura" => $AlturaMasa[0], "masa" => $AlturaMasa[1], "color_cabello" => $personajeApi['hair_color'], 'color_ojos' => $personajeApi['eye_color'], "genero" => $genero, "origen_swapi" => true, "swapi_id" => $id];
        
        try{
            $this->personajes->crear($mapear);
        }catch(Exception $e){
            throw new PersonajesException('No se pudo crear el personaje importado. Inténtalo nuevamente.');
        }

        return;
    }
}
