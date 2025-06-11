<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

---

# Gestão Jurídica - Sistema Completo em Laravel 11

Este é um sistema completo de gestão jurídica desenvolvido em Laravel 11, que inclui funcionalidades essenciais de autenticação, gerenciamento de clientes, serviços jurídicos, anexos e agendamentos.

🚀 Funcionalidades Implementadas

Autenticação de usuários (Login e Logout)

Recuperação de senha por e-mail

Autenticação em dois fatores (2FA) via e-mail

Cadastro rápido e avançado de clientes (Pessoa Física e Jurídica)

Cadastro e gerenciamento de tipos de serviços jurídicos

Upload e gerenciamento de arquivos anexados aos serviços

Agendamento opcional de consultas e atendimentos jurídicos

Validações robustas no frontend com SweetAlert e no backend com Laravel Validator

Configuração pronta para uso com Docker e Laravel Sail

---

🛠️ Tecnologias Utilizadas

Laravel 11

PHP 8.2

JavaScript (jQuery)

Bootstrap 5

Choices.js (Select avançado)

SweetAlert2

Docker e Docker Compose

---

## Sobre o Projeto
O projeto é configurado para funcionar imediatamente com Docker, facilitando o setup do ambiente de desenvolvimento.

---

## Pré-requisitos

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

---

## Como Usar

### Passo 1: Clone o Repositório

```bash
git clone https://github.com/seu-usuario/gestao-juridica.git
cd gestao-juridica

Passo 2: Configuração Inicial
    1.Copie o arquivo .env.example para .env:

        cp .env.example .env

    2.Gere a chave da aplicação:

        docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php82-composer:latest php artisan key:generate

Passo 3: Subir os Containers
    1.Construa e inicie os containers:

        docker-compose up -d --build

Passo 4: Instalar Dependências
    1.Dependências PHP:

        docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html laravelsail/php82-composer:latest composer install

    2.Dependências JavaScript:

        docker exec -it laravel_app bash
        npm install && npm run dev

Passo 5: Executar Migrations e seeders (popula a tabela de niveis de acesso, popula user e user_data com o user admin, popula permissoes com a permissao Administrador)
        docker exec -it laravel_app bash
        php artisan db:reset

Passo 6: Acessar o Projeto
    1.Acesse no navegador: 

        http://localhost

Funcionalidades
Login e Logout: Implementado com autenticação básica.
Recuperação de Senha: Envio de link de redefinição por e-mail.
Autenticação em Dois Fatores (2FA): Geração e verificação de códigos enviados por e-mail.
Configuração com Docker e Laravel Sail.
Contribuição
Contribuições são bem-vindas! Abra uma Issue ou envie um Pull Request para sugerir melhorias.

Licença
Este projeto está licenciado sob a MIT License.   






