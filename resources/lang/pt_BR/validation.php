<?php

return [
    'required' => 'O campo :attribute é obrigatório.',
    'min' => [
        'string' => 'O campo :attribute deve ter pelo menos :min caracteres.',
    ],
    'regex' => [
        'lowercase' => 'O campo :attribute deve conter pelo menos uma letra minúscula.',
        'uppercase' => 'O campo :attribute deve conter pelo menos uma letra maiúscula.',
        'numeric' => 'O campo :attribute deve conter pelo menos um número.',
        'special' => 'O campo :attribute deve conter pelo menos um caractere especial (@, $, !, %, *, #, ?, &).',
    ],
    'confirmed' => 'A confirmação do campo :attribute não coincide.',
    'email' => 'O campo :attribute deve ser um endereço de e-mail válido.',
];
