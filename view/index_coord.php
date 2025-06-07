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
    
    // Busca o edital mais recente
    $queryEdital = "SELECT id_edital FROM tb_Editais ORDER BY id_edital DESC LIMIT 1";
    $stmtEdital = $conn->query($queryEdital);
    $edital = $stmtEdital->fetch();
    $idEdital = $edital ? $edital['id_edital'] : null;

    // Busca as datas do cronograma
    if ($idEdital) {
        $queryCronograma = "SELECT tipo_evento, data_inicio, data_fim FROM tb_cronograma WHERE id_edital = :id_edital";
        $stmtCronograma = $conn->prepare($queryCronograma);
        $stmtCronograma->execute(['id_edital' => $idEdital]);
        $cronograma = $stmtCronograma->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    error_log("Erro ao buscar cronograma: " . $e->getMessage());
}

function formatarData($data) {
    return $data ? date('d/m/Y', strtotime($data)) : '';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../estilos/style.css">
  <link rel="icon" type="image/png" href="../imagens/logo-horus.png">
  <title>Painel do Coordenador</title>
  <style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal {
        background: #fff;
        padding: 30px;
        border-radius: 8px;
        width: 400px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .modal h3 {
        margin-top: 0;
        color: #2c3e50;
        margin-bottom: 20px;
    }

    .modal-form-group {
        margin-bottom: 15px;
    }

    .modal-form-group label {
        display: block;
        margin-bottom: 5px;
        color: #2c3e50;
        font-weight: 600;
    }

    .modal-form-group input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }

    .modal-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }

    .modal-btn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .modal-btn-save {
        background-color: #28a745;
        color: white;
    }

    .modal-btn-cancel {
        background-color: #6c757d;
        color: white;
    }

    .modal-btn:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }

    .data-cell {
        min-width: 200px;
    }

    .periodo-datas {
        display: block;
        font-size: 14px;
        color: #2c3e50;
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
          <a href="https://fatecitapira.cps.sp.gov.br/" target="_blank"><img src="../imagens/logo-fatec_itapira.png"></a>
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
    <a class="inicio" href="index_coord.php">
      <img src="../imagens/home.png" alt="Início"> <span>Início</span>
    </a>
    <a href="aprovacao.php" id="linkAprovacao">
      <img src="../imagens/inscricoes.png" alt="Inscrições"> <span>Inscrições</span>
    </a>
    <a href="editais.php">
      <img src="../imagens/aprovacao.png" alt="Editais"> <span>Editais</span>
    </a>
    <a href="relatorio_coord.php">
      <img src="../imagens/relat.png" alt="Relatórios"> <span>Relatórios</span>
    </a>
    <a href="../login.php">
      <img src="../imagens/logout.png" alt="Logout"> <span>Logout</span>
    </a>
  </nav>

  <main>
    <h1>Painel do Coordenador</h1>
    <table class="tbls">
      <caption><br></caption>
      <thead>
        <tr>
          <th class="cece" scope="col" colspan="3">Cronograma</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Divulgação do Edital</td>
          <td class="data-cell">
            <?php 
            $evento = array_filter($cronograma ?? [], function($item) {
                return $item['tipo_evento'] === 'divulgacao_edital';
            });
            $evento = reset($evento);
            if ($evento): ?>
                <span class="periodo-datas">
                    <?php echo formatarData($evento['data_inicio']); ?> - 
                    <?php echo formatarData($evento['data_fim']); ?>
                </span>
            <?php endif; ?>
          </td>
          <td class="destaque">
            <img class="img-edit" src="../imagens/editar.png" 
                 onclick="editarDataCronograma('divulgacao_edital', '<?php echo $evento['data_inicio'] ?? ''; ?>', '<?php echo $evento['data_fim'] ?? ''; ?>')">
          </td>
        </tr>
        <tr>
          <td>Inscrições HAE Abertas</td>
          <td class="data-cell">
            <?php 
            $evento = array_filter($cronograma ?? [], function($item) {
                return $item['tipo_evento'] === 'inscricoes_abertas';
            });
            $evento = reset($evento);
            if ($evento): ?>
                <span class="periodo-datas">
                    <?php echo formatarData($evento['data_inicio']); ?> - 
                    <?php echo formatarData($evento['data_fim']); ?>
                </span>
            <?php endif; ?>
          </td>
          <td class="destaque">
            <img class="img-edit" src="../imagens/editar.png" 
                 onclick="editarDataCronograma('inscricoes_abertas', '<?php echo $evento['data_inicio'] ?? ''; ?>', '<?php echo $evento['data_fim'] ?? ''; ?>')">
          </td>
        </tr>
        <tr>
          <td>Aprovações HAE</td>
          <td class="data-cell">
            <?php 
            $evento = array_filter($cronograma ?? [], function($item) {
                return $item['tipo_evento'] === 'aprovacoes';
            });
            $evento = reset($evento);
            if ($evento): ?>
                <span class="periodo-datas">
                    <?php echo formatarData($evento['data_inicio']); ?> - 
                    <?php echo formatarData($evento['data_fim']); ?>
                </span>
            <?php endif; ?>
          </td>
          <td class="destaque">
            <img class="img-edit" src="../imagens/editar.png" 
                 onclick="editarDataCronograma('aprovacoes', '<?php echo $evento['data_inicio'] ?? ''; ?>', '<?php echo $evento['data_fim'] ?? ''; ?>')">
          </td>
        </tr>
        <tr>
          <td>Divulgação Lista de Aprovados</td>
          <td class="data-cell">
            <?php 
            $evento = array_filter($cronograma ?? [], function($item) {
                return $item['tipo_evento'] === 'lista_aprovados';
            });
            $evento = reset($evento);
            if ($evento): ?>
                <span class="periodo-datas">
                    <?php echo formatarData($evento['data_inicio']); ?> - 
                    <?php echo formatarData($evento['data_fim']); ?>
                </span>
            <?php endif; ?>
          </td>
          <td class="destaque">
            <img class="img-edit" src="../imagens/editar.png" 
                 onclick="editarDataCronograma('lista_aprovados', '<?php echo $evento['data_inicio'] ?? ''; ?>', '<?php echo $evento['data_fim'] ?? ''; ?>')">
          </td>
        </tr>
        <tr>
          <td>Entrega de Relatórios HAE</td>
          <td class="data-cell">
            <?php 
            $evento = array_filter($cronograma ?? [], function($item) {
                return $item['tipo_evento'] === 'entrega_relatorios';
            });
            $evento = reset($evento);
            if ($evento): ?>
                <span class="periodo-datas">
                    <?php echo formatarData($evento['data_inicio']); ?> - 
                    <?php echo formatarData($evento['data_fim']); ?>
                </span>
            <?php endif; ?>
          </td>
          <td class="destaque">
            <img class="img-edit" src="../imagens/editar.png" 
                 onclick="editarDataCronograma('entrega_relatorios', '<?php echo $evento['data_inicio'] ?? ''; ?>', '<?php echo $evento['data_fim'] ?? ''; ?>')">
          </td>
        </tr>
        <tr>
          <td>Aprovação de Relatórios HAE</td>
          <td class="data-cell">
            <?php 
            $evento = array_filter($cronograma ?? [], function($item) {
                return $item['tipo_evento'] === 'aprovacao_relatorios';
            });
            $evento = reset($evento);
            if ($evento): ?>
                <span class="periodo-datas">
                    <?php echo formatarData($evento['data_inicio']); ?> - 
                    <?php echo formatarData($evento['data_fim']); ?>
                </span>
            <?php endif; ?>
          </td>
          <td class="destaque">
            <img class="img-edit" src="../imagens/editar.png" 
                 onclick="editarDataCronograma('aprovacao_relatorios', '<?php echo $evento['data_inicio'] ?? ''; ?>', '<?php echo $evento['data_fim'] ?? ''; ?>')">
          </td>
        </tr>
      </tbody>
    </table>
  </main>

  <!-- Modal para edição de datas -->
  <div class="modal-overlay" id="modalDatas">
    <div class="modal">
        <h3>Editar Período</h3>
        <form id="formDatas" onsubmit="salvarDatas(event)">
            <input type="hidden" id="tipoEvento" name="tipo_evento">
            <div class="modal-form-group">
                <label for="dataInicio">Data Início:</label>
                <input type="date" id="dataInicio" name="data_inicio" required>
            </div>
            <div class="modal-form-group">
                <label for="dataFim">Data Fim:</label>
                <input type="date" id="dataFim" name="data_fim" required>
            </div>
            <div class="modal-buttons">
                <button type="button" class="modal-btn modal-btn-cancel" onclick="fecharModal()">Cancelar</button>
                <button type="submit" class="modal-btn modal-btn-save">Salvar</button>
            </div>
        </form>
    </div>
  </div>

  <!-- Scripts -->
  <script>
    function editarDataCronograma(tipoEvento, dataInicio, dataFim) {
        document.getElementById('tipoEvento').value = tipoEvento;
        document.getElementById('dataInicio').value = dataInicio;
        document.getElementById('dataFim').value = dataFim;
        document.getElementById('modalDatas').style.display = 'flex';
    }

    function fecharModal() {
        document.getElementById('modalDatas').style.display = 'none';
    }

    async function salvarDatas(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const dados = {
            tipo_evento: formData.get('tipo_evento'),
            data_inicio: formData.get('data_inicio'),
            data_fim: formData.get('data_fim')
        };

        try {
            const response = await fetch('../controller/salvar_cronograma.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(dados)
            });

            if (response.ok) {
                fecharModal();
                window.location.reload();
            } else {
                const data = await response.json();
                alert(data.erro || 'Erro ao salvar as datas. Por favor, tente novamente.');
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Erro ao salvar as datas. Por favor, tente novamente.');
        }
    }

    // Fecha o modal ao clicar fora dele
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('modalDatas');
        if (event.target === modal) {
            fecharModal();
        }
    });

    // Fecha o modal ao pressionar ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            fecharModal();
        }
    });
  </script>
  <script src="../js/script.js" defer></script>
</body>

</html>