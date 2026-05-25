<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class NotificationService
{
    public function send(
        User $user,
        string $type,
        string $title,
        string $message,
        string $icon = 'bell',
        ?string $url = null,
        array $data = []
    ): ?Notification {
        if (! $user->notification_enabled) {
            return null;
        }

        return $user->notifications()->create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => array_merge($data, [
                'icon' => $icon,
                'url' => $url,
            ]),
        ]);
    }

    public function sendToAdmins(
        string $type,
        string $title,
        string $message,
        string $icon = 'bell',
        ?string $url = null,
        array $data = []
    ): void {
        User::where('role', 'admin')->get()->each(function (User $admin) use ($type, $title, $message, $icon, $url, $data) {
            $this->send($admin, $type, $title, $message, $icon, $url, $data);
        });
    }

    /**
     * @param  Collection<int, User>|EloquentCollection<int, User>|iterable<User>  $users
     */
    public function sendToUsers(
        iterable $users,
        string $type,
        string $title,
        string $message,
        string $icon = 'bell',
        ?string $url = null,
        array $data = []
    ): void {
        foreach ($users as $user) {
            $this->send($user, $type, $title, $message, $icon, $url, $data);
        }
    }
}
