
<script setup>
import { Check, X, AlertCircle } from 'lucide-vue-next'

defineProps({
    show: Boolean,
    message: {
        type: String,
        default: 'Berhasil!',
    },
    type: {
        type: String,
        default: 'success',
    },
})

defineEmits(['close'])
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-x-4 opacity-0"
            enter-to-class="translate-x-0 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="translate-x-0 opacity-100"
            leave-to-class="translate-x-4 opacity-0"
        >
            <div
                v-if="show"
                class="fixed right-4 top-4 z-[120] w-[calc(100%-2rem)] max-w-sm overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-900/10 sm:right-6 sm:top-6"
                role="status"
                aria-live="polite"
            >
                <div class="flex items-center gap-3 p-4">
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                        :class="type === 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'"
                    >
                        <Check v-if="type === 'success'" :size="21" />
                        <AlertCircle v-else :size="21" />
                    </div>

                    <p class="flex-1 text-sm font-semibold text-slate-800">
                        {{ message }}
                    </p>

                    <button
                        type="button"
                        aria-label="Tutup notifikasi"
                        @click="$emit('close')"
                        class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                    >
                        <X :size="17" />
                    </button>
                </div>

                <div
                    class="h-1 origin-left animate-[toast-progress_3s_linear_forwards]"
                    :class="type === 'success' ? 'bg-emerald-500' : 'bg-rose-500'"
                ></div>
            </div>
        </Transition>
    </Teleport>
</template>

<style>
@keyframes toast-progress {
    from { transform: scaleX(1); }
    to { transform: scaleX(0); }
}
</style>
