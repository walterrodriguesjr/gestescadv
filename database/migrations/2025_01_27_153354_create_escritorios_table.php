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
        Schema::create('escritorios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('nome_escritorio');
            $table->string('cnpj_escritorio')->nullable();
            $table->string('telefone_escritorio')->nullable();
            $table->string('celular_escritorio');
            $table->string('email_escritorio');
            $table->string('cep_escritorio')->nullable();
            $table->string('logradouro_escritorio')->nullable();
            $table->string('numero_escritorio')->nullable();
            $table->string('bairro_escritorio')->nullable();
            $table->string('estado_escritorio')->nullable();
            $table->string('cidade_escritorio')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escritorios');
    }
};
