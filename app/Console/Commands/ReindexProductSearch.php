<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;

class ReindexProductSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:reindex-search';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild search_text for every product (run once after deploying search, for rows saved before it existed)';

    public function handle(): int
    {
        $count = 0;

        Product::query()->chunkById(100, function ($products) use (&$count) {
            foreach ($products as $product) {
                // Re-saving triggers Product::booted()'s saving hook, which
                // rebuilds search_text — no need to duplicate that logic here.
                $product->save();
                $count++;
            }
        });

        $this->info("Da dung lai search_text cho {$count} san pham.");

        return self::SUCCESS;
    }
}
