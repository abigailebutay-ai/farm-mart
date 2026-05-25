<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\User;
use App\Services\NotificationService;
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

    public function store(Request $request, NotificationService $notifications)
    {
        $validated = $this->validateAnnouncement($request);
        $validated['status'] = $validated['status'] ?? 'published';
        $validated['published_at'] = $validated['status'] === 'published' ? now() : null;
        $validated['created_by'] = auth()->id();

        $announcement = Announcement::create($validated);

        if ($announcement->status === 'published') {
            $this->notifyPublishedAnnouncement($announcement, $notifications);
        }

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

    public function update(Request $request, Announcement $announcement, NotificationService $notifications)
    {
        $validated = $this->validateAnnouncement($request);
        $validated['status'] = $validated['status'] ?? 'published';
        $wasPublished = $announcement->status === 'published';

        if ($validated['status'] === 'published' && ! $announcement->published_at) {
            $validated['published_at'] = now();
        }

        if ($validated['status'] === 'draft') {
            $validated['published_at'] = null;
        }

        $announcement->update($validated);

        if (! $wasPublished && $announcement->status === 'published') {
            $this->notifyPublishedAnnouncement($announcement, $notifications);
        }

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

    private function notifyPublishedAnnouncement(Announcement $announcement, NotificationService $notifications): void
    {
        $notifications->sendToUsers(
            User::whereIn('role', ['farmer', 'consumer', 'buyer'])->get(),
            'announcement.published',
            'New announcement',
            $announcement->title,
            'megaphone',
            route('home'),
            ['announcement_id' => $announcement->id]
        );
    }
}
