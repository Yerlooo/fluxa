<script setup>
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    AlertCircle,
    CalendarDays,
    Check,
    CheckCircle2,
    Circle,
    Clock3,
    Flag,
    ListTodo,
    LoaderCircle,
    LogOut,
    Plus,
    Search,
    Trash2,
    X,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    tasks: {
        type: Object,
        default: () => ({
            data: [],
            links: [],
            total: 0,
            current_page: 1,
            last_page: 1,
            from: null,
            to: null,
        }),
    },
    user: {
        type: Object,
        default: () => ({ name: '', email: '' }),
    },
    stats: {
        type: Object,
        default: () => ({ total: 0, active: 0, in_progress: 0, completed: 0 }),
    },
    filters: {
        type: Object,
        default: () => ({ search: '', filter: 'all' }),
    },
});

const search = ref(props.filters.search ?? '');
const activeFilter = ref(props.filters.filter ?? 'all');

const showAddModal = ref(false);
const showDeleteModal = ref(false);
const showBulkDeleteModal = ref(false);
const taskToDelete = ref(null);
const deleting = ref(false);
const deletingBulk = ref(false);
const bulkDeleteMode = ref('selected');
const modalSelectedTaskIds = ref([]);

const toast = ref({ show: false, message: '', type: 'success' });
let toastTimer = null;

const taskForm = useForm({ title: '', priority: 'medium' });

const userName = computed(() => props.user?.name || 'Pengguna');
const userInitial = computed(() => userName.value.trim().charAt(0).toUpperCase() || 'U');
const taskList = computed(() => props.tasks?.data ?? []);
const totalTasks = computed(() => Number(props.stats?.total ?? 0));
const activeTasks = computed(() => Number(props.stats?.active ?? 0));
const completedTasks = computed(() => Number(props.stats?.completed ?? 0));
const hasActiveFilters = computed(() => search.value.trim() !== '' || activeFilter.value !== 'all');
const modalTaskList = computed(() => taskList.value);
const modalSelectedCount = computed(() => modalSelectedTaskIds.value.length);
const isDeletingAll = computed(() => bulkDeleteMode.value === 'all');

const allModalTasksSelected = computed(
    () => modalTaskList.value.length > 0 && modalTaskList.value.every((task) => modalSelectedTaskIds.value.includes(task.id)),
);

const someModalTasksSelected = computed(() => modalTaskList.value.some((task) => modalSelectedTaskIds.value.includes(task.id)));

function showToast(message, type = 'success') {
    if (toastTimer) clearTimeout(toastTimer);
    toast.value = { show: true, message, type };
    toastTimer = setTimeout(() => {
        toast.value.show = false;
        toastTimer = null;
    }, 4000);
}

function closeToast() {
    toast.value.show = false;
    if (toastTimer) clearTimeout(toastTimer);
    toastTimer = null;
}

function applyFilters() {
    router.get(
        '/tasks',
        {
            search: search.value.trim() || undefined,
            filter: activeFilter.value !== 'all' ? activeFilter.value : undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
}

function changeFilter(filter) {
    activeFilter.value = filter;
    applyFilters();
}

function clearFilters() {
    search.value = '';
    activeFilter.value = 'all';
    router.get(
        '/tasks',
        {},
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
}

function openAddModal() {
    taskForm.reset();
    taskForm.clearErrors();
    taskForm.priority = 'medium';
    showAddModal.value = true;
}

function closeAddModal() {
    if (taskForm.processing) return;
    showAddModal.value = false;
    taskForm.clearErrors();
}

function addTask() {
    taskForm.post('/tasks', {
        preserveScroll: true,
        onSuccess: () => {
            showAddModal.value = false;
            taskForm.reset();
            taskForm.priority = 'medium';
            search.value = '';
            activeFilter.value = 'all';
            showToast('Tugas berhasil ditambahkan.');
        },
        onError: () => showToast('Tugas gagal ditambahkan. Periksa kembali isian Anda.', 'error'),
    });
}

function toggleTask(task) {
    const nextStatus = task.status === 'done' ? 'todo' : 'done';
    router.patch(
        `/tasks/${task.id}`,
        { status: nextStatus },
        {
            preserveScroll: true,
            onSuccess: () => showToast(nextStatus === 'done' ? 'Tugas berhasil ditandai selesai.' : 'Tugas dikembalikan ke daftar aktif.'),
            onError: () => showToast('Status tugas gagal diperbarui.', 'error'),
        },
    );
}

function openDeleteModal(task) {
    taskToDelete.value = task;
    showDeleteModal.value = true;
}

function closeDeleteModal() {
    if (deleting.value) return;
    showDeleteModal.value = false;
    taskToDelete.value = null;
}

function deleteTask() {
    if (!taskToDelete.value || deleting.value) return;
    deleting.value = true;

    router.delete(`/tasks/${taskToDelete.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            taskToDelete.value = null;
            showToast('Tugas berhasil dihapus.');
        },
        onError: () => showToast('Tugas gagal dihapus. Silakan coba lagi.', 'error'),
        onFinish: () => {
            deleting.value = false;
        },
    });
}

function openBulkDeleteModal() {
    if (totalTasks.value === 0) {
        showToast('Tidak ada tugas untuk dipilih.', 'error');
        return;
    }
    bulkDeleteMode.value = 'selected';
    modalSelectedTaskIds.value = [];
    showBulkDeleteModal.value = true;
}

function openDeleteAllModal() {
    if (totalTasks.value === 0) {
        showToast('Tidak ada tugas untuk dihapus.', 'error');
        return;
    }
    bulkDeleteMode.value = 'all';
    modalSelectedTaskIds.value = [];
    showBulkDeleteModal.value = true;
}

function closeBulkDeleteModal() {
    if (deletingBulk.value) return;
    showBulkDeleteModal.value = false;
    modalSelectedTaskIds.value = [];
}

function isModalTaskSelected(id) {
    return modalSelectedTaskIds.value.includes(id);
}

function toggleModalTask(id) {
    modalSelectedTaskIds.value = isModalTaskSelected(id)
        ? modalSelectedTaskIds.value.filter((item) => item !== id)
        : [...modalSelectedTaskIds.value, id];
}

function toggleSelectAllModalTasks() {
    const ids = modalTaskList.value.map((task) => task.id);
    modalSelectedTaskIds.value = allModalTasksSelected.value
        ? modalSelectedTaskIds.value.filter((id) => !ids.includes(id))
        : [...new Set([...modalSelectedTaskIds.value, ...ids])];
}

function clearModalSelection() {
    modalSelectedTaskIds.value = [];
}

function confirmBulkDelete() {
    if (deletingBulk.value) return;

    const deletingAll = isDeletingAll.value;
    const ids = [...modalSelectedTaskIds.value];

    if (!deletingAll && ids.length === 0) {
        showToast('Pilih setidaknya satu tugas untuk dihapus.', 'error');
        return;
    }

    deletingBulk.value = true;

    const options = {
        preserveScroll: true,
        onSuccess: () => {
            showBulkDeleteModal.value = false;
            modalSelectedTaskIds.value = [];
            showToast(deletingAll ? 'Semua tugas berhasil dihapus.' : `${ids.length} tugas berhasil dihapus.`);
        },
        onError: () => showToast('Penghapusan tugas gagal. Silakan coba lagi.', 'error'),
        onFinish: () => {
            deletingBulk.value = false;
        },
    };

    if (deletingAll) {
        router.delete('/tasks/all', options);
    } else {
        router.delete('/tasks/bulk', { ...options, data: { ids } });
    }
}

function logout() {
    router.post('/logout');
}

function formatDateTime(date) {
    if (!date) return '';
    const parsed = new Date(date);
    if (Number.isNaN(parsed.getTime())) return '';
    return new Intl.DateTimeFormat('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(parsed);
}

function handleEscape(event) {
    if (event.key !== 'Escape') return;
    if (showAddModal.value) closeAddModal();
    if (showDeleteModal.value) closeDeleteModal();
    if (showBulkDeleteModal.value) closeBulkDeleteModal();
}

onMounted(() => window.addEventListener('keydown', handleEscape));

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleEscape);
    if (toastTimer) clearTimeout(toastTimer);
});
</script>

<template>
    <Head title="To-do List" />

    <AppSidebarLayout
        :breadcrumbs="[
            { title: 'Workspace', href: '/' },
            { title: 'To-do List', href: '/tasks' },
        ]"
    >
        <div class="min-h-screen bg-slate-50 text-slate-800">
            <!-- Toast -->
            <div class="pointer-events-none fixed right-4 top-4 z-[120] flex w-[calc(100%-2rem)] max-w-sm flex-col gap-3 sm:right-6 sm:top-6">
                <Transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="translate-x-4 opacity-0"
                    enter-to-class="translate-x-0 opacity-100"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="translate-x-0 opacity-100"
                    leave-to-class="translate-x-4 opacity-0"
                >
                    <div
                        v-if="toast.show"
                        role="status"
                        aria-live="polite"
                        class="pointer-events-auto flex items-start gap-3 rounded-2xl border bg-white p-4 shadow-xl"
                        :class="toast.type === 'error' ? 'border-rose-200' : 'border-emerald-200'"
                    >
                        <div
                            class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full"
                            :class="toast.type === 'error' ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600'"
                        >
                            <AlertCircle v-if="toast.type === 'error'" :size="19" />
                            <CheckCircle2 v-else :size="19" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold text-slate-900">
                                {{ toast.type === 'error' ? 'Peringatan' : 'Berhasil' }}
                            </p>
                            <p class="mt-1 break-words text-sm text-slate-600">{{ toast.message }}</p>
                        </div>
                        <button class="rounded-lg p-1 text-slate-400 hover:bg-slate-100" aria-label="Tutup notifikasi" @click="closeToast">
                            <X :size="17" />
                        </button>
                    </div>
                </Transition>
            </div>

            <main class="mx-auto max-w-7xl space-y-8 px-4 py-7 sm:px-6 lg:px-8 lg:py-9">
                <section>
                    <p class="mb-2 text-sm font-medium text-indigo-600">
                        <CalendarDays class="mr-1 inline" :size="16" />
                        Ruang kerja pribadi Anda
                    </p>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Selamat datang, {{ userName }}!</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500 sm:text-base">
                        Atur aktivitas, selesaikan tugas, dan jaga semua hal penting tetap terorganisir.
                    </p>
                </section>

                <!-- Statistik -->
                <section class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                                <ListTodo :size="22" />
                            </div>
                            <span class="text-xs text-slate-400">Semua tugas</span>
                        </div>
                        <p class="mt-5 text-3xl font-bold text-slate-900">{{ totalTasks }}</p>
                        <p class="mt-1 text-sm text-slate-500">Total tugas tersimpan</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><Clock3 :size="22" /></div>
                            <span class="text-xs text-slate-400">Belum selesai</span>
                        </div>
                        <p class="mt-5 text-3xl font-bold text-slate-900">{{ activeTasks }}</p>
                        <p class="mt-1 text-sm text-slate-500">Tugas yang perlu dikerjakan</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                                <CheckCircle2 :size="22" />
                            </div>
                            <span class="text-xs text-slate-400">Selesai</span>
                        </div>
                        <p class="mt-5 text-3xl font-bold text-slate-900">{{ completedTasks }}</p>
                        <p class="mt-1 text-sm text-slate-500">Tugas yang telah diselesaikan</p>
                    </div>
                </section>

                <!-- Daftar tugas -->
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 p-5 sm:p-6">
                        <div class="flex flex-col justify-between gap-4 xl:flex-row xl:items-center">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">Daftar tugas</h2>
                                <p class="mt-1 text-sm text-slate-500">Kelola semua tugas dalam satu tempat.</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    class="inline-flex items-center gap-2 rounded-xl border border-rose-200 px-3.5 py-2.5 text-sm font-medium text-rose-600 hover:bg-rose-50 disabled:opacity-40"
                                    :disabled="totalTasks === 0 || deletingBulk"
                                    @click="openBulkDeleteModal"
                                >
                                    <Trash2 :size="16" /> Hapus dipilih
                                </button>
                                <button
                                    class="inline-flex items-center gap-2 rounded-xl border border-rose-200 px-3.5 py-2.5 text-sm font-medium text-rose-600 hover:bg-rose-50 disabled:opacity-40"
                                    :disabled="totalTasks === 0 || deletingBulk"
                                    @click="openDeleteAllModal"
                                >
                                    <Trash2 :size="16" /> Hapus semua
                                </button>
                            </div>
                        </div>

                        <form class="mt-5 flex flex-col gap-3 sm:flex-row" @submit.prevent="applyFilters">
                            <div class="relative flex-1">
                                <Search class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" :size="18" />
                                <input
                                    v-model="search"
                                    type="search"
                                    placeholder="Cari tugas..."
                                    class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-10 pr-4 text-sm outline-none focus:border-indigo-400 focus:bg-white focus:ring-4 focus:ring-indigo-50"
                                />
                            </div>
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                            >
                                <Search :size="17" /> Cari
                            </button>
                            <button
                                type="button"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700"
                                @click="openAddModal"
                            >
                                <Plus :size="18" /> Tugas baru
                            </button>
                        </form>

                        <div class="mt-5 flex flex-wrap items-center gap-2">
                            <button
                                v-for="filter in [
                                    { value: 'all', label: 'Semua' },
                                    { value: 'active', label: 'Aktif' },
                                    { value: 'completed', label: 'Selesai' },
                                ]"
                                :key="filter.value"
                                class="rounded-lg px-3.5 py-2 text-sm font-medium"
                                :class="activeFilter === filter.value ? 'bg-indigo-50 text-indigo-700' : 'text-slate-500 hover:bg-slate-100'"
                                @click="changeFilter(filter.value)"
                            >
                                {{ filter.label }}
                            </button>
                            <button
                                v-if="hasActiveFilters"
                                class="rounded-lg px-3 py-2 text-sm text-slate-400 hover:text-slate-700"
                                @click="clearFilters"
                            >
                                Reset
                            </button>
                        </div>
                    </div>

                    <div v-if="taskList.length" class="divide-y divide-slate-100">
                        <article
                            v-for="task in taskList"
                            :key="task.id"
                            class="flex items-start gap-3 px-5 py-5 transition hover:bg-slate-50/70 sm:gap-4 sm:px-6"
                        >
                            <button
                                class="mt-0.5 shrink-0 rounded-full"
                                :class="task.status === 'done' ? 'text-emerald-500' : 'text-slate-300 hover:text-indigo-500'"
                                :aria-label="task.status === 'done' ? 'Tandai belum selesai' : 'Tandai selesai'"
                                @click="toggleTask(task)"
                            >
                                <CheckCircle2 v-if="task.status === 'done'" :size="23" />
                                <Circle v-else :size="23" />
                            </button>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-col justify-between gap-2 sm:flex-row sm:items-start">
                                    <div class="min-w-0">
                                        <h3
                                            class="break-words font-semibold"
                                            :class="task.status === 'done' ? 'text-slate-400 line-through' : 'text-slate-800'"
                                        >
                                            {{ task.title }}
                                        </h3>
                                        <p v-if="task.description" class="mt-1 break-words text-sm text-slate-500">{{ task.description }}</p>
                                    </div>
                                    <button
                                        class="self-start rounded-lg p-2 text-slate-400 hover:bg-rose-50 hover:text-rose-600"
                                        :aria-label="`Hapus tugas ${task.title}`"
                                        @click="openDeleteModal(task)"
                                    >
                                        <Trash2 :size="17" />
                                    </button>
                                </div>
                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600"
                                        ><ListTodo :size="13" />{{ task.category || 'Personal' }}</span
                                    >
                                    <span
                                        class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-medium"
                                        :class="{
                                            'bg-rose-50 text-rose-600': task.priority === 'high',
                                            'bg-amber-50 text-amber-700': task.priority === 'medium',
                                            'bg-emerald-50 text-emerald-700': task.priority === 'low',
                                        }"
                                        ><Flag :size="13" />{{
                                            task.priority === 'high'
                                                ? 'Prioritas tinggi'
                                                : task.priority === 'low'
                                                  ? 'Prioritas rendah'
                                                  : 'Prioritas sedang'
                                        }}</span
                                    >
                                    <span
                                        class="inline-flex items-center gap-1 rounded-lg px-2.5 py-1 text-xs font-medium"
                                        :class="
                                            task.status === 'done'
                                                ? 'bg-emerald-50 text-emerald-700'
                                                : task.status === 'in_progress'
                                                  ? 'bg-blue-50 text-blue-700'
                                                  : 'bg-slate-100 text-slate-600'
                                        "
                                    >
                                        <Check v-if="task.status === 'done'" :size="13" />
                                        <Clock3 v-else-if="task.status === 'in_progress'" :size="13" />
                                        <Circle v-else :size="13" />
                                        {{
                                            task.status === 'done'
                                                ? 'Selesai'
                                                : task.status === 'in_progress'
                                                  ? 'Sedang dikerjakan'
                                                  : 'Belum dikerjakan'
                                        }}
                                    </span>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-400">
                                    <span v-if="task.created_at">Dibuat {{ formatDateTime(task.created_at) }}</span>
                                    <span v-if="task.completed_at">Selesai {{ formatDateTime(task.completed_at) }}</span>
                                </div>
                            </div>
                        </article>
                    </div>

                    <div v-else class="px-6 py-16 text-center">
                        <ListTodo class="mx-auto text-slate-300" :size="32" />
                        <h3 class="mt-4 font-semibold text-slate-800">{{ hasActiveFilters ? 'Tugas tidak ditemukan' : 'Belum ada tugas' }}</h3>
                        <p class="mx-auto mt-2 max-w-sm text-sm text-slate-500">
                            {{
                                hasActiveFilters
                                    ? 'Coba ubah kata kunci atau filter pencarian Anda.'
                                    : 'Mulai dengan menambahkan tugas pertama agar aktivitas Anda lebih terorganisir.'
                            }}
                        </p>
                        <button
                            v-if="hasActiveFilters"
                            class="mt-5 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold hover:bg-slate-50"
                            @click="clearFilters"
                        >
                            Reset filter
                        </button>
                        <button
                            v-else
                            class="mt-5 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
                            @click="openAddModal"
                        >
                            <Plus :size="17" /> Buat tugas pertama
                        </button>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="tasks.links && tasks.links.length > 3"
                        class="flex flex-col justify-between gap-4 border-t border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:px-6"
                    >
                        <p class="text-sm text-slate-500">
                            Menampilkan <strong>{{ tasks.from ?? 0 }}</strong> sampai <strong>{{ tasks.to ?? 0 }}</strong> dari
                            <strong>{{ tasks.total ?? 0 }}</strong> tugas
                        </p>
                        <div class="flex flex-wrap items-center gap-1">
                            <template v-for="(link, index) in tasks.links" :key="index">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    preserve-scroll
                                    class="flex h-9 min-w-9 items-center justify-center rounded-lg px-2 text-sm transition"
                                    :class="link.active ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-100'"
                                    v-html="link.label"
                                />
                                <span
                                    v-else
                                    class="flex h-9 min-w-9 items-center justify-center rounded-lg px-2 text-sm text-slate-300"
                                    v-html="link.label"
                                />
                            </template>
                        </div>
                    </div>
                </section>

                <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-slate-400">
                    <span>Fluxa Personal Workspace</span>
                    <button
                        class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-slate-500 hover:bg-white hover:text-rose-600"
                        @click="logout"
                    >
                        <LogOut :size="15" /> Keluar
                    </button>
                </div>
            </main>
        </div>

        <!-- Modal tambah tugas -->
        <div
            v-if="showAddModal"
            class="fixed inset-0 z-[60] flex items-center justify-center overflow-y-auto bg-slate-950/50 p-4 backdrop-blur-sm"
            @click.self="closeAddModal"
        >
            <div class="my-auto w-full max-w-lg overflow-hidden rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Buat tugas baru</h2>
                        <p class="mt-1 text-sm text-slate-500">Tambahkan aktivitas yang ingin Anda kerjakan.</p>
                    </div>
                    <button class="rounded-lg p-2 text-slate-400 hover:bg-slate-100" aria-label="Tutup" @click="closeAddModal">
                        <X :size="20" />
                    </button>
                </div>
                <form class="space-y-5 p-6" @submit.prevent="addTask">
                    <div>
                        <label for="task-title" class="mb-2 block text-sm font-semibold text-slate-700">Judul tugas</label>
                        <input
                            id="task-title"
                            v-model="taskForm.title"
                            required
                            maxlength="255"
                            placeholder="Contoh: Belajar Laravel"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50"
                            :class="taskForm.errors.title ? 'border-rose-400' : ''"
                        />
                        <p v-if="taskForm.errors.title" class="mt-2 text-sm text-rose-600">{{ taskForm.errors.title }}</p>
                    </div>
                    <div>
                        <label for="task-priority" class="mb-2 block text-sm font-semibold text-slate-700">Prioritas</label>
                        <select
                            id="task-priority"
                            v-model="taskForm.priority"
                            required
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm focus:border-indigo-400 focus:ring-4 focus:ring-indigo-50"
                        >
                            <option value="low">Rendah</option>
                            <option value="medium">Sedang</option>
                            <option value="high">Tinggi</option>
                        </select>
                        <p v-if="taskForm.errors.priority" class="mt-2 text-sm text-rose-600">{{ taskForm.errors.priority }}</p>
                    </div>
                    <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:justify-end">
                        <button
                            type="button"
                            class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 disabled:opacity-50"
                            :disabled="taskForm.processing"
                            @click="closeAddModal"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-60"
                            :disabled="taskForm.processing"
                        >
                            <LoaderCircle v-if="taskForm.processing" class="animate-spin" :size="17" /><Plus v-else :size="17" />
                            {{ taskForm.processing ? 'Menyimpan...' : 'Simpan tugas' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal hapus satu tugas -->
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm"
            @click.self="closeDeleteModal"
        >
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-600"><Trash2 :size="26" /></div>
                <h2 class="mt-4 text-center text-lg font-bold text-slate-900">Hapus tugas ini?</h2>
                <p class="mt-2 text-center text-sm text-slate-500">
                    Tugas <strong class="text-slate-700">"{{ taskToDelete?.title }}"</strong> akan dihapus secara permanen.
                </p>
                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row">
                    <button
                        class="flex-1 rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 disabled:opacity-50"
                        :disabled="deleting"
                        @click="closeDeleteModal"
                    >
                        Batal
                    </button>
                    <button
                        class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-3 text-sm font-semibold text-white hover:bg-rose-700 disabled:opacity-60"
                        :disabled="deleting"
                        @click="deleteTask"
                    >
                        <LoaderCircle v-if="deleting" class="animate-spin" :size="17" /><Trash2 v-else :size="17" />{{
                            deleting ? 'Menghapus...' : 'Ya, hapus'
                        }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal hapus pilihan / semua -->
        <div
            v-if="showBulkDeleteModal"
            class="fixed inset-0 z-[80] flex items-center justify-center overflow-y-auto bg-slate-950/50 p-4 backdrop-blur-sm"
            @click.self="closeBulkDeleteModal"
        >
            <div class="my-auto flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5 sm:px-6">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">{{ isDeletingAll ? 'Hapus semua tugas?' : 'Pilih tugas untuk dihapus' }}</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ isDeletingAll ? 'Periksa kembali sebelum menghapus semua tugas.' : 'Centang tugas yang ingin Anda hapus.' }}
                        </p>
                    </div>
                    <button
                        class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 disabled:opacity-50"
                        :disabled="deletingBulk"
                        aria-label="Tutup modal"
                        @click="closeBulkDeleteModal"
                    >
                        <X :size="20" />
                    </button>
                </div>

                <div v-if="isDeletingAll" class="mx-5 mt-4 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 sm:mx-6">
                    <div class="flex items-start gap-2">
                        <AlertCircle class="mt-0.5 shrink-0" :size="19" />
                        <p>
                            <strong>Perhatian!</strong> Seluruh {{ totalTasks }} tugas milik akun Anda akan dihapus secara permanen. Tindakan ini
                            tidak dapat dibatalkan.
                        </p>
                    </div>
                </div>

                <template v-else>
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-slate-50 px-5 py-3 sm:px-6">
                        <label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-slate-700">
                            <input
                                type="checkbox"
                                :checked="allModalTasksSelected"
                                :indeterminate="someModalTasksSelected && !allModalTasksSelected"
                                class="h-4 w-4 rounded border-slate-300 text-indigo-600"
                                @change="toggleSelectAllModalTasks"
                            />
                            Pilih semua tugas di halaman ini
                        </label>
                        <button
                            v-if="modalSelectedCount"
                            class="text-sm font-medium text-indigo-600"
                            :disabled="deletingBulk"
                            @click="clearModalSelection"
                        >
                            Batalkan pilihan
                        </button>
                    </div>
                    <div class="min-h-0 flex-1 overflow-y-auto">
                        <div v-if="modalTaskList.length" class="divide-y divide-slate-100">
                            <label
                                v-for="task in modalTaskList"
                                :key="task.id"
                                class="flex cursor-pointer items-start gap-3 px-5 py-4 hover:bg-slate-50 sm:px-6"
                                :class="isModalTaskSelected(task.id) ? 'bg-rose-50/50' : ''"
                            >
                                <input
                                    type="checkbox"
                                    :checked="isModalTaskSelected(task.id)"
                                    :disabled="deletingBulk"
                                    class="mt-1 h-4 w-4 rounded border-slate-300 text-rose-600"
                                    @change="toggleModalTask(task.id)"
                                />
                                <div class="min-w-0 flex-1">
                                    <p class="break-words text-sm font-semibold text-slate-800">{{ task.title }}</p>
                                    <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                        <span class="rounded-md bg-slate-100 px-2 py-1 text-slate-600">{{ task.category || 'Personal' }}</span>
                                        <span
                                            class="rounded-md px-2 py-1"
                                            :class="
                                                task.priority === 'high'
                                                    ? 'bg-rose-50 text-rose-600'
                                                    : task.priority === 'low'
                                                      ? 'bg-emerald-50 text-emerald-700'
                                                      : 'bg-amber-50 text-amber-700'
                                            "
                                            >{{
                                                task.priority === 'high'
                                                    ? 'Prioritas tinggi'
                                                    : task.priority === 'low'
                                                      ? 'Prioritas rendah'
                                                      : 'Prioritas sedang'
                                            }}</span
                                        >
                                        <span
                                            class="rounded-md px-2 py-1"
                                            :class="task.status === 'done' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600'"
                                            >{{ task.status === 'done' ? 'Selesai' : 'Belum selesai' }}</span
                                        >
                                    </div>
                                </div>
                                <CheckCircle2 v-if="isModalTaskSelected(task.id)" class="mt-0.5 shrink-0 text-rose-600" :size="19" />
                            </label>
                        </div>
                        <div v-else class="px-6 py-12 text-center text-sm text-slate-500">Tidak ada tugas yang dapat dipilih pada halaman ini.</div>
                    </div>
                </template>

                <div class="border-t border-slate-100 bg-white px-5 py-4 sm:px-6">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <p class="text-sm text-slate-500">
                            {{ isDeletingAll ? 'Total tugas:' : 'Tugas dipilih:' }}
                            <strong class="text-slate-900">{{ isDeletingAll ? totalTasks : modalSelectedCount }}</strong>
                        </p>
                        <p class="text-xs text-slate-400">{{ tasks.total ?? totalTasks }} tugas total</p>
                    </div>
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button
                            class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 disabled:opacity-50"
                            :disabled="deletingBulk"
                            @click="closeBulkDeleteModal"
                        >
                            Batal
                        </button>
                        <button
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-rose-600 px-5 py-3 text-sm font-semibold text-white hover:bg-rose-700 disabled:opacity-50"
                            :disabled="deletingBulk || (!isDeletingAll && modalSelectedCount === 0)"
                            @click="confirmBulkDelete"
                        >
                            <LoaderCircle v-if="deletingBulk" class="animate-spin" :size="17" /><Trash2 v-else :size="17" />
                            {{ deletingBulk ? 'Menghapus...' : isDeletingAll ? 'Ya, hapus semua' : `Hapus ${modalSelectedCount} tugas` }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template>
