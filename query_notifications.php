<?php
//Consulta e retorna as manutenções para o veículo
include_once("conexao.php");
include_once("valida_user.php");
?>

<div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasTopLabel">NOTIFICAÇÕES (<span id="notfCount">150</span>)</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
</div>
<div class="offcanvas-body">
    <div class="notificationsContent">
        <div class="notificationsGroup">
            <h5>Usuários</h5>
            <ol class="list-group list-group-numbered">
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">Subheading</div>
                        Content for list item
                    </div>
                    <span class="badge bg-danger rounded-pill">14</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">Subheading</div>
                        Content for list item
                    </div>
                    <span class="badge bg-warning rounded-pill">14</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">Subheading</div>
                        Content for list item
                    </div>
                    <span class="badge bg-success rounded-pill">14</span>
                </li>
            </ol>
        </div>
        <div class="notificationsGroup">
            <h5>Veículos</h5>
            <ol class="list-group list-group-numbered">
                <a href="veiculos_list.php">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Subheading</div>
                            Content for list item
                        </div>
                        <span class="badge bg-danger rounded-pill">14</span>
                    </li>
                </a>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">Subheading</div>
                        Content for list item
                    </div>
                    <span class="badge bg-warning rounded-pill">14</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="ms-2 me-auto">
                        <div class="fw-bold">Subheading</div>
                        Content for list item
                    </div>
                    <span class="badge bg-success rounded-pill">14</span>
                </li>
            </ol>
        </div>
    </div>
    
</div>