<?php

    include_once("conexao.php");
    $idMsg = 13;

    if ((isset($_POST["cbADD"]) or isset($_POST["cbREM"])) and isset($_POST["ID_VEIC"])) {
        $veiculo = mysqli_real_escape_string($conn, $_POST['ID_VEIC']);
        
        foreach ($_POST["cbADD"] as $linha) {
            
            $SQL = "INSERT INTO tbManutVeic (ID_MANUTENCAO, ID_VEICULO) VALUES ('$linha', '$veiculo')";
            //echo "$SQL<hr>";
            $result = mysqli_query($conn, $SQL) or die("Erro");
            if (!$result) {
                $idMsg = 14;
            }
        }

        foreach ($_POST["cbREM"] as $linha) {
            
            $SQL = "DELETE FROM tbManutVeic WHERE ID_MANUTENCAO = '$linha' AND ID_VEICULO = '$veiculo'";
            echo "$SQL<hr>";
            $result = mysqli_query($conn, $SQL) or die("Erro");
            if (!$result) {
                $idMsg = 14;
            }
        }

    }

?>

<!DOCTYPE html>
        <html lang="pt-br">
            <head>
                <meta charset="utf-8">
            </head>
        
            <body> <?php
                echo "<META HTTP-EQUIV=REFRESH CONTENT = '0;URL=veiculos.php?msg=$idMsg&open_vehicle=$veiculo'>";	
                ?>
            </body>
        </html>