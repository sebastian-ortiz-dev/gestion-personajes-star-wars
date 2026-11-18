<?php

namespace Tests\Feature;

use App\Models\Personaje;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PersonajesBordeTest extends TestCase
{
    use RefreshDatabase;

    private function datos(array $sobrescribir = []): array
    {
        return array_merge([
            'nombre' => 'Personaje de prueba',
            'altura' => 170,
            'masa' => 70,
            'color_cabello' => 'negro',
            'color_ojos' => 'cafe',
            'genero' => 'masculino',
            'planeta_origen' => 'Tatooine',
        ], $sobrescribir);
    }

    public function test_acepta_los_limites_superiores_del_formulario(): void
    {
        $respuesta = $this->post('/personajes', $this->datos([
            'nombre' => str_repeat('a', 100),
            'altura' => 300,
            'masa' => 500,
            'color_cabello' => str_repeat('b', 100),
            'color_ojos' => str_repeat('c', 100),
            'planeta_origen' => str_repeat('d', 100),
        ]));

        $respuesta->assertSessionHasNoErrors();
        $this->assertDatabaseCount('personajes', 1);
    }

    public function test_rechaza_los_limites_excedidos(): void
    {
        $respuesta = $this->post('/personajes', $this->datos([
            'nombre' => str_repeat('a', 101),
            'altura' => 301,
            'masa' => 501,
            'color_cabello' => str_repeat('b', 101),
        ]));

        $respuesta->assertSessionHasErrors(['nombre', 'altura', 'masa', 'color_cabello']);
        $this->assertDatabaseCount('personajes', 0);
    }

    public function test_rechaza_altura_y_masa_en_cero(): void
    {
        $respuesta = $this->post('/personajes', $this->datos([
            'altura' => 0,
            'masa' => 0,
        ]));

        $respuesta->assertSessionHasErrors(['altura', 'masa']);
        $this->assertDatabaseCount('personajes', 0);
    }

    public function test_genero_es_obligatorio(): void
    {
        $respuesta = $this->post('/personajes', $this->datos(['genero' => '']));

        $respuesta->assertSessionHasErrors('genero');
        $this->assertDatabaseCount('personajes', 0);
    }

    public function test_pagina_diez_registros(): void
    {
        for ($i = 1; $i <= 11; $i++) {
            Personaje::create($this->datos(['nombre' => "Personaje {$i}"]));
        }

        $this->get('/personajes')->assertInertia(fn (Assert $page) => $page
            ->component('personajes/index')
            ->where('personajes.per_page', 10)
            ->has('personajes.data', 10)
            ->where('personajes.total', 11));

        $this->get('/personajes?page=2')->assertInertia(fn (Assert $page) => $page
            ->has('personajes.data', 1));
    }

    public function test_listado_ordena_del_mas_nuevo_al_mas_viejo(): void
    {
        Personaje::create($this->datos(['nombre' => 'Primero']));
        Personaje::create($this->datos(['nombre' => 'Segundo']));
        Personaje::create($this->datos(['nombre' => 'Tercero']));

        $this->get('/personajes')->assertInertia(fn (Assert $page) => $page
            ->where('personajes.data.0.nombre', 'Tercero'));
    }

    public function test_filtros_de_nombre_y_genero_se_combinan(): void
    {
        Personaje::create($this->datos(['nombre' => 'Luke Skywalker', 'genero' => 'masculino']));
        Personaje::create($this->datos(['nombre' => 'Leia Organa', 'genero' => 'femenino']));

        $this->get('/personajes?buscar=Luke&genero=masculino')
            ->assertOk()
            ->assertSee('Luke Skywalker')
            ->assertDontSee('Leia Organa');

        $this->get('/personajes?buscar=Luke&genero=femenino')
            ->assertOk()
            ->assertDontSee('Luke Skywalker');
    }

    public function test_la_busqueda_coincide_solo_con_el_inicio_del_nombre(): void
    {
        Personaje::create($this->datos(['nombre' => 'Luke Skywalker']));

        $this->get('/personajes?buscar=uke')
            ->assertOk()
            ->assertDontSee('Luke Skywalker');
    }

    public function test_importar_guarda_null_cuando_la_api_devuelve_unknown(): void
    {
        Http::fake([
            '*' => Http::response([
                'name' => 'R2-D2',
                'height' => 'unknown',
                'mass' => 'unknown',
                'hair_color' => 'n/a',
                'eye_color' => 'red',
                'gender' => 'none',
            ]),
        ]);

        $this->post('/personajes/importar', ['swapi_id' => 3]);

        $this->assertDatabaseHas('personajes', [
            'swapi_id' => 3,
            'altura' => null,
            'masa' => null,
            'genero' => 'n/a',
        ]);
    }

    public function test_importar_redondea_la_masa_decimal(): void
    {
        Http::fake([
            '*' => Http::response([
                'name' => 'Personaje decimal',
                'height' => '180',
                'mass' => '72.6',
                'hair_color' => 'none',
                'eye_color' => 'blue',
                'gender' => 'female',
            ]),
        ]);

        $this->post('/personajes/importar', ['swapi_id' => 4]);

        $this->assertDatabaseHas('personajes', [
            'swapi_id' => 4,
            'masa' => 73,
            'genero' => 'femenino',
        ]);
    }

    public function test_importar_rechaza_ids_fuera_de_rango(): void
    {
        Http::fake();

        $this->post('/personajes/importar', ['swapi_id' => 0])
            ->assertSessionHasErrors('swapi_id');

        $this->post('/personajes/importar', ['swapi_id' => 84])
            ->assertSessionHasErrors('swapi_id');

        $this->assertDatabaseCount('personajes', 0);
    }

    public function test_eliminar_un_personaje_inexistente_no_provoca_error_500(): void
    {
        $respuesta = $this->delete('/personajes/999');

        $respuesta->assertRedirect('/personajes');
        $respuesta->assertSessionHas('error');
    }
}
