<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('andamento_servico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servico_id')->constrained('servicos')->onDelete('cascade');
            $table->string('etapa'); // Ex: Consulta, Parecer, etc.
            $table->foreignId('agenda_id')->nullable()->constrained('agendas')->onDelete('set null');
            $table->text('descricao')->nullable(); // Detalhes opcionais
            $table->text('observacoes')->nullable();
            $table->decimal('honorario', 10, 2)->default(0.00)->nullable();
            $table->dateTime('data_hora');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('andamento_servico');
    }
};
