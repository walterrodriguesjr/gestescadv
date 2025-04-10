<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('servicos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('escritorio_id')->constrained()->onDelete('cascade');
            $table->foreignId('tipo_servico_id')->constrained()->onDelete('cascade');
            $table->enum('tipo_cliente', ['pessoa_fisica', 'pessoa_juridica']);
            $table->unsignedBigInteger('cliente_id'); // Referência manual
            $table->date('data_inicio');
            $table->text('observacoes')->nullable();
            $table->json('anexos')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicos');
    }
};

