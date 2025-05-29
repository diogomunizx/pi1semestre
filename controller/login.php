<?php
// View/login.php

// Inicia ou resume uma sessão existente. Essencial para que, após um login bem-sucedido,
// possamos armazenar as informações do usuário (como ID, nome e tipo) na sessão
// e também para definir mensagens de erro em caso de falha no login.
session_start();

// Inclui o arquivo 'Usuario.php' do diretório 'model'.
// Este arquivo contém a definição da classe 'Usuario', que tem o método 'login'
// responsável por verificar as credenciais no banco de dados.
// NOTA: No arquivo 'cadastro.php' você usou '../Model/Usuario.php' (com 'M' maiúsculo).
// É uma boa prática manter a consistência no nome dos diretórios (case-sensitive em alguns sistemas).
// Vamos assumir que 'model' é o nome correto do diretório aqui.
include '../model/Usuario.php';

// Verifica se o script foi acessado através de uma requisição HTTP POST.
// Isso garante que o código de processamento de login só seja executado
// quando o formulário de login (do index.php) for enviado.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Coleta o email enviado pelo formulário de login.
    // $_POST['email'] corresponde ao valor do campo <input name="email"> no formulário.
    $usuario = $_POST['usuario'];
    // Coleta a senha enviada pelo formulário de login.
    // $_POST['senha'] corresponde ao valor do campo <input name="senha"> no formulário.
    $senha = $_POST['senha'];

    // NOTA DIDÁTICA SOBRE VALIDAÇÃO E SANITIZAÇÃO:
    // Assim como no cadastro, aqui também seria o local para validar os dados recebidos.
    // Por exemplo, verificar se o e-mail tem um formato válido e se a senha não está vazia.
    // A sanitização para prevenir XSS ao exibir dados do usuário (como o nome)
    // deve ser feita no momento da exibição, não necessariamente aqui na entrada.
    // Para SQL Injection, a classe Usuario já deve estar usando Prepared Statements.

    // Cria uma nova instância (objeto) da classe 'Usuario'.
    // Isso nos dá acesso aos métodos definidos na classe, como o método 'login'.
    $usuario = new Usuario();

    // Chama o método 'login' do objeto '$usuario', passando o email e a senha coletados.
    // Espera-se que este método consulte o banco de dados.
    // Se as credenciais forem válidas e o usuário existir, o método deve retornar
    // um array com os dados do usuário (ex: id, nome, tipo).
    // Se as credenciais forem inválidas, deve retornar 'false' ou 'null'.
    // LEMBRETE CRÍTICO: O método `login` na classe `Usuario` precisa ser ajustado
    // para usar `password_verify()` se as senhas estiverem armazenadas com `password_hash()`.
    // A lógica atual no `Usuario.php` (comparando senhas em texto plano) é insegura.
    $dados = $usuario->login($usuario, $senha);

    // Verifica se o método 'login' retornou dados válidos (ou seja, não retornou 'false' ou 'null').
    // Se '$dados' for 'true' (contiver um array com dados do usuário), o login foi bem-sucedido.
    if ($dados) {
        // Login bem-sucedido!
        // Armazena informações importantes do usuário na sessão.
        // Esses dados estarão disponíveis em outras páginas da aplicação enquanto a sessão estiver ativa.

        // Armazena o 'id' do usuário. Útil para identificar o usuário em outras operações.
        $_SESSION['id_Docente'] = $dados['id_Docente'];
        // Armazena o 'nome' do usuário. Pode ser usado para personalizar a interface (ex: "Olá, [Nome]!").
        $_SESSION['Nome'] = $dados['Nome'];
        // Armazena o 'tipo' do usuário (ex: 'administrador', 'professor', 'aluno').
        // Isso será usado para controlar o acesso e redirecionar para o painel correto.
        $_SESSION['funcao'] = $dados['funcao'];

        // Redireciona o usuário para o dashboard apropriado com base no seu 'tipo'.
        // Este é o mesmo bloco de lógica de redirecionamento que vimos no 'index.php',
        // mas aqui ele é executado após um login bem-sucedido.
        if ($dados['funcao'] == 'coordenador') {
            header("Location: ../view/index_coord.html");
        } elseif ($dados['funcao'] == 'professor') {
            header("Location: ../view/index_prof.html");
        } elseif ($dados['tipo'] == 'aluno') {
            header("Location: ../view/dashboard_aluno.php");
        }
        // 'exit' é crucial após um 'header("Location: ...")' para garantir que
        // o script pare a execução e o redirecionamento ocorra imediatamente.
        exit;
    } else {
        // Login falhou (o método 'login' retornou 'false' ou 'null').
        // Isso significa que o email não foi encontrado ou a senha estava incorreta
        // (considerando a lógica correta com `password_verify`).

        // Armazena uma mensagem de erro na sessão. Esta mensagem será exibida
        // na página 'index.php' (para onde o usuário será redirecionado).
        $_SESSION['login_error'] = "Email ou senha incorretos!";

        // Redireciona o usuário de volta para a página de login ('index.php').
        header("Location: ../login.html");
        exit; // Termina a execução do script.
    }
} else {
    // Se o script foi acessado por um método diferente de POST (ex: GET, acesso direto pela URL),
    // não há dados de formulário para processar.
    // Nesse caso, simplesmente redireciona o usuário para a página inicial ('index.php').
    // Isso evita que o usuário acesse 'login.php' diretamente sem submeter o formulário.
    header("Location: ../index.html");
    exit; // Termina a execução do script.
}
