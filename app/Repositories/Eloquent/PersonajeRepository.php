<?php

namespace App\Repositories\Eloquent;

use App\Models\Personaje;
use App\Repositories\PersonajeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PersonajeRepository implements PersonajeRepositoryInterface
{
    public function paginar(string $buscar = '', int $porPagina = 10): LengthAwarePaginator
    {
        return Personaje::query()
            ->when($buscar != '', fn ($query) => $query->where('nombre', 'like', "{$buscar}%"))
            ->orderBy('id')
            ->paginate($porPagina)
            ->withQueryString();
    }

    public function crear(array $datos): Personaje
    {
        return Personaje::create($datos);
    }

    public function actualizar(Personaje $personaje, array $datos): Personaje
    {
        $personaje->update($datos);

        return $personaje->refresh();
    }

    public function eliminar(Personaje $personaje): void
    {
        $personaje->delete();
    }

    public function buscarPorSwapiId(int $swapiId): ?Personaje
    {
        return Personaje::where('swapi_id', $swapiId)->first();
    }

    public function existeSwapiId(int $swapiId): bool
    {
        return Personaje::where('swapi_id', $swapiId)->exists();
    }
}
