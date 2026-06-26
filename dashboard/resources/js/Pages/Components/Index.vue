<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    site: Object,
    components: Array,
    watcherApiUrl: String,
})

const deleteForm = useForm({})
const snippetOpen = ref(false)
const copied = ref(false)

function deleteComponent(id) {
    if (confirm('Delete this component?')) {
        deleteForm.delete(route('sites.components.destroy', [props.site.id, id]))
    }
}

const snippet = `<script src="${props.watcherApiUrl}/component-watcher.umd.cjs"><\/script>
<script>
  ComponentWatcher.init({
    apiUrl: '${props.watcherApiUrl}',
    siteKey: '${props.site.api_key}',
  })
<\/script>`

async function copySnippet() {
    await navigator.clipboard.writeText(snippet)
    copied.value = true
    setTimeout(() => { copied.value = false }, 2000)
}
</script>

<template>
    <Head :title="`${site.name} — Components`" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-400">
                        <Link :href="route('sites.index')" class="hover:text-indigo-600">Sites</Link>
                        <span class="mx-1">/</span>
                        <span>{{ site.name }}</span>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">Components</h2>
                </div>
                <div class="flex items-center gap-3">
                    <button @click="snippetOpen = !snippetOpen"
                        class="rounded border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50">
                        {{ snippetOpen ? 'Hide' : 'Embed Snippet' }}
                    </button>
                    <Link :href="route('sites.components.create', site.id)"
                        class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                        Add Component
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl px-4 space-y-6">

                <!-- Embed snippet -->
                <div v-if="snippetOpen" class="rounded border border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Embed on <span class="font-mono">{{ site.url }}</span></p>
                            <p class="text-xs text-gray-400 mt-0.5">Paste before the closing <code class="font-mono">&lt;/body&gt;</code> tag on every page you want to track.</p>
                        </div>
                        <button @click="copySnippet"
                            class="rounded border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 hover:bg-gray-50">
                            {{ copied ? 'Copied!' : 'Copy' }}
                        </button>
                    </div>
                    <pre class="overflow-x-auto p-4 font-mono text-xs text-gray-700 leading-relaxed">{{ snippet }}</pre>
                    <div class="border-t border-gray-200 bg-amber-50 px-4 py-2 text-xs text-amber-700 rounded-b">
                        Keep your client secret private — do not commit it to public repositories.
                    </div>
                </div>

                <!-- Components table -->
                <div v-if="components.length === 0" class="rounded border border-dashed border-gray-300 p-12 text-center text-gray-400">
                    No components yet. Add one to start tracking.
                </div>
                <table v-else class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-gray-500">
                            <th class="pb-2 font-medium">Name</th>
                            <th class="pb-2 font-medium">Description</th>
                            <th class="pb-2 font-medium">Macros</th>
                            <th class="pb-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="component in components" :key="component.id" class="border-b last:border-0 hover:bg-gray-50">
                            <td class="py-3 font-medium">{{ component.name }}</td>
                            <td class="py-3 text-gray-500">{{ component.description || '—' }}</td>
                            <td class="py-3">{{ component.macros_count }}</td>
                            <td class="py-3 text-right space-x-3">
                                <Link :href="route('sites.components.analytics', [site.id, component.id])" class="text-gray-500 hover:underline">Analytics</Link>
                                <Link :href="route('sites.components.edit', [site.id, component.id])" class="text-indigo-600 hover:underline">Edit</Link>
                                <button @click="deleteComponent(component.id)" class="text-red-500 hover:text-red-700">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
