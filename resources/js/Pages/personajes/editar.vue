<script setup>
import { computed, watch } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    personaje: Object,
});

const page = usePage();

const personajeObtenido = computed(() => props.personaje ?? []);

const form = useForm({
    nombre: personajeObtenido.nombre,
    altura: personajeObtenido.altura ?? '',
    masa: personajeObtenido.masa ?? '',
    color_cabello: personajeObtenido.color_cabello ?? '',
    color_ojos: personajeObtenido.color_ojos ?? '',
    genero: personajeObtenido.genero,
    planeta_origen: personajeObtenido.planeta_origen ?? '',
});

watch(
  () => props.personaje,
  (personajeObtenido) => {
    if (personajeObtenido) {
      form.nombre        = personajeObtenido.nombre ?? ''
      form.altura        = personajeObtenido.altura ?? ''
      form.masa          = personajeObtenido.masa ?? ''
      form.color_cabello = personajeObtenido.color_cabello ?? ''
      form.color_ojos    = personajeObtenido.color_ojos ?? ''
      form.genero        = personajeObtenido.genero ?? ''
    }
  },
  { immediate: true }
)

function actualizar(){
    form.put(`/personajes/${props.personaje.id}`);
}
</script>

<template>
    <div class="min-h-screen bg-slate-100 py-8">
        <div class="mx-auto max-w-3xl px-4">
            <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Editar personaje</h1>
                    <p class="text-sm text-slate-500">Actualice los datos del personaje</p>
                </div>

                <Link href="/personajes" class="text-sm font-medium bg-white w-35 p-2 rounded-md text-slate-600 hover:text-slate-900 hover:bg-gray-200">
                    <- Volver al listado
                </Link>
            </div>

            <form class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" @submit.prevent="actualizar">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="nombre" class="block text-sm font-medium text-slate-700">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input id="nombre" v-model="form.nombre" type="text" required
                            minlength="3"
                            maxlength="100"
                            placeholder="Luke Skywalker"
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                        >
                        <p v-if="form.errors.nombre" class="mt-1 text-sm text-red-600">
                            {{ form.errors.nombre }}
                        </p>
                    </div>

                    <div>
                        <label for="altura" class="block text-sm font-medium text-slate-700">Altura (cm)</label>
                        <input
                            id="altura"
                            v-model="form.altura"
                            type="number"
                            min="1"
                            max="300"
                            placeholder="172"
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                        >
                        <p v-if="form.errors.altura" class="mt-1 text-sm text-red-600">
                            {{ form.errors.altura }}
                        </p>
                    </div>

                    <div>
                        <label for="masa" class="block text-sm font-medium text-slate-700">Masa (kg)</label>
                        <input
                            id="masa"
                            v-model="form.masa"
                            type="number"
                            min="1"
                            max="500"
                            placeholder="77"
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                        >
                        <p v-if="form.errors.masa" class="mt-1 text-sm text-red-600">
                            {{ form.errors.masa }}
                        </p>
                    </div>

                    <div>
                        <label for="color_cabello" class="block text-sm font-medium text-slate-700">Color de cabello</label>
                        <input
                            id="color_cabello"
                            v-model="form.color_cabello"
                            type="text"
                            maxlength="100"
                            placeholder="blond"
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                        >
                        <p v-if="form.errors.color_cabello" class="mt-1 text-sm text-red-600">
                            {{ form.errors.color_cabello }}
                        </p>
                    </div>

                    <div>
                        <label for="color_ojos" class="block text-sm font-medium text-slate-700">Color de ojos</label>
                        <input
                            id="color_ojos"
                            v-model="form.color_ojos"
                            type="text"
                            maxlength="100"
                            placeholder="blue"
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                        >
                        <p v-if="form.errors.color_ojos" class="mt-1 text-sm text-red-600">
                            {{ form.errors.color_ojos }}
                        </p>
                    </div>

                    <div>
                        <label for="genero" class="block text-sm font-medium text-slate-700">
                            Género <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="genero"
                            v-model="form.genero"
                            required
                            class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                        >
                            <option value="" disabled>Selecciona una opción</option>
                            <option value="masculino">Masculino</option>
                            <option value="femenino">Femenino</option>
                            <option value="n/a">n/a</option>
                        </select>
                        <p v-if="form.errors.genero" class="mt-1 text-sm text-red-600">
                            {{ form.errors.genero }}
                        </p>
                    </div>

                    <div>
                        <label for="planeta_origen" class="block text-sm font-medium text-slate-700">Planeta de origen</label>
                        <input
                            id="planeta_origen"
                            v-model="form.planeta_origen"
                            type="text"
                            maxlength="100"
                            placeholder="Tatooine"
                            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                        >
                        <p v-if="form.errors.planeta_origen" class="mt-1 text-sm text-red-600">
                            {{ form.errors.planeta_origen }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-2 border-t border-slate-200 pt-4 sm:flex-row sm:justify-end">
                    <Link href="/personajes" class="cursor-pointer rounded-lg border border-slate-300 bg-white px-4 py-2 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Cancelar
                    </Link>

                    <button type="submit" class="cursor-pointer rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-60" :disabled="form.processing">
                        {{ form.processing ? 'Guardando…' : 'Actualizar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
