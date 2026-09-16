<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartService
{
    private const SESSION_KEY = 'cart_session_id';

    private const COUPON_SESSION_KEY = 'coupon_code';

    public function currentCart(Request $request): Cart
    {
        if ($request->user()) {
            return Cart::firstOrCreate(['user_id' => $request->user()->id]);
        }

        $sessionId = $request->session()->get(self::SESSION_KEY);

        if (! $sessionId) {
            $sessionId = (string) Str::uuid();
            $request->session()->put(self::SESSION_KEY, $sessionId);
        }

        return Cart::firstOrCreate([
            'session_id' => $sessionId,
            'user_id' => null,
        ]);
    }

    public function mergeGuestCartIntoUser(Request $request, User $user): void
    {
        $sessionId = $request->session()->get(self::SESSION_KEY);

        if (! $sessionId) {
            return;
        }

        $guestCart = Cart::where('session_id', $sessionId)->whereNull('user_id')->first();

        if (! $guestCart) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $user->id]);

        foreach ($guestCart->items as $guestItem) {
            $existing = $userCart->items()->where('variant_id', $guestItem->variant_id)->first();

            if ($existing) {
                $existing->increment('quantity', $guestItem->quantity);
                $guestItem->delete();
            } else {
                $guestItem->update(['cart_id' => $userCart->id]);
            }
        }

        $guestCart->delete();
        $request->session()->forget(self::SESSION_KEY);
    }

    public function itemCount(Request $request): int
    {
        return $this->currentCart($request)->items()->sum('quantity');
    }

    /**
     * The coupon code stored in session, applied earlier in the cart page —
     * re-validated against $subtotal every time (never trust the session
     * blindly), so it silently stops applying the moment it goes invalid
     * (expired, used up, or the cart dropped below its minimum).
     */
    public function appliedCoupon(Request $request, float $subtotal): ?Coupon
    {
        $code = $request->session()->get(self::COUPON_SESSION_KEY);

        if (! $code) {
            return null;
        }

        $coupon = Coupon::where('code', $code)->first();

        return $coupon && $coupon->isValidFor($subtotal) ? $coupon : null;
    }

    public function applyCoupon(Request $request, string $code): void
    {
        $request->session()->put(self::COUPON_SESSION_KEY, strtoupper(trim($code)));
    }

    public function removeCoupon(Request $request): void
    {
        $request->session()->forget(self::COUPON_SESSION_KEY);
    }
}
