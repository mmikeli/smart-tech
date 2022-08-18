<?php

include "../config.php";

$sql = "TRUNCATE temp;";
$qry = $strcon->query($sql);

exit('tabela :temp: limpa com sucesso - ' . date("d-m-Y H:i:s"));
