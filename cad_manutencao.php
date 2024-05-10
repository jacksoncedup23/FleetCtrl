<?php
	include_once("conexao.php");
    if(isset($_POST['submit']))
        {
            $id = $_POST['id'];
            $nome = ucwords($_POST['nome']);
            $desc = ucwords($_POST['desc']);
            $id_tipo = $_POST['tipo-manut'];
            $freqkm = $_POST['freq-km'];		
            $freqmes = $_POST['freq-mes'];
            $erro_repetido = 0;

            if (isset($id) and !empty($id)) 
                {
                    //Se foi informado um ID, então deve alterar o registro
                    $SQL = "UPDATE tbmanutencao SET NOME='$nome',DESCRICAO='$desc',FREQ_KM='$freqkm', FREQ_MESES='$freqmes', ID_TIPO_MANUTENCAO = '$id_tipo'  WHERE ID = $id";
                    $SQL_CHECK = "SELECT COUNT(ID) as cont FROM tbmanutencao WHERE (NOME='$nome' and ID_TIPO_MANUTENCAO='$id_tipo') and ID!=$id";
                    $erro_repetido = 7;                

                }
                else
                {
                    //ID não informado, deve incluir um novo registro
                    $SQL = "INSERT INTO tbmanutencao (NOME, DESCRICAO, FREQ_KM, FREQ_MESES, ID_TIPO_MANUTENCAO) VALUES ('$nome','$desc', '$freqkm', '$freqmes', '$id_tipo')";
                    $SQL_CHECK = "SELECT COUNT(ID) as cont FROM tbmanutencao WHERE (NOME='$nome' and ID_TIPO_MANUTENCAO='$id_tipo')";
                    $erro_repetido = 8;
                }

                //Executa SQL para verificar se o usuário, nome de usuário ou e-mail estão repetidos
                if ($showSQL) {
                    echo "<div><strong>Consulta SQL Verificação:</strong><br> " . $SQL_CHECK . "</div><hr>";
                }
                $result = mysqli_query($conn, $SQL_CHECK);
                $result = mysqli_fetch_assoc($result);
                if ($result['cont']>0) {
                    //Define a mensagem de erro como erro por duplicidade de dados
                    $idMsg = $erro_repetido;
                }
                else
                {
                    if ($showSQL) {
                        echo "<div><strong>Consulta SQL Update:</strong><br> " . $SQL . "</div><hr>";
                    }
                    $result = mysqli_query($conn, $SQL) or die("Erro");

                    //Se foi submitado / executado alguma instrução SQL, verifica se funcionou para mostrar uma mensagem
                    if($result==1 )
                        {
                            if (empty($id))
                            {
                                //ID vazio - Novo usuário | Mensagem 4
                                $idMsg = 5;
                            }
                            else
                            {
                                //ID informado - Alterar usuário | Mensagem 3
                                $idMsg = 3;
                            }
                            
                        }
                        else
                        {
                            if (empty($id))
                            {
                                //ID vazio - Erro Novo usuário | Mensagem 4
                                $idMsg = 6;
                            }
                            else
                            {
                                //ID informado - Erro Alterar usuário | Mensagem 3
                                $idMsg = 4;
                            }
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
                echo "<META HTTP-EQUIV=REFRESH CONTENT = '0;URL=manutencao.php?msg=$idMsg'>";	
                ?>
            </body>
        </html>
