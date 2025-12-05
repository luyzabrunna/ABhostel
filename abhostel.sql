-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 05/12/2025 às 05:14
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `abhostel`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `imoveis`
--

CREATE TABLE `imoveis` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descricao` text NOT NULL,
  `cidade` varchar(100) NOT NULL,
  `logradouro` varchar(255) DEFAULT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `estado` varchar(50) NOT NULL,
  `complemento` varchar(255) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `quartos` int(11) DEFAULT 0,
  `suites` int(11) DEFAULT 0,
  `banheiros` int(11) DEFAULT 0,
  `capacidade` int(11) DEFAULT 1,
  `wifi` tinyint(4) DEFAULT 0,
  `ar_condicionado` tinyint(4) DEFAULT 0,
  `estacionamento` tinyint(4) DEFAULT 0,
  `pet_friendly` tinyint(4) DEFAULT 0,
  `piscina` tinyint(4) DEFAULT 0,
  `cozinha` tinyint(4) DEFAULT 0,
  `tv` tinyint(4) DEFAULT 0,
  `area_trabalho` tinyint(4) DEFAULT 0,
  `cafe_manha` tinyint(4) DEFAULT 0,
  `maquina_lavar` tinyint(4) DEFAULT 0,
  `valor` decimal(10,2) NOT NULL,
  `tipo_preco` varchar(50) DEFAULT 'diaria',
  `data_inicio` date DEFAULT NULL,
  `data_termino` date DEFAULT NULL,
  `whatsapp` varchar(30) DEFAULT NULL,
  `email_proprietario` varchar(255) DEFAULT NULL,
  `fotos` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `imoveis`
--

INSERT INTO `imoveis` (`id`, `tipo`, `titulo`, `descricao`, `cidade`, `logradouro`, `numero`, `estado`, `complemento`, `bairro`, `quartos`, `suites`, `banheiros`, `capacidade`, `wifi`, `ar_condicionado`, `estacionamento`, `pet_friendly`, `piscina`, `cozinha`, `tv`, `area_trabalho`, `cafe_manha`, `maquina_lavar`, `valor`, `tipo_preco`, `data_inicio`, `data_termino`, `whatsapp`, `email_proprietario`, `fotos`) VALUES
(1, 'Casa', 'Casa Colinas', 'Casa 3 quartos centro', 'Colinas TO', 'Rua 1', '123', 'Tocantins', 'SEILA', 'Centro', 2, 1, 2, 1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1500.00, 'mensal', '2025-12-05', '2025-12-31', '123123123123', 'teste@mail.com', NULL),
(3, 'Hostel', 'Teste 2', 'Teste', 'Palmas', NULL, NULL, '', NULL, NULL, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 100.00, '1200', NULL, NULL, NULL, NULL, NULL),
(4, 'Casa', 'Casa nova', 'Olá', 'Palmas', 'JK', '123', 'Tocantins (TO)', '', 'Centro', 1, 1, NULL, 1, 0, 1, 1, 0, 0, 0, 1, 0, 0, 0, 2000.00, NULL, '2025-12-05', '2025-12-11', '123123', NULL, '[]'),
(6, 'Casa', 'Casa nova', 'ateasdad', 'Palmas', 'JK', '12331', 'Tocantins (TO)', '123', 'Centro', 2, 1, NULL, 3, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 2000.00, 'mes', '2025-12-05', '2026-01-05', '123123', '', '[]');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `email`, `senha`) VALUES
(1, 'abhostel@gmail.com', '$2y$10$Q8jrY5sLsveW2pStc4ZIpOY29yFuyLpgbfdNLG8oS73d/D4A4TXA.');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `imoveis`
--
ALTER TABLE `imoveis`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `imoveis`
--
ALTER TABLE `imoveis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
