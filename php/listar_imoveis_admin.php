<?php
session_start();

// Impede acesso sem login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require "../php/bd.php"; // nossa classe PDO

// Conexão PDO
$conn = bd::getConexao();

// Buscar imóveis com PDO
$sql = $conn->prepare("SELECT * FROM imoveis ORDER BY id DESC");
$sql->execute();

// Resultado em array associativo
$imoveis = $sql->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Imóveis (Admin) - ABhostel</title>

    <link rel="stylesheet" href="../assets/css/listar_imoveis.css">
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
            <li><a href="painel.php">Painel</a></li>
            <li><a href="listar_imoveis_admin.php" class="active">Imóveis</a></li>
            <li><a href="cadastrar_imovel.php">Cadastrar Imóvel</a></li>
        </ul>

        <a href="painel.php" class="btn-login">Voltar ao painel</a>
    </nav>

    <button class="btn open" aria-label="Abrir menu"><i class="fas fa-bars"></i></button>
</header>

<div class="container">
    <div class="lista-imoveis">

        <div class="coluna-esquerda">

            <p><?= count($imoveis); ?> opções</p>
            <h1>Lista de Imóveis (Admin)</h1>

            <?php if (count($imoveis) > 0): ?>
                <?php foreach ($imoveis as $imovel): ?>

                    <?php
                    // Foto principal
                    $foto = "../Imagens/sem-foto.png";

                    if (!empty($imovel['fotos'])) {
                        $fotosArray = explode(",", $imovel['fotos']);
                        if (!empty(trim($fotosArray[0]))) {
                            $foto = "../uploads/" . trim($fotosArray[0]);
                        }
                    }
                    ?>

                    <div class="imovel">

                        <div class="imovel-img">
                            <img src="<?= $foto ?>" alt="Foto do imóvel">
                        </div>

                        <div class="imovel-info">

                            <p><?= $imovel['titulo']; ?></p>
                            <h3><?= ucfirst($imovel['tipo']); ?></h3>

                            <p>
                                <?= $imovel['quartos']; ?> quartos /
                                <?= $imovel['banheiros']; ?> banheiros /
                                <?= $imovel['wifi'] ? 'wifi' : 'sem wifi'; ?>
                            </p>

                            <div class="imovel-valor">
                                <p><?= $imovel['capacidade']; ?> hóspedes</p>
                                <h4>
                                    R$ <?= number_format($imovel['valor'], 2, ',', '.'); ?>
                                    <span>/ <?= $imovel['tipo_preco']; ?></span>
                                </h4>
                            </div>

                            <div class="acoes-admin">
                                <a href="editar.php?id=<?= $imovel['id']; ?>" class="btn-editar">Editar</a>

                                <a href="excluir.php?id=<?= $imovel['id']; ?>"
                                   class="btn-excluir"
                                   onclick="return confirm('Tem certeza que deseja excluir este imóvel?');">Exclui</a>
                            </div>

                        </div>
                    </div>

                <?php endforeach; ?>

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