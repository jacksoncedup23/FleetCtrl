-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           8.3.0 - MySQL Community Server - GPL
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Copiando estrutura do banco de dados para dbfleetctrl
CREATE DATABASE IF NOT EXISTS `dbfleetctrl` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `dbfleetctrl`;

-- Copiando estrutura para tabela dbfleetctrl.tbconsumo
CREATE TABLE IF NOT EXISTS `tbconsumo` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ID_VEICULO` int NOT NULL,
  `KM` decimal(9,2) NOT NULL,
  `VALOR` decimal(9,2) NOT NULL,
  `LITROS` decimal(9,2) NOT NULL,
  `DATA` date NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `id-veiculo_idx` (`ID_VEICULO`),
  CONSTRAINT `id-veiculo` FOREIGN KEY (`ID_VEICULO`) REFERENCES `tbveiculos` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;

-- Copiando dados para a tabela dbfleetctrl.tbconsumo: ~0 rows (aproximadamente)
INSERT INTO `tbconsumo` (`ID`, `ID_VEICULO`, `KM`, `VALOR`, `LITROS`, `DATA`) VALUES
	(4, 1, 40150.00, 200.00, 20.00, '2024-04-20');

-- Copiando estrutura para tabela dbfleetctrl.tbexecmanut
CREATE TABLE IF NOT EXISTS `tbexecmanut` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `DATA` date NOT NULL,
  `KM` decimal(9,2) NOT NULL,
  `VALOR` decimal(9,2) NOT NULL,
  `OBS` varchar(255) DEFAULT NULL,
  `ID_MANUTENCAO` int NOT NULL,
  `ID_EXECUTOR` int NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_tbExecManut_tbManutVeic1_idx` (`ID_MANUTENCAO`),
  KEY `fk_tbExecManut_tbusuarios1_idx` (`ID_EXECUTOR`),
  CONSTRAINT `fk_tbExecManut_tbManutVeic1` FOREIGN KEY (`ID_MANUTENCAO`) REFERENCES `tbmanutveic` (`ID`),
  CONSTRAINT `fk_tbExecManut_tbusuarios1` FOREIGN KEY (`ID_EXECUTOR`) REFERENCES `tbusuarios` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3;

-- Copiando dados para a tabela dbfleetctrl.tbexecmanut: ~6 rows (aproximadamente)
INSERT INTO `tbexecmanut` (`ID`, `DATA`, `KM`, `VALOR`, `OBS`, `ID_MANUTENCAO`, `ID_EXECUTOR`) VALUES
	(1, '2024-04-28', 45270.00, 15270.24, 'Teste\r\nTeste linha 2', 2, 1),
	(2, '2024-03-24', 55000.00, 250.00, '', 4, 1),
	(3, '2025-05-28', 55270.00, 1500.00, '', 2, 1),
	(4, '2026-06-28', 65270.00, 6700.00, '', 2, 1),
	(5, '2024-04-24', 70000.00, 250.00, 'Lavação completa', 4, 10),
	(6, '2024-05-24', 85000.00, 123.00, '', 4, 10);

-- Copiando estrutura para tabela dbfleetctrl.tbmanutencao
CREATE TABLE IF NOT EXISTS `tbmanutencao` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOME` varchar(45) NOT NULL,
  `DESCRICAO` varchar(255) NOT NULL,
  `FREQ_KM` decimal(9,2) NOT NULL,
  `FREQ_MESES` int NOT NULL,
  `ID_TIPO_MANUTENCAO` int NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_tbmanutencao_tbTipoManutencao1_idx` (`ID_TIPO_MANUTENCAO`),
  CONSTRAINT `fk_tbmanutencao_tbTipoManutencao1` FOREIGN KEY (`ID_TIPO_MANUTENCAO`) REFERENCES `tbtipomanutencao` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3;

-- Copiando dados para a tabela dbfleetctrl.tbmanutencao: ~4 rows (aproximadamente)
INSERT INTO `tbmanutencao` (`ID`, `NOME`, `DESCRICAO`, `FREQ_KM`, `FREQ_MESES`, `ID_TIPO_MANUTENCAO`) VALUES
	(1, 'Troca Pneus', 'Troca Dos Pneus\r\nBLABLABLA', 50000.00, 120, 2),
	(2, 'Troca de óleo', 'Troca do óleo e filtro de óleo, gasolina e ar. Fazer uma verificação se precisa trocar o da gasolina também e uma limpeza geral;', 10000.00, 13, 1),
	(4, 'Lavação', 'Lavação E Limpeza Geral Do Carro', 15000.00, 1, 3),
	(5, 'Higienização Ar Condicionado', 'Higienização Completa Do Sistema De Ar Condicionado Do Veículo', 60000.00, 12, 1);

-- Copiando estrutura para tabela dbfleetctrl.tbmanutveic
CREATE TABLE IF NOT EXISTS `tbmanutveic` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ID_MANUTENCAO` int NOT NULL,
  `ID_VEICULO` int NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_tbmanutencao_tbtipo_manutencao1_idx` (`ID_MANUTENCAO`),
  KEY `fk_tbmanutencao_tbveiculos1_idx` (`ID_VEICULO`),
  CONSTRAINT `fk_tbmanutencao_tbtipo_manutencao1` FOREIGN KEY (`ID_MANUTENCAO`) REFERENCES `tbmanutencao` (`ID`),
  CONSTRAINT `fk_tbmanutencao_tbveiculos1` FOREIGN KEY (`ID_VEICULO`) REFERENCES `tbveiculos` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb3;

-- Copiando dados para a tabela dbfleetctrl.tbmanutveic: ~10 rows (aproximadamente)
INSERT INTO `tbmanutveic` (`ID`, `ID_MANUTENCAO`, `ID_VEICULO`) VALUES
	(2, 2, 1),
	(4, 4, 1),
	(5, 5, 1),
	(6, 2, 3),
	(7, 4, 3),
	(8, 2, 2),
	(9, 4, 2),
	(13, 1, 17),
	(14, 2, 17),
	(15, 4, 17);

-- Copiando estrutura para tabela dbfleetctrl.tbnvacesso
CREATE TABLE IF NOT EXISTS `tbnvacesso` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOME` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ADD` tinyint(1) DEFAULT NULL,
  `REMOVE` tinyint(1) DEFAULT NULL,
  `EDIT` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

-- Copiando dados para a tabela dbfleetctrl.tbnvacesso: ~2 rows (aproximadamente)
INSERT INTO `tbnvacesso` (`ID`, `NOME`, `ADD`, `REMOVE`, `EDIT`) VALUES
	(1, 'Administrador', 1, 1, 1),
	(2, 'Usuário', 0, 0, 0);

-- Copiando estrutura para tabela dbfleetctrl.tbtipomanutencao
CREATE TABLE IF NOT EXISTS `tbtipomanutencao` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOME` varchar(45) NOT NULL,
  `IMAGEM` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

-- Copiando dados para a tabela dbfleetctrl.tbtipomanutencao: ~3 rows (aproximadamente)
INSERT INTO `tbtipomanutencao` (`ID`, `NOME`, `IMAGEM`) VALUES
	(1, 'PREVENTIVA', './images/manut_plan.png'),
	(2, 'CORRETIVA', './images/manut_corretiva.png'),
	(3, 'OUTROS', './images/manut_outros.png');

-- Copiando estrutura para tabela dbfleetctrl.tbtipo_veiculo
CREATE TABLE IF NOT EXISTS `tbtipo_veiculo` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `TIPO` varchar(45) NOT NULL,
  `IMAGEM` varchar(45) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3;

-- Copiando dados para a tabela dbfleetctrl.tbtipo_veiculo: ~3 rows (aproximadamente)
INSERT INTO `tbtipo_veiculo` (`ID`, `TIPO`, `IMAGEM`) VALUES
	(1, 'CARRO', 'carro.png'),
	(2, 'MOTO', 'moto.png'),
	(3, 'CAMINHÃO', 'caminhao.png');

-- Copiando estrutura para tabela dbfleetctrl.tbusuarios
CREATE TABLE IF NOT EXISTS `tbusuarios` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOME` varchar(45) NOT NULL,
  `DT_NASCIMENTO` date NOT NULL,
  `VALIDADE_CNH` date NOT NULL,
  `USUARIO` varchar(45) NOT NULL,
  `SENHA` varchar(45) NOT NULL,
  `EMAIL` varchar(45) NOT NULL,
  `ID_NV_ACESSO` int NOT NULL,
  `IMAGE` varchar(120) NOT NULL DEFAULT './images/uploads/users/default.png',
  PRIMARY KEY (`ID`),
  KEY `fk_tbUsuarios_tbNvAcesso_idx` (`ID_NV_ACESSO`),
  CONSTRAINT `fk_tbUsuarios_tbNvAcesso` FOREIGN KEY (`ID_NV_ACESSO`) REFERENCES `tbnvacesso` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb3;

-- Copiando dados para a tabela dbfleetctrl.tbusuarios: ~5 rows (aproximadamente)
INSERT INTO `tbusuarios` (`ID`, `NOME`, `DT_NASCIMENTO`, `VALIDADE_CNH`, `USUARIO`, `SENHA`, `EMAIL`, `ID_NV_ACESSO`, `IMAGE`) VALUES
	(1, 'Jackson Eduardo Da Veiga', '1995-03-18', '2025-05-25', 'jackson', 'jacko(1', 'jacko.gt@hotmail.com', 1, './images/uploads/users/01-05-2024_04-14-jackson.jpg'),
	(7, 'João', '2000-05-18', '2024-03-15', 'joao', 'joao', 'jao@gmail.com', 2, './images/uploads/users/01-05-2024_04-58-joao'),
	(8, 'Axy', '2024-05-22', '2024-05-28', 'aaaa', 'aaa', 'aaa', 1, './images/uploads/users/default.png'),
	(9, 'Eliane', '1996-06-17', '2025-05-25', 'eliane', 'eliane', 'eliane@gmail.com', 2, './images/uploads/users/01-05-2024_05-21-eliane.jpg'),
	(10, 'Maria', '2021-05-11', '2015-02-15', 'maria', 'maria', 'maria@gmail.com', 2, './images/uploads/users/01-05-2024_05-03-maria');

-- Copiando estrutura para tabela dbfleetctrl.tbveiculos
CREATE TABLE IF NOT EXISTS `tbveiculos` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `ID_TIPO` int NOT NULL,
  `MARCA` varchar(45) NOT NULL,
  `MODELO` varchar(45) NOT NULL,
  `ANO` int NOT NULL,
  `PLACA` varchar(8) NOT NULL,
  `KM_INICIAL` decimal(9,2) NOT NULL,
  `VALOR` decimal(9,2) NOT NULL,
  `DT_CADASTRO` date DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `TIPO_VEICULO_idx` (`ID_TIPO`),
  CONSTRAINT `TIPO_VEICULO` FOREIGN KEY (`ID_TIPO`) REFERENCES `tbtipo_veiculo` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb3;

-- Copiando dados para a tabela dbfleetctrl.tbveiculos: ~6 rows (aproximadamente)
INSERT INTO `tbveiculos` (`ID`, `ID_TIPO`, `MARCA`, `MODELO`, `ANO`, `PLACA`, `KM_INICIAL`, `VALOR`, `DT_CADASTRO`) VALUES
	(1, 1, 'FIAT', 'UNO', 2010, 'ABC-1234', 40000.00, 24000.00, '2024-02-24'),
	(2, 2, 'Honda', 'BIZ', 2018, 'ABC-1233', 20000.00, 12000.00, '2024-03-23'),
	(3, 3, 'Mercedes', '1113', 2004, 'ABC-1122', 80000.00, 150000.00, '2024-04-16'),
	(17, 1, 'Volkswagen', 'Gol', 2018, 'AAA-9874', 20000.00, 80000.00, '2024-05-07'),
	(18, 1, 'Hyundai', 'HB20', 2015, 'AAA-8564', 0.00, 85000.00, '2024-05-07'),
	(19, 1, 'Chevrolet', 'Onix', 2019, 'AXD-5846', 15000.00, 60000.00, '2024-05-07'),
	(20, 1, 'Volkswagen', 'Golf', 2020, 'AFD-5874', 15000.00, 35780.00, '2024-05-07'),
	(21, 1, 'Volkswagen', 'Voyage', 2017, 'ARD-8547', 25000.00, 97500.00, '2024-05-07');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
