<?php
    //Consulta e retorna a lista com as manutenções cadastradas para o veículo
	include_once("conexao.php");
    if (isset($_POST['id_veiculo'])){
        $id_veiculo = $_POST['id_veiculo'];
    } else
    {
        $id_veiculo = null;
    }
    
    $SQL_Consulta = "SELECT v.*, tp.IMAGEM, tp.TIPO as NM_TIPO FROM tbVeiculos v, tbtipo_veiculo tp WHERE v.ID_TIPO = tp.ID AND v.ID = $id_veiculo";
    $results = mysqli_query($conn, $SQL_Consulta);
    $result = mysqli_fetch_assoc($results)
?>

<div class="modal-content">
    <div class="modal-header">
        <h1 class="modal-title fs-5" id="TitleConsulta"><?php echo $result['MARCA'] . " - " . $result['MODELO'] . " | " . $result['ANO']; ?></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="clearfix">
                <img src="./images/<?php echo $result['IMAGEM']; ?>" class="col-md-6 float-md-end mb-3 ms-md-3" alt="...">
                <div class="VeicCod"><strong>Código: </strong><?php echo $result['ID']; ?></div>
                <div class="VeicTipo"><strong>Tipo: </strong><?php echo $result['NM_TIPO']; ?></div>	
                <div class="VeicMarca"><strong>Marca: </strong><?php echo $result['MARCA']; ?></div>
                <div class="VeicModelo"><strong>Modelo: </strong><?php echo $result['MODELO']; ?></div>
                <div class="VeicAno"><strong>Ano: </strong><?php echo $result['ANO']; ?></div>
                <div class="VeicPlaca"><strong>Placa: </strong><?php echo $result['PLACA']; ?></div>
                <div class="VeicKMInit"><strong>KM Inicial: </strong><?php echo $result['KM_INICIAL']; ?></div>
                <div class="VeicKMAtual"><strong>KM Atual: </strong><?php echo $result['KM_INICIAL']; ?></div>
                <div class="VeicKMAtual"><strong>Cadastrado em: </strong><?php echo formatDate($result['DT_CADASTRO']); ?></div>
        </div>
        <br>
    </div>

    <div class="modal-header">
        <h5 class="modal-title fs-5" id="TitleConsulta">Manutenções</h5>
    </div>
    <div class="modal-body">
        <div class="accordion" id="accordionExample">
            <!-- Lista de manutenções -->
            <?php 
                $SQL_MANUTENCOES = "SELECT manut.*, manutveic.ID as ID_MANUT_VEIC, (SELECT COUNT(*) FROM tbexecmanut execs WHERE execs.ID_MANUTENCAO=manutveic.ID) as QT_MANUT FROM tbmanutencao manut, tbmanutveic manutveic WHERE manut.ID = manutveic.ID_MANUTENCAO AND manutveic.ID_VEICULO = " . $result['ID'];
                
                
                $Manutencoes = mysqli_query($conn, $SQL_MANUTENCOES);
                $cont = 1;
                $qtManutencoes = 0;
                while($manutencao = mysqli_fetch_assoc($Manutencoes)){
                    $qtManutencoes ++;
                ?>
            
                <div class="accordion-item">
                    <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#manut_group_<?php echo $cont . "_" . $result['ID']; ?>" aria-expanded="true" aria-controls="collapseOne">
                        <?php echo $manutencao['NOME'] . " | " . $manutencao['QT_MANUT'] . " execuções"; ?>
                    </button>
                    </h2>
                    <div id="manut_group_<?php echo $cont . "_" . $result['ID']; ?>" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalManutencao" data-whatever="<?php echo $result['ID']; ?>" data-whatevermarca="<?php echo $result['MARCA']; ?>"  data-whatevermodelo="<?php echo $result['MODELO']; ?>" data-whateverano="<?php echo $result['ANO']; ?>"  data-whatevermanutid="<?php echo $manutencao['ID']; ?>" ><img src="./images/manutencao.png"> Executar manutenção</button>
                    <br/>
                    <br/>    
                        <?php 
                            $SQL_ManutExec = "SELECT exec.*, usr.NOME as RESP FROM tbexecManut exec, tbUsuarios usr WHERE usr.ID=exec.ID_EXECUTOR AND exec.ID_MANUTENCAO = " . $manutencao['ID_MANUT_VEIC'] . " ORDER BY exec.DATA DESC, exec.KM DESC LIMIT 1";
                            if ($showSQL) {echo $SQL_ManutExec;}
                            $ExecManutencoes = mysqli_query($conn, $SQL_ManutExec);
                            $encontrou = false;
                            while($ExecManutencao = mysqli_fetch_assoc($ExecManutencoes)){
                                $encontrou = true;
                        ?>
                        <!-- Cria um container para cada manutenção executada -->
                            <div class="container">
                                <div class="row mb-3 border-bottom">
                                    <h5>Última manutenção realizada:</h5>
                                </div>
                                <div class="row align-items-start">
                                    <div class="col-3">
                                    <strong>Data:</strong>
                                    </div>
                                    <div class="col-3">
                                        <?php 
                                            //Converter a data
                                            $dateManutExec = DateTime::createFromFormat('Y-m-d', $ExecManutencao['DATA'])->format('d/m/Y');
                                            echo $dateManutExec;
                                        ?>
                                    </div>
                                    <div class="col-2">
                                    <strong>KM:</strong>
                                    </div>
                                    <div class="col-4">
                                        <?php 
                                            echo number_format($ExecManutencao['KM'],0,",",".");
                                        ?>
                                    </div>
                                </div>
                                <div class="row align-items-start">
                                    <div class="col-3">
                                    <strong>Responsável:</strong>
                                    </div>
                                    <div class="col-9">
                                        <?php 
                                            echo $ExecManutencao['RESP'];
                                        ?>
                                    </div>
                                </div>
                                <div class="row align-items-start">
                                    <div class="col-3">
                                    <strong>Valor:</strong>
                                    </div>
                                    <div class="col-9">
                                        <?php 
                                            echo "R$ " . number_format($ExecManutencao['VALOR'],2,",",".");
                                        ?>
                                    </div>
                                </div>
                                <div class="row align-items-start">
                                    <div class="col">
                                    <strong>Observações:</strong>
                                    </div>
                                </div>
                                <div class="row align-items-start">
                                    <div class="col">
                                        <?php 
                                            echo $ExecManutencao['OBS'];
                                        ?>
                                    </div>
                                </div>
                            </div>
                        <!-- Fim do conteiner de manutenção executada -->
                        <?php 
                            }
                            if(!$encontrou) {
                                ?>
                                    <div class="container">
                                        <div class="row">
                                            <h6>Nenhuma manutenção realizada!</h6>
                                        </div>
                                    </div>
                                <?php
                            }
                        ?>
                    </div>
                    </div>
                </div>
            <?php 
                $cont ++;
                }
                if ($qtManutencoes==0) {
                    ?>
                        <div class="container">
                            <div class="row">
                                <h6>Nenhuma manutenção cadastrada!</h6>
                                <a href="manutencao.php?vehicleid=<?php echo $result['ID']; ?>"><button type="button" class="btn btn-primary"><img src="./images/add_maintenance.png"> Definir manutenções</button></a>
                            </div>
                        </div>
                    <?php
                }
            ?>
        </div>								
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
    </div>
</div>
