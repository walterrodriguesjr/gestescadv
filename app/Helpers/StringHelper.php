<?php

namespace App\Helpers;

class StringHelper
{
    public static function normalizar($texto)
    {
        // Remove acentos
        $texto = iconv('UTF-8', 'ASCII//TRANSLIT', $texto);
        // Converte tudo para minúsculas
        $texto = strtolower($texto);
        // Remove caracteres especiais (mantém apenas letras, números e espaços)
        $texto = preg_replace('/[^a-z0-9\s]/', '', $texto);
        // Remove espaços extras
        return trim(preg_replace('/\s+/', ' ', $texto));
    }
}
