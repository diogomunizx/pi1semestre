<?php
session_start();

// Verifica se o usuário está logado e é coordenador
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'coordenador') {
    header('Content-Type: application/json');
    echo json_encode(['erro' => 'Acesso não autorizado']);
    exit;
}

require_once '../model/Database.php';

// Recebe os dados do POST
$dados = json_decode(file_get_contents('php://input'), true);

if (!isset($dados['tipo_evento']) || !isset($dados['data_inicio']) || !isset($dados['data_fim'])) {
    header('Content-Type: application/json');
    echo json_encode(['erro' => 'Dados incompletos']);
    exit;
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Busca o edital mais recente
    $queryEdital = "SELECT id_edital FROM tb_Editais ORDER BY id_edital DESC LIMIT 1";
    $stmtEdital = $conn->query($queryEdital);
    $edital = $stmtEdital->fetch();
    
    if (!$edital) {
        header('Content-Type: application/json');
        echo json_encode(['erro' => 'Nenhum edital encontrado']);
        exit;
    }
    
    $idEdital = $edital['id_edital'];
    
    // Verifica se já existe um registro para este tipo de evento neste edital
    $queryVerifica = "SELECT id_cronograma FROM tb_cronograma WHERE tipo_evento = :tipo_evento AND id_edital = :id_edital";
    $stmtVerifica = $conn->prepare($queryVerifica);
    $stmtVerifica->execute([
        'tipo_evento' => $dados['tipo_evento'],
        'id_edital' => $idEdital
    ]);
    
    if ($stmtVerifica->fetch()) {
        // Atualiza o registro existente
        $query = "UPDATE tb_cronograma SET data_inicio = :data_inicio, data_fim = :data_fim WHERE tipo_evento = :tipo_evento AND id_edital = :id_edital";
    } else {
        // Insere um novo registro
        $query = "INSERT INTO tb_cronograma (tipo_evento, data_inicio, data_fim, id_edital) VALUES (:tipo_evento, :data_inicio, :data_fim, :id_edital)";
    }
    
    $stmt = $conn->prepare($query);
    $stmt->execute([
        'tipo_evento' => $dados['tipo_evento'],
        'data_inicio' => $dados['data_inicio'],
        'data_fim' => $dados['data_fim'],
        'id_edital' => $idEdital
    ]);
    
    header('Content-Type: application/json');
    echo json_encode(['sucesso' => true]);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['erro' => 'Erro ao salvar as datas: ' . $e->getMessage()]);
} 