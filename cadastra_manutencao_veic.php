<?php
	include_once("conexao.php");
	//$id = mysqli_real_escape_string($conn, $_POST['id']);
	$veiculo = $_POST['id'];
	$idVeicManut = $_POST['tpmanut'];
	$km_exec = $_POST['kmExec'];
	$data_exec = $_POST['dtExec'];
	$valor = $_POST['valor'];
	$obs = $_POST['obs_manut'];
	$id_exec = $_POST['id_executor_manut'];


	$sql = "INSERT INTO tbexecmanut (DATA, KM, VALOR, OBS, ID_MANUTENCAO, ID_EXECUTOR) VALUES 
			('$data_exec', '$km_exec', '$valor', '$obs', $idVeicManut, $id_exec)";
	echo $sql;
	$resultado = mysqli_query($conn, $sql);	
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
	</head>

	<body> <?php
		if(mysqli_affected_rows($conn) != 0){
			//Se afetou alguma linha, envia para a página inicial com a mensagem sucesso
			echo "<META HTTP-EQUIV=REFRESH CONTENT = '0;URL=veiculos.php?msg=11&open_vehicle=$veiculo''>";	
		}else{
			//Se afetou alguma linha, envia para a página inicial com a mensagem erro
			echo "<META HTTP-EQUIV=REFRESH CONTENT = '0;URL=veiculos.php?msg=12'>";	
		}?>
	</body>
</html>
<?php
	//$conn->close();
?>