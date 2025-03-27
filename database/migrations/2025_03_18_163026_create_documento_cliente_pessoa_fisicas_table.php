<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('documento_cliente_pessoa_fisicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_pessoa_fisica_id')
                  ->constrained('cliente_pessoa_fisicas')
                  ->onDelete('cascade');
            $table->string('nome_original');
            $table->string('nome_arquivo');
            $table->string('tipo_documento')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('documento_cliente_pessoa_fisicas');
    }
};

