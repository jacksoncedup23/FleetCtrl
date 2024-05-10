<?php
	include_once("conexao.php");
    if(isset($_POST['submit']))
        {
            $id = $_POST['id'];
            $nome = ucwords(strtolower($_POST['nome']));
            $dt_nascimento = $_POST['dtnasc'];
            $dt_validade = $_POST['dtcnh'];
            $usuario = strtolower($_POST['username']);		
            $senha = trim($_POST['senha']);
            $email = strtolower($_POST['email']);
            $nivel = $_POST['nvacesso'];

            $nomeArquivo = $_FILES['file']['name'];
            $tipo = $_FILES['file']['type'];
            $tempName = $_FILES['file']['tmp_name'];
            $tamanho = $_FILES['file']['size'];

            $erro_image = 0; //Limpa erro na imagem
            $imgFile = "./images/uploads/users/default.png";
            if (!empty($nomeArquivo)) {
                $tamanhoMax = 1024 * 1024 * 3; //3Mb
                if ($tamanho > $tamanhoMax) {
                    $erro_image = 8; //Tamanho imagem inválido
                }
                $arquivosPermitidos = ["png", "jpg", "jpeg"];
                $ext = pathinfo($nomeArquivo, PATHINFO_EXTENSION);

                if (!in_array($ext,$arquivosPermitidos)) {
                    $erro_image = 9; //Formato de arquivo não suportado
                }

                

                $hoje = date("d-m-Y_h-i");
                $novoName = "./images/uploads/users/" .$hoje . "-" . $usuario . "." . $ext;

                if ($erro_image==8) {
                    //Ocorreu algum erro com o upload
                    echo "<script>alert('Tamanho da imagem maior que o máximo permitido (3Mb)!')</script>";
                } else if ($erro_image==9) {
                    //Ocorreu algum erro com o upload
                    echo "<script>alert('Formato de arquivo não suportado! Utilize uma imgem no formato PNG ou JPEG!')</script>";
                } else {
                    if(!move_uploaded_file($tempName,  $novoName)) {
                        echo "<script>alert('Erro ao carregar imagem!')</script>";
                        $novoName = "";
                        $imgFile = "./images/uploads/users/default.png";
                    } else {
                        $imgFile = "$novoName";
                    }

                }
            }
            

            $erro_repetido = 0;

            if (isset($id) and !empty($id)) 
                {
                    //Se foi informado um ID, então deve alterar o registro
                    $SQL = "UPDATE TBUSUARIOS SET NOME='$nome',DT_NASCIMENTO='$dt_nascimento',VALIDADE_CNH='$dt_validade', USUARIO='$usuario', EMAIL = '$email', ID_NV_ACESSO='$nivel'";
                    $SQL_CHECK = "SELECT COUNT(ID) as cont FROM TBUSUARIOS WHERE (NOME='$nome' or USUARIO='$usuario' or EMAIL='$email') and ID!=$id";
                    $erro_repetido = 7;

                    if(isset($senha) and !empty($senha)) 
                    {
                        $SQL = $SQL . ",SENHA = '$senha'";
                    }
                    if(isset($nomeArquivo) and !empty($nomeArquivo)) 
                    {
                        $SQL = $SQL . ",IMAGE = '$novoName'";
                    }
                    $SQL = $SQL . " WHERE ID = $id";

                    
                    
                }
                else
                {
                    //ID não informado, deve incluir um novo registro
                    echo $imgFile;
                    $SQL = "INSERT INTO TBUSUARIOS (NOME, DT_NASCIMENTO, VALIDADE_CNH, USUARIO, SENHA, EMAIL, ID_NV_ACESSO, IMAGE) VALUES ('$nome','$dt_nascimento', '$dt_validade', '$usuario', '$senha', '$email', '$nivel', '$imgFile')";
                    $SQL_CHECK = "SELECT COUNT(ID) as cont FROM TBUSUARIOS WHERE (NOME='$nome' or USUARIO='$usuario' or EMAIL='$email')";
                    $erro_repetido = 8;
                }

                //Executa SQL para verificar se o usuário, nome de usuário ou e-mail estão repetidos
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
                echo "<META HTTP-EQUIV=REFRESH CONTENT = '0;URL=usuarios.php?msg=$idMsg'>";	
                ?>
            </body>
        </html>
