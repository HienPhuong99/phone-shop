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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('topic')->index(); // tu-van | so-sanh | huong-dan | tin-moi
            $table->string('focus_keyword')->nullable(); // từ khoá chính bài này nhắm tới
            $table->string('excerpt', 300); // tóm tắt ở thẻ bài viết, cũng là meta description mặc định
            $table->longText('body');
            $table->string('thumbnail')->nullable();
            $table->string('thumbnail_thumb')->nullable();
            $table->string('meta_title')->nullable(); // để trống = dùng title
            $table->string('meta_description', 300)->nullable(); // để trống = dùng excerpt
            $table->json('faqs')->nullable(); // [{question, answer}] — nguồn cho FAQPage schema
            $table->string('status')->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
