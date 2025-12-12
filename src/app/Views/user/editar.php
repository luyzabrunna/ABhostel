?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABhostel - Editar Imóvel</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Nunito&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/cadastro_imovel.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php
require_once "../php/ImovelController.php";
session_start();

// Verifica login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$controller = new ImovelController();
$mensagem = "";
$erro = "";

// Pega o ID do imóvel
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: listar_imoveis_admin.php");
    exit;
}

// Busca o imóvel
$imovel = $controller->buscarPorId($id);

if (!$imovel) {
    header("Location: listar_imoveis_admin.php");
    exit;
}

// Processa atualização
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $resultado = $controller->update($id, $_POST, $_FILES);
    
    if ($resultado['success']) {
        $mensagem = $resultado['message'];
        // Recarrega os dados atualizados
        $imovel = $controller->buscarPorId($id);
    } else {
        $erro = $resultado['message'];
    }
}
?>

<header>
    <div class="logo">
        <a href="index.php"><img src="/assets/imagens/logo.png" alt="Logo ABhostel"></a>
    </div>
    <nav class="menu-desktop">
        <ul class="menu-links">
            <li><a href="index.php">Início</a></li>
            <li><a href="listar_imoveis_admin.php">Imóveis</a></li>
            <li><a href="#" class="active">Editar imóvel</a></li>
        </ul>
        <a href="painel.php" class="btn-login">Voltar ao painel</a>
    </nav>
    <button class="btn open" aria-label="Abrir menu"><i class="fas fa-bars"></i></button>
</header>

<div class="menu-overlay" aria-hidden="true"></div>
<div class="mobile-menu" aria-hidden="true">
    <div class="mobile-links">
        <a href="index.php">Início</a>
        <hr class="divider">
        <a href="listar_imoveis_admin.php">Imóveis</a>
        <a href="cadastrar_imovel.php">Cadastrar