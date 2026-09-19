<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class ProductComparator
{
    /**
     * How to read a "who's ahead" number out of a spec's raw text, and
     * which direction wins. Specs left out (Camera, Chất liệu, Màu sắc…)
     * only ever get match/diff coloring — their values aren't reducible
     * to one comparable number without misrepresenting the product.
     *
     * @var array<string, 'higher'|'lower'>
     */
    private const WINNABLE_SPECS = [
        'Màn hình' => 'higher',
        'Pin' => 'higher',
        'Trọng lượng' => 'lower',
        'Dung lượng RAM' => 'higher',
        'Bộ nhớ trong' => 'higher',
    ];

    /**
     * How to phrase the winning margin for each winnable spec. %s is the
     * formatted difference (unit included).
     *
     * @var array<string, string>
     */
    private const DELTA_PHRASING = [
        'Màn hình' => 'Lớn hơn %s',
        'Pin' => '+%s',
        'Trọng lượng' => 'Nhẹ hơn %s',
        'Dung lượng RAM' => '+%s',
        'Bộ nhớ trong' => '+%s',
    ];

    /**
     * Builds one comparison row for a spec label across all compared
     * products: each product's raw value, whether every product that has
     * the spec agrees on it, and which product (if any) clearly leads.
     *
     * @param  Collection<int, Product>  $products
     * @return array{
     *     label: string,
     *     values: list<string|null>,
     *     isMatch: bool,
     *     winnerIndex: int|null,
     *     delta: string|null,
     * }
     */
    public function compareRow(string $label, Collection $products): array
    {
        $values = $products
            ->map(fn ($product) => $product->specifications[$label] ?? null)
            ->all();

        $present = array_filter($values, fn ($v) => $v !== null);

        $isMatch = count($present) === count($values) && count($values) > 1
            && collect($present)->map($this->normalize(...))->unique()->count() === 1;

        [$winnerIndex, $delta] = $isMatch ? [null, null] : $this->findWinner($label, $values);

        return [
            'label' => $label,
            'values' => $values,
            'isMatch' => $isMatch,
            'winnerIndex' => $winnerIndex,
            'delta' => $delta,
        ];
    }

    /**
     * Drops a trailing parenthetical aside ("8GB (tối ưu cho AI)" → "8GB")
     * and folds case/whitespace, so seeder copy differences don't hide a
     * spec that's genuinely identical.
     */
    private function normalize(string $value): string
    {
        return mb_strtolower(trim(preg_replace('/\s*\([^)]*\)\s*$/u', '', $value)));
    }

    /**
     * @param  list<string|null>  $values
     * @return array{0: int|null, 1: string|null}
     */
    private function findWinner(string $label, array $values): array
    {
        $direction = self::WINNABLE_SPECS[$label] ?? null;

        if ($direction === null) {
            return [null, null];
        }

        $numbers = array_map(
            fn (?string $v) => $v === null ? null : $this->extractComparableNumber($label, $v),
            $values
        );

        $candidates = array_filter($numbers, fn ($n) => $n !== null);

        if (count($candidates) < 2) {
            return [null, null];
        }

        $best = $direction === 'higher' ? max($candidates) : min($candidates);

        // A tie declares no winner rather than crowning every tied product.
        if (count(array_keys($candidates, $best, true)) !== 1) {
            return [null, null];
        }

        $winnerIndex = array_search($best, $numbers, true);
        $rest = array_diff($candidates, [$best]);
        // The closest competitor — the largest of what's left when higher
        // wins, the smallest when lower wins — not just any runner-up.
        $runnerUp = $direction === 'higher' ? max($rest) : min($rest);
        $margin = abs($best - $runnerUp);

        return [$winnerIndex, sprintf(self::DELTA_PHRASING[$label], $this->formatMargin($label, $margin))];
    }

    private function formatMargin(string $label, float $margin): string
    {
        return match ($label) {
            'Màn hình' => rtrim(rtrim(number_format($margin, 1, ',', ''), '0'), ',').'″',
            'Pin' => number_format($margin, 0, ',', '.').' mAh',
            'Trọng lượng' => number_format($margin, 0, ',', '.').'g',
            default => $margin >= 1024
                ? number_format($margin / 1024, 1, ',', '').'TB'
                : number_format($margin, 0, ',', '.').'GB',
        };
    }

    /**
     * Pulls a comparable number out of a spec string, unit-aware because
     * the seeder's number formatting isn't: "4.422 mAh" and "221g" use "."
     * as a thousands separator (Vietnamese convention), but "6.7 inch"
     * uses it as an actual decimal point.
     */
    private function extractComparableNumber(string $label, string $value): ?float
    {
        return match ($label) {
            'Màn hình' => preg_match('/([\d]+(?:[.,]\d+)?)\s*inch/ui', $value, $m)
                ? (float) str_replace(',', '.', $m[1])
                : null,
            'Pin' => preg_match('/([\d.,]+)\s*mAh/ui', $value, $m)
                ? $this->parseGroupedNumber($m[1])
                : null,
            'Trọng lượng' => preg_match('/([\d.,]+)\s*g\b/ui', $value, $m)
                ? $this->parseGroupedNumber($m[1])
                : null,
            default => $this->extractLargestByteSize($value),
        };
    }

    /**
     * Pulls the largest size (GB/TB normalized to GB) out of a spec
     * string — an option list ("256GB / 512GB / 1TB") compares by its
     * biggest tier, same as a single value ("8GB").
     */
    private function extractLargestByteSize(string $value): ?float
    {
        if (! preg_match_all('/([\d.,]+)\s*(TB|GB)\b/ui', $value, $matches, PREG_SET_ORDER)) {
            return null;
        }

        $sizes = array_map(
            fn (array $m) => $this->parseGroupedNumber($m[1]) * (strtoupper($m[2]) === 'TB' ? 1024 : 1),
            $matches
        );

        return max($sizes);
    }

    /**
     * "4.422" here means four thousand four hundred twenty-two, not four
     * point four two two — "." groups thousands, "," is the decimal point.
     */
    private function parseGroupedNumber(string $raw): float
    {
        return (float) str_replace(',', '.', str_replace('.', '', $raw));
    }
}
