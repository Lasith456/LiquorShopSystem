<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Foreign keys for category & size
            $table->unsignedBigInteger('category_id')->after('id');
            $table->unsignedBigInteger('size_id')->after('category_id');

            // New fields
            $table->integer('qty')->default(0)->after('detail');
            $table->decimal('selling_price', 10, 2)->after('qty');

            // Foreign key constraints
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['size_id']);
            $table->dropColumn(['category_id', 'size_id', 'qty', 'selling_price']);
        });
    }
};
