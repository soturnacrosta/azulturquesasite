-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 03/01/2025 às 17:57
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
-- Banco de dados: `tags`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `paginas`
--

CREATE TABLE `paginas` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `conteudo` text NOT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo` varchar(255) DEFAULT 'normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `paginas`
--

INSERT INTO `paginas` (`id`, `titulo`, `conteudo`, `data_criacao`, `tipo`) VALUES
(233, 'Letras de nossas músicas', 'Letras das músicas da Azul Turquesa', '2025-01-03 16:53:43', 'normal'),
(234, 'Galeria de fotos', 'Fotos promocionais da banda Azul Turquesa', '2025-01-03 16:53:55', 'galeria'),
(235, 'Onde nos ouvir', 'Onde ouvir a Azul Turquesa', '2025-01-03 16:53:57', 'links'),
(238, 'Homepage', 'Homepage do site da Azul Turquesa', '2025-01-03 16:55:39', 'normal'),
(239, 'Sobre nós', 'Sobre a banda Azul Turquesa', '2025-01-03 16:55:48', 'normal'),
(241, 'Downloads', 'Downloads das músicas da Azul Turquesa', '2025-01-03 16:56:36', 'normal');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagina_tags`
--

CREATE TABLE `pagina_tags` (
  `pagina_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pagina_tags`
--

INSERT INTO `pagina_tags` (`pagina_id`, `tag_id`) VALUES
(233, 78),
(233, 79),
(233, 80),
(234, 80),
(234, 81),
(234, 82),
(234, 83),
(234, 84),
(238, 87),
(238, 88),
(239, 80),
(239, 89),
(239, 90),
(239, 91),
(241, 92),
(241, 93),
(241, 94);

-- --------------------------------------------------------

--
-- Estrutura para tabela `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `conteudo` text NOT NULL,
  `data_publicacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tags`
--

INSERT INTO `tags` (`id`, `nome`) VALUES
(80, 'azul turquesa'),
(84, 'Azul Turquesa Duo'),
(94, 'baixar musicas da azul turquesa'),
(91, 'curiosidades azul turquesa'),
(92, 'downloads'),
(93, 'downloads da azul turquesa'),
(83, 'Efemera'),
(81, 'fotos da azul turquesa'),
(90, 'historia da azul turquesa'),
(87, 'homepage do site azul turquesa'),
(78, 'letras da azul turquesa'),
(79, 'musicas da azul turquesa'),
(85, 'onde ouvir azul turquesa'),
(86, 'ondeouvir'),
(88, 'posts'),
(89, 'sobre a azul turquesa'),
(82, 'Soturna Crosta');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `paginas`
--
ALTER TABLE `paginas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `titulo` (`titulo`);

--
-- Índices de tabela `pagina_tags`
--
ALTER TABLE `pagina_tags`
  ADD PRIMARY KEY (`pagina_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Índices de tabela `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `paginas`
--
ALTER TABLE `paginas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=242;

--
-- AUTO_INCREMENT de tabela `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `pagina_tags`
--
ALTER TABLE `pagina_tags`
  ADD CONSTRAINT `pagina_tags_ibfk_1` FOREIGN KEY (`pagina_id`) REFERENCES `paginas` (`id`),
  ADD CONSTRAINT `pagina_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
