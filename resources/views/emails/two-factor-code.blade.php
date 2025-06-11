<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Autenticação</title>
</head>
<body>
    <p>Olá, {{ $user->name }}.</p>
    <p>Seu código de autenticação é:</p>
    <h2>{{ $code }}</h2>
    <p>O código expira em 10 minutos.</p>
    <p>Se você não solicitou este código, ignore este e-mail.</p>
</body>
</html>
