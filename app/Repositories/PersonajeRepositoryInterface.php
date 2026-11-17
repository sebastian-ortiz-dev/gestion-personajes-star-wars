<?php

namespace App\Repositories;

use App\Models\Personaje;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PersonajeRepositoryInterface
{
    public function paginar(string $buscar = '', int $porPagina = 10): LengthAwarePaginator;

    public function crear(array $datos): ?Personaje;

    public function actualizar(int $id, array $datos): int;

    public function eliminar(int $id): int;

    public function buscarPorId(int $id): ?Personaje;

    public function existeSwapiId(int $swapiId): bool;
}
