<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['code', 'type', 'value', 'min_order_amount', 'max_uses', 'used_count', 'expires_at', 'active'])]
class Coupon extends Model
{
    public const TYPE_PERCENT = 'percent';

    public const TYPE_FIXED = 'fixed';

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'expires_at' => 'datetime',
            'active' => 'boolean',
        ];
    }

    /**
     * Whether this coupon can currently be applied to an order of
     * $orderAmount (the cart subtotal, before shipping) — active, not
     * expired, not past its use limit, and the order meets any minimum.
     */
    public function isValidFor(float $orderAmount): bool
    {
        if (! $this->active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        if ($this->min_order_amount !== null && $orderAmount < (float) $this->min_order_amount) {
            return false;
        }

        return true;
    }

    /**
     * The discount amount in VND for an order of $orderAmount — capped at
     * the order amount itself so a fixed-value coupon never goes negative.
     */
    public function discountFor(float $orderAmount): float
    {
        $discount = $this->type === self::TYPE_PERCENT
            ? $orderAmount * ((float) $this->value / 100)
            : (float) $this->value;

        return min($discount, $orderAmount);
    }
}
