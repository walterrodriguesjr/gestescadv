<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_documento', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('escritorio_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index(); // quem criou
            $table->string('titulo');
            $table->timestamps();

            $table->foreign('escritorio_id')->references('id')->on('escritorios')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_documento');
    }
};
