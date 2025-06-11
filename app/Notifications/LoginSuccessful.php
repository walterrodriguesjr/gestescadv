<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;

class LoginSuccessful extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Novo login detectado em sua conta')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Detectamos um novo login na sua conta.')
            ->line('📍 **Localização Aproximada:** ' . Request::ip())
            ->line('🖥 **Dispositivo:** ' . Request::header('User-Agent'))
            ->line('📅 **Data e Hora:** ' . Carbon::now()->format('d/m/Y H:i:s'))
            ->line('Se foi você, ignore este e-mail. Caso contrário, altere sua senha imediatamente.')
            ->action('Alterar Senha', url('/perfil/alterar-senha'))
            ->line('Mantenha sua conta segura ativando a autenticação de dois fatores (2FA).');
    }
}
