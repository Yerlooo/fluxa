<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class NoteController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $search = trim((string) $request->query('search', ''));
        $category = trim((string) $request->query('category', 'all'));
        $filter = (string) $request->query('filter', 'all');

        if (! in_array($filter, ['all', 'favorites'], true)) {
            $filter = 'all';
        }

        $query = $user->notes()->latest();

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($category !== '' && $category !== 'all') {
            $query->where('category', $category);
        }

        if ($filter === 'favorites') {
            $query->where('is_favorite', true);
        }

        $notes = $query->paginate(12)->withQueryString();

        $categories = $user->notes()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return Inertia::render('Notes', [
            'notes' => $notes,
            'categories' => $categories,
            'stats' => [
                'total' => $user->notes()->count(),
                'favorites' => $user->notes()
                    ->where('is_favorite', true)
                    ->count(),
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
            'content' => ['nullable', 'string', 'max:50000'],
            'category' => ['required', 'string', 'max:100'],
        ]);

        $validated['is_favorite'] = false;

        $request->user()->notes()->create($validated);

        return to_route('notes.index')
            ->with('success', 'Catatan berhasil ditambahkan.');
    }

    public function update(
        Request $request,
        string $note
    ): RedirectResponse {
        $noteModel = $request->user()
            ->notes()
            ->findOrFail($note);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:50000'],
            'category' => ['required', 'string', 'max:100'],
        ]);

        $noteModel->update($validated);

        return back()->with('success', 'Catatan berhasil diperbarui.');
    }

    public function toggleFavorite(
        Request $request,
        string $note
    ): RedirectResponse {
        $noteModel = $request->user()
            ->notes()
            ->findOrFail($note);

        $noteModel->update([
            'is_favorite' => ! $noteModel->is_favorite,
        ]);

        return back()->with('success', 'Status favorit catatan diperbarui.');
    }

    public function destroy(
        Request $request,
        string $note
    ): RedirectResponse {
        $noteModel = $request->user()
            ->notes()
            ->findOrFail($note);

        $noteModel->delete();

        return back()->with('success', 'Catatan berhasil dihapus.');
    }
}
