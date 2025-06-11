<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;
use Illuminate\Support\Str;

class CodigoExclusaoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $codigo;
    public $user;
    public $csvPath;

    /**
     * Create a new message instance.
     */
    public function __construct($codigo)
    {
        $this->codigo = $codigo;
        $this->user = Auth::user();

        // Gerar CSV com os dados do usuário
        $this->csvPath = $this->generateCsv();
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Código de Confirmação para Exclusão de Conta')
            ->view('emails.codigo_exclusao')
            ->with([
                'codigo' => $this->codigo,
                'user' => $this->user
            ])
            ->attach($this->csvPath, [
                'as' => 'meus_dados.csv',
                'mime' => 'text/csv',
            ]);
    }

    /**
     * Gera um arquivo CSV com os dados do usuário.
     */
    private function generateCsv()
    {
        $csvFileName = 'user_data_' . Str::random(10) . '.csv';
        $csvPath = storage_path('app/' . $csvFileName);

        // Criar o CSV
        $csv = Writer::createFromPath($csvPath, 'w+');
        $csv->insertOne(['Campo', 'Valor']); // Cabeçalho

        // Adicionar os dados do usuário
        $csv->insertOne(['Nome', $this->user->name]);
        $csv->insertOne(['E-mail', $this->user->email]);
        $csv->insertOne(['Criado em', $this->user->created_at]);
        $csv->insertOne(['Última atualização', $this->user->updated_at]);

        return $csvPath;
    }
}
