te><script setup lang="ts">
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/components/ui/breadcrumb';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import { PanelLeft } from 'lucide-vue-next';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
</script>

<template>
    <header
        class="sticky top-0 z-30 flex h-14 min-w-0 shrink-0 items-center gap-3 border-b border-border/70 bg-background/90 px-3 backdrop-blur-xl transition-colors sm:h-16 sm:gap-4 sm:px-5 lg:px-8"
    >
        <SidebarTrigger
            class="flex size-9 shrink-0 items-center justify-center rounded-xl text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground"
        >
            <PanelLeft class="size-[18px]" />
        </SidebarTrigger>

        <div class="h-5 w-px shrink-0 bg-border" />

        <div class="min-w-0 flex-1">
            <Breadcrumb v-if="breadcrumbs.length">
                <BreadcrumbList class="flex-nowrap gap-1.5 overflow-hidden text-sm">
                    <template
                        v-for="(item, index) in breadcrumbs"
                        :key="`${item.title}-${index}`"
                    >
                        <BreadcrumbItem class="min-w-0">
                            <BreadcrumbPage
                                v-if="index === breadcrumbs.length - 1"
                                class="truncate font-semibold text-foreground"
                            >
                                {{ item.title }}
                            </BreadcrumbPage>

                            <BreadcrumbLink
                                v-else
                                :href="item.href"
                                class="truncate text-muted-foreground transition-colors hover:text-foreground"
                            >
                                {{ item.title }}
                            </BreadcrumbLink>
                        </BreadcrumbItem>

                        <BreadcrumbSeparator
                            v-if="index !== breadcrumbs.length - 1"
                            class="shrink-0 text-muted-foreground/50"
                        />
                    </template>
                </BreadcrumbList>
            </Breadcrumb>

            <p v-else class="truncate text-sm font-semibold tracking-tight text-foreground">
                Fluxa Workspace
            </p>
        </div>

        <div class="hidden shrink-0 items-center gap-2 sm:flex">
            <span
                class="inline-flex items-center gap-2 rounded-full border border-border/80 bg-card px-3 py-1.5 text-xs font-medium text-muted-foreground"
            >
                <span class="size-2 rounded-full bg-indigo-500" />
                Personal workspace
            </span>
        </div>
    </header>
</template>