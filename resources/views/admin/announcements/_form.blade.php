@csrf

<div class="space-y-5">
    <div>
        <label for="title" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-gray-200">Title</label>
        <input
            id="title"
            name="title"
            type="text"
            value="{{ old('title', $announcement->title) }}"
            required
            maxlength="255"
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100 dark:border-gray-800 dark:bg-gray-900 dark:text-white"
        >
        @error('title')
            <p class="mt-2 text-sm font-semibold text-red-600 dark:text-red-300">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="body" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-gray-200">Body</label>
        <textarea
            id="body"
            name="body"
            rows="7"
            required
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100 dark:border-gray-800 dark:bg-gray-900 dark:text-white"
        >{{ old('body', $announcement->body) }}</textarea>
        @error('body')
            <p class="mt-2 text-sm font-semibold text-red-600 dark:text-red-300">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="status" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-gray-200">Status</label>
        <select
            id="status"
            name="status"
            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100 dark:border-gray-800 dark:bg-gray-900 dark:text-white"
        >
            <option value="published" @selected(old('status', $announcement->status ?? 'published') === 'published')>Published</option>
            <option value="draft" @selected(old('status', $announcement->status ?? 'published') === 'draft')>Draft</option>
        </select>
        @error('status')
            <p class="mt-2 text-sm font-semibold text-red-600 dark:text-red-300">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 dark:border-gray-800 sm:flex-row">
        <x-ui.primary-button>{{ $submitLabel }}</x-ui.primary-button>
        <x-ui.secondary-button href="{{ route('admin.announcements.index') }}">Cancel</x-ui.secondary-button>
    </div>
</div>
