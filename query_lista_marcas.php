<?php
    //Consulta e retorna as manutenções para o veículo
	include_once("conexao.php");
    if (isset($_POST['tipo']) and !empty($_POST['tipo'])){
        $tipo = $_POST['tipo'];
        $SQL_Consulta = "SELECT MARCA, COUNT(ID) as CONT FROM tbveiculos WHERE ID_TIPO=$tipo GROUP BY MARCA ORDER BY 2 DESC, 1 ASC LIMIT 10";
    } else
    {
        $marca = null;
        $SQL_Consulta = "SELECT MARCA, COUNT(ID) as CONT FROM tbveiculos GROUP BY MARCA ORDER BY 2 DESC, 1 ASC LIMIT 10";
    }
        
    //$SQL_Consulta = "SELECT MARCA, COUNT(ID) as CONT FROM tbveiculos GROUP BY MARCA ORDER BY 2 DESC, 1 ASC LIMIT 10";
    $result = mysqli_query($conn, $SQL_Consulta);
?>
<?php 
    while($results = mysqli_fetch_assoc($result)){
        $n++;
?>
    <li><a class="dropdown-item" href="#" onclick="setMarca('<?php echo $results['MARCA'];?>')"><?php echo $results['MARCA'];?></a></li>
<?php
    }
    if($n==0) {
        
        ?>
            <li><a class="dropdown-item disabled" href="#">Nada encontrado!</a></li>
        <?php
    }

?>



