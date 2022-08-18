<?php
    
  if(session_status() != PHP_SESSION_ACTIVE) {
        ini_set('session.use_strict_mode', 0);
        session_start();
    }

  if(isset($_GET['usuario'])){
    header("Location: usuarios.php"); exit();
  }

  if(isset($_GET['revenda'])){
    $pathPage = '
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Cad. Revenda</li>
      </ol>
      <h6 class="font-weight-bolder mb-0">Nova Revenda</h6>
    ';
  }else if(isset($_GET['usuario'])){
    $pathPage = '
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Usuários</li>
      </ol>
      <h6 class="font-weight-bolder mb-0">Novo Usuário</h6>
    ';
  }else if(isset($_GET['produto'])){
    $pathPage = '
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Estoque</li>
      </ol>
      <h6 class="font-weight-bolder mb-0">Novo Produto</h6>
    ';
  }else if(isset($_GET['categorias'])){
    $pathPage = '
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Estoque</li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Produtos</li>
      </ol>
      <h6 class="font-weight-bolder mb-0">Nova Categoria</h6>
    ';
  }else{
    $pathPage = '
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
        <li class="breadcrumb-item text-sm text-dark active" aria-current="page">404 Not Found</li>
      </ol>
      <h6 class="font-weight-bolder mb-0">Página de Cadastro não Encontrada</h6>
    ';
  }

  include("sys/check_permission.php");
  include("pre/sets.php");
  include("sys/config.php");

  $chaveRevenda = $_SESSION['chaveRevenda'];
  $nivelRevenda = $_SESSION['nivelRevenda'];

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
    
    <?php if(isset($_GET['revenda'])){ ?>

    <div class="container-fluid">
      <a class="btn bg-gradient-secondary" href="cad-revenda.php"><i class="fas fa-arrow-circle-left me-2"></i>Voltar</a>
    </div>

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Revendedor</h6>
            </div>
            <div class="card-body">
              <form class="row" role="form" method="POST" action="sys/create.php?revenda">

                <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                  <label>Revendedor</label>
                  <input type="text" name="nome" class="form-control">
                </div>

                <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                  <label>e-Mail</label>
                  <input type="text" name="email" class="form-control">
                </div>

                <div class="col-lg-3 col-md-3 col-sm-11 col-11">
                  <label>Senha</label>
                  <input id="inputPass" type="password" name="senha" class="form-control">
                </div>

                <div class="col-lg-1 col-md-1 col-sm-1 col-1 row align-items-center justify-content-center">
                  <i style="padding-top: 20px; cursor: pointer;" id="eyeOpen" class="fas fa-eye"></i>
                  <i style="padding-top: 20px; cursor: pointer;" id="eyeClose" class="fas fa-eye-slash"></i>
                </div>

                <div class="text-center">
                  <button type="submit" class="btn bg-gradient-primary w-100 mt-4 mb-0">Salvar</button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>

  	<?php }else if(isset($_GET['usuario'])){ ?>

    <div class="container-fluid">
      <a class="btn bg-gradient-secondary" href="usuarios.php"><i class="fas fa-arrow-circle-left me-2"></i>Voltar</a>
    </div>

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Usuário</h6>
            </div>
            <div class="card-body">
              <form class="row" role="form" method="POST" action="sys/create.php?usuario">

                <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                  <label>Nome</label>
                  <input type="text" name="nome" class="form-control">
                </div>

                <div class="col-lg-4 col-md-4 col-sm-12 col-12">
                  <label>e-Mail</label>
                  <input type="text" name="email" class="form-control">
                </div>

                <div class="col-lg-3 col-md-3 col-sm-11 col-11">
                  <label>Senha</label>
                  <input id="inputPass" type="password" name="senha" class="form-control">
                </div>

                <div class="col-lg-1 col-md-1 col-sm-1 col-1 row align-items-center justify-content-center">
                  <i style="padding-top: 20px; cursor: pointer;" id="eyeOpen" class="fas fa-eye"></i>
                  <i style="padding-top: 20px; cursor: pointer;" id="eyeClose" class="fas fa-eye-slash"></i>
                </div>

                <div class="text-center">
                  <button type="submit" class="btn bg-gradient-primary w-100 mt-4 mb-0">Salvar</button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>

    <?php }else if(isset($_GET['produto'])){

      $sql = "SELECT chave, nome FROM categorias";
      $qry = $strcon->query($sql) or die($strcon->error);

    ?>

    <div class="container-fluid">
      <a class="btn bg-gradient-secondary" href="estoque.php"><i class="fas fa-arrow-circle-left me-2"></i>Voltar</a>
      <a class="btn bg-gradient-info" href="cad.php?categorias">Categorias</a>
    </div>

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Produto</h6>
            </div>
            <div class="card-body">
              <form class="row" role="form" method="POST" action="sys/create.php?produto">

                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                  <label>Nome Produto</label>
                  <input type="text" name="nome" class="form-control">
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                  <label>Categoria</label>
                  <select name="categoria" class="form-control" required>
                    <option selected disabled>:: SELECIONE ::</option>
                    <?php while($dados = $qry->fetch_assoc()){ ?>
                    <option value="<?php echo $dados['chave']; ?>"><?php echo $dados['nome']; ?></option>
                    <?php } ?>
                  </select>
                </div>

                <div class="text-center">
                  <button type="submit" class="btn bg-gradient-primary w-100 mt-4 mb-0">Salvar</button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>

    <?php }else if(isset($_GET['categorias'])){ ?>

    <div class="container-fluid">
      <a class="btn bg-gradient-secondary" href="cad.php?produto"><i class="fas fa-arrow-circle-left me-2"></i>Voltar</a>
    </div>

    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6>Categorias</h6>
            </div>
            <div class="card-body">
              <form class="row" role="form" method="POST" action="sys/create.php?categorias">

                <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                  <label>Nome da Categoria</label>
                  <input type="text" name="nome" class="form-control">
                </div>

                <div class="text-center">
                  <button type="submit" class="btn bg-gradient-primary w-100 mt-4 mb-0">Salvar</button>
                </div>

              </form>
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
              <p>Erro 404</p>
            </div>
            <div class="card-body">
              <h6>Página não Encontrada</h6>
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

    $(document).ready(function(){

      $("#eyeOpen").show();
      $("#eyeClose").hide();

      $("#eyeOpen").click(function(){

        $("#eyeOpen").hide();
        $("#eyeClose").show();

        $("#inputPass").attr('type', 'text');

      });

      $("#eyeClose").click(function(){

        $("#eyeOpen").show();
        $("#eyeClose").hide();

        $("#inputPass").attr('type', 'password');

      });

    });

  </script>

</body>

</html>