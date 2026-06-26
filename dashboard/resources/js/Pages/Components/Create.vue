<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import MacroEditor from '@/Components/MacroEditor.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({ site: Object })

const form = useForm({
    name: '',
    description: '',
    screen_blank: false,
    macros: [],
})

function submit() {
    form.post(route('sites.components.store', props.site.id))
}
</script>

<template>
    <Head title="Add Component" />
    <AuthenticatedLayout>
        <template #header>
            <div>
                <div class="text-xs text-gray-400">
                    <Link :href="route('sites.index')" class="hover:text-indigo-600">Sites</Link>
                    <span class="mx-1">/</span>
                    <Link :href="route('sites.components.index', site.id)" class="hover:text-indigo-600">{{ site.name }}</Link>
                    <span class="mx-1">/</span>
                    <span>New Component</span>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">Add Component</h2>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-2xl px-4">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Name</label>
                        <input v-model="form.name" type="text" class="w-full rounded border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">Description</label>
                        <textarea v-model="form.description" rows="2" class="w-full rounded border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                    </div>
                    <div class="flex items-center justify-between rounded border border-gray-200 bg-gray-50 px-4 py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Screen Blanking</p>
                            <p class="text-xs text-gray-400 mt-0.5">Briefly blank the screen when a user takes a screenshot with this component visible.</p>
                            <p class="text-xs text-amber-500 mt-1">⚠ Only works on Windows (PrintScreen). macOS intercepts screenshot shortcuts before the browser can react.</p>
                        </div>
                        <button type="button" @click="form.screen_blank = !form.screen_blank"
                            :class="form.screen_blank ? 'bg-indigo-600' : 'bg-gray-200'"
                            class="relative inline-flex h-6 w-11 shrink-0 rounded-full transition-colors focus:outline-none">
                            <span :class="form.screen_blank ? 'translate-x-5' : 'translate-x-1'"
                                class="inline-block h-4 w-4 translate-y-1 rounded-full bg-white shadow transition-transform" />
                        </button>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Macros</label>
                        <MacroEditor v-model="form.macros" />
                        <p v-if="form.errors.macros" class="mt-1 text-xs text-red-500">{{ form.errors.macros }}</p>
                    </div>
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" :disabled="form.processing" class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:opacity-50">
                            Create Component
                        </button>
                        <Link :href="route('sites.components.index', site.id)" class="text-sm text-gray-500 hover:text-gray-700">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
