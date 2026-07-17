# SGM-Sistema_Gestao_Manutencao
Sistema para gestão de manutenção que centraliza o controle de ordens de serviço, ativos, equipes e manutenções preventivas e corretivas. Permite acompanhar indicadores, histórico de intervenções e otimizar processos, aumentando a eficiência operacional e reduzindo custos.

## Sistema de Login Completo com BOOTSTRAP, FONT AWESOME, PHP, AJAX, JQuery, SweetAlert2 e MySQL

Sistema de login completo com validação de usuário, recuperação de senha e envio de e-mail. Utilizando BOOTSTRAP, JQUERY, SWEETALERT2, AJAX e PHPMAILER.

## Funcionalidades

1. **Login de Usuários**
   - Formulário com campos para e-mail e senha
   - Validação de campos
   - Feedback visual com SweetAlert2

2. **Cadastro de Usuários**
   - Formulário com campos para nome, CPF, telefone, e-mail e senha
   - Validação e máscaras para CPF, telefone e e-mail
   - Confirmação de senha
   - Gera token de validação da conta de usuário com validade de 24 horas
   - Verifica se o token de validação já existe e está expirado. Caso esteja, faz a renovação do token com validade de 1 hora e retorna feedback visual com SweetAlert2
   - Envia e-mail contendo token, link e botão para validação da conta do usuário usando PHPMailer
   - Feedback visual com SweetAlert2

3. **Validação de Usuários**
   - Validação da conta do usuário através do token

4. **Recuperação de Senha**
   - Formulário com campos para CPF e e-mail
   - Validação e máscaras para CPF e e-mail
   - Verificação se a conta do usuário foi validada
   - Gera token de recuperação da senha do usuário com validade de 1 hora
   - Verifica se o token de recuperação já existe e está expirado. Caso esteja, faz a renovação do token com validade de 1 hora e retorna feedback visual com SweetAlert2
   - Envia e-mail contendo token, link e botão para redefinição de senha usando PHPMailer
   - Feedback visual com SweetAlert2

5. **Redefinição de Senha**
   - Formulário com campo para inserir nova senha
   - Validação de comprimento da senha
   - Confirmação de senha
   - Feedback visual com SweetAlert2

6. **Painel do Usuário**
   - Exibição das informações do usuário logado
   - Opção para logout

## Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Composer (para instalação do PHPMailer)
- Servidor web (WAMPSERVER)

## Instalação

1. **Clone ou baixe este repositório para o diretório do seu servidor web**

2. **Instale as dependências usando o Composer**
   ```
   composer install
   ```

3. **Crie o banco de dados**
   - Importe o arquivo `config/database.sql` para o seu servidor MySQL
   - Ou execute os comandos SQL contidos no arquivo

4. **Configure a conexão com o banco de dados**
   - Abra o arquivo `config/database.php`
   - Altere as constantes `DB_HOST`, `DB_USER`, `DB_PASS` e `DB_NAME` conforme necessário

5. **Configure o envio de e-mails**
   - Abra o arquivo `includes/enviar_email.php`
   - Altere as configurações do servidor SMTP, usuário e senha
   - Substitua os endereços de e-mail do remetente

## Uso

1. **Acesse o sistema pelo navegador**
   - Exemplo: `http://localhost/`

2. **Cadastre um novo usuário**
   - Clique em "Cadastre-se" na página de login
   - Preencha o formulário com seus dados
   - Verifique seu e-mail para validar a conta

3. **Faça login no sistema**
   - Após validar sua conta, volte para a página de login
   - Insira seu e-mail e senha
   - Acesse o painel do usuário

## Segurança

- Senhas armazenadas com hash seguro (password_hash)
- Proteção contra SQL Injection usando prepared statements
- Validação de dados no cliente e no servidor
- Sessões seguras

