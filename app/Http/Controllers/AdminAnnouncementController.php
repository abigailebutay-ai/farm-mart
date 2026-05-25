<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AdminAnnouncementController extends Controller
{
    public function index()
    {
        return view('admin.announcements.index', [
            'announcements' => Announcement::with('creator')->latest()->paginate(10),
        ]);
    }

    public function create()
    {
        return view('admin.announcements.create', [
            'announcement' => new Announcement(['status' => 'published']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateAnnouncement($request);
        $validated['status'] = $validated['status'] ?? 'published';
        $validated['published_at'] = $validated['status'] === 'published' ? now() : null;
        $validated['created_by'] = auth()->id();

        Announcement::create($validated);

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement created successfully.');
    }

    public function edit(Announcement $announcement)
    {
        return view('admin.announcements.edit', [
            'announcement' => $announcement,
        ]);
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $this->validateAnnouncement($request);
        $validated['status'] = $validated['status'] ?? 'published';

        if ($validated['status'] === 'published' && ! $announcement->published_at) {
            $validated['published_at'] = now();
        }

        if ($validated['status'] === 'draft') {
            $validated['published_at'] = null;
        }

        $announcement->update($validated);

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement updated successfully.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();

        return redirect()
            ->route('admin.announcements.index')
            ->with('success', 'Announcement deleted successfully.');
    }

    private function validateAnnouncement(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'status' => ['nullable', 'in:published,draft'],
        ]);
    }
}
