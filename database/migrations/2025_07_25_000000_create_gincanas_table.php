<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gincanas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->integer('duracao');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('contexto', 255);
            $table->enum('privacidade', ['publica', 'privada']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gincanas');
    }
};
