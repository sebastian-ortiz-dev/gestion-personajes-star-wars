# Sistema de Gestión de Personajes Star Wars

Aplicación web para administrar personajes de Star Wars: listado con paginación y búsqueda, alta, edición y eliminación de registros, más importación de personajes desde la API pública [SWAPI](https://swapi.py4e.com/documentation).

Construida con Laravel, Inertia.js, Vue 3 (Composition API) y Tailwind CSS.

## Información del Candidato

- **Nombre:** Sebastian Ortiz.
- **Email:** sebastian090905@gmail.com.
- **Teléfono:** (+58) 414-325-02-36.

## Requisitos del Sistema

- PHP 8.2 o superior
- Composer 2
- Node.js 20.19 o superior (requerido por Vite 7)
- MySQL 8 o superior

## Instalación

1. Clonar el repositorio y entrar al proyecto:

```bash
git clone [tu-repo]
cd [nombre-proyecto]
```

2. Instalar dependencias de PHP y de Node:

```bash
composer install
npm install
```

3. Configurar el entorno:

```bash
cp .env.example .env
php artisan key:generate
```

En Windows el primer comando es `copy .env.example .env`.

Editar `.env` con las credenciales de MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE="nombre de tu db"
DB_USERNAME="nombre de usuario"
DB_PASSWORD=
```

4. Ejecutar las migraciones (incluye las tablas de sesiones, caché y colas que usa el proyecto):

```bash
php artisan migrate
```

5. Compilar los assets:

```bash
npm run build
```

Para desarrollo con recarga en caliente: `npm run dev`.

6. Iniciar el servidor:

```bash
php artisan serve
```

7. Acceder a:

```
http://localhost:8000/personajes
```

La raíz `/` redirige a `/personajes`.

## Funcionalidades Implementadas

### Obligatorias

- CRUD completo de personajes (listar, crear, editar, eliminar).
- Tabla con nombre, altura, masa, género y origen (Manual / SWAPI).
- Paginación de 10 registros por página, conservando los filtros en la URL.
- Formulario de creación con validación en frontend y errores del backend bajo cada campo.
- Formulario de edición precargado con los datos actuales.
- Eliminación con confirmación previa.
- Importación desde SWAPI mediante modal, con campo de ID (1–83), spinner de carga y mensajes de éxito o error.
- Validaciones en backend con Form Requests.
- Componentes Vue reutilizables: `PersonajeForm.vue` e `ImportModal.vue`.
- Diseño responsive (tabla con scroll horizontal en móvil y formularios de una columna).

### Bonus implementados

- **Service Layer:** `PersonajeService` concentra la lógica de negocio.
- **Repository Pattern:** interfaz `PersonajeRepositoryInterface` con implementación Eloquent, resuelta por inyección de dependencias.
- **Búsqueda por nombre** en el listado.

## Rutas

| Método | Ruta | Acción | Nombre |
| --- | --- | --- | --- |
| GET | `/personajes` | Listado paginado | `personajes.index` |
| GET | `/personajes/crear` | Formulario de creación | `personajes.crear` |
| POST | `/personajes` | Guardar personaje | `personajes.agregar` |
| GET | `/personajes/{id}/editar` | Formulario de edición | `personajes.editar` |
| PUT | `/personajes/{id}` | Actualizar personaje | `personajes.aplicarCambios` |
| DELETE | `/personajes/{id}` | Eliminar personaje | `personajes.borrar` |
| POST | `/personajes/importar` | Importar desde SWAPI | `personajes.importar` |

## Estructura del Proyecto

```
app/
├── Helpers/Helpers.php                  Normalización de altura y masa de SWAPI
├── Http/
│   ├── Controllers/PersonajesController.php
│   ├── Middleware/HandleInertiaRequests.php   Comparte los mensajes flash
│   └── Requests/
│       ├── PersonajeRequest.php          Validación de crear y editar
│       └── ImportPersonajeRequest.php    Validación del ID de SWAPI
├── Models/Personaje.php
├── Providers/AppServiceProvider.php      Binding del repositorio
├── Repositories/
│   ├── PersonajeRepositoryInterface.php
│   └── Eloquent/PersonajeRepository.php
└── Services/PersonajeService.php

resources/js/
├── Components/
│   ├── PersonajeForm.vue                 Formulario compartido por crear y editar
│   └── ImportModal.vue                   Modal de importación desde SWAPI
└── Pages/personajes/
    ├── index.vue
    ├── crear.vue
    └── editar.vue
```

## Tecnologías

- PHP 8.2
- Laravel 12
- Vue.js 3.5 (Composition API con `<script setup>`)
- Inertia.js 3
- Tailwind CSS 4
- Vite 7
- MySQL

## Notas

**Campos importados.** SWAPI devuelve más información de la que pide el esquema. Solo se mapean seis campos (`name`, `height`, `mass`, `hair_color`, `eye_color`, `gender`); `planeta_origen` se guarda en `null` porque el enunciado no incluye el planeta en el mapeo y obtenerlo requiere una segunda consulta a `homeworld`.

**Normalización de masa y altura.** SWAPI entrega ambos valores como texto y no siempre como enteros: `"72.2"`, `"unknown"`, `"n/a"` y valores con separador de miles como `"1,358"` (Jabba). `Helpers::verificarAlturaMasa()` elimina las comas, valida que el valor sea numérico, redondea y convierte a `int`; cualquier valor no numérico queda en `null`. Por eso un `72.2` se almacena como `72`: la columna es entera y el redondeo es intencional.

**Género.** El esquema solo admite `masculino`, `femenino` y `n/a`. SWAPI devuelve además `hermaphrodite` (Jabba) y `none` (droides). Todo valor distinto de `male` o `female` se normaliza a `n/a`.

**Límites de validación e importación.** El formulario manual limita `masa` a 500 kg y `altura` a 300 cm. La importación no pasa por `PersonajeRequest`, solo por `ImportPersonajeRequest`, que valida el ID; por eso un personaje como Jabba puede almacenarse con `masa = 1358`. Es una inconsistencia consciente: se prioriza reflejar el dato real de SWAPI y el límite del formulario protege la carga manual.

**Duplicados.** Antes de consultar la API se verifica si el `swapi_id` ya existe, y la validación del request también lo comprueba con `unique`. La columna tiene índice `unique`.

**Errores de SWAPI.** Las peticiones usan un timeout de 10 segundos y se distinguen cuatro casos con mensajes propios: sin conexión, respuesta 404 (ID inexistente), respuesta con error HTTP y personaje ya importado. Ningún fallo de la API produce un error 500 en la aplicación.

**Búsqueda.** Filtra por coincidencia al inicio del nombre (`LIKE 'texto%'`), no en cualquier posición. Se aplica junto a la paginación mediante `withQueryString()`.

## Verificación Manual

Recorrido rápido para comprobar el funcionamiento:

1. `/personajes` muestra la tabla paginada y el botón **Importar desde SWAPI**.
2. **Crear Nuevo** → guardar con el formulario vacío muestra los errores; con datos válidos aparece el mensaje de éxito y el registro.
3. Editar un registro y guardar: los cambios se reflejan en el listado.
4. Eliminar: pide confirmación y borra el registro.
5. Importar el ID `1` (Luke Skywalker): se crea con badge SWAPI.
6. Importar el mismo ID otra vez: mensaje de duplicado, sin crear un registro nuevo.
7. Importar el ID `16` (Jabba): masa `1358`, género `n/a`.
8. Importar el ID `99`: mensaje de ID inexistente.
9. Apagar la red e importar: mensaje de error de conexión.

## DECISIONES TÉCNICAS

### 1. Organización del código backend

El controlador es delgado: recibe el Form Request, delega en `PersonajeService` y devuelve la respuesta de Inertia o el redirect con el mensaje flash. La lógica de negocio está en `PersonajeService`, que depende de `PersonajeRepositoryInterface` (implementada por `Eloquent\PersonajeRepository`) y de `Helpers` para la normalización numérica. La interfaz se resuelve en `AppServiceProvider`, así que el service nunca conoce Eloquent.

Usé Service + Repository porque permite sustituir la fuente de datos sin tocar el controlador y deja el service testeable con un mock. La validación queda en Form Requests y el acceso a datos en un único punto, de modo que las consultas no se repiten entre `index`, `editar` e `importar`.

### 2. Componentes Vue

Creé dos:

- **`PersonajeForm.vue`**: los siete campos del personaje, los errores de validación y los botones de acción. Recibe el objeto `useForm()` como prop y emite `guardar`; la página decide si hace `post` o `put`. Un prop `modo` cambia el texto del botón (**Guardar** / **Actualizar**).
- **`ImportModal.vue`**: el modal con el campo de ID, el spinner y los mensajes. Recibe `mostrar` y emite `cerrar`, y maneja su propio `useForm` porque su envío es independiente del listado.

Son reutilizables porque no dependen de la página: no acceden a props del controlador ni a rutas concretas, toda la variación entra por props y toda la acción sale por eventos. `PersonajeForm` se usa en dos páginas y `ImportModal` podría moverse a cualquier otra vista sin cambios.

### 3. Manejo de errores de SWAPI

La importación se valida primero en dos capas: `ImportPersonajeRequest` exige un entero entre 1 y 83 y `unique` sobre `swapi_id`, y el service consulta `existeSwapiId()` antes de gastar una petición. Después, la llamada usa `Http::timeout(10)` dentro de `try/catch`, y se distinguen los casos: excepción de conexión o timeout, respuesta 404, respuesta con error HTTP (`failed()`) y cualquier otro. Cada caso devuelve un mensaje en español que el controlador envía como flash; el listado lo muestra en un banner y el modal cierra su estado de carga. Ningún error de la API rompe la vista ni devuelve un 500.

### 4. Validaciones

Están en dos Form Requests: `PersonajeRequest` para crear y editar (nombre obligatorio de 3 a 100, altura y masa enteras dentro de rango, género en la lista, textos de máximo 100) y `ImportPersonajeRequest` para el ID de SWAPI. Elegí Form Requests en lugar de validar en el controlador porque la regla queda declarada en un solo archivo, se reutiliza entre `store` y `update`, y el controlador solo recibe datos ya validados con `validated()`.

Ese `validated()` cumple una segunda función: filtra el payload. Aunque alguien inyecte `origen_swapi` o `swapi_id` en el POST, esos campos no llegan al modelo; los asigna el service en la importación. En el frontend hay validación básica (`required`, `minlength`, `maxlength`, `min`, `max`) solo como ayuda visual: la fuente de verdad es el backend, y los errores se muestran bajo cada campo con `form.errors`.

### 5. Mayor dificultad

La importación, por los datos de SWAPI. No son limpios y rompen el esquema en casos concretos:

- `mass` llega como texto y a veces con separador de miles: `"1,358"`. Un `(int)` directo devuelve `1`, y el error pasa desapercibido salvo que revises el registro. Lo detecté importando a Jabba.
- `mass` también trae decimales (`"72.2"`), y la columna es entera.
- `gender` incluye `hermaphrodite` (Jabba) y `none` (droides), que no existen en el `enum`, así que el insert falla en lugar de guardar un valor inválido.

Lo resolví centralizando la conversión en `Helpers::verificarAlturaMasa()` (quitar comas, comprobar `is_numeric`, redondear) y mapeando el género con un array más un `?? 'n/a'` como red de seguridad. Después de eso probé los casos límite: ID 1, 16, 83, 99, duplicado, `unknown` y formulario vacío.

### 6. Uso de Inteligencia Artificial

Sí, usé un asistente de IA como apoyo, no para generar la aplicación completa. En concreto:

- Revisión de la arquitectura al separar Controller, Service y Repository.
- Diagnóstico de errores de configuración al montar Vite e Inertia.
- Contraste del mapeo de SWAPI, sobre todo la masa con comas y los géneros fuera del `enum`.
- Revisión de redacción de los mensajes al usuario y de este README.

Validé cada aporte ejecutando el flujo completo en local y leyendo el código antes de aceptarlo: crear con errores, crear válido, editar, eliminar, importar ID 1, 16, 99 y un duplicado, y cortar la red para probar el timeout. Lo que no entendía lo reescribí o lo descarté; el código que entregué puedo explicarlo y modificarlo en vivo.

### 7. Mejoras futuras

- Pruebas automatizadas: feature tests para el CRUD y para la importación con `Http::fake()`, cubriendo 404, timeout y duplicado.
- Docker Compose con MySQL y la aplicación, para levantar todo con un comando.
- Seeder con personajes de ejemplo.
- Comando Artisan para importar un rango de IDs de SWAPI de una vez.
- Filtro por género y ordenamiento por columna en la tabla.
- Mensajes de validación en español con `lang/es`.
- Importar `planeta_origen` consultando `homeworld` en una segunda petición.
- Búsqueda con debounce en tiempo real en lugar de enviar el formulario.

### 8. Autoevaluación

**8/10.** Lo mejor es la separación de responsabilidades: el controlador no tiene lógica de negocio ni consultas, la validación está en un único lugar y la importación contempla los casos sucios de la API sin romper el flujo. También la normalización de datos, que es la parte que suele fallar en silencio.

No me pongo más porque faltan pruebas automatizadas y el manejo de errores es funcional pero mejorable: los mensajes del service son strings en lugar de excepciones tipadas, y no hay logs de las fallas de la API.

### 9. Tiempo invertido

Aproximadamente **[X] horas**, distribuidas en configuración del proyecto y autenticación de Inertia, backend (modelo, migración, repositorio, service, requests), frontend (listado, formularios, modal, responsive), importación de SWAPI y esta documentación.
