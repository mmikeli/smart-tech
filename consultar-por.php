<?php
    
  if(session_status() != PHP_SESSION_ACTIVE) {
        ini_set('session.use_strict_mode', 0);
        session_start();
    }

  $pathPage = '
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
      <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
      <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Estoque</li>
      <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Consultar Código</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Resultados da Consulta</h6>
  ';

  include("sys/check_permission.php");
  include("pre/sets.php");
  include("sys/config.php");

  $chaveRevenda = $_SESSION['chaveRevenda'];
  $chaveEstoque = $_SESSION['chaveEstoque'];
  $nivelRevenda = $_SESSION['nivelRevenda'];

  if(empty($_GET['field'])){

    header("Location: consultar.php?consulta&empty");

  }

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

  <?php echo $sidebar; ?>

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">

    <?php echo $topNav; ?>
    
    <div class="container-fluid">
      <a class="btn bg-gradient-secondary" href="consultar.php"><i class="fas fa-arrow-circle-left me-2"></i>Voltar</a>
    </div>

    <?php if(isset($_GET['codigo'])){

      $codigo = $_GET['field'];

      if($nivelRevenda == '555'){

          $sqlChave = "SELECT chave, codigo FROM seriais WHERE codigo LIKE '$codigo%' AND chave_estoque = '$chaveEstoque' LIMIT 1";
          $qryChave = $strcon->query($sqlChave) or die($strcon->error);
          $conSerial = mysqli_fetch_assoc($qryChave);
          $chaveSerial = $conSerial['chave'];

      }else if($nivelRevenda == '777'){

          $sqlChave = "SELECT chave, codigo FROM seriais WHERE codigo LIKE '$codigo%' LIMIT 1";
          $qryChave = $strcon->query($sqlChave) or die($strcon->error);
          $conSerial = mysqli_fetch_assoc($qryChave);
          $chaveSerial = $conSerial['chave'];

      }

      
      $sqlLog = "SELECT chave_serial, de, para, produto, origem, data, hora FROM log WHERE chave_serial = '$chaveSerial' ORDER BY chave DESC";
      $qryLog = $strcon->query($sqlLog) or die($strcon->error);
      
    ?>

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Resutado da Busca por Código</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">

              <?php if($conSerial){ ?>
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">De</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Para</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Origem</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Produto</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Código</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Data | Hora</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">&nbsp;</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while($dadosLog = $qryLog->fetch_assoc()){

                      $continue = TRUE;
                      if($nivelRevenda !== '777'){
                        $continue = FALSE;
                      }
                      if($dadosLog['origem'] !== 'SERIAL-CONSUMIDO'){
                        $continue = FALSE;
                      }

                    ?>
                    <tr>
                      <td>
                        <h6 class="ps-3 mb-0 text-sm"><?php echo $dadosLog['de']; ?></h6>
                      </td>
                      <td>
                        <h6 class="ps-3 mb-0 text-sm"><?php echo $dadosLog['para']; ?></h6>
                      </td>
                      <td>
                        <h6 class="ps-3 mb-0 text-sm"><?php echo $dadosLog['origem']; ?></h6>
                      </td>
                      <td>
                        <h6 class="ps-3 mb-0 text-sm"><?php echo $dadosLog['produto']; ?></h6>
                      </td>
                      <td>
                        <h6 class="ps-3 mb-0 text-sm"><?php echo $conSerial['codigo']; ?></h6>
                      </td>
                      <td>
                        <h6 class="ps-3 mb-0 text-sm"><?php echo date_format(date_create($dadosLog['data']), 'd/m/Y'). ' | ' .$dadosLog['hora']; ?></h6>
                      </td>
                      <td>
                        <h6 class="ps-3 mb-0 text-sm">
                          <?php if($continue === TRUE){ ?>
                          <a style="cursor: pointer;" title="estornar" onclick="estorno(this.id)" id="<?php echo $dadosLog['chave_serial']; ?>"><i class="fas fa-recycle"></i></a>
                          <?php }else{} ?>
                        </h6>
                      </td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            <?php }else{ ?>
              <h3 class="m-5">Nenhum resultado encontrado em seus Históricos!</h3>
            <?php } ?>
            </div>
          </div>
        </div>
      </div>

    <?php }else if(isset($_GET['cliente'])){

      $cliente = '%'.$_GET['field'].'%';

      if($nivelRevenda == '555'){

          $sqlLog = "SELECT chave_serial, de, para, produto, origem, data, hora FROM log WHERE para LIKE '$cliente' AND de = (SELECT nome FROM revenda WHERE chave = '$chaveRevenda') ORDER BY chave DESC";
          $qryLog = $strcon->query($sqlLog) or die($strcon->error);

      }else if($nivelRevenda == '777'){

          $sqlLog = "SELECT chave_serial, de, para, produto, origem, data, hora FROM log WHERE para LIKE '$cliente' ORDER BY chave DESC";
          $qryLog = $strcon->query($sqlLog) or die($strcon->error);

      }
    ?>

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Resutado da Busca por Cliente</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">


              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">De</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Para</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Origem</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Produto</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Código</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Data | Hora</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">&nbsp;</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php while($dadosLog = $qryLog->fetch_assoc()){

                      $chaveDesteSerial = $dadosLog['chave_serial'];

                      $sqlCheck = "SELECT chave, codigo FROM seriais WHERE chave = '$chaveDesteSerial'";
                      $qryCheck = $strcon->query($sqlCheck) or die($strcon->error);
                      $row = mysqli_num_rows($qryCheck);
                      $dadosSerial = mysqli_fetch_assoc($qryCheck);

                      $continue = TRUE;
                      if($nivelRevenda !== '777'){
                        $continue = FALSE;
                      }
                      if($row == 0){
                        $continue = FALSE;
                      }
                      if($dadosLog['origem'] !== 'SERIAL-CONSUMIDO'){
                        $continue = FALSE;
                      }

                    ?>
                    <tr>
                      <td>
                        <h6 class="ps-3 mb-0 text-sm"><?php echo $dadosLog['de']; ?></h6>
                      </td>
                      <td>
                        <h6 class="ps-3 mb-0 text-sm"><?php echo $dadosLog['para']; ?></h6>
                      </td>
                      <td>
                        <h6 class="ps-3 mb-0 text-sm"><?php echo $dadosLog['origem']; ?></h6>
                      </td>
                      <td>
                        <h6 class="ps-3 mb-0 text-sm"><?php echo $dadosLog['produto']; ?></h6>
                      </td>
                      <td>
                        <h6 class="ps-3 mb-0 text-sm"><?php echo $dadosSerial['codigo']; ?></h6>
                      </td>
                      <td>
                        <h6 class="ps-3 mb-0 text-sm"><?php echo date_format(date_create($dadosLog['data']), 'd/m/Y'). ' | ' .$dadosLog['hora']; ?></h6>
                      </td>
                      <td>
                        <h6 class="ps-3 mb-0 text-sm">
                          <?php if($continue === TRUE){ ?>
                          <a style="cursor: pointer;" title="estornar" onclick="estorno(this.id)" id="<?php echo $dadosLog['chave_serial']; ?>"><i class="fas fa-recycle"></i></a>
                          <?php }else{} ?>
                        </h6>
                      </td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>
      </div>

    <?php }else{ ?>

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Algo deu errado.</h6>
            </div>
            <div class="card-body px-5 pt-5 pb-5">

            	<h3>Não entendemos sua busca.<br><a href="consultar-codigo.php">Clique aqui e tente novamente</a></h3>

            </div>
          </div>
        </div>
      </div>

    <?php } ?>

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
  <script src="assets/js/plugins/chartjs.min.js"></script>
  <script>
    var ctx = document.getElementById("chart-bars").getContext("2d");

    new Chart(ctx, {
      type: "bar",
      data: {
        labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [{
          label: "Sales",
          tension: 0.4,
          borderWidth: 0,
          borderRadius: 4,
          borderSkipped: false,
          backgroundColor: "#fff",
          data: [450, 200, 100, 220, 500, 100, 400, 230, 500],
          maxBarThickness: 6
        }, ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
            },
            ticks: {
              suggestedMin: 0,
              suggestedMax: 500,
              beginAtZero: true,
              padding: 15,
              font: {
                size: 14,
                family: "Open Sans",
                style: 'normal',
                lineHeight: 2
              },
              color: "#fff"
            },
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false
            },
            ticks: {
              display: false
            },
          },
        },
      },
    });


    var ctx2 = document.getElementById("chart-line").getContext("2d");

    var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);

    gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
    gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)'); //purple colors

    var gradientStroke2 = ctx2.createLinearGradient(0, 230, 0, 50);

    gradientStroke2.addColorStop(1, 'rgba(20,23,39,0.2)');
    gradientStroke2.addColorStop(0.2, 'rgba(72,72,176,0.0)');
    gradientStroke2.addColorStop(0, 'rgba(20,23,39,0)'); //purple colors

    new Chart(ctx2, {
      type: "line",
      data: {
        labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [{
            label: "Mobile apps",
            tension: 0.4,
            borderWidth: 0,
            pointRadius: 0,
            borderColor: "#cb0c9f",
            borderWidth: 3,
            backgroundColor: gradientStroke1,
            fill: true,
            data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
            maxBarThickness: 6

          },
          {
            label: "Websites",
            tension: 0.4,
            borderWidth: 0,
            pointRadius: 0,
            borderColor: "#3A416F",
            borderWidth: 3,
            backgroundColor: gradientStroke2,
            fill: true,
            data: [30, 90, 40, 140, 290, 290, 340, 230, 400],
            maxBarThickness: 6
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              padding: 10,
              color: '#b2b9bf',
              font: {
                size: 11,
                family: "Open Sans",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              color: '#b2b9bf',
              padding: 20,
              font: {
                size: 11,
                family: "Open Sans",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
        },
      },
    });
  </script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="assets/js/soft-ui-dashboard.min.js?v=1.0.3"></script>

  <?php echo $scripts; ?>

  <script type="text/javascript">

    function estorno(serial){

      if(confirm("Tem certeza que deseja estornar o serial para o revendedor?")){

        window.location.href = "sys/update.php?estornar&cod="+serial;

      }

    }

  </script>

</body>

</html>