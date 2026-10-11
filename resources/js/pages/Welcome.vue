<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, BellRing, Bookmark, CalendarDays, Check, CheckCheck, Command, Layers3, NotebookPen, Sparkles } from 'lucide-vue-next';
</script>

<template>
    <Head title="Fluxa — Your space, your flow" />

    <main class="min-h-screen overflow-hidden bg-[#fafaff] text-slate-900 dark:bg-[#0b0b12] dark:text-white">
        <!-- Background decoration -->
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="absolute -left-40 -top-40 size-[28rem] rounded-full bg-violet-300/20 blur-[120px] dark:bg-violet-600/15"></div>
            <div class="absolute -right-40 top-40 size-[30rem] rounded-full bg-indigo-300/20 blur-[120px] dark:bg-indigo-600/10"></div>
        </div>

        <!-- Navbar -->
        <header class="relative z-10 mx-auto flex max-w-7xl items-center justify-between px-6 py-5 lg:px-10">
            <Link href="/" class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-2xl bg-violet-600 text-white shadow-lg shadow-violet-600/20">
                    <Command :size="22" :stroke-width="2.5" />
                </div>
                <span class="text-xl font-bold tracking-tight">fluxa<span class="text-violet-600">.</span></span>
            </Link>

            <nav class="flex items-center gap-3">
                <Link
                    href="/login"
                    class="rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white"
                >
                    Login
                </Link>
                <Link
                    href="/register"
                    class="rounded-xl bg-violet-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-violet-600/20 transition hover:bg-violet-700"
                >
                    Daftar
                    <ArrowRight class="ml-1 inline" :size="16" />
                </Link>
            </nav>
        </header>

        <!-- Hero -->
        <section class="relative z-10 mx-auto grid max-w-7xl items-center gap-14 px-6 pb-20 pt-16 lg:grid-cols-2 lg:px-10 lg:pb-28 lg:pt-24">
            <div class="max-w-2xl">
                <div
                    class="mb-7 inline-flex items-center gap-2 rounded-full border border-violet-200 bg-white/80 px-4 py-2 text-sm font-medium text-violet-700 shadow-sm dark:border-violet-400/20 dark:bg-white/5 dark:text-violet-300"
                >
                    <Sparkles :size="16" />
                    Your personal productivity space
                </div>

                <h1 class="text-5xl font-bold leading-[1.08] tracking-tight sm:text-6xl lg:text-7xl">
                    Your space.
                    <span class="mt-2 block bg-gradient-to-r from-violet-600 via-indigo-600 to-blue-500 bg-clip-text text-transparent">
                        Your flow.
                    </span>
                </h1>

                <p class="mt-7 max-w-xl text-base leading-8 text-slate-600 dark:text-slate-400 sm:text-lg">
                    One place to manage tasks, save important links, and keep your activities organized. Less clutter, more focus on what matters.
                </p>

                <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                    <Link
                        href="/register"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-violet-600 px-6 py-4 text-sm font-semibold text-white shadow-xl shadow-violet-600/20 transition hover:-translate-y-0.5 hover:bg-violet-700"
                    >
                        Start Now!
                        <ArrowRight :size="18" />
                    </Link>
                    <Link
                        href="/login"
                        class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white/70 px-6 py-4 text-sm font-semibold text-slate-700 transition hover:bg-white dark:border-white/10 dark:bg-white/5 dark:text-slate-200 dark:hover:bg-white/10"
                    >
                        I already have an account
                    </Link>
                </div>

                <div class="mt-8 flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                    <div class="flex -space-x-2">
                        <div
                            class="flex size-8 items-center justify-center rounded-full border-2 border-[#fafaff] bg-violet-100 text-violet-700 dark:border-[#0b0b12] dark:bg-violet-400/20 dark:text-violet-300"
                        >
                            <Check :size="15" />
                        </div>
                        <div
                            class="flex size-8 items-center justify-center rounded-full border-2 border-[#fafaff] bg-indigo-100 text-indigo-700 dark:border-[#0b0b12] dark:bg-indigo-400/20 dark:text-indigo-300"
                        >
                            <Sparkles :size="15" />
                        </div>
                    </div>
                    Designed for focus and simplicity.
                </div>
            </div>

            <!-- Product preview -->
            <div class="relative mx-auto w-full max-w-lg">
                <div
                    class="absolute -inset-5 rounded-[2.5rem] bg-gradient-to-br from-violet-300/30 to-blue-300/20 blur-2xl dark:from-violet-600/20 dark:to-blue-600/10"
                ></div>

                <div
                    class="relative rounded-[2rem] border border-white bg-white/90 p-5 shadow-2xl shadow-violet-950/10 dark:border-white/10 dark:bg-[#14141f]/95 dark:shadow-black/30 sm:p-7"
                >
                    <div class="flex items-center justify-between border-b border-slate-100 pb-5 dark:border-white/10">
                        <div>
                            <p class="text-xs font-medium text-slate-400">PERSONAL WORKSPACE</p>
                            <h2 class="mt-1 text-lg font-bold">My space</h2>
                        </div>
                        <div
                            class="flex size-10 items-center justify-center rounded-xl bg-violet-100 text-violet-700 dark:bg-violet-400/15 dark:text-violet-300"
                        >
                            <Layers3 :size="20" />
                        </div>
                    </div>

                    <div class="mt-6">
                        <div class="flex items-center justify-between">
                            <h3 class="font-semibold">Today's focus</h3>
                            <span
                                class="rounded-lg bg-violet-50 px-2.5 py-1 text-xs font-medium text-violet-700 dark:bg-violet-400/10 dark:text-violet-300"
                            >
                                To-do List
                            </span>
                        </div>

                        <div class="mt-4 space-y-3">
                            <div
                                class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-white p-4 dark:border-white/10 dark:bg-white/[0.03]"
                            >
                                <div class="flex size-5 shrink-0 items-center justify-center rounded-md bg-violet-600 text-white">
                                    <Check :size="13" />
                                </div>
                                <span class="flex-1 text-sm text-slate-400 line-through">Review daily priorities</span>
                                <CheckCheck class="text-emerald-500" :size="18" />
                            </div>

                            <div
                                class="flex items-center gap-3 rounded-2xl border border-violet-200 bg-violet-50/70 p-4 dark:border-violet-400/20 dark:bg-violet-400/[0.07]"
                            >
                                <div class="size-5 shrink-0 rounded-md border-2 border-violet-400"></div>
                                <span class="flex-1 text-sm font-medium">Finish an important task</span>
                                <span
                                    class="rounded-md bg-violet-100 px-2 py-1 text-[10px] font-semibold text-violet-700 dark:bg-violet-400/15 dark:text-violet-300"
                                    >FOCUS</span
                                >
                            </div>

                            <div
                                class="flex items-center gap-3 rounded-2xl border border-slate-100 bg-white p-4 dark:border-white/10 dark:bg-white/[0.03]"
                            >
                                <div class="size-5 shrink-0 rounded-md border-2 border-slate-200 dark:border-white/20"></div>
                                <span class="flex-1 text-sm text-slate-600 dark:text-slate-300">Plan tomorrow's schedule</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 rounded-2xl border border-slate-100 bg-slate-50/80 p-4 dark:border-white/10 dark:bg-white/[0.03]">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex size-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 dark:bg-indigo-400/15 dark:text-indigo-300"
                            >
                                <Bookmark :size="19" />
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold">Bookmarks</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Keep useful links within reach.</p>
                            </div>
                            <ArrowRight class="text-slate-400" :size="18" />
                        </div>
                    </div>

                    <div class="mt-5 flex items-center gap-2 text-xs text-slate-400">
                        <span class="size-2 rounded-full bg-emerald-500"></span>
                        Your workspace, organized your way.
                    </div>
                </div>

                <div
                    class="absolute -right-3 top-16 hidden items-center gap-2 rounded-2xl border border-white bg-white px-4 py-3 shadow-xl dark:border-white/10 dark:bg-[#1b1b29] sm:flex"
                >
                    <div
                        class="flex size-8 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-300"
                    >
                        <CheckCheck :size="17" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold">One step at a time</p>
                        <p class="mt-0.5 text-[10px] text-slate-400">Make progress every day</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section
            id="features"
            class="relative z-10 border-t border-slate-200/70 bg-white/50 px-6 py-20 dark:border-white/10 dark:bg-white/[0.02] lg:px-10"
        >
            <div class="mx-auto max-w-7xl">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-violet-600 dark:text-violet-400">The Fluxa Experience</p>

                    <h2 class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl">Everything you need to stay organized.</h2>

                    <p class="mt-4 leading-7 text-slate-600 dark:text-slate-400">
                        From managing your work to saving ideas and building good habits, Fluxa is designed to support your productivity all in one
                        personal space.
                    </p>
                </div>

                <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- To-do List -->
                    <article
                        class="rounded-3xl border border-violet-200/80 bg-white p-7 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-violet-950/5 dark:border-violet-400/20 dark:bg-[#14141f]"
                    >
                        <div
                            class="flex size-12 items-center justify-center rounded-2xl bg-violet-100 text-violet-700 dark:bg-violet-400/15 dark:text-violet-300"
                        >
                            <CheckCheck :size="23" />
                        </div>
                        <h3 class="mt-6 text-lg font-bold">Smart To-do List</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-400">
                            Organize your tasks, categories, priorities, and deadlines. Track completed work and focus on what’s most important.
                        </p>
                    </article>

                    <!-- Bookmarks -->
                    <article
                        class="rounded-3xl border border-slate-200/80 bg-white p-7 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-violet-950/5 dark:border-white/10 dark:bg-[#14141f]"
                    >
                        <div
                            class="flex size-12 items-center justify-center rounded-2xl bg-indigo-100 text-indigo-700 dark:bg-indigo-400/15 dark:text-indigo-300"
                        >
                            <Bookmark :size="23" />
                        </div>
                        <h3 class="mt-6 text-lg font-bold">Smart Bookmarks</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-400">
                            Save your favorite links, organize them by category, bookmark them, and easily find important websites again.
                        </p>
                    </article>

                    <!-- Notes -->
                    <article
                        class="rounded-3xl border border-slate-200/80 bg-white p-7 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-violet-950/5 dark:border-white/10 dark:bg-[#14141f]"
                    >
                        <div
                            class="flex size-12 items-center justify-center rounded-2xl bg-amber-100 text-amber-700 dark:bg-amber-400/15 dark:text-amber-300"
                        >
                            <NotebookPen :size="23" />
                        </div>
                        <h3 class="mt-6 text-lg font-bold">Personal Notes</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-400">
                            Save your ideas, study notes, and important information so you can easily find them when you need them.
                        </p>
                    </article>

                    <!-- Calendar and Habits -->
                    <article
                        class="rounded-3xl border border-slate-200/80 bg-white p-7 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-violet-950/5 dark:border-white/10 dark:bg-[#14141f]"
                    >
                        <div
                            class="flex size-12 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-300"
                        >
                            <CalendarDays :size="23" />
                        </div>
                        <h3 class="mt-6 text-lg font-bold">Calendar & Habits</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-400">
                            Plan your activities, create a schedule, and build positive habits by keeping track of your daily routines and maintaining
                            consistency.
                        </p>
                    </article>

                    <!-- Reminders -->
                    <article
                        class="rounded-3xl border border-slate-200/80 bg-white p-7 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-violet-950/5 dark:border-white/10 dark:bg-[#14141f]"
                    >
                        <div
                            class="flex size-12 items-center justify-center rounded-2xl bg-rose-100 text-rose-700 dark:bg-rose-400/15 dark:text-rose-300"
                        >
                            <BellRing :size="23" />
                        </div>
                        <h3 class="mt-6 text-lg font-bold">Reminders & Progress</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-400">
                            Set reminders for important deadlines and track your progress to stay on track toward your goals.
                        </p>
                    </article>

                    <!-- Workspace -->
                    <article
                        class="rounded-3xl border border-slate-200/80 bg-white p-7 transition hover:-translate-y-1 hover:shadow-xl hover:shadow-violet-950/5 dark:border-white/10 dark:bg-[#14141f]"
                    >
                        <div
                            class="flex size-12 items-center justify-center rounded-2xl bg-blue-100 text-blue-700 dark:bg-blue-400/15 dark:text-blue-300"
                        >
                            <Layers3 :size="23" />
                        </div>
                        <h3 class="mt-6 text-lg font-bold">Personal Workspace</h3>
                        <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-400">
                            Manage your account, profile, and display preferences in a personal workspace designed to be simple, convenient, and
                            organized.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="relative z-10 px-6 py-20 lg:px-10">
            <div
                class="mx-auto max-w-5xl overflow-hidden rounded-[2rem] bg-gradient-to-br from-violet-600 via-indigo-600 to-blue-600 px-7 py-14 text-center text-white shadow-2xl shadow-violet-600/20 sm:px-12"
            >
                <Sparkles class="mx-auto" :size="28" />
                <h2 class="mt-5 text-3xl font-bold tracking-tight sm:text-4xl">Make room for what matters.</h2>
                <p class="mx-auto mt-4 max-w-xl leading-7 text-white/80">
                    Start creating a neater personal workspace. Small steps today can make tomorrow better.
                </p>
            </div>
        </section>

        <!-- Footer -->
        <footer class="relative z-10 border-t border-slate-200/70 px-6 py-7 dark:border-white/10">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 text-sm text-slate-500 sm:flex-row">
                <Link href="/" class="flex items-center gap-2 font-bold text-slate-800 dark:text-slate-200">
                    <Command :size="17" class="text-violet-600" />
                    fluxa.
                </Link>
                <p>Made for your own flow. © {{ new Date().getFullYear() }} Fluxa.</p>
            </div>
        </footer>
    </main>
</template>
