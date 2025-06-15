<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    if (!preg_match('/@fatec\.sp\.gov\.br$/', $email)) {
        echo "Apenas e-mails fatec.sp.gov.br são permitidos.";
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM tb_Usuario WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(16));
        $expira_em = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $conn->prepare("INSERT INTO tokens_redefinicao (email, token, expira_em) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expira_em]);
        //Acessar Local
        $link = "http://localhost/pi1semestre1/view/redefinir_senha.php?token=$token";
        //$link = "https://http://horusdsm.lovestoblog.com/view/redefinir_senha.php?token=$token";

        echo "Link de redefinição: <a href='$link'>$link</a>";
    } else {
        echo "E-mail não encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <title>Esqueci Minha Senha</title>
  <link rel="stylesheet" href="../estilos/login.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
  <div class="top-header">
    <img src="../imagens/logo-fatec_itapira.png" alt="Imagem 2" />
    <img src="../imagens/logo-cps.png" alt="Imagem 3" />
  </div>
  <div class="top-divider"></div>
  <div class="card p-4 shadow" style="max-width: 400px;">
    <h4 class="mb-3">Esqueci Minha Senha</h4>
    <form method="POST">
      <div class="mb-3">
        <label for="email" class="form-label">E-mail @fatec.sp.gov.br</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>
      <button type="submit" class="btn w-100" style="background-color: #e60000; color: white;">Enviar link</button>
    </form>
  </div>
  <div class="bottom-left-logo">
    <img src="../imagens/print.png" alt="Logo Governo de SP">
  </div>
</body>
</html>
