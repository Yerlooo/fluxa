```vue
<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Props {
    class?: string;
}

defineProps<Props>();
</script>

<template>
    <Link
        href="/dashboard"
        :class="[
            'group flex min-w-0 items-center gap-3',
            $props.class,
        ]"
    >
        <!-- Fluxa custom logo -->
        <div
            class="relative flex size-10 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-gradient-to-br from-indigo-500 via-violet-600 to-purple-700 shadow-md shadow-indigo-500/20 transition-all duration-300 group-hover:scale-105 group-hover:shadow-lg group-hover:shadow-indigo-500/30"
        >
            <svg
                viewBox="0 0 40 40"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                class="size-8"
                aria-label="Fluxa logo"
                role="img"
            >
                <path
                    d="M10 12.5C10 10.567 11.567 9 13.5 9H29"
                    stroke="white"
                    stroke-width="4"
                    stroke-linecap="round"
                />
                <path
                    d="M10 12V27.5C10 29.433 11.567 31 13.5 31H16"
                    stroke="white"
                    stroke-width="4"
                    stroke-linecap="round"
                />
                <path
                    d="M10.5 20H24.5C27.538 20 30 22.462 30 25.5C30 28.538 27.538 31 24.5 31H23"
                    stroke="#C4B5FD"
                    stroke-width="4"
                    stroke-linecap="round"
                />
                <circle
                    cx="29"
                    cy="9"
                    r="2.5"
                    fill="#DDD6FE"
                />
            </svg>
        </div>

        <!-- Brand name -->
        <div class="grid min-w-0 flex-1 text-left">
            <span
                class="truncate text-lg font-bold leading-none tracking-tight text-foreground"
            >
                Fluxa<span class="text-indigo-500">.</span>
            </span>
            <span
                class="mt-1 truncate text-[10px] font-medium uppercase tracking-[0.0em] text-muted-foreground"
            >
                Personal workspace
            </span>
        </div>
    </Link>
</template>
```