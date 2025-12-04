<?php
require_once "../php/bd.php";

// RECEBE OS FILTROS DA BUSCA DO BANNER
$localizacao = $_GET['localizacao'] ?? "";
$data_inicial = $_GET['data_inicial'] ?? "";
$data_final = $_GET['data_final'] ?? "";
$hospedes = $_GET['hospedes'] ?? "";

// QUERY BASE
$sql = "SELECT * FROM imoveis WHERE 1=1";

// FILTRA POR CIDADE (localização)
if (!empty($localizacao)) {
    $sql .= " AND cidade LIKE :localizacao";
}

// FILTRA POR CAPACIDADE (hóspedes)
if (!empty($hospedes)) {
    $sql .= " AND capacidade >= :hospedes";
}

$stmt = $conn->prepare($sql);

// BIND DOS FILTROS
if (!empty($localizacao)) {
    $stmt->bindValue(":localizacao", "%$localizacao%");
}
if (!empty($hospedes)) {
    $stmt->bindValue(":hospedes", $hospedes);
}

$stmt->execute();
$imoveis = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Imoveis - ABhostel</title>

    <!-- Fontes -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Nunito&display=swap" rel="stylesheet" />

    <!-- CSS -->
    <link rel="stylesheet" href="../assets/css/listar_imoveis.css">

    <!-- Ícones -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- HEADER -->
  <header>
    <div class="logo">
      <a href="/index.php"><img src="/Imagens/logo.png" alt="Logo ABhostel"></a>
    </div>

    <!-- MENU DESKTOP -->
    <nav class="menu-desktop">
      <ul class="menu-links">
        <li><a href="../index.php" class="active">Início</a></li>
        <li><a href="../php/listar_imoveis.php">Imóveis</a></li>
        <li><a href="">Anuncie seu imóvel</a></li>
      </ul>
      <a href="../php/login.php" class="btn-login">Entrar / Cadastre-se</a> 
    </nav>

     <!-- ÍCONE MENU MOBILE -->
    <button class="btn open" aria-label="Abrir menu"><i class="fas fa-bars"></i></button>
  </header>

  <!-- OVERLAY E MENU MOBILE -->
  <div class="menu-overlay" aria-hidden="true"></div>
  <div class="mobile-menu" aria-hidden="true">
    <div class="mobile-links">
      <a href="/index.php">Início</a>
      <hr class="divider">
      <a href="/php/listar_imoveis.php">Imóveis</a>
      <a href="">Anuncie seu imóvel</a>
      <hr class="divider">
      <a href="/php/sobre.php">Sobre</a>
      <a href="/php/contato.php">Contato</a>
      <hr class="divider">
      <a href="/php/login.php" class="login-mobile">Entrar / Cadastre-se</a>
    </div>

    <div class="mobile-social">
      <a href="#"><i class="fab fa-instagram"></i></a>
      <a href="#"><i class="fab fa-facebook"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
    </div>
  </div>

  <!-- Conteúdo da página -->
  <div class="container">
      
      <div class="lista-imoveis">

          <!-- ============================
                COLUNA ESQUERDA (IMÓVEIS)
          ============================== -->
          <div class="coluna-esquerda">
              <p><?php echo count($imoveis); ?> opções encontradas</p>
              <h1>Lista de Imóveis</h1>

              <?php if (count($imoveis) == 0): ?>
                  <p style="margin: 20px 0;">Nenhum imóvel encontrado 😥</p>
              <?php endif; ?>

              <?php foreach ($imoveis as $imovel): ?>
              <div class="imovel">
                  
                  <div class="imovel-img">
                      <img src="../uploads/<?php echo $imovel['imagem']; ?>" alt="">
                  </div>

                  <div class="imovel-info">

                      <p><?php echo $imovel['descricao']; ?></p>

                      <h3><?php echo $imovel['titulo']; ?></h3>

                      <p>
                        <?php echo $imovel['quartos']; ?> quartos / 
                        <?php echo $imovel['banheiros']; ?> banheiros /
                        wifi
                      </p>

                      <!-- Estrelas fixas só para estética -->
                      <i class="fa-solid fa-star"></i>
                      <i class="fa-solid fa-star"></i>
                      <i class="fa-solid fa-star"></i>
                      <i class="fa-solid fa-star-half-stroke"></i>
                      <i class="fa-regular fa-star"></i>

                      <div class="imovel-valor">
                          <p><?php echo $imovel['capacidade']; ?> Hóspedes</p>
                          <h4>R$ <?php echo $imovel['preco']; ?> <span>/ mês</span></h4>
                      </div>
                  </div>
              </div>
              <?php endforeach; ?>
          </div>

          <!-- COLUNA DIREITA -->
          <div class="coluna-direita">
              <div class="barra-lateral">
                  <h2>Filtro de Busca</h2>

                  <!-- (FILTROS DECORATIVOS - NÃO FUNCIONAIS AINDA) -->

                  <h3>Tipo de Imóvel</h3>
                  <form action="" method="post">

                    <div class="filtro"><input type="checkbox"> <p>Casa</p></div>
                    <div class="filtro"><input type="checkbox"> <p>Apartamento</p></div>
                    <div class="filtro"><input type="checkbox"> <p>Kitnet</p></div>
                    <div class="filtro"><input type="checkbox"> <p>Suíte</p></div>
                    <div class="filtro"><input type="checkbox"> <p>Hotel</p></div>
                    <div class="filtro"><input type="checkbox"> <p>Pousada</p></div>
                    <div class="filtro"><input type="checkbox"> <p>Chalé</p></div>

                    <h3>Facilidades</h3>
                    <!-- filtros só estéticos -->

                    <button class="cadastro-btn">Buscar</button>
                  </form>
              </div>
          </div>
      </div>
  </div>

  <script src="../assets/js/menu.js"></script>
</body>
</html>