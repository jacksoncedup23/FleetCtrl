<?php
	include_once("conexao.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>USUÁRIOS | FleetCtrl</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<style>
			.primeiro {
				border: 10px solid red;
				height: 85vh;
			}
			.text-content {
				color:darkslategrey;
				padding: 20px;
			}
			.dv-left {
				float: left;
				width: 48%;
				height: 100%;
				min-width: 500px;
				border: 2px solid black;
				align-items: center;
				display: flex;
				flex-direction: row;
				flex-wrap: wrap;
				justify-content: center;
			}
			.dv-right {
				float: right;
				width: 48%;
				height: 100%;
				border: 2px solid black;
			}
			.dv-right img {
				height: 100%;
			}
		</style>
	</head>
	<body>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
		<!-- BARRA DE NAVEGAÇÃO ---------------------------------------------------------------------------------- -->
		<nav class="sticky-top <?php echo $nav_class;?>">
		<div class="container-fluid">
			<a class="navbar-brand" href="#">FleetCtrl</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav me-auto mb-2 mb-lg-0">
					<li class="nav-item">
					<a class="nav-link active" aria-current="page" href="">Home</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" aria-current="page" href="usuarios.php">Usuários</a>
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
			<button class="btn btn-outline-secondary me-2" type="submit">Cadastre-se</button>
			<a href="login.php"><button class="btn btn-outline-primary" type="submit">Entrar</button></a>
			</div>
		</div>
	</nav>

	<div class="primeiro">
		<h7 id="fat">fat</h7>
		<div class="text-content dv-left">
			<h1>Planilhas desorganizadas, cadernetas e anotações em papel nunca mais!</h1>
			<br>
			<h4>Tenha ganhos administrativos e melhore o seu dia-a-dia de trabalho!</h4>
			Conheça seus lucros reais com cada veículo
			<br>
			Tenha em mãos o controle de manutenções e não perca dinheiro com indisponibilidade por falta de gestão!
		
		</div>
		<div class="dv-right">
			<img src="./images/img1.png">
		</div>

	</div>
	<div class="primeiro">
		<h7 id="mdo">mdo</h7>
		<div class="text-content dv-left">
			<h1>Planilhas desorganizadas, cadernetas e anotações em papel nunca mais!</h1>
			<br>
			<h4>Tenha ganhos administrativos e melhore o seu dia-a-dia de trabalho!</h4>
			Conheça seus lucros reais com cada veículo
			<br>
			Tenha em mãos o controle de manutenções e não perca dinheiro com indisponibilidade por falta de gestão!
		
		</div>
		<div class="dv-right">
			<img src="./images/img1.png">
		</div>

	</div>
  </body>
</html>