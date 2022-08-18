<?php
date_default_timezone_set('America/Sao_Paulo');

if (session_status() != PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', 0);
    session_start();
}

include "check_permission.php";
include "sets.php";
include "config.php";

$chaveRevenda = $_SESSION['chaveRevenda'];
$nivelRevenda = $_SESSION['nivelRevenda'];

if (isset($_GET['desalocar'])) {

    $revID  = $_GET['r'];
    $prodID = $_GET['p'];

    $sql = "SELECT chave FROM estoque WHERE chave_revenda = '$revID'";
    $qry = $strcon->query($sql);
    $con = mysqli_fetch_assoc($qry);

    $estoqueID = $con['chave'];

    $sqlSS   = "SELECT chave FROM seriais WHERE chave_produto = '$prodID' AND chave_estoque = '$estoqueID' AND status = '0'";
    $qrySS   = $strcon->query($sqlSS);
    $dadosSS = mysqli_fetch_assoc($qrySS);
    $rows    = mysqli_num_rows($qrySS);

    echo $rows;

}

if (isset($_GET['dump_temp'])) {

    $temp_row_id = $_GET['i'];

    $sql = "DELETE FROM temp WHERE codigo_lista = $temp_row_id";
    if ($qry = $strcon->query($sql)) {
        echo 'success';
    } else {
        echo 'error';
    }
}
