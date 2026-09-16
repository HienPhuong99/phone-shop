<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Lowercased, accent-stripped "name + series + category" kept in
            // sync on save (see Product::booted()) so search works for
            // Vietnamese input typed without diacritics. A plain index isn't
            // useful here: search is a leading-wildcard LIKE, which no
            // B-tree index can serve.
            $table->text('search_text')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('search_text');
        });
    }
};
