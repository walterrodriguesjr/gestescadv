<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cliente_pessoa_fisicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escritorio_id')->constrained()->onDelete('cascade');
            $table->text('nome');
            $table->text('cpf'); 
            $table->text('telefone')->nullable();
            $table->text('celular');
            $table->text('email')->nullable();
            $table->text('cep')->nullable();
            $table->text('logradouro')->nullable();
            $table->text('numero')->nullable();
            $table->text('bairro')->nullable();
            $table->text('cidade')->nullable();
            $table->text('estado')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_pessoa_fisicas');
    }
};

