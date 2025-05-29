<?php
// Model/Usuario.php

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
        // O bloco 'try...catch' é usado para tratamento de exceções (erros).
        // Se ocorrer um erro dentro do bloco 'try', o código no bloco 'catch' correspondente será executado.
        // Isso é crucial para lidar com falhas de conexão com o banco de dados de forma elegante.
        try {
            // 'new PDO(...)' cria uma instância da classe PDO, que representa uma conexão com um banco de dados.
            // O primeiro argumento é o DSN (Data Source Name), que especifica o tipo de banco, host e nome do banco.
            //   "mysql:host=localhost;dbname=sisescolar"
            //     - "mysql": Indica que estamos conectando a um banco de dados MySQL.
            //     - "host=localhost": O servidor do banco de dados está na máquina local.
            //     - "dbname=sisescolar": O nome do banco de dados ao qual queremos nos conectar.
            // O segundo argumento ("root") é o nome de usuário para o banco de dados.
            // O terceiro argumento ("") é a senha para o banco de dados.
            //   NOTA PARA OS ALUNOS: Em produção, NUNCA use usuário 'root' sem senha ou com senhas fracas.
            //   Crie usuários específicos com permissões limitadas para cada aplicação.
            //   As credenciais do banco também não devem estar hardcoded diretamente no código,
            //   mas sim em arquivos de configuração fora do versionamento (ex: usando variáveis de ambiente).
            $this->pdo = new PDO("mysql:host=sql206.infinityfree.com;dbname=if0_39097196_horus", "if0_39097196", "V9ymqZGOD3");

            // '$this->pdo->setAttribute(...)' configura atributos da conexão PDO.
            // 'PDO::ATTR_ERRMODE' define como os erros do PDO serão reportados.
            // 'PDO::ERRMODE_EXCEPTION' faz com que o PDO lance exceções (objetos PDOException) quando um erro ocorre.
            // Isso permite que usemos blocos 'try...catch' para lidar com esses erros de forma estruturada.
            // Outras opções seriam PDO::ERRMODE_SILENT (apenas códigos de erro) ou PDO::ERRMODE_WARNING (emite E_WARNING).
            // ERRMODE_EXCEPTION é geralmente a melhor escolha para um desenvolvimento robusto.
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Se uma 'PDOException' ocorrer durante a tentativa de conexão (bloco 'try'),
            // o código dentro deste bloco 'catch' será executado.
            // '$e' é um objeto que contém informações sobre a exceção (o erro).
            // 'die(...)' exibe uma mensagem e termina a execução do script imediatamente.
            // Em uma aplicação mais sofisticada, você poderia logar o erro em um arquivo em vez de
            // mostrá-lo diretamente ao usuário, e exibir uma mensagem mais amigável.
            die("Erro ao conectar ao banco de dados: " . $e->getMessage());
        }
    }

    // 'public function cadastrar(...)' define um método público chamado 'cadastrar'.
    // Métodos públicos podem ser chamados de fora da classe (ex: $usuario->cadastrar(...)).
    // Este método recebe nome, email, senha e tipo do usuário como argumentos.
    public function cadastrar($nome, $email, $senha, $tipo) {
        // !!! ALERTA DE SEGURANÇA IMPORTANTE PARA OS ALUNOS !!!
        // A senha está sendo recebida e será inserida no banco de dados EM TEXTO PLANO.
        // ISSO É UMA FALHA DE SEGURANÇA GRAVÍSSIMA!
        // Senhas NUNCA devem ser armazenadas dessa forma. Se o banco de dados for comprometido,
        // todas as senhas dos usuários estarão expostas.
        //
        // SOLUÇÃO: Usar `password_hash()` antes de inserir no banco.
        // Exemplo: $hashSenha = password_hash($senha, PASSWORD_DEFAULT);
        // E então armazenar $hashSenha no banco.

        // '$this->pdo->prepare(...)' prepara uma instrução SQL para execução.
        // Usar "prepared statements" (instruções preparadas) é a principal forma de prevenir
        // ataques de SQL Injection. Os placeholders (como :nome, :email) são usados para
        // indicar onde os valores reais serão inseridos de forma segura.
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (nome, email, senha, tipo) VALUES (:nome, :email, :senha, :tipo)");

        // '$stmt->bindParam(...)' vincula uma variável PHP a um placeholder na instrução SQL.
        // Quando a instrução for executada, o valor da variável PHP será usado no lugar do placeholder.
        // ':nome' (placeholder) é vinculado à variável $nome (parâmetro da função).
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':senha', $senha); // <-- Aqui a senha em texto plano está sendo vinculada.
        $stmt->bindParam(':tipo', $tipo);

        // '$stmt->execute()' executa a instrução preparada.
        // Para INSERT, UPDATE, DELETE, execute() retorna 'true' em caso de sucesso ou 'false' em caso de falha.
        return $stmt->execute();
    }

    // 'public function login(...)' define um método público para autenticar um usuário.
    // Recebe email e senha como argumentos.
    public function login($usuario, $senha) {
        // !!! ALERTA DE SEGURANÇA IMPORTANTE PARA OS ALUNOS !!!
        // A consulta está comparando a senha fornecida DIRETAMENTE com a senha armazenada no banco.
        // Isso só funcionaria (de forma insegura) se as senhas estivessem em texto plano no banco.
        //
        // SOLUÇÃO APÓS IMPLEMENTAR password_hash() no cadastro:
        // 1. Selecionar o usuário APENAS pelo email: "SELECT * FROM usuarios WHERE email = :email"
        // 2. Obter o hash da senha armazenada no banco para esse usuário.
        // 3. Usar `password_verify($senhaFornecida, $hashDoBanco)` para comparar a senha fornecida
        //    com o hash armazenado. password_verify() cuida de toda a complexidade da comparação de hashes.

        // Prepara a instrução SQL para selecionar um usuário com base no email E senha.
        // Embora use prepared statements (o que é bom para prevenir SQL injection na parte do email),
        // a lógica de comparação da senha é falha em termos de segurança.
        $stmt = $this->pdo->prepare("SELECT * FROM tb_Usuarios WHERE usuario = :usuario AND senha = :senha");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':senha', $senha); // <-- Comparando senha em texto plano.

        // Executa a consulta.
        $stmt->execute();

        // '$stmt->fetch(...)' busca a próxima linha do conjunto de resultados da consulta.
        // 'PDO::FETCH_ASSOC' instrui o PDO a retornar a linha como um array associativo,
        // onde as chaves do array são os nomes das colunas da tabela 'usuarios'.
        // Se nenhum usuário for encontrado com o email e senha fornecidos (da forma insegura atual),
        // fetch() retornará 'false'. Caso contrário, retorna o array com os dados do usuário.
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}