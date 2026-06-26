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
                <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="site in sites" :key="site.id"
                        class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                        <!-- Screenshot thumbnail -->
                        <Link :href="route('sites.components.index', site.id)" class="block overflow-hidden bg-gray-100" style="height:160px">
                            <img
                                :src="`https://s.wordpress.com/mshots/v1/${encodeURIComponent(site.url)}?w=640&h=320`"
                                :alt="site.name"
                                class="h-full w-full object-cover object-top transition-opacity duration-300"
                                loading="lazy"
                                @error="(e) => e.target.style.display = 'none'"
                            />
                        </Link>
                        <!-- Card body -->
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <Link :href="route('sites.components.index', site.id)"
                                        class="block truncate font-medium text-indigo-600 hover:underline">
                                        {{ site.name }}
                                    </Link>
                                    <a :href="site.url" target="_blank" rel="noopener"
                                        class="block truncate text-xs text-gray-400 hover:text-gray-600">
                                        {{ site.url }}
                                    </a>
                                </div>
                                <span class="shrink-0 rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-600">
                                    {{ site.components_count }} component{{ site.components_count === 1 ? '' : 's' }}
                                </span>
                            </div>
                            <div class="mt-3 flex items-center justify-between gap-2">
                                <code class="truncate rounded bg-gray-100 px-1.5 py-0.5 font-mono text-xs text-gray-500">
                                    {{ site.api_key }}
                                </code>
                                <button @click="deleteSite(site.id)" class="shrink-0 text-xs text-red-400 hover:text-red-600">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
