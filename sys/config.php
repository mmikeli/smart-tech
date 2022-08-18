<?php
		
	if(session_status() != PHP_SESSION_ACTIVE) {
        ini_set('session.use_strict_mode', 0);
        session_start();
    }

	$strcon = mysqli_connect('194.195.84.204', 'u296526003_root', 'Mj05012018@', 'u296526003_app') or die('Erro ao conectar ao banco de dados');

?>