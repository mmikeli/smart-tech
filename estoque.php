<?php
    
  if(session_status() != PHP_SESSION_ACTIVE) {
        ini_set('session.use_strict_mode', 0);
        session_start();
    }

  $pathPage = '
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
      <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
      <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Estoque</li>
    </ol>
    <h6 class="font-weight-bolder mb-0">Estoque</h6>
  ';

  include("sys/check_permission.php");
  include("pre/sets.php");
  include("sys/config.php");

  $chaveRevenda = $_SESSION['chaveRevenda'];
  $nivelRevenda = $_SESSION['nivelRevenda'];

  $sql = "SELECT produtos.chave, produtos.nome, (SELECT categorias.nome FROM categorias WHERE categorias.chave = produtos.chave_categoria) as categoria FROM produtos ORDER BY produtos.nome ASC";
  $qry = $strcon->query($sql) or die($strcon->error);

  if(isset($_GET['exLista'])){

    $lista = $_GET['exLista'];

    $sqlEx = "DELETE FROM temp WHERE codigo_lista = '$lista'";
    $qryEx = $strcon->query($sqlEx) or die($strcon->error);

    header("Location: estoque.php"); exit();

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

    <?php if($_SESSION['nivelRevenda'] == '777'){ ?>
    <div class="container-fluid">
      <a class="btn bg-gradient-primary" href="importar-csv.php">Importar CSV</a>
      <a class="btn bg-gradient-info" href="distribuir-carga.php">Distribuir Carga</a>
      <a class="btn bg-gradient-secondary" href="desalocar-seriais.php">Desalocar Seriais</a>
      <a class="btn bg-gradient-warning" href="gerar-codigo.php">Gerar Código</a>
      <a class="btn bg-gradient-danger" href="consultar.php">Consultar Código</a>
      <a class="btn bg-gradient-success" href="cad.php?produto">Criar Novo Produto</a>
    </div>
    <?php }else if($_SESSION['nivelRevenda'] == '555'){ ?>
    <div class="container-fluid">
      <a class="btn bg-gradient-primary" href="gerar-codigo.php">Gerar Código</a>
      <a class="btn bg-gradient-warning" href="consultar.php">Consultar Código</a>
    </div>
    <?php } ?>

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Tabela de Produtos</h6>
            </div>
            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Produto</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Seriais Disponíveis</th>
                    </tr>
                  </thead>
                  <tbody >
                    <?php while($dados = $qry->fetch_assoc()){ 

                      $chaveProduto = $dados['chave'];

                      $sql2 = "SELECT chave, status, chave_estoque FROM seriais WHERE chave_produto = $chaveProduto AND chave_estoque = (SELECT chave FROM estoque WHERE chave_revenda = '$chaveRevenda') AND status = '0'";
                      $qry2 = $strcon->query($sql2) or die($strcon->error);
                      $qtdSeriais = mysqli_num_rows($qry2);
                      $dados_seriais = mysqli_fetch_assoc($qry2);

                      if($chaveRevenda != '1' && $qtdSeriais <= 0){}else{

                    ?>
                    <tr chave_revenda='<?=$chaveRevenda?>' chave_estoque='<?=$dados_seriais["chave_estoque"]?>' status='<?=$dados_seriais["status"]?>' chave_produto='<?=$chaveProduto?>' qtd_serial='<?=$qtdSeriais?>'>
                      <td>
                        <h6 <?php if($qtdSeriais == '0'){ echo 'style="color: red;"'; } ?> class="ps-3 mb-0 text-sm"><?php echo $dados['nome'] . ' ' . $dados['categoria']; ?></h6>
                      </td>
                      <td>
                        <h6 <?php if($qtdSeriais == '0'){ echo 'style="color: red;"'; } ?> class="ps-3 mb-0 text-sm"><?php echo $qtdSeriais; ?></h6>
                      </td>
                    </tr>
                    <?php } } ?>
                  </tbody>
                </table>
              </div>
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

  <?php if(isset($_GET['success']) && isset($_GET['qtd'])){ ?>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Parabéns</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Foram distribuidos <?php echo $_GET['qtd']; ?> seriais
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="window.location.href = 'estoque.php'">Fechar</button>
          </div>
        </div>
      </div>
    </div>

  <?php } ?>
  <?php if(isset($_GET['success']) && isset($_GET['imp'])){ ?>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Parabéns</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Foram importados <?php echo $_GET['q']; ?> seriais
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="window.location.href = 'estoque.php'">Fechar</button>
          </div>
        </div>
      </div>
    </div>

  <?php } ?>
  <?php if(isset($_GET['fail']) && isset($_GET['imp'])){ ?>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Ops...</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Algo deu errado. Lista não importada.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="window.location.href = 'estoque.php'">Fechar</button>
          </div>
        </div>
      </div>
    </div>

  <?php } ?>
  <?php if(isset($_GET['success']) && isset($_GET['prod'])){ ?>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Parabéns</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Produto cadastrado com sucesso.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="window.location.href = 'estoque.php'">Fechar</button>
          </div>
        </div>
      </div>
    </div>

  <?php } ?>
  <?php if(isset($_GET['fail']) && isset($_GET['prod'])){ ?>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Ops...</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Algo deu errado. Produto não cadastrado.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="window.location.href = 'estoque.php'">Fechar</button>
          </div>
        </div>
      </div>
    </div>

  <?php } ?>
  <?php if(isset($_GET['success']) && isset($_GET['cat'])){ ?>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Parabéns</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Categoria cadastrada com sucesso.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="window.location.href = 'estoque.php'">Fechar</button>
          </div>
        </div>
      </div>
    </div>

  <?php } ?>
  <?php if(isset($_GET['fail']) && isset($_GET['cat'])){ ?>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Ops...</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Algo deu errado. Categoria não cadastrada.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="window.location.href = 'estoque.php'">Fechar</button>
          </div>
        </div>
      </div>
    </div>

  <?php } ?>
  <?php if(isset($_GET['success']) && isset($_GET['distribuir'])){ ?>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Parabéns</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <?php echo $_GET['q']; ?> Seriais distribuidos com sucesso.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="window.location.href = 'estoque.php'">Fechar</button>
          </div>
        </div>
      </div>
    </div>

  <?php } ?>
  <?php if(isset($_GET['fail']) && isset($_GET['distribuir'])){ ?>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Ops...</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Algo deu errado. Seriais não distribuidos.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="window.location.href = 'estoque.php'">Fechar</button>
          </div>
        </div>
      </div>
    </div>

  <?php } ?>
  <?php if(isset($_GET['success']) && isset($_GET['desalocar'])){ ?>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Parabéns</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Foram desalocados <?php echo $_GET['q'] ?> seriais de volta para seu estoque.
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="window.location.href = 'estoque.php'">Fechar</button>
          </div>
        </div>
      </div>
    </div>

  <?php } ?>
  <!-- Button trigger modal -->
  <button id="showModal" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" style="display: none;">
    show modal
  </button>

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

</body>

</html>