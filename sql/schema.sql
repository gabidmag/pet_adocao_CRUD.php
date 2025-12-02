-- Criar o banco de dados
CREATE DATABASE IF NOT EXISTS adocao_animais;
USE adocao_animais;

-- Tabela de usuários (login / admin)
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome_usuario VARCHAR(150) NOT NULL,
    email_usuario VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    ativo TINYINT(1) DEFAULT 1,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de animais utilizada pelo frontend e admin
CREATE TABLE IF NOT EXISTS animais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    especie VARCHAR(100) DEFAULT NULL,
    raca VARCHAR(100) DEFAULT NULL,
    idade INT DEFAULT NULL,
    descricao TEXT DEFAULT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    status ENUM('disponivel','adotado','indisponivel') DEFAULT 'disponivel',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status)
);

-- Tabela de pedidos/adoções (adocoes)
CREATE TABLE IF NOT EXISTS adocoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_id INT NOT NULL,
    nome_adotante VARCHAR(150) NOT NULL,
    email_adotante VARCHAR(150) NOT NULL,
    telefone_adotante VARCHAR(50) DEFAULT NULL,
    motivo_adocao TEXT DEFAULT NULL,
    status ENUM('pendente','aprovada','rejeitada') DEFAULT 'pendente',
    data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_adocoes_animal FOREIGN KEY (animal_id) REFERENCES animais(id) ON DELETE CASCADE,
    INDEX idx_status_adocao (status),
    INDEX idx_animal_id (animal_id)
);

-- Inserções de exemplo (opcional)
INSERT INTO usuarios (nome_usuario, email_usuario, senha_hash, ativo)
VALUES
('Administrador', 'admin@local.test', '$2y$10$abcdefghijklmnopqrstuvwx1234567890abcdefghi', 1);

INSERT INTO animais (nome, especie, raca, idade, descricao, status)
VALUES
('Rex', 'Cachorro', 'Vira-lata', 3, 'Cachorro dócil, ótimo com crianças', 'disponivel'),
('Mimi', 'Gato', 'SRD', 2, 'Gata carinhosa e independente', 'disponivel'),
('Bilu', 'Cachorro', 'Labrador', 5, 'Calmo e brincalhão', 'disponivel');