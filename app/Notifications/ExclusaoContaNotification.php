<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExclusaoContaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $csvPath;

    public function __construct($csvPath)
    {
        $this->csvPath = $csvPath;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $email = (new MailMessage)
            ->subject('Seus Dados Antes da Exclusão')
            ->greeting("Olá, {$notifiable->name}!")
            ->line("Você solicitou a exclusão da sua conta. Antes disso, estamos enviando seus dados.")
            ->line("Caso deseje armazená-los, baixe o arquivo CSV anexo.")
            ->line("Após a exclusão, esses dados não estarão mais disponíveis.")
            ->line("Se você não fez essa solicitação, entre em contato conosco.");

        // Certifique-se de que o arquivo existe antes de anexar
        if (Storage::exists($this->csvPath)) {
            $email->attach(Storage::path($this->csvPath), [
                'as' => 'meus_dados.csv',
                'mime' => 'text/csv',
            ]);
            Log::info("CSV anexado ao e-mail: " . Storage::path($this->csvPath));
        } else {
            Log::error("Arquivo CSV não encontrado: " . $this->csvPath);
        }

        return $email;
    }
}
