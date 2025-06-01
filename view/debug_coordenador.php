<?php
session_start();

// Verifica se o usuário está logado e é coordenador
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'coordenador') {
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    echo "<h2>Informações do Coordenador</h2>";
    echo "<pre>";
    echo "ID do Coordenador: " . $_SESSION['id_Docente'] . "\n";
    echo "Nome: " . $_SESSION['Nome'] . "\n";
    echo "Função: " . $_SESSION['funcao'] . "\n";
    echo "</pre>";

    // Busca os cursos que este coordenador coordena
    $queryCursos = "SELECT id_curso, Materia, id_docenteCoordenador 
                    FROM tb_cursos 
                    WHERE id_docenteCoordenador = :id_coordenador";
    $stmtCursos = $conn->prepare($queryCursos);
    $stmtCursos->execute(['id_coordenador' => $_SESSION['id_Docente']]);
    $cursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Cursos Coordenados</h2>";
    echo "<pre>";
    print_r($cursos);
    echo "</pre>";

    // Busca todas as inscrições dos cursos que coordena
    $queryInscricoes = "SELECT i.*, c.Materia as curso, u.Nome as professor
                        FROM tb_frm_inscricao_hae i
                        INNER JOIN tb_cursos c ON i.id_curso = c.id_curso
                        INNER JOIN tb_Usuario u ON i.tb_Docentes_id_Docente = u.id_Docente
                        WHERE c.id_docenteCoordenador = :id_coordenador";
    $stmtInscricoes = $conn->prepare($queryInscricoes);
    $stmtInscricoes->execute(['id_coordenador' => $_SESSION['id_Docente']]);
    $inscricoes = $stmtInscricoes->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Inscrições para Aprovação</h2>";
    echo "<pre>";
    print_r($inscricoes);
    echo "</pre>";

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?> 