<script setup>
const props = defineProps({
    modelValue: Array,
})
const emit = defineEmits(['update:modelValue'])

const macros = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val),
})

function addMacro() {
    macros.value = [...macros.value, { id: null, type: 'css', value: '', priority: macros.value.length }]
}

function removeMacro(index) {
    macros.value = macros.value.filter((_, i) => i !== index)
}

const typeColors = { id: 'bg-blue-100 text-blue-700', css: 'bg-purple-100 text-purple-700', js: 'bg-amber-100 text-amber-700' }
</script>

<script>
import { computed } from 'vue'
export default {}
</script>

<template>
    <div class="space-y-2">
        <div v-for="(macro, index) in macros" :key="index" class="flex items-center gap-2 rounded border border-gray-200 bg-gray-50 px-3 py-2">
            <select v-model="macro.type" class="rounded border-gray-300 bg-white text-xs font-mono shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="css">css</option>
                <option value="id">id</option>
                <option value="js">js</option>
            </select>
            <span :class="['rounded px-1.5 py-0.5 text-xs font-mono font-semibold', typeColors[macro.type]]">{{ macro.type }}</span>
            <input v-model="macro.value" type="text"
                :placeholder="macro.type === 'id' ? '#my-component' : macro.type === 'css' ? '.my-component' : 'document.querySelector(...)'"
                class="min-w-0 flex-1 rounded border-gray-200 bg-white font-mono text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
            <input v-model.number="macro.priority" type="number" min="0" class="w-16 rounded border-gray-200 bg-white text-xs shadow-sm" placeholder="Order" />
            <button type="button" @click="removeMacro(index)" class="text-gray-400 hover:text-red-500">✕</button>
        </div>
        <button type="button" @click="addMacro" class="text-sm text-indigo-600 hover:text-indigo-800">+ Add macro</button>
    </div>
</template>
