-- Cria a tabela guestbook
CREATE TABLE `meu_site`.`guestbook` ( `id` INT(5) UNSIGNED ZEROFILL NULL AUTO_INCREMENT , `nome` VARCHAR(255) NOT NULL , `localizacao` VARCHAR(50) NOT NULL , `mensagem` TEXT NOT NULL , `data` DATETIME NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
