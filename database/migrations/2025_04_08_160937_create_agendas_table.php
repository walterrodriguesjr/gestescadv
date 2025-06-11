<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('escritorio_id')->constrained()->onDelete('cascade');
            $table->foreignId('servico_id')->constrained()->onDelete('cascade');
            $table->foreignId('motivo_agenda_id')->constrained('motivos_agenda')->onDelete('restrict');

            $table->enum('tipo_cliente', ['pessoa_fisica', 'pessoa_juridica']);
            $table->unsignedBigInteger('cliente_id')->nullable(); // Cliente pode ser opcional

            $table->datetime('data_hora_inicio');
            $table->datetime('data_hora_fim');

            $table->text('observacoes')->nullable();
            $table->decimal('honorario', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('agendas');
    }
};

