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
        Schema::create('tipo_servicos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('escritorio_id');
            $table->string('nome_servico', 255);
            $table->timestamps();
            $table->foreign('escritorio_id')->references('id')->on('escritorios');
            $table->softDeletes();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_servicos');
    }
};
