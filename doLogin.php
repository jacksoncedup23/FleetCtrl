<?php
    session_start();
    
    if((!isset($_SESSION['user']) == true))
    {
        unset($_SESSION['user']);
        unset($_SESSION['userID']);
        unset($_SESSION['userName']);
        header('Location: login.php');
    }

    $LogUser = $_SESSION['user'];
    // print_r($_REQUEST);
    if(isset($_POST['submit']) && !empty($_POST['user']) && !empty($_POST['senha']))
    {
        // Acessa
        include_once('conexao.php');
        $user = $_POST['user'];
        $senha = $_POST['senha'];

        // print_r('Email: ' . $email);
        // print_r('<br>');
        // print_r('Senha: ' . $senha);

        $sql = "SELECT * FROM tbusuarios WHERE USUARIO = '$user' and SENHA = '$senha'";

        $result = $conn->query($sql);
        $dadosUser = mysqli_fetch_assoc($result);
        

        if(mysqli_num_rows($result) < 1)
        {
            unset($_SESSION['user']);
            unset($_SESSION['userID']);
            unset($_SESSION['userName']);

            header('Location: login.php?error');
        }
        else
        {
            $_SESSION['user'] = $user;
            $_SESSION['userID'] = $dadosUser['ID'];
            $_SESSION['userName'] = $dadosUser['NOME'];
            header('Location: veiculos.php');
        }
    }
    else
    {
        // Não acessa
        header('Location: login.php');
    }
?>