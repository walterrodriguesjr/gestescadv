<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class CodigoExclusaoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $codigo;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct($codigo)
    {
        $this->codigo = $codigo;
        $this->user = Auth::user(); // Obtém o usuário autenticado
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
                'user' => $this->user // Passa o usuário para a view
            ]);
    }
}
