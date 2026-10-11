<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class BookmarkController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $search = trim((string) $request->query('search', ''));
        $category = trim((string) $request->query('category', 'all'));
        $filter = (string) $request->query('filter', 'all');

        if (!in_array($filter, ['all', 'favorites'], true)) {
            $filter = 'all';
        }

        $query = $user->bookmarks()->latest();

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('url', 'like', "%{$search}%");
            });
        }

        if ($category !== '' && $category !== 'all') {
            $query->where('category', $category);
        }

        if ($filter === 'favorites') {
            $query->where('is_favorite', true);
        }

        $bookmarks = $query->paginate(10)->withQueryString();

        $categories = $user->bookmarks()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $total = $user->bookmarks()->count();

        $favoriteCount = $user->bookmarks()
            ->where('is_favorite', true)
            ->count();

        return Inertia::render('Bookmarks', [
            'bookmarks' => $bookmarks,
            'categories' => $categories,
            'stats' => [
                'total' => $total,
                'favorites' => $favoriteCount,
            ],
            'filters' => [
                'search' => $search,
                'category' => $category,
                'filter' => $filter,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:2048'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['required', 'string', 'max:100'],
        ]);

        $validated['is_favorite'] = false;

        $request->user()->bookmarks()->create($validated);

        return to_route('bookmarks.index')
            ->with('success', 'Bookmark berhasil ditambahkan.');
    }

    public function update(
        Request $request,
        string $bookmark
    ): RedirectResponse {
        $bookmarkModel = $request->user()
            ->bookmarks()
            ->findOrFail($bookmark);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:2048'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['required', 'string', 'max:100'],
        ]);

        $bookmarkModel->update($validated);

        return back()->with('success', 'Bookmark berhasil diperbarui.');
    }

    public function toggleFavorite(
        Request $request,
        string $bookmark
    ): RedirectResponse {
        $bookmarkModel = $request->user()
            ->bookmarks()
            ->findOrFail($bookmark);

        $bookmarkModel->update([
            'is_favorite' => !$bookmarkModel->is_favorite,
        ]);

        return back()->with('success', 'Status favorit berhasil diperbarui.');
    }

    public function destroy(
        Request $request,
        string $bookmark
    ): RedirectResponse {
        $bookmarkModel = $request->user()
            ->bookmarks()
            ->findOrFail($bookmark);

        $bookmarkModel->delete();

        return back()->with('success', 'Bookmark berhasil dihapus.');
    }
}
