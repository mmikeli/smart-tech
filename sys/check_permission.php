<?php

	if(isset($_SESSION['chaveUsuario'])){}else{
		session_destroy();
		header("Location: /sign-in.php?erro");
		exit();
	}