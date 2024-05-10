<?php
function getPrm($prmName)
{
	if (isset($_GET[$prmName])) {
		return $_GET[$prmName];
	} else {
		return "";
	}
}

use function PHPSTORM_META\map;


include_once("conexao.php");

//Verifica a quantidade de páginas
$SQLContPages = 'SELECT CEIL(COUNT(*) /' . $linesPerPage . ') as CONT FROM tbveiculos';
$contador = $conn->query($SQLContPages);

$contador = mysqli_fetch_assoc($contador);
$pagesCount = $contador['CONT'];

//Verifica a pagina que está sendo exibida
if (isset($_GET['page'])) {
	$page = $_GET['page'];
} else {
	$page = 1;
}
if (!is_numeric($page)) {
	$page = 1;
}
if ($page > $pagesCount and $pagesCount > 0) {
	$page = $pagesCount;
}

//Executa a consulta no banco de dados
$SQL_Consulta = "SELECT * FROM (SELECT v.*, tp.IMAGEM, tp.TIPO as 'NM_TIPO', COALESCE((SELECT MAX(cons.KM) FROM tbconsumo cons WHERE cons.ID_VEICULO = v.ID),v.KM_INICIAL) as KM_ATUAL FROM tbveiculos v, tbtipo_veiculo tp WHERE v.ID_TIPO = tp.ID ";
if (isset($_GET['search'])) {
	$busca = str_replace(" ", "%", $_GET['search']);
	$SQL_Consulta = $SQL_Consulta . " and (v.MARCA LIKE '%$busca%' or v.MODELO LIKE '%$busca%' or v.PLACA LIKE '%$busca%' or tp.TIPO LIKE '%$busca%')";
}

$SQL_Consulta = $SQL_Consulta . " LIMIT $linesPerPage OFFSET " . ($page - 1) * $linesPerPage;
$SQL_Consulta = $SQL_Consulta . ") a ";
if (isset($_GET["order"])) {
	$SQL_Consulta = $SQL_Consulta . "ORDER BY " . $_GET["order"];
	if (getPrm("dir") == "DESC") {
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
	<style>
		.lnkConsulta:hover {
			color: rgb(13, 110, 253);
			text-decoration: underline;
			cursor: pointer;
		}
	</style>
</head>

<body>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

	<!-- BARRA DE NAVEGAÇÃO ---------------------------------------------------------------------------------- -->
	<nav class="<?php echo $nav_class; ?>">
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
							<li><a class="dropdown-item disabled" href="#" data-bs-toggle="modal" data-bs-target="#ModalNovo">Novo</a></li>
							<li>
								<hr class="dropdown-divider">
							</li>
							<li><a class="dropdown-item" href="veiculos.php">Mostrar todos</a></li>
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
					<input class="form-control me-2" type="search" id="search" name="search" placeholder="Buscar" aria-label="Buscar" value="<?php if (isset($_GET["search"])) {
																																					echo $_GET["search"];
																																				} ?>">
					<button class="<?php echo $nav_class_btn_busca; ?>" type="submit">Buscar</button>
				</form>
				<button class="btn btn-outline-danger" type="submit">Sair</button>
			</div>
		</div>
	</nav>
	<!-- BARRA DE NAVEGAÇÃO ---------------------------------------------------------------------------------- -->

	<div class="container">
		<div class="row row-cols-3 row-cols-md-<?php echo $veiculos_por_coluna; ?> g-3">
			<?php while ($users = mysqli_fetch_assoc($result_users)) { ?>
				<div class="col">
					<div class="card h-100">
						<img src="./images/<?php echo $users['IMAGEM']; ?>" class="card-img-top" alt="...">
						<div class="card-body">
							<h5 class="card-title"><span class="lnkConsulta" data-bs-toggle="modal" data-bs-target="#Consulta<?php echo $users['ID']; ?>"><?php echo $users['MARCA'] . " - " . $users['MODELO'] . " - " . $users['ANO']; ?></span></h5>
							<div class="card-text">
								Status de manutenções:
							</div>
							<p class="card-text">KM/litro atual</p>
							<a href="#" class="btn btn-primary"><?php echo $users['MARCA']; ?></a>
						</div>
						<div class="card-footer">
							<small class="text-body-secondary">Last updated 3 mins ago</small>
						</div>
					</div>
				</div>

				<!-- Inicio Modal consulta-->
				<div class="modal fade" id="Consulta<?php echo $users['ID']; ?>" tabindex="-1" aria-labelledby="TitleConsulta" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h1 class="modal-title fs-5" id="TitleConsulta"><?php echo $users['MARCA'] . " - " . $users['MODELO'] . " | " . $users['ANO']; ?></h1>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">

								<div class="VeicCod"><strong>Código: </strong><?php echo $users['ID']; ?></div>
								<div class="VeicTipo"><strong>Tipo: </strong><?php echo $users['NM_TIPO']; ?></div>
								<div class="VeicMarca"><strong>Marca: </strong><?php echo $users['MARCA']; ?></div>
								<div class="VeicModelo"><strong>Modelo: </strong><?php echo $users['MODELO']; ?></div>
								<div class="VeicAno"><strong>Ano: </strong><?php echo $users['ANO']; ?></div>
								<div class="VeicPlaca"><strong>Placa: </strong><?php echo $users['PLACA']; ?></div>
								<div class="VeicKMInit"><strong>KM Inicial: </strong><?php echo $users['KM_INICIAL']; ?></div>
								<div class="VeicKMAtual"><strong>KM Atual: </strong><?php echo $users['KM_INICIAL']; ?></div>
								<br>


							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
							</div>
						</div>
					</div>
				</div>
				<!-- Fim Modal -->
			<?php
			}
			?>



		</div>
	</div>
</body>

</html>