<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bem-vindo ao Escritório</title>
</head>

<body>
    <h2>Olá, {{ $user->name }}!</h2>
    <p>Você foi cadastrado no nosso sistema. Para acessar sua conta, primeiro você precisa definir uma senha.</p>
    <p><strong>Para redefinir sua senha, clique no link abaixo:</strong></p>
    <p>
        <a href="{{ $link }}" style="color: blue; text-decoration: none;">
            Redefinir minha senha
        </a>
    </p>
    <p>Se você não solicitou esse acesso, por favor ignore este e-mail.</p>
</body>

</html>
