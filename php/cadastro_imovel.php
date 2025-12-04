<?php
require_once "../php/bd.php";
session_start();

// VERIFICA LOGIN
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$conn = bd::getConexao();
$mensagem = "";

// QUANDO O FORM É ENVIADO
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $tipo = $_POST['propertyType'];
    $titulo = $_POST['title'];
    $descricao = $_POST['description'];

    $cep = $_POST['cep'];
    $logradouro = $_POST['logradouro'];
    $numero = $_POST['numero'];
    $complemento = $_POST['complemento'];
    $bairro = $_POST['bairro'];
    $estado = $_POST['estado'];

    $quartos = $_POST['quartos'];
    $suites = $_POST['suites'];
    $banheiros = $_POST['banheiro'];
    $capacidade = $_POST['capacidade'];

    // Checkboxes
    $wifi = isset($_POST['wi-fi']) ? 1 : 0;
    $piscina = isset($_POST['piscina']) ? 1 : 0;
    $estacionamento = isset($_POST['estacionamento']) ? 1 : 0;
    $ar = isset($_POST['ar-condicionado']) ? 1 : 0;
    $tv = isset($_POST['tv_a_cabo']) ? 1 : 0;

    $valor = $_POST['valor'];
    $tipo_preco = $_POST['tipo-preço'];
    $data_inicio = $_POST['data-inicio'];
    $data_termino = $_POST['data-termino'];

    $whatsapp = $_POST['whatsapp'];
    $email_prop = $_POST['email-proprietario'];

    // UPLOAD DAS FOTOS
    $listaFotos = [];

    if (!empty($_FILES['fotos']['name'][0])) {
        foreach ($_FILES['fotos']['name'] as $i => $nomeFoto) {
            $tmp = $_FILES['fotos']['tmp_name'][$i];

            $novoNome = uniqid() . "_" . $nomeFoto;
            move_uploaded_file($tmp, "../uploads/" . $novoNome);

            $listaFotos[] = $novoNome;
        }
    }

    $fotosJSON = json_encode($listaFotos);

    // INSERE NO BANCO
    $sql = $conn->prepare("
        INSERT INTO imoveis (
            tipo, titulo, descricao, cep, logradouro, numero, complemento, bairro, estado,
            quartos, suites, banheiros, capacidade,
            wifi, piscina, estacionamento, ar_condicionado, tv_cabo,
            valor, tipo_preco, data_inicio, data_termino,
            whatsapp, email_proprietario, fotos
        )
        VALUES (
            :tipo, :titulo, :descricao, :cep, :logradouro, :numero, :complemento, :bairro, :estado,
            :quartos, :suites, :banheiros, :capacidade,
            :wifi, :piscina, :estacionamento, :ar, :tv,
            :valor, :tipo_preco, :data_inicio, :data_termino,
            :whatsapp, :email_prop, :fotos
        )
    ");

    $sql->execute([
        ':tipo' => $tipo,
        ':titulo' => $titulo,
        ':descricao' => $descricao,

        ':cep' => $cep,
        ':logradouro' => $logradouro,
        ':numero' => $numero,
        ':complemento' => $complemento,
        ':bairro' => $bairro,
        ':estado' => $estado,

        ':quartos' => $quartos,
        ':suites' => $suites,
        ':banheiros' => $banheiros,
        ':capacidade' => $capacidade,

        ':wifi' => $wifi,
        ':piscina' => $piscina,
        ':estacionamento' => $estacionamento,
        ':ar' => $ar,
        ':tv' => $tv,

        ':valor' => $valor,
        ':tipo_preco' => $tipo_preco,
        ':data_inicio' => $data_inicio,
        ':data_termino' => $data_termino,

        ':whatsapp' => $whatsapp,
        ':email_prop' => $email_prop,

        ':fotos' => $fotosJSON
    ]);

    $mensagem = "Imóvel cadastrado com sucesso!";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ABhostel - Cadastrar Imóvel</title>
    <link rel="stylesheet" href="../assets/css/cadastro_imovel.css">
</head>
<body>

<header>
    <div class="logo">
        <a href="../index.php"><img src="../imagens/logo.png" alt="Logo ABhostel"></a>
    </div>

    <nav class="menu-desktop">
        <ul class="menu-links">
            <li><a href="../index.php">Início</a></li>
            <li><a href="listar_imoveis.php">Imóveis</a></li>
            <li><a href="#" class="active">Anuncie seu imóvel</a></li>
        </ul>
        <a href="logout.php" class="btn-login">Sair</a>
    </nav>
</header>

<br><br>

<div class="page-wrapper">
<main class="card">

    <h1>Anuncie seu Imóvel</h1>

    <?php if ($mensagem): ?>
        <p class="msg-sucesso" style="color: green; font-weight:bold;">
            <?= $mensagem ?>
        </p>
    <?php endif; ?>

    <!-- FORMULÁRIO -->
    <form method="POST" enctype="multipart/form-data">

        <h2 class="section-title">Informações básicas do imóvel</h2>

        <div class="form-group">
            <label for="propertyType">Tipo de imóvel</label>
            <select name="propertyType" id="propertyType" required>
                <option value="">Selecione</option>
                <option value="Casa">Casa</option>
                <option value="Apartamento">Apartamento</option>
                <option value="Kitnet">Kitnet</option>
                <option value="Suíte">Suíte</option>
                <option value="Hotel">Hotel</option>
                <option value="Pousada">Pousada</option>
                <option value="Chalé">Chalé</option>
            </select>
        </div>

        <div class="form-group">
            <label for="title">Título do anúncio</label>
            <input type="text" id="title" name="title" maxlength="100" required>
        </div>

        <div class="form-group">
            <label for="description">Descrição</label>
            <textarea id="description" name="description" maxlength="500" rows="4" required></textarea>
        </div>

        <h2 class="section-title">Localização do imóvel</h2>

        <div class="form-group">
            <label>CEP</label>
            <input type="text" name="cep">
        </div>

        <div class="form-group">
            <label>Logradouro (rua/avenida)</label>
            <input type="text" name="logradouro" required>
        </div>

        <div class="form-group">
            <label>Número</label>
            <input type="text" name="numero" required>
        </div>

        <div class="form-group">
            <label>Complemento (opcional)</label>
            <input type="text" name="complemento">
        </div>

        <div class="form-group">
            <label>Bairro</label>
            <input type="text" name="bairro" required>
        </div>

        <div class="form-group">
            <label>Estado</label>
            <select name="estado" required>
                <option value="">Selecione</option>
                <option value="Acre (AC)">Acre (AC)</option>
                    <option value="Alagoas (AL)">Alagoas (AL)</option>
                    <option value="Amapá (AP)">Amapá (AP)</option>
                    <option value="Amazonas (AM)">Amazonas (AM)</option>
                    <option value="Bahia (BA)">Bahia (BA)</option>
                    <option value="Ceará (CE)">Ceará (CE)</option>
                    <option value="Espírito Santo (ES)">Espírito Santo (ES)</option>
                    <option value="Goiás (GO)">Goiás (GO)</option>
                    <option value="Maranhão (MA)">Maranhão (MA)</option>
                    <option value="Mato Grosso (MT)">Mato Grosso (MT)</option>
                    <option value="Mato Grosso do Sul (MS)">Mato Grosso do Sul (MS)</option>
                    <option value="Minas Gerais (MG)">Minas Gerais (MG)</option>
                    <option value="Pará (PA)">Pará (PA)</option>
                    <option value="Paraíba (PB)">Paraíba (PB)</option>
                    <option value="Paraná (PR)">Paraná (PR)</option>
                    <option value="Pernambuco (PE)">Pernambuco (PE)</option>
                    <option value="Piauí (PI)">Piauí (PI)</option>
                    <option value="Rio de Janeiro (RJ)">Rio de Janeiro (RJ)</option>
                    <option value="Rio Grande do Norte (RN)">Rio Grande do Norte (RN)</option>
                    <option value="Rio Grande do Sul (RS)">Rio Grande do Sul (RS)</option>
                    <option value="Rondônia (RO)">Rondônia (RO)</option>
                    <option value="Roraima (RR)">Roraima (RR)</option>
                    <option value="Santa Catarina (SC)">Santa Catarina (SC)</option>
                    <option value="São Paulo (SP)">São Paulo (SP)</option>
                    <option value="Sergipe (SE)">Sergipe (SE)</option>
                    <option value="Tocantins (TO)">Tocantins (TO)</option>
                    <option value="Distrito Federal (DF)">Distrito Federal (DF)</option>
            </select>
        </div>

        <h2 class="section-title">Estrutura do imóvel</h2>

        <div class="form-group">
            <label>Quartos</label>
            <input type="number" name="quartos" min="1" required>
        </div>

        <div class="form-group">
            <label>Suítes</label>
            <input type="number" name="suites" min="0">
        </div>

        <div class="form-group">
            <label>Banheiros</label>
            <input type="number" name="banheiro" min="1" required>
        </div>

        <div class="form-group">
            <label>Capacidade</label>
            <input type="number" name="capacidade" min="1" required>
        </div>

        <h2 class="section-title">O que o imóvel oferece</h2>

        <label><input type="checkbox" name="wi-fi"> Wi-fi</label>
        <label><input type="checkbox" name="piscina"> Piscina</label>
        <label><input type="checkbox" name="estacionamento"> Estacionamento</label>
        <label><input type="checkbox" name="ar-condicionado"> Ar-condicionado</label>
        <label><input type="checkbox" name="tv_a_cabo"> TV a cabo</label>

        <h2 class="section-title">Preço e período</h2>

        <div class="form-group">
            <label>Valor (R$)</label>
            <input type="number" name="valor" required>
        </div>

        <div class="form-group">
            <label>Tipo de preço</label>
            <select name="tipo-preço" required>
                <option value="noite">Por noite</option>
                <option value="semana">Por semana</option>
                <option value="mes">Por mês</option>
            </select>
        </div>

        <div class="form-group">
            <label>Data de início</label>
            <input type="date" name="data-inicio" required>
        </div>

        <div class="form-group">
            <label>Data de término</label>
            <input type="date" name="data-termino" required>
        </div>

        <h2 class="section-title">Contato do proprietário</h2>

        <div class="form-group">
            <label>WhatsApp</label>
            <input type="tel" name="whatsapp" required>
        </div>

        <div class="form-group">
            <label>Email (opcional)</label>
            <input type="email" name="email-proprietario">
        </div>

        <h2 class="section-title">Fotos do imóvel</h2>

        <div class="form-group">
            <label>Adicionar fotos</label>
            <input type="file" name="fotos[]" multiple accept="image/*">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Publicar anúncio</button>
        </div>

    </form>

</main>
</div>

</body>
</html>