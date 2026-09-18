<?php

namespace App\Console\Commands;

use App\Models\Product;
use Database\Seeders\ProductSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use ReflectionMethod;

class PruneDemoProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:prune-demo {--force : Skip the confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete leftover Product::factory() demo rows (random 4-digit slugs) that predate ProductSeeder, one-off cleanup for envs seeded before the real catalog existed';

    public function handle(): int
    {
        $realSlugs = $this->realCatalogSlugs();

        $stale = Product::query()->whereNotIn('slug', $realSlugs)->get(['id', 'name', 'slug']);

        if ($stale->isEmpty()) {
            $this->info('Khong co san pham demo nao can xoa.');

            return self::SUCCESS;
        }

        $this->line("Se xoa {$stale->count()} san pham khong nam trong catalog that:");
        foreach ($stale as $product) {
            $this->line("  - {$product->name} ({$product->slug})");
        }

        if (! $this->option('force') && ! $this->confirm('Xac nhan xoa?')) {
            $this->info('Da huy.');

            return self::SUCCESS;
        }

        Product::query()->whereIn('id', $stale->pluck('id'))->delete();

        $this->info("Da xoa {$stale->count()} san pham demo.");

        return self::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function realCatalogSlugs(): array
    {
        $seeder = new ProductSeeder;
        $method = new ReflectionMethod($seeder, 'models');
        $method->setAccessible(true);

        return collect($method->invoke($seeder))
            ->map(fn (array $model) => Str::slug($model['name']))
            ->all();
    }
}
