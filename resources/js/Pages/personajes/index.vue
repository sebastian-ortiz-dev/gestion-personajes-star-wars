<script setup>
import { computed, ref } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    personajes: Object,
    filtros: Object,
});

const page = usePage();

const buscar = ref(props.filtros?.buscar ?? '');
const mostrarModal = ref(false);

const importForm = useForm({ swapi_id: '' });

const flash = computed(() => page.props.flash ?? {});
const lista = computed(() => props.personajes?.data ?? []);

function filtrar() {
    router.get('/personajes', { buscar: buscar.value }, { preserveState: true, replace: true });
}

function abrirModal() {
    importForm.reset();
    importForm.clearErrors();
    mostrarModal.value = true;
}

function cerrarModal() {
    if (!importForm.processing) {
        mostrarModal.value = false;
    }
}

function importar() {
    importForm.post('/personajes/importar', {
        preserveScroll: true,
        onSuccess: () => {
            mostrarModal.value = false;
        },
    });
}

function eliminar(personaje) {
    if (confirm(`¿Eliminar a ${personaje.nombre}? Esta acción no se puede deshacer.`)) {
        router.delete(`/personajes/${personaje.id}`, { preserveScroll: true });
    }
}
</script>

<template>
    <div class="min-h-screen bg-slate-100 py-8">
        <div class="mx-auto max-w-6xl px-4">
            <header class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Personajes de Star Wars</h1>
                    <p class="text-sm text-slate-500">Gestión de personajes creados manualmente e importados desde SWAPI</p>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row">
                    <button
                        type="button"
                        class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                        @click="abrirModal"
                    >
                        Importar desde SWAPI
                    </button>

                    <Link
                        href="/personajes/crear"
                        class="rounded-lg bg-slate-800 px-4 py-2 text-center text-sm font-medium text-white hover:bg-slate-700"
                    >
                        Crear Nuevo
                    </Link>
                </div>
            </header>

            <div
                v-if="flash.success"
                class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"
            >
                {{ flash.success }}
            </div>

            <div class="mb-4">
                <form class="flex gap-2" @submit.prevent="filtrar">
                    <input
                        v-model="buscar"
                        type="search"
                        placeholder="Buscar por nombre"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none sm:max-w-sm"
                    >
                    <button
                        type="submit"
                        class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                    >
                        Buscar
                    </button>
                </form>
            </div>

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
                                    <span
                                        class="rounded-full px-2 py-1 text-xs font-medium"
                                        :class="personaje.origen_swapi ? 'bg-sky-100 text-sky-800' : 'bg-slate-100 text-slate-700'"
                                    >
                                        {{ personaje.origen_swapi ? 'SWAPI' : 'Manual' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <Link
                                            :href="`/personajes/${personaje.id}/editar`"
                                            class="rounded-md border border-slate-300 px-3 py-1 text-xs font-medium text-slate-700 hover:bg-slate-100"
                                        >
                                            Editar
                                        </Link>
                                        <button
                                            type="button"
                                            class="rounded-md border border-red-200 px-3 py-1 text-xs font-medium text-red-700 hover:bg-red-50"
                                            @click="eliminar(personaje)"
                                        >
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="lista.length === 0">
                                <td colspan="6" class="px-4 py-10 text-center text-slate-500">
                                    No hay personajes registrados.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col gap-3 border-t border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-slate-500">
                        Mostrando {{ personajes?.from ?? 0 }}–{{ personajes?.to ?? 0 }} de {{ personajes?.total ?? 0 }}
                    </p>

                    <nav class="flex flex-wrap gap-1">
                        <template v-for="(link, index) in personajes?.links ?? []" :key="index">
                            <Link
                                v-if="link.url"
                                :href="link.url"
                                class="rounded-md px-3 py-1 text-xs font-medium"
                                :class="link.active ? 'bg-slate-800 text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-100'"
                                preserve-scroll
                                v-html="link.label"
                            />
                            <span
                                v-else
                                class="rounded-md border border-slate-100 px-3 py-1 text-xs text-slate-300"
                                v-html="link.label"
                            />
                        </template>
                    </nav>
                </div>
            </div>
        </div>

        <div
            v-if="mostrarModal"
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4"
            role="dialog"
            aria-modal="true"
            @click.self="cerrarModal"
        >
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                <h2 class="text-lg font-semibold text-slate-800">Importar desde SWAPI</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Ingresa el ID del personaje en SWAPI (entre 1 y 83).
                </p>

                <form class="mt-4" @submit.prevent="importar">
                    <label for="swapi_id" class="block text-sm font-medium text-slate-700">ID de SWAPI</label>
                    <input
                        id="swapi_id"
                        v-model="importForm.swapi_id"
                        type="number"
                        min="1"
                        max="83"
                        required
                        class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none"
                    >
                    <p v-if="importForm.errors.swapi_id" class="mt-1 text-sm text-red-600">
                        {{ importForm.errors.swapi_id }}
                    </p>

                    <div class="mt-6 flex justify-end gap-2">
                        <button
                            type="button"
                            class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                            :disabled="importForm.processing"
                            @click="cerrarModal"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="flex items-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-60"
                            :disabled="importForm.processing"
                        >
                            <span
                                v-if="importForm.processing"
                                class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"
                            />
                            {{ importForm.processing ? 'Importando…' : 'Importar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
