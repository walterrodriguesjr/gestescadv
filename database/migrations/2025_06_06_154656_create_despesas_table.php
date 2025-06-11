<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('despesas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escritorio_id')->constrained('escritorios')->onDelete('cascade');
            $table->foreignId('tipo_despesa_id')->constrained('tipos_despesa')->onDelete('cascade');
            $table->decimal('valor', 12, 2);
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->boolean('situacao')->default(false); // false = não pago, true = pago
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('despesas');
    }
};
