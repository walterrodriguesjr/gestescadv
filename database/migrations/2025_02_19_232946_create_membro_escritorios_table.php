<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('membro_escritorios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relacionado ao usuário
            $table->foreignId('escritorio_id')->constrained('escritorios')->onDelete('cascade'); // Relacionado ao escritório
            $table->foreignId('gestor_id')->constrained('users')->onDelete('cascade'); // Usuário que cadastrou
            $table->enum('status', ['pendente', 'ativo', 'inativo'])->default('pendente')->after('gestor_id');
            $table->softDeletes(); // Soft delete
            $table->timestamps(); // Criado e atualizado em
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membro_escritorios');
    }
};

