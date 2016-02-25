
-- Cria o banco de dados
CREATE DATABASE `meu_site`;

-- Cria o usuário do banco de dados
CREATE USER 'bd_user'@'%' IDENTIFIED WITH mysql_native_password;GRANT USAGE ON *.* TO 'bd_user'@'%' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;SET PASSWORD FOR 'bd_user'@'%' = PASSWORD('123456');GRANT ALL PRIVILEGES ON `meu_site`.* TO 'bd_user'@'%';
