<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('product_size', function (Blueprint $table) {
            if (!Schema::hasColumn('product_size', 'qty')) {
                $table->integer('qty')->default(0);
            }
            if (!Schema::hasColumn('product_size', 'selling_price')) {
                $table->decimal('selling_price', 10, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_size', function (Blueprint $table) {
            $table->dropColumn(['qty', 'selling_price']);
        });
    }
};
