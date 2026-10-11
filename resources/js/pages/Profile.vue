<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { computed, watch, onBeforeUnmount, ref } from 'vue'
import {
    ArrowLeft,
    UserRound,
    Mail,
    LockKeyhole,
    Save,
    CheckCircle2,
    ShieldCheck,
    X,
} from 'lucide-vue-next'

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
})

const page = usePage()

// Toast notification
const toast = ref({
    show: false,
    message: '',
})

let toastTimer = null

function showToast(message) {
    toast.value = {
        show: true,
        message,
    }

    if (toastTimer) {
        clearTimeout(toastTimer)
    }

    toastTimer = setTimeout(() => {
        toast.value.show = false
        toastTimer = null
    }, 3500)
}

function closeToast() {
    toast.value.show = false

    if (toastTimer) {
        clearTimeout(toastTimer)
        toastTimer = null
    }
}

watch(
    () => page.props.flash?.success,
    (message) => {
        if (message) {
            showToast(message)
        }
    },
    { immediate: true }
)

onBeforeUnmount(() => {
    if (toastTimer) {
        clearTimeout(toastTimer)
    }
})

// Avatar initials
const initials = computed(() => {
    return (props.user.name || 'Pengguna')
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((word) => word.charAt(0).toUpperCase())
        .join('')
})

// Profile form
const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
})

function saveProfile() {
    profileForm.patch('/profile', {
        preserveScroll: true,
        onSuccess: () => {
            showToast('Profil berhasil diperbarui.')
        },
    })
}

// Password form
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

function savePassword() {
    passwordForm.put('/profile/password', {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset()
            showToast('Password berhasil diperbarui.')
        },
    })
}
</script>

<template>
    <Head title="Profil Saya - Fluxa" />

    <div class="min-h-screen overflow-x-clip bg-slate-50 text-slate-800">
        <!-- Toast notification -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-y-2 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="translate-y-2 opacity-0"
        >
            <div
                v-if="toast.show"
                class="fixed inset-x-3 top-3 z-[100] mx-auto flex max-w-sm items-start gap-3 rounded-2xl border border-emerald-100 bg-white p-3.5 shadow-xl shadow-emerald-900/10 sm:inset-x-auto sm:right-6 sm:top-6 sm:w-full sm:p-4"
                role="status"
                aria-live="polite"
            >
                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 sm:h-10 sm:w-10"
                >
                    <CheckCircle2 class="h-5 w-5" />
                </div>

                <div class="min-w-0 flex-1 pt-0.5">
                    <p class="font-semibold text-slate-900">
                        Berhasil!
                    </p>

                    <p class="mt-1 break-words text-sm leading-5 text-slate-500">
                        {{ toast.message }}
                    </p>
                </div>

                <button
                    type="button"
                    @click="closeToast"
                    class="shrink-0 rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                    aria-label="Tutup notifikasi"
                >
                    <X class="h-4 w-4" />
                </button>
            </div>
        </Transition>

        <!-- Header -->
        <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
            <div
                class="mx-auto flex max-w-6xl items-center justify-between gap-3 px-4 py-3.5 sm:px-6 sm:py-4 lg:px-8"
            >
                <Link
                    href="/"
                    class="shrink-0 text-2xl font-bold tracking-tight text-violet-600"
                >
                    Fluxa<span class="text-slate-800">.</span>
                </Link>

                <Link
                    href="/dashboard"
                    class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-600 transition hover:border-violet-200 hover:bg-violet-50 hover:text-violet-700 sm:px-4"
                >
                    <ArrowLeft class="h-4 w-4 shrink-0" />
                    <span class="sm:hidden">Dashboard</span>
                    <span class="hidden sm:inline">Kembali ke Dashboard</span>
                </Link>
            </div>
        </header>

        <!-- Main content -->
        <main
            class="mx-auto w-full max-w-6xl px-4 py-7 sm:px-6 sm:py-10 lg:px-8 lg:py-12"
        >
            <!-- Page heading -->
            <div class="mb-7 sm:mb-9">
                <div
                    class="mb-3 inline-flex items-center gap-2 rounded-full border border-violet-100 bg-violet-50 px-3 py-1.5 text-xs font-semibold tracking-wide text-violet-700"
                >
                    <UserRound class="h-3.5 w-3.5" />
                    PENGATURAN AKUN
                </div>

                <h1
                    class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl lg:text-4xl"
                >
                    Profil Saya
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 sm:text-base">
                    Kelola identitas dan keamanan akun Fluxa milikmu.
                </p>
            </div>

            <!-- Main grid -->
            <div class="grid min-w-0 grid-cols-1 items-start gap-5 sm:gap-6 lg:grid-cols-[280px_minmax(0,1fr)] lg:gap-7">
                <!-- Account summary -->
                <aside
                    class="min-w-0 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm shadow-slate-200/40 sm:p-6 lg:sticky lg:top-24"
                >
                    <div class="flex items-center gap-4 text-left sm:flex-col sm:text-center">
                        <div
                            class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-violet-100 to-indigo-100 text-xl font-bold text-violet-700 ring-4 ring-violet-50 sm:h-20 sm:w-20 sm:text-2xl"
                        >
                            {{ initials }}
                        </div>

                        <div class="min-w-0 flex-1 sm:w-full sm:flex-none">
                            <h2 class="break-words text-base font-bold text-slate-900 sm:text-lg">
                                {{ user.name }}
                            </h2>

                            <p class="mt-1 break-all text-xs leading-5 text-slate-500 sm:text-sm">
                                {{ user.email }}
                            </p>

                            <span
                                class="mt-2.5 inline-flex items-center gap-1.5 rounded-full bg-violet-50 px-3 py-1.5 text-xs font-medium text-violet-700 sm:mt-4"
                            >
                                <UserRound class="h-3.5 w-3.5" />
                                Akun Personal
                            </span>
                        </div>
                    </div>

                    <div class="my-5 border-t border-slate-100 sm:my-6" />

                    <div
                        class="flex items-start gap-3 rounded-xl bg-emerald-50/70 p-3.5 sm:p-4"
                    >
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-600 shadow-sm"
                        >
                            <ShieldCheck class="h-5 w-5" />
                        </div>

                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-slate-800">
                                Jaga keamanan akun
                            </p>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Gunakan password yang kuat dan jangan membagikannya kepada orang lain.
                            </p>
                        </div>
                    </div>
                </aside>

                <!-- Forms -->
                <div class="min-w-0 space-y-5 sm:space-y-6">
                    <!-- Edit profile -->
                    <section
                        class="min-w-0 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm shadow-slate-200/40 sm:p-6 lg:p-8"
                    >
                        <div class="mb-6 flex items-start gap-3 sm:gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700 sm:h-12 sm:w-12"
                            >
                                <UserRound class="h-5 w-5 sm:h-6 sm:w-6" />
                            </div>

                            <div class="min-w-0 flex-1">
                                <h2 class="text-base font-bold text-slate-900 sm:text-lg">
                                    Informasi Profil
                                </h2>

                                <p class="mt-1 text-sm leading-5 text-slate-500">
                                    Perbarui nama dan email akunmu.
                                </p>
                            </div>
                        </div>

                        <form
                            @submit.prevent="saveProfile"
                            class="space-y-5"
                        >
                            <!-- Name -->
                            <div class="min-w-0">
                                <label
                                    for="name"
                                    class="mb-2 block text-sm font-semibold text-slate-700"
                                >
                                    Nama lengkap
                                </label>

                                <input
                                    id="name"
                                    v-model="profileForm.name"
                                    type="text"
                                    autocomplete="name"
                                    required
                                    maxlength="255"
                                    class="block w-full min-w-0 rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-violet-400 focus:bg-white focus:ring-4 focus:ring-violet-100 sm:px-4"
                                    placeholder="Masukkan nama lengkap"
                                />

                                <p
                                    v-if="profileForm.errors.name"
                                    class="mt-2 break-words text-sm text-red-600"
                                >
                                    {{ profileForm.errors.name }}
                                </p>
                            </div>

                            <!-- Email -->
                            <div class="min-w-0">
                                <label
                                    for="email"
                                    class="mb-2 block text-sm font-semibold text-slate-700"
                                >
                                    Alamat email
                                </label>

                                <div class="relative min-w-0">
                                    <Mail
                                        class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400 sm:left-4"
                                    />

                                    <input
                                        id="email"
                                        v-model="profileForm.email"
                                        type="email"
                                        autocomplete="email"
                                        required
                                        maxlength="255"
                                        class="block w-full min-w-0 rounded-xl border border-slate-200 bg-slate-50/50 py-3 pl-11 pr-3.5 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-violet-400 focus:bg-white focus:ring-4 focus:ring-violet-100 sm:pl-12 sm:pr-4"
                                        placeholder="nama@email.com"
                                    />
                                </div>

                                <p
                                    v-if="profileForm.errors.email"
                                    class="mt-2 break-words text-sm text-red-600"
                                >
                                    {{ profileForm.errors.email }}
                                </p>
                            </div>

                            <div class="border-t border-slate-100 pt-5">
                                <button
                                    type="submit"
                                    :disabled="profileForm.processing"
                                    class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-violet-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-violet-200 transition hover:bg-violet-700 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                                >
                                    <Save class="h-4 w-4 shrink-0" />

                                    {{
                                        profileForm.processing
                                            ? 'Menyimpan...'
                                            : 'Simpan Profil'
                                    }}
                                </button>
                            </div>
                        </form>
                    </section>

                    <!-- Change password -->
                    <section
                        class="min-w-0 rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm shadow-slate-200/40 sm:p-6 lg:p-8"
                    >
                        <div class="mb-6 flex items-start gap-3 sm:gap-4">
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700 sm:h-12 sm:w-12"
                            >
                                <LockKeyhole class="h-5 w-5 sm:h-6 sm:w-6" />
                            </div>

                            <div class="min-w-0 flex-1">
                                <h2 class="text-base font-bold text-slate-900 sm:text-lg">
                                    Keamanan Password
                                </h2>

                                <p class="mt-1 text-sm leading-5 text-slate-500">
                                    Ubah password untuk melindungi akunmu.
                                </p>
                            </div>
                        </div>

                        <form
                            @submit.prevent="savePassword"
                            class="space-y-5"
                        >
                            <!-- Current password -->
                            <div class="min-w-0">
                                <label
                                    for="current_password"
                                    class="mb-2 block text-sm font-semibold text-slate-700"
                                >
                                    Password lama
                                </label>

                                <input
                                    id="current_password"
                                    v-model="passwordForm.current_password"
                                    type="password"
                                    autocomplete="current-password"
                                    required
                                    class="block w-full min-w-0 rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-violet-400 focus:bg-white focus:ring-4 focus:ring-violet-100 sm:px-4"
                                    placeholder="Masukkan password saat ini"
                                />

                                <p
                                    v-if="passwordForm.errors.current_password"
                                    class="mt-2 break-words text-sm text-red-600"
                                >
                                    {{ passwordForm.errors.current_password }}
                                </p>
                            </div>

                            <!-- New password -->
                            <div class="min-w-0">
                                <label
                                    for="password"
                                    class="mb-2 block text-sm font-semibold text-slate-700"
                                >
                                    Password baru
                                </label>

                                <input
                                    id="password"
                                    v-model="passwordForm.password"
                                    type="password"
                                    autocomplete="new-password"
                                    required
                                    minlength="8"
                                    class="block w-full min-w-0 rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-violet-400 focus:bg-white focus:ring-4 focus:ring-violet-100 sm:px-4"
                                    placeholder="Minimal 8 karakter"
                                />

                                <p
                                    v-if="passwordForm.errors.password"
                                    class="mt-2 break-words text-sm text-red-600"
                                >
                                    {{ passwordForm.errors.password }}
                                </p>
                            </div>

                            <!-- Confirm password -->
                            <div class="min-w-0">
                                <label
                                    for="password_confirmation"
                                    class="mb-2 block text-sm font-semibold text-slate-700"
                                >
                                    Konfirmasi password baru
                                </label>

                                <input
                                    id="password_confirmation"
                                    v-model="passwordForm.password_confirmation"
                                    type="password"
                                    autocomplete="new-password"
                                    required
                                    minlength="8"
                                    class="block w-full min-w-0 rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-violet-400 focus:bg-white focus:ring-4 focus:ring-violet-100 sm:px-4"
                                    placeholder="Ulangi password baru"
                                />

                                <p
                                    v-if="passwordForm.errors.password_confirmation"
                                    class="mt-2 break-words text-sm text-red-600"
                                >
                                    {{ passwordForm.errors.password_confirmation }}
                                </p>
                            </div>

                            <div class="border-t border-slate-100 pt-5">
                                <button
                                    type="submit"
                                    :disabled="passwordForm.processing"
                                    class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                                >
                                    <LockKeyhole class="h-4 w-4 shrink-0" />

                                    {{
                                        passwordForm.processing
                                            ? 'Memperbarui...'
                                            : 'Ubah Password'
                                    }}
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>

            <!-- Footer -->
            <footer class="mt-8 border-t border-slate-200/70 pt-6 text-center sm:mt-10 sm:pt-7">
                <p class="text-xs text-slate-400">
                    Fluxa
                    <span class="mx-1.5 text-slate-300">•</span>
                    Ruang kerja personalmu
                </p>
            </footer>
        </main>
    </div>
</template>
