<?php
session_start();
define('BASE_URL', 'https://horusdsm.lovestoblog.com');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="estilos/login.css">
  <link rel="icon" type="image/png" href="imagens/logo-horus.png">
  <title>Login Fatec</title>
</head>
<body>
  <!-- Cabeçalho superior -->
  <div class="top-header">
    <img src="imagens/logo-fatec_itapira.png" alt="Imagem 2" />
    <img src="imagens/logo-cps.png" alt="Imagem 3" />
  </div>
  <div class="top-divider"></div>

  <!-- Conteúdo principal -->
  <div class="main-content">
    <div class="login-container">
      <h1>Login Fatec</h1>
      <?php
      if (isset($_SESSION['login_error'])) {
          echo '<div class="error-message">' . $_SESSION['login_error'] . '</div>';
          unset($_SESSION['login_error']);
      }
      ?>
      <form method="post" action="controller/login.php">
        <div class="input-group">
          <label for="usuario">Usuário</label>
          <input type="text" id="usuario" name="usuario" required />
        </div>
        <div class="input-group">
          <label for="senha">Senha</label>
          <input type="password" id="senha" name="senha" required autocomplete="current-password" />
        </div>
        
        <button id="botaoentrar" type="submit">Entrar</button>
      </form>

      <!-- Rodapé -->
      <div class="footer">
        <p>Esqueceu sua senha? <a href="view/esqueciminhasenha2.php">Clique aqui</a></p>
        <p>Fatec Itapira - DSM 2025</p>
      </div>
    </div>
  </div>

  <!-- Logotipo no canto inferior esquerdo -->
  <div class="bottom-left-logo">
    <img src="imagens/print.png" alt="Logo Governo de SP">
  </div>
</body>
</html> 