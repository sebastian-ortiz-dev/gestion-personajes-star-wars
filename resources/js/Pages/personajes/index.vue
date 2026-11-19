<script setup>
import { computed, ref } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import ListarPersonajes from '../../components/ListarPersonajes.vue';
import Modal from '../../components/Modal.vue';

const props = defineProps({
    personajes: Object,
    filtros: Object,
});

const page = usePage();

const buscar = ref(props.filtros?.buscar ?? '');
const genero = ref(props.filtros?.genero ?? '');
const mostrarModal = ref(false);

const importForm = useForm({swapi_id: ''});

const success = computed(() => page.props.flash?.success);
const error = computed(() => page.props.flash?.error);
const lista = computed(() => props.personajes?.data ?? []);

function filtrar() {
    router.get('/personajes', { buscar: buscar.value, genero: genero.value }, { preserveState: true, replace: true });
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
            importForm.reset();
            mostrarModal.value = false;
        },
    });
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
                    <button type="button" class="cursor-pointer rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50" @click="abrirModal">
                        Importar desde SWAPI
                    </button>

                    <Link href="/personajes/crear" class="rounded-lg bg-slate-800 px-4 py-2 text-center text-sm font-medium text-white hover:bg-slate-700" >
                        Crear Nuevo
                    </Link>
                </div>
            </header>

            <div v-if="success" class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ success }}
            </div>

            <div v-if="error" class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ error }}
            </div>

            <div class="mb-4">
                <form class="flex gap-2" @submit.prevent="filtrar">
                    <input v-model="buscar" type="search" placeholder="Buscar por nombre" class="w-full bg-white rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none sm:max-w-sm">
                    <select id="genero" v-model="genero" class="mt-1 w-48 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                        <option value="">Todos los géneros</option>
                        <option value="masculino">Masculino</option>
                        <option value="femenino">Femenino</option>
                        <option value="n/a">n/a</option>
                    </select>
                    <button type="submit" class="cursor-pointer rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50" >
                        Buscar
                    </button>
                </form>
            </div>

            <ListarPersonajes :lista="lista" :personajes="personajes" />
            
        </div>

        <Modal v-if="mostrarModal" :importForm="importForm" @importarId="importar" @cerrar="cerrarModal" />

        
    </div>
</template>
