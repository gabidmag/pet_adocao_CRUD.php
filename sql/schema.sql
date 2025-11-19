-- Criar o banco de dados
CREATE DATABASE IF NOT EXISTS adocao_animais;
USE adocao_animais;

-- Tabela de Espécies
CREATE TABLE especies (
    id_especie INT AUTO_INCREMENT PRIMARY KEY,
    nome_especie VARCHAR(50) NOT NULL UNIQUE,
    descricao TEXT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Raças
CREATE TABLE racas (
    id_raca INT AUTO_INCREMENT PRIMARY KEY,
    id_especie INT NOT NULL,
    nome_raca VARCHAR(100) NOT NULL,
    porte ENUM('Pequeno', 'Médio', 'Grande') NOT NULL,
    expectativa_vida INT,
    caracteristicas TEXT,
    FOREIGN KEY (id_especie) REFERENCES especies(id_especie),
    INDEX idx_especie (id_especie)
);

-- Tabela de Abrigos/ONGs Parceiras
CREATE TABLE abrigos (
    id_abrigo INT AUTO_INCREMENT PRIMARY KEY,
    nome_abrigo VARCHAR(200) NOT NULL,
    cnpj VARCHAR(18) UNIQUE,
    telefone VARCHAR(20),
    email VARCHAR(150),
    endereco_completo TEXT NOT NULL,
    responsavel VARCHAR(150),
    capacidade_maxima INT,
    animais_ativos INT DEFAULT 0,
    ativo BOOLEAN DEFAULT TRUE,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Animais para Adoção
CREATE TABLE animais (
    id_animal INT AUTO_INCREMENT PRIMARY KEY,
    nome_animal VARCHAR(100) NOT NULL,
    id_especie INT NOT NULL,
    id_raca INT NOT NULL,
    id_abrigo INT NOT NULL,
    
    -- Informações básicas
    sexo ENUM('M', 'F') NOT NULL,
    data_nascimento DATE,
    idade_estimada INT, -- em meses
    peso DECIMAL(5,2), -- em kg
    cor_principal VARCHAR(50),
    cor_secundaria VARCHAR(50),
    
    -- Saúde
    vacinado BOOLEAN DEFAULT FALSE,
    vermifugado BOOLEAN DEFAULT FALSE,
    castrado BOOLEAN DEFAULT FALSE,
    microchip VARCHAR(50) UNIQUE,
    historico_saude TEXT,
    necessidades_especiais TEXT,
    
    -- Comportamento
    temperamento ENUM('Calmo', 'Brincalhão', 'Timido', 'Sociável', 'Independente') DEFAULT 'Sociável',
    nivel_energia ENUM('Baixo', 'Moderado', 'Alto') DEFAULT 'Moderado',
    bom_com_criancas BOOLEAN DEFAULT TRUE,
    bom_com_outros_animais BOOLEAN DEFAULT TRUE,
    
    -- Status
    status ENUM('Disponível', 'Processo Adoção', 'Adotado', 'Indisponível') DEFAULT 'Disponível',
    data_entrada DATE NOT NULL,
    data_adocao DATE NULL,
    
    -- Descrição
    descricao TEXT,
    historia TEXT,
    cuidados_especiais TEXT,
    
    -- Fotos (serão armazenadas como caminhos)
    foto_principal VARCHAR(255),
    
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_especie) REFERENCES especies(id_especie),
    FOREIGN KEY (id_raca) REFERENCES racas(id_raca),
    FOREIGN KEY (id_abrigo) REFERENCES abrigos(id_abrigo),
    
    INDEX idx_especie_raca (id_especie, id_raca),
    INDEX idx_abrigo_status (id_abrigo, status),
    INDEX idx_status (status)
);

-- Tabela de Fotos dos Animais
CREATE TABLE fotos_animais (
    id_foto INT AUTO_INCREMENT PRIMARY KEY,
    id_animal INT NOT NULL,
    caminho_foto VARCHAR(255) NOT NULL,
    descricao_foto VARCHAR(200),
    ordem INT DEFAULT 0,
    data_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_animal) REFERENCES animais(id_animal) ON DELETE CASCADE,
    INDEX idx_animal (id_animal)
);

-- Tabela de Adotantes
CREATE TABLE adotantes (
    id_adotante INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(200) NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    data_nascimento DATE NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(150) NOT NULL,
    
    -- Endereço
    cep VARCHAR(9) NOT NULL,
    logradouro VARCHAR(200) NOT NULL,
    numero VARCHAR(10) NOT NULL,
    complemento VARCHAR(100),
    bairro VARCHAR(100) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    estado CHAR(2) NOT NULL,
    
    -- Situação
    tipo_residencia ENUM('Casa', 'Apartamento', 'Sítio', 'Outro') NOT NULL,
    possui_quintal BOOLEAN DEFAULT FALSE,
    quintal_cerrado BOOLEAN DEFAULT FALSE,
    experiencia_animais BOOLEAN DEFAULT FALSE,
    outros_animais TEXT,
    
    -- Trabalho
    situacao_emprego ENUM('Empregado', 'Autônomo', 'Desempregado', 'Aposentado', 'Estudante') NOT NULL,
    renda_mensal DECIMAL(10,2),
    
    -- Família
    estado_civil ENUM('Solteiro', 'Casado', 'Divorciado', 'Viúvo'),
    pessoas_na_casa INT DEFAULT 1,
    criancas_na_casa BOOLEAN DEFAULT FALSE,
    idade_criancas VARCHAR(50),
    
    -- Status
    status ENUM('Ativo', 'Inativo', 'Suspenso') DEFAULT 'Ativo',
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_cpf (cpf),
    INDEX idx_cidade (cidade)
);

-- Tabela de Processos de Adoção
CREATE TABLE processos_adocao (
    id_processo INT AUTO_INCREMENT PRIMARY KEY,
    id_animal INT NOT NULL,
    id_adotante INT NOT NULL,
    id_abrigo INT NOT NULL,
    
    -- Status do processo
    status ENUM(
        'Solicitada', 
        'Em Análise', 
        'Visita Agendada', 
        'Visita Realizada',
        'Aprovada', 
        'Reprovada', 
        'Cancelada',
        'Concluída'
    ) DEFAULT 'Solicitada',
    
    -- Datas importantes
    data_solicitacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_analise DATE,
    data_visita DATE,
    data_aprovacao DATE,
    data_conclusao DATE,
    
    -- Avaliações
    pontuacao_entrevista INT, -- 1-10
    observacoes_entrevista TEXT,
    adequacao_animal TEXT,
    
    -- Motivos (em caso de reprovação/cancelamento)
    motivo_reprovacao TEXT,
    motivo_cancelamento TEXT,
    
    -- Contrato
    numero_contrato VARCHAR(50) UNIQUE,
    data_assinatura_contrato DATE,
    
    -- Acompanhamento pós-adoção
    data_primeiro_acompanhamento DATE,
    data_segundo_acompanhamento DATE,
    situacao_animal TEXT,
    
    FOREIGN KEY (id_animal) REFERENCES animais(id_animal),
    FOREIGN KEY (id_adotante) REFERENCES adotantes(id_adotante),
    FOREIGN KEY (id_abrigo) REFERENCES abrigos(id_abrigo),
    
    INDEX idx_status (status),
    INDEX idx_adotante (id_adotante),
    INDEX idx_animal (id_animal)
);

-- Tabela de Funcionários/Voluntários
CREATE TABLE funcionarios (
    id_funcionario INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(200) NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    telefone VARCHAR(20),
    email VARCHAR(150) NOT NULL,
    cargo ENUM('Coordenador', 'Voluntário', 'Veterinário', 'Assistente') NOT NULL,
    id_abrigo INT,
    ativo BOOLEAN DEFAULT TRUE,
    data_admissao DATE,
    data_demissao DATE,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_abrigo) REFERENCES abrigos(id_abrigo),
    INDEX idx_abrigo (id_abrigo)
);

-- Tabela de Agendamentos (Visitas, Consultas, etc.)
CREATE TABLE agendamentos (
    id_agendamento INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('Visita Animal', 'Entrevista Adoção', 'Consulta Veterinária', 'Entrega Animal') NOT NULL,
    id_animal INT,
    id_adotante INT,
    id_funcionario INT NOT NULL,
    data_agendamento DATETIME NOT NULL,
    duracao_estimada INT DEFAULT 60, -- em minutos
    status ENUM('Agendado', 'Confirmado', 'Realizado', 'Cancelado', 'Não Compareceu') DEFAULT 'Agendado',
    observacoes TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_animal) REFERENCES animais(id_animal),
    FOREIGN KEY (id_adotante) REFERENCES adotantes(id_adotante),
    FOREIGN KEY (id_funcionario) REFERENCES funcionarios(id_funcionario),
    
    INDEX idx_data (data_agendamento),
    INDEX idx_tipo_status (tipo, status)
);

-- Tabela de Doações
CREATE TABLE doacoes (
    id_doacao INT AUTO_INCREMENT PRIMARY KEY,
    id_doador INT, -- Pode ser adotante ou não cadastrado
    nome_doador VARCHAR(200),
    cpf_doador VARCHAR(14),
    email_doador VARCHAR(150),
    telefone_doador VARCHAR(20),
    
    tipo_doacao ENUM('Dinheiro', 'Ração', 'Medicamento', 'Brinquedo', 'Outros') NOT NULL,
    valor_doacao DECIMAL(10,2),
    descricao_item TEXT,
    quantidade INT DEFAULT 1,
    
    data_doacao DATE NOT NULL,
    comprovante_path VARCHAR(255),
    observacoes TEXT,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_data (data_doacao),
    INDEX idx_tipo (tipo_doacao)
);

-- Tabela de Registros Veterinários
CREATE TABLE registros_veterinarios (
    id_registro INT AUTO_INCREMENT PRIMARY KEY,
    id_animal INT NOT NULL,
    id_veterinario INT, -- Funcionário do tipo veterinário
    tipo_procedimento ENUM('Consulta', 'Vacina', 'Cirurgia', 'Exame', 'Medicação') NOT NULL,
    descricao TEXT NOT NULL,
    data_procedimento DATE NOT NULL,
    proxima_data DATE, -- Próxima vacina/consulta
    medicamentos_prescritos TEXT,
    observacoes TEXT,
    custo DECIMAL(8,2),
    comprovante_path VARCHAR(255),
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_animal) REFERENCES animais(id_animal),
    FOREIGN KEY (id_veterinario) REFERENCES funcionarios(id_funcionario),
    
    INDEX idx_animal (id_animal),
    INDEX idx_data (data_procedimento)
);

-- Tabela de Logs e Auditoria
CREATE TABLE logs_sistema (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    tabela_afetada VARCHAR(100) NOT NULL,
    id_registro_afetado INT,
    operacao ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    valores_antigos JSON,
    valores_novos JSON,
    id_usuario INT, -- Funcionário que fez a operação
    ip_origem VARCHAR(45),
    data_operacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_tabela (tabela_afetada),
    INDEX idx_data (data_operacao)
);

-- Views Úteis

-- View: Animais Disponíveis para Adoção
CREATE VIEW vw_animais_disponiveis AS
SELECT 
    a.id_animal,
    a.nome_animal,
    e.nome_especie,
    r.nome_raca,
    r.porte,
    a.sexo,
    a.idade_estimada,
    a.cor_principal,
    a.temperamento,
    ab.nome_abrigo,
    a.foto_principal,
    a.descricao
FROM animais a
JOIN especies e ON a.id_especie = e.id_especie
JOIN racas r ON a.id_raca = r.id_raca
JOIN abrigos ab ON a.id_abrigo = ab.id_abrigo
WHERE a.status = 'Disponível'
AND ab.ativo = TRUE;

-- View: Estatísticas de Adoção
CREATE VIEW vw_estatisticas_adocao AS
SELECT 
    e.nome_especie,
    COUNT(*) as total_animais,
    SUM(CASE WHEN a.status = 'Adotado' THEN 1 ELSE 0 END) as adotados,
    SUM(CASE WHEN a.status = 'Disponível' THEN 1 ELSE 0 END) as disponiveis,
    ROUND((SUM(CASE WHEN a.status = 'Adotado' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as taxa_adocao
FROM animais a
JOIN especies e ON a.id_especie = e.id_especie
GROUP BY e.id_especie, e.nome_especie;

-- View: Processos de Adoção em Andamento
CREATE VIEW vw_processos_andamento AS
SELECT 
    p.id_processo,
    a.nome_animal,
    e.nome_especie,
    ad.nome_completo as adotante,
    p.status,
    p.data_solicitacao,
    DATEDIFF(CURRENT_DATE, p.data_solicitacao) as dias_em_andamento
FROM processos_adocao p
JOIN animais a ON p.id_animal = a.id_animal
JOIN especies e ON a.id_especie = e.id_especie
JOIN adotantes ad ON p.id_adotante = ad.id_adotante
WHERE p.status IN ('Solicitada', 'Em Análise', 'Visita Agendada', 'Visita Realizada');

-- Inserir Dados Iniciais

INSERT INTO especies (nome_especie, descricao) VALUES
('Cachorro', 'Animal doméstico da família Canidae'),
('Gato', 'Animal doméstico da família Felidae'),
('Coelho', 'Animal doméstico da família Leporidae'),
('Pássaro', 'Aves domésticas de diversas espécies');

INSERT INTO racas (id_especie, nome_raca, porte, expectativa_vida) VALUES
(1, 'Vira-lata', 'Médio', 15),
(1, 'Labrador', 'Grande', 12),
(1, 'Poodle', 'Pequeno', 15),
(1, 'Bulldog', 'Médio', 10),
(2, 'Vira-lata', 'Pequeno', 16),
(2, 'Siamês', 'Pequeno', 15),
(2, 'Persa', 'Pequeno', 14);

INSERT INTO abrigos (nome_abrigo, telefone, email, endereco_completo, responsavel, capacidade_maxima) VALUES
('Abrigo Amigo dos Animais', '(11) 9999-8888', 'contato@amigodosanimais.org', 'Rua das Flores, 123 - Centro - São Paulo/SP', 'Maria Silva', 50),
('Patas Felizes', '(11) 7777-6666', 'adocao@patasfelizes.org', 'Av. Principal, 456 - Jardim - São Paulo/SP', 'João Santos', 30);

-- Criar usuário para aplicação
CREATE USER 'app_adocao'@'localhost' IDENTIFIED BY 'senha_segura_123';
GRANT SELECT, INSERT, UPDATE, DELETE ON adocao_animais.* TO 'app_adocao'@'localhost';
FLUSH PRIVILEGES;