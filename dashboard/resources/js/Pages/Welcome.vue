<script setup>
import { Head, Link } from '@inertiajs/vue3'

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
})
</script>

<template>
    <Head title="ComponentWatch — Component-level analytics" />

    <div class="min-h-screen bg-white flex flex-col">

        <!-- Nav -->
        <nav class="flex items-center justify-between px-8 py-5 border-b border-gray-100">
            <span class="font-semibold text-gray-900 tracking-tight">ComponentWatch</span>
            <div class="flex items-center gap-4 text-sm">
                <Link v-if="canLogin" :href="route('login')" class="text-gray-500 hover:text-gray-800">Log in</Link>
                <Link v-if="canRegister" :href="route('register')"
                    class="rounded bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-700">
                    Get started
                </Link>
            </div>
        </nav>

        <!-- Hero -->
        <main class="flex-1 flex flex-col items-center justify-center px-6 text-center py-24">
            <span class="mb-4 inline-block rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-600">
                Analytics at the component level
            </span>
            <h1 class="text-5xl font-bold tracking-tight text-gray-900 max-w-2xl leading-tight">
                Know exactly how your components perform
            </h1>
            <p class="mt-6 text-lg text-gray-500 max-w-xl leading-relaxed">
                ComponentWatch tracks how often each component on your site is seen, clicked, and hovered —
                identified by CSS selectors, IDs, or custom JS snippets. No page-level averages. Real component data.
            </p>
            <div class="mt-10 flex items-center gap-4">
                <Link v-if="canRegister" :href="route('register')"
                    class="rounded-lg bg-indigo-600 px-6 py-3 text-sm font-medium text-white hover:bg-indigo-700 shadow-sm">
                    Create a free account
                </Link>
                <Link v-if="canLogin" :href="route('login')"
                    class="text-sm text-gray-500 hover:text-gray-800">
                    Already have an account? Log in →
                </Link>
            </div>
        </main>

        <!-- Features -->
        <section class="border-t border-gray-100 bg-gray-50 px-8 py-16">
            <div class="mx-auto max-w-4xl grid grid-cols-1 gap-8 sm:grid-cols-3">
                <Feature
                    title="Macro-based discovery"
                    body="Identify components with CSS selectors, element IDs, or arbitrary JavaScript — works on any stack." />
                <Feature
                    title="Behaviour analytics"
                    body="Tracks clicks, mouseovers, and hover time per component instance. Events are batched and flushed efficiently." />
                <Feature
                    title="One script tag"
                    body="Drop a single script onto your site. No build step, no framework dependency, no secrets in your HTML." />
            </div>
        </section>

        <footer class="border-t border-gray-100 px-8 py-6 text-center text-xs text-gray-400">
            ComponentWatch
        </footer>

    </div>
</template>

<script>
import { defineComponent, h } from 'vue'

const Feature = defineComponent({
    props: { title: String, body: String },
    render() {
        return h('div', [
            h('h3', { class: 'text-sm font-semibold text-gray-800 mb-2' }, this.title),
            h('p', { class: 'text-sm text-gray-500 leading-relaxed' }, this.body),
        ])
    },
})

export default { components: { Feature } }
</script>
