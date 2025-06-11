<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('permissoes_usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('nivel_acesso_id')->constrained('niveis_acesso')->onDelete('cascade');
            $table->foreignId('escritorio_id')->nullable()->constrained('escritorios')->onDelete('cascade');
            $table->foreignId('concedente_id')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissoes_usuarios');
    }
};
