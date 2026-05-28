<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;

class AdminCouponController extends Controller
{
    public function index()
    {
        return view('admin.coupons.index', [
            'coupons' => Coupon::latest()->paginate(10),
        ]);
    }

    public function create()
    {
        return view('admin.coupons.create', [
            'coupon' => new Coupon([
                'rule_type' => 'amount',
                'type' => 'fixed',
                'is_active' => true,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        Coupon::create($this->validateCoupon($request));

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', [
            'coupon' => $coupon,
        ]);
    }

    public function update(Request $request, Coupon $coupon)
    {
        $coupon->update($this->validateCoupon($request, $coupon));

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }

    private function validateCoupon(Request $request, ?Coupon $coupon = null): array
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('coupons', 'code')->ignore($coupon),
            ],
            'rule_type' => ['required', 'in:amount,kilogram'],
            'minimum_kg' => [new RequiredIf($request->input('rule_type') === 'kilogram'), 'nullable', 'numeric', 'min:0.01'],
            'type' => ['required', 'in:fixed,percent'],
            'value' => ['required', 'numeric', 'min:0.01'],
            'minimum_order_amount' => [new RequiredIf($request->input('rule_type') === 'amount'), 'nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = $request->boolean('is_active');
        $validated['minimum_kg'] = $validated['rule_type'] === 'kilogram' ? $validated['minimum_kg'] : null;

        return $validated;
    }
}
