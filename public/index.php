<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Trabalho 9 de Banco de Dados I</title>

        <!--Styles -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">

        <!-- Scripts -->
        <script type="text/javascript" src="js/jquery-2.1.4.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/Application.js"></script>
        <script type="text/javascript" src="js/app.js"></script>

    </head>
    <body>
        <div class="container">
            <div class="page-header">
                <h1>9º Trabalho de Bando de Dados</h1>
            </div>

            <div id="main-panel" class="panel panel-default">
                <div class="panel-heading">
                    <span class="panel-title">Livro de Visitas</span>
                    <small>(<span class="total">0</span> assinaturas)</small>
                    <button class="btn btn-primary btn-sm new-guestbook">
                        <span class="glyphicon glyphicon-check" aria-hidden="true"></span>
                        Assinar
                    </button>
                </div>
                <ul id="visitas-list" class="list-group"></ul>
                <ul class="list-group empty-message">
                    <li class="list-group-item">
                        Nenhuma visita registrada
                    </li>
                </ul>
            </div>

        </div>
        <div id="footer">Desenvolvido por <b>Edwino Stein</b></div>

        <div id="form-template" style="display: none">
            <form class="template-container">
                <div class="form-group">
                    <label class="control-label" for="nome-input">Nome</label>
                    <input name="nome" type="text" class="form-control" id="nome-input" placeholder="Seu nome aqui" maxlength="250">
                </div>
                <div class="form-group">
                    <label class="control-label" for="localizacao-input">Localização</label>
                    <input name="localizacao" type="text" class="form-control" id="localizacao-input" placeholder="Onde você está" maxlength="45">
                </div>
                <div class="form-group">
                    <label class="control-label" for="mensagem-input">Mensagem</label>
                    <textarea  id="mensagem-input" name="mensagem" class="form-control" rows="3" placeholder="Deixe sua mensagem"></textarea>
                </div>
            </form>
        </div>
    </body>
</html>
