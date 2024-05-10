<?php
	include_once("conexao.php");
	$id = mysqli_real_escape_string($conn, $_POST['id']);
	$idVeic = $_POST['id'];
	$km = $_POST['kmact'];
	$valor = $_POST['valor'];
	$litros = $_POST['litros'];
	$data = $_POST['dat'];

	$sql = "INSERT INTO tbconsumo (ID_VEICULO, KM, VALOR, LITROS, DATA) VALUES ($idVeic, $km, $valor, $litros, '$data')";
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
			//Se afetou alguma linha, envia para a página inicial com a mensagem 1 (sucesso)
			echo "<META HTTP-EQUIV=REFRESH CONTENT = '0;URL=veiculos.php?msg=9'>";	
		}else{
			//Se afetou alguma linha, envia para a página inicial com a mensagem 2 (erro ao excluir)
			echo "<META HTTP-EQUIV=REFRESH CONTENT = '0;URL=veiculos.php?msg=10'>";	
		}?>
	</body>
</html>
<?php
	//$conn->close();
?>