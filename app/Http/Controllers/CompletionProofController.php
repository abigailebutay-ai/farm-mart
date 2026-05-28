<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompletionProofController extends Controller
{
    public function show(string $path)
    {
        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');

        if (! Str::startsWith($path, 'completion_proofs/')) {
            abort(404);
        }

        $order = Order::where('completion_proof', $path)->firstOrFail();
        $user = auth()->user();

        $canView = $user->isAdmin()
            || ($user->isConsumer() && $order->user_id === $user->id)
            || ($user->isFarmer() && $order->items()->where('farmer_id', $user->id)->exists());

        abort_unless($canView, 403);

        $disk = Storage::disk(config('filesystems.default'));

        if (! $disk->exists($path)) {
            abort(404);
        }

        return $disk->response($path);
    }
}
