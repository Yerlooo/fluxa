<script setup lang="ts">
import {
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import type { Component } from 'vue';

interface NavItem {
    title: string;
    href: string;
    icon: Component;
}

defineProps<{
    items: NavItem[];
}>();

const page = usePage<SharedData>();
</script>

<template>
    <SidebarGroup class="px-1 py-0">
        <SidebarGroupLabel
            class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-[0.18em] text-muted-foreground group-data-[collapsible=icon]:hidden"
        >
            Workspace
        </SidebarGroupLabel>

        <SidebarMenu class="gap-1.5">
            <SidebarMenuItem
                v-for="item in items"
                :key="item.title"
            >
                <SidebarMenuButton
                    as-child
    :is-active="page.url === item.href"
    :tooltip="item.title"
    class="h-11 rounded-xl px-3 text-sm font-medium text-sidebar-foreground/75 transition-all duration-200 hover:bg-sidebar-accent hover:text-sidebar-accent-foreground data-[active=true]:bg-indigo-50 data-[active=true]:font-semibold data-[active=true]:text-indigo-700 dark:data-[active=true]:bg-indigo-400/15 dark:data-[active=true]:text-indigo-300"
                >
                    <Link :href="item.href">
                        <component
                            :is="item.icon"
                            class="size-[18px] shrink-0"
                        />
                        <span>{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>