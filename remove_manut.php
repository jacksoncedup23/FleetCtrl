<?php
	include_once("conexao.php");
	$id = mysqli_real_escape_string($conn, $_POST['id']);
	
	$sql = "DELETE FROM tbmanutencao WHERE ID = '$id'";
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
			echo "<META HTTP-EQUIV=REFRESH CONTENT = '0;URL=manutencao.php?msg=1'>";	
		}else{
			//Se afetou alguma linha, envia para a página inicial com a mensagem 2 (erro ao excluir)
			echo "<META HTTP-EQUIV=REFRESH CONTENT = '0;URL=manutencao.php?msg=2'>";	
		}?>
	</body>
</html>
<?php
	//$conn->close();
?>