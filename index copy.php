<?php

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

	include_once("conexao.php");
	
	if(isset($_POST['submit']))
	{
		$id = $_POST['id'];
		$nome = $_POST['nome'];
		$dt_nascimento = $_POST['dtnasc'];
		$usuario = $_POST['username'];		
		$senha = trim($_POST['senha']);
		$email = $_POST['email'];
		$nivel = $_POST['nvacesso'];
		$erro_repetido = 0;

	    if (isset($id) and !empty($id)) 
			{
				//Se foi informado um ID, então deve alterar o registro
				$SQL = "UPDATE TBUSUARIOS SET NOME='$nome',DT_NASCIMENTO='$dt_nascimento', USUARIO='$usuario', EMAIL = '$email', NV_ACESSO_ID='$nivel'";
				$SQL_CHECK = "SELECT COUNT(ID) as cont FROM TBUSUARIOS WHERE (NOME='$nome' or USUARIO='$usuario' or EMAIL='$email') and ID!=$id";
				$erro_repetido = 7;

				if(isset($senha) and !empty($senha)) 
				{
					$SQL = $SQL . ",SENHA = '$senha'";
				}

				$SQL = $SQL . " WHERE ID = $id";

				
				
			}
			else
			{
				//ID não informado, deve incluir um novo registro
				$SQL = "INSERT INTO TBUSUARIOS (NOME, DT_NASCIMENTO, USUARIO, SENHA, EMAIL, NV_ACESSO_ID) VALUES ('$nome','$dt_nascimento','$usuario', '$senha', '$email', '$nivel')";
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
	else
	{
		//Se nao teve nenhum formulário submitado, verifica se deve mostrar alguma mensagem
		if (isset($_GET["msg"])) {
			$idMsg = $_GET["msg"];
		}
		else
		{
			$idMsg = 0;
		}
	}	

	//Verifica a quantidade de páginas
	$SQLContPages = 'SELECT CEIL(COUNT(*) /' . $linesPerPage . ') as CONT FROM tbUsuarios';
    $contador = $conn->query($SQLContPages);
    
    $contador = mysqli_fetch_assoc($contador);
    $pagesCount = $contador['CONT'];

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
	if ($page > $pagesCount)
		{
			$page = $pagesCount;
		}

	//Executa a consulta no banco de dados
	$SQL_Consulta = "SELECT u.*, a.ID as 'IDNIVEL', a.NOME as 'NIVEL' FROM tbUsuarios u,tbNvAcesso a WHERE u.NV_ACESSO_ID = a.ID";
	if (isset($_GET['search']))
	{
		$busca = str_replace(" ", "%", $_GET['search']);
		$SQL_Consulta = $SQL_Consulta . " and (u.NOME LIKE '%$busca%' or u.USUARIO LIKE '%$busca%' or u.EMAIL LIKE '%$busca%')";
	}

	$SQL_Consulta = $SQL_Consulta . " LIMIT $linesPerPage OFFSET " . ($page - 1) * $linesPerPage;
	//$result_users = mysqli_query($conn, $SQL_Consulta);
	
	

	
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>USUÁRIOS | FleetCtrl</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		
	</head>
	<body>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
			<div class="page-header">
				<h1>Usuários</h1>
			</div>
			<form method="get" action="#" class="row g-3">
				<div class="col-auto">
					<input type="text" class="form-control" id="search" name="search" placeholder="buscar" value="<?php echo $_GET['search']; ?>">
				</div>
				<div class="col-auto">
					<button type="submit" class="btn btn-secondary mb-3">Buscar</button>
				</div>
				<div class="col-auto">
					<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ModalNovo">Novo usuário</button>
				</div>
			</form>
			<div class="row">
				<div class="col-md-12">
					<table class="table">
						<thead>
							<tr>
								<th>#</th>
								<th>Usuário</th>
								<th>E-mail</th>
								<th>Nível de acesso</th>
								<th>Ação</th>
							</tr>
						</thead>
						<tbody>
							<?php while($users = mysqli_fetch_assoc($result_users)){ ?>
								<tr>
									<td class="align-middle"><?php echo $users['ID']; ?></td>
									<td class="align-middle"><?php echo $users['NOME']; ?></td>
									<td class="align-middle"><?php echo $users['EMAIL']; ?></td>
									<td class="align-middle"><?php echo $users['NIVEL']; ?></td>
									<td class="align-middle">
										<button type="button" class="btn btn-xs btn-primary" data-bs-toggle="modal" data-bs-target="#Consulta<?php echo $users['ID']; ?>"><img src="./images/visualizar.png"></button>
										<button type="button" class="btn btn-xs btn-warning" data-bs-toggle="modal" data-bs-target="#ModalEdit" data-whatever="<?php echo $users['ID']; ?>" data-whatevernome="<?php echo $users['NOME']; ?>" data-whateverdtnasc="<?php echo $users['DT_NASCIMENTO']; ?>" data-whateverusuario="<?php echo $users['USUARIO']; ?>" data-whateveremail="<?php echo $users['EMAIL']; ?>" data-whateveracesso="<?php echo $users['IDNIVEL']; ?>"><img src="./images/editar.png"></button>
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

											<div class="UserCod"><strong>Código: </strong><?php echo $users['ID']; ?></div>
											<div class="UserNome"><strong>Nome: </strong><?php echo $users['NOME']; ?></div>	
											<div class="UserUsuario"><strong>Usuário: </strong><?php echo $users['USUARIO']; ?></div>
											<div class="UserDt"><strong>Data de Nascimento: </strong><?php echo $users['DT_NASCIMENTO']; ?></div>
											<div class="Useremail"><strong>E-mail: </strong><?php echo $users['EMAIL']; ?></div>
											<br>
											
											<div class="UserNvAccesso"><strong>Nível de acesso: </strong><?php echo $users['NIVEL']; ?></div>
																					
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
			<!-- Paginação -->
			<nav aria-label="...">
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

					<li class="page-item <?php if ($page == $pagesCount) {echo "disabled";}?>">
						<a class="page-link" href="?page=<?php echo ($page+1); ?>">Próxima</a>
					</li>
				</ul>
			</nav>
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
						
							<input name="id" type="hidden" class="form-control" id="id" value="">
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
					<form method="POST" action="?" enctype="multipart/form-data">
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
							<label for="username" class="control-label">Usuário:</label>
							<input name="username" type="text" class="form-control" id="username" required>
						</div>
						<div class="form-group">
							<label for="senha" class="control-label">Senha:</label>
    						<input type="password" id="senha" name="senha" class="form-control" aria-describedby="passwordHelpInline">
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
							<select name="nvacesso" id="nvacesso" class="form-select" required>

								<option selected>Selecione um nível de acesso</option>
									<?php 
										$SQL_NV_ACESSO = "SELECT * FROM tbNvAcesso";
										$result_nv_acesso = mysqli_query($conn, $SQL_NV_ACESSO);
										while($niveis_acesso = mysqli_fetch_assoc($result_nv_acesso)){ ?>
										<option value="<?php echo $niveis_acesso['ID']; ?>"><?php echo $niveis_acesso['NOME']; ?></option>
									<?php } ?>
							</select>
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
	
	<!-- Modal Novo Usuário -->

		<div class="modal fade" id="ModalNovo" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h1 class="modal-title fs-5" id="staticBackdropLabel">Novo usuário</h1>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form method="POST" action="?" enctype="multipart/form-data">
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

									<option selected>Selecione um nível de acesso</option>
										<?php 
											$SQL_NV_ACESSO = "SELECT * FROM tbNvAcesso";
											$result_nv_acesso = mysqli_query($conn, $SQL_NV_ACESSO);
											while($niveis_acesso = mysqli_fetch_assoc($result_nv_acesso)){ ?>
											<option value="<?php echo $niveis_acesso['ID']; ?>"><?php echo $niveis_acesso['NOME']; ?></option>
										<?php } ?>
								</select>
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


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->



	<script type="text/javascript">
		$('#ModalEdit').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Button that triggered the modal
		  var recid = button.data('whatever')
		  var recnome = button.data('whatevernome')
		  var recdtnasc = button.data('whateverdtnasc')
		  var recusuario = button.data('whateverusuario')
		  //var recsenha = button.data('whateversenha')
		  var recemail = button.data('whateveremail')
		  //var recfoto = button.data('whateverfoto')
		  var recacesso = button.data('whateveracesso')
		
		  var modal = $(this)
		  modal.find('#id').val(recid)
		  modal.find('#id-view').val(recid)
		  modal.find('#nome').val(recnome)
		  modal.find('#dtnasc').val(recdtnasc)
		  modal.find('#username').val(recusuario)
		  //modal.find('#nome').val(recsenha)
		  modal.find('#email').val(recemail)
		  //modal.find('#nome').val(recfoto)
		  modal.find('#nvacesso').val(recacesso)
		  
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

  </body>
</html>