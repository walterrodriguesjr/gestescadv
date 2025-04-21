<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('motivos_agenda', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->timestamps();
        });

        // Semente inicial dos motivos mais comuns
        DB::table('motivos_agenda')->insert([
            ['nome' => 'Consulta'],
            ['nome' => 'Estudo de Caso'],
            ['nome' => 'Audiência'],
            ['nome' => 'Diligência'],
            ['nome' => 'Reunião com Cliente'],
            ['nome' => 'Visita Técnica'],
        ]);
    }

    public function down(): void {
        Schema::dropIfExists('motivos_agenda');
    }
};

