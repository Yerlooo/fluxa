<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    user: {
        name: string;
        email: string;
    };
}>();
</script>

<template>
    <Head title="Dashboard" />

    <AppSidebarLayout
        :breadcrumbs="[
            { title: 'Workspace', href: '/dashboard' },
            { title: 'Dashboard', href: '/dashboard' },
        ]"
    >
        <div class="flex min-h-[calc(100vh-8rem)] items-center justify-center p-6">
            <div class="w-full max-w-2xl text-center">
                <p class="mb-3 text-sm font-medium uppercase tracking-[0.2em] text-indigo-500">
                    Personal workspace
                </p>

                <h1 class="text-3xl font-semibold tracking-tight text-slate-900 dark:text-white sm:text-4xl">
                    Selamat datang, {{ user?.name || 'Pengguna' }}!
                </h1>

                <p class="mx-auto mt-4 max-w-lg text-sm leading-7 text-slate-500 dark:text-slate-400 sm:text-base">
                    Selamat datang di Fluxa, ruang pribadi untuk mengelola tugas
                    dan menyimpan bookmark penting agar aktivitasmu lebih teratur.
                </p>
            </div>
        </div>
    </AppSidebarLayout>
</template>