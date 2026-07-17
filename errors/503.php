<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <!-- Meta tags Obrigatórias -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!--<meta http-equiv="refresh" content="5">--><!--Recarrega a página automaticamente de 5 em 5 segundos-->
    <link rel="shortcut icon" href="/sgm/errors/img/favicon.ico" />
    <link rel="stylesheet" href="/sgm/errors/css/errors.css">

    <title> ERRO 503</title>
  </head>
  <body>
    <div class="container">
      <div class="centralizar">
        <h1 class="errorConexao"><img class="icone" src="/sgm/errors/img/warning-icon.svg" alt="Ícone de alerta"> Serviço não disponível !</h1>
        <img style="width: 20rem;" src="/sgm/errors/img/erro503.svg">
      </div>
    </div>

    
    <!--Função redireciona para página anterior automáticamente após 20 segundos -->
    <script type="text/javascript">
    function redireciona() {
      setTimeout(() => {
        location.href = '/sgm';
      }, 20000);
    }
    window.addEventListener('load', redireciona);
    </script>
    
  </body>
</html>