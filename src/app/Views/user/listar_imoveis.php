?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Imóveis - ABhostel</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@900&family=Nunito&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/listar_imoveis.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php
require_once "../php/ImovelController.php";

$controller = new ImovelController();

// Prepara filtros
$filtros = [
    'localizacao' => $_GET['localizacao'] ?? '',
    'hospedes' => $_GET['hospedes'] ?? '',
    'tipo' => $_GET['tipo'] ?? '',
    'estado' => $_GET['estado'] ?? '',
    'wifi' => isset($_GET['wifi']) ? 1 : 0,
    'ar_condicionado' => isset($_GET['ar_condicionado']) ? 1 : 0,
    'estacionamento' => isset($_GET['estacionamento']) ? 1 : 0,
    'pet_friendly' => isset($_GET['pet_friendly']) ? 1 : 0,
    'piscina' => isset($_GET['piscina']) ? 1 : 0,
    'cozinha' => isset($_GET['cozinha']) ? 1 : 0,
    'tv' => isset($_GET['tv']) ? 1 : 0,
    'area_trabalho' => isset($_GET['area_trabalho']) ? 1 : 0,
    'cafe_manha' => isset($_GET['cafe_manha']) ? 1 : 0,
    'maquina_lavar' => isset($_GET['maquina_lavar']) ? 1 : 0
];

$imoveis = $controller->listar($filtros);

include "header.php";
?>

<div class="container">
    <div class="lista-imoveis">

        <!-- COLUNA ESQUERDA (IMÓVEIS) -->
        <div class="coluna-esquerda">
            <p><?php echo count($imoveis); ?> opções encontradas</p>
            <h1>Lista de Imóveis</h1>

            <?php if (count($imoveis) == 0): ?>
                <p>Nenhum imóvel encontrado com os filtros selecionados.</p>
            <?php endif; ?>

            <?php foreach ($imoveis as $imovel): ?>
            <div class="imovel">
                
                <div class="imovel-img">
                    <?php
                    $foto = $controller->getPrimeiraFoto($imovel['fotos']);
                    ?>
                    <img src="<?= $foto ?>" alt="Foto do imóvel">
                </div>

                <div class="imovel-info">
                    <p><?php echo htmlspecialchars($imovel['descricao']); ?></p>
                    <h3><?php echo htmlspecialchars($imovel['titulo']); ?></h3>

                    <p>
                        <?php echo $imovel['quartos']; ?> quartos / 
                        <?php echo $imovel['banheiros']; ?> banheiros
                        <?php if ($imovel['wifi']): ?> / wifi<?php endif; ?>
                    </p>

                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star-half-stroke"></i>
                    <i class="fa-regular fa-star"></i>

                    <div class="imovel-valor">
                        <p><?php echo $imovel['capacidade']; ?> Hóspedes</p>
                        <h4>
                            R$ <?= number_format($imovel['valor'], 2, ',', '.'); ?>
                            <span>/ <?= $imovel['tipo_preco']; ?></span>
                        </h4>
                    </div>

                    <a href="detalhes_imovel.php?id=<?= $imovel['id']; ?>" class="btn-ver-mais">Ver detalhes</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- COLUNA DIREITA (FILTROS) -->
        <div class="coluna-direita">
            <div class="barra-lateral">
                <h2>Filtro de Busca</h2>

                <form action="" method="get">

                    <h3>Tipo de Imóvel</h3>
                    <div class="filtro">
                        <select name="tipo">
                            <option value="">Todos</option>
                            <option value="Casa">Casa</option>
                            <option value="Apartamento">Apartamento</option>
                            <option value="Kitnet">Kitnet</option>
                            <option value="Suíte">Suíte</option>
                            <option value="Hotel">Hotel</option>
                            <option value="Pousada">Pousada</option>
                            <option value="Chalé">Chalé</option>
                        </select>
                    </div>

                    <h3>Facilidades</h3>
                    
                    <div class="filtro">
                        <input type="checkbox" name="wifi" id="wifi"> 
                        <label for="wifi">Wi-fi</label>
                    </div>

                    <div class="filtro">
                        <input type="checkbox" name="ar_condicionado" id="ar"> 
                        <label for="ar">Ar-condicionado</label>
                    </div>

                    <div class="filtro">
                        <input type="checkbox" name="estacionamento" id="estac"> 
                        <label for="estac">Estacionamento</label>
                    </div>

                    <div class="filtro">
                        <input type="checkbox" name="pet_friendly" id="pet"> 
                        <label for="pet">Pet-friendly</label>
                    </div>

                    <div class="filtro">
                        <input type="checkbox" name="piscina" id="pisc"> 
                        <label for="pisc">Piscina</label>
                    </div>

                    <div class="filtro">
                        <input type="checkbox" name="cozinha" id="coz"> 
                        <label for="coz">Cozinha</label>
                    </div>

                    <div class="filtro">
                        <input type="checkbox" name="tv" id="tv"> 
                        <label for="tv">TV</label>
                    </div>

                    <div class="filtro">
                        <input type="checkbox" name="area_trabalho" id="area"> 
                        <label for="area">Área de trabalho</label>
                    </div>

                    <div class="filtro">
                        <input type="checkbox" name="cafe_manha" id="cafe"> 
                        <label for="cafe">Café da manhã</label>
                    </div>

                    <div class="filtro">
                        <input type="checkbox" name="maquina_lavar" id="maq"> 
                        <label for="maq">Máquina de lavar</label>
                    </div>

                    <h3>Localização</h3>
                    
                    <div class="filtro">
                        <input type="text" name="localizacao" placeholder="Cidade" value="<?= $_GET['localizacao'] ?? '' ?>">
                    </div>

                    <div class="filtro">
                        <select name="estado">
                            <option value="">Todos os estados</option>
                            <option value="Acre (AC)">Acre (AC)</option>
                            <option value="São Paulo (SP)">São Paulo (SP)</option>
                            <option value="Rio de Janeiro (RJ)">Rio de Janeiro (RJ)</option>
                            <!-- Adicionar outros estados -->
                        </select>
                    </div>

                    <h3>Hóspedes</h3>
                    <div class="filtro">
                        <input type="number" name="hospedes" min="1" placeholder="Número de hóspedes" value="<?= $_GET['hospedes'] ?? '' ?>">
                    </div>

                    <button type="submit" class="cadastro-btn">Buscar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/menu.js"></script>
</body>
</html>

<?php