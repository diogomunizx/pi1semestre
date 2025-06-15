<?php
session_start();

// Verifica se o usuário está logado e é professor
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'professor') {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

require_once '../model/Database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Busca os horários das aulas do professor
    $query = "SELECT 
                CASE 
                    WHEN diaSemana = 1 THEN 'segunda'
                    WHEN diaSemana = 2 THEN 'terca'
                    WHEN diaSemana = 3 THEN 'quarta'
                    WHEN diaSemana = 4 THEN 'quinta'
                    WHEN diaSemana = 5 THEN 'sexta'
                    WHEN diaSemana = 6 THEN 'sabado'
                END as dia_semana,
                TIME_FORMAT(horarioInicio, '%H:%i') as hora_inicio,
                TIME_FORMAT(horarioFim, '%H:%i') as hora_fim
              FROM tb_HorasAulas 
              WHERE id_Docente = :id_docente";

    $stmt = $conn->prepare($query);
    $stmt->execute(['id_docente' => $_SESSION['id_Docente']]);
    $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($horarios);

} catch (Exception $e) {
    error_log("Erro ao buscar horários das aulas: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erro ao buscar horários das aulas']);
}
?> 