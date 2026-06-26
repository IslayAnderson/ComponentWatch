<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    sites: Array,
})

const deleteForm = useForm({})

function deleteSite(id) {
    if (confirm('Delete this site and all its components?')) {
        deleteForm.delete(route('sites.destroy', id))
    }
}
</script>

<template>
    <Head title="Sites" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Sites</h2>
                <Link :href="route('sites.create')"
                    class="rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Add Site
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl px-4">
                <div v-if="sites.length === 0" class="rounded border border-dashed border-gray-300 p-12 text-center text-gray-400">
                    No sites yet. Add one to get started.
                </div>
                <table v-else class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-gray-500">
                            <th class="pb-2 font-medium">Name</th>
                            <th class="pb-2 font-medium">URL</th>
                            <th class="pb-2 font-medium">Components</th>
                            <th class="pb-2 font-medium">Site Key</th>
                            <th class="pb-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="site in sites" :key="site.id" class="border-b last:border-0 hover:bg-gray-50">
                            <td class="py-3 font-medium">
                                <Link :href="route('sites.components.index', site.id)" class="text-indigo-600 hover:underline">
                                    {{ site.name }}
                                </Link>
                            </td>
                            <td class="py-3 text-gray-500">{{ site.url }}</td>
                            <td class="py-3">{{ site.components_count }}</td>
                            <td class="py-3">
                                <code class="rounded bg-gray-100 px-1.5 py-0.5 font-mono text-xs text-gray-600">{{ site.api_key }}</code>
                            </td>
                            <td class="py-3 text-right">
                                <button @click="deleteSite(site.id)" class="text-red-500 hover:text-red-700">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
