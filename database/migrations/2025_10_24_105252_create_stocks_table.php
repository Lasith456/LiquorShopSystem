<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('reference_no')->unique();
            $table->date('date')->default(now());
            $table->decimal('total_value', 12, 2)->default(0);
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // who added it
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
