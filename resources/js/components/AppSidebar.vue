<script setup lang="ts">
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import type { NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Bookmark, CheckSquare, LayoutGrid, NotebookPen, Sparkles, UserRound } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'To-do List',
        href: '/tasks',
        icon: CheckSquare,
    },
    {
        title: 'Bookmarks',
        href: '/bookmarks',
        icon: Bookmark,
    },    
    {
        title: 'Notes',
        href: '/notes',
        icon: NotebookPen,
    },
    {
        title: 'Profil',
        href: '/profile',
        icon: UserRound,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset" class="border-r border-sidebar-border/70 bg-sidebar">
        <SidebarHeader class="px-3 pb-4 pt-5">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child class="h-12 rounded-xl px-3 transition-colors hover:bg-sidebar-accent">
                        <Link href="/dashboard">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>

            <div class="px-3 pt-4 group-data-[collapsible=icon]:hidden">
                <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-400">Personal workspace</p>
                <p class="mt-1 text-xs text-slate-500">Your space, your flow.</p>
            </div>
        </SidebarHeader>

        <SidebarContent class="px-2">
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter class="gap-3 p-3">
            <div
                class="rounded-2xl border border-indigo-200/70 bg-indigo-50/80 p-4 group-data-[collapsible=icon]:hidden dark:border-indigo-400/20 dark:bg-indigo-400/10"
            >
                <div
                    class="mb-3 flex size-9 items-center justify-center rounded-xl bg-white text-indigo-600 shadow-sm dark:bg-indigo-400/15 dark:text-indigo-300"
                >
                    <Sparkles :size="18" />
                </div>

                <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">Stay in flow.</p>

                <p class="mt-1 text-xs leading-relaxed text-slate-500 dark:text-slate-400">Small steps every day lead to meaningful progress.</p>
            </div>

            <div class="border-t border-slate-100 pt-3">
                <NavUser />
            </div>
        </SidebarFooter>
    </Sidebar>
</template>
