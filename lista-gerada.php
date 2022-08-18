<?php

if (session_status() != PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', 0);
    session_start();
}

$pathPage = '
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
      <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
      <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Estoque</li>
      <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Gerar Código</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Lista de Códigos Gerados</h6>
  ';

include "sys/check_permission.php";
include "pre/sets.php";
include "sys/config.php";

$chaveRevenda = $_SESSION['chaveRevenda'];
$nivelRevenda = $_SESSION['nivelRevenda'];

$chaveLista = $_GET['i'];
$sql        = "SELECT codigo_serial FROM temp WHERE codigo_lista = '$chaveLista'";
$qry        = $strcon->query($sql);
$qry2       = $strcon->query($sql);
$row        = mysqli_num_rows($qry);

$chaveProduto = $_GET['e'];
$sqlP         = "SELECT p.nome, (SELECT c.nome FROM categorias c WHERE c.chave = p.chave_categoria) as categoria FROM produtos p WHERE chave = '$chaveProduto'";
$qryP         = $strcon->query($sqlP);
$dadosP       = mysqli_fetch_assoc($qryP);

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" type="image/png" href="assets/img/favicon.png">
  <title>
    Portal SmartTech
  </title>
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/0fa6f1791c.js" crossorigin="anonymous"></script>
  <link href="assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- CSS Files -->
  <link id="pagestyle" href="assets/css/soft-ui-dashboard.css?v=1.0.3" rel="stylesheet" />
</head>

<body class="g-sidenav-show  bg-gray-100">

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">

    <div class="container-fluid text-center mt-5">
      <a class="btn bg-gradient-danger" href="estoque.php?exLista=<?php echo $chaveLista; ?>"><i class="fas fa-times me-2"></i>Fechar</a>
    </div>

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6 id="titulo"><?php echo $row . ' ' . $dadosP['nome'] . ' ' . $dadosP['categoria']; ?></h6>
            </div>
            <div class="card-body px-4 pt-2 pb-2">

            	<ul class="list-unstyled">
            		<?php while ($dados = $qry->fetch_assoc()) {?>
            		<li><?php echo $dados['codigo_serial']; ?></li>
            		<?php }?>
            	</ul>

            	<a class="btn bg-gradient-primary w-25 mt-5" id="copy" currentList="<?php echo $chaveLista; ?>">Copiar</a>
            	<a class="btn bg-gradient-primary w-25 mt-5" id="txtGenerate">Baixar .TXT</a>

            	<textarea style="opacity: 0; text-align: left;" id="copyHere"><?php echo '*' . $row . ' ' . $dadosP['nome'] . ' ' . $dadosP['categoria'] . '*' . "\n" . "\n"; ?><?php while ($dados2 = $qry2->fetch_assoc()) {echo $dados2['codigo_serial'] . "\n";}?></textarea>
              <input type="text" id="idLista" value="<?=$chaveLista?>">
            </div>
          </div>
        </div>
      </div>

      <footer class="footer pt-3  ">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
              <div class="copyright text-center text-sm text-muted text-lg-start">
                Copyright © <script>
                  document.write(new Date().getFullYear())
                </script> Smart Tech,
                desenvolvido por
                <a href="#" class="font-weight-bold">Supernova</a>
              </div>
            </div>
          </div>
        </div>
      </footer>

    </div>
  </main>

  <!--   Core JS Files   -->
  <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="assets/js/soft-ui-dashboard.min.js?v=1.0.3"></script>

  <?php echo $scripts; ?>

  <script type="text/javascript">

  	$("#copy").click(function(){

	    $("#copyHere").select();
	    document.execCommand("copy");

	    var lista = $("#copy").attr('currentList');

	    alert("Texto copiado com sucesso!");

	});

</script>

<script>
  $(document).ready(function(){

    var temp_index = $("#idLista").val();

    $("#copyHere").select();
	  document.execCommand("copy");

    $.ajax({
      url: 'sys/catch.php?dump_temp&i='+temp_index,
      type: 'GET'
    });

  });
</script>

<script type="text/javascript">

  function download(content, filename){

      var a = document.createElement('a');
      var blob = new Blob([content], {'type':'plain/Text'});
      a.href = window.URL.createObjectURL(blob);
      a.download = filename;
      a.click();

  }

  $("#txtGenerate").click(function(){

    var txt = $("#copyHere").val();
    var titulo = $("#titulo").text();

    var data = new Date();
    var dia = String(data.getDate()).padStart(2, '0');
    var mes = String(data.getMonth() + 1).padStart(2, '0');
    var ano = data.getFullYear();
    dataAtual = dia + '-' + mes + '-' + ano;

    var file = titulo + ' ' + dataAtual + '.txt';

    download(txt, file);

  });

</script>

</body>

</html>