<?php
	include_once("conexao.php");
    if(isset($_POST['submit']))
        {
            $id = $_POST['id'];
            $tipo = $_POST['tpveic'];
            $marca = ucwords($_POST['marca']);
            $modelo = ucwords($_POST['modelo']);		
            $ano = $_POST['ano'];
            $placa = strtoupper($_POST['placa']);
            $kminit = $_POST['kminit'];  //Não será editável!
            $valor = $_POST['valor'];  //Não será editável!
            $erro_repetido = 0;

            if (isset($id) and !empty($id)) 
                {
                    //Se foi informado um ID, então deve alterar o registro
                    $SQL = "UPDATE tbveiculos SET ID_TIPO='$tipo',MARCA='$marca', MODELO='$modelo', ANO = '$ano', PLACA='$placa'";
                    $SQL_CHECK = "SELECT COUNT(ID) as cont FROM tbveiculos WHERE (PLACA='$placa') and ID!=$id";
                    $erro_repetido = 7;
                    $SQL = $SQL . " WHERE ID = $id";				
                }
                else
                {
                    //ID não informado, deve incluir um novo registro
                    $SQL = "INSERT INTO tbveiculos (ID_TIPO, MARCA, MODELO, ANO, PLACA, KM_INICIAL, VALOR, DT_CADASTRO) VALUES ('$tipo','$marca','$modelo', '$ano', '$placa', '$kminit', '$valor', current_date)";
                    $SQL_CHECK = "SELECT COUNT(ID) as cont FROM tbveiculos WHERE (PLACA='$placa')";
                    $erro_repetido = 8;
                }

                //Executa SQL para verificar se existe repetição
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
                                //ID vazio - Novo registro | Mensagem 4
                                $idMsg = 5;
                            }
                            else
                            {
                                //ID informado - Alterar registro | Mensagem 3
                                $idMsg = 3;
                            }
                            
                        }
                        else
                        {
                            if (empty($id))
                            {
                                //ID vazio - Erro Novo registro | Mensagem 4
                                $idMsg = 6;
                            }
                            else
                            {
                                //ID informado - Erro Alterar registro | Mensagem 3
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
                echo "<META HTTP-EQUIV=REFRESH CONTENT = '0;URL=veiculos.php?msg=$idMsg'>";	
                ?>
            </body>
        </html>
