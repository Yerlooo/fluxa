
<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Link } from '@inertiajs/vue3';
import { ArrowUpRight, Sparkles } from 'lucide-vue-next';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>

    <div class="relative min-h-svh overflow-hidden bg-[#f7f7fc] text-slate-900 dark:bg-[#09090f] dark:text-white">
        <div class="pointer-events-none absolute -left-32 -top-32 size-96 rounded-full bg-violet-300/30 blur-3xl dark:bg-violet-600/15"></div>
        <div class="pointer-events-none absolute -bottom-40 -right-32 size-[28rem] rounded-full bg-indigo-300/30 blur-3xl dark:bg-indigo-600/15"></div>

        <div class="relative mx-auto grid min-h-svh w-full max-w-[1440px] lg:grid-cols-2">
            <section class="flex flex-col justify-between px-6 pb-5 pt-6 sm:px-10 lg:px-14 lg:py-10">
                <Link :href="route('home')" class="inline-flex w-fit items-center gap-3">
                    <div class="flex size-11 items-center justify-center rounded-2xl bg-violet-600 text-white shadow-lg shadow-violet-600/25">
                        <AppLogoIcon class="size-7 fill-current" />
                    </div>
                    <span class="text-xl font-bold tracking-tight">Fluxa</span>
                </Link>

                <div class="mx-auto hidden w-full max-w-lg py-12 lg:block">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-violet-200 bg-white/70 px-4 py-2 text-sm font-medium text-violet-700 shadow-sm dark:border-white/10 dark:bg-white/5 dark:text-violet-300">
                        <Sparkles :size="16" />
                        Your personal workspace
                    </div>

                    <h1 class="text-5xl font-bold leading-[1.15] tracking-tight xl:text-6xl">
                        Your space.
                        <span class="block bg-gradient-to-r from-violet-600 to-indigo-500 bg-clip-text text-transparent">
                            Your flow.
                        </span>
                    </h1>

                    <p class="mt-6 max-w-md text-base leading-8 text-slate-600 dark:text-slate-400">
                        Satu ruang untuk mengatur tugas, menyimpan tautan penting,
                        dan mengembangkan ide. Mulai hari dengan lebih terarah bersama Fluxa.
                    </p>

                    <div class="mt-12 rounded-3xl border border-white/80 bg-white/75 p-6 shadow-xl shadow-violet-950/5 backdrop-blur-xl dark:border-white/10 dark:bg-white/[0.04]">
                        <div class="flex items-center gap-3">
                            <div class="flex size-10 items-center justify-center rounded-xl bg-violet-100 text-violet-700 dark:bg-violet-400/15 dark:text-violet-300">
                                <Sparkles :size="20" />
                            </div>
                            <div>
                                <p class="font-semibold">Make room for what matters.</p>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Small steps. Clear mind. Better flow.</p>
                            </div>
                        </div>
                        <div class="mt-6 space-y-3">
                            <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3 dark:bg-white/[0.04]">
                                <span class="size-2 rounded-full bg-violet-500"></span>
                                <span class="text-sm">Organize your daily tasks</span>
                                <ArrowUpRight class="ml-auto text-slate-400" :size="16" />
                            </div>
                            <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3 dark:bg-white/[0.04]">
                                <span class="size-2 rounded-full bg-indigo-500"></span>
                                <span class="text-sm">Keep your favorite links close</span>
                                <ArrowUpRight class="ml-auto text-slate-400" :size="16" />
                            </div>
                            <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3 dark:bg-white/[0.04]">
                                <span class="size-2 rounded-full bg-emerald-500"></span>
                                <span class="text-sm">Build your own productive rhythm</span>
                                <ArrowUpRight class="ml-auto text-slate-400" :size="16" />
                            </div>
                        </div>
                    </div>
                </div>

                <p class="hidden text-xs text-slate-400 lg:block">
                    © {{ new Date().getFullYear() }} Fluxa. Your space, your flow.
                </p>
            </section>

            <section class="flex items-center justify-center px-5 pb-10 pt-4 sm:px-10 lg:px-12 lg:py-10">
                <div class="w-full max-w-md">
                    <div class="mb-6 text-center lg:text-left">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-violet-600 dark:text-violet-400">
                            Welcome to Fluxa
                        </p>
                        <h2 class="mt-3 text-3xl font-bold tracking-tight sm:text-4xl">
                            {{ title }}
                        </h2>
                        <p class="mt-3 text-sm leading-6 text-slate-500 dark:text-slate-400">
                            {{ description }}
                        </p>
                    </div>

                    <div class="rounded-3xl border border-white/80 bg-white/90 p-6 shadow-xl shadow-violet-950/5 backdrop-blur-xl sm:p-8 dark:border-white/10 dark:bg-[#12121b]/90">
                        <slot />
                    </div>

                    <p class="mt-6 text-center text-xs text-slate-400">
                        Your space, your flow.
                        <Link :href="route('home')" class="font-medium text-violet-600 hover:text-violet-700 dark:text-violet-400">
                            Back to home
                        </Link>
                    </p>
                </div>
            </section>
        </div>
    </div>
</template>
