<script setup>
import { Link, router } from '@inertiajs/vue3';

defineProps({
    lista: { type: Array, required: true},
    personajes: { type: Object, required: true }
});

function eliminar(personaje){
    if (confirm(`¿Eliminar a ${personaje.nombre}? Esta acción no se puede deshacer ¿Estas seguro?.`)) {
        router.delete(`/personajes/${personaje.id}`, { preserveScroll: true });
    }
}
</script>

<template>
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Nombre</th>
                        <th class="px-4 py-3 font-semibold">Altura</th>
                        <th class="px-4 py-3 font-semibold">Masa</th>
                        <th class="px-4 py-3 font-semibold">Género</th>
                        <th class="px-4 py-3 font-semibold">Origen</th>
                        <th class="px-4 py-3 text-right font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr v-for="personaje in lista" :key="personaje.id" class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-medium text-slate-800">{{ personaje.nombre }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ personaje.altura ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ personaje.masa ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ personaje.genero }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2 py-1 text-xs font-medium" :class="personaje.origen_swapi ? 'bg-sky-100 text-sky-800' : 'bg-slate-100 text-slate-700'">
                                {{ personaje.origen_swapi ? 'SWAPI' : 'Manual' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <Link :href="`/personajes/${personaje.id}/editar`" class="rounded-md border border-slate-300 px-3 py-1 text-xs font-medium text-slate-700 hover:bg-slate-100">
                                    Editar
                                </Link>
                                <button type="button" class="cursor-pointer rounded-md border border-red-200 px-3 py-1 text-xs font-medium text-red-700 hover:bg-red-50" @click="eliminar(personaje)">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="lista.length == 0">
                        <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                            No hay personajes registrados.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs text-slate-500">
                Mostrando {{ personajes?.from ?? 0 }}-{{ personajes?.to ?? 0 }} de {{ personajes?.total ?? 0 }}
            </p>

            <nav class="flex flex-wrap gap-1">
                <template v-for="(link, index) in personajes?.links ?? []" :key="index">
                    <Link v-if="link.url" :href="link.url" class="rounded-md px-3 py-1 text-xs font-medium" :class="link.active ? 'bg-slate-800 text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-100'" preserve-scroll v-html="link.label"/>
                        <span v-else class="rounded-md border border-slate-100 px-3 py-1 text-xs text-slate-300" v-html="link.label"/>
                </template>
            </nav>
        </div>
    </div>
</template>