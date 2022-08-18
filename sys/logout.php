<?php
    
  if(session_status() != PHP_SESSION_ACTIVE) {
        ini_set('session.use_strict_mode', 0);
        session_start();
    }

  include("config.php");

  if(isset($_SESSION['chaveUsuario'])){

  	$chaveUsuario = $_SESSION['chaveUsuario'];

  	$sql = "UPDATE usuarios SET status = '0' WHERE chave = '$chaveUsuario'";
  	$qry = $strcon->query($sql) or die($strcon->error);
		
  	session_destroy();
  	header("Location: /sign-in.php");
  	exit();

  }