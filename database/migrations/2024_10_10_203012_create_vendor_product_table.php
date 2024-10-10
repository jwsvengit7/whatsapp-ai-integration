<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorProductTable extends Migration
{
    public function up():void
    {
        Schema::create('vendor_product', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->foreignId('vendor_id')
                ->constrained('users') // Foreign key referencing users table
                ->onDelete('cascade'); // Delete vendor products if the user is deleted
            $table->foreignId('product_id')
                ->constrained()
                ->onDelete('cascade');
            $table->boolean('is_vendor')->default(true);
            $table->timestamp('date')->nullable();
            $table->timestamps();
        });
    }

    public function down():void
    {
        Schema::dropIfExists('vendor_product');
    }
}
