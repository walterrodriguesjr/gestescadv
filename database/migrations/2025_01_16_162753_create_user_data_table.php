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
        Schema::create('user_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relacionamento com a tabela users
            $table->string('cpf')->nullable();
            $table->string('telefone')->nullable();
            $table->string('celular')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();
            $table->string('oab')->nullable(); // Número da OAB (se aplicável)
            $table->string('estado_oab')->nullable(); // Estado da OAB
            $table->date('data_nascimento')->nullable();
            $table->string('foto')->nullable()->after('estado_oab');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_data');
    }
};

