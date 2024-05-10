<?php
    //Consulta e retorna as manutenções para o veículo
	include_once("conexao.php");
    if (isset($_POST['marca'])){
        $marca = $_POST['marca'];
    } else
    {
        $marca = null;
    }
    
    $SQL_Consulta = "SELECT MODELO, COUNT(ID) as CONT FROM tbveiculos WHERE marca = '$marca' GROUP BY MODELO ORDER BY 2 DESC, 1 ASC LIMIT 10";
    $result = mysqli_query($conn, $SQL_Consulta);
?>
<?php 
    if(empty($marca)) {
        ?>
        <li><a class="dropdown-item disabled" href="#">Nada encontrado!</a></li>
        <?php
    } else {
        while($results = mysqli_fetch_assoc($result)){
            $n++;
        ?>
            
            <li><a class="dropdown-item" href="#" onclick="setModelo('<?php echo $results['MODELO'];?>')"><?php echo $results['MODELO'];?></a></li>
    <?php 
        }
        if($n==0) {
            ?>
                <li><a class="dropdown-item disabled" href="#">Nada encontrado!</a></li>
            <?php
        }
    }
    ?>



