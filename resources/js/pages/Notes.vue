<script setup>
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    BookOpen, Check, ChevronLeft, ChevronRight, FileText,
    Folder, Heart, LoaderCircle, Pencil, Plus, Search,
    Star, Trash2, X,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    notes: {
        type: Object,
        default: () => ({
            data: [], links: [], total: 0, current_page: 1,
            last_page: 1, from: null, to: null,
        }),
    },
    categories: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({ total: 0, favorites: 0 }) },
    filters: {
        type: Object,
        default: () => ({ search: '', category: 'all', filter: 'all' }),
    },
});

const search = ref(props.filters?.search ?? '');
const selectedCategory = ref(props.filters?.category ?? 'all');
const selectedFilter = ref(props.filters?.filter ?? 'all');

const showModal = ref(false);
const modalMode = ref('create');
const selectedNote = ref(null);
const showDetailModal = ref(false);
const showDeleteDialog = ref(false);
const deleting = ref(false);

const toast = ref({ show: false, message: '', type: 'success' });
let toastTimer = null;

const createForm = useForm({
    title: '',
    content: '',
    category: 'Personal',
});

const editForm = useForm({
    id: null,
    title: '',
    content: '',
    category: 'Personal',
});

const activeForm = computed(() =>
    modalMode.value === 'edit' ? editForm : createForm,
);

const notesList = computed(() => props.notes?.data ?? []);

const hasActiveFilters = computed(() =>
    search.value.trim() !== '' ||
    selectedCategory.value !== 'all' ||
    selectedFilter.value !== 'all',
);

function showToast(message, type = 'success') {
    if (toastTimer) clearTimeout(toastTimer);

    toast.value = { show: true, message, type };

    toastTimer = setTimeout(() => {
        toast.value.show = false;
        toastTimer = null;
    }, 3500);
}

function closeToast() {
    toast.value.show = false;
    if (toastTimer) clearTimeout(toastTimer);
    toastTimer = null;
}

function applyFilters() {
    router.get('/notes', {
        search: search.value.trim() || undefined,
        category: selectedCategory.value !== 'all'
            ? selectedCategory.value
            : undefined,
        filter: selectedFilter.value !== 'all'
            ? selectedFilter.value
            : undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function changeFilter(filter) {
    selectedFilter.value = filter;
    applyFilters();
}

function clearFilters() {
    search.value = '';
    selectedCategory.value = 'all';
    selectedFilter.value = 'all';
    applyFilters();
}

function openDetail(note) {
    selectedNote.value = note;
    showDetailModal.value = true;
}

function openCreateModal() {
    modalMode.value = 'create';
    createForm.reset();
    createForm.clearErrors();
    createForm.category = 'Personal';
    showModal.value = true;
}

function openEditModal(note) {
    showDetailModal.value = false;
    modalMode.value = 'edit';
    editForm.clearErrors();
    editForm.id = note.id;
    editForm.title = note.title ?? '';
    editForm.content = note.content ?? '';
    editForm.category = note.category ?? 'Personal';
    showModal.value = true;
}

function closeModal() {
    if (activeForm.value.processing) return;

    showModal.value = false;
    createForm.clearErrors();
    editForm.clearErrors();
}

function submitNote() {
    if (activeForm.value.processing) return;

    if (modalMode.value === 'create') {
        createForm.post('/notes', {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                createForm.reset();
                search.value = '';
                selectedCategory.value = 'all';
                selectedFilter.value = 'all';

                showToast('Catatan berhasil ditambahkan.');

                router.get('/notes', {}, {
                    preserveScroll: true,
                    replace: true,
                });
            },
            onError: () => showToast(
                'Periksa kembali judul dan isi catatan.',
                'error',
            ),
        });

        return;
    }

    editForm.patch(`/notes/${editForm.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            showToast('Catatan berhasil diperbarui.');
        },
        onError: () => showToast(
            'Catatan gagal diperbarui. Periksa kembali isinya.',
            'error',
        ),
    });
}

function toggleFavorite(note) {
    router.patch(`/notes/${note.id}/favorite`, {}, {
        preserveScroll: true,
        onSuccess: () => showToast(
            note.is_favorite
                ? 'Catatan dihapus dari favorit.'
                : 'Catatan ditambahkan ke favorit.',
        ),
        onError: () => showToast(
            'Gagal memperbarui status favorit.',
            'error',
        ),
    });
}

function confirmDelete(note) {
    selectedNote.value = note;
    showDeleteDialog.value = true;
}

function closeDeleteDialog() {
    if (deleting.value) return;

    showDeleteDialog.value = false;
    selectedNote.value = null;
}

function deleteNote() {
    if (!selectedNote.value || deleting.value) return;

    deleting.value = true;

    router.delete(`/notes/${selectedNote.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteDialog.value = false;
            selectedNote.value = null;
            showDetailModal.value = false;
            showToast('Catatan berhasil dihapus.');
        },
        onError: () => showToast('Catatan gagal dihapus.', 'error'),
        onFinish: () => {
            deleting.value = false;
        },
    });
}

// Format tanggal pada kartu catatan.
function formatDate(date) {
    if (!date) return '-';

    const parsed = new Date(date);
    if (Number.isNaN(parsed.getTime())) return '-';

    return parsed.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

// Format tanggal beserta jam pada modal detail.
function formatDateTime(date) {
    if (!date) return '-';

    const parsed = new Date(date);
    if (Number.isNaN(parsed.getTime())) return '-';

    return parsed.toLocaleString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}

function handleEscape(event) {
    if (event.key !== 'Escape') return;

    if (showModal.value) closeModal();
    else if (showDetailModal.value) showDetailModal.value = false;
    else if (showDeleteDialog.value) closeDeleteDialog();
}

onMounted(() => {
    window.addEventListener('keydown', handleEscape);
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleEscape);
    if (toastTimer) clearTimeout(toastTimer);
});
</script>

<template>
    <Head title="Personal Notes - Fluxa" />

    <AppSidebarLayout
        :breadcrumbs="[
            { title: 'Workspace', href: '/dashboard' },
            { title: 'Notes', href: '/notes' },
        ]"
    >
        <div class="min-h-screen bg-slate-50 text-slate-800">
            <!-- Notifikasi -->
            <div class="pointer-events-none fixed right-4 top-4 z-[120] w-[calc(100%-2rem)] max-w-sm sm:right-6 sm:top-6">
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
                        class="pointer-events-auto flex items-center gap-3 rounded-2xl border bg-white p-4 shadow-xl"
                        :class="toast.type === 'error' ? 'border-rose-200' : 'border-emerald-200'"
                    >
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl"
                            :class="toast.type === 'error' ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600'"
                        >
                            <X v-if="toast.type === 'error'" :size="19" />
                            <Check v-else :size="19" />
                        </div>
                        <p class="min-w-0 flex-1 text-sm font-medium text-slate-700">
                            {{ toast.message }}
                        </p>
                        <button
                            type="button"
                            aria-label="Tutup notifikasi"
                            class="text-slate-400 hover:text-slate-700"
                            @click="closeToast"
                        >
                            <X :size="17" />
                        </button>
                    </div>
                </Transition>
            </div>

            <main class="mx-auto max-w-7xl space-y-7 p-4 md:p-8 lg:p-10">
                <!-- Intro -->
                <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-600 to-violet-700 p-6 text-white md:p-8">
                    <div class="relative z-10 max-w-2xl">
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15">
                            <BookOpen :size="25" />
                        </div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-indigo-200">
                            Personal Notes
                        </p>
                        <h1 class="mt-2 text-2xl font-bold md:text-3xl">
                            Semua ide berharga, satu tempat.
                        </h1>
                        <p class="mt-3 max-w-xl text-sm leading-6 text-indigo-100 md:text-base">
                            Catat ide, simpan hal penting, dan temukan kembali pemikiranmu kapan saja.
                        </p>
                        <button
                            type="button"
                            class="mt-6 inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-indigo-700 shadow-sm transition hover:bg-indigo-50"
                            @click="openCreateModal"
                        >
                            <Plus :size="18" />
                            Tulis catatan
                        </button>
                    </div>
                    <div class="pointer-events-none absolute -right-8 -top-14 h-64 w-64 rounded-full border-[35px] border-white/10" />
                    <div class="pointer-events-none absolute -bottom-24 right-28 h-48 w-48 rounded-full bg-white/5" />
                </section>

                <!-- Statistik -->
                <section class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-slate-500">Total catatan</p>
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                                <FileText :size="20" />
                            </div>
                        </div>
                        <p class="mt-3 text-3xl font-bold text-slate-900">{{ stats.total ?? 0 }}</p>
                        <p class="mt-1 text-xs text-slate-500">Catatan tersimpan</p>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-slate-500">Favorit</p>
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                                <Star :size="20" />
                            </div>
                        </div>
                        <p class="mt-3 text-3xl font-bold text-slate-900">{{ stats.favorites ?? 0 }}</p>
                        <p class="mt-1 text-xs text-slate-500">Catatan pilihanmu</p>
                    </div>
                </section>

                <!-- Pencarian dan filter -->
                <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-5">
                    <form class="flex flex-col gap-3 md:flex-row" @submit.prevent="applyFilters">
                        <div class="relative min-w-0 flex-1">
                            <Search :size="19" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" />
                            <input
                                v-model="search"
                                type="search"
                                placeholder="Cari judul atau isi catatan..."
                                class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                            />
                        </div>
                        <select
                            v-model="selectedCategory"
                            class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 md:w-52"
                        >
                            <option value="all">Semua kategori</option>
                            <option v-for="category in categories" :key="category" :value="category">
                                {{ category }}
                            </option>
                        </select>
                        <button type="submit" class="flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-700">
                            <Search :size="17" />
                            Cari
                        </button>
                        <button
                            type="button"
                            class="flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700"
                            @click="openCreateModal"
                        >
                            <Plus :size="18" />
                            Tambah catatan
                        </button>
                    </form>

                    <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                        <button
                            type="button"
                            class="rounded-lg px-4 py-2 text-sm font-medium transition"
                            :class="selectedFilter === 'all' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            @click="changeFilter('all')"
                        >
                            Semua
                        </button>
                        <button
                            type="button"
                            class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition"
                            :class="selectedFilter === 'favorites' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            @click="changeFilter('favorites')"
                        >
                            <Star :size="15" />
                            Favorit
                        </button>
                        <button
                            v-if="hasActiveFilters"
                            type="button"
                            class="ml-auto flex items-center gap-1 rounded-lg px-3 py-2 text-sm text-slate-500 hover:bg-slate-100 hover:text-slate-800"
                            @click="clearFilters"
                        >
                            <X :size="15" />
                            Reset filter
                        </button>
                    </div>
                </section>

                <!-- Daftar catatan -->
                <section>
                    <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Koleksi catatan</h2>
                            <p class="mt-1 text-sm text-slate-500">Ide dan pemikiran yang kamu simpan.</p>
                        </div>
                        <p class="text-sm text-slate-500">{{ notes.total ?? 0 }} hasil</p>
                    </div>

                    <div v-if="notesList.length" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <article
                            v-for="note in notesList"
                            :key="note.id"
                            class="flex min-w-0 flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md"
                        >
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                                    <FileText :size="20" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h3 class="break-words font-semibold text-slate-900">{{ note.title }}</h3>
                                    <p class="mt-1 text-xs text-slate-400">
                                        Diperbarui {{ formatDate(note.updated_at) }}
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    :aria-label="note.is_favorite ? 'Hapus dari favorit' : 'Tambahkan ke favorit'"
                                    :class="note.is_favorite ? 'bg-amber-50 text-amber-500' : 'text-slate-400 hover:bg-amber-50 hover:text-amber-500'"
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl transition"
                                    @click="toggleFavorite(note)"
                                >
                                    <Star :size="19" :fill="note.is_favorite ? 'currentColor' : 'none'" />
                                </button>
                            </div>

                            <p class="mt-4 min-h-12 whitespace-pre-line break-words text-sm leading-6 text-slate-600">
                                {{ note.content || 'Belum ada isi catatan.' }}
                            </p>

                            <div class="mt-4 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-medium text-slate-600">
                                    <Folder :size="13" />
                                    {{ note.category || 'Personal' }}
                                </span>
                                <span v-if="note.is_favorite" class="inline-flex items-center gap-1 rounded-lg bg-amber-50 px-2.5 py-1.5 text-xs font-medium text-amber-600">
                                    <Heart :size="12" />
                                    Favorit
                                </span>
                            </div>

                            <div class="mt-5 flex items-center justify-between gap-2 border-t border-slate-100 pt-4">
                                <button
                                    type="button"
                                    class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-indigo-600 transition hover:bg-indigo-50"
                                    @click="openDetail(note)"
                                >
                                    <BookOpen :size="17" />
                                    Lihat detail
                                </button>
                                <div class="flex items-center gap-1">
                                    <button
                                        type="button"
                                        aria-label="Edit catatan"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                                        @click="openEditModal(note)"
                                    >
                                        <Pencil :size="17" />
                                    </button>
                                    <button
                                        type="button"
                                        aria-label="Hapus catatan"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 hover:bg-rose-50 hover:text-rose-600"
                                        @click="confirmDelete(note)"
                                    >
                                        <Trash2 :size="17" />
                                    </button>
                                </div>
                            </div>
                        </article>
                    </div>

                    <!-- Kondisi kosong -->
                    <div v-else class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-500">
                            <BookOpen :size="28" />
                        </div>
                        <h3 class="mt-5 text-lg font-semibold text-slate-900">
                            {{ hasActiveFilters ? 'Catatan tidak ditemukan' : 'Belum ada catatan' }}
                        </h3>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                            {{ hasActiveFilters
                                ? 'Coba kata kunci atau filter lain untuk menemukan catatan.'
                                : 'Mulai tulis ide, catatan belajar, atau hal penting yang ingin kamu simpan.' }}
                        </p>
                        <button
                            v-if="hasActiveFilters"
                            type="button"
                            class="mt-5 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold hover:bg-slate-50"
                            @click="clearFilters"
                        >
                            Reset pencarian
                        </button>
                        <button
                            v-else
                            type="button"
                            class="mt-5 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700"
                            @click="openCreateModal"
                        >
                            <Plus :size="17" />
                            Tulis catatan pertama
                        </button>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="(notes.last_page ?? 1) > 1"
                        class="mt-6 flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <p class="text-sm text-slate-500">
                            Menampilkan
                            <strong class="text-slate-800">{{ notes.from ?? 0 }}–{{ notes.to ?? 0 }}</strong>
                            dari
                            <strong class="text-slate-800">{{ notes.total ?? 0 }}</strong>
                            catatan
                        </p>
                        <div class="flex flex-wrap items-center gap-1">
                            <template v-for="(link, index) in notes.links" :key="`${link.label}-${index}`">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    preserve-scroll
                                    :aria-current="link.active ? 'page' : undefined"
                                    :class="link.active
                                        ? 'border-indigo-600 bg-indigo-600 text-white'
                                        : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'"
                                    class="flex h-9 min-w-9 items-center justify-center rounded-lg border px-3 text-sm transition"
                                >
                                    <ChevronLeft v-if="link.label.includes('Previous') || link.label.includes('&laquo;')" :size="16" />
                                    <ChevronRight v-else-if="link.label.includes('Next') || link.label.includes('&raquo;')" :size="16" />
                                    <span v-else v-html="link.label" />
                                </Link>
                                <span
                                    v-else
                                    class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-100 px-3 text-sm text-slate-300"
                                >
                                    <ChevronLeft v-if="link.label.includes('Previous') || link.label.includes('&laquo;')" :size="16" />
                                    <ChevronRight v-else-if="link.label.includes('Next') || link.label.includes('&raquo;')" :size="16" />
                                    <span v-else v-html="link.label" />
                                </span>
                            </template>
                        </div>
                    </div>
                </section>

                <footer class="text-center text-xs text-slate-400">Fluxa Personal Workspace</footer>
            </main>
        </div>

        <!-- Modal detail catatan -->
        <div
            v-if="showDetailModal && selectedNote"
            class="fixed inset-0 z-[70] flex items-center justify-center overflow-y-auto bg-slate-950/50 p-4 backdrop-blur-sm"
            @click.self="showDetailModal = false"
        >
            <section class="my-auto max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-3xl border border-white/80 bg-white p-6 shadow-2xl sm:p-8">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex min-w-0 items-start gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                            <FileText :size="24" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Detail catatan</p>
                            <h2 class="mt-2 break-words text-xl font-bold text-slate-900 sm:text-2xl">
                                {{ selectedNote.title }}
                            </h2>
                            <span class="mt-2 inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-medium text-slate-600">
                                <Folder :size="13" />
                                {{ selectedNote.category || 'Personal' }}
                            </span>
                        </div>
                    </div>
                    <button
                        type="button"
                        aria-label="Tutup detail"
                        class="shrink-0 rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                        @click="showDetailModal = false"
                    >
                        <X :size="20" />
                    </button>
                </div>

                <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <h3 class="mb-3 text-sm font-semibold text-slate-700">Isi catatan</h3>
                    <p class="min-h-24 whitespace-pre-wrap break-words text-sm leading-7 text-slate-700">{{ selectedNote.content || 'Belum ada isi catatan.' }}</p>
                </div>

                <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="flex items-center gap-2 text-slate-500">
                            <FileText :size="16" />
                            <span class="text-xs font-medium">Tanggal dibuat</span>
                        </div>
                        <p class="mt-2 text-sm font-semibold text-slate-800">
                            {{ formatDateTime(selectedNote.created_at) }}
                        </p>
                    </div>
                    <div class="rounded-xl border border-slate-200 p-4">
                        <div class="flex items-center gap-2 text-slate-500">
                            <Pencil :size="16" />
                            <span class="text-xs font-medium">Terakhir diperbarui</span>
                        </div>
                        <p class="mt-2 text-sm font-semibold text-slate-800">
                            {{ formatDateTime(selectedNote.updated_at) }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-5">
                    <button
                        type="button"
                        class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        @click="showDetailModal = false"
                    >
                        Tutup
                    </button>
                    <button
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
                        @click="openEditModal(selectedNote)"
                    >
                        <Pencil :size="16" />
                        Edit catatan
                    </button>
                </div>
            </section>
        </div>

        <!-- Modal tambah / edit -->
        <div
            v-if="showModal"
            class="fixed inset-0 z-[60] flex items-center justify-center overflow-y-auto bg-slate-950/50 p-4 backdrop-blur-sm"
            @click.self="closeModal"
        >
            <section class="my-auto w-full max-w-2xl rounded-3xl border border-white/80 bg-white p-6 shadow-2xl sm:p-8">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Personal Notes</p>
                        <h2 class="mt-2 text-xl font-bold text-slate-900">
                            {{ modalMode === 'create' ? 'Tulis catatan baru' : 'Edit catatan' }}
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">Simpan ide dan hal pentingmu di sini.</p>
                    </div>
                    <button
                        type="button"
                        aria-label="Tutup modal"
                        :disabled="activeForm.processing"
                        class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 disabled:opacity-40"
                        @click="closeModal"
                    >
                        <X :size="20" />
                    </button>
                </div>

                <form class="mt-6 space-y-5" @submit.prevent="submitNote">
                    <div>
                        <label for="note-title" class="mb-2 block text-sm font-semibold text-slate-700">Judul catatan</label>
                        <input
                            id="note-title"
                            v-model="activeForm.title"
                            type="text"
                            maxlength="255"
                            required
                            autofocus
                            placeholder="Contoh: Ide proyek baru"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                        />
                        <p v-if="activeForm.errors.title" class="mt-1.5 text-xs text-rose-600">{{ activeForm.errors.title }}</p>
                    </div>

                    <div>
                        <label for="note-category" class="mb-2 block text-sm font-semibold text-slate-700">Kategori</label>
                        <input
                            id="note-category"
                            v-model="activeForm.category"
                            type="text"
                            maxlength="100"
                            required
                            list="note-category-options"
                            placeholder="Contoh: Personal, Belajar, Ide"
                            class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                        />
                        <datalist id="note-category-options">
                            <option value="Personal" />
                            <option value="Belajar" />
                            <option value="Ide" />
                            <option value="Pekerjaan" />
                            <option value="Keuangan" />
                            <option v-for="category in categories" :key="category" :value="category" />
                        </datalist>
                        <p v-if="activeForm.errors.category" class="mt-1.5 text-xs text-rose-600">{{ activeForm.errors.category }}</p>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <label for="note-content" class="block text-sm font-semibold text-slate-700">Isi catatan</label>
                            <span class="text-xs text-slate-400">Opsional</span>
                        </div>
                        <textarea
                            id="note-content"
                            v-model="activeForm.content"
                            rows="8"
                            maxlength="50000"
                            placeholder="Tuliskan ide, informasi, atau catatanmu..."
                            class="w-full resize-y rounded-xl border border-slate-200 px-4 py-3 text-sm leading-6 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                        />
                        <p v-if="activeForm.errors.content" class="mt-1.5 text-xs text-rose-600">{{ activeForm.errors.content }}</p>
                        <p class="mt-1 text-right text-xs text-slate-400">{{ activeForm.content?.length ?? 0 }} / 50.000 karakter</p>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                        <button
                            type="button"
                            :disabled="activeForm.processing"
                            class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50"
                            @click="closeModal"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="activeForm.processing"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <LoaderCircle v-if="activeForm.processing" :size="17" class="animate-spin" />
                            <Check v-else :size="17" />
                            {{ activeForm.processing
                                ? 'Menyimpan...'
                                : modalMode === 'create'
                                    ? 'Simpan catatan'
                                    : 'Simpan perubahan' }}
                        </button>
                    </div>
                </form>
            </section>
        </div>

        <!-- Konfirmasi hapus -->
        <ConfirmDialog
            :show="showDeleteDialog"
            title="Hapus catatan ini?"
            :message="`Catatan '${selectedNote?.title ?? ''}' akan dihapus permanen dan tidak dapat dikembalikan.`"
            :loading="deleting"
            @cancel="closeDeleteDialog"
            @confirm="deleteNote"
        />
    </AppSidebarLayout>
</template>