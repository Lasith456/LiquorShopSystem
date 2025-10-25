<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sell_items', function (Blueprint $table) {
            $table->unsignedBigInteger('size_id')->nullable()->after('product_id');
            $table->decimal('cost_price', 10, 2)->default(0)->after('qty');
        });
    }

    public function down()
    {
        Schema::table('sell_items', function (Blueprint $table) {
            $table->dropColumn(['size_id', 'cost_price']);
        });
    }

};
