<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id_Docente'])) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../estilos/style.css">
    <link rel="stylesheet" href="../estilos/perfil.css">
    <link rel="icon" type="image/png" href="../imagens/logo-horus.png">
    <title>HORUS - Cadastro</title>
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
                <a href="https://fatecitapira.cps.sp.gov.br/" target="_blank"><img src="../imagens/logo-fatec_itapira.png" alt="Logo Fatec"></a>
            </div>
            <div class="cps">
                <a href="https://www.cps.sp.gov.br/" target="_blank"><img src="../imagens/logo-cps.png" alt="Logo CPS"></a>
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
    <?php if (strtolower($_SESSION['funcao']) === 'professor'): ?>
    <a class="inicio" href="index_prof.php">
        <img src="../imagens/home.png" alt="Início"> <span>Início</span>
    </a>
    <a href="inscricao.php" id="linkInscricao">
        <img src="../imagens/inscricao.png" alt="Inscrição"> <span>Inscrição</span>
    </a>
    <a href="relatorio_prof.php">
        <img src="../imagens/relat.png" alt="Relatório"> <span>Relatório</span>
    </a>
    <?php else: ?>
    <a class="inicio" href="index_coord.php">
        <img src="../imagens/home.png" alt="Início"> <span>Início</span>
    </a>
    <a href="aprovacao.php" id="linkAprovacao">
        <img src="../imagens/inscricoes.png" alt="Inscrições"> <span>Inscrições</span>
    </a>
    <a href="relatorio_coord.php">
        <img src="../imagens/relat.png" alt="Relatórios"> <span>Relatórios</span>
    </a>
    <?php endif; ?>
    <a href="../login.php">
        <img src="../imagens/logout.png" alt="Logout"> <span>Logout</span>
    </a>
</nav>

<main>
    <div class="form-container form-container--cadastro">
        <form class="form-ajuste">
            <div class="header-Cadastro">
                <h1>Meu Perfil</h1>
            </div>

            <div class="input-grupoCadastro">
                <label for="professor">Nome</label>
                <input type="text" id="professor" disabled >
            </div>

            <div class="input-grupoCadastro">
                <label for="matricula">Matrícula</label>
                <input type="text" id="matricula" disabled >
            </div>

            <div class="input-grupoCadastro">
                <label for="email">E-mail</label>
                <input type="email" id="email" disabled >
            </div>

            <div class="input-grupoCadastro">
                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" disabled >
            </div>

             <div class="input-grupoCadastro" id="campo-senha" style="display:none;">
                <label for="senhaAtualCadastro">Senha Atual</label>
                <input type="password" id="senhaAtualCadastro" required>
            </div>    

            <div class="btns-Alterar">
                <a href="#" onclick="liberarCampoCadastro()">Alterar Cadastro</a>
                <a href="#" onclick="liberarFormAlterarSenha()">Alterar Senha</a>
            </div>
            <button type="button" class="btn-salvarAlterarCadastro" style="display: none;" onclick="salvarAlteracao()">Salvar Alterações</button>
            <button type="button" class="btn-cancelarAlterarCadastro" style="display: none;" onclick="cancelarAlteracaoCadastro()">Cancelar</button>
       </form>

        <form class="form-AlterarSenha" style="display: none;">
            <div class="header-AlterarSenha">
                <h1>Alterar Senha</h1>
            </div>

            <div class="input-grupoCadastro">
                <label for="password">Senha Atual</label>
                <input type="password" id="password" required>
            </div>

            <div class="input-grupoCadastro">
                <label for="novaPassword">Nova Senha</label>
                <input type="password" id="novaPassword" required onkeyup="validarSenha(this.value)">
                <div class="senha-requisitos">
                    <div class="senha-requisito" id="req-length">Mínimo de 8 caracteres</div>
                    <div class="senha-requisito" id="req-uppercase">Pelo menos uma letra maiúscula</div>
                    <div class="senha-requisito" id="req-lowercase">Pelo menos uma letra minúscula</div>
                    <div class="senha-requisito" id="req-number">Pelo menos um número</div>
                    <div class="senha-requisito" id="req-special">Pelo menos um caractere especial</div>
                </div>
            </div>

            <div class="input-grupoCadastro">
                <label for="confirmarPassword">Confirmar Nova Senha</label>
                <input type="password" id="confirmarPassword" required onkeyup="validarConfirmacaoSenha()">
                <div class="senha-feedback" id="senha-feedback"></div>
            </div>

            <button type="button" class="btn-salvarAlterarSenha" onclick="salvarAlteracaoSenha()">Salvar Nova Senha</button>
            <button type="button" class="btn-cancelarAlterarSenha" onclick="cancelarAlteracaoSenha()">Cancelar</button>
        </form>
    </div>
</main>

<script src="../js/script.js" defer></script>

<script>document.addEventListener('DOMContentLoaded', () => {
fetch('../controller/usuario_perfil.php')
    .then(response => {
        if (!response.ok) throw new Error('Erro ao buscar dados');
        return response.json();
    })
    .then(data => {
        console.log(data); // Veja todos os dados retornados aqui!
        document.getElementById('professor').value = data.Nome;
        document.getElementById('email').value = data.email;
        document.getElementById('telefone').value = data.telefone;
        document.getElementById('matricula').value = data.id_Docente;
    })
    .catch(error => console.error(error));
});</script>


</body>
</html> 