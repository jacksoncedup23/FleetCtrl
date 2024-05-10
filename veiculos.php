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

	$mensagens = array(
		"",
		"Veículo removido com sucesso!",
		"Ocorreu um erro ao remover o veículo!",
		"Veículo alterado com sucesso!",
		"Ocorreu um erro ao alterar o veículo!",
		"Veículo cadastrado com sucesso!",
		"Ocorreu um erro ao cadastrar o novo veículo!",
		"Erro ao alterar veículo! Placa duplicada!",
		"Erro ao inserir veículo! Placa duplicada!",
		"Consumo do veículo atualizado",
		"Erro ao atualizar consumo do veículo!",
		"Manutenção planejada com sucesso!",
		"Erro ao planejar manutenção do veículo!",
		"Manutenções cadastradas para o veículo com sucesso!",
		"Erro ao cadastrar manutenções para o veículo com sucesso!",
		"Usuário não possui permissão!");
	
	$toastColor = array(
			"",
			"success",
			"danger",
			"success",
			"danger",
			"success",
			"danger",
			"danger",
			"danger",
			"success",
			"danger",
			"success",
			"danger",
			"success",
			"danger",
			"warning");
	
		//Se nao teve nenhum formulário submitado, verifica se deve mostrar alguma mensagem
		if (isset($_GET["msg"])) {
			$idMsg = $_GET["msg"];
		}
		else
		{
			$idMsg = 0;
		}

	//Verifica a quantidade de páginas
	$SQLContPages = 'SELECT CEIL(COUNT(*) /' . $linesPerPage . ') as CONT, COUNT(*) as TOTAL FROM tbveiculos v, tbtipo_veiculo tp WHERE v.ID_TIPO=tp.ID ';
	if (isset($_GET['search']))
	{
		$busca = str_replace(" ", "%", $_GET['search']);
		$SQLContPages = $SQLContPages . " and (v.MARCA LIKE '%$busca%' or v.MODELO LIKE '%$busca%' or v.PLACA LIKE '%$busca%' or tp.TIPO LIKE '%$busca%')";
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
	//$SQL_Consulta = "SELECT *, CASE WHEN (DATEDIFF(PROX_DT_MANUT,CURRENT_DATE) < 0 OR PROX_KM_MANUT - KM_ATUAL <= 0) THEN 'VENCIDO' WHEN (DATEDIFF(PROX_DT_MANUT,CURRENT_DATE) < $param_dias_vencendo OR PROX_KM_MANUT - KM_ATUAL <= $param_km_vencendo) THEN 'VENCENDO' ELSE 'EM DIA' END AS STS_MANUT FROM 
	//				(SELECT v.*, tp.TIPO as 'NM_TIPO', COALESCE((SELECT MAX(cons.KM) FROM tbconsumo cons WHERE cons.ID_VEICULO = v.ID),v.KM_INICIAL) as KM_ATUAL, (SELECT COUNT(manut.ID) FROM tbmanutencao manut WHERE manut.ID_VEICULO = v.ID_TIPO and (manut.DT_EXEC is NULL or manut.KM_EXEC is null or manut.ID_EXECUTOR is null)) as CONT_MANUT,
	//					(SELECT MIN(manut.DT_PLAN) FROM tbmanutencao manut WHERE manut.ID_VEICULO = v.ID AND (manut.DT_EXEC IS NULL OR manut.KM_EXEC IS NULL OR manut.ID_EXECUTOR IS NULL)) AS PROX_DT_MANUT,
	//					(SELECT MIN(manut.KM_PLAN) FROM tbmanutencao manut WHERE manut.ID_VEICULO = v.ID AND (manut.DT_EXEC IS NULL OR manut.KM_EXEC IS NULL OR manut.ID_EXECUTOR IS NULL)) AS PROX_KM_MANUT FROM tbveiculos v, tbtipo_veiculo tp WHERE v.ID_TIPO = tp.ID ";

	$SQL_Consulta = "SELECT *,
	CASE WHEN (DATEDIFF(PROX_DT_MANUT,CURRENT_DATE) < 0 OR PROX_KM_MANUT - KM_ATUAL <= 0) THEN 'VENCIDO' WHEN (DATEDIFF(PROX_DT_MANUT,CURRENT_DATE) < $param_dias_vencendo OR PROX_KM_MANUT - KM_ATUAL <= $param_km_vencendo) THEN 'VENCENDO' ELSE 'EM DIA' END AS STS_MANUT
	FROM 
	(SELECT v.*, tp.TIPO as 'NM_TIPO', tp.IMAGEM, COALESCE((SELECT MAX(cons.KM) FROM tbconsumo cons WHERE cons.ID_VEICULO = v.ID),v.KM_INICIAL) as KM_ATUAL, proxManut.PROX_KM_MANUT, proxManut.PROX_DT_MANUT
	FROM
		tbveiculos v
		INNER JOIN tbtipo_veiculo tp ON (v.ID_TIPO = tp.ID)
		LEFT JOIN
			(SELECT 
				manutveic.ID_VEICULO ID_VEIC,
				min(COALESCE(exec.KM, veic.KM_INICIAL) + manut.FREQ_KM) AS PROX_KM_MANUT,
				min(COALESCE(exec.DATA,veic.DT_CADASTRO) + INTERVAL manut.FREQ_MESES MONTH) as PROX_DT_MANUT
			FROM
				tbmanutveic manutveic
					INNER JOIN
				tbmanutencao manut ON (manut.ID = manutveic.ID_MANUTENCAO)
					INNER JOIN
				tbVeiculos veic ON (veic.ID = manutveic.ID_VEICULO)
					LEFT JOIN
				tbExecManut exec ON (exec.DATA = (SELECT 
						MAX(DATA)
					FROM
						tbExecManut auxA
					WHERE
						auxA.ID_MANUTENCAO = manutveic.ID)
					AND exec.KM = (SELECT 
						MAX(KM)
					FROM
						tbExecManut auxA
					WHERE
						auxA.ID_MANUTENCAO = manutveic.ID))
						GROUP BY manutveic.ID_VEICULO) proxManut
				ON (proxManut.ID_VEIC = v.ID)
	WHERE 1=1 ";
	if (isset($_GET['search']))
	{
		$busca = str_replace(" ", "%", $_GET['search']);
		$SQL_Consulta = $SQL_Consulta . " and (v.MARCA LIKE '%$busca%' or v.MODELO LIKE '%$busca%' or v.PLACA LIKE '%$busca%' or tp.TIPO LIKE '%$busca%')";
	}
	//echo $SQL_Consulta;
	$SQL_Consulta = $SQL_Consulta . " LIMIT $linesPerPage OFFSET " . ($page - 1) * $linesPerPage;
	$SQL_Consulta = $SQL_Consulta . ") a ";
	if(isset($_GET["order"])) 
	{
		$SQL_Consulta = $SQL_Consulta . "ORDER BY " . $_GET["order"];
		if (getPrm("dir")=="DESC")
		{
			$SQL_Consulta = $SQL_Consulta . " DESC";
		}
	}
	if ($showSQL) {
		echo "<div><strong>Consulta SQL:</strong><br> " . $SQL_Consulta . "</div><hr>";

	}
	$result_users = mysqli_query($conn, $SQL_Consulta);
	
	

	
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>VEÍCULOS | FleetCtrl</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<link rel="stylesheet" type="text/css" href="style/veiculos.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="style/standard.css" media="screen" />

		<script  type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/2.2.3/jquery.min.js"></script>


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
					<li class="nav-item">
						<a class="nav-link" aria-current="page" href="usuarios.php">Usuários</a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link active dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							Veículos
						</a>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item <?php echo $LogUserAdd==0? "disabled" : ""; ?>" href="#" data-bs-toggle="modal" data-bs-target="#ModalNovo">Novo</a></li>
							<li><hr class="dropdown-divider"></li>
							<li class="dropdown-header">Filtrar</li>
							<li><a class="dropdown-item" href="veiculos.php">Todos</a></li>
							<li><hr class="dropdown-divider"></li>
							<li><a class="dropdown-item" href="veiculos_list.php">Cards</a></li>
						</ul>
					</li>
					<li class="nav-item">
						<a class="nav-link" aria-current="page" href="manutencao.php">Manutenção</a>
					</li>
					<li class="nav-item">
						<a class="nav-link disabled" aria-disabled="true">Disabled</a>
					</li>
			</ul>
			<form class="d-flex" role="search" method="get" action="#">
				<input class="form-control me-2" type="search" id="search" name="search" placeholder="Buscar" aria-label="Buscar" value="<?php if(isset($_GET["search"])) {echo $_GET["search"];}?>">
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
						<h1>Veículos</h1>
					</div>

					<div class="col-auto">
						<button id="btnNovoVeiculo" type="button" class="btn btn-primary <?php echo $LogUserAdd==0? "d-none" : ""; ?>" data-bs-toggle="modal" data-bs-target="#ModalNovo" <?php echo $LogUserAdd==0? "disabled" : ""; ?>>Novo veículo</button>
					</div>
				</div>
				<div class="scroll">
					<div class="row">
						<div class="col-md-12">
							<div class="bgTable">
								<table class="table table-striped">
									<thead>
										<tr>
											<th><a href="?<?php if(isset($_GET["search"])) {echo "search=" . $_GET["search"] . "&";} ?>order=ID<?php if(getPrm("dir")!="DESC" and getPrm("order")=="ID"){ echo "&dir=DESC";}?>">#<?php if(getPrm("order")=="ID"){ if(getPrm("dir")!="DESC"){echo " ▼";} else {echo " ▲";}}?></a></th>
											<th><a href="?<?php if(isset($_GET["search"])) {echo "search=" . $_GET["search"] . "&";} ?>order=NM_TIPO<?php if(getPrm("dir")!="DESC" and getPrm("order")=="NM_TIPO"){ echo "&dir=DESC";}?>">Tipo<?php if(getPrm("order")=="NM_TIPO"){ if(getPrm("dir")!="DESC"){echo " ▼";} else {echo " ▲";}}?></a></th>
											<th><a href="?<?php if(isset($_GET["search"])) {echo "search=" . $_GET["search"] . "&";} ?>order=MARCA<?php if(getPrm("dir")!="DESC" and getPrm("order")=="MARCA"){ echo "&dir=DESC";}?>">Marca<?php if(getPrm("order")=="MARCA"){ if(getPrm("dir")!="DESC"){echo " ▼";} else {echo " ▲";}}?></a></th>
											<th><a href="?<?php if(isset($_GET["search"])) {echo "search=" . $_GET["search"] . "&";} ?>order=MODELO<?php if(getPrm("dir")!="DESC" and getPrm("order")=="MODELO"){ echo "&dir=DESC";}?>">Modelo<?php if(getPrm("order")=="MODELO"){ if(getPrm("dir")!="DESC"){echo " ▼";} else {echo " ▲";}}?></a></th>
											<th><a href="?<?php if(isset($_GET["search"])) {echo "search=" . $_GET["search"] . "&";} ?>order=ANO<?php if(getPrm("dir")!="DESC" and getPrm("order")=="ANO"){ echo "&dir=DESC";}?>">Ano<?php if(getPrm("order")=="ANO"){ if(getPrm("dir")!="DESC"){echo " ▼";} else {echo " ▲";}}?></a></th>
											<th><a href="?<?php if(isset($_GET["search"])) {echo "search=" . $_GET["search"] . "&";} ?>order=KM_ATUAL<?php if(getPrm("dir")!="DESC" and getPrm("order")=="KM_ATUAL"){ echo "&dir=DESC";}?>">KM<?php if(getPrm("order")=="KM_ATUAL"){ if(getPrm("dir")!="DESC"){echo " ▼";} else {echo " ▲";}}?></a></th>
											<th>Próxima Manutenção</th>
											<th width="200px">Ações</th>
										</tr>
									</thead>
									<tbody>
										<?php while($users = mysqli_fetch_assoc($result_users)){ ?>
											<tr>
												<td class="align-middle"><?php echo $users['ID']; ?></td>

												<td class="align-middle"><span class="lnkConsulta" data-bs-toggle="modal" data-bs-target="#Consulta" onclick="consulta_veiculo('<?php echo $users['ID'];?>')"><img src="./images/<?php echo $users['IMAGEM']; ?>" class="tipoIcon" alt="Ícone tipo" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="<?php echo $users['NM_TIPO']; ?>"></span></td>
												<td class="align-middle"><?php echo $users['MARCA']; ?></td>
												<td class="align-middle"><?php echo $users['MODELO']; ?></td>
												<td class="align-middle"><?php echo $users['ANO']; ?></td>
												<td class="align-middle"><?php echo round($users['KM_ATUAL']); ?></td>
												<td class="align-middle"><?php 
													if (!empty($users['PROX_DT_MANUT']) and !empty($users['PROX_KM_MANUT'])) {
														$dateManut = DateTime::createFromFormat('Y-m-d', $users['PROX_DT_MANUT'])->format('d/m/Y');
														echo $dateManut . " ou " . number_format($users['PROX_KM_MANUT'],0,",",".") . " km";
													} 
													else
													{
														echo "Nada planejado";
													}
													if($users['STS_MANUT']=="VENCIDO") 
													{
														echo '<img src="./images/atencao.png" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Manutenção vencida!!!">';
													} else if($users['STS_MANUT']=="VENCENDO")
													{
														echo '<img src="./images/alerta.png" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Manutenção próxima!">';
													} else
													{
														echo '<img src="./images/valido.png" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Manutenção em dia">';
													}
													?></td>
												<td class="align-middle">
												<div class="btn-group" role="group" aria-label="Button group with nested dropdown">
													<button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#Consulta" onclick="consulta_veiculo('<?php echo $users['ID'];?>')"><img src="./images/visualizar.png"></button>
													<button type="button" class="btn btn-xs btn-warning <?php echo $LogUserAdd==0? "d-none" : ""; ?>" data-bs-toggle="modal" data-bs-target="#ModalEdit" data-whatever="<?php echo $users['ID']; ?>" data-whatevertipo="<?php echo $users['ID_TIPO']; ?>" data-whatevermarca="<?php echo $users['MARCA']; ?>" data-whatevermodelo="<?php echo $users['MODELO']; ?>" data-whateverano="<?php echo $users['ANO']; ?>" data-whateverplaca="<?php echo $users['PLACA']; ?>" data-whateverkminit="<?php echo round($users['KM_INICIAL']); ?>" data-whateverkmact="<?php echo round($users['KM_ATUAL']); ?>"  data-whatevervalor="<?php echo round($users['VALOR']); ?>" <?php echo $LogUserAdd==0? "disabled" : ""; ?>><img src="./images/editar.png"></button>
													<?php if($LogUserEdit==1) {
														//Permite editar
													?>
														<div class="btn-group" role="group">
															<button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
															Mais
															</button>
															<ul class="dropdown-menu">
																<li><a class="dropdown-item btn" data-bs-toggle="modal" data-bs-target="#modalConsumo" data-whatever="<?php echo $users['ID']; ?>" data-whatevermarca="<?php echo $users['MARCA']; ?>"  data-whatevermodelo="<?php echo $users['MODELO']; ?>" data-whateverano="<?php echo $users['ANO']; ?>" data-whateverkmact="<?php echo round($users['KM_ATUAL']); ?>"><img src="./images/combustivel.png"> Consumo</a></li>
																<li><a class="dropdown-item btn <?php echo $LogUserAdd==0? "disabled" : ""; ?>" <?php echo $LogUserAdd==0? "disabled" : ""; ?>href="manutencao.php?vehicleid=<?php echo $users['ID']; ?>"><img src="./images/add_maintenance.png"> Definir manutenções</a></li>
																<li><a class="dropdown-item btn" data-bs-toggle="modal" data-bs-target="#modalManutencao" data-whatever="<?php echo $users['ID']; ?>" data-whatevermarca="<?php echo $users['MARCA']; ?>"  data-whatevermodelo="<?php echo $users['MODELO']; ?>" data-whateverano="<?php echo $users['ANO']; ?>" data-whateverkmact="<?php echo round($users['KM_ATUAL']); ?>"><img src="./images/manutencao.png"> Executar manutenção</a></li>
																<li><a class="dropdown-item btn btn-outline-danger <?php echo $LogUserRemove==0? "disabled" : ""; ?>" data-bs-toggle="modal" data-bs-target="#removerVeiculo" data-whatever="<?php echo $users['ID']; ?>" data-whatevermarca="<?php echo $users['MARCA']; ?>"  data-whatevermodelo="<?php echo $users['MODELO']; ?>" data-whateverano="<?php echo $users['ANO']; ?>"><img src="./images/excluir.png"> Excluir</a></li>
															</ul>
														</div>
													<?php } else {
														//Não permite editar

													?>
														<button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalConsumo" data-whatever="<?php echo $users['ID']; ?>" data-whatevermarca="<?php echo $users['MARCA']; ?>"  data-whatevermodelo="<?php echo $users['MODELO']; ?>" data-whateverano="<?php echo $users['ANO']; ?>" data-whateverkmact="<?php echo round($users['KM_ATUAL']); ?>"><img src="./images/combustivel.png"></button>
														<button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalManutencao" data-whatever="<?php echo $users['ID']; ?>" data-whatevermarca="<?php echo $users['MARCA']; ?>"  data-whatevermodelo="<?php echo $users['MODELO']; ?>" data-whateverano="<?php echo $users['ANO']; ?>" data-whateverkmact="<?php echo round($users['KM_ATUAL']); ?>"><img src="./images/manutencao.png"></button>
														<button type="button" class="btn btn-danger <?php echo $LogUserRemove==0? "disabled" : ""; ?>" data-bs-toggle="modal" data-bs-target="#removerVeiculo" data-whatever="<?php echo $users['ID']; ?>" data-whatevermarca="<?php echo $users['MARCA']; ?>"  data-whatevermodelo="<?php echo $users['MODELO']; ?>" data-whateverano="<?php echo $users['ANO']; ?>"><img src="./images/excluir.png"></button>
													<?php } ?>
												</div>
													
													
													
												
													<!--
													===============================Código para verificar proxima manutenção=============================
													SELECT manut.*, manutveic.ID as ID_MANUT_VEIC, exec.*,  coalesce(exec.KM, KM_INICIAL) + manut.FREQ_KM as novaKM FROM tbmanutveic manutveic inner join tbmanutencao manut on (manut.ID = manutveic.ID_MANUTENCAO) inner join tbVeiculos veic on (veic.ID = manutveic.ID_VEICULO) left join tbExecManut exec on (exec.DATA = (SELECT max(DATA) FROM tbExecManut auxA WHERE auxA.ID_MANUTENCAO = manutveic.ID) and exec.KM = (SELECT max(KM) FROM tbExecManut auxA WHERE auxA.ID_MANUTENCAO = manutveic.ID)) WHERE manutveic.ID_VEICULO = 1;
													===============================Código para verificar proxima manutenção=============================

												-->
													
													
													
												</td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
							</div>		
					</div>	
				</div> 	
			</div>
				<div class="paginationPanel">
					<!-- Paginação -->
					<nav aria-label="...">
						<div class="container text-left">
							<div class="row align-items-start">
								<div class="col counterText">
									<?php echo $registros . " registro(s)"; ?>
								</div>
								<div class="col">
									<ul class="pagination justify-content-end">
										<li class="page-item <?php if ($page <= 1) {echo "disabled";}?>">
											<a class="page-link" href="?page=<?php echo ($page-1); ?>">Anterior</a>
										</li>
										<?php
											$pg = 1;
											while ($pg <= $pagesCount) 
											{
												?>

												<li class="page-item <?php if ($page == $pg) {echo "active"; } ?>" aria-current="page">
													<a class="page-link" href="?page=<?php echo $pg; ?>"><?php echo $pg; ?></a>
												</li>
											
										<?php
											$pg ++;
											}
										?>

										<li class="page-item <?php if ($page == $pagesCount or $pagesCount<=1) {echo "disabled";}?>">
											<a class="page-link" href="?page=<?php echo ($page+1); ?>">Próxima</a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</nav>
					<!-- FIM da paginação -->
				</div>
			
		</div>


		<!-- Inicio Modal Consulta-->
		<div class="modal fade" id="Consulta" tabindex="-1" aria-labelledby="TitleConsulta" aria-hidden="true">
			<div id="divContentConsulta" class="modal-dialog">
				
			</div>
		</div>
		
		<!-- Fim Modal -->
		<!-- Modal Excluir -->
		<div class="modal fade" id="removerVeiculo" tabindex="-1" aria-labelledby="removerVeiculoLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h1 class="modal-title fs-5" id="removerVeiculoLabel">Excluir veículo</h1>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						Tem certeza que você deseja excluir o veículo <strong id="nome"></strong>?
						
						
					</div>
					<div class="modal-footer">
						<!-- action="remove_user.php" -->
						<form method="POST" action="remove_veic.php" enctype="multipart/form-data">
						
							<input name="id" type="hidden" class="form-control" id="id" value="">
							<button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancelar</button>
							<button id="btn-remove-confirm" type="submit" class="btn btn-danger">Excluir</button>
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
					<h1 class="modal-title fs-5" id="staticBackdropLabel">Editar Veículo</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form method="POST" action="cad_veiculos.php" enctype="multipart/form-data">
						<div class="form-group">
							<label for="id-view" class="control-label">Código:</label>
							<input name="id-view" id="id-view" class="form-control" type="text" placeholder="Código"  disabled value="">
						</div>
						<div class="form-group">
							<label for="tpveic" class="control-label">Tipo:</label>
							<select name="tpveic" id="tpveic" class="form-select" required>

								<option selected></option>
									<?php 
										$SQL_TP_VEIC = "SELECT * FROM tbtipo_veiculo";
										$result_tp_veic = mysqli_query($conn, $SQL_TP_VEIC);
										while($tipo_veic = mysqli_fetch_assoc($result_tp_veic)){ ?>
										<option value="<?php echo $tipo_veic['ID']; ?>"><?php echo $tipo_veic['TIPO']; ?></option>
									<?php } ?>
							</select>
						</div>
						<div class="form-group">
							<label for="marca" class="control-label">Marca:</label>
							<input name="marca" type="text" class="form-control" id="marca" required>
						</div>
						<div class="form-group">
							<label for="modelo" class="control-label">Modelo:</label>
							<input name="modelo" type="text" class="form-control" id="modelo" required>
						</div>
						<div class="form-group">
							<label for="ano" class="control-label">Ano:</label>
							<input name="ano" type="number" class="form-control" id="ano" min="1900" max="2025" required>
						</div>
						<div class="form-group">
							<label for="placa" class="control-label">Placa:</label>
							<input name="placa" type="text" class="form-control" id="placa" required>
						</div>
						<div class="form-group">
							<label for="kminit" class="control-label">KM Inicial:</label>
							<input name="kminit" type="number" class="form-control" id="kminit" min="0" max="999999" readonly>
						</div>
						<div class="form-group">
							<label for="valor-edt" class="control-label">Valor de aquisição:</label>
							<input name="valor" type="number" class="form-control" id="valor-edt" min="1" max="9999999" readonly>
						</div>
						
						<input name="id" type="hidden" class="form-control" id="id" value="">
			
					
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
	
	<!-- Modal Novo -->

		<div class="modal fade" id="ModalNovo" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h1 class="modal-title fs-5" id="staticBackdropLabel">Novo veículo</h1>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form id="form-new-veic" method="POST" action="cad_veiculos.php" enctype="multipart/form-data">
							<div class="form-group">
								<label for="id-view" class="control-label">Código:</label>
								<input name="id-view" id="id-view" class="form-control" type="text" placeholder="Código"  disabled value="">
							</div>
							<div class="form-group">
								<label for="tpveic_novo" class="control-label">Tipo:</label>
								<select name="tpveic" id="tpveic_novo" class="form-select" required>

									<option selected></option>
										<?php 
											$SQL_TP_VEIC = "SELECT * FROM tbtipo_veiculo";
											$result_tp_veic = mysqli_query($conn, $SQL_TP_VEIC);
											while($tipo_veic = mysqli_fetch_assoc($result_tp_veic)){ ?>
											<option value="<?php echo $tipo_veic['ID']; ?>"><?php echo $tipo_veic['TIPO']; ?></option>
										<?php } ?>
								</select>
							</div>
							<div class="form-group">
								<label for="marca_novo" class="control-label">Marca:</label>
								<div class="input-group">
									<input name="marca" type="text" class="form-control" id="marca_novo" required>
									<button id="btnListaMarcas" name="boitao" class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="refreshMarcas()">Marcas...</button>
									<ul class="dropdown-menu dropdown-menu-end" id="listaMarcas">
										<li><a class="dropdown-item" href="#">Nada encontrado!</a></li>
									</ul>
								</div>
							</div>
							<div class="form-group">
								<label for="modelo_novo" class="control-label">Modelo:</label>
								<div class="input-group">
								<input name="modelo" type="text" class="form-control" id="modelo_novo" required>
									<button id="btnListModelos" class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="refreshModelos()">Modelos...</button>
									<ul class="dropdown-menu dropdown-menu-end" id="listaModelos">
										<li><a class="dropdown-item" href="#">Action</a></li>
										<li><a class="dropdown-item" href="#">Another action</a></li>
										<li><a class="dropdown-item" href="#">Something else here</a></li>
										<li><a class="dropdown-item" href="#">Separated link</a></li>
									</ul>
								</div>
							</div>
							<div class="form-group">
								<label for="ano" class="control-label">Ano:</label>
								<input name="ano" type="number" class="form-control" id="ano" min="1900" max="2025" required>
							</div>
							<div class="form-group">
								<label for="placa" class="control-label">Placa:</label>
								<input name="placa" type="text" class="form-control" id="placa" required>
							</div>
							<div class="form-group">
								<label for="kminit" class="control-label">KM Inicial:</label>
								<input name="kminit" type="number" class="form-control" id="kminit" min="0" max="999999">
							</div>
							<div class="form-group">
								<label for="valor" class="control-label">Valor de aquisição:</label>
								<input name="valor" type="number" class="form-control" id="valor" min="1" max="9999999" pattern="^\d*(\.\d{0,2})?$">
							</div>
							
							<input name="id" type="hidden" class="form-control" id="id" value="">	
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
								<button type="submit" class="btn btn-primary" name="submit" id="submit">Salvar</button>

							</div>
						</form>
					</div>
				</div>
			</div>
		</div>


	<!-- --------------------------------------------------------------------------------------------------------------------------------------- -->
	<!-- --------------------------------------------------------------------------------------------------------------------------------------- -->
		<!-- Modal Registrar KM / consumo -->

		<div class="modal fade" id="modalConsumo" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="staticBackdropLabel">Manter consumo do Veículo</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form method="POST" action="consumo_veic.php" enctype="multipart/form-data">
						<div class="form-group">
							<label for="id-view" class="control-label">Código:</label>
							<input name="id-view" id="id-view" class="form-control" type="text" placeholder="Código"  disabled value="">
						</div>
						<div class="form-group">
							<label for="veiculo" class="control-label">Veículo:</label>
							<input name="veiculo" type="text" class="form-control" id="veiculo" disabled>
						</div>
						<div class="form-group">
							<label for="lastkm" class="control-label">Última KM:</label>
							<input name="lastkm" type="text" class="form-control" id="lastkm" disabled>
						</div>
						<div class="form-group">
							<label for="kmact" class="control-label">KM atual:</label>
							<input name="kmact" type="number" class="form-control" id="kmact" min="0" max="999999" required>
						</div>
						<div class="form-group">
							<label for="valor" class="control-label">Valor:</label>
							<input name="valor" type="number" class="form-control" id="valor" min="0" required>
						</div>
						<div class="form-group">
							<label for="litros" class="control-label">Litros:</label>
							<input name="litros" type="number" class="form-control" id="litros" min="0" required>
						</div>
						<div class="form-group">
							<label for="dat" class="control-label">Data:</label>
							<input name="dat" type="date" class="form-control" id="dat"  required>
						</div>
						<input name="id" type="hidden" class="form-control" id="id" value="">
			
					
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
		<!-- Modal Registrar manutenção -->

		<div class="modal fade" id="modalManutencao" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="staticBackdropLabel">Execução de manutenção</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form method="POST" action="cadastra_manutencao_veic.php" enctype="multipart/form-data">
						<div class="form-group">
							<div class="row align-items-start">
								<div class="col-3">
									<label for="id-view_manut" class="control-label">Código:</label>
									<input name="id-view" id="id-view_manut" class="form-control" type="text" placeholder="Código"  disabled value="">
								</div>
								<div class="col-9">
									<label for="veiculo_manut" class="control-label">Veículo:</label>
									<input name="veiculo" type="text" class="form-control" id="veiculo_manut" disabled>
								</div>
							</div>	
						</div>
						<div class="form-group">
							<div class="row align-items-start">
								<div class="col-3">
									<label for="id_executor_manut" class="control-label">Cód.:</label>
									<input name="id_executor_manut_view" type="text" class="form-control" id="id_executor_manut_view" disabled>
									<input name="id_executor_manut" type="text" class="form-control" id="id_executor_manut" hidden>
								</div>
								<div class="col-9">
									<label for="nm_executor_manut" class="control-label">Executor:</label>
									<input name="nm_executor_manut" type="text" class="form-control" id="nm_executor_manut"  disabled>
								</div>
							</div>	
						</div>
						<div class="form-group">
							<label for="tpmanut" class="control-label">Manutenção:</label>
							<select name="tpmanut" id="tpmanut" class="form-select" onchange="consulta_dados_manutencao(this.value)" required>

								
							</select>
						</div>
						<div class="form-group" id="maint_last_data">
							<div class="row align-items-start">
								<div class="col">
									<label for="lastkm_manut" class="control-label">Última KM:</label>
									<input name="lastkm" type="text" class="form-control" id="lastkm_manut" disabled>
								</div>
								<div class="col">
									<label for="lastdt_manut" class="control-label">Última data:</label>
									<input name="lastdt" type="date" class="form-control" id="lastdt_manut" disabled>
								</div>
							</div>	
						</div>
						<div class="form-group">
							<label for="kmExec" class="control-label">KM executada</label>
							<input onblur="validarKM()" name="kmExec" type="number" class="form-control" id="kmExec" min="0" max="999999" required>
							<span id="msg-erro-km" style="display: none; color:red">KM precisa ser maior do que a anterior!</span>
						</div>

						<div class="form-group">
							<label for="dtExec" class="control-label">Data:</label>
							<input onblur="validarDT()" name="dtExec" type="date" class="form-control" id="dtExec"  required>
							<span id="msg-erro-dt" style="display: none; color:red">A data precisa ser posterior a anterior!</span>
							<span id="msg-erro-dt-fut" style="display: none; color:red">A data não pode ser futura!</span>
						</div>

						<div class="form-group">
							<label for="valor_manut" class="control-label">Valor:</label>
							<input name="valor" type="number" step="0.01" class="form-control" id="valor_manut" min="0" required>
						</div>
						<div class="mb-3">
							<label for="obs_manut" class="form-label">Observações</label>
							<textarea class="form-control" name="obs_manut" id="obs_manut" rows="3"></textarea>
						</div>
						<input name="id" type="hidden" class="form-control" id="id_manut" value="">
						<input name="id_veic_manut" type="hidden" class="form-control" id="id_veic_manut" value="">
			
					
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
	<script>
		//Validação do campo de KM.
		function validarKM() {
			var inputNumero = document.getElementById("kmExec");
			var numero = parseFloat(inputNumero.value);
			var minimo = parseFloat(inputNumero.min); // Defina o valor mínimo aqui

			if (numero <= minimo) {
				document.getElementById("msg-erro-km").style.display = "block";
			} else {
				document.getElementById("msg-erro-km").style.display = "none";
			}
		}
		function validarDT() {
			var dtExecInput = document.getElementById("dtExec");
			var numero = new Date(dtExecInput.value + " 00:00");
			var minimo = new Date(dtExecInput.min + " 23:59"); // Defina o valor mínimo aqui
			var maximo = new Date(dtExecInput.max); // Defina o valor máximo aqui

			if (numero <= minimo) {
				document.getElementById("msg-erro-dt").style.display = "block";
			} else if (numero > maximo){
				document.getElementById("msg-erro-dt-fut").style.display = "block";
			} else {
				document.getElementById("msg-erro-dt").style.display = "none";
				document.getElementById("msg-erro-dt-fut").style.display = "none";
			}
		}
		function setKMManut() {
			document.getElementById("kmExec").value=document.getElementById("nextkm_manut").value
			validarKM()
		}
		function setDTManut() {
			document.getElementById("dtExec").value=document.getElementById("nextdt_manut").value
			validarDT()
		}

		async function refreshNotifications() {
			await $.post("query_notifications.php", {}, function(x) { $("#notificationPanel").html(x); } );
			var notfCount = parseInt(document.getElementById('notfCount').innerHTML) >99 ? "99+" : parseInt(document.getElementById('notfCount').innerHTML)

			document.getElementById('showNotfCount').innerHTML = notfCount
			

		}
		
		function refreshMarcas() {
			tipo=document.getElementById("tpveic_novo").value;
			
			$.post("query_lista_marcas.php", {tipo:tipo}, function(x) { $("#listaMarcas").html(x); } );
		}

		function setMarca(marca) {
			document.getElementById("marca_novo").value=marca;
		}

		function refreshModelos() {
			marca=document.getElementById("marca_novo").value;
			$.post("query_lista_modelos.php", {marca:marca}, function(x) { $("#listaModelos").html(x); } );
		}

		function setModelo(modelo) {
			document.getElementById("modelo_novo").value=modelo;
		}


		refreshNotifications();

	</script>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->

	<script>
		
			//$("#btnNovoVeiculo").click()
	</script>

	<script type="text/javascript">
		//Modal editar
		$('#ModalEdit').on('show.bs.modal', function (event) {
			
			var allow = <?php echo $LogUserEdit; ?>;
			if (!allow) {
				alert("Usuário <?php echo $LogUserName;?> não tem permissão para editar!" )
				abort;
			}

			var button = $(event.relatedTarget) // Button that triggered the modal
			var veicid = button.data('whatever')
			var veictipo = button.data('whatevertipo')
			var veicmarca = button.data('whatevermarca')
			var veicmodelo = button.data('whatevermodelo')
			var veicano = button.data('whateverano')
			var veicplaca = button.data('whateverplaca')
			var veickminit = button.data('whateverkminit')
			var veicvalor = button.data('whatevervalor')
			//var veic = button.data('whatever')
			
			var modal = $(this)
			modal.find('#id').val(veicid)
			modal.find('#id-view').val(veicid)
			modal.find('#tpveic').val(veictipo)
			modal.find('#marca').val(veicmarca)
			modal.find('#modelo').val(veicmodelo)
			modal.find('#ano').val(veicano)
			modal.find('#placa').val(veicplaca)
			modal.find('#kminit').val(veickminit)
			modal.find('#valor-edt').val(veicvalor)
			

			//modal.find('#nvacesso').val(recacesso)
		  
		})
	</script>

	<script type="text/javascript">
		//Modal Remover
		$('#removerVeiculo').on('show.bs.modal', function (event) {
			var allow = <?php echo $LogUserRemove; ?>;
			if (!allow) {
				alert("Usuário <?php echo $LogUserName;?> não tem permissão para excluir!" )
				abort;
			}
		  
			var button = $(event.relatedTarget) // Button that triggered the modal
			var veicid = button.data('whatever')
			var veicmarca = button.data('whatevermarca')
			var veicmodelo = button.data('whatevermodelo')
			var veicano = button.data('whateverano')
			
			var modal = $(this)
			modal.find('#id').val(veicid)
			modal.find('#nome').text(veicmarca + ' - ' + veicmodelo + ' | ' + veicano)
		})
	</script>
	
	<script>
		//Execução do PHP em background
		function consulta_veiculo(id_veic) {
			$.post("query_consulta_veiculo.php", {id_veiculo:id_veic}, function(x) { $("#divContentConsulta").html(x); } );
		}
		function user_sidebar() {
			$.post("query_user_sidebar.php", {}, function(x) { $("#userSidebar").html(x); } );
		}
		
		async function consulta_dados_manutencao(id_manut) {
			await $.post("query_consulta_dados_manut.php", {id_veic_manut:id_manut}, function(x) { $("#maint_last_data").html(x); } );

			if(id_manut==null || id_manut==''){
					document.getElementById('kmExec').disabled=true;
					document.getElementById('dtExec').disabled=true;
					document.getElementById('valor_manut').disabled=true;
					document.getElementById('obs_manut').disabled=true;
				} else {
					document.getElementById('kmExec').disabled=false;
					document.getElementById('dtExec').disabled=false;
					document.getElementById('valor_manut').disabled=false;
					document.getElementById('obs_manut').disabled=false;
					document.getElementById('kmExec').min = document.getElementById('lastkm_manut').value
					document.getElementById('dtExec').min = document.getElementById('lastdt_manut').value
					const hoje = new Date();
					//hoje = hoje.getFullYear() + "-" + hoje.getMonth() + "-" + hoje.getDate()
					//alert(hoje.getFullYear() + "-" + hoje.getMonth() + "-" + hoje.getDate())
					document.getElementById('dtExec').max = hoje.getFullYear() + "-" + hoje.getMonth() + "-" + hoje.getDate()
				}
			
		}
		async function consulta_manutencao(id_veic,id_manut=null) {
			await $.post("query_manute_veiculo.php", {id_veiculo:id_veic}, function(x) { $("#tpmanut").html(x); } );
			
			if(id_manut!=null) {
				document.getElementById('tpmanut').value=id_manut;
			}


		}

		
   	</script>
	

	<script type="text/javascript">
		//Modal consumo
			$('#modalConsumo').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Button that triggered the modal
		  var veicid = button.data('whatever')
		  var veicmarca = button.data('whatevermarca')
		  var veicmodelo = button.data('whatevermodelo')
		  var veicano = button.data('whateverano')
		  var veickm = button.data('whateverkmact')
		  var date = new Date()
		  var dt = date.getFullYear() + "-" + ("0" + (date.getMonth()+1)).slice(-2) + "-" + ("0" + date.getDate()).slice(-2)
		  //var dt = date.toLocaleString()
			
		  var modal = $(this)
		  modal.find('#id').val(veicid)
		  modal.find('#id-view').val(veicid)
		  modal.find('#veiculo').val(veicmarca + ' - ' + veicmodelo + ' | ' + veicano)
		  modal.find('#lastkm').val(veickm)
		  document.getElementById('kmact').min = veickm
		  modal.find('#kmact').val('')
		  modal.find('#valor').val('')
		  modal.find('#litros').val('')
		  modal.find('#dat').val(dt)

		})
	</script>

	<script type="text/javascript">
		//Modal executar manutenção
			$('#modalManutencao').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Button that triggered the modal
		  var veicid = button.data('whatever')
		  var idUser = "<?php echo $LogUserID; ?>"
		  var nmUser = "<?php echo $LogUserName; ?>"
		  var veicmarca = button.data('whatevermarca')
		  var veicmodelo = button.data('whatevermodelo')
		  var veicano = button.data('whateverano')
		  var veickm = button.data('whateverkmact')
		  var manut_id = button.data('whatevermanutid')
		  var date = new Date()
		  var dt = date.getFullYear() + "-" + ("0" + (date.getMonth()+1)).slice(-2) + "-" + ("0" + date.getDate()).slice(-2)
		  //var dt = date.toLocaleString()

		var modal = $(this)
		  modal.find('#id_manut').val(veicid)
		  modal.find('#id_veic_manut').val(veicid)

		  modal.find('#id-view_manut').val(veicid)
		  modal.find('#veiculo_manut').val(veicmarca + ' - ' + veicmodelo + ' | ' + veicano)
		  modal.find('#id_executor_manut_view').val(idUser)
		  modal.find('#id_executor_manut').val(idUser)
		  modal.find('#nm_executor_manut').val(nmUser)
		  modal.find('#nm_executor_manut').val(nmUser)
		  modal.find('#tpmanut').val('Troca Pneus')
		  

		  modal.find('#lastkm_manut').val(veickm)
		  modal.find('#kmExec').val('')
		  modal.find('#dtExec').val('')
		  modal.find('#valor_manut').val('')
		  modal.find('#obs_manut').val('')
		  


		  consulta_manutencao(veicid,manut_id)
		  consulta_dados_manutencao(manut_id);

		  
		})
	</script>

	<script type="text/javascript">
		//Modal novo veiculo
		$('#ModalNovo').on('show.bs.modal', function (event) {
		var allow = <?php echo $LogUserAdd; ?>;
			if (!allow) {
				alert("Usuário <?php echo $LogUserName;?> não tem permissão para cadastrar!" )
				abort;
			}
		  var button = $(event.relatedTarget) // Button that triggered the modal


		  const formulario = document.querySelector('#form-new-veic');
			formulario.reset();
		})
	</script>

	<?php
		if($idMsg>0) {
			echo "<script>

			var toastElList = [].slice.call(document.querySelectorAll('.toast'))
			var toastList = toastElList.map(function(toastEl) {
			return new bootstrap.Toast(toastEl)
			})
			toastList.forEach(toast => toast.show())
	
			</script>";
		}
	?>
<?php
		//Se tiver um veículo informado, já abre consulta
		if(isset($_GET['open_vehicle']) and !empty($_GET['open_vehicle'])) {
			echo'<button hidden id="btnConsultaVeic" type="button" data-bs-toggle="modal" data-bs-target="#Consulta"></button>';
			echo "<script>
					document.getElementById('btnConsultaVeic').click();
					consulta_veiculo('" . $_GET['open_vehicle'] . "')
				</script>";
		}
	?>
	<script>
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl)
		})
	</script>
  </body>
</html>