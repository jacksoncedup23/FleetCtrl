<?php
	include_once("conexao.php");
	include_once("valida_user.php");
	function getPrm($prmName) {
		if (isset($_GET[$prmName])) {
			return $_GET[$prmName];
		}
		else
		{
			return "";
		}
	  }

use function PHPSTORM_META\map;
	//Verifica se está selecionando manutenções para um veículo
	if(getPrm("vehicleid")!="") {
		$veic_id = getPrm("vehicleid");
		$SQLVeic = "SELECT * FROM tbveiculos WHERE ID = $veic_id";
    	$veiculos = $conn->query($SQLVeic);
    
		$veiculos = mysqli_fetch_assoc($veiculos);
		$veic_nome = $veiculos['MARCA'] . " - " . $veiculos["MODELO"];

		$addManuVeic = true;

		if(!$LogUserEdit) {
			echo "<script>alert('O usuário $LogUserName não possui permissão para alterar as manutenções!')</script>";
			header('Location: veiculos.php?msg=15');
		}
		
		
	} else {
		$addManuVeic = false;
	}
	$mensagens = array(
		"",
		"Usuário removido com sucesso!",
		"Ocorreu um erro ao remover o usuário!",
		"Usuário alterado com sucesso!",
		"Ocorreu um erro ao alterar o usuário!",
		"Usuário criado com sucesso!",
		"Ocorreu um erro ao criar o novo usuário!",
		"Erro ao alterar usuário devido a duplicidade de algum dado!",
		"Erro ao inserir usuário devido a duplicidade de algum dado!");
	
	$toastColor = array(
			"",
			"success",
			"danger",
			"success",
			"danger",
			"success",
			"danger",
			"danger",
			"danger");

	$buscas_padrao = array(
		"VENCIDO" => " and DATEDIFF(VALIDADECNH,CURRENT_DATE) < 0",
		"EM DIA" => " and DATEDIFF(VALIDADECNH,CURRENT_DATE) >= " . $param_dias_vencendo,
		"VENCENDO" => " and DATEDIFF(VALIDADECNH,CURRENT_DATE) BETWEEN 0 AND " . ($param_dias_vencendo-1)
		);
	
	
	
	//Se nao teve nenhum formulário submitado, verifica se deve mostrar alguma mensagem
	if (isset($_GET["msg"])) {
		$idMsg = $_GET["msg"];
	}
	else
	{
		$idMsg = 0;
	}
	

	//Verifica a quantidade de páginas
	$SQLContPages = 'SELECT CEIL(COUNT(*) /' . $linesPerPage . ') as CONT, COUNT(*) as TOTAL FROM tbManutencao man, tbtipomanutencao tp WHERE man.ID_TIPO_MANUTENCAO=tp.ID  ';

	if (isset($_GET['search']))
	{
		if(isset($buscas_padrao[strtoupper($_GET['search'])])){
			$SQLContPages = $SQLContPages . $buscas_padrao[strtoupper($_GET['search'])]; 
		}
		else
		{
		$busca = str_replace(" ", "%", $_GET['search']);
		$SQLContPages = $SQLContPages . " and (man.NOME LIKE '%$busca%' or man.DESCRICAO LIKE '%$busca%' or tp.NOME LIKE '%$busca%')";
		}
	}

	$contador = $conn->query($SQLContPages);
    
    $contador = mysqli_fetch_assoc($contador);
    $pagesCount = $contador['CONT'];
	$registros = $contador['TOTAL'];

	//Verifica a pagina que está sendo exibida
	if (isset($_GET['page'])) {
		$page = $_GET['page'];
	}
	else
	{
		$page = 1;
	}
	if (!is_numeric($page))
		{
			$page = 1;
		}
	if ($page > $pagesCount and $pagesCount > 0)
		{
			$page = $pagesCount;
		}

	//Executa a consulta no banco de dados
	if ($addManuVeic) {
		//Se estiver adicionando manutenções, lista apenas as que não estão no veículo.
		//$SQL_Consulta = $SQL_Consulta . " AND ID NOT iN (SELECT a.ID_MANUTENCAO FROM tbManutVeic a WHERE a.ID_VEICULO = '" . $veic_id . "')";
		$SQL_Consulta = "SELECT * FROM (SELECT (SELECT COUNT(*) FROM tbExecManut ex WHERE ex.ID_MANUTENCAO = manVeic.ID) as CONT_MANUT, man.*, tipo.ID as TIPO_ID, tipo.NOME as TIPO, tipo.IMAGEM, CASE when manVeic.ID is null THEN 'ADD' ELSE 'REMOVE' END as ACT FROM tbManutencao man INNER JOIN tbTipoManutencao tipo ON (man.ID_TIPO_MANUTENCAO = tipo.ID) LEFT JOIN tbManutVeic manVeic ON (manVeic.ID_MANUTENCAO=man.ID AND manVeic.ID_VEICULO = $veic_id) WHERE 1=1";
	} else {
		$SQL_Consulta = "SELECT * FROM (SELECT man.*, tipo.ID as TIPO_ID, tipo.NOME as TIPO, tipo.IMAGEM, 'NONE' as ACT FROM tbManutencao man, tbTipoManutencao tipo WHERE man.ID_TIPO_MANUTENCAO = tipo.ID ";
	}
	
	$SQL_Consulta = $SQL_Consulta . ") a WHERE 1=1 ";
	
	if (isset($_GET['search']))
	{
		if(isset($buscas_padrao[strtoupper($_GET['search'])])){
			$SQL_Consulta = $SQL_Consulta . $buscas_padrao[strtoupper($_GET['search'])]; 
		}
		else
		{
		$busca = str_replace(" ", "%", $_GET['search']);
		$SQL_Consulta = $SQL_Consulta . " and (NOME LIKE '%$busca%' or DESCRICAO LIKE '%$busca%' or TIPO LIKE '%$busca%')";
		}
	}
	if(isset($_GET["order"])) 
	{
		$SQL_Consulta = $SQL_Consulta . "ORDER BY " . $_GET["order"];
		if (getPrm("dir")=="DESC")
		{
			$SQL_Consulta = $SQL_Consulta . " DESC";
		}
	} else
	If($addManuVeic) {
		$SQL_Consulta = $SQL_Consulta . "ORDER BY ACT ASC, ID ASC";
	}

	$SQL_Consulta = $SQL_Consulta . " LIMIT $linesPerPage OFFSET " . ($page - 1) * $linesPerPage;
	if ($showSQL) {
		echo "<div><strong>Consulta SQL:</strong><br> " . $SQL_Consulta . "</div><hr>";
		echo "<strong>ADICIONANDO MANUTENÇÃO NO VEÍCULO</strong> - ID: " . $veic_id . " - NOME: " . $veic_nome . "</div><hr>";
	}
	$result_users = mysqli_query($conn, $SQL_Consulta);

	
	

	
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>MANUTENÇÕES | FleetCtrl</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

		<link rel="stylesheet" type="text/css" href="style/manutencao.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="style/standard.css" media="screen" />
			
	</head>
	<body>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
	
	<!-- BARRA DE NAVEGAÇÃO ---------------------------------------------------------------------------------- -->
	<nav class="<?php echo $nav_class;?>">
		<div class="container-fluid">
			<a class="navbar-brand" href="#">FleetCtrl</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav me-auto mb-2 mb-lg-0">
					<li class="nav-item">
					<a class="nav-link" aria-current="page" href="index.php">Home</a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link" aria-current="page" href="usuarios.php">Usuários</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" aria-current="page" href="veiculos.php">Veículos</a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link active dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							Manutenção
						</a>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item <?php echo $LogUserAdd==0? "disabled" : ""; ?>" href="#" data-bs-toggle="modal" data-bs-target="#ModalNovo">Novo</a></li>
							<li><hr class="dropdown-divider"></li>
							<li class="dropdown-header ">Filtrar</li>
							<li><a class="dropdown-item" href="usuarios.php?search=em+dia">CNH em dia</a></li>
							<li><a class="dropdown-item" href="usuarios.php?search=vencendo">CNH vencendo</a></li>
							<li><a class="dropdown-item" href="usuarios.php?search=vencido">CNH vencida</a></li>
							<li><a class="dropdown-item" href="manutencao.php">Mostrar todos</a></li>
						</ul>
					</li>
					<li class="nav-item">
						<a class="nav-link disabled" aria-disabled="true">Disabled</a>
					</li>
			</ul>
			<form class="d-flex" role="search" method="get" action="?vehicleid=1&">
				<input class="form-control me-2" type="search" id="search" name="search" placeholder="Buscar" aria-label="Buscar" value="<?php if(isset($_GET["search"])) {echo $_GET["search"];}?>">
				<?php 
					if($addManuVeic) {
						echo '<input type="hidden" name="vehicleid" id="vehicleid" value="' . $veic_id . '">';
					}
						
				?>
				<button class="<?php echo $nav_class_btn_busca;?>" type="submit">Buscar</button>

			</form>
			<div id="btnNotifications">
				<button type="button" class="btn position-relative notifications" data-bs-toggle="offcanvas" data-bs-target="#notificationPanel" onclick="refreshNotifications()">
					<img src="./images/sino (1).png" alt="Notificações">
					<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
						<span id="showNotfCount">0</span>
						<span class="visually-hidden">unread messages</span>
					</span>
				</button>
			</div>
			<a onclick="user_sidebar()" data-bs-toggle="offcanvas" data-bs-target="#userSidebar" aria-controls="offcanvasRight"><img src="<?php echo $LogUserData['IMAGE']; ?>" class="userImgProf" alt="Imagem do usuário"></a>
			</div>
		</div>
	</nav>
	<!-- BARRA DE NAVEGAÇÃO ---------------------------------------------------------------------------------- -->
	<!-- DADOS DO USUÁRIO ------------------------------------------------------------------------------------ -->

	<div id="userSidebar" class="offcanvas offcanvas-end" tabindex="-1" aria-labelledby="userPanel">
		
	</div>
	<!-- FIM DADOS USUARIO ----------------------------------------------------------------------------------- -->
	<!-- Menu com as notificações do usuário -->
	<div class="offcanvas offcanvas-top " tabindex="-1" id="notificationPanel" aria-labelledby="offcanvasTopLabel">
		
	</div>
	<!-- Mensagem de exito/erro -->

		<div class="toastMsg">
			<div class="toast align-items-center text-bg-<?php echo $toastColor[$idMsg]; ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
				<div class="d-flex">
					<div class="toast-body">
					<?php echo $mensagens[$idMsg]; ?>
					</div>
					<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
				</div>
			</div>
		</div>


	<!-- --------------------------- -->
		<div class="container theme-showcase" role="main">
			<div class="bgPanel">
				<div class="headContent">
					<div class="page-header">
							<?php
								if($addManuVeic) {
									//Incluindo manutenções em veículo
									echo "<h1>Definir manutenções para o veículo</h1>";
									echo "<h5>$veic_nome [$veic_id]</h5>";

								} else {
									echo "<h1>Manutenções</h1>";
								}
							?>
					</div>
					<?php
						if(!$addManuVeic) {
					?>
					
						<div class="col-auto mb-4">
							<button type="button" class="btn btn-primary <?php echo $LogUserAdd==0? "d-none" : ""; ?>" data-bs-toggle="modal" data-bs-target="#ModalNovo" <?php echo $LogUserAdd==0? "disabled" : ""; ?>>Nova manutenção</button>
						</div>

					<?php } ?>
				</div>
				
				<div class="scroll">
					<div class="row">
						<div class="col-md-12">
							<form method="post" action="add_manutencao.php">
								<?php 
									if($addManuVeic) {
								?>
									<a href="veiculos.php"><button type="button" class="btn btn-secondary teste" name="submit">Voltar</button></a>
									<button type="submit" class="btn btn-primary" name="submit">Salvar</button>
								<?php 
									}
								?>		
								<div class="bgTable">
									<table class="table">
										<thead>
											<tr>
												<th><a href="?<?php if(isset($_GET["search"])) {echo "search=" . $_GET["search"] . "&";} if(isset($_GET["vehicleid"])) {echo "vehicleid=" . $_GET["vehicleid"] . "&";} ?>order=ID<?php if(getPrm("dir")!="DESC" and getPrm("order")=="ID"){ echo "&dir=DESC";}?>">#<?php if(getPrm("order")=="ID"){ if(getPrm("dir")!="DESC"){echo " ▼";} else {echo " ▲";}}?></a></th>
												<th><a href="?<?php if(isset($_GET["search"])) {echo "search=" . $_GET["search"] . "&";} if(isset($_GET["vehicleid"])) {echo "vehicleid=" . $_GET["vehicleid"] . "&";} ?>order=TIPO<?php if(getPrm("dir")!="DESC" and getPrm("order")=="TIPO"){ echo "&dir=DESC";}?>">Tipo<?php if(getPrm("order")=="TIPO"){ if(getPrm("dir")!="DESC"){echo " ▼";} else {echo " ▲";}}?></a></th>
												<th><a href="?<?php if(isset($_GET["search"])) {echo "search=" . $_GET["search"] . "&";} if(isset($_GET["vehicleid"])) {echo "vehicleid=" . $_GET["vehicleid"] . "&";} ?>order=NOME<?php if(getPrm("dir")!="DESC" and getPrm("order")=="NOME"){ echo "&dir=DESC";}?>">Nome<?php if(getPrm("order")=="NOME"){ if(getPrm("dir")!="DESC"){echo " ▼";} else {echo " ▲";}}?></a></th>
												<th><a href="?<?php if(isset($_GET["search"])) {echo "search=" . $_GET["search"] . "&";} if(isset($_GET["vehicleid"])) {echo "vehicleid=" . $_GET["vehicleid"] . "&";} ?>order=FREQ_KM<?php if(getPrm("dir")!="DESC" and getPrm("order")=="FREQ_KM"){ echo "&dir=DESC";}?>">Frequência (KM)<?php if(getPrm("order")=="FREQ_KM"){ if(getPrm("dir")!="DESC"){echo " ▼";} else {echo " ▲";}}?></a></th>
												<th><a href="?<?php if(isset($_GET["search"])) {echo "search=" . $_GET["search"] . "&";} if(isset($_GET["vehicleid"])) {echo "vehicleid=" . $_GET["vehicleid"] . "&";} ?>order=FREQ_MESES<?php if(getPrm("dir")!="DESC" and getPrm("order")=="FREQ_MESES"){ echo "&dir=DESC";}?>">Frequência (meses)<?php if(getPrm("order")=="FREQ_MESES"){ if(getPrm("dir")!="DESC"){echo " ▼";} else {echo " ▲";}}?></a></th>
												<th>Ação</th>
											</tr>
										</thead>
										<tbody>
											<?php 
												if($addManuVeic) {
													echo '<input type="hidden" name="ID_VEIC" id="ID_VEIC" value="' . $veic_id . '">';
												} 
												while($users = mysqli_fetch_assoc($result_users)){
												/*
												$dateCNH = DateTime::createFromFormat('Y-m-d', $users['VALIDADECNH'])->format('d/m/Y');
												if($users['DIAS_VENC']<0)
												{
													$vencido = 2;
												} else if ($users['DIAS_VENC']<$param_dias_vencendo)
												{
													$vencido = 1;
												} else
												{
													$vencido = 0;
												}*/
												?>
												<tr>
													
													<td class="align-middle"><?php echo $users['ID']; ?></td>
														
													<td class="align-middle">
														<?php
															//echo $users['TIPO']; 
															echo $users['TIPO'] . ' <img class=".iconImg" src="' . $users['IMAGEM'] . '" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' . $users['TIPO'] . '"> ';
														?>
													</td>
													<td class="align-middle"><span class="lnkConsulta" data-bs-toggle="modal" data-bs-target="#Consulta<?php echo $users['ID']; ?>"><?php echo $users['NOME']; ?></span></td>
													<td class="align-middle"><?php echo $users['FREQ_KM']; ?></td>
													<td class="align-middle"><?php echo $users['FREQ_MESES']; ?></td>
													<td class="align-middle">
													<?php if(!$addManuVeic) {?>
														<button type="button" class="btn btn-xs btn-primary" data-bs-toggle="modal" data-bs-target="#Consulta<?php echo $users['ID']; ?>"><img src="./images/visualizar.png"></button>
														<?php if($LogUserEdit==1) {
														//Permite editar
														?>
															<button type="button" class="btn btn-xs btn-warning" data-bs-toggle="modal" data-bs-target="#ModalEdit" data-whatever="<?php echo $users['ID']; ?>" data-whatevernome="<?php echo $users['NOME']; ?>" data-whatevertipo="<?php echo $users['TIPO_ID']; ?>" data-whateverddesc="<?php echo $users['DESCRICAO']; ?>" data-whateverfqkm="<?php echo $users['FREQ_KM']; ?>" data-whateverfqmeses="<?php echo $users['FREQ_MESES']; ?>"><img src="./images/editar.png"></button>
														<?php }?>
														<?php if($LogUserRemove==1) {
														//Permite editar
														?>
															<button type="button" class="btn btn-xs btn-danger" data-bs-toggle="modal" data-bs-target="#delete" data-whatever="<?php echo $users['ID']; ?>" data-whatevernome="<?php echo $users['NOME']; ?>"><img src="./images/excluir.png"></button>
														<?php }?>
														
													<?php } ?>
													<?php 
															if($addManuVeic) {
																$aux = '';
																	
																if($users['ACT']=='ADD') {
																	//CheckBox para Incluir
																	$cbName = "cbADD";
																	$lblClass = "btn-outline-success";
																	$nome = "Adicionar";
																} else if($users['ACT']=='REMOVE') {
																	//CheckBox para Incluir
																	$cbName = "cbREM";
																	$nome = "Remover";
																	if ($users['CONT_MANUT']>0){
																		//Já existem manutenções. Não pode excluir.
																		$aux = 'disabled';
																		$lblClass = "btn-outline-danger strk";
																	} else {
																		$aux = '';
																		$lblClass = "btn-outline-danger";
																	}
																}
																if (!empty($aux)) {
																	echo '<img src="./images/info_alert.png" class="info_remove" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Não pode ser removido pois já foram executadas manutenções">';
																} else {
																	echo '<img class="info_remove" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Liberado para adicionar">';
																}
																echo '<input ' . $aux . ' type="checkbox" class="btn-check" name="' . $cbName . '[]" id="cbx' . $users['ID'] . '" autocomplete="off"  value="' . $users['ID'] . '">';
																echo '<label " class="btn ' . $lblClass . '" for="cbx' . $users['ID'] . '">' . $nome . '</label><br>';
																
																//echo '<input class="form-check-input" type="checkbox" name="' . $cbName . '[]" id="cb' . $users['ID'] . '" value="' . $users['ID'] . '">';
																//echo '<label class="mx-3" for="cb' . $users['ID'] . '"> ' . $users['ID']  . '</label>';
																
															}	
														?>
													</td>
												</tr>
												<!-- Inicio Modal -->
												<div class="modal fade" id="Consulta<?php echo $users['ID']; ?>" tabindex="-1" aria-labelledby="TitleConsulta" aria-hidden="true">
													<div class="modal-dialog">
														<div class="modal-content">
														<div class="modal-header">
															<h1 class="modal-title fs-5" id="TitleConsulta"><?php echo $users['NOME']; ?></h1>
															<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
														</div>
														<div class="modal-body">

															<div class="UserCod"><strong>Código: </strong><?php echo $users['ID']; ?></div>
															<div class="UserNome"><strong>Tipo: </strong>
																<?php
																	echo $users['TIPO'] . ' <img class=".iconImg" src="' . $users['IMAGEM'] . '" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="' . $users['TIPO'] . '"> ';
																?>
															</div>	
															<div class="UserNome"><strong>Nome: </strong><?php echo $users['NOME']; ?></div>	
															<div class="UserUsuario"><strong>Descrição: </strong><?php echo $users['DESCRICAO']; ?></div>
															<div class="UserDt"><strong>Frequência (km): </strong><?php echo $users['FREQ_KM']; ?></div>
															<div class="Useremail"><strong>Frequência (meses): </strong>
																<?php 
																	echo $users['FREQ_MESES'] . " meses"; 
																	if($users['FREQ_MESES']>12) {
																		$ano = $users['FREQ_MESES']/12;
																		$anos = floor($ano);
																		$meses = ($ano-$anos) * 12;
																		

																		if($anos > 1) {
																			echo " | $anos anos";
																		} else
																		{echo " | $anos ano";}
																		if($meses > 1 ){
																			echo " e $meses meses";
																		} else if($meses>0) {
																			echo " e $meses mês";
																		}
																	}
																?>
															</div>
														</div>
														<div class="modal-footer">
															<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
														</div>
														</div>
													</div>
												</div>
												<!-- Fim Modal -->
											<?php } ?>
										</tbody>
									</table>
								</div>
								
							</form>
						</div>		
					</div>		
				</div>												
			</div>
				<div class="paginationPanel">
					<!-- Paginação -->
					<nav aria-label="...">
						<div class="container text-left">
							<div class="row align-items-start">
								<div class="col">
									<?php echo $registros . " registro(s)"; ?>
								</div>
								<div class="col">
									<ul class="pagination justify-content-end">
										<li class="page-item <?php if ($page <= 1) {echo "disabled";}?>">
											<a class="page-link" href="?page=<?php
															echo ($page-1) . "&";
															if(isset($_GET["search"])) {echo "search=" . $_GET["search"] . "&";}
															if(isset($_GET["vehicleid"])) {echo "vehicleid=" . $_GET["vehicleid"] . "&";}
															if(isset($_GET["order"])) {echo "order=" . $_GET["order"] . "&";}
															if(isset($_GET["dir"])) {echo "dir=" . $_GET["dir"] . "&";}
														?>">Anterior</a>
										</li>
										<?php
											$pg = 1;
											while ($pg <= $pagesCount) 
											{
												?>

												<li class="page-item <?php if ($page == $pg) {echo "active"; } ?>" aria-current="page">
													<a class="page-link" href="?page=
														<?php
															echo $pg . "&";
															if(isset($_GET["search"])) {echo "search=" . $_GET["search"] . "&";}
															if(isset($_GET["vehicleid"])) {echo "vehicleid=" . $_GET["vehicleid"] . "&";}
															if(isset($_GET["order"])) {echo "order=" . $_GET["order"] . "&";}
															if(isset($_GET["dir"])) {echo "dir=" . $_GET["dir"] . "&";}
														?>"><?php echo $pg; ?></a>
													
												</li>
											
										<?php
											$pg ++;
											}
										?>

										<li class="page-item <?php if ($page == $pagesCount or $pagesCount<=1) {echo "disabled";}?>">
											<a class="page-link" href="?page=<?php
															echo ($page+1) . "&";
															if(isset($_GET["search"])) {echo "search=" . $_GET["search"] . "&";}
															if(isset($_GET["vehicleid"])) {echo "vehicleid=" . $_GET["vehicleid"] . "&";}
															if(isset($_GET["order"])) {echo "order=" . $_GET["order"] . "&";}
															if(isset($_GET["dir"])) {echo "dir=" . $_GET["dir"] . "&";}
														?>">Próxima</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
						
					</nav>
				</div>
			<!-- FIM da paginação -->
		</div>


	
		<!-- Modal Excluir -->
		<div class="modal fade" id="delete" tabindex="-1" aria-labelledby="delete" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h1 class="modal-title fs-5" id="delete">Excluir manutenção</h1>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						Tem certeza que você deseja excluir a manutenção <strong id="nome"></strong>?
						
						
					</div>
					<div class="modal-footer">
						<!-- action="remove_user.php" -->
						<form method="POST" action="remove_manut.php" enctype="multipart/form-data">
						
							<input name="id" type="hidden" class="form-control" id="id-remove" value="">
							<button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancelar</button>
							<button type="submit" class="btn btn-danger">Excluir</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- --------------------------------------------------------------------------------------------------------------------------------------- -->
		<!-- Modal edição -->

		<div class="modal fade" id="ModalEdit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="staticBackdropLabel">Editar manutenção</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form method="POST" action="cad_manutencao.php" enctype="multipart/form-data">
						<div class="form-group">
							<label for="id-view-edt" class="control-label">Código:</label>
							<input name="id-view" id="id-view-edt" class="form-control" type="text" placeholder="Código"  disabled value="">
						</div>
						<div class="form-group">
							<label for="tipo-manut-edt" class="control-label">Tipo de manutenção:</label>
							<select name="tipo-manut" id="tipo-manut-edt" class="form-select" required>

								<option selected></option>
									<?php 
										$SQL_TP_MANUT = "SELECT * FROM tbTipoManutencao";
										$result_tp_manut = mysqli_query($conn, $SQL_TP_MANUT);
										while($tp_manut = mysqli_fetch_assoc($result_tp_manut)){ ?>
										<option value="<?php echo $tp_manut['ID']; ?>"><?php echo $tp_manut['NOME']; ?></option>
									<?php } ?>
							</select>
						</div>
						<div class="form-group">
							<label for="nome-edt" class="control-label">Nome:</label>
							<input name="nome" type="text" class="form-control" id="nome-edt" required>
						</div>
						<div class="form-group">
							<label for="desc-edt" class="control-label">Descrição:</label>
							<textarea name="desc" type="text" class="form-control" id="desc-edt" required></textarea>
						</div>
						<div class="form-group">
							<label for="freq-km-edt" class="control-label">Frequência (KM):</label>
							<input name="freq-km" type="number" class="form-control" id="freq-km-edt" min="1" max="9999999" required>
						</div>
						<div class="form-group">
							<label for="freq-mes-edt" class="control-label">Frequência (Meses):</label>
							<input name="freq-mes" type="number" class="form-control" id="freq-mes-edt" min="1" max="1200" required>
						</div>
						
						<input name="id" type="hidden" class="form-control" id="id-edt" value="">
			
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
					<button type="submit" class="btn btn-primary" name="submit" id="btn-salvar-edt">Salvar</button>

				</div>
				</form>
				</div>
			</div>
		</div>


	<!-- --------------------------------------------------------------------------------------------------------------------------------------- -->
	
	<!-- Modal Novo Usuário -->

		<div class="modal fade" id="ModalNovo" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h1 class="modal-title fs-5" id="staticBackdropLabel">Novo usuário</h1>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form id="form-new-manut" method="POST" action="cad_manutencao.php" enctype="multipart/form-data">
						<div class="form-group">
							<label for="id-view-edt" class="control-label">Código:</label>
							<input name="id-view" id="id-view-edt" class="form-control" type="text" placeholder="Código"  disabled value="">
						</div>
						<div class="form-group">
							<label for="tipo-manut-edt" class="control-label">Tipo de manutenção:</label>
							<select name="tipo-manut" id="tipo-manut-edt" class="form-select" required>

								<option selected></option>
									<?php 
										$SQL_TP_MANUT = "SELECT * FROM tbTipoManutencao";
										$result_tp_manut = mysqli_query($conn, $SQL_TP_MANUT);
										while($tp_manut = mysqli_fetch_assoc($result_tp_manut)){ ?>
										<option value="<?php echo $tp_manut['ID']; ?>"><?php echo $tp_manut['NOME']; ?></option>
									<?php } ?>
							</select>
						</div>
						<div class="form-group">
							<label for="nome-edt" class="control-label">Nome:</label>
							<input name="nome" type="text" class="form-control" id="nome-edt" required>
						</div>
						<div class="form-group">
							<label for="desc-edt" class="control-label">Descrição:</label>
							<textarea name="desc" type="text" class="form-control" id="desc-edt" required></textarea>
						</div>
						<div class="form-group">
							<label for="freq-km-edt" class="control-label">Frequência (KM):</label>
							<input name="freq-km" type="number" class="form-control" id="freq-km-edt" min="1" max="9999999" required>
						</div>
						<div class="form-group">
							<label for="freq-mes-edt" class="control-label">Frequência (Meses):</label>
							<input name="freq-mes" type="number" class="form-control" id="freq-mes-edt" min="1" max="1200" required>
						</div>
						
						<input name="id" type="hidden" class="form-control" id="id-edt" value="">
				
						
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
						<button type="submit" class="btn btn-primary" name="submit" id="submit">Salvar</button>

					</div>
					</form>
				</div>
			</div>
		</div>


	<!-- --------------------------------------------------------------------------------------------------------------------------------------- -->


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->

	<script>
		function user_sidebar() {
			$.post("query_user_sidebar.php", {}, function(x) { $("#userSidebar").html(x); } );
		}
		async function refreshNotifications() {
			await $.post("query_notifications.php", {}, function(x) { $("#notificationPanel").html(x); } );
			var notfCount = parseInt(document.getElementById('notfCount').innerHTML) >99 ? "99+" : parseInt(document.getElementById('notfCount').innerHTML)

			document.getElementById('showNotfCount').innerHTML = notfCount
			

		}
		
		refreshNotifications();

		function showToast() {
			var toastElList = [].slice.call(document.querySelectorAll('.toast'))
			var toastList = toastElList.map(function(toastEl) {
			return new bootstrap.Toast(toastEl)
			})
			toastList.forEach(toast => toast.show())
		}
		
	</script>

	<script type="text/javascript">
		$('#ModalEdit').on('show.bs.modal', function (event) {
			var allow = <?php echo $LogUserEdit; ?>;
			if (!allow) {
				alert("Usuário <?php echo $LogUserName;?> não tem permissão para editar!" )
				abort;
			}
			var button = $(event.relatedTarget) // Button that triggered the modal
			var manut_id = button.data('whatever')
			var manut_tipo = button.data('whatevertipo')
			var manut_nome = button.data('whatevernome')
			var manut_desc = button.data('whateverddesc')
			var manut_fq_km = button.data('whateverfqkm')
			var manut_fq_meses = button.data('whateverfqmeses')

			
			var modal = $(this)
			modal.find('#id-edt').val(manut_id)
			modal.find('#id-view-edt').val(manut_id)
			//document.getElementById('nome-edt').required = false
			modal.find('#tipo-manut-edt').val(manut_tipo)
			modal.find('#nome-edt').val(manut_nome)
			modal.find('#desc-edt').val(manut_desc)
			modal.find('#freq-km-edt').val(manut_fq_km)
			modal.find('#freq-mes-edt').val(manut_fq_meses)
		  
		})
	</script>

	<script type="text/javascript">
		$('#delete').on('show.bs.modal', function (event) {
			var allow = <?php echo $LogUserRemove; ?>;
			if (!allow) {
				alert("Usuário <?php echo $LogUserName;?> não tem permissão para excluir!" )
				abort;
			}

			var button = $(event.relatedTarget) // Button that triggered the modal
			var recid = button.data('whatever')
			var recnome = button.data('whatevernome')
				
			var modal = $(this)
			modal.find('#id-remove').val(recid)
			modal.find('#nome').text(recnome)
		})
	</script>

	<?php
		if($idMsg>0) {
			echo "<script>
				showToast();
			</script>";
		}
	?>
	<script type="text/javascript">
		//Reset no formulário de nova manutenção
		$('#ModalNovo').on('show.bs.modal', function (event) {
			var allow = <?php echo $LogUserAdd; ?>;
			if (!allow) {
				alert("Usuário <?php echo $LogUserName;?> não tem permissão para incluir!" )
				abort;
			}

			var button = $(event.relatedTarget) // Button that triggered the modal


			const formulario = document.querySelector('#form-new-manut');
				formulario.reset();
		})
	</script>

	<script>
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl)
		})
	</script>
  </body>
</html>