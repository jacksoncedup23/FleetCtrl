<?php
    //Consulta e retorna a lista com as manutenções cadastradas para o veículo
	include_once("conexao.php");
    if (isset($_POST['id'])){
        $id = $_POST['id'];
    } else
    {
        $id = 1;
    }
    
    $SQL_Consulta = "SELECT u.*, a.ID as 'IDNIVEL', a.NOME as 'NIVEL', DATEDIFF(u.VALIDADE_CNH,current_date) as DIAS_VENC 
                    FROM 
                        tbUsuarios u,tbNvAcesso a 
                    WHERE u.ID_NV_ACESSO = a.ID and u.ID=$id";
    $results = mysqli_query($conn, $SQL_Consulta);
    $result = mysqli_fetch_assoc($results);


    $dateCNH = DateTime::createFromFormat('Y-m-d', $result['VALIDADE_CNH'])->format('d/m/Y');
    if($result['DIAS_VENC']<0)
    {
        $vencido = 2;
    } else if ($result['DIAS_VENC']<$param_dias_vencendo)
    {
        $vencido = 1;
    } else
    {
        $vencido = 0;
    }
?>

<div class="modal-header">
						<h1 class="modal-title fs-5" id="TitleConsulta"><?php echo $result['NOME']; ?></h1>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<div class="clearfix">
							<img src="<?php echo $result['IMAGE']; ?>" class="userImgView col-md-6 float-md-end mb-3 ms-md-3" alt="...">
							<?php
								$dateNasc = DateTime::createFromFormat('Y-m-d', $result['DT_NASCIMENTO'])->format('d/m/Y');

							?>

							<div class="UserCod">
                                <strong>Código: </strong><?php echo $result['ID']; ?>
                            </div>
							<div class="UserNome">
                                <strong>Nome: </strong><?php echo $result['NOME']; ?>
                            </div>
							<div class="UserUsuario"><strong>Usuário: </strong><?php echo $result['USUARIO']; ?></div>
							
							<div class="UserDt"><strong>Data de Nascimento: </strong><?php echo $dateNasc; ?></div>
							<div class="Useremail"><strong>E-mail: </strong><?php echo $result['EMAIL']; ?></div>
							<br>
							<div class="UserDt"><strong>Validade da CNH: </strong>
								<span <?php if($vencido==2) { echo 'style="color:red;"';} ?>>
								<?php 
									echo $dateCNH; 
									if($vencido==1) 
									{
										echo '<img src="./images/alerta.png" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="CNH vencendo em menos de um mês!">';
									} else if($vencido==2)
									{
										echo '<img src="./images/atencao.png" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="CNH vencida!">';
									} else
									{
										echo '<img src="./images/valido.png" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="CNH válida!">';
									}
								?>
								</span>
							</div>
							<br>
							
							<div class="UserNvAccesso"><strong>Nível de acesso: </strong><?php echo $result['NIVEL']; ?></div>
								
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
					</div>