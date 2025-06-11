<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Request;

class LoginFailedTooManyTimes extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Define os canais de entrega da notificação.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Conteúdo do e-mail enviado ao usuário.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('⚠️ Múltiplas tentativas de login detectadas')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Detectamos **5 tentativas falhas** de login na sua conta em um curto período de tempo.')
            ->line('🔍 **Detalhes da tentativa:**')
            ->line('📍 **Endereço IP:** ' . Request::ip())
            ->line('🖥 **Dispositivo:** ' . Request::header('User-Agent'))
            ->line('📅 **Data e Hora:** ' . Carbon::now()->format('d/m/Y H:i:s'))
         
            ->line('Caso **não tenha sido você**, recomendamos que altere sua senha imediatamente e verifique suas configurações de segurança.')
            ->line('Para uma proteção extra, sugerimos ativar a **Autenticação de Dois Fatores (2FA)** em sua conta.')
            ->line('Se precisar de ajuda, nossa equipe de suporte está disponível para você.')
            ->line('Mantenha sua conta segura! 🔐');
    }

    /**
     * Representação da notificação como array.
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
