@csrf

<div class="space-y-5" x-data="{ ruleType: @js(old('rule_type', $coupon->rule_type ?? 'amount')) }">
    <div>
        <label for="code" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-gray-200">Coupon Code</label>
        <input id="code" name="code" type="text" value="{{ old('code', $coupon->code) }}" required maxlength="50" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100 dark:border-gray-800 dark:bg-gray-900 dark:text-white" placeholder="BULK10KG">
        @error('code')<p class="mt-2 text-sm font-semibold text-red-600 dark:text-red-300">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="rule_type" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-gray-200">Discount Rule</label>
        <select id="rule_type" name="rule_type" x-model="ruleType" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
            <option value="amount">Order Amount</option>
            <option value="kilogram">Total Kilograms</option>
        </select>
        @error('rule_type')<p class="mt-2 text-sm font-semibold text-red-600 dark:text-red-300">{{ $message }}</p>@enderror
    </div>

    <div x-show="ruleType === 'kilogram'" x-cloak>
        <label for="minimum_kg" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-gray-200">Minimum Kilograms Required</label>
        <input id="minimum_kg" name="minimum_kg" type="number" step="0.01" min="0" value="{{ old('minimum_kg', $coupon->minimum_kg) }}" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
        <p class="mt-1 text-sm text-slate-500 dark:text-gray-400">This coupon can only be used when the buyer orders at least this total kg.</p>
        @error('minimum_kg')<p class="mt-2 text-sm font-semibold text-red-600 dark:text-red-300">{{ $message }}</p>@enderror
    </div>

    <div x-show="ruleType === 'amount'" x-cloak>
        <label for="minimum_order_amount" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-gray-200">Minimum Order Amount</label>
        <input id="minimum_order_amount" name="minimum_order_amount" type="number" step="0.01" min="0" value="{{ old('minimum_order_amount', $coupon->minimum_order_amount) }}" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
        @error('minimum_order_amount')<p class="mt-2 text-sm font-semibold text-red-600 dark:text-red-300">{{ $message }}</p>@enderror
    </div>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label for="type" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-gray-200">Discount Type</label>
            <select id="type" name="type" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
                <option value="fixed" @selected(old('type', $coupon->type ?? 'fixed') === 'fixed')>Fixed Amount</option>
                <option value="percent" @selected(old('type', $coupon->type ?? 'fixed') === 'percent')>Percent</option>
            </select>
            @error('type')<p class="mt-2 text-sm font-semibold text-red-600 dark:text-red-300">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="value" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-gray-200">Discount Value</label>
            <input id="value" name="value" type="number" step="0.01" min="0.01" value="{{ old('value', $coupon->value) }}" required class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
            @error('value')<p class="mt-2 text-sm font-semibold text-red-600 dark:text-red-300">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div>
            <label for="usage_limit" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-gray-200">Usage Limit</label>
            <input id="usage_limit" name="usage_limit" type="number" min="1" value="{{ old('usage_limit', $coupon->usage_limit) }}" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
        </div>
        <div>
            <label for="starts_at" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-gray-200">Starts At</label>
            <input id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at', optional($coupon->starts_at)->format('Y-m-d\TH:i')) }}" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
        </div>
        <div>
            <label for="expires_at" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-gray-200">Expires At</label>
            <input id="expires_at" name="expires_at" type="datetime-local" value="{{ old('expires_at', optional($coupon->expires_at)->format('Y-m-d\TH:i')) }}" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-900 focus:border-emerald-600 focus:outline-none focus:ring-4 focus:ring-emerald-100 dark:border-gray-800 dark:bg-gray-900 dark:text-white">
        </div>
    </div>

    <label class="flex items-center gap-3 rounded-xl border border-slate-100 p-4 dark:border-gray-800">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $coupon->is_active ?? true)) class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
        <span class="text-sm font-semibold text-slate-700 dark:text-gray-200">Active coupon</span>
    </label>

    <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 dark:border-gray-800 sm:flex-row">
        <x-ui.primary-button>{{ $submitLabel }}</x-ui.primary-button>
        <x-ui.secondary-button href="{{ route('admin.coupons.index') }}">Cancel</x-ui.secondary-button>
    </div>
</div>
