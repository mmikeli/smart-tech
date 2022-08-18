<?php
		
if(session_status() != PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', 0);
    session_start();
}

include("config.php");

if(empty($_POST['email']) || empty($_POST['senha'])){

	header("Location: /sign-in.php?empty");

}

$email = $_POST['email'];
$senha = $_POST['senha'];

$sqlLogin = "SELECT chave, chave_revenda, nome FROM usuarios WHERE email = '$email' AND senha = '$senha'";
$qryLogin = $strcon->query($sqlLogin) or die($strcon->error);
$row = mysqli_num_rows($qryLogin);

if($row == 1){

	$dadosUsuario = mysqli_fetch_assoc($qryLogin);

	$chaveUsuario = $dadosUsuario['chave'];
	$chaveRevenda = $dadosUsuario['chave_revenda'];
	$nomeUsuario = $dadosUsuario['nome'];

	$sqlRev = "SELECT nivel FROM revenda WHERE chave = '$chaveRevenda'";
	$qryRev = $strcon->query($sqlRev) or die($strcon->error);
	$dadosRevenda = mysqli_fetch_assoc($qryRev);

	$nivelRevenda = $dadosRevenda['nivel'];

	$sqlEtq = "SELECT chave FROM estoque WHERE chave_revenda = '$chaveRevenda'";
	$qryEtq = $strcon->query($sqlEtq) or die($strcon->error);
	$dadosEstoque = mysqli_fetch_assoc($qryEtq);

	$chaveEstoque = $dadosEstoque['chave'];

	$sqlUsu = "UPDATE usuarios SET status = '1' WHERE chave = '$chaveUsuario'";
	$qryUsu = $strcon->query($sqlUsu) or die($strcon->error);

	$_SESSION["chaveEstoque"] = $chaveEstoque;
	$_SESSION["chaveRevenda"] = $chaveRevenda;
	$_SESSION["nivelRevenda"] = $nivelRevenda;
	$_SESSION["chaveUsuario"] = $chaveUsuario;
	$_SESSION["nomeUsuario"] = $nomeUsuario;

	header("Location: /estoque.php"); exit();

}else{

	header("Location: /sign-in.php?incorrect"); exit();

}