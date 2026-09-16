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
        Schema::table('addresses', function (Blueprint $table) {
            // Province/city name (from App\Support\Provinces — the 63
            // Vietnamese provinces/cities). Used to price shipping by
            // region instead of one flat fee everywhere. Nullable so
            // existing rows aren't broken; the checkout form requires it
            // for new addresses.
            $table->string('province')->nullable()->after('address_line');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('province');
        });
    }
};
