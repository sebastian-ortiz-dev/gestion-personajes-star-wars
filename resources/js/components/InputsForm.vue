<script setup>
import { Link, router, useForm } from '@inertiajs/vue3';

defineProps({
    form: {type: Object, required: true }
});

</script>

<template>
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label for="nombre" class="block text-sm font-medium text-slate-700">
                Nombre <span class="text-red-500">*</span>
            </label>
            <input id="nombre" v-model="form.nombre" type="text" required minlength="3" maxlength="100" placeholder="Luke Skywalker" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                <p v-if="form.errors.nombre" class="mt-1 text-sm text-red-600">
                    {{ form.errors.nombre }}
                </p>
        </div>

        <div>
            <label for="altura" class="block text-sm font-medium text-slate-700">Altura (cm)</label>
            <input id="altura" v-model="form.altura" type="number" min="1" max="300" placeholder="172" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
            <p v-if="form.errors.altura" class="mt-1 text-sm text-red-600">
                {{ form.errors.altura }}
            </p>
        </div>

        <div>
            <label for="masa" class="block text-sm font-medium text-slate-700">Masa (kg)</label>
            <input id="masa" v-model="form.masa" type="number" min="1" max="500" placeholder="77" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none" >
            <p v-if="form.errors.masa" class="mt-1 text-sm text-red-600">
                {{ form.errors.masa }}
            </p>
        </div>

        <div>
            <label for="color_cabello" class="block text-sm font-medium text-slate-700">Color de cabello</label>
            <input id="color_cabello" v-model="form.color_cabello" type="text" maxlength="100" placeholder="amarillo" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
            <p v-if="form.errors.color_cabello" class="mt-1 text-sm text-red-600">
                {{ form.errors.color_cabello }}
            </p>
        </div>

        <div>
            <label for="color_ojos" class="block text-sm font-medium text-slate-700">Color de ojos</label>
            <input id="color_ojos" v-model="form.color_ojos" type="text" maxlength="100" placeholder="azules" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
            <p v-if="form.errors.color_ojos" class="mt-1 text-sm text-red-600">
                {{ form.errors.color_ojos }}
            </p>
        </div>

        <div>
            <label for="genero" class="block text-sm font-medium text-slate-700">
                Género <span class="text-red-500">*</span>
            </label>
            <select id="genero" v-model="form.genero" required class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
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
            <input id="planeta_origen" v-model="form.planeta_origen" type="text" maxlength="100" placeholder="Tatooine" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
            <p v-if="form.errors.planeta_origen" class="mt-1 text-sm text-red-600">
                {{ form.errors.planeta_origen }}
            </p>
        </div>
    </div>
</template>