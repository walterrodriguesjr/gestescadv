<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de Confirmação</title>
</head>
<body>
    <h2>Código de Confirmação</h2>
    <p>Olá, {{ $user->name }}!</p>
    <p>Seu código de verificação para exclusão de conta é: <strong>{{ $codigo }}</strong></p>
    <p>Este código expira em 10 minutos.</p>
    <br>
    <p>Em anexo, você encontrará um arquivo CSV contendo seus dados armazenados no sistema.</p>
    <p>Se não solicitou essa exclusão, ignore este e-mail.</p>
</body>
</html>
