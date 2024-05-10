<?php
    //Consulta e retorna as manutenções para o veículo
    include_once("conexao.php");
    include_once("valida_user.php");
?>

<div class="offcanvas-header">

    <h5 class="offcanvas-title" id="userPanelLabel">BEM-VINDO</h5>
    <a href="logout.php"><button type="button" class="btn btn-outline-danger btn-logout">Sair</button></a>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
</div>
<div class="offcanvas-body">
    <div class="card" style="width: 100%;">
        <img src="<?php echo $LogUserData['IMAGE']; ?>" class="userImg card-img-top" alt="Imagem do usuário">
        <div class="card-body">
            <h5 class="card-title mb-0 border-bottom"><a class="lnkUser" href="<?php echo "usuarios.php?search=" . $LogUserData['ID']; ?>"><?php echo $LogUserData['NOME']; ?></a></h5>
            <p class="card-text"><?php echo $LogUserData['ACESSO']; ?></p>
            <p class="card-text"><?php echo $LogUserData['EMAIL']; ?></p>
            
            
        </div>
    </div>
</div>