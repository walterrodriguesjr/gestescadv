<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHonorariosTable extends Migration
{
    public function up()
    {
        Schema::create('honorarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('escritorio_id')->constrained('escritorios')->onDelete('cascade');
            $table->foreignId('servico_id')->constrained('servicos')->onDelete('cascade');
            $table->foreignId('cliente_id');
            $table->decimal('valor', 10, 2);
            $table->text('observacoes')->nullable();
            $table->date('data_recebimento')->nullable()->after('valor');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('honorarios');
    }
}
