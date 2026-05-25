<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeedbackController extends Controller
{
    public const TYPES = [
        'System Experience',
        'Product Quality',
        'Farmer/Seller Experience',
        'Order Experience',
        'Other',
    ];

    public function index()
    {
        $user = auth()->user();

        return view('consumer.feedback', [
            'feedbackTypes' => self::TYPES,
            'orders' => $user->orders()->latest()->get(),
            'feedback' => $user->feedback()->with('order')->latest()->get(),
        ]);
    }

    public function store(Request $request, NotificationService $notifications)
    {
        $validated = $request->validate([
            'feedback_type' => ['required', Rule::in(self::TYPES)],
            'order_id' => [
                'nullable',
                Rule::exists('orders', 'id')->where(fn ($query) => $query->where('user_id', auth()->id())),
            ],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $feedback = Feedback::create([
            'user_id' => auth()->id(),
            'order_id' => $validated['order_id'] ?? null,
            'feedback_type' => $validated['feedback_type'],
            'rating' => $validated['rating'],
            'message' => $validated['message'],
            'status' => 'unread',
        ]);

        $feedback->loadMissing(['user', 'order.items.farmer']);

        $notifications->sendToAdmins(
            'feedback.submitted',
            'New feedback submitted',
            ($feedback->user->name ?? 'A buyer') . " submitted {$feedback->feedback_type} feedback.",
            'star',
            route('admin.user-reports'),
            ['feedback_id' => $feedback->id]
        );

        if ($feedback->order) {
            $feedback->order->items
                ->pluck('farmer')
                ->filter()
                ->unique('id')
                ->each(function ($farmer) use ($feedback, $notifications) {
                    $notifications->send(
                        $farmer,
                        'feedback.submitted',
                        'Buyer feedback received',
                        ($feedback->user->name ?? 'A buyer') . " submitted feedback for Order #{$feedback->order_id}.",
                        'star',
                        route('orders.show', $feedback->order),
                        ['feedback_id' => $feedback->id, 'order_id' => $feedback->order_id]
                    );
                });
        }

        return back()->with('success', 'Feedback submitted successfully.');
    }
}
