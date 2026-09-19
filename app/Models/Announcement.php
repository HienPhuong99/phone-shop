<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'message', 'cta_label', 'cta_url', 'starts_at', 'ends_at', 'active'])]
class Announcement extends Model
{
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'active' => 'boolean',
        ];
    }

    /**
     * Announcements switched on by the admin and inside their scheduled
     * window right now — null starts_at/ends_at means "no bound" on that
     * side, so a bare "active" one shows immediately with no expiry.
     */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query
            ->where('active', true)
            ->where(fn (Builder $q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn (Builder $q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    /**
     * Where this announcement stands right now, for the admin list — a
     * toggle can be "on" and still not be showing yet, or not anymore.
     */
    public function getScheduleLabelAttribute(): string
    {
        if (! $this->active) {
            return 'Đã tắt';
        }

        if ($this->starts_at?->isFuture()) {
            return 'Lên lịch';
        }

        if ($this->ends_at?->isPast()) {
            return 'Đã hết hạn';
        }

        return 'Đang hiện';
    }
}
