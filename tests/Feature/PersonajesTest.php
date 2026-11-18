<?php

namespace Tests\Feature;

use App\Models\Personaje;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PersonajesTest extends TestCase
{
    use RefreshDatabase;

    private function datosValidos(array $sobrescribir = []): array
    {
        return array_merge([
            'nombre' => 'Luke Skywalker',
            'altura' => 172,
            'masa' => 77,
            'color_cabello' => 'rubio',
            'color_ojos' => 'azul',
            'genero' => 'masculino',
            'planeta_origen' => 'Tatooine',
        ], $sobrescribir);
    }

    public function test_listado_responde_ok(): void
    {
        Personaje::create($this->datosValidos());

        $this->get('/personajes')->assertOk();
    }

    public function test_crear_personaje_valido(): void
    {
        $respuesta = $this->post('/personajes', $this->datosValidos());

        $respuesta->assertRedirect('/personajes');
        $respuesta->assertSessionHas('success');

        $this->assertDatabaseHas('personajes', [
            'nombre' => 'Luke Skywalker',
            'origen_swapi' => false,
            'swapi_id' => null,
        ]);
    }

    public function test_crear_con_datos_invalidos_no_guarda(): void
    {
        $respuesta = $this->post('/personajes', [
            'nombre' => 'Lu',
            'altura' => 400,
            'masa' => 900,
            'genero' => 'otro',
        ]);

        $respuesta->assertSessionHasErrors(['nombre', 'altura', 'masa', 'genero']);

        $this->assertDatabaseCount('personajes', 0);
    }

    public function test_ignora_campos_de_swapi_inyectados_en_el_formulario(): void
    {
        $this->post('/personajes', $this->datosValidos([
            'origen_swapi' => true,
            'swapi_id' => 5,
        ]));

        $this->assertDatabaseHas('personajes', [
            'nombre' => 'Luke Skywalker',
            'origen_swapi' => false,
            'swapi_id' => null,
        ]);
    }

    public function test_actualizar_personaje(): void
    {
        $personaje = Personaje::create($this->datosValidos());

        $respuesta = $this->put("/personajes/{$personaje->id}", $this->datosValidos([
            'nombre' => 'Anakin Skywalker',
        ]));

        $respuesta->assertRedirect('/personajes');
        $respuesta->assertSessionHas('success');

        $this->assertDatabaseHas('personajes', [
            'id' => $personaje->id,
            'nombre' => 'Anakin Skywalker',
        ]);
    }

    public function test_eliminar_personaje(): void
    {
        $personaje = Personaje::create($this->datosValidos());

        $respuesta = $this->delete("/personajes/{$personaje->id}");

        $respuesta->assertRedirect('/personajes');
        $respuesta->assertSessionHas('success');

        $this->assertDatabaseMissing('personajes', ['id' => $personaje->id]);
    }

    public function test_editar_personaje_inexistente_redirecciona(): void
    {
        $this->get('/personajes/999/editar')->assertRedirect('/personajes');
    }

    public function test_filtrar_por_genero(): void
    {
        Personaje::create($this->datosValidos(['nombre' => 'Leia Organa', 'genero' => 'femenino']));
        Personaje::create($this->datosValidos(['nombre' => 'Luke Skywalker']));

        $this->get('/personajes?genero=femenino')
            ->assertOk()
            ->assertSee('Leia Organa')
            ->assertDontSee('Luke Skywalker');
    }

    public function test_filtrar_por_nombre(): void
    {
        Personaje::create($this->datosValidos(['nombre' => 'Leia Organa', 'genero' => 'femenino']));
        Personaje::create($this->datosValidos(['nombre' => 'Luke Skywalker']));

        $this->get('/personajes?buscar=Luke')
            ->assertOk()
            ->assertSee('Luke Skywalker')
            ->assertDontSee('Leia Organa');
    }

    public function test_importar_personaje_desde_swapi(): void
    {
        Http::fake([
            '*' => Http::response([
                'name' => 'Luke Skywalker',
                'height' => '172',
                'mass' => '77',
                'hair_color' => 'blond',
                'eye_color' => 'blue',
                'gender' => 'male',
            ]),
        ]);

        $respuesta = $this->post('/personajes/importar', ['swapi_id' => 1]);

        $respuesta->assertRedirect('/personajes');
        $respuesta->assertSessionHas('success');

        $this->assertDatabaseHas('personajes', [
            'swapi_id' => 1,
            'nombre' => 'Luke Skywalker',
            'altura' => 172,
            'masa' => 77,
            'genero' => 'masculino',
            'origen_swapi' => true,
        ]);
    }

    public function test_importar_normaliza_masa_con_coma_y_genero_desconocido(): void
    {
        Http::fake([
            '*' => Http::response([
                'name' => 'Jabba Desilijic Tiure',
                'height' => '175',
                'mass' => '1,358',
                'hair_color' => 'n/a',
                'eye_color' => 'orange',
                'gender' => 'hermaphrodite',
            ]),
        ]);

        $this->post('/personajes/importar', ['swapi_id' => 16]);

        $this->assertDatabaseHas('personajes', [
            'swapi_id' => 16,
            'masa' => 1358,
            'genero' => 'n/a',
        ]);
    }

    public function test_no_permite_importar_el_mismo_swapi_id(): void
    {
        Personaje::create($this->datosValidos([
            'swapi_id' => 1,
            'origen_swapi' => true,
        ]));

        Http::fake();

        $respuesta = $this->post('/personajes/importar', ['swapi_id' => 1]);

        $respuesta->assertSessionHasErrors('swapi_id');

        $this->assertDatabaseCount('personajes', 1);
    }

    public function test_importar_id_inexistente_muestra_mensaje(): void
    {
        Http::fake([
            '*' => Http::response([], 404),
        ]);

        $respuesta = $this->post('/personajes/importar', ['swapi_id' => 83]);

        $respuesta->assertSessionHas('error');

        $this->assertStringContainsString('83', session('error'));
        $this->assertDatabaseCount('personajes', 0);
    }

    public function test_importar_con_api_caida_muestra_mensaje(): void
    {
        Http::fake(fn () => throw new ConnectionException('Connection timed out'));

        $respuesta = $this->post('/personajes/importar', ['swapi_id' => 1]);

        $respuesta->assertSessionHas('error');

        $this->assertDatabaseCount('personajes', 0);
    }

    public function test_importar_con_error_http_muestra_mensaje(): void
    {
        Http::fake([
            '*' => Http::response([], 500),
        ]);

        $respuesta = $this->post('/personajes/importar', ['swapi_id' => 1]);

        $respuesta->assertSessionHas('error');

        $this->assertDatabaseCount('personajes', 0);
    }
}
