<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class LmStudioService
{
    protected string $endpoint;

    public function __construct()
    {
        $this->endpoint = env('LMSTUDIO_API_URL', 'http://localhost:1234/v1/chat/completions');
    }


    public function gerarSugestaoDocumento(string $prompt): string
    {
        $userId = Auth::id() ?? 'anon'; // Fallback se não estiver logado (caso use em algum contexto público)
        $cacheKey = 'ia_response_user_' . $userId . '_' . md5($prompt);

        return cache()->remember($cacheKey, now()->addMinutes(10), function () use ($prompt) {
            $response = Http::timeout(60)->post($this->endpoint, [
                'model' => 'nous-hermes-2-mistral-7b-dpo',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'temperature' => 0.4,
                'top_p' => 0.9,
                'stream' => false,
                'max_tokens' => 1024
            ]);

            if ($response->failed()) {
                throw new \Exception('Erro ao consultar LM Studio: ' . $response->body());
            }

            return $response->json()['choices'][0]['message']['content'] ?? 'Sem resposta da IA.';
        });
    }
}
