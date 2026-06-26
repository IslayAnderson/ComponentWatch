<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'

const props = defineProps({
    users: Array,
})

function impersonate(userId) {
    router.post(route('admin.impersonate', userId))
}
</script>

<template>
    <Head title="Admin" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Admin</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-5xl px-4">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left text-gray-500">
                            <th class="pb-2 font-medium">User</th>
                            <th class="pb-2 font-medium">Sites</th>
                            <th class="pb-2 font-medium">Components</th>
                            <th class="pb-2 font-medium">Joined</th>
                            <th class="pb-2 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id" class="border-b last:border-0 hover:bg-gray-50">
                            <td class="py-3">
                                <div class="font-medium text-gray-800">{{ user.name }}</div>
                                <div class="text-xs text-gray-400">{{ user.email }}</div>
                                <span v-if="user.is_admin" class="inline-block mt-0.5 rounded bg-indigo-100 px-1.5 py-0.5 text-xs font-medium text-indigo-700">Admin</span>
                            </td>
                            <td class="py-3">{{ user.sites_count }}</td>
                            <td class="py-3">{{ user.components_count }}</td>
                            <td class="py-3 text-gray-400">{{ user.created_at }}</td>
                            <td class="py-3 text-right">
                                <button v-if="!user.is_admin" @click="impersonate(user.id)"
                                    class="rounded border border-gray-300 px-3 py-1 text-xs text-gray-600 hover:bg-gray-50">
                                    Impersonate
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
