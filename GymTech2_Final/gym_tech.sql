-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21/09/2026 às 23:33
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
-- Banco de dados: `gym_tech`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos`
--

CREATE TABLE `alunos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `data_nascimento` date NOT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `plano` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Ativo',
  `modalidade` varchar(50) NOT NULL,
  `objetivo` text DEFAULT NULL,
  `peso_inicial` decimal(5,2) DEFAULT NULL,
  `altura` decimal(3,2) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT 'img/avatar-padrao.jpg',
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `alunos`
--

INSERT INTO `alunos` (`id`, `nome`, `cpf`, `email`, `senha`, `endereco`, `data_nascimento`, `celular`, `plano`, `status`, `modalidade`, `objetivo`, `peso_inicial`, `altura`, `avatar`, `data_cadastro`) VALUES
(1, 'Mariana Costa', '123.456.789-01', 'mariana@teste.com', 'aluno123', 'Rua das Flores, 123', '0000-00-00', '(11) 98765-4321', 'Plano Performance', 'Ativo', 'Musculação', 'Ganho de massa muscular e hipertrofia', 65.00, 1.70, 'img/avatar-padrao.jpg', '2026-09-21 19:22:10'),
(2, 'Lucas Ferreira', '987.654.321-02', 'lucas@teste.com', 'aluno123', 'Avenida Central, 456', '0000-00-00', '(11) 91234-5678', 'Plano Essencial', 'Ativo', 'Hipertrofia', 'Definição muscular e perda de gordura', 80.50, 1.82, 'img/avatar-padrao.jpg', '2026-09-21 19:22:10'),
(3, 'Carolina Monteiro', '838.724.862-38', 'carolina@teste.com', 'aluno123', 'Alameda dos Ipês, 789', '1995-09-11', '(11) 99887-7665', 'Plano Cardio+', 'Ativo', 'Condicionamento', 'Ganho de massa', 60.00, 1.65, 'img/avatar-padrao.jpg', '2026-09-21 19:22:10');

-- --------------------------------------------------------

--
-- Estrutura para tabela `treinos`
--

CREATE TABLE `treinos` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `observacoes` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Em Andamento'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `treinos`
--

INSERT INTO `treinos` (`id`, `aluno_id`, `nome`, `categoria`, `observacoes`, `status`) VALUES
(1, 1, 'Treino A - Membros Inferiores', 'Musculação', 'Agachamento livre 4x10, Leg Press 4x12, Cadeira Extensora 3x15', 'Concluído'),
(2, 2, 'Treino B - Peito e Tríceps', 'Hipertrofia', 'Supino Reto 4x8, Crossover 3x12, Tríceps Testa 4x10', 'Concluído');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` varchar(20) NOT NULL,
  `email` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `usuario`, `senha`, `perfil`, `email`) VALUES
(4, 'Recepção Gym Tech', 'recepcao', 'gestao@123', 'recepcao', NULL),
(5, 'Professor Treino', 'personal', 'treino@123', 'professor', NULL),
(8, 'Administrador Master', 'admin', 'admin@123', 'admin', 'admin@gymtech.com');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `treinos`
--
ALTER TABLE `treinos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_treinos_alunos` (`aluno_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `alunos`
--
ALTER TABLE `alunos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `treinos`
--
ALTER TABLE `treinos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `treinos`
--
ALTER TABLE `treinos`
  ADD CONSTRAINT `fk_treinos_alunos` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
