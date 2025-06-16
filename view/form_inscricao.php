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

    // Debug para ver a conexão
    if (!$conn) {
        error_log("Erro: Conexão não estabelecida");
    }

    // Busca os dados do professor
    $queryProfessor = "SELECT email, matricula FROM tb_Usuario WHERE id_Docente = :id_docente";
    $stmtProfessor = $conn->prepare($queryProfessor);
    $stmtProfessor->execute(['id_docente' => $_SESSION['id_Docente']]);
    $dadosProfessor = $stmtProfessor->fetch(PDO::FETCH_ASSOC);

    // Busca os editais disponíveis
    $queryEditais = "SELECT id_edital, vigencia, dataInicioInscricao, dataFimInscricao, edital_status 
                    FROM tb_Editais 
                    WHERE edital_status = 'ABERTO' 
                    AND dataFimInscricao >= CURDATE()
                    ORDER BY dataFimInscricao DESC";
    $stmtEditais = $conn->prepare($queryEditais);
    $stmtEditais->execute();
    $editais = $stmtEditais->fetchAll(PDO::FETCH_ASSOC);

    // Debug para ver os editais retornados
    error_log("Editais encontrados: " . print_r($editais, true));

    // Busca os cursos
    $queryCursos = "SELECT c.id_curso, c.Materia, c.id_docenteCoordenador, u.Nome as coordenador 
                    FROM tb_cursos c 
                    LEFT JOIN tb_Usuario u ON c.id_docenteCoordenador = u.id_Docente 
                    ORDER BY c.Materia";
    $stmtCursos = $conn->prepare($queryCursos);
    $stmtCursos->execute();
    $cursos = $stmtCursos->fetchAll();
} catch (Exception $e) {
    error_log("Erro ao carregar dados: " . $e->getMessage());
    $_SESSION['erro'] = "Ocorreu um erro ao carregar os dados. Por favor, tente novamente mais tarde.";
    header("Location: inscricao.php");
    exit;
}

// Função auxiliar para formatar a data
function formatarData($data)
{
    return date('d/m/Y', strtotime($data));
}

// Adicionar no início do arquivo, após o session_start()
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantidadeHAE = intval($_POST['hae_trabalho_gti']);
    $totalHoras = 0;
    $dias = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];

    // Calcula o total de horas selecionadas
    foreach ($dias as $dia) {
        $inicio = $_POST["horario_inicio_{$dia}"] ?? '';
        $fim = $_POST["horario_final_{$dia}"] ?? '';
        
        if ($inicio && $fim) {
            $inicioHora = intval(explode(':', $inicio)[0]);
            $fimHora = intval(explode(':', $fim)[0]);
            $totalHoras += ($fimHora - $inicioHora);
        }
    }

    // Valida se o total de horas corresponde à quantidade de HAE
    if ($totalHoras !== $quantidadeHAE) {
        $_SESSION['erro'] = "A quantidade de horas selecionadas ({$totalHoras}h) deve ser exatamente igual à quantidade de HAE solicitada ({$quantidadeHAE}h).";
        header("Location: form_inscricao.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../estilos/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="icon" type="image/png" href="../imagens/logo-horus.png">
    <title>HORUS - Nova Inscrição</title>
    <style>
        /* Estilos específicos para as etapas do formulário */
        .form-steps {
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .form-steps.active {
            display: block;
            opacity: 1;
        }

        /* Ajuste do espaçamento entre as seções */
        .form-steps>h4 {
            margin-top: 20px;
            margin-bottom: 15px;
        }

        /* Estilos para os botões de navegação */
        .form-navigation {
            margin-top: 30px;
            padding: 20px 0;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
        }

        .nav-button {
            min-width: 120px;
        }

        .edital-info {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .edital-info select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .edital-status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-aberto {
            background-color: #28a745;
            color: white;
        }

        .status-encerrado {
            background-color: #dc3545;
            color: white;
        }

        .edital-datas {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        /* Adicione estes estilos */
        .horario-select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 120px;
            background-color: #fff;
            cursor: pointer;
        }

        .horario-select:disabled {
            background-color: #f5f5f5;
            cursor: not-allowed;
        }

        .horario-error {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .tabela-inscricao td {
            padding: 10px;
            vertical-align: middle;
        }

        .horario-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .horario-label {
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>

<body>
    <header>
        <div class="header-content">
            <div class="user-profile" onclick="toggleDropdown()">
                <span><?php echo htmlspecialchars($_SESSION['Nome'][0]); ?></span>
                <div class="dropdown-menu" id="dropdown-menu">
                    <a href="perfil_cadastro.php">Ajustes</a>
                    <a href="perfil_Aulas.php">Minhas aulas</a>
                </div>
            </div>
            <div class="institutions">
                <div class="fatec">
                    <a href="https://fatecitapira.cps.sp.gov.br/" target="_blank"><img
                            src="../imagens/logo-fatec_itapira.png"></a>
                </div>
                <div class="cps">
                    <a href="https://www.cps.sp.gov.br/" target="_blank"><img src="../imagens/logo-cps.png"></a>
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
        <a href="editais_prof.php">
            <img src="../imagens/aprovacao.png" alt="Editais"> <span>Editais</span>
        </a>
        <a href="relatorio_prof.php">
            <img src="../imagens/relat.png" alt="Relatório"> <span>Relatório</span>
        </a>
        <a href="dashboard_professor.php">
            <img src="../imagens/grafico-de-barras.png" alt="Dashboard"> <span>Dashboard</span>
        </a>
        <a href="../login.php">
            <img src="../imagens/logout.png" alt="Logout"> <span>Logout</span>
        </a>
    </nav>

    <main>
        <div class="form-container">
            <div class="info-inscricao">
                <h3>Nova Inscrição</h3>
                <h3>Projeto de Hora Atividade Específica – H.A.E.</h3>
            </div>

            <form class="form-inscricao" action="processa_inscricao.php" method="POST">
                <?php if (isset($_SESSION['erro'])): ?>
                    <div class="erro"><?php echo $_SESSION['erro'];
                                        unset($_SESSION['erro']); ?></div>
                <?php endif; ?>

                <!-- Seleção do Edital -->
                <div class="edital-info">
                    <h4>Selecione o Edital:</h4>
                    <?php if (empty($editais)): ?>
                        <p style="color: red;">Nenhum edital encontrado no sistema.</p>
                    <?php else: ?>
                        <select id="edital" name="id_edital" required onchange="atualizarInfoEdital()">
                            <option value="">Selecione um edital</option>
                            <?php foreach ($editais as $edital): ?>
                                <option value="<?php echo $edital['id_edital']; ?>"
                                    data-status="<?php echo $edital['edital_status']; ?>"
                                    data-inicio="<?php echo formatarData($edital['dataInicioInscricao']); ?>"
                                    data-fim="<?php echo formatarData($edital['dataFimInscricao']); ?>">
                                    <?php echo htmlspecialchars($edital['vigencia'] . ' (ID: ' . $edital['id_edital'] . ')'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="edital-detalhes" class="edital-datas" style="display: none;">
                            Status: <span id="edital-status"></span><br>
                            Período de Inscrições: <span id="edital-periodo"></span>
                        </div>
                    <?php endif; ?>

                    <!-- Debug: Mostra os dados dos editais -->
                    <?php if (isset($_SESSION['debug']) && $_SESSION['debug']): ?>
                        <div style="margin-top: 20px; padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">
                            <h5>Debug: Editais Disponíveis</h5>
                            <pre><?php print_r($editais); ?></pre>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Indicadores de Progresso -->
                <div class="step-indicators">
                    <div class="step-indicator active" data-step="1">1</div>
                    <div class="step-indicator" data-step="2">2</div>
                    <div class="step-indicator" data-step="3">3</div>
                    <div class="step-indicator" data-step="4">4</div>
                </div>

                <!-- Barra de Progresso -->
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 25%"></div>
                    </div>
                </div>

                <!-- Etapa 1: Informações do Docente -->
                <div class="form-steps active" id="step1">
                    <h4>Informações docente</h4>
                    <label for="professor">Nome:</label>
                    <input type="text" id="professor" name="professor" disabled value="<?php echo htmlspecialchars($_SESSION['Nome']); ?>" required><br>

                    <label for="email">E-mail:</label>
                    <input type="email" id="email" name="email" disabled value="<?php echo htmlspecialchars($dadosProfessor['email']); ?>" required><br>

                    <label for="matricula">Matrícula:</label>
                    <input type="text" id="matricula" name="matricula" disabled value="<?php echo htmlspecialchars($dadosProfessor['matricula']); ?>" required>
                </div>

                <!-- Etapa 2: Tipo de HAE e Curso -->
                <div class="form-steps" id="step2">
                    <h4>Tipo de HAE que está solicitando:</h4>
                    <select id="role" name="role" required>
                        <option value="" disabled selected>Selecione o tipo de HAE</option>
                        <option value="Estudos e Projetos">Estudos e Projetos</option>
                        <option value="Extensão de serviços à comunidade">Extensão de serviços à comunidade</option>
                        <option value="Administração acadêmica">Administração acadêmica</option>
                        <option value="Plantão didático">Plantão didático</option>
                        <option value="Estágio Supervisionado">Estágio Supervisionado</option>
                        <option value="Orientação de TG">Orientação de Trabalho de Graduação</option>
                        <option value="Iniciação científica">Projeto de iniciação científica</option>
                        <option value="Revista Prospectus">Revista Prospectus</option>
                        <option value="Divulgação de Cursos">Divulgação Cursos</option>
                        <option value="ENADE">ENADE</option>
                    </select>

                    <h4>Curso</h4>
                    <select id="curso" name="curso" required>
                        <option value="">Selecione um curso</option>
                        <?php foreach ($cursos as $curso): ?>
                            <option value="<?php echo $curso['id_curso']; ?>"
                                data-coordenador-id="<?php echo $curso['id_docenteCoordenador']; ?>">
                                <?php echo htmlspecialchars($curso['Materia'] . ' - Coord.: ' . $curso['coordenador']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="id_docenteCoordenador" id="id_docenteCoordenador">

                    <div class="hae-projeto-linha">
                        <label for="hae">Quantidade de H.A.E:</label>
                        <input type="number" id="gti" name="hae_trabalho_gti" min="0" max="12">
                    </div>
                </div>

                <!-- Etapa 3: Informações do Projeto -->
                <div class="form-steps" id="step3">
                    <h4>Informações do projeto</h4>
                    <label for="titulo_projeto">Título do projeto:</label>
                    <input type="text" id="titulo_projeto" name="titulo_projeto" required><br>

                    <h4>Horário de Execução do Projeto</h4>
                    <table class="tabela-inscricao" border="1">
                        <tr>
                            <th>Dia da Semana</th>
                            <th>Horário Início</th>
                            <th>Horário Final</th>
                        </tr>
                        <tr>
                            <td>Segunda-feira</td>
                            <td>
                                <div class="horario-container">
                                    <select name="horario_inicio_segunda" class="horario-select">
                                        <?php echo gerarOpcoesHorario(); ?>
                                    </select>
                                    <div id="horario-error-segunda" class="horario-error"></div>
                                </div>
                            </td>
                            <td>
                                <div class="horario-container">
                                    <select name="horario_final_segunda" class="horario-select">
                                        <?php echo gerarOpcoesHorario(); ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Terça-feira</td>
                            <td>
                                <div class="horario-container">
                                    <select name="horario_inicio_terca" class="horario-select">
                                        <?php echo gerarOpcoesHorario(); ?>
                                    </select>
                                    <div id="horario-error-terca" class="horario-error"></div>
                                </div>
                            </td>
                            <td>
                                <div class="horario-container">
                                    <select name="horario_final_terca" class="horario-select">
                                        <?php echo gerarOpcoesHorario(); ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Quarta-feira</td>
                            <td>
                                <div class="horario-container">
                                    <select name="horario_inicio_quarta" class="horario-select">
                                        <?php echo gerarOpcoesHorario(); ?>
                                    </select>
                                    <div id="horario-error-quarta" class="horario-error"></div>
                                </div>
                            </td>
                            <td>
                                <div class="horario-container">
                                    <select name="horario_final_quarta" class="horario-select">
                                        <?php echo gerarOpcoesHorario(); ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Quinta-feira</td>
                            <td>
                                <div class="horario-container">
                                    <select name="horario_inicio_quinta" class="horario-select">
                                        <?php echo gerarOpcoesHorario(); ?>
                                    </select>
                                    <div id="horario-error-quinta" class="horario-error"></div>
                                </div>
                            </td>
                            <td>
                                <div class="horario-container">
                                    <select name="horario_final_quinta" class="horario-select">
                                        <?php echo gerarOpcoesHorario(); ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Sexta-feira</td>
                            <td>
                                <div class="horario-container">
                                    <select name="horario_inicio_sexta" class="horario-select">
                                        <?php echo gerarOpcoesHorario(); ?>
                                    </select>
                                    <div id="horario-error-sexta" class="horario-error"></div>
                                </div>
                            </td>
                            <td>
                                <div class="horario-container">
                                    <select name="horario_final_sexta" class="horario-select">
                                        <?php echo gerarOpcoesHorario(); ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Sábado</td>
                            <td>
                                <div class="horario-container">
                                    <select name="horario_inicio_sabado" class="horario-select">
                                        <?php echo gerarOpcoesHorario(); ?>
                                    </select>
                                    <div id="horario-error-sabado" class="horario-error"></div>
                                </div>
                            </td>
                            <td>
                                <div class="horario-container">
                                    <select name="horario_final_sabado" class="horario-select">
                                        <?php echo gerarOpcoesHorario(); ?>
                                    </select>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Etapa 4: Detalhamento do Projeto -->
                <div class="form-steps" id="step4">
                    <h4>Detalhamento do Projeto</h4>
                    <table class="tabela-inscricao" border="1">
                        <tr>
                            <th colspan="2">1 – METAS RELACIONADAS AO PROJETO</th>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <textarea id="metas" name="metas" class="textarea-auto-ajuste"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">2 - OBJETIVOS DO PROJETO – DETALHAMENTO</th>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <textarea id="objetivos" name="objetivos" class="textarea-auto-ajuste"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">3 - JUSTIFICATIVAS DO PROJETO – DETALHAMENTO</th>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <textarea id="justificativas" name="justificativas" class="textarea-auto-ajuste"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">4 - RECURSOS MATERIAIS E HUMANOS - DETALHAMENTO</th>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <textarea id="recursos" name="recursos" class="textarea-auto-ajuste"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">5 - RESULTADOS ESPERADOS</th>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <textarea id="resultados" name="resultados" class="textarea-auto-ajuste"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">6 - METODOLOGIA</th>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <textarea id="metodologia" name="metodologia" class="textarea-auto-ajuste"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">7 - CRONOGRAMA DE EXECUÇÃO</th>
                        </tr>
                        <tr>
                            <td>Mês 1</td>
                            <td>
                                <textarea id="cronograma_mes1" name="cronograma_mes1" class="textarea-auto-ajuste"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>Mês 2</td>
                            <td>
                                <textarea id="cronograma_mes2" name="cronograma_mes2" class="textarea-auto-ajuste"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>Mês 3</td>
                            <td>
                                <textarea id="cronograma_mes3" name="cronograma_mes3" class="textarea-auto-ajuste"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>Mês 4</td>
                            <td>
                                <textarea id="cronograma_mes4" name="cronograma_mes4" class="textarea-auto-ajuste"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>Mês 5</td>
                            <td>
                                <textarea id="cronograma_mes5" name="cronograma_mes5" class="textarea-auto-ajuste"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>Mês 6</td>
                            <td>
                                <textarea id="cronograma_mes6" name="cronograma_mes6" class="textarea-auto-ajuste"></textarea>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Navegação entre etapas -->
                <div class="form-navigation">
                    <button type="button" class="nav-button prev" onclick="prevStep()" disabled>Anterior</button>
                    <button type="button" class="nav-button next" onclick="nextStep()">Próxima</button>
                    <button type="submit" class="nav-button submit">Enviar</button>
                </div>
            </form>
        </div>
    </main>
    <script src="../js/script.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currentStep = 1;
            const totalSteps = 4;

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

            // Adiciona os event listeners aos botões
            document.querySelector('.nav-button.next').addEventListener('click', nextStep);
            document.querySelector('.nav-button.prev').addEventListener('click', prevStep);

            // Inicializa o formulário
            updateProgress();
        });

        function atualizarInfoEdital() {
            const select = document.getElementById('edital');
            const detalhes = document.getElementById('edital-detalhes');
            const status = document.getElementById('edital-status');
            const periodo = document.getElementById('edital-periodo');

            if (select.value) {
                const option = select.options[select.selectedIndex];
                const editalStatus = option.dataset.status;

                status.innerHTML = `<span class="edital-status status-${editalStatus.toLowerCase()}">${editalStatus}</span>`;
                periodo.textContent = `${option.dataset.inicio} a ${option.dataset.fim}`;

                detalhes.style.display = 'block';
            } else {
                detalhes.style.display = 'none';
            }
        }

        document.getElementById('curso').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const coordenadorId = selectedOption.getAttribute('data-coordenador-id');
            document.getElementById('id_docenteCoordenador').value = coordenadorId;
        });

        // Função para validar os horários com base na quantidade de HAE
        function validarHorariosComHAE() {
            const dias = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];
            let totalHorasSelecionadas = 0;

            // Função para calcular o total de horas selecionadas
            function calcularTotalHoras() {
                totalHorasSelecionadas = 0;
                dias.forEach(dia => {
                    const inicio = document.querySelector(`select[name="horario_inicio_${dia}"]`);
                    const fim = document.querySelector(`select[name="horario_final_${dia}"]`);
                    
                    if (inicio.value && fim.value) {
                        const inicioHora = parseInt(inicio.value.split(':')[0]);
                        const fimHora = parseInt(fim.value.split(':')[0]);
                        if (fimHora > inicioHora) {
                            totalHorasSelecionadas += (fimHora - inicioHora);
                        }
                    }
                });
                return totalHorasSelecionadas;
            }

            // Função para validar e atualizar os selects
            function validarHorario(dia, inicio, fim) {
                const quantidadeHAE = parseInt(document.getElementById('gti').value) || 0;
                
                if (inicio.value && fim.value) {
                    const inicioHora = parseInt(inicio.value.split(':')[0]);
                    const fimHora = parseInt(fim.value.split(':')[0]);
                    
                    // Verifica se o horário final é maior que o inicial
                    if (fimHora <= inicioHora) {
                        alert(`O horário final deve ser maior que o horário de início para ${dia}-feira`);
                        inicio.value = '';
                        fim.value = '';
                        return;
                    }

                    const horasDia = fimHora - inicioHora;
                    
                    // Calcula o novo total de horas
                    const novoTotal = calcularTotalHoras();
                    
                    // Verifica se o novo total excede a quantidade de HAE
                    if (novoTotal > quantidadeHAE) {
                        alert(`Você solicitou ${quantidadeHAE} HAE (${quantidadeHAE} hora(s)). A soma total de horas não pode exceder este valor.`);
                        inicio.value = '';
                        fim.value = '';
                        return;
                    }

                    // Mostra mensagem informativa sobre horas restantes
                    const horasRestantes = quantidadeHAE - novoTotal;
                    if (horasRestantes > 0) {
                        console.log(`Você ainda pode selecionar ${horasRestantes} hora(s) de trabalho.`);
                    } else if (horasRestantes === 0) {
                        console.log('Você atingiu o limite de horas para a quantidade de HAE solicitada.');
                    }
                }
            }

            // Adiciona os event listeners para cada dia
            dias.forEach(dia => {
                const inicio = document.querySelector(`select[name="horario_inicio_${dia}"]`);
                const fim = document.querySelector(`select[name="horario_final_${dia}"]`);

                inicio.addEventListener('change', () => {
                    validarHorario(dia, inicio, fim);
                });

                fim.addEventListener('change', () => {
                    validarHorario(dia, inicio, fim);
                });
            });

            // Adiciona listener para o campo de quantidade de HAE
            document.getElementById('gti').addEventListener('change', function() {
                const novaQuantidade = parseInt(this.value) || 0;
                const totalAtual = calcularTotalHoras();

                if (totalAtual > novaQuantidade) {
                    alert(`Você já selecionou ${totalAtual}h de horários, mas reduziu a quantidade de HAE para ${novaQuantidade}h. Por favor, ajuste os horários.`);
                    // Limpa todos os horários selecionados
                    dias.forEach(dia => {
                        document.querySelector(`select[name="horario_inicio_${dia}"]`).value = '';
                        document.querySelector(`select[name="horario_final_${dia}"]`).value = '';
                    });
                    totalHorasSelecionadas = 0;
                }
            });
        }

        // Função para carregar os horários das aulas do professor
        async function carregarHorariosAulas() {
            try {
                const response = await fetch('get_horarios_aulas.php');
                const horariosAulas = await response.json();
                return horariosAulas;
            } catch (error) {
                console.error('Erro ao carregar horários das aulas:', error);
                return [];
            }
        }

        // Função para validar conflitos com horários de aula
        async function validarConflitosAulas() {
            const horariosAulas = await carregarHorariosAulas();
            const dias = ['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];

            dias.forEach(dia => {
                const inicio = document.querySelector(`select[name="horario_inicio_${dia}"]`);
                const fim = document.querySelector(`select[name="horario_final_${dia}"]`);
                const errorDiv = document.getElementById(`horario-error-${dia}`);

                const validarConflito = () => {
                    if (inicio.value && fim.value) {
                        const inicioHora = parseInt(inicio.value.split(':')[0]);
                        const fimHora = parseInt(fim.value.split(':')[0]);
                        
                        // Verifica conflito com horários de aula
                        const conflito = horariosAulas.some(aula => {
                            if (aula.dia_semana.toLowerCase() === dia) {
                                const aulaInicio = parseInt(aula.hora_inicio.split(':')[0]);
                                const aulaFim = parseInt(aula.hora_fim.split(':')[0]);
                                return (inicioHora < aulaFim && fimHora > aulaInicio);
                            }
                            return false;
                        });

                        if (conflito) {
                            errorDiv.textContent = 'Conflito com horário de aula!';
                            errorDiv.style.display = 'block';
                            inicio.value = '';
                            fim.value = '';
                        } else {
                            errorDiv.style.display = 'none';
                        }
                    }
                };

                inicio.addEventListener('change', validarConflito);
                fim.addEventListener('change', validarConflito);
            });
        }

        // Inicializa as validações quando o documento estiver carregado
        document.addEventListener('DOMContentLoaded', function() {
            validarHorariosComHAE();
            validarConflitosAulas();
        });
    </script>

    <?php
    // Modifica a geração dos horários para mostrar de 1h em 1h
    function gerarOpcoesHorario() {
        $options = '<option value="">Selecione</option>';
        for ($hora = 7; $hora <= 22; $hora++) {
            $time = sprintf("%02d:00", $hora);
            $options .= "<option value='$time'>$time</option>";
        }
        return $options;
    }
    ?>
</body>

</html>