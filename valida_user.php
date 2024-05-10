<?php
    
    session_start();
		
	if((!isset($_SESSION['user']) == true or !isset($_SESSION['userID']) == true or !isset($_SESSION['userName']) == true) and $forceLogin==true)
	{
		unset($_SESSION['user']);
		header('Location: login.php');
	}

	$LogUser = $_SESSION['user'];
	$LogUserID = $_SESSION['userID'];
	$LogUserName = $_SESSION['userName'];
	
	include_once("conexao.php");
    
	$SQL = "SELECT u.*, nv.NOME as ACESSO, nv.ADD, nv.REMOVE, nv.EDIT FROM tbusuarios u, tbnvacesso nv WHERE nv.ID=u.ID_NV_ACESSO AND u.ID = $LogUserID";
	$LogUserData = $conn->query($SQL);
    $LogUserData = mysqli_fetch_assoc($LogUserData);
	$LogUserAdd = $LogUserData['ADD'];
	$LogUserRemove = $LogUserData['REMOVE'];
	$LogUserEdit = $LogUserData['EDIT'];

?>