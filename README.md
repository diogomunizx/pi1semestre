

Documento de Especificação de Requisitos de Software (ERS)
Projeto: HAE - Horas de Atividade Específica
Disciplina: Engenharia de Software II
Curso: Desenvolvimento de Software Multiplataforma
Grupo: Acyr Neto, Diogo Muniz, Izac Gomes, Julia Barbosa e Luan Trani
1. Introdução

1.1. Objetivo do Documento

O presente documento tem como objetivo especificar de forma estruturada e detalhada os requisitos do Sistema de Controle de HAE — uma aplicação web desenvolvida pela software house Horus, como Projeto Integrador II do curso de Desenvolvimento de Software Multiplataforma da Fatec Itapira. O sistema visa informatizar o processo de submissão, avaliação e acompanhamento das Horas de Atividade Específica (HAE), atualmente realizado de forma manual através de e-mails, planilhas e formulários. Através da modelagem de requisitos, casos de uso e protótipos, o documento fornece uma base sólida para o desenvolvimento técnico da solução, promovendo clareza na comunicação entre equipe de desenvolvimento, professores e gestores acadêmicos.
2. Visão Geral do Sistema

2.1. Visão do Produto

O produto desenvolvido pela HORUS é um sistema web para gerenciar as Horas de Atividade Específica (HAE) dos docentes da Fatec Itapira. Ele oferece uma plataforma centralizada onde os professores podem submeter seus projetos, que são posteriormente avaliados e aprovados ou não pelos coordenadores. Além disso, o sistema possibilita o envio de relatórios referentes aos projetos aprovados naquele semestre, permitindo que os coordenadores realizem análises, solicitação de correção e aprovação do relatório. O sistema visa otimizar os processos, garantir maior eficiência e proporcionar transparência na gestão das HAEs, configurando-se como uma solução personalizada para atender às necessidades específicas da instituição.

2.2. Justificativa

Atualmente, o controle das HAEs ocorre de forma manual, por meio de e-mails e documentos físicos, resultando em processos lentos, suscetíveis a erros e com pouca transparência. O sistema automatiza e centraliza as etapas de submissão, avaliação e aprovação, otimizando o fluxo de trabalho e melhorando a experiência dos envolvidos.

2.3. Objetivos e Benefícios

Objetivos:
- Facilitar a submissão de projetos de HAE pelos docentes.
- Garantir maior controle e transparência nas avaliações.
- Consolidar os dados para uma gestão eficiente.

Benefícios:
- Otimização do tempo dos docentes e coordenadores.
- Acompanhamento claro das etapas do processo.
- Redução de erros na contagem e controle das horas de atividades.
3. Requisitos do Sistema

Requisitos são as necessidades ou condições que um sistema deve atender para satisfazer as expectativas dos usuários e stakeholders. Eles são fundamentais para guiar o desenvolvimento, validar entregas e garantir que o produto atenda aos objetivos.

3.1.1 Requisitos Funcionais

Login com autenticação integrada ao sistema SIGA
ID
RF01
DESCRIÇÃO
O sistema deve permitir que os docentes realizem login utilizando suas credenciais institucionais, com os dados de autenticação sendo importados do sistema SIGA.
JUSTIFICATIVA
Facilitar o acesso ao sistema, garantindo integração com os dados já existentes e evitando duplicidade de informações.
ORIGEM
Integração institucional (SIGA + HORUS).
DEPENDÊNCIAS
Conexão e sincronização com o SIGA para validação dos dados de login.
CRITÉRIO DE ACEITAÇÃO
O usuário consegue acessar o sistema com suas credenciais do SIGA e visualizar seu perfil com sucesso.
PRIORIDADE
Alta.
OBSERVAÇÕES
Não será permitido cadastro manual de docentes. Toda autenticação será feita via SIGA.


Submissão de projetos com dados obrigatórios como título, cronograma, metas e justificativas

ID
RF02
DESCRIÇÃO
O sistema deve permitir que os docentes submetam seus projetos de Hora Atividade Específica, especificando o tipo de HAE, título do projeto, metas, objetivos e cronograma.
JUSTIFICATIVA
Para que os projetos sejam avaliados pela coordenação conforme previsto no regulamento.
ORIGEM
Usuário final (Docente).
DEPENDÊNCIAS
Login e autenticação no sistema.
CRITÉRIO DE ACEITAÇÃO
O docente consegue submeter o projeto.
PRIORIDADE
Alta.
OBSERVAÇÕES
Formulário com campos obrigatórios e validação.


Avaliação dos projetos pelos coordenadores com parecer e justificativa obrigatória

ID
RF03
DESCRIÇÃO
O sistema deve permitir que coordenadores de curso acessem os projetos submetidos e realizem avaliações, com opção de deferir ou indeferir.
JUSTIFICATIVA
Garantir que os projetos sejam analisados conforme critérios institucionais.
ORIGEM
Usuário final (Coordenador).
DEPENDÊNCIAS
Submissão prévia de projetos pelos docentes.
CRITÉRIO DE ACEITAÇÃO
O coordenador consegue emitir parecer com justificativa e notificar o docente.
PRIORIDADE
Alta.
OBSERVAÇÕES
A justificativa deve ser obrigatoriamente preenchida pelo coordenador.


Submissão de relatórios finais

ID
RF04
DESCRIÇÃO
O sistema deve permitir o envio de relatórios no final da execução do projeto por parte dos docentes, contendo detalhes sobre o desenvolvimento.
JUSTIFICATIVA
Cumprimento das exigências do edital e acompanhamento institucional.
ORIGEM
Usuário final (Docente).
DEPENDÊNCIAS
Aprovação do projeto.
CRITÉRIO DE ACEITAÇÃO
O docente consegue enviar o relatório e recebe confirmação de envio.
PRIORIDADE
Alta.
OBSERVAÇÕES
Os campos dos relatórios devem ser obrigatoriamente preenchidos.


Avaliação de Relatórios Finais
ID
RF05
DESCRIÇÃO
O sistema deve permitir que coordenadores de curso acessem os relatórios referente aos projetos de HAE submetidos pelos docentes e realizem avaliações, com opção de aprovar ou solicitar correção.
JUSTIFICATIVA
Cumprimento das exigências do edital e acompanhamento institucional.
ORIGEM
Usuário final (Coordenador).
DEPENDÊNCIAS
Submissão do relatório.
CRITÉRIO DE ACEITAÇÃO
O coordenador consegue emitir parecer e salvar sua decisão com justificativa.
PRIORIDADE
Alta.
OBSERVAÇÕES
O registro da decisão deve ser armazenado com data e justificativa.


Upload do Arquivo de Edital
ID
RF06
DESCRIÇÃO
O sistema deve permitir que coordenadores de curso façam upload dos editais.
JUSTIFICATIVA
Divulgar as exigências do edital para que os professores se mantenham informados.
ORIGEM
Usuário final (Coordenador).
DEPENDÊNCIAS
Login e autenticação no sistema.
CRITÉRIO DE ACEITAÇÃO
O coordenador consegue fazer upload do edital.
PRIORIDADE
Média.
OBSERVAÇÕES




Download do Arquivo de Edital
ID
RF07
DESCRIÇÃO
O sistema deve permitir que os professores façam download do edital publicado pelo coordenador.
JUSTIFICATIVA
Fazer com que os professores tenham acesso às informações.
ORIGEM
Usuário final (Professor).
DEPENDÊNCIAS
Upload do edital.
CRITÉRIO DE ACEITAÇÃO
O professor consegue fazer download do documento.
PRIORIDADE
Média.
OBSERVAÇÕES




Cadastro do horário de trabalho do docente
ID
RF08
DESCRIÇÃO
O sistema deve permitir que docentes cadastrem suas horas de trabalho semanais, descrevendo o horário de trabalho de cada dia da semana e em qual unidade escolar do CPS.
JUSTIFICATIVA
Validar se o docente não trabalha mais de 8 horas por dia e se tem um intervalo de 11 horas de um dia para o outro.
ORIGEM
Usuário final (Professor).
DEPENDÊNCIAS
Login e autenticação no sistema.
CRITÉRIO DE ACEITAÇÃO
O docente consegue fazer o cadastro das suas horas de aula semanais com as validações.
PRIORIDADE
Baixa.
OBSERVAÇÕES
No formulário de inscrição de HAE o sistema não deve permitir que o docente preencha que deseja realizar o projeto num horário que ele já ministra aula.




3.1.2 Requisitos Não Funcionais

Requisitos Não Funcionais descrevem características e restrições do sistema relacionadas ao desempenho, segurança, usabilidade e outros aspectos de qualidade.

RNF01 – Disponibilidade 24/7.
RNF02 – Armazenamento em banco de dados relacional.
RNF03 – Tempo de resposta ≤ 5 segundos.
RNF04 – Compatibilidade com navegadores modernos.
RNF05 – Segurança de dados conforme boas práticas.

4. Modelagem

Modelagem é a atividade de representar visualmente e conceitualmente o funcionamento do sistema, através de diagramas que facilitam a compreensão e comunicação entre a equipe.

4.1 Diagrama de Casos de Uso (UML)

A modelagem de casos de uso descreve as interações entre os usuários (atores) e o sistema, destacando os principais fluxos de atividades.



4.2 Especificação dos Casos de Uso

Caso de Uso: Submissão de Projeto
Atores: Docente, Sistema
Fluxo de Eventos Principal:
1. O docente acessa sua conta.
2. O docente acessa “Inscrição” no menu lateral.
3. O docente clica em “Nova Inscrição”.
4. O sistema apresenta o formulário de submissão.
5. O docente preenche os dados do projeto e envia.
6. O sistema confirma a submissão.

Fluxo Alternativo: Formulários com campo obrigatório nulo
5.1. Se faltar alguma informação obrigatória, o sistema alerta o docente e impede a submissão.
Fluxo Alternativo: Horário de HAE conflita com horário de Aula
5.1. O docente preenche o horário da HAE num horário que ele tem aula.
5.2. O sistema apresenta erro e pede para selecionar outro horário.
 
Caso de Uso: Análise do Projeto
Atores: Coordenador, Sistema
Fluxo de Eventos Principal:
1. O coordenador acessa sua conta.
2. O coordenador acessa “Inscrições” no menu lateral.
3. O sistema apresenta as inscrições submetidas pelos professores, referente ao curso que o coordenador logado coordena.
4. O coordenador clica no projeto que ele deseja avaliar.
5. O sistema apresenta o formulário de análise.
6. O coordenador preenche a justificativa e aprova ou rejeita o projeto.
7. O sistema confirma a submissão.

Fluxo Alternativo: Não há nenhuma inscrição de HAE no curso do coordenador logado
3.1. O sistema apresenta que ainda não há inscrições para análise.

Fluxo Alternativo: Coordenador verifica a inscrição completa
5.1. O coordenador clica em “Ver Inscrição Completa”.
5.2. O sistema apresenta a inscrição completa preenchida pelo professor.
5.3 O coordenador clica em voltar.

Caso de Uso: Submissão de Relatório
Atores: Docente, Sistema
Fluxo de Eventos Principal:
1. O docente acessa sua conta.
2. O docente acessa “Relatórios” no menu lateral.
3. O sistema apresenta os projetos aprovados do professor logado.
4. O docente clica em “Enviar relatório” no projeto que ele deseja fazer a submissão.
5. O sistema apresenta o formulário para preencher os dados do relatório.
6. O docente preenche as informações e envia.
6. O sistema confirma a submissão.

Fluxo Alternativo: O professor não teve nenhum projeto aprovado para enviar relatório
3.1. O sistema apresenta que não há nenhum projeto para envio de relatório.

Fluxo Alternativo: O professor verifica a inscrição completa antes de enviar relatório
5.1. O docente clica em “Ver inscrição” no projeto que ele deseja analisar.
5.2. O sistema exibe a inscrição de HAE vinculada àquele projeto.
5.2. O coordenador clica em “voltar”.

Fluxo Alternativo: Campos obrigatórios do relatório estarem nulos
6.1. Se faltar alguma informação obrigatória, o sistema alerta o docente e impede a submissão.
Caso de Uso: Análise de Relatório
Atores: Coordenador, Sistema
Fluxo de Eventos Principal:
1. O coordenador acessa sua conta.
2. O coordenador acessa “Inscrições” no menu lateral.
3. O sistema apresenta os relatórios submetidos pelos professores, referente ao curso que o coordenador logado coordena.
4. O coordenador clica no relatório que ele deseja avaliar.
5. O sistema apresenta o formulário de análise.
6. O coordenador preenche a justificativa e aprova ou solicita correção.
7. O sistema confirma a submissão.

Fluxo Alternativo: Não há relatórios
3.1. O sistema informa que não há relatórios.

Fluxo Alternativo: Coordenador verifica o relatório completo
5.1. O coordenador clica em “Ver Relatório Completo”.
5.2. O sistema apresenta o relatório completo preenchido pelo professor.
5.3 O coordenador clica em “voltar”.

Fluxo Alternativo: Campos obrigatórios estão nulos
6.1. Se faltar alguma informação obrigatória, o sistema alerta o coordenador e impede a submissão.

Caso de Uso: Cadastro de Edital
Atores: Coordenador, Sistema
Fluxo de Eventos Principal:
1. O coordenador acessa sua conta.
2. O coordenador acessa “Editais” no menu lateral.
3. O sistema apresenta a tela inicial da página de editais.
4. O coordenador clica em “Novo Edital”.
5. O sistema apresenta o formulário de cadastro.
6. O coordenador preenche os dados e salva.
7. O sistema retorna na página inicial de editais.
8. O coordenador clica em “Escolher Arquivo”, anexa o documento e clica em “Upload”.
9. O sistema apresenta mensagem de sucesso.

Fluxo Alternativo: Campos obrigatórios nulos
6.1. Se faltar alguma informação obrigatória, o sistema alerta o coordenador e impede a submissão.

Fluxo Alternativo: Documento inválido
8.1. Coordenador anexa um documento inválido (que não é PDF).
8.1. O sistema apresenta erro e pede para enviar um arquivo de extensão PDF.

Caso de Uso: Consulta de Edital
Atores: Docente, Sistema
Fluxo de Eventos Principal:
1. O docente acessa sua conta.
2. O docente acessa “Editais” no menu lateral.
3. O sistema apresenta os editais cadastrados.
4. O docente clicar em “Download PDF”.
5. O download é concluído.

Fluxo Alternativo: Não há editais cadastrados
3.1 O sistema informa que não há editais cadastrados.

Fluxo Alternativo: Não há arquivo para download
3.1 O botão de download não é exibido.

Caso de Uso: Cadastro de Horas Aula
Atores: Docente, Sistema
Fluxo de Eventos Principal:
1. O docente acessa sua conta.
2. O docente clica no seu ícone de perfil e em “Minhas Aulas”.
3. O sistema apresenta a tela do Minhas Aulas.
4. O docente clicar em “Alterar Dados”.
5. O docente navega até o dia da semana que deseja adicionar o horário de trabalho dele.
6. O docente clica em “Adicionar horário” e preenche os campos.
7. O sistema salva os dados e retorna para a tela inicial do Minhas Aulas.

Fluxo Alternativo: Validação de Horas
6.1. O Docente digita um horário final menor que o inicial, digita um horário que ultrapassa às 22h30 ou um horário que não tenha um intervalo de 11 horas de um dia para o outro.
6.2. O sistema apresenta o erro e solicita correção.

4.3 Diagrama de Atividades


4.4 Modelagem de Classes

5. Protótipos das Telas

Inserir imagens que representam as principais telas do sistema: Tela de Login, Cadastro de Docente, Submissão de Projeto, Avaliação de Projeto, Painel do Coordenador, Painel do Diretor.
6. Considerações Finais
O desenvolvimento do sistema HORUS permitiu à equipe aplicar na prática os conceitos abordados na disciplina de Engenharia de Software II, especialmente no que tange à especificação e modelagem de requisitos. Ao automatizar um processo antes realizado manualmente, o sistema contribui para maior transparência, agilidade e segurança na concessão das HAEs.
Durante o desenvolvimento, identificamos a necessidade de alinhar rigorosamente as funcionalidades com as regras do edital vigente e as particularidades institucionais da Fatec Itapira. Como próximos passos, propomos:
Testes com usuários reais (docentes e coordenadores).


Ajustes visuais e de acessibilidade com base em feedback.
7. Anexos
7.1 Script do Banco de Dados

# Trabalho P1 primeiro Semestre
— Criando o banco (nome com numeração devido ao site de hospedagem gratuito que estamos usando)
CREATE DATABASE IF0_39097196_horus;
USE IF0_39097196_horus;




-- Tabela de Docentes
CREATE TABLE tb_Usuario (
  id_Docente INT PRIMARY KEY AUTO_INCREMENT,
  usuario VARCHAR(45) NOT NULL UNIQUE,
  Nome VARCHAR(80) NOT NULL,
  email VARCHAR(80) NOT NULL,
  telefone VARCHAR(17),
  senha VARCHAR(45) NOT NULL,
  funcao ENUM('professor', 'Coordenador', 'Prof/Coord') NOT NULL,
  matricula int(30) NOT NULL UNIQUE
);


-- Tabela de Unidades Fatec
CREATE TABLE tb_unidadeFatec (
  id_unidadeFatec INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  Nome_Fantasia VARCHAR(120) NOT NULL UNIQUE
);


-- Tabela de Editais
CREATE TABLE tb_Editais (
  id_edital INT PRIMARY KEY AUTO_INCREMENT,
  vigencia VARCHAR(7) NOT NULL DEFAULT 'MM/AAAA',
  dataInicioInscricao DATE NOT NULL,
  dataFimInscricao VARCHAR(45) NOT NULL,
  usuario_alteracao_id VARCHAR(45),
  edital_status ENUM('ABERTO', 'ENCERRADO'),
  Unidade_Fatec_idUnidade_Fatec INT UNSIGNED NOT NULL
);


-- Tabela de Histórico de Editais
CREATE TABLE tb_Editais_Historico (
  id_editalHistorico INT PRIMARY KEY,
  descricao VARCHAR(100),
  id_Edital INT NOT NULL,
  data_acao_edital DATETIME NOT NULL,
  acao_edital ENUM('inicio', 'encerrado', 'estornar') NOT NULL
);


-- Tabela de Cursos
CREATE TABLE tb_cursos (
  id_curso INT NOT NULL AUTO_INCREMENT,
  Materia VARCHAR(70) NOT NULL,
  coordenador VARCHAR(45),
  descricao VARCHAR(120),
  id_docenteCoordenador INT NOT NULL,
  PRIMARY KEY (id_curso, id_docenteCoordenador)
);


-- Tabela de Disciplinas
CREATE TABLE tb_disciplinas (
  id_disciplina INT PRIMARY KEY AUTO_INCREMENT,
  disciplina VARCHAR(80) NOT NULL,
  observacao VARCHAR(45)
);


-- Tabela de Relação Docentes-Disciplinas
CREATE TABLE tb_docentes_Disciplina (
  id_Docente INT NOT NULL,
  id_disciplina INT NOT NULL,
  PRIMARY KEY (id_Docente, id_disciplina)
);


-- Tabela de Formulário de Inscrição HAE
CREATE TABLE tb_frm_inscricao_hae (
  id_frmInscricaoHae INT NOT NULL,
  tb_Docentes_id_Docente INT NOT NULL,
  id_edital INT NOT NULL,
  id_curso INT NOT NULL,
  tb_cursos_id_docenteCoordenador INT NOT NULL,
  tipoHae VARCHAR(120) NOT NULL,
  tituloProjeto VARCHAR(90) NOT NULL,
  inicioProjeto DATE NOT NULL,
  fimProjeto DATE NOT NULL,
  metasProjeto VARCHAR(150) NOT NULL,
  objetivoProjeto VARCHAR(200) NOT NULL,
  justificativaProjeto VARCHAR(200) NOT NULL,
  recursosMateriais VARCHAR(80) NOT NULL,
  resultadoEsperado VARCHAR(150) NOT NULL,
  metodologia VARCHAR(200) NOT NULL,
  cronogramaMes1 VARCHAR(120),
  cronogramaMes2 VARCHAR(120),
  cronogramaMes3 VARCHAR(120),
  cronogramaMes4 VARCHAR(120),
  cronogramaMes5 VARCHAR(120),
  cronogramaMes6 VARCHAR(120),
  tb_horarioExecHae_id_horarioExecHae INT NOT NULL,
  PRIMARY KEY (id_frmInscricaoHae, id_curso, tb_cursos_id_docenteCoordenador, tb_horarioExecHae_id_horarioExecHae)
);


-- Tabela de Relação Unidade Fatec-Docente
CREATE TABLE tb_unidadefatec_Docente (
  id_unidadefatec INT UNSIGNED NOT NULL,
  id_Docente INT NOT NULL,
  PRIMARY KEY (id_unidadefatec, id_Docente)
);


-- Tabela de Horas de Aulas
CREATE TABLE tb_HorasAulas (
  id_HorasAulas INT PRIMARY KEY AUTO_INCREMENT,
  id_Docente INT NOT NULL,
  diaSemana TINYINT NOT NULL,
  horarioInicio TIME NOT NULL,
  horarioFim TIME NOT NULL,
  instituicao VARCHAR(45) NOT NULL
);


-- Tabela de Relação Cursos-Disciplinas
CREATE TABLE tb_cursos_Disciplinas (
  id_curso INT NOT NULL,
  id_docenteCoordenador INT NOT NULL,
  id_disciplina INT NOT NULL,
  PRIMARY KEY (id_curso, id_docenteCoordenador, id_disciplina)
);


-- Tabela de Horário de Execução HAE
CREATE TABLE tb_horarioExecHae (
  id_horarioExecHae INT PRIMARY KEY AUTO_INCREMENT,
  diaSemana TINYINT NOT NULL,
  horarioInicio TIME NOT NULL,
  horarioFinal TIME NOT NULL,
  tb_frm_inscricao_hae_id_frmInscricaoHae INT NOT NULL,
  tb_frm_inscricao_hae_id_curso INT NOT NULL,
  tb_frm_inscricao_hae_tb_cursos_id_docenteCoordenador INT NOT NULL,
  tb_frm_inscricao_hae_tb_horarioExecHae_id_horarioExecHae INT NOT NULL
);


-- Tabela de Justificativa HAE
CREATE TABLE tb_justificativaHae (
  id_justificativaHae INT PRIMARY KEY AUTO_INCREMENT,
  justificativa TEXT NOT NULL,
  data_avaliacao DATETIME NOT NULL,
  status ENUM('APROVADO', 'REPROVADO', 'PENDENTE') NOT NULL,
  id_frmInscricaoHae INT NOT NULL,
  id_docenteCoordenador INT NOT NULL
);


-- Tabela de Relatórios HAE
CREATE TABLE tb_relatorioHae (
    id_relatorioHae INT PRIMARY KEY AUTO_INCREMENT,
    id_frmInscricaoHae INT NOT NULL,
    descricao_atividades TEXT NOT NULL,
    resultados_alcancados TEXT NOT NULL,
    data_entrega DATE NOT NULL,
    status ENUM('PENDENTE', 'APROVADO', 'CORRECAO') NOT NULL DEFAULT 'PENDENTE',
    observacoes_coordenador TEXT,
    data_avaliacao DATETIME,
    FOREIGN KEY (id_frmInscricaoHae) REFERENCES tb_frm_inscricao_hae(id_frmInscricaoHae)
);


-- Constraints de Chave Estrangeira
ALTER TABLE tb_Editais
ADD CONSTRAINT fk_edital_unidade
FOREIGN KEY (Unidade_Fatec_idUnidade_Fatec)
REFERENCES tb_unidadeFatec (id_unidadeFatec);


ALTER TABLE tb_Editais_Historico
ADD CONSTRAINT fk_historico_edital
FOREIGN KEY (id_Edital)
REFERENCES tb_Editais (id_edital);


ALTER TABLE tb_docentes_Disciplina
ADD CONSTRAINT fk_docente_disciplina_docente
FOREIGN KEY (id_Docente)
REFERENCES tb_Docentes (id_Docente);


ALTER TABLE tb_docentes_Disciplina
ADD CONSTRAINT fk_docente_disciplina_disciplina
FOREIGN KEY (id_disciplina)
REFERENCES tb_disciplinas (id_disciplina);


ALTER TABLE tb_frm_inscricao_hae
ADD CONSTRAINT fk_inscricao_docente
FOREIGN KEY (tb_Docentes_id_Docente)
REFERENCES tb_Docentes (id_Docente);


ALTER TABLE tb_frm_inscricao_hae
ADD CONSTRAINT fk_inscricao_edital
FOREIGN KEY (id_edital)
REFERENCES tb_Editais (id_edital);


ALTER TABLE tb_frm_inscricao_hae
ADD CONSTRAINT fk_inscricao_curso
FOREIGN KEY (id_curso, tb_cursos_id_docenteCoordenador)
REFERENCES tb_cursos (id_curso, id_docenteCoordenador);


ALTER TABLE tb_unidadefatec_Docente
ADD CONSTRAINT fk_unidade_docente_unidade
FOREIGN KEY (id_unidadefatec)
REFERENCES tb_unidadeFatec (id_unidadeFatec);


ALTER TABLE tb_unidadefatec_Docente
ADD CONSTRAINT fk_unidade_docente_docente
FOREIGN KEY (id_Docente)
REFERENCES tb_Docentes (id_Docente);


ALTER TABLE tb_HorasAulas
ADD CONSTRAINT fk_horas_aulas_docente
FOREIGN KEY (id_Docente)
REFERENCES tb_Docentes (id_Docente);


ALTER TABLE tb_cursos_Disciplinas
ADD CONSTRAINT fk_curso_disciplina_curso
FOREIGN KEY (id_curso, id_docenteCoordenador)
REFERENCES tb_cursos (id_curso, id_docenteCoordenador);


ALTER TABLE tb_cursos_Disciplinas
ADD CONSTRAINT fk_curso_disciplina_disciplina
FOREIGN KEY (id_disciplina)
REFERENCES tb_disciplinas (id_disciplina);


ALTER TABLE tb_horarioExecHae
ADD CONSTRAINT fk_horario_inscricao
FOREIGN KEY (
  tb_frm_inscricao_hae_id_frmInscricaoHae,
  tb_frm_inscricao_hae_id_curso,
  tb_frm_inscricao_hae_tb_cursos_id_docenteCoordenador,
  tb_frm_inscricao_hae_tb_horarioExecHae_id_horarioExecHae
)
REFERENCES tb_frm_inscricao_hae (
  id_frmInscricaoHae,
  id_curso,
  tb_cursos_id_docenteCoordenador,
  tb_horarioExecHae_id_horarioExecHae
);


ALTER TABLE tb_frm_inscricao_hae
ADD INDEX idx_frm_inscricao_coordenador (id_frmInscricaoHae, tb_cursos_id_docenteCoordenador);


ALTER TABLE tb_justificativaHae
ADD CONSTRAINT fk_justificativa_inscricao
FOREIGN KEY (id_frmInscricaoHae, id_docenteCoordenador)
REFERENCES tb_frm_inscricao_hae (id_frmInscricaoHae, tb_cursos_id_docenteCoordenador);


CREATE TABLE tb_cronograma (
    id_cronograma INT PRIMARY KEY AUTO_INCREMENT,
    tipo_evento ENUM('divulgacao_edital', 'inscricoes_abertas', 'aprovacoes', 'lista_aprovados', 'entrega_relatorios', 'aprovacao_relatorios') NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    id_edital INT NOT NULL,
    FOREIGN KEY (id_edital) REFERENCES tb_Editais(id_edital)
);


ALTER TABLE tb_Editais
ADD COLUMN arquivo_pdf VARCHAR(255) NULL AFTER edital_status;


ALTER TABLE tb_frm_inscricao_hae
MODIFY COLUMN inicioProjeto date NULL;


ALTER TABLE tb_frm_inscricao_hae
MODIFY COLUMN fimProjeto date NULL;


8. Referências
FALBO, Ricardo de Almeida. Engenharia de Requisitos: Notas de Aula. Vitória: Universidade Federal do Espírito Santo – UFES, 2017. Disponível em: https://www.inf.ufes.br/~falbo/downloads/ensino/engenharia-requisitos/notas-aula-eng-requisitos-2017.pdf. Acesso em: 13 jun. 2025.
FALBO, Ricardo de Almeida. Engenharia de Software: Notas de Aula. Vitória: Universidade Federal do Espírito Santo – UFES, 2014. Disponível em: https://www.inf.ufes.br/~falbo/downloads/ensino/engenharia-software/notas-aula-eng-soft-2014.pdf. Acesso em: 13 jun. 2025.
FATEC ITAPIRA. Edital de concessão de cotas de Hora Atividade Específica (H.A.E.) – 2º semestre de 2024, primeira chamada. Itapira: Fatec de Itapira “Ogari de Castro Pacheco”, 2024. 15 p.
FATEC ITAPIRA. Formulário de avaliação – Projeto Interdisciplinar I – Avaliadores Externos. Curso de Desenvolvimento de Software Multiplataforma. Itapira: Fatec de Itapira “Ogari de Castro Pacheco”, 2024. 2 p.
FATEC ITAPIRA. PI-2 – Descrição do projeto: Tratamento de inscrições para Editais de HAE (Horas de Atividade Específicas). Fatec Itapira – Dr. Ogari de Castro Pacheco, Curso de Desenvolvimento de Software Multiplataforma. 2025. 11 p.
PORTES, Ana Célia. Aula 03 – Diagrama de Atividades. Engenharia de Software I. Itapira: Fatec Itapira, 2024. Apostila digital (PDF), 17 p.
PORTES, Ana Célia. Aula 06 – Documentação de Requisitos. Engenharia de Software II. Itapira: Fatec Itapira, 2024. Apostila digital (PDF), 19 p.


