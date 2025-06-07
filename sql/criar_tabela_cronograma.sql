-- Tabela de Cronograma
CREATE TABLE IF NOT EXISTS tb_cronograma (
    id_cronograma INT PRIMARY KEY AUTO_INCREMENT,
    tipo_evento ENUM('divulgacao_edital', 'inscricoes_abertas', 'aprovacoes', 'lista_aprovados', 'entrega_relatorios', 'aprovacao_relatorios') NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    id_edital INT NOT NULL,
    FOREIGN KEY (id_edital) REFERENCES tb_Editais(id_edital)
); 