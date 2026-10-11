<script setup>
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import BookmarkFavicon from '@/components/BookmarkFavicon.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    ArrowUpRight, Bookmark, Check, ChevronLeft, ChevronRight,
    ExternalLink, Folder, LoaderCircle, Pencil, Plus, Search,
    Star, Trash2, X,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    bookmarks: {
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
    auth: { type: Object, default: () => ({ user: null }) },
});

const search = ref(props.filters?.search ?? '');
const selectedCategory = ref(props.filters?.category ?? 'all');
const selectedFilter = ref(props.filters?.filter ?? 'all');

const showModal = ref(false);
const modalMode = ref('create');
const showDeleteModal = ref(false);
const selectedBookmark = ref(null);
const deleting = ref(false);

const toast = ref({ show: false, message: '', type: 'success' });
let toastTimer = null;

const createForm = useForm({
    title: '',
    url: '',
    description: '',
    category: 'Personal',
});

const editForm = useForm({
    title: '',
    url: '',
    description: '',
    category: 'Personal',
    id: null,
});

const activeForm = computed(() =>
    modalMode.value === 'edit' ? editForm : createForm,
);

const bookmarksList = computed(() => props.bookmarks?.data ?? []);
const userName = computed(() => props.auth?.user?.name ?? 'Pengguna');

const initials = computed(() =>
    userName.value.split(' ').filter(Boolean).slice(0, 2)
        .map((part) => part[0]).join('').toUpperCase(),
);

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
    router.get('/bookmarks', {
        search: search.value.trim() || undefined,
        category: selectedCategory.value !== 'all' ? selectedCategory.value : undefined,
        filter: selectedFilter.value !== 'all' ? selectedFilter.value : undefined,
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

function openCreateModal() {
    modalMode.value = 'create';
    createForm.reset();
    createForm.clearErrors();
    createForm.category = 'Personal';
    showModal.value = true;
}

function openEditModal(bookmark) {
    modalMode.value = 'edit';
    editForm.clearErrors();
    editForm.id = bookmark.id;
    editForm.title = bookmark.title ?? '';
    editForm.url = bookmark.url ?? '';
    editForm.description = bookmark.description ?? '';
    editForm.category = bookmark.category ?? 'Personal';
    showModal.value = true;
}

function closeModal() {
    if (activeForm.value.processing) return;
    showModal.value = false;
    createForm.clearErrors();
    editForm.clearErrors();
}

function submitBookmark() {
    if (activeForm.value.processing) return;

    if (modalMode.value === 'create') {
        createForm.post('/bookmarks', {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
                createForm.reset();
                search.value = '';
                selectedCategory.value = 'all';
                selectedFilter.value = 'all';
                showToast('Bookmark berhasil ditambahkan.');
                router.get('/bookmarks', {}, {
                    preserveScroll: true,
                    replace: true,
                });
            },
            onError: () => showToast('Periksa kembali data bookmark.', 'error'),
        });
        return;
    }

    editForm.patch(`/bookmarks/${editForm.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showModal.value = false;
            showToast('Bookmark berhasil diperbarui.');
        },
        onError: () => showToast('Bookmark gagal diperbarui.', 'error'),
    });
}

function toggleFavorite(bookmark) {
    router.patch(`/bookmarks/${bookmark.id}/favorite`, {}, {
        preserveScroll: true,
        onSuccess: () => showToast(
            bookmark.is_favorite
                ? 'Bookmark dihapus dari favorit.'
                : 'Bookmark ditambahkan ke favorit.',
        ),
        onError: () => showToast('Gagal memperbarui favorit.', 'error'),
    });
}

function confirmDelete(bookmark) {
    selectedBookmark.value = bookmark;
    showDeleteModal.value = true;
}

function closeDeleteModal() {
    if (deleting.value) return;
    showDeleteModal.value = false;
    selectedBookmark.value = null;
}

function deleteBookmark() {
    if (!selectedBookmark.value || deleting.value) return;
    deleting.value = true;

    router.delete(`/bookmarks/${selectedBookmark.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false;
            selectedBookmark.value = null;
            showToast('Bookmark berhasil dihapus.');
        },
        onError: () => showToast('Bookmark gagal dihapus.', 'error'),
        onFinish: () => { deleting.value = false; },
    });
}

function formatDate(date) {
    if (!date) return '-';
    const parsed = new Date(date);
    if (Number.isNaN(parsed.getTime())) return '-';
    return parsed.toLocaleDateString('id-ID', {
        day: 'numeric', month: 'short', year: 'numeric',
    });
}

function handleEscape(event) {
    if (event.key !== 'Escape') return;
    closeModal();
    closeDeleteModal();
}

onMounted(() => window.addEventListener('keydown', handleEscape));

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleEscape);
    if (toastTimer) clearTimeout(toastTimer);
});
</script>

<template>
    <Head title="Bookmarks - Fluxa" />

    <AppSidebarLayout
        :breadcrumbs="[
            { title: 'Workspace', href: '/' },
            { title: 'Bookmarks', href: '/bookmarks' },
        ]"
    >
        <div class="min-h-screen bg-slate-50 text-slate-800">
            <!-- Toast notification -->
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
                        <p class="min-w-0 flex-1 text-sm font-medium text-slate-700">{{ toast.message }}</p>
                        <button aria-label="Tutup notifikasi" class="text-slate-400 hover:text-slate-700" @click="closeToast"><X :size="17" /></button>
                    </div>
                </Transition>
            </div>

            <main class="mx-auto max-w-7xl space-y-7 p-4 md:p-8 lg:p-10">
                <!-- Intro -->
                <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-600 to-violet-700 p-6 text-white md:p-8">
                    <div class="relative z-10 max-w-2xl">
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15"><Bookmark :size="25" /></div>
                        <h1 class="text-2xl font-bold md:text-3xl">Semua tautan penting, satu tempat.</h1>
                        <p class="mt-3 max-w-xl text-sm leading-6 text-indigo-100 md:text-base">Simpan website favoritmu, kelompokkan berdasarkan kategori, dan temukan kembali kapan saja.</p>
                    </div>
                    <div class="pointer-events-none absolute -right-8 -top-14 h-64 w-64 rounded-full border-[35px] border-white/10" />
                    <div class="pointer-events-none absolute -bottom-24 right-28 h-48 w-48 rounded-full bg-white/5" />
                </section>

                <!-- Statistik -->
                <section class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-slate-500">Total bookmark</p>
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600"><Bookmark :size="20" /></div>
                        </div>
                        <p class="mt-3 text-3xl font-bold text-slate-900">{{ stats.total ?? 0 }}</p>
                        <p class="mt-1 text-xs text-slate-500">Tautan tersimpan</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-slate-500">Favorit</p>
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-500"><Star :size="20" /></div>
                        </div>
                        <p class="mt-3 text-3xl font-bold text-slate-900">{{ stats.favorites ?? 0 }}</p>
                        <p class="mt-1 text-xs text-slate-500">Tautan pilihanmu</p>
                    </div>
                </section>

                <!-- Search dan filter -->
                <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:p-5">
                    <form class="flex flex-col gap-3 md:flex-row" @submit.prevent="applyFilters">
                        <div class="relative min-w-0 flex-1">
                            <Search :size="19" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400" />
                            <input v-model="search" type="search" placeholder="Cari nama atau URL bookmark..." class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-11 pr-4 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" />
                        </div>
                        <select v-model="selectedCategory" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 md:w-52">
                            <option value="all">Semua kategori</option>
                            <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
                        </select>
                        <button type="submit" class="flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-700"><Search :size="17" /> Cari</button>
                        <button type="button" class="flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700" @click="openCreateModal"><Plus :size="18" /><span>Tambah Bookmark</span></button>
                    </form>

                    <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                        <button type="button" class="rounded-lg px-4 py-2 text-sm font-medium transition" :class="selectedFilter === 'all' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" @click="changeFilter('all')">Semua</button>
                        <button type="button" class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition" :class="selectedFilter === 'favorites' ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" @click="changeFilter('favorites')"><Star :size="15" /> Favorit</button>
                        <button v-if="hasActiveFilters" type="button" class="ml-auto flex items-center gap-1 rounded-lg px-3 py-2 text-sm text-slate-500 hover:bg-slate-100 hover:text-slate-800" @click="clearFilters"><X :size="15" /> Reset filter</button>
                    </div>
                </section>

                <!-- Koleksi bookmark -->
                <section>
                    <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Koleksi bookmark</h2>
                            <p class="mt-1 text-sm text-slate-500">Tautan yang kamu simpan.</p>
                        </div>
                        <p class="text-sm text-slate-500">{{ bookmarks.total ?? 0 }} hasil</p>
                    </div>

                    <div v-if="bookmarksList.length" class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                        <article v-for="bookmark in bookmarksList" :key="bookmark.id" class="group flex min-w-0 flex-col rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-200 hover:shadow-md">
                            <div class="flex items-start gap-3">
                                <!-- Favicon website menggantikan ikon Bookmark -->
                                <BookmarkFavicon
                                    :url="bookmark.url"
                                    :name="bookmark.title"
                                />

                                <div class="min-w-0 flex-1">
                                    <h3 class="break-words font-semibold text-slate-900">{{ bookmark.title }}</h3>
                                    <a :href="bookmark.url" target="_blank" rel="noopener noreferrer" class="mt-1 flex items-center gap-1 break-all text-xs text-indigo-600 hover:text-indigo-800">
                                        <span class="line-clamp-2">{{ bookmark.url }}</span><ArrowUpRight :size="14" class="shrink-0" />
                                    </a>
                                </div>
                                <button type="button" :aria-label="bookmark.is_favorite ? 'Hapus dari favorit' : 'Tambahkan ke favorit'" :class="bookmark.is_favorite ? 'bg-amber-50 text-amber-500' : 'text-slate-400 hover:bg-amber-50 hover:text-amber-500'" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl transition" @click="toggleFavorite(bookmark)">
                                    <Star :size="19" :fill="bookmark.is_favorite ? 'currentColor' : 'none'" />
                                </button>
                            </div>

                            <p v-if="bookmark.description" class="mt-4 line-clamp-3 text-sm leading-6 text-slate-600">{{ bookmark.description }}</p>

                            <div class="mt-4 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-medium text-slate-600"><Folder :size="13" />{{ bookmark.category || 'Personal' }}</span>
                                <span class="text-xs text-slate-400">{{ formatDate(bookmark.created_at) }}</span>
                            </div>

                            <div class="mt-5 flex items-center justify-between border-t border-slate-100 pt-4">
                                <a :href="bookmark.url" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-indigo-600 hover:bg-indigo-50"><ExternalLink :size="16" /> Buka tautan</a>
                                <div class="flex items-center gap-1">
                                    <button type="button" aria-label="Edit bookmark" class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700" @click="openEditModal(bookmark)"><Pencil :size="17" /></button>
                                    <button type="button" aria-label="Hapus bookmark" class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 hover:bg-rose-50 hover:text-rose-600" @click="confirmDelete(bookmark)"><Trash2 :size="17" /></button>
                                </div>
                            </div>
                        </article>
                    </div>

                    <!-- Empty state -->
                    <div v-else class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-500"><Bookmark :size="28" /></div>
                        <h3 class="mt-5 text-lg font-semibold text-slate-900">{{ hasActiveFilters ? 'Bookmark tidak ditemukan' : 'Belum ada bookmark' }}</h3>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">{{ hasActiveFilters ? 'Coba kata kunci atau filter lain untuk menemukan tautan.' : 'Simpan tautan website yang sering kamu gunakan agar mudah ditemukan kembali.' }}</p>
                        <button v-if="hasActiveFilters" type="button" class="mt-5 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold hover:bg-slate-50" @click="clearFilters">Reset pencarian</button>
                        <button v-else type="button" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700" @click="openCreateModal"><Plus :size="17" /> Tambah bookmark pertama</button>
                    </div>

                    <!-- Pagination -->
                    <div v-if="(bookmarks.last_page ?? 1) > 1" class="mt-6 flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-4 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-slate-500">Menampilkan <strong class="text-slate-800">{{ bookmarks.from ?? 0 }}–{{ bookmarks.to ?? 0 }}</strong> dari <strong class="text-slate-800">{{ bookmarks.total ?? 0 }}</strong> bookmark</p>
                        <div class="flex flex-wrap items-center gap-1">
                            <template v-for="(link, index) in bookmarks.links" :key="`${link.label}-${index}`">
                                <Link v-if="link.url" :href="link.url" preserve-scroll :aria-current="link.active ? 'page' : undefined" :class="link.active ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'" class="flex h-9 min-w-9 items-center justify-center rounded-lg border px-3 text-sm transition">
                                    <ChevronLeft v-if="link.label.includes('Previous') || link.label.includes('&laquo;')" :size="16" />
                                    <ChevronRight v-else-if="link.label.includes('Next') || link.label.includes('&raquo;')" :size="16" />
                                    <span v-else v-html="link.label" />
                                </Link>
                                <span v-else class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-100 px-3 text-sm text-slate-300">
                                    <ChevronLeft v-if="link.label.includes('Previous') || link.label.includes('&laquo;')" :size="16" />
                                    <ChevronRight v-else-if="link.label.includes('Next') || link.label.includes('&raquo;')" :size="16" />
                                    <span v-else v-html="link.label" />
                                </span>
                            </template>
                        </div>
                    </div>
                </section>

                <footer class="text-center text-xs text-slate-400">Fluxa Personal Workspace · {{ userName }}</footer>
            </main>
        </div>

        <!-- Modal create / edit -->
        <div v-if="showModal" class="fixed inset-0 z-[60] flex items-center justify-center overflow-y-auto bg-slate-950/50 p-4 backdrop-blur-sm" @click.self="closeModal">
            <div class="my-auto w-full max-w-lg overflow-hidden rounded-3xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">{{ modalMode === 'create' ? 'Tambah bookmark' : 'Edit bookmark' }}</h2>
                        <p class="mt-1 text-sm text-slate-500">Simpan informasi tautan website.</p>
                    </div>
                    <button type="button" class="flex h-9 w-9 items-center justify-center rounded-xl text-slate-500 hover:bg-slate-100" aria-label="Tutup" @click="closeModal"><X :size="20" /></button>
                </div>

                <form class="space-y-5 p-6" @submit.prevent="submitBookmark">
                    <div>
                        <label for="bookmark-title" class="mb-2 block text-sm font-medium text-slate-700">Nama bookmark <span class="text-rose-500">*</span></label>
                        <input id="bookmark-title" v-model="activeForm.title" type="text" required maxlength="255" placeholder="Contoh: Dokumentasi Laravel" class="w-full rounded-xl border bg-white px-4 py-3 text-slate-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" :class="activeForm.errors.title ? 'border-rose-400' : 'border-slate-200'" />
                        <p v-if="activeForm.errors.title" class="mt-1 text-xs text-rose-600">{{ activeForm.errors.title }}</p>
                    </div>

                    <div>
                        <label for="bookmark-url" class="mb-2 block text-sm font-medium text-slate-700">URL website <span class="text-rose-500">*</span></label>
                        <input id="bookmark-url" v-model="activeForm.url" type="url" required maxlength="2048" placeholder="https://laravel.com" class="w-full rounded-xl border bg-white px-4 py-3 text-slate-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" :class="activeForm.errors.url ? 'border-rose-400' : 'border-slate-200'" />
                        <p v-if="activeForm.errors.url" class="mt-1 text-xs text-rose-600">{{ activeForm.errors.url }}</p>
                    </div>

                    <div>
                        <label for="bookmark-category" class="mb-2 block text-sm font-medium text-slate-700">Kategori <span class="text-rose-500">*</span></label>
                        <input id="bookmark-category" v-model="activeForm.category" type="text" required maxlength="100" list="bookmark-category-options" placeholder="Contoh: Development" class="w-full rounded-xl border bg-white px-4 py-3 text-slate-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" :class="activeForm.errors.category ? 'border-rose-400' : 'border-slate-200'" />
                        <datalist id="bookmark-category-options">
                            <option value="Development" /><option value="Belajar" /><option value="Tools" /><option value="Design" /><option value="Personal" />
                        </datalist>
                        <p v-if="activeForm.errors.category" class="mt-1 text-xs text-rose-600">{{ activeForm.errors.category }}</p>
                    </div>

                    <div>
                        <label for="bookmark-description" class="mb-2 block text-sm font-medium text-slate-700">Deskripsi <span class="font-normal text-slate-400">(opsional)</span></label>
                        <textarea id="bookmark-description" v-model="activeForm.description" rows="3" maxlength="2000" placeholder="Tambahkan catatan singkat tentang tautan ini..." class="w-full resize-y rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" :class="activeForm.errors.description ? 'border-rose-400' : 'border-slate-200'" />
                        <p v-if="activeForm.errors.description" class="mt-1 text-xs text-rose-600">{{ activeForm.errors.description }}</p>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row">
                        <button type="button" :disabled="activeForm.processing" class="flex-1 rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50" @click="closeModal">Batal</button>
                        <button type="submit" :disabled="activeForm.processing" class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white hover:bg-indigo-700 disabled:opacity-60">
                            <LoaderCircle v-if="activeForm.processing" :size="17" class="animate-spin" /><Check v-else :size="17" />
                            {{ activeForm.processing ? 'Menyimpan...' : modalMode === 'create' ? 'Simpan bookmark' : 'Simpan perubahan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal hapus bookmark -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm" @click.self="closeDeleteModal">
            <div class="w-full max-w-sm rounded-3xl bg-white p-6 shadow-2xl">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 text-rose-600"><Trash2 :size="25" /></div>
                <h2 class="mt-5 text-center text-lg font-bold text-slate-900">Hapus bookmark?</h2>
                <p class="mt-2 text-center text-sm leading-6 text-slate-500">Bookmark <strong class="text-slate-700">{{ selectedBookmark?.title }}</strong> akan dihapus secara permanen.</p>
                <div class="mt-6 flex gap-3">
                    <button type="button" :disabled="deleting" class="flex-1 rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:opacity-50" @click="closeDeleteModal">Batal</button>
                    <button type="button" :disabled="deleting" class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-rose-600 px-4 py-3 text-sm font-semibold text-white hover:bg-rose-700 disabled:opacity-60" @click="deleteBookmark"><LoaderCircle v-if="deleting" class="animate-spin" :size="16" /><Trash2 v-else :size="16" />{{ deleting ? 'Menghapus...' : 'Ya, hapus' }}</button>
                </div>
            </div>
        </div>
    </AppSidebarLayout>
</template>