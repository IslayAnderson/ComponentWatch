<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    site: Object,
    component: Object,
    stats: Object,
    page_breakdown: Array,
    html_hashes: Array,
    screenshots: Array,
    watcherApiUrl: String,
})

const takingScreenshot = ref(false)

function formatMs(ms) {
    if (ms === null) return '—'
    if (ms < 1000) return `${ms}ms`
    return `${(ms / 1000).toFixed(1)}s`
}

async function requestScreenshot() {
    takingScreenshot.value = true
    try {
        const res = await fetch(route('sites.components.screenshot-token', [props.site.id, props.component.id]), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                'Accept': 'application/json',
            },
        })
        const data = await res.json()
        window.open(data.url, '_blank')

        // Poll for new screenshot for up to 2 minutes
        const start = Date.now()
        const poll = setInterval(() => {
            if (Date.now() - start > 120_000) {
                clearInterval(poll)
                takingScreenshot.value = false
                return
            }
            router.reload({ only: ['screenshots'], onSuccess: () => {
                if (props.screenshots.length > 0) {
                    clearInterval(poll)
                    takingScreenshot.value = false
                }
            }})
        }, 3000)
    } catch {
        takingScreenshot.value = false
    }
}
</script>

<template>
    <Head :title="`${component.name} — Analytics`" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs text-gray-400">
                        <Link :href="route('sites.index')" class="hover:text-indigo-600">Sites</Link>
                        <span class="mx-1">/</span>
                        <Link :href="route('sites.components.index', site.id)" class="hover:text-indigo-600">{{ site.name }}</Link>
                        <span class="mx-1">/</span>
                        <Link :href="route('sites.components.edit', [site.id, component.id])" class="hover:text-indigo-600">{{ component.name }}</Link>
                        <span class="mx-1">/</span>
                        <span>Analytics</span>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-800">{{ component.name }}</h2>
                </div>
                <button @click="requestScreenshot" :disabled="takingScreenshot"
                    class="rounded border border-gray-300 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 disabled:opacity-50 flex items-center gap-2">
                    <span v-if="takingScreenshot" class="inline-block h-3 w-3 rounded-full border-2 border-gray-400 border-t-transparent animate-spin"></span>
                    {{ takingScreenshot ? 'Waiting for screenshot…' : 'Take Screenshot' }}
                </button>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl space-y-8 px-4">

                <!-- Summary stats -->
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <Stat label="Discoveries" :value="stats.total_discoveries" />
                    <Stat label="Unique Pages" :value="stats.unique_pages" />
                    <Stat label="Sessions" :value="stats.unique_sessions" />
                    <Stat label="Clicks" :value="stats.clicks" />
                    <Stat label="Mouseovers" :value="stats.mouseovers" />
                    <Stat label="Hover Events" :value="stats.hover_time_events" />
                    <Stat label="Avg Hover Time" :value="formatMs(stats.avg_hover_ms)" />
                    <Stat label="Screenshot Detections" :value="stats.screenshot_detections" />
                </div>

                <!-- Screenshots -->
                <section v-if="screenshots.length">
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">Screenshots</h3>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                        <div v-for="shot in screenshots" :key="shot.id" class="rounded border border-gray-200 overflow-hidden">
                            <img :src="shot.image_url" class="w-full object-cover" :alt="`Screenshot from ${shot.page_url}`" />
                            <div class="px-3 py-2 bg-gray-50 border-t border-gray-200">
                                <p class="font-mono text-xs text-gray-500 truncate">{{ shot.page_url }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ shot.created_at }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Page breakdown -->
                <section v-if="page_breakdown.length">
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">Pages</h3>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b text-left text-gray-500">
                                <th class="pb-2 font-medium">URL</th>
                                <th class="pb-2 font-medium">Discoveries</th>
                                <th class="pb-2 font-medium">Clicks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="page in page_breakdown" :key="page.url" class="border-b last:border-0 hover:bg-gray-50">
                                <td class="py-2 font-mono text-xs text-gray-600">{{ page.url }}</td>
                                <td class="py-2">{{ page.discoveries }}</td>
                                <td class="py-2">{{ page.clicks }}</td>
                            </tr>
                        </tbody>
                    </table>
                </section>

                <!-- HTML hashes -->
                <section v-if="html_hashes.length">
                    <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-500">HTML Variants</h3>
                    <p class="mb-3 text-xs text-gray-400">Each unique hash represents a distinct HTML structure for this component.</p>
                    <div class="space-y-2">
                        <div v-for="h in html_hashes" :key="h.hash" class="rounded border border-gray-200 bg-gray-50 px-4 py-3">
                            <div class="flex items-center justify-between">
                                <code class="font-mono text-xs text-gray-500">{{ h.hash }}</code>
                                <span class="text-sm text-gray-600">{{ h.count }} {{ h.count === 1 ? 'instance' : 'instances' }}</span>
                            </div>
                            <div class="mt-1 flex flex-wrap gap-1">
                                <span v-for="page in h.pages" :key="page" class="rounded bg-gray-200 px-2 py-0.5 font-mono text-xs text-gray-600">{{ page }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- No data state -->
                <div v-if="stats.total_discoveries === 0" class="rounded border border-dashed border-gray-300 p-12 text-center text-gray-400">
                    No data yet. Make sure the JS library is installed on <span class="font-medium">{{ site.url }}</span> and the component is being discovered.
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import { defineComponent, h } from 'vue'

const Stat = defineComponent({
    props: { label: String, value: [String, Number] },
    render() {
        return h('div', { class: 'rounded border border-gray-200 bg-white p-4' }, [
            h('div', { class: 'text-2xl font-semibold text-gray-800' }, String(this.value ?? '—')),
            h('div', { class: 'mt-0.5 text-xs text-gray-500' }, this.label),
        ])
    },
})

export default { components: { Stat } }
</script>
