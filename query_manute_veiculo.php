<?php
    //Consulta e retorna as manutenções para o veículo
	include_once("conexao.php");
    if (isset($_POST['id_veiculo'])){
        $id_veiculo = $_POST['id_veiculo'];
    } else
    {
        $id_veiculo = null;
    }
    
    $SQL_Consulta = "SELECT m.ID, m.NOME FROM tbmanutveic mv, tbmanutencao m WHERE m.ID = mv.ID_MANUTENCAO and mv.ID_VEICULO = $id_veiculo";
    $results = mysqli_query($conn, $SQL_Consulta);
?>

<option selected></option>
<?php 
    while($tipo_manu = mysqli_fetch_assoc($results)){ ?>
    <option value="<?php echo $tipo_manu['ID']; ?>"><?php echo $tipo_manu['NOME']; ?></option>
<?php } ?>
