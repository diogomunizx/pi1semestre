<?php
ob_clean(); // limpa qualquer saída anterior

session_start();
header('Content-Type: application/json');

// Verifica se o usuário está logado
if (!isset($_SESSION['id_Docente'])) {
  echo json_encode(['sucesso' => false, 'mensagem' => 'Usuário não autenticado.']);
  exit;
}

require_once '../config/database.php';

$id_Docente = $_SESSION['id_Docente'];

// Lê e decodifica o JSON enviado pelo JavaScript
$inputJSON = file_get_contents('php://input');
$dados = json_decode($inputJSON, true);

if (!isset($dados['horarios']) || !is_array($dados['horarios'])) {
  echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos.']);
  exit;
}

try {
  $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Inicia a transação
  $pdo->beginTransaction();

  // Deleta os registros antigos do docente
  $stmtDelete = $pdo->prepare("DELETE FROM tb_HorasAulas WHERE id_Docente = ?");
  $stmtDelete->execute([$id_Docente]);

  // Prepara o INSERT
  $stmtInsert = $pdo->prepare("
    INSERT INTO tb_HorasAulas (id_Docente, diaSemana, horarioInicio, horarioFim, instituicao)
    VALUES (?, ?, ?, ?, ?)
  ");

  foreach ($dados['horarios'] as $linha) {
    $stmtInsert->execute([
      $id_Docente,
      $linha['diaSemana'],
      $linha['horarioInicio'],
      $linha['horarioFim'],
      $linha['instituicao']
    ]);
  }

  $pdo->commit();
  echo json_encode(['sucesso' => true]);

} catch (Exception $e) {
  if ($pdo->inTransaction()) {
    $pdo->rollBack();
  }
  echo json_encode([
    'sucesso' => false,
    'mensagem' => 'Erro ao salvar dados: ' . $e->getMessage()
  ]);
}
