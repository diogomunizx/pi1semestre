<?php
session_start();

// Verifica se o usuário está logado e é professor
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'professor') {
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // Inicia a transação
        $conn->beginTransaction();

        // Verifica se o edital existe
        $queryEdital = "SELECT id_edital, edital_status 
                       FROM tb_Editais 
                       WHERE id_edital = :id_edital";
        
        $stmtEdital = $conn->prepare($queryEdital);
        $stmtEdital->execute(['id_edital' => $_POST['id_edital']]);
        $edital = $stmtEdital->fetch();
        
        if (!$edital) {
            throw new Exception("O edital selecionado não existe.");
        }

        // Primeiro, busca o ID do coordenador do curso
        $queryCoordenador = "SELECT id_docenteCoordenador FROM tb_cursos WHERE id_curso = :id_curso";
        $stmtCoordenador = $conn->prepare($queryCoordenador);
        $stmtCoordenador->execute(['id_curso' => $_POST['curso']]);
        $coordenador = $stmtCoordenador->fetch();

        if (!$coordenador) {
            throw new Exception("Coordenador não encontrado para o curso selecionado.");
        }

        // Gera um novo ID para a inscrição
        $queryMaxId = "SELECT MAX(id_frmInscricaoHae) as max_id FROM tb_frm_inscricao_hae";
        $stmtMaxId = $conn->prepare($queryMaxId);
        $stmtMaxId->execute();
        $maxId = $stmtMaxId->fetch();
        $novoId = ($maxId['max_id'] ?? 0) + 1;

        // Insere a inscrição principal
        $queryInscricao = "INSERT INTO tb_frm_inscricao_hae (
            id_frmInscricaoHae,
            tb_Docentes_id_Docente,
            id_curso,
            tb_cursos_id_docenteCoordenador,
            tipoHae,
            tituloProjeto,
            inicioProjeto,
            fimProjeto,
            metasProjeto,
            objetivoProjeto,
            justificativaProjeto,
            recursosMateriais,
            resultadoEsperado,
            metodologia,
            cronogramaMes1,
            cronogramaMes2,
            cronogramaMes3,
            cronogramaMes4,
            cronogramaMes5,
            cronogramaMes6,
            quantidadeHae,
            tb_horarioExecHae_id_horarioExecHae,
            id_edital
        ) VALUES (
            :id_inscricao,
            :id_docente,
            :id_curso,
            :id_coordenador,
            :tipo_hae,
            :titulo_projeto,
            :inicio_projeto,
            :fim_projeto,
            :metas,
            :objetivos,
            :justificativas,
            :recursos,
            :resultados,
            :metodologia,
            :cronograma1,
            :cronograma2,
            :cronograma3,
            :cronograma4,
            :cronograma5,
            :cronograma6,
            :quantidade_hae,
            :id_horario,
            :id_edital
        )";

        $stmtInscricao = $conn->prepare($queryInscricao);
        $stmtInscricao->execute([
            'id_inscricao' => $novoId,
            'id_docente' => $_SESSION['id_Docente'],
            'id_curso' => $_POST['curso'],
            'id_coordenador' => $coordenador['id_docenteCoordenador'],
            'tipo_hae' => $_POST['role'],
            'titulo_projeto' => $_POST['titulo_projeto'],
            'inicio_projeto' => $_POST['inicio_projeto'],
            'fim_projeto' => $_POST['termino_projeto'],
            'metas' => $_POST['metas'],
            'objetivos' => $_POST['objetivos'],
            'justificativas' => $_POST['justificativas'],
            'recursos' => $_POST['recursos'],
            'resultados' => $_POST['resultados'] ?? '',
            'metodologia' => $_POST['metodologia'] ?? '',
            'cronograma1' => $_POST['cronograma_mes1'] ?? '',
            'cronograma2' => $_POST['cronograma_mes2'] ?? '',
            'cronograma3' => $_POST['cronograma_mes3'] ?? '',
            'cronograma4' => $_POST['cronograma_mes4'] ?? '',
            'cronograma5' => $_POST['cronograma_mes5'] ?? '',
            'cronograma6' => $_POST['cronograma_mes6'] ?? '',
            'quantidade_hae' => $_POST['hae_trabalho_gti'],
            'id_horario' => $novoId,
            'id_edital' => $_POST['id_edital']
        ]);

        // Insere os horários de execução
        $diasSemana = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];
        $queryHorario = "INSERT INTO tb_horarioExecHae (
            id_horarioExecHae,
            diaSemana,
            horarioInicio,
            horarioFinal,
            tb_frm_inscricao_hae_id_frmInscricaoHae,
            tb_frm_inscricao_hae_id_curso,
            tb_frm_inscricao_hae_tb_cursos_id_docenteCoordenador,
            tb_frm_inscricao_hae_tb_horarioExecHae_id_horarioExecHae
        ) VALUES (
            :id_horario,
            :dia_semana,
            :horario_inicio,
            :horario_final,
            :id_inscricao,
            :id_curso,
            :id_coordenador,
            :id_horario_ref
        )";

        $stmtHorario = $conn->prepare($queryHorario);
        
        foreach ($diasSemana as $index => $dia) {
            $horarioInicio = $_POST["horario_inicio_$dia"];
            $horarioFinal = $_POST["horario_final_$dia"];
            
            if (!empty($horarioInicio) && !empty($horarioFinal)) {
                $stmtHorario->execute([
                    'id_horario' => $novoId,
                    'dia_semana' => $index + 2, // 2 = segunda, 3 = terça, etc.
                    'horario_inicio' => $horarioInicio,
                    'horario_final' => $horarioFinal,
                    'id_inscricao' => $novoId,
                    'id_curso' => $_POST['curso'],
                    'id_coordenador' => $coordenador['id_docenteCoordenador'],
                    'id_horario_ref' => $novoId
                ]);
            }
        }

        // Confirma a transação
        $conn->commit();

        // Redireciona para a página de inscrições com mensagem de sucesso
        $_SESSION['mensagem'] = "Inscrição realizada com sucesso!";
        header("Location: inscricao.php");
        exit;

    } catch (Exception $e) {
        // Em caso de erro, desfaz a transação
        if (isset($conn)) {
            $conn->rollBack();
        }
        error_log("Erro ao salvar inscrição: " . $e->getMessage());
        $_SESSION['erro'] = "Ocorreu um erro ao salvar a inscrição: " . $e->getMessage();
        header("Location: form_inscricao.php");
        exit;
    }
} else {
    header("Location: form_inscricao.php");
    exit;
} 