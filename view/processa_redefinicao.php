<?php
include 'conexao.php';

$token = $_POST['token'];
$novaSenha = $_POST['novaSenha'];
$confirmarSenha = $_POST['confirmarSenha'];

if ($novaSenha !== $confirmarSenha) {
    die("As senhas não coincidem.");
}

$stmt = $conn->prepare("SELECT email FROM tokens_redefinicao WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Token inválido.");
}

$email = $result->fetch_assoc()['email'];
$senhaCriptografada = password_hash($novaSenha, PASSWORD_DEFAULT);

// Atualiza a senha
$update = $conn->prepare("UPDATE tb_Usuario SET senha = ? WHERE email = ?");
$update->bind_param("ss", $senhaCriptografada, $email);
$update->execute();

// Remove o token
$delete = $conn->prepare("DELETE FROM tokens_redefinicao WHERE token = ?");
$delete->bind_param("s", $token);
$delete->execute();

echo "<script>alert('Senha redefinida com sucesso.'); window.location.href = 'login.html';</script>";
?>
