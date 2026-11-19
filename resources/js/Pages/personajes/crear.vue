<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import InputsForm from '../../components/InputsForm.vue';

const form = useForm({
    nombre: '',
    altura: '',
    masa: '',
    color_cabello: '',
    color_ojos: '',
    genero: '',
    planeta_origen: '',
});

function limpiar(){
    form.nombre = '';
    form.altura = '';
    form.masa = '';
    form.color_cabello = '';
    form.color_ojos = '';
    form.genero = '';
    form.planeta_origen = '';
}

function guardar(){
    form.post('/personajes');
}
</script>

<template>
    <div class="min-h-screen bg-slate-100 py-8">
        <div class="mx-auto max-w-3xl px-4">
            <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Crear personaje</h1>
                    <p class="text-sm text-slate-500">Completa los datos del personaje</p>
                </div>

                <Link href="/personajes" class="text-sm font-medium bg-white w-35 p-2 rounded-md text-slate-600 hover:text-slate-900 hover:bg-gray-200">
                    <- Volver al listado
                </Link>
            </div>

            <form class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6" @submit.prevent="guardar">
                <InputsForm :form="form"/>

                <div class="mt-6 flex flex-col-reverse gap-2 border-t border-slate-200 pt-4 sm:flex-row sm:justify-end">
                    <button type="button" class="cursor-pointer rounded-lg border border-slate-300 bg-white px-4 py-2 text-center text-sm font-medium text-slate-700 hover:bg-slate-50" @click="limpiar">
                        Limpiar
                    </button>

                    <Link href="/personajes" class="cursor-pointer rounded-lg border border-slate-300 bg-white px-4 py-2 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">
                        Cancelar
                    </Link>

                    <button type="submit" class="cursor-pointer rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-60" :disabled="form.processing">
                        {{ form.processing ? 'Guardando…' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
