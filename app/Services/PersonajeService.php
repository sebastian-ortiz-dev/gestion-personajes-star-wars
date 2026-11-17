<?php

namespace App\Services;

use App\Models\Personaje;
use App\Repositories\PersonajeRepositoryInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PersonajeService 
{
    public function __construct(private readonly PersonajeRepositoryInterface $personajes)
    {   
    }

    public function listar(string $buscar){
        return $this->personajes->paginar($buscar);
    }

    public function importarSwapi(int $id){
        // valida en db si ya existe
        $existe = $this->personajes->existeSwapiId($id);

        if($existe){
            return "El id ya fue importado";
        }

        $response = Http::timeout(10)->get("https://swapi.py4e.com/api/people/{$id}/");

        if ($response->failed()) {
            error_log(print_r("Error al consultar la Api", true));
            return "Error al consultar la Api";
        }

        $mapaGenero = [
            'male'   => 'masculino',
            'female' => 'femenino',
            'n/a'    => 'n/a',
        ];

        // obtenemos la respuesta
        $personajeApi = $response->json();

        // mapeamos
        $mapear = ["nombre" => $personajeApi['name'], "altura" => (int) $personajeApi['height'] ?? null, "masa" => (int) $personajeApi['mass'] ?? null, "color_cabello" => $personajeApi['hair_color'], 'color_ojos' => $personajeApi['eye_color'], "genero" => $mapaGenero[strtolower($personajeApi['gender'])], "origen_swapi" => true, "swapi_id" => $id];
        
        $this->personajes->crear($mapear);

        return true;
    }
}
