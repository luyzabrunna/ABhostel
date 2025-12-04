<?php
session_start();

// Só acessa logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require "../php/bd.php";

// Buscar imóveis no banco
$conn = bd::getConexao();
$sql = "SELECT * FROM imoveis ORDER BY id DESC";
$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Imóveis (Admin) - ABhostel</title>

    <!-- CSS PRINCIPAL -->
    <link rel="stylesheet" href="../assets/css/listar_imoveis.css">

    <!-- Fonte e ícones -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Nunito&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<header>
    <div class="logo">
        <a href="painel.php"><img src="../Imagens/logo.png" alt="Logo ABhostel"></a>
    </div>

    <nav class="menu-desktop">
        <ul class="menu-links">
            <li><a href="../index.php">Início</a></li>
            <li><a href="listar_imoveis_admin.php" class="active">Imóveis</a></li>
            <li><a href="cadastrar_imovel.php">Cadastrar Imóvel</a></li>
        </ul>

        <!-- No admin aparece botão sair -->
        <a href="painel.php" class="btn-login">Voltar ao painel</a>
    </nav>

    <button class="btn open" aria-label="Abrir menu"><i class="fas fa-bars"></i></button>
</header>

<div class="container">
    <div class="lista-imoveis">

        <div class="coluna-esquerda">

            <p><?php echo $res->num_rows; ?> opções</p>
            <h1>Lista de Imóveis (Admin)</h1>

            <?php if ($res->num_rows > 0): ?>
                <?php while ($imovel = $res->fetch_assoc()): ?>

                    <?php
                    // Foto principal
                    $foto = "../Imagens/sem-foto.png";

                    if (!empty($imovel['fotos'])) {
                        $fotosArray = explode(",", $imovel['fotos']);
                        if ($fotosArray[0] != "") {
                            $foto = "../uploads/" . trim($fotosArray[0]);
                        }
                    }
                    ?>

                    <div class="imovel">

                        <div class="imovel-img">
                            <img src="<?php echo $foto; ?>" alt="Foto do imóvel">
                        </div>

                        <div class="imovel-info">

                            <p><?php echo $imovel['titulo']; ?></p>
                            <h3><?php echo ucfirst($imovel['tipo']); ?></h3>

                            <p>
                                <?php echo $imovel['quartos']; ?> quartos /
                                <?php echo $imovel['banheiros']; ?> banheiros /
                                <?php echo $imovel['wifi'] ? 'wifi' : 'sem wifi'; ?>
                            </p>

                            <div class="imovel-valor">
                                <p><?php echo $imovel['capacidade']; ?> hóspedes</p>

                                <h4>
                                    R$ <?php echo number_format($imovel['valor'], 2, ',', '.'); ?>
                                    <span>/ <?php echo $imovel['tipo_preco']; ?></span>
                                </h4>
                            </div>

                            <!-- BOTÕES ADMIN -->
                            <div class="acoes-admin">
                                <a href="editar_imovel.php?id=<?php echo $imovel['id']; ?>" class="btn-editar">
                                    Editar
                                </a>

                                <a href="excluir_imovel.php?id=<?php echo $imovel['id']; ?>" class="btn-excluir"
                                    onclick="return confirm('Tem certeza que deseja excluir este imóvel?');">
                                    Excluir
                                </a>
                            </div>

                        </div>
                    </div>

                <?php endwhile; ?>

            <?php else: ?>
                <p>Nenhum imóvel cadastrado ainda.</p>
            <?php endif; ?>

        </div>

        <div class="coluna-direita">
            <div class="barra-lateral">
                <h2>Filtro (somente visual)</h2>
                <p>Será implementado depois.</p>
            </div>
        </div>

    </div>
</div>

<script src="../assets/js/menu.js"></script>
</body>
</html>