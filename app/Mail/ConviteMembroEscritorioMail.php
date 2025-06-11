<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConviteMembroEscritorioMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    /**
     * Cria uma nova instância do e-mail.
     */
    public function __construct($user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Constrói o e-mail.
     */
    public function build()
    {
        return $this->subject('Convite para acessar o sistema')
                    ->markdown('emails.convite_membro')
                    ->with([
                        'user' => $this->user,
                        'token' => $this->token,
                        'resetLink' => url("/reset-password/{$this->token}")
                    ]);
    }
}

