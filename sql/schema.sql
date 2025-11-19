USE `petadocao_db`;

DROP TABLE IF EXISTS `animais`;

CREATE TABLE `animais` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nome` VARCHAR(100) NOT NULL,
  `especie` VARCHAR(50) NOT NULL,
  `raca` VARCHAR(100),
  `idade_anos` INT,
  `idade_meses` INT,
  `genero` VARCHAR(20),
  `porte` VARCHAR(30),
  `localizacao` VARCHAR(255),
  `historia` TEXT,
  `taxa_adocao` DECIMAL(10, 2) DEFAULT 0.00,
  `foto_path` VARCHAR(255) NULL,
  `destaque` BOOLEAN DEFAULT FALSE,
  `data_cadastro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);