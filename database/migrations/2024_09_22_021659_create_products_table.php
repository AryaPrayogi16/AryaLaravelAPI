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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // menambahkan unique constraint
            $table->integer('price')->unsigned(); // harga tidak bisa negatif
            $table->integer('stock')->unsigned(); // stok tidak bisa negatif
            $table->integer('sold')->default(0)->unsigned(); // jumlah terjual tidak bisa negatif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
