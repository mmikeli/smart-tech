<?php
date_default_timezone_set('America/Sao_Paulo');

if (session_status() != PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', 0);
    session_start();
}

include "sys/check_permission.php";
include "config.php";

$hoje  = date('Y-m-d');
$agora = date('H:i:s');
$chain = true;

if (isset($_GET['revenda'])) {

    if (empty($_POST['email']) || empty($_POST['senha'])) {
        $chain = false;
    } else {

        $email = $_POST['email'];
        $nome  = $_POST['nome'];
        $senha = $_POST['senha'];

    }

    if ($chain === true) {
        $sql       = "INSERT INTO revenda VALUES (NULL, '$nome', '$hoje', '555')";
        $qry       = $strcon->query($sql);
        $revendaId = $strcon->insert_id;

        if (!$qry) {$chain = false;}
    }

    if ($chain === true) {
        $sql = "INSERT INTO usuarios VALUES (NULL, '$email', '$senha', '$nome', '$revendaId', '$hoje', '0')";
        $qry = $strcon->query($sql);

        if (!$qry) {
            $chain = false;

            $sql = "DELETE FROM revenda WHERE chave = '$revendaId'";
            $qry = $strcon->query($sql);
        } else {

            $sql = "INSERT INTO estoque VALUES (NULL, '$revendaId', '$hoje')";
            $qry = $strcon->query($sql);

            if (!$qry) {
                $chain = false;

                $sql = "DELETE FROM revenda WHERE chave = '$revendaId'";
                $qry = $strcon->query($sql);

                $sql = "DELETE FROM estoque WHERE chave_revenda = '$revendaId'";
                $qry = $strcon->query($sql);
            }

        }
    }

    if ($chain === true) {

        header("Location: /cad-revenda.php?success&cad");exit();

    } else {

        header("Location: /cad-revenda.php?fail&cad");exit();

    }

}

if (isset($_GET['usuario'])) {

    if (empty($_POST['email']) || empty($_POST['senha'])) {
        $chain = false;
    } else {

        $email = $_POST['email'];
        $senha = $_POST['senha'];
        $nome  = $_POST['nome'];

    }

    if ($chain === true) {
        $sql = "INSERT INTO usuarios VALUES (NULL, '$email', '$senha', '$nome', '1', '$hoje', '0')";
        $qry = $strcon->query($sql);

        if (!$qry) {$chain = false;}
    }

    if ($chain === true) {

        header("Location: /usuarios.php?success&cad");exit();

    } else {

        header("Location: /usuarios.php?fail&cad");exit();

    }

}

if (isset($_GET['produto'])) {

    if (empty($_POST['nome']) || empty($_POST['categoria'])) {
        $chain = false;
    } else {

        $nome      = $_POST['nome'];
        $categoria = $_POST['categoria'];

    }

    if ($chain === true) {
        $sql = "INSERT INTO produtos VALUES (NULL, '$nome', '$categoria')";
        $qry = $strcon->query($sql);

        if (!$qry) {$chain = false;}
    }

    if ($chain === true) {

        header("Location: /estoque.php?success&prod");exit();

    } else {

        header("Location: /estoque.php?fail&prod");exit();

    }

}

if (isset($_GET['categorias'])) {

    if (empty($_POST['nome'])) {
        $chain = false;
    } else {

        $nome = $_POST['nome'];

    }

    if ($chain === true) {
        $sql = "INSERT INTO categorias VALUES (NULL, '$nome')";
        $qry = $strcon->query($sql);

        if (!$qry) {$chain = false;}
    }

    if ($chain === true) {

        header("Location: /estoque.php?success&cat");exit();

    } else {

        header("Location: /estoque.php?fail&cat");exit();

    }

}

if (isset($_GET['imports'])) {

    $chaveImportacao = $_GET['i'];
    $sqlL            = "SELECT serial FROM await_import WHERE chave_importacao = '$chaveImportacao'";
    $qryL            = $strcon->query($sqlL);

    $chaveProduto = $_GET['e'];
    $sqlP         = "SELECT p.nome, (SELECT c.nome FROM categorias c WHERE c.chave = p.chave_categoria) as categoria FROM produtos p WHERE chave = '$chaveProduto'";
    $qryP         = $strcon->query($sqlP);
    $dadosProduto = mysqli_fetch_assoc($qryP);
    $nomeProduto  = $dadosProduto['nome'] . ' ' . $dadosProduto['categoria'];

    $qtdSeriais   = $_GET['q'];
    $loteImportId = $_GET['o'];

    if ($chain === true) {

        while ($dadosSeriais = $qryL->fetch_assoc()) {

            $codigo = $dadosSeriais['serial'];

            $sqlLote          = "INSERT INTO seriais VALUES (NULL, '$chaveProduto', '$loteImportId', NULL, '1', '$codigo', NULL, '0')";
            $qryLote          = $strcon->query($sqlLote);
            $chaveDesteSerial = $strcon->insert_id;

            $sqlLog = "INSERT INTO log VALUES (NULL, '$chaveDesteSerial', 'SMART TECH', 'SMART TECH', '$nomeProduto', 'LISTA-IMPORTACAO', '$hoje', '$agora')";
            $qryLog = $strcon->query($sqlLog);

        }

    }

    if ($chain === false) {
        header("Location: /estoque.php?fail&imp");
    } else {
        header("Location: /estoque.php?success&imp&q=" . $qtdSeriais);
    }

}

if (isset($_GET['teste_imports'])) {

    if (empty($_FILES['csv'])) {
        $chain = false;
    } else {

        $idProduto = $_POST['produto'];

        $sqlProd     = "SELECT p.nome, (SELECT c.nome FROM categorias c WHERE c.chave = p.chave_categoria) as categoria FROM produtos p WHERE chave = '$idProduto'";
        $qryProd     = $strcon->query($sqlProd);
        $conProd     = mysqli_fetch_assoc($qryProd);
        $nomeProduto = $conProd['nome'] . ' ' . $conProd['categoria'];

    }

    if ($chain === true) {

        $defaultFilePath = '/home/u296526003/domains/portalsmart.com.br/public_html/app/assets/files/';
        $upload_file     = $defaultFilePath . basename($_FILES['csv']['name']);

        move_uploaded_file($_FILES['csv']['tmp_name'], $upload_file);

        $arquivo_novo = $hoje . '_' . $agora . ".csv";

        $arquivo_antigo = $defaultFilePath . $_FILES['csv']['name'];

        rename($arquivo_antigo, $defaultFilePath . $arquivo_novo);

        $content = file_get_contents($defaultFilePath . $arquivo_novo);

        $content = trim($content);
        $content = str_replace(['"', "'"], ['', ''], $content);

        $arrayContent = explode("\n", $content);

        $numberSerials = count($arrayContent);

        $sql  = "SELECT chave_importacao FROM await_import ORDER BY chave_importacao DESC LIMIT 1";
        $qry  = $strcon->query($sql);
        $conn = mysqli_fetch_assoc($qry);

        if (!$conn['chave_importacao']) {
            $num = 0;
        } else {
            $num = $conn['chave_importacao'];
        }

        $chave_importacao = $num + 1;

        $sql          = "INSERT INTO lote_importacao VALUES (NULL, '$idProduto', NULL, '0', '$hoje', '$arquivo_novo')";
        $qry          = $strcon->query($sql);
        $loteImportId = $strcon->insert_id;

        $countAgain = 0;
        for ($i = 0; $i < $numberSerials; $i++) {

            $codigo = $arrayContent[$i];

            $sqlCheck = "SELECT chave FROM seriais WHERE codigo = '$codigo'";
            $qryCheck = $strcon->query($sqlCheck);
            $row      = mysqli_num_rows($qryCheck);

            if (!($row > 0)) {

                $sqlLote          = "INSERT INTO await_import VALUES (NULL, '$chave_importacao', '$idProduto', '$codigo')";
                $qryLote          = $strcon->query($sqlLote);
                $chaveDesteSerial = $strcon->insert_id;

                $countAgain++;

            }

        }

        $sqlUp = "UPDATE lote_importacao SET qtd_seriais = '$countAgain' WHERE chave = '$loteImportId'";
        $qryUp = $strcon->query($sqlUp);

        $sql   = "DELETE FROM await_import WHERE chave_importacao = '$chaveImportacao'";
        $qryUp = $strcon->query($sqlUp);

    }

    if ($chain === false) {
        header("Location: /await-list-import.php?fail");
    } else {
        header("Location: /await-list-import.php?success&i=" . $chave_importacao . "&e=" . $idProduto . "&o=" . $loteImportId);
    }

}
