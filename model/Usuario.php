<?php
// Model/Usuario.php

require_once __DIR__ . '/Database.php';

// A palavra-chave 'class' define uma classe. Uma classe é um modelo ou "planta baixa"
// para criar objetos. Objetos são instâncias de classes.
// 'Usuario' é o nome da nossa classe. Por convenção, nomes de classes geralmente começam com letra maiúscula.
class Usuario {
    // 'private' é um modificador de visibilidade. Propriedades (variáveis) e métodos (funções)
    // declarados como 'private' só podem ser acessados de dentro da própria classe 'Usuario'.
    // Isso ajuda no encapsulamento, protegendo os detalhes internos da classe.
    // '$pdo' vai armazenar o objeto de conexão com o banco de dados (PDO - PHP Data Objects).
    private $pdo;

    // '__construct' é um método mágico especial chamado "construtor".
    // Ele é executado automaticamente sempre que um novo objeto da classe 'Usuario' é criado
    // usando a palavra-chave 'new' (ex: $meuUsuario = new Usuario();).
    // É comumente usado para inicializar o estado do objeto, como estabelecer a conexão com o banco.
    public function __construct() {
        try {
            $this->pdo = Database::getInstance()->getConnection();
        } catch (Exception $e) {
            throw new Exception("Erro ao inicializar usuário: " . $e->getMessage());
        }
    }

    // 'public function cadastrar(...)' define um método público chamado 'cadastrar'.
    // Métodos públicos podem ser chamados de fora da classe (ex: $usuario->cadastrar(...)).
    // Este método recebe nome, email, senha e tipo do usuário como argumentos.
    public function cadastrar($nome, $email, $senha, $tipo) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO tb_Usuario (nome, email, senha, funcao) VALUES (:nome, :email, :senha, :tipo)");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senha);
            $stmt->bindParam(':tipo', $tipo);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro no cadastro: " . $e->getMessage());
            throw new Exception("Erro ao cadastrar usuário. Por favor, tente novamente mais tarde.");
        }
    }

    // 'public function login(...)' define um método público para autenticar um usuário.
    // Recebe email e senha como argumentos.
    public function login($usuario, $senha) {
        try {
            error_log("Tentativa de login para usuário: " . $usuario);
            
            $stmt = $this->pdo->prepare("SELECT * FROM tb_Usuario WHERE usuario = :usuario LIMIT 1");
            $stmt->bindParam(':usuario', $usuario);
            $stmt->execute();
            
            $dados = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($dados) {
                error_log("Usuário encontrado: " . json_encode($dados));
                
                if ($senha === $dados['senha']) {
                    error_log("Senha correta. Função do usuário: " . $dados['funcao']);
                    
                    // Garante que a função está exatamente como no banco de dados
                    $funcao = $dados['funcao'];
                    error_log("Função do usuário antes do retorno: " . $funcao);
                    
                    return [
                        'id_usuario' => $dados['id_Docente'],
                        'nome' => $dados['Nome'],
                        'funcao' => $funcao // Retorna a função exatamente como está no banco
                    ];
                } else {
                    error_log("Senha incorreta para o usuário: " . $usuario);
                }
            } else {
                error_log("Usuário não encontrado: " . $usuario);
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Erro no login: " . $e->getMessage());
            throw new Exception("Erro ao realizar login. Por favor, tente novamente mais tarde.");
        }
    }
}