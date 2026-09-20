<?php

namespace App\Models;

use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable([
    'title', 'slug', 'topic', 'focus_keyword', 'excerpt', 'body', 'thumbnail', 'thumbnail_thumb',
    'meta_title', 'meta_description', 'faqs', 'status', 'published_at',
])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /**
     * The four content pillars the editorial plan is built on. The key is
     * what lands in the `topic` column and in the /tin-tuc?chu-de= filter,
     * the value is what the reader sees.
     *
     * @var array<string, string>
     */
    public const TOPICS = [
        'tu-van' => 'Tư vấn mua',
        'so-sanh' => 'So sánh',
        'huong-dan' => 'Hướng dẫn',
        'tin-moi' => 'Tin mới',
    ];

    /**
     * Words a Vietnamese reader gets through in a minute — used only to
     * print "đọc 5 phút" on the card, so a rough figure is fine.
     */
    private const WORDS_READ_PER_MINUTE = 200;

    protected function casts(): array
    {
        return [
            'faqs' => 'array',
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Posts the public may read: switched to published by the admin and
     * past their publish time. A null published_at means "no scheduled
     * time", so publishing takes effect immediately — same convention as
     * Announcement's null bounds.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', 'published')
            ->where(fn (Builder $q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function scopeTopic(Builder $query, ?string $topic): Builder
    {
        return $query->when(
            $topic && array_key_exists($topic, self::TOPICS),
            fn (Builder $q) => $q->where('topic', $topic)
        );
    }

    /**
     * Newest first, with posts that have no publish time falling back to
     * when they were created so they never sink to the bottom.
     */
    public function scopeLatestPublished(Builder $query): Builder
    {
        return $query->orderByRaw('COALESCE(published_at, created_at) DESC');
    }

    public function getTopicLabelAttribute(): string
    {
        return self::TOPICS[$this->topic] ?? 'Tin mới';
    }

    /**
     * Where this post stands right now, for the admin list — a post can be
     * marked published and still be scheduled for later.
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->status !== 'published') {
            return 'Bản nháp';
        }

        return $this->published_at?->isFuture() ? 'Lên lịch' : 'Đang hiện';
    }

    public function getSeoTitleAttribute(): string
    {
        return $this->meta_title ?: $this->title;
    }

    public function getSeoDescriptionAttribute(): string
    {
        return Str::limit(preg_replace('/\s+/', ' ', trim($this->meta_description ?: $this->excerpt)), 160);
    }

    public function getReadingMinutesAttribute(): int
    {
        return max(1, (int) ceil(str_word_count(strip_tags($this->body)) / self::WORDS_READ_PER_MINUTE));
    }

    /**
     * Turn the admin textarea into typed blocks so a shop owner can write
     * an article without knowing any HTML. The markup is deliberately tiny:
     *
     *   ## Tiêu đề phụ   → h2 (also becomes a mục lục entry)
     *   ### Tiêu đề nhỏ  → h3
     *   - Gạch đầu dòng  → bullet list
     *   > Ghi chú        → highlighted note
     *   anything else    → paragraph
     *
     * @return list<array{type: 'heading'|'subheading'|'list'|'note'|'paragraph', text?: string, id?: string, items?: list<string>}>
     */
    public function getBodyBlocksAttribute(): array
    {
        $blocks = [];

        foreach (preg_split('/\R/', (string) $this->body) ?: [] as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            if (str_starts_with($line, '## ')) {
                $text = trim(substr($line, 3));
                $blocks[] = ['type' => 'heading', 'text' => $text, 'id' => Str::slug($text)];

                continue;
            }

            if (str_starts_with($line, '### ')) {
                $blocks[] = ['type' => 'subheading', 'text' => trim(substr($line, 4))];

                continue;
            }

            if (str_starts_with($line, '> ')) {
                $blocks[] = ['type' => 'note', 'text' => trim(substr($line, 2))];

                continue;
            }

            if (str_starts_with($line, '- ')) {
                $item = trim(substr($line, 2));

                // Consecutive "- " lines collapse into one list rather than
                // one single-item list each.
                if (($blocks[array_key_last($blocks)]['type'] ?? null) === 'list') {
                    $blocks[array_key_last($blocks)]['items'][] = $item;

                    continue;
                }

                $blocks[] = ['type' => 'list', 'items' => [$item]];

                continue;
            }

            $blocks[] = ['type' => 'paragraph', 'text' => $line];
        }

        return $blocks;
    }

    /**
     * Render the inline markup a shop owner can type inside any line:
     * **đậm** and [chữ hiển thị](/duong-dan). The text is escaped first,
     * so the only HTML that reaches the page is the two tags below, and a
     * link target is only accepted when it is a site path or an http(s)
     * URL — no javascript: href can come out of the admin textarea.
     */
    public static function inlineHtml(string $text): string
    {
        $escaped = e($text);

        $linked = preg_replace(
            '~\[([^\]]+)\]\((/[^)\s]*|https?://[^)\s]+)\)~',
            '<a href="$2">$1</a>',
            $escaped
        );

        return preg_replace('~\*\*([^*]+)\*\*~', '<strong>$1</strong>', $linked);
    }

    /**
     * The "## " headings, for the mục lục box at the top of an article —
     * the jump links Google and AI assistants use to cite a section.
     *
     * @return list<array{id: string, text: string}>
     */
    public function getTableOfContentsAttribute(): array
    {
        return array_values(array_map(
            fn (array $block) => ['id' => $block['id'], 'text' => $block['text']],
            array_filter($this->body_blocks, fn (array $block) => $block['type'] === 'heading')
        ));
    }

    /**
     * Only FAQ rows with both halves filled in — a half-empty row would
     * render an empty question and break the FAQPage schema.
     *
     * @return list<array{question: string, answer: string}>
     */
    public function getAnsweredFaqsAttribute(): array
    {
        return array_values(array_filter(
            $this->faqs ?? [],
            fn (array $faq) => filled($faq['question'] ?? null) && filled($faq['answer'] ?? null)
        ));
    }
}
