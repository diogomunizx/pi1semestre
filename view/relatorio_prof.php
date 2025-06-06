<?php
session_start();

// Verifica se o usuário está logado e é professor
if (!isset($_SESSION['id_Docente']) || strtolower($_SESSION['funcao']) !== 'professor') {
    header("Location: ../login.php");
    exit;
}

require_once '../model/Database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Busca as inscrições aprovadas do professor
    $query = "SELECT i.id_frmInscricaoHae, 
                     i.tituloProjeto,
                     i.tipoHae,
                     i.quantidadeHae,
                     c.Materia as curso,
                     coord.Nome as coordenador,
                     j.status as status_inscricao,
                     r.status as status_relatorio,
                     r.id_relatorioHae
              FROM tb_frm_inscricao_hae i
              INNER JOIN tb_cursos c ON i.id_curso = c.id_curso
              INNER JOIN tb_Usuario coord ON c.id_docenteCoordenador = coord.id_Docente
              INNER JOIN tb_justificativaHae j ON i.id_frmInscricaoHae = j.id_frmInscricaoHae
              LEFT JOIN tb_relatorioHae r ON i.id_frmInscricaoHae = r.id_frmInscricaoHae
              WHERE i.tb_Docentes_id_Docente = :id_docente
              AND j.status = 'APROVADO'
              ORDER BY i.id_frmInscricaoHae DESC";
              
    $stmt = $conn->prepare($query);
    $stmt->execute(['id_docente' => $_SESSION['id_Docente']]);
    $inscricoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Erro ao buscar inscrições: " . $e->getMessage());
    $erro = "Ocorreu um erro ao carregar as inscrições. Por favor, tente novamente mais tarde.";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../estilos/style.css">
    <link rel="icon" type="image/png" href="../imagens/logo-horus.png">
    <title>HORUS - Relatório</title>
    <style>
        .form-container, #formularioProfessor {
            max-width: 1000px;
            margin: 20px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Indicadores de Progresso */
        .step-indicators {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .step-indicator {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
            position: relative;
        }

        .step-indicator.active {
            background: #28a745;
            color: white;
        }

        .step-indicator.completed {
            background: #28a745;
            color: white;
        }

        /* Barra de Progresso */
        .progress-container {
            width: 100%;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            margin: 20px 0 30px;
        }

        .progress-bar {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .progress-fill {
            position: absolute;
            height: 100%;
            background: #28a745;
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        /* Etapas do Formulário */
        .form-steps {
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .form-steps.active {
            display: block;
            opacity: 1;
        }

        .form-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .form-section {
            margin-bottom: 40px;
        }

        .form-section h4 {
            color: #2c3e50;
            font-size: 20px;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 15px;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="date"],
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #28a745;
            outline: none;
        }

        .form-group textarea {
            min-height: 180px;
            resize: vertical;
        }

        .info-text {
            padding: 12px;
            background: #f8f9fa;
            border-radius: 6px;
            margin: 5px 0;
            border: 1px solid #e9ecef;
            font-size: 14px;
            color: #495057;
        }

        .field-description {
            font-size: 13px;
            color: #6c757d;
            margin: 5px 0 10px;
            font-style: italic;
        }

        .projeto-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #e9ecef;
        }

        .projeto-info .form-group:last-child {
            margin-bottom: 0;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .nav-button {
            display: inline-block;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        .nav-button:hover {
            transform: translateY(-1px);
            opacity: 0.9;
        }

        .nav-button.prev {
            background-color: #6c757d;
            color: white;
        }

        .nav-button.submit {
            background-color: #28a745;
            color: white;
            padding: 14px 28px;
            font-size: 16px;
            min-width: 200px;
        }

        /* Estilo para campos inválidos */
        .form-group input.invalid,
        .form-group textarea.invalid {
            border-color: #dc3545;
        }
    </style>
</head>

<body>
    <header>
        <div class="header-content">
            <div class="user-profile" onclick="toggleDropdown()">
                <span><?php echo htmlspecialchars($_SESSION['Nome'][0]); ?></span>
                <div class="dropdown-menu" id="dropdown-menu">
                    <a href="#" onclick="alterarVisualizacao()">Alterar Visualização</a>
                    <a href="perfil_cadastro.php">Ajustes</a>
                    <a href="perfil_Aulas.php">Minhas aulas</a>
                </div>
            </div>
            <div class="institutions">
                <div class="fatec">
                    <a href="https://fatecitapira.cps.sp.gov.br/" target="_blank">
                        <img src="../imagens/logo-fatec_itapira.png">
                    </a>
                </div>
                <div class="cps">
                    <a href="https://www.cps.sp.gov.br/" target="_blank">
                        <img src="../imagens/logo-cps.png">
                    </a>
                </div>
            </div>
        </div>
    </header>

    <nav class="sidebar">
        <div class="logo-container">
            <a href="#">
                <img src="../imagens/logo-horus.png" alt="Logo HORUS">
            </a>
        </div>
        <a class="inicio" href="index_prof.php">
            <img src="../imagens/home.png" alt="Início"> <span>Início</span>
        </a>
        <a href="inscricao.php" id="linkInscricao">
            <img src="../imagens/inscricao.png" alt="Inscrição"> <span>Inscrição</span>
        </a>
        <a href="relatorio_prof.php" class="active">
            <img src="../imagens/relat.png" alt="Relatório"> <span>Relatório</span>
        </a>
        <a href="../login.php">
            <img src="../imagens/logout.png" alt="Logout"> <span>Logout</span>
        </a>
    </nav>

    <main>
        <?php if (isset($erro)): ?>
            <div class="erro"><?php echo $erro; ?></div>
        <?php else: ?>
            <table class="tbls" id="tableProfessor">
                <h3 class="titulos">Seus projetos para envio de relatórios</h3>
                <br>
                <?php if (isset($_SESSION['mensagem'])): ?>
                    <div class="sucesso"><?php echo $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?></div>
                <?php endif; ?>
                
                <thead>
                    <tr>
                        <td>Inscrição</td>
                        <td>Coordenador</td>
                        <td>Projeto</td>
                        <td>Tipo HAE</td>
                        <td>Quantidade HAE</td>
                        <td>Status Relatório</td>
                        <td>Ações</td>
                        <td>Imprimir</td>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($inscricoes)): ?>
                        <tr>
                            <td colspan="8">Nenhum projeto aprovado encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($inscricoes as $inscricao): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($inscricao['id_frmInscricaoHae']); ?></td>
                                <td><?php echo htmlspecialchars($inscricao['coordenador']); ?></td>
                                <td><?php echo htmlspecialchars($inscricao['tituloProjeto']); ?></td>
                                <td><?php echo htmlspecialchars($inscricao['tipoHae']); ?></td>
                                <td><?php echo htmlspecialchars($inscricao['quantidadeHae']); ?></td>
                                <td>
                                    <?php if (!$inscricao['status_relatorio']): ?>
                                        <span class="status-badge status-pendente">PENDENTE</span>
                                    <?php else: ?>
                                        <span class="status-badge status-<?php echo strtolower($inscricao['status_relatorio']); ?>">
                                            <?php echo $inscricao['status_relatorio']; ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="destaque">
                                    <?php if (!$inscricao['status_relatorio'] || $inscricao['status_relatorio'] === 'CORRECAO'): ?>
                                        <a href="#" onclick="prepararRelatorioHae(
                                            '<?php echo $inscricao['id_frmInscricaoHae']; ?>', 
                                            '<?php echo htmlspecialchars(addslashes($inscricao['tituloProjeto'])); ?>', 
                                            '<?php echo htmlspecialchars(addslashes($inscricao['tipoHae'])); ?>',
                                            '<?php echo htmlspecialchars(addslashes($inscricao['coordenador'])); ?>',
                                            '<?php echo htmlspecialchars(addslashes($inscricao['curso'])); ?>',
                                            '<?php echo htmlspecialchars($inscricao['quantidadeHae']); ?>'
                                        ); return false;">
                                            <img src="../imagens/relatorio.png" style="cursor: pointer;">
                                        </a>
                                    <?php else: ?>
                                        <a href="#" onclick="verRelatorio('<?php echo $inscricao['id_relatorioHae']; ?>'); return false;">
                                            <img src="../imagens/olho.png" style="cursor: pointer;">
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="#" onclick="imprimirRelatorio('<?php echo $inscricao['id_frmInscricaoHae']; ?>'); return false;">
                                        <img class="destaque" src="../imagens/imprimir.png" style="cursor: pointer;">
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <div id="formularioProfessor" style="display: none;">
                <!-- Indicadores de Progresso -->
                <div class="step-indicators">
                    <div class="step-indicator active" data-step="1">1</div>
                    <div class="step-indicator" data-step="2">2</div>
                </div>

                <!-- Barra de Progresso -->
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 50%"></div>
                    </div>
                </div>

                <form class="form-relatorio" action="processa_relatorio.php" method="POST">
                    <input type="hidden" name="id_inscricao" id="id_inscricao">
                    
                    <!-- Etapa 1: Informações do Professor e Projeto -->
                    <div class="form-steps active" id="step1">
                        <h4>Informações do Professor e Projeto</h4>
                        <div class="form-group">
                            <label for="professor">Nome:</label>
                            <input type="text" id="professor" name="professor" disabled value="<?php echo htmlspecialchars($_SESSION['Nome']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="email">E-mail:</label>
                            <input type="email" id="email" name="email" disabled value="<?php echo htmlspecialchars($_SESSION['email']); ?>">
                        </div>

                        <div class="form-group">
                            <label for="matricula">Matrícula:</label>
                            <input type="text" id="matricula" name="matricula" value="<?php echo htmlspecialchars($_SESSION['id_Docente']); ?>" disabled>
                        </div>

                        <div class="projeto-info">
                            <div class="form-group">
                                <label>Título do Projeto:</label>
                                <p id="titulo_projeto_display" class="info-text"></p>
                            </div>

                            <div class="form-group">
                                <label>Tipo HAE:</label>
                                <p id="tipo_hae_display" class="info-text"></p>
                            </div>

                            <div class="form-group">
                                <label>Coordenador:</label>
                                <p id="coordenador_display" class="info-text"></p>
                            </div>

                            <div class="form-group">
                                <label>Curso:</label>
                                <p id="curso_display" class="info-text"></p>
                            </div>

                            <div class="form-group">
                                <label>Quantidade HAE:</label>
                                <p id="quantidade_hae_display" class="info-text"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Etapa 2: Relatório -->
                    <div class="form-steps" id="step2">
                        <h4>Relatório de Atividades</h4>
                        
                        <div class="form-group">
                            <label for="descricao_atividades">Descrição das Atividades Realizadas:</label>
                            <p class="field-description">Descreva detalhadamente as atividades desenvolvidas durante o projeto.</p>
                            <textarea name="descricao_atividades" id="descricao_atividades" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="resultados_alcancados">Resultados Alcançados:</label>
                            <p class="field-description">Descreva os resultados obtidos e como eles se relacionam com os objetivos iniciais do projeto.</p>
                            <textarea name="resultados_alcancados" id="resultados_alcancados" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="data_entrega">Data de Entrega:</label>
                            <input type="date" id="data_entrega" name="data_entrega" required>
                        </div>
                    </div>

                    <!-- Navegação entre etapas -->
                    <div class="form-navigation">
                        <button type="button" class="nav-button prev" onclick="prevStep()" disabled>Anterior</button>
                        <button type="button" class="nav-button next" onclick="nextStep()">Próxima</button>
                        <button type="submit" class="nav-button submit" style="display: none;">Enviar Relatório</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </main>

    <script>
        let currentStep = 1;
        const totalSteps = 2;

        function updateProgress() {
            const progress = (currentStep / totalSteps) * 100;
            document.querySelector('.progress-fill').style.width = `${progress}%`;

            // Atualiza os indicadores de etapa
            document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
                indicator.classList.remove('active', 'completed');
                if (index + 1 < currentStep) {
                    indicator.classList.add('completed');
                } else if (index + 1 === currentStep) {
                    indicator.classList.add('active');
                }
            });

            // Atualiza os botões de navegação
            const prevButton = document.querySelector('.nav-button.prev');
            const nextButton = document.querySelector('.nav-button.next');
            const submitButton = document.querySelector('.nav-button.submit');

            prevButton.disabled = currentStep === 1;
            
            if (currentStep === totalSteps) {
                nextButton.style.display = 'none';
                submitButton.style.display = 'block';
            } else {
                nextButton.style.display = 'block';
                submitButton.style.display = 'none';
            }
        }

        function showStep(step) {
            // Oculta todas as etapas com transição suave
            document.querySelectorAll('.form-steps').forEach(formStep => {
                formStep.classList.remove('active');
            });

            // Mostra a etapa atual com transição suave
            const currentStepElement = document.getElementById(`step${step}`);
            if (currentStepElement) {
                setTimeout(() => {
                    currentStepElement.classList.add('active');
                }, 50);
            }
        }

        function nextStep() {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
                updateProgress();
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
                updateProgress();
            }
        }

        // Funções específicas para o relatório do professor
        const prepararRelatorioHae = (idInscricao, tituloProjeto, tipoHae, coordenador, curso, quantidadeHae) => {
            // Esconde a tabela e mostra o formulário
            document.getElementById('tableProfessor').style.display = 'none';
            document.getElementById('formularioProfessor').style.display = 'block';
            
            // Preenche os campos do formulário
            document.getElementById('id_inscricao').value = idInscricao;
            document.getElementById('titulo_projeto_display').textContent = tituloProjeto.replace(/\\/g, '');
            document.getElementById('tipo_hae_display').textContent = tipoHae.replace(/\\/g, '');
            document.getElementById('coordenador_display').textContent = coordenador.replace(/\\/g, '');
            document.getElementById('curso_display').textContent = curso.replace(/\\/g, '');
            document.getElementById('quantidade_hae_display').textContent = quantidadeHae;
            
            // Define a data mínima como hoje
            const hoje = new Date().toISOString().split('T')[0];
            document.getElementById('data_entrega').min = hoje;

            // Reinicia o progresso
            currentStep = 1;
            showStep(1);
            updateProgress();
        };

        const verRelatorio = (idRelatorio) => {
            window.location.href = `ver_relatorio.php?id=${idRelatorio}`;
        };

        const imprimirRelatorio = (idInscricao) => {
            window.open(`imprimir_relatorio.php?id=${idInscricao}`, '_blank');
        };

        // Adiciona validação no envio do formulário
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('.form-relatorio');
            if (form) {
                form.addEventListener('submit', (e) => {
                    const requiredFields = form.querySelectorAll('[required]');
                    let isValid = true;

                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.classList.add('invalid');
                        } else {
                            field.classList.remove('invalid');
                        }
                    });

                    if (!isValid) {
                        e.preventDefault();
                        alert('Por favor, preencha todos os campos obrigatórios.');
                    }
                });
            }
        });
    </script>

    <script src="../js/script.js" defer></script>
</body>

</html> 