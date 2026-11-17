<?php

namespace App\Repositories\Eloquent;

use App\Models\Personaje;
use App\Repositories\PersonajeRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PersonajeRepository implements PersonajeRepositoryInterface
{
    public function __construct(private readonly Personaje $personaje)
    { 
    }

    public function paginar(string $buscar = '', int $porPagina = 10): LengthAwarePaginator
    {
        return $this->personaje::query()
            ->when($buscar != '', fn ($query) => $query->where('nombre', 'like', "{$buscar}%"))
            ->orderBy('id')
            ->paginate($porPagina)
            ->withQueryString();
    }

    public function crear(array $datos): ?Personaje
    {
        return $this->personaje::create($datos);
    }

    public function actualizar(int $id, array $datos): int
    {
        return $this->personaje->where('id', $id)->update($datos);
    }

    public function eliminar(int $id): int
    {
        return $this->personaje->where('id', $id)->delete();
    }

    public function buscarPorId(int $id): ?Personaje
    {
        return $this->personaje::where('id', $id)->first();
    }

    public function existeSwapiId(int $swapiId): bool
    {
        return $this->personaje::where('swapi_id', $swapiId)->exists();
    }
}
