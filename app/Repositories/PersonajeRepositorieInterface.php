<?php 

namespace App\Repositories;

use App\Models\Reserva;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PersonajesRepositorieInterface{
    public function paginar(string $buscar = '', int $porPagina = 10): LengthAwarePaginator;

    public function crear(array $datos): Personaje;

    public function actualizar(Personaje $personaje, array $datos): Personaje;

    public function eliminar(Personaje $personaje): void;

    public function buscarPorSwapiId(int $swapiId): ?Personaje;

    public function existeSwapiId(int $swapiId): bool; 
}