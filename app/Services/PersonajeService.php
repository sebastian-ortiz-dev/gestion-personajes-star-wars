<?php

namespace App\Services;

use App\Models\Personaje;
use App\Repositories\PersonajeRepositoryInterface;
use App\Helpers\Helpers;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PersonajeService 
{
    public function __construct(private readonly PersonajeRepositoryInterface $personajes, private readonly Helpers $helper)
    {   
    }

    public function listar(string $buscar){
        return $this->personajes->paginar($buscar);
    }

    public function agregar(array $datos){
        try{
            $this->personajes->crear($datos);
            return true;
        }catch(Exception $excep){
            return 'Ocurrio un error al crear el personaje.';
        }
    }

    public function importarSwapi(int $id){
        // valida en db si ya existe
        $existe = $this->personajes->existeSwapiId($id);

        if($existe){
            return "El id ya fue importado";
        }

        try{
            $response = Http::timeout(10)->get("https://swapi.py4e.com/api/people/{$id}/");
        }catch(Exception $e){
            return "Error al consultar la Api";
        }

        if($response->status() == 404){
            return "No se encontro el personaje con ese id";
        }

        if($response->failed()){
            return 'Ocurrio un error al consultar la Api';
        }

        // obtenemos la respuesta
        $personajeApi = $response->json();

        $AlturaMasa = $this->helper->verificarAlturaMasa($personajeApi["height"], $personajeApi["mass"]);

        $mapaGenero = [
            'male'   => 'masculino',
            'female' => 'femenino',
            'n/a'  => 'n/a',
        ];

        $genero = $mapaGenero[strtolower($personajeApi['gender'] )] ?? 'n/a';

        // mapeamos
        $mapear = ["nombre" => $personajeApi['name'], "altura" => $AlturaMasa[0], "masa" => $AlturaMasa[1], "color_cabello" => $personajeApi['hair_color'], 'color_ojos' => $personajeApi['eye_color'], "genero" => $genero, "origen_swapi" => true, "swapi_id" => $id];
        
        $this->personajes->crear($mapear);

        return true;
    }
}
