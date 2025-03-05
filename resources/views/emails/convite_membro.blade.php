@component('mail::message')
# Olá, {{ $user->name }} 👋

Você foi adicionado como membro do escritório no nosso sistema.

Para acessar sua conta, é necessário **redefinir sua senha**. Clique no botão abaixo para definir uma nova senha:

@component('mail::button', ['url' => $resetLink])
Redefinir Senha
@endcomponent

Se você não esperava este convite, ignore este e-mail.

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
