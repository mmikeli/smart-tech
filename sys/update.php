<?php
	date_default_timezone_set('America/Sao_Paulo');
	//max_execution_time(300);
		
	if(session_status() != PHP_SESSION_ACTIVE) {
        ini_set('session.use_strict_mode', 0);
        session_start();
    }

include("sys/check_permission.php");
include("config.php");

$chaveRevenda = $_SESSION['chaveRevenda'];
$nivelRevenda = $_SESSION['nivelRevenda'];
$chaveEstoque = $_SESSION['chaveEstoque'];
$nomeUsuario = $_SESSION['nomeUsuario'];

$hoje = date('Y-m-d');
$agora = date('H:i:s');
$chain = true;

if(isset($_GET['revenda'])){

	$revID = $_GET['i'];

	$nome = $_POST['nome'];
	$email = $_POST['email'];
	$senha = $_POST['senha'];

	$sql = "UPDATE usuarios SET nome = '$nome', email = '$email', senha = '$senha' WHERE chave = '$revID'";
	$qry = $strcon->query($sql) or die($strcon->error);

	$sql = "UPDATE revenda SET nome = '$nome' WHERE chave = (SELECT chave_revenda FROM usuarios WHERE chave = '$revID')";
	$qry = $strcon->query($sql) or die($strcon->error);

	if($qry){
		header("Location: /cad-revenda.php?success&edit");
	}else{
		header("Location: /cad-revenda.php?fail&edit");
	}

}

if(isset($_GET['usuario'])){

	$usuID = $_GET['i'];

	$nome = $_POST['nome'];
	$email = $_POST['email'];
	$senha = $_POST['senha'];

	$sql = "UPDATE usuarios SET nome = '$nome', email = '$email', senha = '$senha' WHERE chave = '$usuID'";
	$qry = $strcon->query($sql) or die($strcon->error);

	if($qry){
		header("Location: /usuarios.php?success&edit");
	}else{
		header("Location: /usuarios.php?fail&edit");
	}

}

if(isset($_GET['distribuir'])){

	if(empty($_POST['revendedor']) || empty($_POST['produto']) || empty($_POST['quantidade'])){
		$chain = false;
	}else{

		$chaveRevendedor = $_POST['revendedor'];
		$chaveProduto = $_POST['produto'];
		$quantidade = $_POST['quantidade'];

		$sqlProd = "SELECT p.nome, (SELECT c.nome FROM categorias c WHERE c.chave = p.chave_categoria) as categoria FROM produtos p WHERE chave = '$chaveProduto'";
		$qryProd = $strcon->query($sqlProd) or die($strcon->error);
		$conProd = mysqli_fetch_assoc($qryProd);
		$nomeProduto = $conProd['nome']. ' ' .$conProd['categoria'];

	}

	if($chain === true){

		$sqlFR = "SELECT revenda.chave, revenda.nome, (SELECT estoque.chave FROM estoque WHERE estoque.chave_revenda = revenda.chave) as chaveEstoque FROM revenda WHERE revenda.chave = '$chaveRevendedor'";
		$qryFR = $strcon->query($sqlFR) or die($strcon->error);
		$conFR = mysqli_fetch_assoc($qryFR);
		
		$nomeRevenda = $conFR['nome'];
		$chave_estoque = $conFR['chaveEstoque'];

		if($qryFR){

			$sqlL = "INSERT INTO lote_distribuicao VALUES (NULL, '$chaveProduto', '$chaveRevendedor', '$quantidade', '0', '$hoje')";
			$qryL = $strcon->query($sqlL) or die($strcon->error);
			$chaveDistribuicao = $strcon->insert_id;

		}else{
			$chain = false;
		}

		for($i = 1; $i <= $quantidade; $i++){

			if($chain === true){

				$sqlS = "SELECT chave, chave_lote_importacao FROM seriais WHERE chave_estoque = '1' AND chave_produto = '$chaveProduto' AND status = '0' LIMIT 1";
				$qryS = $strcon->query($sqlS) or die($strcon->error);
				$conS = mysqli_fetch_assoc($qryS);
				if(mysqli_num_rows($qryS) !== 1){

					header("Location: /estoque.php?error"); exit();

				}

				$chaveSerial = $conS['chave'];
				$chaveImportacao = $conS['chave_lote_importacao'];

				$sqlSS = "UPDATE seriais SET chave_estoque = '$chave_estoque', chave_lote_distribuicao = '$chaveDistribuicao' WHERE chave = '$chaveSerial'";
				$qrySS = $strcon->query($sqlSS) or die($strcon->error);

				$sqlLog = "INSERT INTO log VALUES (NULL, '$chaveSerial', 'SMART TECH $nomeUsuario', '$nomeRevenda', '$nomeProduto', 'SERIAL-DISTRIBUIDO', '$hoje', '$agora')";
				$qryLog = $strcon->query($sqlLog) or die($strcon->error);

				$sqlImp = "UPDATE lote_importacao SET seriais_distribuidos = seriais_distribuidos + 1 WHERE chave = '$chaveImportacao'";
				$qryImp = $strcon->query($sqlImp) or die($strcon->error);

			}

		}

		if($chain === true){
			header("Location: /estoque.php?success&distribuir&q=".$quantidade); exit();
		}else{
			header("Location: /estoque.php?fail&distribuir"); exit();
		}

	}else{
			header("Location: /estoque.php?fail&distribuir"); exit();
	}

}

if(isset($_GET['gerar'])){

	$sqlRV = "SELECT nome FROM revenda WHERE chave = '$chaveRevenda'";
	$qryRV = $strcon->query($sqlRV) or die($strcon->error);
	$conRV = mysqli_fetch_assoc($qryRV);
	$nomeRevenda = $conRV['nome'];

	if(empty($_POST['produto']) || empty($_POST['quantidade'])){
		$chain = false;
	}else{

		$chaveProduto = $_POST['produto'];

		$sqlNP = "SELECT p.nome, (SELECT c.nome FROM categorias c WHERE c.chave = p.chave_categoria) as categoria FROM produtos p WHERE chave = '$chaveProduto' LIMIT 1";
		$qryNP = $strcon->query($sqlNP) or die($strcon->error);
		$conNP = mysqli_fetch_assoc($qryNP);

		$nomeProduto = $conNP['nome']. ' ' .$conNP['categoria'];
		$quantidade = $_POST['quantidade'];
		$cliente = $_POST['cliente'];

	}

	if($chain === true){

		$sqlLista = "SELECT codigo_lista FROM temp ORDER BY codigo_lista DESC LIMIT 1";
		$qryLista = $strcon->query($sqlLista) or die($strcon->error);
		$conLista = mysqli_fetch_assoc($qryLista);
		$num = $conLista['codigo_lista'];

		$codigoLista = $num + 1;

		$sqlSS = "SELECT chave, codigo, chave_lote_distribuicao FROM seriais WHERE status = '0' AND chave_estoque = '$chaveEstoque' AND chave_produto = '$chaveProduto' LIMIT $quantidade";
		$qrySS = $strcon->query($sqlSS) or die($strcon->error);
		
		while($conSS = $qrySS->fetch_assoc()){

			$chaveSerial = $conSS['chave'];
			$codigoSerial = $conSS['codigo'];
			$chaveDistribuicao = $conSS['chave_lote_distribuicao'];

			$sqlS = "UPDATE seriais SET status = '1', saida = '$hoje' WHERE chave = '$chaveSerial'";
			$qryS = $strcon->query($sqlS) or die($strcon->error);

			$sqlLog = "INSERT INTO log VALUES(NULL, '$chaveSerial', '$nomeRevenda', '$cliente', '$nomeProduto', 'SERIAL-CONSUMIDO', '$hoje', '$agora')";
			$qryLog = $strcon->query($sqlLog) or die($strcon->error);

			$sqlDist = "UPDATE lote_distribuicao SET seriais_consumidos = seriais_consumidos + 1 WHERE chave = '$chaveDistribuicao'";
			$qryDist = $strcon->query($sqlDist) or die($strcon->error);

			$sql = "INSERT INTO temp VALUES (NULL, '$codigoLista', '$codigoSerial', '$chaveProduto')";
			$qry = $strcon->query($sql) or die($strcon->error);
			$chaveListaTemp = $strcon->insert_id;

		}

	}

	header("Location: /lista-gerada.php?i=".$codigoLista."&e=".$chaveProduto); exit();

}

if(isset($_GET['estornar'])){

	$chaveSerial = $_GET['cod'];

	$sql = "SELECT l.para, l.produto, (SELECT s.codigo FROM seriais s WHERE s.chave = '$chaveSerial') as codigo, (SELECT s.chave_lote_distribuicao FROM seriais s WHERE s.chave = '$chaveSerial') as chave_lote_distribuicao FROM log l WHERE l.chave_serial = '$chaveSerial' AND l.origem = 'SERIAL-DISTRIBUIDO'";
	$qry = $strcon->query($sql) or die($strcon->error);
	$con = mysqli_fetch_assoc($qry);

	$nomeRevenda = $con['para'];
	$nomeProduto = $con['produto'];
	$codigoSerial = $con['codigo'];
	$chaveDistribuicao = $con['chave_lote_distribuicao'];

	$sql = "INSERT INTO log VALUES (NULL, '$chaveSerial', 'SMART TECH', '$nomeRevenda', '$nomeProduto', 'ESTORNO', '$hoje', '$agora')";
	$qry = $strcon->query($sql) or die($strcon->error);

	$sql = "UPDATE seriais SET status = '0', saida = NULL WHERE chave = '$chaveSerial'";
	$qry = $strcon->query($sql) or die($strcon->error);

	$sql = "UPDATE lote_distribuicao SET seriais_consumidos = seriais_consumidos - 1 WHERE chave = '$chaveDistribuicao'";
	$qry = $strcon->query($sql) or die($strcon->error);

	header("Location: /consultar.php?success&estornar"); exit();

}

if(isset($_GET['excluirImportacao'])){

	$loteID = $_GET['i'];

	$sql = "SELECT chave FROM seriais WHERE chave_lote_importacao = '$loteID'";
	$qry = $strcon->query($sql) or die($strcon->error);

	while($dados = $qry->fetch_assoc()){

		$chaveDesteSerial = $dados['chave'];

		$sqlDelete = "DELETE FROM log WHERE chave_serial = '$chaveDesteSerial'";
		$qryDelete = $strcon->query($sqlDelete) or die($strcon->error);

	}

	$sqlDel = "DELETE FROM lote_importacao WHERE chave = '$loteID'";
	$qryDel = $strcon->query($sqlDel) or die($strcon->error);

	$sqlDel = "DELETE FROM seriais WHERE chave_lote_importacao = '$loteID'";
	$qryDel = $strcon->query($sqlDel) or die($strcon->error);

	header("Location: /historico-importados.php?success&exImp"); exit();

}

if(isset($_GET['excluirDistribuicao'])){

	$loteID = $_GET['i'];

	$sql = "SELECT chave, chave_produto FROM seriais WHERE chave_lote_distribuicao = '$loteID'";
	$qry = $strcon->query($sql) or die($strcon->error);

	$sqlRev = "SELECT nome FROM revenda WHERE chave = '$chaveRevenda'";
	$qryRev = $strcon->query($sqlRev) or die($strcon->error);
	$conRev = mysqli_fetch_assoc($qryRev) or die($strcon->error);
	$nomeRevenda = $conRev['nome'];

	while($dados = $qry->fetch_assoc()){

		$chaveDesteSerial = $dados['chave'];
		$chaveProduto = $dados['chave_produto'];

		if(!$nomeProduto){
			$sqlProd = "SELECT p.nome, (SELECT c.nome FROM categorias c WHERE c.chave = p.chave_categoria) as categoria FROM produtos p WHERE p.chave = '$chaveProduto'";
			$qryProd = $strcon->query($sqlProd) or die($strcon->error);
			$conProd = mysqli_fetch_assoc($qryProd);
			$nomeProduto = $conProd['nome'].' '.$conProd['categoria'];
		}

		$sqlLog = "INSERT INTO log VALUES (NULL, '$chaveDesteSerial', '$nomeRevenda', 'SMART TECH', '$nomeProduto', 'ESTORNO-DISTRIBUIÇÃO', '$hoje', '$agora')";
		$qryLog = $strcon->query($sqlLog) or die($strcon->error);

	}

	$sqlDel = "DELETE FROM lote_distribuicao where chave = '$loteID'";
	$qryDel = $strcon->query($sqlDel) or die($strcon->error);

	$sqlUp = "UPDATE seriais SET status = '0', saida = NULL, chave_estoque = 1, chave_lote_distribuicao = NULL WHERE chave_lote_distribuicao = $loteID";
	$qryUp = $strcon->query($sqlUp) or die($strcon->error);

	header("Location: /historico-distribuicao.php?seccess&exDist"); exit();

}

if(isset($_GET['desalocar'])){

	$revendedor = $_POST['revendedor'];
	$produto = $_POST['produto'];
	$quantidade = $_POST['quantidade'];

	for($i = 0; $i < $quantidade; $i++){

		$sql = "SELECT chave FROM seriais WHERE chave_produto = '$produto' AND chave_estoque = (SELECT chave FROM estoque WHERE chave_revenda = '$revendedor') AND status = '0' LIMIT 1";
		$qry = $strcon->query($sql) or die($strcon->error);
		$dadoSS = mysqli_fetch_assoc($qry);

		$chaveDesteSerial = $dadoSS['chave'];

		$sql = "UPDATE lote_distribuicao SET qtd_seriais = qtd_seriais - 1 WHERE chave = (SELECT chave_lote_distribuicao FROM seriais WHERE chave = '$chaveDesteSerial')";
		$qry = $strcon->query($sql) or die($strcon->error);

		$sql = "UPDATE seriais SET chave_estoque = '1', chave_lote_distribuicao = NULL WHERE chave = '$chaveDesteSerial'";
		$qry = $strcon->query($sql) or die($strcon->error);

	}

	header("Location: /estoque.php?success&desalocar&q=".$quantidade); exit();

}