<?php
require_once '../config/database.php';

$token = $_GET['token'] ?? '';

$stmt = $conn->prepare("SELECT * FROM tokens_redefinicao WHERE token = ? AND expira_em > NOW()");
$stmt->execute([$token]);
$tokenData = $stmt->fetch();

if (!$tokenData) {
    die("Token inválido ou expirado.");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <title>Redefinir Senha</title>
  <link rel="stylesheet" href="../estilos/login.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
  <div class="top-header">
    <img src="../imagens/logo-fatec_itapira.png" alt="Imagem 2" />
    <img src="../imagens/logo-cps.png" alt="Imagem 3" />
  </div>
  <div class="card p-4 shadow" style="max-width: 400px;">
    <h4 class="mb-3">Redefinir Senha</h4>
    <form method="POST" action="processa_redefinicao.php">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
      <div class="mb-3">
        <label for="novaSenha" class="form-label">Nova Senha</label>
        <input type="password" class="form-control" id="novaSenha" name="novaSenha" required>
      </div>
      <div class="mb-3">
        <label for="confirmarSenha" class="form-label">Confirmar Senha</label>
        <input type="password" class="form-control" id="confirmarSenha" name="confirmarSenha" required>
      </div>
      <button type="submit" class="btn w-100" style="background-color: #e60000; color: white;">Nova Senha</button>
    </form>
  </div>
  <div class="bottom-left-logo">
    <img src="../imagens/print.png" alt="Logo Governo de SP">
  </div>
</body>
</html>
