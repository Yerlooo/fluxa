<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Globe } from 'lucide-vue-next';

const props = defineProps<{
    url: string;
    name?: string;
}>();

const failed = ref(false);

const domain = computed(() => {
    try {
        const normalizedUrl = /^https?:\/\//i.test(props.url)
            ? props.url
            : `https://${props.url}`;

        return new URL(normalizedUrl).hostname;
    } catch {
        return '';
    }
});

const faviconUrl = computed(() => {
    if (!domain.value) return '';

    return `https://www.google.com/s2/favicons?domain=${encodeURIComponent(domain.value)}&sz=64`;
});

watch(
    () => props.url,
    () => {
        failed.value = false;
    },
);
</script>

<template>
    <div
        class="flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-slate-200 bg-white"
        :title="name || domain || 'Website'"
    >
        <img
            v-if="faviconUrl && !failed"
            :src="faviconUrl"
            :alt="`Logo ${name || domain}`"
            class="size-7 object-contain"
            loading="lazy"
            @error="failed = true"
        />

        <Globe
            v-else
            :size="22"
            class="text-slate-400"
        />
    </div>
</template>