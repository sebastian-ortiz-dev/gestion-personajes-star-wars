<script setup>
import { computed, watch } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import InputsForm from '../../components/InputsForm.vue';

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
      form.planeta_origen = personajeObtenido.planeta_origen ?? ''
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
                <InputsForm :form="form" />

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
