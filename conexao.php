<?php
	$servidor = "localhost:3306";
	$usuario = "root";
	$senha = "cedup123";
	$dbname = "dbfleetctrl";
	
	//Opções DEV
	$showSQL = false;
	$forceLogin = true;

	//Definição global de quantas linhas devem estar em cada página
	$linesPerPage = 10;
	$param_dias_vencendo = 30; // Define em até quantos dias é considerado como "vencendo"
	$param_km_vencendo = 200; // Define em até quantos quilômetros considerado como "vencendo"
	$nav_class_btn_busca = "btn btn-outline-secondary me-2";
	$nav_class = "sticky-top navbar navbar-expand-lg bg-body-tertiary";
	$veiculos_por_coluna = 4; // Cards de veículos (colunas)

	//Criar a conexão
	$conn = mysqli_connect($servidor, $usuario, $senha, $dbname);

	function formatDate($dt) {
		$date = DateTime::createFromFormat('Y-m-d', $dt)->format('d/m/Y');
		return $date;
	}
	

	//Código para ler e definir Cookie
	//	echo $_COOKIE['veic-per-col'];
	//	setcookie("veic-per-col","",time()+8600);
?>