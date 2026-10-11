<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $search = trim((string) $request->query('search', ''));
        $filter = $request->query('filter', 'all');

        if (! in_array($filter, ['all', 'active', 'completed'], true)) {
            $filter = 'all';
        }

        $stats = [
            'total' => $user->tasks()->count(),
            'active' => $user->tasks()->where('status', '!=', 'done')->count(),
            'in_progress' => $user->tasks()->where('status', 'in_progress')->count(),
            'completed' => $user->tasks()->where('status', 'done')->count(),
        ];

        $query = $user->tasks();

        if ($search !== '') {
            $query->where('title', 'like', '%' . $search . '%');
        }

        if ($filter === 'active') {
            $query->where('status', '!=', 'done');
        } elseif ($filter === 'completed') {
            $query->where('status', 'done');
        }

        $tasks = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('ToDoList', [
            'tasks' => $tasks,
            'user' => $user->only('name', 'email'),
            'stats' => $stats,
            'filters' => [
                'search' => $search,
                'filter' => $filter,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'in:low,medium,high'],
        ]);

        $validated['category'] = 'Personal';
        $validated['status'] = 'todo';

        $request->user()->tasks()->create($validated);

        return back()->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function update(Request $request, string $task)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:todo,in_progress,done'],
        ]);

        $taskModel = $request->user()->tasks()->findOrFail($task);

        $taskModel->status = $validated['status'];
        $taskModel->completed_at = $validated['status'] === 'done'
            ? ($taskModel->completed_at ?? now())
            : null;

        $taskModel->save();

        return back()->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Request $request, string $task)
    {
        $request->user()->tasks()->findOrFail($task)->delete();

        return back()->with('success', 'Tugas berhasil dihapus.');
    }

    /**
     * Menghapus beberapa tugas yang dipilih.
     */
    public function destroySelected(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:500'],
            'ids.*' => ['required', 'integer', 'distinct'],
        ]);

        $user = $request->user();

        // Hanya menghapus tugas milik pengguna yang sedang login.
        $deletedCount = $user->tasks()
            ->whereIn('id', $validated['ids'])
            ->delete();

        if ($deletedCount === 0) {
            return back()->with('error', 'Tidak ada tugas terpilih yang dapat dihapus.');
        }

        return back()->with(
            'success',
            "{$deletedCount} tugas terpilih berhasil dihapus."
        );
    }

    /**
     * Menghapus seluruh tugas milik pengguna yang sedang login.
     */
    public function destroyAll(Request $request)
    {
        $deletedCount = $request->user()->tasks()->count();

        if ($deletedCount === 0) {
            return back()->with('error', 'Tidak ada tugas untuk dihapus.');
        }

        DB::transaction(function () use ($request) {
            $request->user()->tasks()->delete();
        });

        return back()->with(
            'success',
            "{$deletedCount} tugas berhasil dihapus."
        );
    }
}
