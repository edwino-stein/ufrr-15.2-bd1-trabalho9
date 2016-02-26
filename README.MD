Trabalho 9 de Bando de Dados I

***** Descição dos diretórios e arquivos *****

 - Lib: Classes utilizadas pela aplicação;
 - public/: DocumentRoot da aplicação;
 - public/index.php: Página principal da aplicação;
 - public/visitas.php: Página que fornece a API de comunicação com o bando de dados;
 - Sql/: Diretório que contém as SQLs que geram o bando de dados;
 - Sql/banco_de_dados.sql: Arquivo que gera o banco de dados e o usuário de acesso;
 - Sql/guestbook.sql: Arquivo que cria a tabela guestbook;
 - Visitas/: Diretório que contém o código da aplicação no servidor;
 - config.php: Arquivo que contém a configuração de bando de dados;
 - Visitas.php: Classe responsavel por inicializar a aplicação;


***** Como configurar e executar a aplicação *****

1 - Crie o banco de dados, usuário de acesso e a tabela a partir das SQLs do diretório Sql/;
2 - Caso necessite, altere os parametros de conexão com o bando de dados no arquivo config.php
3 - Configure o servidor HTTP para apontar para o diretório public/
4 - Acesse o arquivo index.php pelo navegador para acessar a aplicação.


***** Requesitos da aplicação *****

 - PHP 5.5.X ou superiror;
 - Servidor HTTP (Testado com servidor apache 2.4.12);
 - MySql 14.14;
 - Testado apenas no Linux Ubuntu 15.10;
