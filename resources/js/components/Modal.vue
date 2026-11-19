<script setup>
const emit = defineEmits(['cerrar', 'importarId']);

defineProps({
    importForm: {type: Object, required:true }
});
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 px-4" role="dialog" aria-modal="true" @click.self="$emit('cerrar')">
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <h2 class="text-lg font-semibold text-slate-800">Importar desde SWAPI</h2>
            <p class="mt-1 text-sm text-slate-500">
                Ingresa el ID del personaje en SWAPI (entre 1 y 83).
            </p>

            <form class="mt-4" @submit.prevent="$emit('importarId')">
                <label for="swapi_id" class="block text-sm font-medium text-slate-700">ID de SWAPI</label>
                <input id="swapi_id" v-model="importForm.swapi_id" type="number" min="1" max="83" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-500 focus:outline-none">
                <p v-if="importForm.errors.swapi_id" class="mt-1 text-sm text-red-600">
                    {{ importForm.errors.swapi_id }}
                </p>

                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" class="cursor-pointer rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50" :disabled="importForm.processing" @click="$emit('cerrar')">
                        Cancelar
                    </button>
                    <button type="submit" class="cursor-pointer flex items-center gap-2 rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 disabled:opacity-60" :disabled="importForm.processing" >                            
                        <span v-if="importForm.processing" class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"/>
                        {{ importForm.processing ? 'Importando…' : 'Importar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>