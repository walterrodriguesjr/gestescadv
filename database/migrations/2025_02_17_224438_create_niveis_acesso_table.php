<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('niveis_acesso', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->unique();
            $table->json('permissoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('niveis_acesso');
    }
};
