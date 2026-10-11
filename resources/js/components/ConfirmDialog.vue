
<script setup>
import { watch, onMounted, onBeforeUnmount } from 'vue'
import { Trash2, X, AlertTriangle } from 'lucide-vue-next'

const props = defineProps({
    show: Boolean,
    title: {
        type: String,
        default: 'Hapus tugas ini?',
    },
    message: {
        type: String,
        default: 'Tugas yang dihapus tidak dapat dikembalikan.',
    },
    loading: Boolean,
})

const emit = defineEmits(['cancel', 'confirm'])

function handleKeydown(event) {
    if (event.key === 'Escape' && props.show && !props.loading) {
        emit('cancel')
    }
}

watch(
    () => props.show,
    (visible) => {
        document.body.style.overflow = visible ? 'hidden' : ''
    },
)

onMounted(() => window.addEventListener('keydown', handleKeydown))

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleKeydown)
    document.body.style.overflow = ''
})
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show"
                class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/35 p-4 backdrop-blur-[3px]"
                @click.self="!loading && emit('cancel')"
            >
                <Transition
                    appear
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="scale-95 translate-y-2 opacity-0"
                    enter-to-class="scale-100 translate-y-0 opacity-100"
                >
                    <section
                        v-if="show"
                        role="alertdialog"
                        aria-modal="true"
                        aria-labelledby="confirm-title"
                        aria-describedby="confirm-message"
                        class="w-full max-w-[420px] rounded-3xl border border-white/80 bg-white p-6 shadow-2xl shadow-slate-900/15 sm:p-7"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600 ring-1 ring-rose-100">
                                <Trash2 :size="22" />
                            </div>

                            <button
                                type="button"
                                aria-label="Tutup dialog"
                                :disabled="loading"
                                @click="emit('cancel')"
                                class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 disabled:opacity-40"
                            >
                                <X :size="19" />
                            </button>
                        </div>

                        <div class="mt-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-rose-600">
                                Konfirmasi tindakan
                            </p>

                            <h2 id="confirm-title" class="mt-2 text-xl font-bold tracking-tight text-slate-900">
                                {{ title }}
                            </h2>

                            <p id="confirm-message" class="mt-2 text-sm leading-6 text-slate-500">
                                {{ message }}
                            </p>
                        </div>

                        <div class="mt-7 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <button
                                type="button"
                                :disabled="loading"
                                @click="emit('cancel')"
                                class="rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 disabled:opacity-50 sm:min-w-28"
                            >
                                Batalkan
                            </button>

                            <button
                                type="button"
                                :disabled="loading"
                                @click="emit('confirm')"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-3 text-sm font-semibold text-white shadow-sm shadow-rose-200 transition hover:bg-rose-700 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60 sm:min-w-32"
                            >
                                <span v-if="loading" class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                                <Trash2 v-else :size="16" />
                                {{ loading ? 'Menghapus...' : 'Ya, hapus' }}
                            </button>
                        </div>

                        <div class="mt-5 flex items-center justify-center gap-1.5 text-xs text-slate-400">
                            <AlertTriangle :size="13" />
                            Tindakan ini tidak dapat dibatalkan.
                        </div>
                    </section>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
