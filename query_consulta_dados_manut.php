<?php
	include_once("conexao.php");
    if (isset($_POST['id_veic_manut'])){
        $id_veic_manut = $_POST['id_veic_manut'];
    } else
    {
        $id_veic_manut = null;
    }
    
    if (!empty($id_veic_manut)){
        $SQL_Consulta = "SELECT COALESCE(em.KM,v.KM_INICIAL) as KM,
                        COALESCE(em.`DATA`,v.DT_CADASTRO) as DT_MANUT,
                        COALESCE(em.VALOR,0) as VL_MANUT,
                        (COALESCE(em.KM,v.KM_INICIAL) + m.FREQ_KM) AS PROX_KM_MANUT,
                        (COALESCE(em.DATA,v.DT_CADASTRO) + INTERVAL m.FREQ_MESES MONTH) as PROX_DT_MANUT
                        FROM
                            tbmanutveic mv
                            INNER JOIN tbveiculos v ON (mv.ID_VEICULO=v.ID)
                            INNER JOIN tbmanutencao m ON (m.ID=mv.ID_MANUTENCAO)
                            LEFT JOIN tbexecmanut em ON
                                (
                                    em.ID_MANUTENCAO=mv.ID
                                    AND em.`DATA`=(SELECT MAX(DATA) FROM tbexecmanut WHERE ID_MANUTENCAO=mv.ID)
                                    AND em.KM=(SELECT MAX(KM) FROM tbexecmanut WHERE ID_MANUTENCAO=mv.ID)
                                )	
                            WHERE mv.ID = $id_veic_manut";
        if($showSQL) { echo $SQL_Consulta;}
        $results = mysqli_query($conn, $SQL_Consulta);
        $result = mysqli_fetch_assoc($results);
    }
?>

<div class="row align-items-start">
    <div class="col-4">
        <label for="lastkm_manut" class="control-label">Última KM:</label>
        <input name="lastkm" type="text" class="form-control" id="lastkm_manut" value="<?php if (!empty($id_veic_manut)){ echo $result['KM']; } ?>" disabled>
    </div>
    <div class="col-4">
        <label for="lastdt_manut" class="control-label">Última data:</label>
        <input name="lastdt" type="date" class="form-control" id="lastdt_manut" value="<?php if (!empty($id_veic_manut)){ echo $result['DT_MANUT'];}?>" disabled>
    </div>
    <div class="col-4">
        <label for="lastvl_manut" class="control-label">Último valor:</label>
        <input name="lastvl" type="number" class="form-control" id="lastvl_manut" value="<?php if (!empty($id_veic_manut)){ echo $result['VL_MANUT'];}?>" disabled>
    </div>
    <?php
        if(isset($result['PROX_KM_MANUT']) and isset($result['PROX_DT_MANUT'])){
            if (!empty($result['PROX_KM_MANUT'])){
                $PROX_KM = $result['PROX_KM_MANUT'];
            } else {
                $PROX_KM = "";
            }
            if (!empty($result['PROX_DT_MANUT'])){
                $PROX_DT = $result['PROX_DT_MANUT'];
            } else {
                $PROX_DT = "";
            }
            echo '</div>
                <div class="row align-items-start">
                    <div class="col-4">
                        <label for="nextkm_manut" class="control-label">Próxima KM:</label>
                        <input name="nextkm" type="text" class="form-control" id="nextkm_manut" value="' . $PROX_KM . '" disabled>
                    </div>
                    <div class="col-2 align-self-end">
                        <button type="button" class="btn btn-outline-secondary" onClick="setKMManut()" >Usar</button>
                    </div>
                    <div class="col-4">
                        <label for="nextdt_manut" class="control-label">Próxima data:</label>
                        <input name="nextdt" type="date" class="form-control" id="nextdt_manut" value="' . $PROX_DT . '" disabled>
                    </div>
                    <div class="col-2 align-self-end">
                        <button type="button" class="btn btn-outline-secondary" onClick="setDTManut()">Usar</button>
                    </div>';
        }
    ?>
</div>	