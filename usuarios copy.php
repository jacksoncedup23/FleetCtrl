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
		"{VENCIDO}" => " and DATEDIFF(VALIDADE_CNH,CURRENT_DATE) < 0",
		"{EM DIA}" => " and DATEDIFF(VALIDADE_CNH,CURRENT_DATE) >= " . $param_dias_vencendo,
		"{VENCENDO}" => " and DATEDIFF(VALIDADE_CNH,CURRENT_DATE) BETWEEN 0 AND " . ($param_dias_vencendo-1)
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
	$SQLContPages = 'SELECT CEIL(COUNT(*) /' . $linesPerPage . ') as CONT, COUNT(*) as TOTAL FROM tbUsuarios';
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
	$SQL_Consulta = "SELECT * FROM (SELECT u.*, a.ID as 'IDNIVEL', a.NOME as 'NIVEL', DATEDIFF(u.VALIDADE_CNH,current_date) as DIAS_VENC FROM tbUsuarios u,tbNvAcesso a WHERE u.ID_NV_ACESSO = a.ID";
	if (isset($_GET['search']))
	{
		if(isset($buscas_padrao[strtoupper($_GET['search'])])){
			$SQL_Consulta = $SQL_Consulta . $buscas_padrao[strtoupper($_GET['search'])]; 
		}
		else
		{
		$busca = str_replace(" ", "%", $_GET['search']);
		$SQL_Consulta = $SQL_Consulta . " and (u.NOME LIKE '%$busca%' or u.USUARIO LIKE '%$busca%' or u.EMAIL LIKE '%$busca%' or u.ID='$busca')";
		}
	}
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
		<title>USUÁRIOS | FleetCtrl</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		
		<link rel="stylesheet" type="text/css" href="style/usuarios.css" media="screen" />
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
						<a class="nav-link active dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							Usuários
						</a>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#ModalNovo">Novo</a></li>
							<li><hr class="dropdown-divider"></li>
							<li><a class="dropdown-item" href="usuarios.php">Mostrar todos</a></li>
							<li><hr class="dropdown-divider"></li>
							<li><a class="dropdown-item" href="usuarios.php?search={em+dia}">CNH em dia</a></li>
							<li><a class="dropdown-item" href="usuarios.php?search={vencendo}">CNH vencendo</a></li>
							<li><a class="dropdown-item" href="usuarios.php?search={vencido}">CNH vencida</a></li>
						</ul>
					</li>
					<li class="nav-item">
						<a class="nav-link" aria-current="page" href="veiculos.php">Veículos</a>
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
			<a onclick="user_sidebar()" data-bs-toggle="offcanvas" data-bs-target="#userSidebar" aria-controls="offcanvasRight"><img src="<?php echo $LogUserData['IMAGE']; ?>" class="userImgProf" alt="Imagem do usuário"></a>
			</div>
		</div>
	</nav>
	<!-- BARRA DE NAVEGAÇÃO ---------------------------------------------------------------------------------- -->
	<!-- DADOS DO USUÁRIO ------------------------------------------------------------------------------------ -->

	<div id="userSidebar" class="offcanvas offcanvas-end" tabindex="-1" aria-labelledby="userPanel">
		
	</div>
	<!-- FIM DADOS USUARIO ----------------------------------------------------------------------------------- -->
	<!-- Mensagem de exito/erro -->

		<div style="position: absolute; top: 10px; right: 10px;">
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
				<div class="page-header">
					<h1>Usuários</h1>
				</div>
				<div class="col-auto mb-4">
					<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalNovo">Novo usuário</button>
				</div>

				<div class="row">
					<div class="col-md-12">
						<div class="bgTable">
							<table class="table">
								<thead>
									<tr>
										<th><a href="?<?php if(isset($_GET["search"])) {echo "search=" . $_GET["search"] . "&";} ?>order=ID<?php if(getPrm("dir")!="DESC" and getPrm("order")=="ID"){ echo "&dir=DESC";}?>">#<?php if(getPrm("order")=="ID"){ if(getPrm("dir")!="DESC"){echo " ▼";} else {echo " ▲";}}?></a></th>
										<th><a href="?<?php if(isset($_GET["search"])) {echo "search=" . $_GET["search"] . "&";} ?>order=NOME<?php if(getPrm("dir")!="DESC" and getPrm("order")=="NOME"){ echo "&dir=DESC";}?>">Usuário<?php if(getPrm("order")=="NOME"){ if(getPrm("dir")!="DESC"){echo " ▼";} else {echo " ▲";}}?></a></th>
										<th><a href="?<?php if(isset($_GET["search"])) {echo "search=" . $_GET["search"] . "&";} ?>order=VALIDADE_CNH<?php if(getPrm("dir")!="DESC" and getPrm("order")=="VALIDADE_CNH"){ echo "&dir=DESC";}?>">Validade CNH<?php if(getPrm("order")=="VALIDADE_CNH"){ if(getPrm("dir")!="DESC"){echo " ▼";} else {echo " ▲";}}?></a></th>
										<th><a href="?<?php if(isset($_GET["search"])) {echo "search=" . $_GET["search"] . "&";} ?>order=EMAIL<?php if(getPrm("dir")!="DESC" and getPrm("order")=="EMAIL"){ echo "&dir=DESC";}?>">E-mail<?php if(getPrm("order")=="EMAIL"){ if(getPrm("dir")!="DESC"){echo " ▼";} else {echo " ▲";}}?></a></th>
										<th><a href="?<?php if(isset($_GET["search"])) {echo "search=" . $_GET["search"] . "&";} ?>order=NIVEL<?php if(getPrm("dir")!="DESC" and getPrm("order")=="NIVEL"){ echo "&dir=DESC";}?>">Nível de acesso<?php if(getPrm("order")=="NIVEL"){ if(getPrm("dir")!="DESC"){echo " ▼";} else {echo " ▲";}}?></a></th>
										<th>Ação</th>
									</tr>
								</thead>
								<tbody>
									<?php while($users = mysqli_fetch_assoc($result_users)){
										$dateCNH = DateTime::createFromFormat('Y-m-d', $users['VALIDADE_CNH'])->format('d/m/Y');
										if($users['DIAS_VENC']<0)
										{
											$vencido = 2;
										} else if ($users['DIAS_VENC']<$param_dias_vencendo)
										{
											$vencido = 1;
										} else
										{
											$vencido = 0;
										}
										?>
										<tr>
											<td class="align-middle"><?php echo $users['ID']; ?></td>
											<td class="align-middle"><span class="lnkConsulta" data-bs-toggle="modal" data-bs-target="#Consulta<?php echo $users['ID']; ?>"><?php echo $users['NOME']; ?></span></td>
											<td class="align-middle" <?php if($vencido==2) { echo 'style="color:red;"';} ?>>
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
												?></td>
											<td class="align-middle"><?php echo $users['EMAIL']; ?></td>
											<td class="align-middle"><?php echo $users['NIVEL']; ?></td>
											<td class="align-middle">
												<button type="button" class="btn btn-xs btn-primary" data-bs-toggle="modal" data-bs-target="#Consulta<?php echo $users['ID']; ?>"><img src="./images/visualizar.png"></button>
												<button type="button" class="btn btn-xs btn-warning" data-bs-toggle="modal" data-bs-target="#ModalEdit" data-whatever="<?php echo $users['ID']; ?>" data-whatevernome="<?php echo $users['NOME']; ?>" data-whateverdtnasc="<?php echo $users['DT_NASCIMENTO']; ?>" data-whateverdtcnh="<?php echo $users['VALIDADE_CNH']; ?>" data-whateverusuario="<?php echo $users['USUARIO']; ?>" data-whateveremail="<?php echo $users['EMAIL']; ?>" data-whateveracesso="<?php echo $users['IDNIVEL']; ?>"><img src="./images/editar.png"></button>
												<button type="button" class="btn btn-xs btn-danger" data-bs-toggle="modal" data-bs-target="#removeUser" data-whatever="<?php echo $users['ID']; ?>" data-whatevernome="<?php echo $users['NOME']; ?>"><img src="./images/excluir.png"></button>
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
													<div class="clearfix">
														<img src="./images/uploads/users/default.png" class="col-md-6 float-md-end mb-3 ms-md-3" alt="...">
														<?php
															$dateNasc = DateTime::createFromFormat('Y-m-d', $users['DT_NASCIMENTO'])->format('d/m/Y');

														?>

														<div class="UserCod"><strong>Código: </strong><?php echo $users['ID']; ?></div>
														<div class="UserNome"><strong>Nome: </strong><?php echo $users['NOME']; ?></div>	
														<div class="UserUsuario"><strong>Usuário: </strong><?php echo $users['USUARIO']; ?></div>
														
														<div class="UserDt"><strong>Data de Nascimento: </strong><?php echo $dateNasc; ?></div>
														<div class="Useremail"><strong>E-mail: </strong><?php echo $users['EMAIL']; ?></div>
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
														
														<div class="UserNvAccesso"><strong>Nível de acesso: </strong><?php echo $users['NIVEL']; ?></div>
															
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
					</div>		
				</div>		
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
			</div>
			<!-- FIM da paginação -->
		</div>


	
		<!-- Modal Excluir -->
		<div class="modal fade" id="removeUser" tabindex="-1" aria-labelledby="removeUserLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h1 class="modal-title fs-5" id="removeUserLabel">Excluir usuário</h1>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						Tem certeza que você deseja excluir o usuário <strong id="nome"></strong>?
						
						
					</div>
					<div class="modal-footer">
						<!-- action="remove_user.php" -->
						<form method="POST" action="remove_user.php" enctype="multipart/form-data">
						
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
					<h1 class="modal-title fs-5" id="staticBackdropLabel">Editar usuário</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form method="POST" action="cad_user.php" enctype="multipart/form-data">
						<div class="form-group">
							<label for="id-view-edt" class="control-label">Código:</label>
							<input name="id-view" id="id-view-edt" class="form-control" type="text" placeholder="Código"  disabled value="">
						</div>
						<div class="form-group">
							<label for="nome-edt" class="control-label">Nome:</label>
							<input name="nome" type="text" class="form-control" id="nome-edt" required>
						</div>
						<div class="form-group">
							<label for="dtnasc-edt" class="control-label">Data de nascimento:</label>
							<input name="dtnasc" type="date" class="form-control" id="dtnasc-edt" required>
						</div>
						<div class="form-group">
							<label for="dtcnh-edt" class="control-label">Validade da CNH:</label>
							<input name="dtcnh" type="date" class="form-control" id="dtcnh-edt" required>
						</div>
						<div class="form-group">
							<label for="username-edt" class="control-label">Usuário:</label>
							<input name="username" type="text" class="form-control" id="username-edt" required>
						</div>
						<div class="form-group">
							<label for="senha-edt" class="control-label">Senha:</label>
    						<input type="password" id="senha-edt" name="senha" class="form-control" aria-describedby="passwordHelpInline">
							<span id="passwordHelpInline" class="form-text">
								Precisa conter no mínimo 6 caracteres.
							</span>
  
						</div>
						<div class="form-group">
							<label for="email-edt" class="control-label">E-mail:</label>
							<input name="email" type="text" class="form-control" id="email-edt" required>
						</div>
						<div class="form-group">
							<label for="nvacesso-edt" class="control-label">Nível de acesso:</label>
							<select name="nvacesso" id="nvacesso-edt" class="form-select" required>

								<option selected></option>
									<?php 
										$SQL_NV_ACESSO = "SELECT * FROM tbNvAcesso";
										$result_nv_acesso = mysqli_query($conn, $SQL_NV_ACESSO);
										while($niveis_acesso = mysqli_fetch_assoc($result_nv_acesso)){ ?>
										<option value="<?php echo $niveis_acesso['ID']; ?>"><?php echo $niveis_acesso['NOME']; ?></option>
									<?php } ?>
							</select>
						</div>
						<!-- ENVIAR ARQUIVO -->
						<div class="form-group">
							<label for="file" class="control-label">Foto:</label>
							<input name="file" id="file" class="form-control" type="file" placeholder="Arquivo"  >
						</div>
						<!-- FIM ENVAIR ARQUIVo -->
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
						<form id="form-new-user" method="POST" action="cad_user.php" enctype="multipart/form-data">
							<div class="form-group">
								<label for="id-view" class="control-label">Código:</label>
								<input name="id-view" id="id-view" class="form-control" type="text" placeholder="Código"  disabled value="">
							</div>
							<div class="form-group">
								<label for="nome" class="control-label">Nome:</label>
								<input name="nome" type="text" class="form-control" id="nome" required>
							</div>
							<div class="form-group">
								<label for="dtnasc" class="control-label">Data de nascimento:</label>
								<input name="dtnasc" type="date" class="form-control" id="dtnasc" required>
							</div>
							<div class="form-group">
								<label for="dtcnh" class="control-label">Validade da CNH:</label>
								<input name="dtcnh" type="date" class="form-control" id="dtcnh" required>
							</div>
							<div class="form-group">
								<label for="username" class="control-label">Usuário:</label>
								<input name="username" type="text" class="form-control" id="username" required>
							</div>
							<div class="form-group">
								<label for="senha" class="control-label">Senha:</label>
								<input type="password" id="senha" name="senha" class="form-control" aria-describedby="passwordHelpInline" required>
								<span id="passwordHelpInline" class="form-text">
									Precisa conter no mínimo 6 caracteres.
								</span>
	
							</div>
							<div class="form-group">
								<label for="email" class="control-label">E-mail:</label>
								<input name="email" type="text" class="form-control" id="email" required>
							</div>
							<div class="form-group">
								<label for="nvacesso" class="control-label">Nível de acesso:</label>
								<select name="nvacesso" id="nvacesso" class="form-select"  required>

									<option selected></option>
										<?php 
											$SQL_NV_ACESSO = "SELECT * FROM tbNvAcesso";
											$result_nv_acesso = mysqli_query($conn, $SQL_NV_ACESSO);
											while($niveis_acesso = mysqli_fetch_assoc($result_nv_acesso)){ ?>
											<option value="<?php echo $niveis_acesso['ID']; ?>"><?php echo $niveis_acesso['NOME']; ?></option>
										<?php } ?>
								</select>
							</div>
							<!-- ENVIAR ARQUIVO -->
							<div class="form-group">
								<label for="file" class="control-label">Foto:</label>
								<input name="file" id="file" class="form-control" type="file" placeholder="Arquivo"  >
							</div>
							<!-- FIM ENVAIR ARQUIVo -->
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


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->

	<script>
		function user_sidebar() {
			$.post("query_user_sidebar.php", {}, function(x) { $("#userSidebar").html(x); } );
		}
	</script>

	<script type="text/javascript">
		$('#ModalEdit').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Button that triggered the modal
		  var recid = button.data('whatever')
		  var recnome = button.data('whatevernome')
		  var recdtnasc = button.data('whateverdtnasc')
		  var recdtcnh = button.data('whateverdtcnh')
		  var recusuario = button.data('whateverusuario')
		  //var recsenha = button.data('whateversenha')
		  var recemail = button.data('whateveremail')
		  //var recfoto = button.data('whateverfoto')
		  var recacesso = button.data('whateveracesso')
		
		  var modal = $(this)
		  modal.find('#id-edt').val(recid)
		  modal.find('#id-view-edt').val(recid)
		  //document.getElementById('nome-edt').required = false
		  modal.find('#nome-edt').val(recnome)
		  modal.find('#dtnasc-edt').val(recdtnasc)
		  modal.find('#dtcnh-edt').val(recdtcnh)
		  modal.find('#username-edt').val(recusuario)
		  //modal.find('#nome').val(recsenha)
		  modal.find('#email-edt').val(recemail)
		  //modal.find('#nome').val(recfoto)
		  modal.find('#nvacesso-edt').val(recacesso)
		  
		})
	</script>

	<script type="text/javascript">
		$('#removeUser').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Button that triggered the modal
		  var recid = button.data('whatever')
		  var recnome = button.data('whatevernome')
			
		  var modal = $(this)
		  modal.find('#id').val(recid)
		  modal.find('#nome').text(recnome)
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
	<script type="text/javascript">
		//Reset no formulário de nova manutenção
		$('#ModalNovo').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Button that triggered the modal


		  const formulario = document.querySelector('#form-new-user');
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