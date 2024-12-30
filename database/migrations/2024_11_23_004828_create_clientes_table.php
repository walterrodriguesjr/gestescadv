<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('cliente_nome_completo');
            $table->string('cliente_cpf');
            $table->string('cliente_email');
            $table->string('cliente_celular');
            $table->string('cliente_telefone')->nullable();
            $table->string('cliente_cep')->nullable();
            $table->string('cliente_rua')->nullable();
            $table->string('cliente_numero')->nullable();
            $table->string('cliente_bairro')->nullable();
            $table->string('cliente_estado')->nullable();
            $table->string('cliente_cidade')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
