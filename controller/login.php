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
require_once '../model/Usuario.php';

// Define a URL base do site
define('BASE_URL', 'https://horusdsm.lovestoblog.com');

// Verifica se o script foi acessado através de uma requisição HTTP POST.
// Isso garante que o código de processamento de login só seja executado
// quando o formulário de login (do index.php) for enviado.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_login = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($usuario_login) || empty($senha)) {
        $_SESSION['login_error'] = "Por favor, preencha todos os campos!";
        header("Location: ../login.php");
        exit;
    }

    try {
        $usuario_obj = new Usuario();
        $dados = $usuario_obj->login($usuario_login, $senha);

        if ($dados) {
            $_SESSION['id_Docente'] = $dados['id_usuario'];
            $_SESSION['Nome'] = $dados['nome'];
            $_SESSION['funcao'] = $dados['funcao'];

            // Converte para minúsculo para comparação
            $funcao = strtolower($dados['funcao']);
            error_log("Função do usuário após login: " . $funcao); // Log para debug

            // Verifica a função e redireciona
            if ($funcao === 'coordenador') {
                header("Location: ../view/index_coord.php");
            } 
            elseif ($funcao === 'professor') {
                header("Location: ../view/index_prof.php");
            }
            else {
                $_SESSION['login_error'] = "Tipo de usuário não reconhecido: " . $dados['funcao'];
                header("Location: ../login.php");
            }
            exit;
        } else {
            $_SESSION['login_error'] = "Usuário ou senha incorretos!";
            header("Location: ../login.php");
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
        $_SESSION['login_error'] = "Erro ao realizar login. Por favor, tente novamente mais tarde.";
        header("Location: ../login.php");
    }
    exit;
} else {
    // Se o script foi acessado por um método diferente de POST (ex: GET, acesso direto pela URL),
    // não há dados de formulário para processar.
    // Nesse caso, simplesmente redireciona o usuário para a página inicial ('index.php').
    // Isso evita que o usuário acesse 'login.php' diretamente sem submeter o formulário.
    header("Location: ../login.php");
    exit; // Termina a execução do script.
}
