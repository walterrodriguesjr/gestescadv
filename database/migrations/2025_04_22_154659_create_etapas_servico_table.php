<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('etapas_servico', function (Blueprint $table) {
        $table->id();
        $table->string('nome');
        $table->string('icone_cor')->nullable(); // Ex: bg-primary, bg-success
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etapas_servico');
    }
};
