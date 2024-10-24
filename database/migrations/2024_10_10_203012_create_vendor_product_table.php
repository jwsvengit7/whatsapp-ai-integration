<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vendor_product')) { // Check if the table already exists
            Schema::create('vendor_product', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vendor_id')
                    ->constrained('users')
                    ->onDelete('cascade');
                $table->foreignId('product_id')
                    ->constrained()
                    ->onDelete('cascade');
                $table->boolean('is_vendor')->default(true);
                $table->timestamp('date')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_product');
    }
};

