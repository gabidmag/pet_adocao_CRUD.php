-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 04/12/2025 às 08:20
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `petadocao_db`
--
CREATE DATABASE IF NOT EXISTS `petadocao_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `petadocao_db`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `adocoes`
--

CREATE TABLE `adocoes` (
  `id` int(11) NOT NULL,
  `animal_id` int(11) NOT NULL,
  `nome_adotante` varchar(150) NOT NULL,
  `email_adotante` varchar(150) NOT NULL,
  `telefone_adotante` varchar(50) DEFAULT NULL,
  `motivo_adocao` text DEFAULT NULL,
  `status` enum('pendente','aprovada','rejeitada') DEFAULT 'pendente',
  `data_pedido` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `animais`
--

CREATE TABLE `animais` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `especie` varchar(50) NOT NULL,
  `raca` varchar(100) DEFAULT NULL,
  `idade_anos` int(11) DEFAULT 0,
  `idade_meses` int(11) DEFAULT 0,
  `genero` varchar(20) DEFAULT NULL,
  `porte` varchar(30) DEFAULT NULL,
  `localizacao` varchar(255) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `taxa_adocao` decimal(10,2) DEFAULT 0.00,
  `foto` varchar(255) DEFAULT NULL,
  `destaque` tinyint(1) DEFAULT 0,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('disponivel','adotado','indisponivel') DEFAULT 'disponivel'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `animais`
--

INSERT INTO `animais` (`id`, `nome`, `especie`, `raca`, `idade_anos`, `idade_meses`, `genero`, `porte`, `localizacao`, `descricao`, `taxa_adocao`, `foto`, `destaque`, `data_cadastro`, `status`) VALUES
(1, 'Fofinho', 'Cachorro', 'Vira-lata Caramelo', 1, 8, 'Macho', 'Pequeno', 'Recife, PE ', 'Encontrado na rua, é muito dócil, brincalhão e adora um carinho na barriga. Já está vacinado e vermifugado.', 20.00, 'uploads/69236ed445b32_1763929812.jpg', 0, '2025-11-23 20:30:12', 'disponivel'),
(3, 'Max', 'Cachorro', 'Golden Retriever', 2, 0, 'Macho', 'Grande', 'São Paulo, SP', 'Max é um companheiro leal e amigável, perfeito para famílias. Adora buscar bolinhas e passeios no parque.', 250.00, 'uploads/max-golden.jpg', 1, '2025-11-23 20:39:44', 'disponivel'),
(4, 'Luna', 'Gato', 'Gato Doméstico', 1, 2, 'Fêmea', 'Pequeno', 'Rio de Janeiro, RJ', 'Luna é uma gatinha calma e carinhosa. Adora um lugar quentinho para dormir e um bom carinho na barriga.', 150.00, 'uploads/luna-gato.jpg', 0, '2025-11-23 20:39:44', 'disponivel'),
(5, 'Thor', 'Cachorro', 'Beagle', 3, 0, 'Macho', 'Médio', 'Belo Horizonte, MG', 'Um explorador nato! Thor é curioso, ativo e se dá muito bem com outros cães. Precisa de espaço para gastar energia.', 200.00, 'uploads/thor-beagle.jpg', 1, '2025-11-23 20:39:44', 'disponivel'),
(6, 'Mia', 'Gato', 'Siamês', 1, 0, 'Fêmea', 'Pequeno', 'Curitiba, PR', 'Mia é uma gata elegante de lindos olhos azuis. É um pouco tímida no começo, mas muito apegada ao seu dono.', 180.00, 'uploads/mia-branca.jpg', 1, '2025-11-23 20:39:44', 'disponivel'),
(7, 'Rex', 'Cachorro', 'Pastor Alemão', 8, 0, 'Macho', 'Grande', 'Porto Alegre, RS', 'Um senhor cão muito leal e protetor. Rex é calmo, obediente e um ótimo cão de guarda para a família.', 50.00, 'uploads/rex-pastor.jpg', 0, '2025-11-23 20:39:44', 'disponivel'),
(8, 'Biscoito', 'Cachorro', 'Vira-lata (SRD)', 0, 5, 'Macho', 'Pequeno', 'Salvador, BA', 'Esse pequeno é pura alegria! Biscoito é muito brincalhão, cheio de energia e adora fazer amizade com todos.', 120.00, 'uploads/biscoito-viralata.jpg', 1, '2025-11-23 20:39:44', 'disponivel'),
(9, 'Salem', 'Gato', 'Bombay', 2, 0, 'Macho', 'Médio', 'Recife, PE', 'Um lindo panterinha. Salem é misterioso, adora um bom colo e é o companheiro perfeito para uma noite de filmes.', 80.00, 'uploads/salem-preto.jpg', 0, '2025-11-23 20:39:44', 'disponivel'),
(10, 'Bela', 'Cachorro', 'Cocker Spaniel', 4, 0, 'Fêmea', 'Médio', 'Brasília, DF', 'Bela é extremamente dócil e apegada à família. Ótima para apartamentos, contanto que tenha seus passeios diários.', 180.00, 'uploads/bela-cocker.jpg', 0, '2025-11-23 20:39:44', 'disponivel'),
(11, 'Garfield', 'Gato', 'Persa', 5, 0, 'Macho', 'Médio', 'Florianópolis, SC', 'Um verdadeiro lorde do sofá. Garfield é calmo, majestoso e adora ser escovado. Procura um lar tranquilo.', 200.00, 'uploads/garfield-persa.jpg', 1, '2025-11-23 20:39:44', 'disponivel'),
(12, 'Nina', 'Cachorro', 'Poodle', 0, 9, 'Fêmea', 'Pequeno', 'Manaus, AM', 'Nina é muito inteligente e fácil de treinar. Já sabe sentar e dar a patinha! Perfeita para quem busca uma companheira esperta.', 170.00, 'uploads/nina-poodle.jpg', 0, '2025-11-23 20:39:44', 'disponivel');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nome_usuario` varchar(150) NOT NULL,
  `email_usuario` varchar(150) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `tipo_usuario` enum('admin','usuario') NOT NULL DEFAULT 'usuario',
  `ativo` tinyint(1) DEFAULT 1,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nome_usuario`, `email_usuario`, `senha_hash`, `tipo_usuario`, `ativo`, `data_cadastro`) VALUES
(1, 'Administrador', 'admin@local.test', '$2y$10$abcdefghijklmnopqrstuvwx1234567890abcdefghi', 'admin', 1, '2025-12-02 22:13:37'),
(2, 'Luiz', 'luiz@gmail.com', '$2y$10$H66HOK991QydWLh/lkELW.konQ6a1Q1Vu333Ceb4wHdYiA9X8EwM6', 'usuario', 1, '2025-12-02 22:13:46');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `adocoes`
--
ALTER TABLE `adocoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `animais`
--
ALTER TABLE `animais`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email_usuario` (`email_usuario`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `adocoes`
--
ALTER TABLE `adocoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `animais`
--
ALTER TABLE `animais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;