<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    protected $token;

    /**
     * Cria uma nova instância da notificação.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Define os canais de entrega da notificação.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Conteúdo do e-mail enviado ao usuário.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🔐 Redefinição de Senha')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Recebemos uma solicitação para redefinir sua senha.')
            ->line('Se não foi você, ignore este e-mail.')
            ->line('Caso queira prosseguir, clique no botão abaixo:')
            ->action('Redefinir Senha', url('/reset-password/' . $this->token))
            ->line('Se você não solicitou essa alteração, sua senha permanecerá inalterada.')
            ->line('Mantenha sua conta segura!');
    }
}
