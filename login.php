<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="style/login.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="style/standard.css" media="screen" />

    <title>Tela de login</title>

</head>
<body>
    <div class="login">
        <img class="logoImg" src="./images/logo.png" alt="">
        <h1>Login</h1>
        <form action="doLogin.php" method="POST">
            <div class="inputField">
                <input type="text" name="user" placeholder="Usuário" value="jackson">
            </div>
            <div class="inputField">
                <input type="password" name="senha" placeholder="Senha" value="jacko(1">
            </div>
            <?php
                if(isset($_GET['error'])) {
                    ?>
                    <div class="errorMsg">
                        <span class="errorTxt">Usuário ou senha inválido!</span>
                    </div>            
                <?php 
                    }
                ?>
            <input class="inputSubmit" type="submit" name="submit" value="Entrar">
            <br>
            <br>
            <a href="index.php"><input class="backBtn" type="button" name="voltar" value="Votlar"></a>
        </form>
    </div>
</body>
</html>