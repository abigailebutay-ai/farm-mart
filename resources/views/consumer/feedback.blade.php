@extends('layouts.app')

@section('page-title', 'Feedback')

@section('content')
    <x-ui.page-header
        title="Feedback"
        subtitle="Share your experience with SariwaLink."
    />

    <div class="grid gap-5 xl:grid-cols-[0.95fr_1.05fr]">
        <x-ui.dashboard-card class="buyer-card" title="Submit Feedback" subtitle="Tell us what worked well or what needs attention.">
            <form method="POST" action="{{ route('consumer.feedback.store') }}" class="grid gap-4">
                @csrf

                <div>
                    <label for="feedback_type" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">Feedback Type</label>
                    <select id="feedback_type" name="feedback_type" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-800 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100" required>
                        <option value="">Select feedback type</option>
                        @foreach($feedbackTypes as $type)
                            <option value="{{ $type }}" @selected(old('feedback_type') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('feedback_type')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="order_id" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">Related Order</label>
                    <select id="order_id" name="order_id" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-800 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100">
                        <option value="">No specific order</option>
                        @foreach($orders as $order)
                            <option value="{{ $order->id }}" @selected((string) old('order_id') === (string) $order->id)>
                                Order #{{ $order->id }} - {{ $order->created_at->timezone(config('app.timezone'))->format('M d, Y') }} - PHP {{ number_format($order->total, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('order_id')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="rating" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">Rating</label>
                    <select id="rating" name="rating" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-800 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100" required>
                        <option value="">Select rating</option>
                        @for($rating = 5; $rating >= 1; $rating--)
                            <option value="{{ $rating }}" @selected((string) old('rating') === (string) $rating)>
                                {{ $rating }} {{ \Illuminate\Support\Str::plural('star', $rating) }}
                            </option>
                        @endfor
                    </select>
                    @error('rating')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="message" class="mb-2 block text-sm font-bold text-slate-700 dark:text-slate-200">Message</label>
                    <textarea id="message" name="message" rows="5" maxlength="1000" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-slate-800 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100" placeholder="Write your feedback here..." required>{{ old('message') }}</textarea>
                    <p class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">Maximum 1000 characters.</p>
                    @error('message')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <x-ui.primary-button class="w-full sm:w-auto">Submit Feedback</x-ui.primary-button>
                </div>
            </form>
        </x-ui.dashboard-card>

        <x-ui.dashboard-card class="receipt-card" title="My Feedback" subtitle="Your recent feedback submissions.">
            <div class="space-y-3">
                @forelse($feedback as $item)
                    <article class="purchase-card rounded-xl border p-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="buyer-text font-bold">{{ $item->feedback_type }}</p>
                                    <x-ui.status-badge :status="$item->status" />
                                </div>
                                <p class="buyer-muted mt-1 text-sm">
                                    Rating: {{ $item->rating }}/5
                                    @if($item->order)
                                        - Order #{{ $item->order->id }}
                                    @else
                                        - No specific order
                                    @endif
                                </p>
                            </div>
                            <p class="buyer-muted text-sm">{{ $item->created_at->format('M d, Y') }}</p>
                        </div>
                        <p class="buyer-text mt-3 text-sm leading-relaxed">{{ $item->message }}</p>
                    </article>
                @empty
                    <x-ui.empty-state title="No feedback submitted yet" message="Your submitted feedback will appear here." icon="star" />
                @endforelse
            </div>
        </x-ui.dashboard-card>
    </div>
@endsection
