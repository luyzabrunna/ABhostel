<?php
require_once "../php/bd.php";

class ImovelController {
    
    private $conn;
    private $uploadDir;
    
    public function __construct() {
        $this->conn = bd::getConexao();
        $this->uploadDir = __DIR__ . "/../uploads/";
        
        // Garante que a pasta uploads existe
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }
    
    /**
     * CREATE - Cadastra um novo imóvel
     * @return array ['success' => bool, 'message' => string, 'id' => int|null]
     */
    public function create($dados, $arquivos) {
        try {
            // Validação de sessão
            if (!$this->verificarLogin()) {
                return ['success' => false, 'message' => 'Usuário não autenticado'];
            }
            
            // Dados básicos
            $tipo = $dados['propertyType'];
            $titulo = $dados['title'];
            $descricao = $dados['description'];
            
            // Localização
            $cidade = $dados['cidade'];
            $logradouro = $dados['logradouro'];
            $numero = $dados['numero'];
            $complemento = $dados['complemento'] ?? '';
            $bairro = $dados['bairro'];
            $estado = $dados['estado'];
            
            // Estrutura
            $quartos = $dados['quartos'];
            $suites = $dados['suites'] ?? 0;
            $banheiros = $dados['banheiros'];
            $capacidade = $dados['capacidade'];
            
            // Facilidades (checkboxes)
            $wifi = isset($dados['wifi']) ? 1 : 0;
            $ar_condicionado = isset($dados['ar-condicionado']) ? 1 : 0;
            $estacionamento = isset($dados['estacionamento']) ? 1 : 0;
            $pet_friendly = isset($dados['pet_friendly']) ? 1 : 0;
            $piscina = isset($dados['piscina']) ? 1 : 0;
            $cozinha = isset($dados['cozinha']) ? 1 : 0;
            $tv = isset($dados['tv']) ? 1 : 0;
            $area_trabalho = isset($dados['area_trabalho']) ? 1 : 0;
            $cafe_manha = isset($dados['cafe']) ? 1 : 0;
            $maquina_lavar = isset($dados['maquina']) ? 1 : 0;
            
            // Preço e período
            $valor = $dados['valor'];
            $tipo_preco = $dados['tipo_preço'];
            $data_inicio = $dados['data_inicio'];
            $data_termino = $dados['data_termino'];
            
            // Contato
            $whatsapp = $dados['whatsapp'];
            $email_prop = $dados['email_proprietario'] ?? '';
            
            // Upload das fotos
            $fotosJSON = $this->processarUploadFotos($arquivos);
            
            // Preparar SQL de inserção
            $sql = $this->conn->prepare("
                INSERT INTO imoveis (
                    tipo, titulo, descricao, cidade, logradouro, numero, complemento, bairro, estado,
                    quartos, suites, banheiros, capacidade,
                    wifi, piscina, estacionamento, ar_condicionado, tv, pet_friendly, cozinha, area_trabalho,
                    cafe_manha, maquina_lavar,
                    valor, tipo_preco, data_inicio, data_termino,
                    whatsapp, email_proprietario, fotos
                )
                VALUES (
                    :tipo, :titulo, :descricao, :cidade, :logradouro, :numero, :complemento, :bairro, :estado,
                    :quartos, :suites, :banheiros, :capacidade,
                    :wifi, :piscina, :estacionamento, :ar_condicionado, :tv, :pet_friendly, :cozinha, :area_trabalho,
                    :cafe_manha, :maquina_lavar,
                    :valor, :tipo_preco, :data_inicio, :data_termino,
                    :whatsapp, :email_prop, :fotos
                )
            ");
            
            $sql->execute([
                ':tipo' => $tipo,
                ':titulo' => $titulo,
                ':descricao' => $descricao,
                ':cidade' => $cidade,
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
                ':ar_condicionado' => $ar_condicionado,
                ':tv' => $tv,
                ':pet_friendly' => $pet_friendly,
                ':cozinha' => $cozinha,
                ':area_trabalho' => $area_trabalho,
                ':cafe_manha' => $cafe_manha,
                ':maquina_lavar' => $maquina_lavar,
                ':valor' => $valor,
                ':tipo_preco' => $tipo_preco,
                ':data_inicio' => $data_inicio,
                ':data_termino' => $data_termino,
                ':whatsapp' => $whatsapp,
                ':email_prop' => $email_prop,
                ':fotos' => $fotosJSON
            ]);
            
            $lastId = $this->conn->lastInsertId();
            
            return [
                'success' => true, 
                'message' => 'Imóvel cadastrado com sucesso!',
                'id' => $lastId
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false, 
                'message' => 'Erro ao cadastrar imóvel: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * READ - Lista imóveis com filtros (versão pública)
     * @return array Lista de imóveis
     */
    public function listar($filtros = []) {
        try {
            // Query base
            $sql = "SELECT * FROM imoveis WHERE 1=1";
            $params = [];
            
            // Filtro por localização (cidade)
            if (!empty($filtros['localizacao'])) {
                $sql .= " AND cidade LIKE :localizacao";
                $params[':localizacao'] = "%" . $filtros['localizacao'] . "%";
            }
            
            // Filtro por capacidade (hóspedes)
            if (!empty($filtros['hospedes'])) {
                $sql .= " AND capacidade >= :hospedes";
                $params[':hospedes'] = $filtros['hospedes'];
            }
            
            // Filtro por tipo de imóvel
            if (!empty($filtros['tipo'])) {
                $sql .= " AND tipo = :tipo";
                $params[':tipo'] = $filtros['tipo'];
            }
            
            // Filtro por estado
            if (!empty($filtros['estado'])) {
                $sql .= " AND estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            
            // Filtros de facilidades
            if (!empty($filtros['wifi'])) {
                $sql .= " AND wifi = 1";
            }
            if (!empty($filtros['ar_condicionado'])) {
                $sql .= " AND ar_condicionado = 1";
            }
            if (!empty($filtros['estacionamento'])) {
                $sql .= " AND estacionamento = 1";
            }
            if (!empty($filtros['pet_friendly'])) {
                $sql .= " AND pet_friendly = 1";
            }
            if (!empty($filtros['piscina'])) {
                $sql .= " AND piscina = 1";
            }
            if (!empty($filtros['cozinha'])) {
                $sql .= " AND cozinha = 1";
            }
            if (!empty($filtros['tv'])) {
                $sql .= " AND tv = 1";
            }
            if (!empty($filtros['area_trabalho'])) {
                $sql .= " AND area_trabalho = 1";
            }
            if (!empty($filtros['cafe_manha'])) {
                $sql .= " AND cafe_manha = 1";
            }
            if (!empty($filtros['maquina_lavar'])) {
                $sql .= " AND maquina_lavar = 1";
            }
            
            // Ordenação
            $sql .= " ORDER BY id DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * READ - Lista imóveis (versão admin)
     * @return array Lista de imóveis
     */
    public function listarAdmin() {
        try {
            if (!$this->verificarLogin()) {
                return [];
            }
            
            $sql = $this->conn->prepare("SELECT * FROM imoveis ORDER BY id DESC");
            $sql->execute();
            
            return $sql->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * READ - Busca um imóvel específico por ID
     * @param int $id
     * @return object|null
     */
    public function buscarPorId($id) {
        try {
            $sql = $this->conn->prepare("SELECT * FROM imoveis WHERE id = :id");
            $sql->bindValue(":id", $id);
            $sql->execute();
            
            return $sql->fetch(PDO::FETCH_OBJ);
            
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * UPDATE - Atualiza um imóvel existente
     * @param int $id
     * @param array $dados
     * @param array $arquivos
     * @return array ['success' => bool, 'message' => string]
     */
    public function update($id, $dados, $arquivos) {
        try {
            // Validação de sessão
            if (!$this->verificarLogin()) {
                return ['success' => false, 'message' => 'Usuário não autenticado'];
            }
            
            // Busca o imóvel atual para pegar as fotos antigas
            $imovel = $this->buscarPorId($id);
            
            if (!$imovel) {
                return ['success' => false, 'message' => 'Imóvel não encontrado'];
            }
            
            // Dados básicos
            $tipo = $dados['propertyType'];
            $titulo = $dados['title'];
            $descricao = $dados['description'];
            
            // Localização
            $cidade = $dados['cidade'];
            $logradouro = $dados['logradouro'];
            $numero = $dados['numero'];
            $complemento = $dados['complemento'] ?? '';
            $bairro = $dados['bairro'];
            $estado = $dados['estado'];
            
            // Estrutura
            $quartos = $dados['quartos'];
            $suites = $dados['suites'] ?? 0;
            $banheiros = $dados['banheiros'];
            $capacidade = $dados['capacidade'];
            
            // Facilidades (checkboxes)
            $wifi = isset($dados['wifi']) ? 1 : 0;
            $ar_condicionado = isset($dados['ar_condicionado']) ? 1 : 0;
            $estacionamento = isset($dados['estacionamento']) ? 1 : 0;
            $pet_friendly = isset($dados['pet_friendly']) ? 1 : 0;
            $piscina = isset($dados['piscina']) ? 1 : 0;
            $cozinha = isset($dados['cozinha']) ? 1 : 0;
            $tv = isset($dados['tv']) ? 1 : 0;
            $area_trabalho = isset($dados['area_trabalho']) ? 1 : 0;
            $cafe_manha = isset($dados['cafe_manha']) ? 1 : 0;
            $maquina_lavar = isset($dados['maquina_lavar']) ? 1 : 0;
            
            // Preço e período
            $valor = $dados['valor'];
            $tipo_preco = $dados['tipo_preco'];
            $data_inicio = $dados['data_inicio'];
            $data_termino = $dados['data_termino'];
            
            // Contato
            $whatsapp = $dados['whatsapp'];
            $email_prop = $dados['email_proprietario'] ?? '';
            
            // Processar fotos (mantém as antigas + adiciona novas)
            $fotosAntigas = json_decode($imovel->fotos, true);
            if (!is_array($fotosAntigas)) {
                $fotosAntigas = [];
            }
            
            $todasFotos = $fotosAntigas;
            
            // Adiciona novas fotos se houver
            if (!empty($arquivos['fotos']['name'][0])) {
                $novasFotos = $this->processarUploadFotosArray($arquivos);
                $todasFotos = array_merge($todasFotos, $novasFotos);
            }
            
            $fotosJSON = json_encode($todasFotos);
            
            // Preparar SQL de atualização
            $sql = $this->conn->prepare("
                UPDATE imoveis SET
                    tipo = :tipo,
                    titulo = :titulo,
                    descricao = :descricao,
                    cidade = :cidade,
                    logradouro = :logradouro,
                    numero = :numero,
                    complemento = :complemento,
                    bairro = :bairro,
                    estado = :estado,
                    quartos = :quartos,
                    suites = :suites,
                    banheiros = :banheiros,
                    capacidade = :capacidade,
                    wifi = :wifi,
                    piscina = :piscina,
                    estacionamento = :estacionamento,
                    ar_condicionado = :ar_condicionado,
                    tv = :tv,
                    pet_friendly = :pet_friendly,
                    cozinha = :cozinha,
                    area_trabalho = :area_trabalho,
                    cafe_manha = :cafe_manha,
                    maquina_lavar = :maquina_lavar,
                    valor = :valor,
                    tipo_preco = :tipo_preco,
                    data_inicio = :data_inicio,
                    data_termino = :data_termino,
                    whatsapp = :whatsapp,
                    email_proprietario = :email_prop,
                    fotos = :fotos
                WHERE id = :id
            ");
            
            $sql->execute([
                ':tipo' => $tipo,
                ':titulo' => $titulo,
                ':descricao' => $descricao,
                ':cidade' => $cidade,
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
                ':ar_condicionado' => $ar_condicionado,
                ':tv' => $tv,
                ':pet_friendly' => $pet_friendly,
                ':cozinha' => $cozinha,
                ':area_trabalho' => $area_trabalho,
                ':cafe_manha' => $cafe_manha,
                ':maquina_lavar' => $maquina_lavar,
                ':valor' => $valor,
                ':tipo_preco' => $tipo_preco,
                ':data_inicio' => $data_inicio,
                ':data_termino' => $data_termino,
                ':whatsapp' => $whatsapp,
                ':email_prop' => $email_prop,
                ':fotos' => $fotosJSON,
                ':id' => $id
            ]);
            
            return [
                'success' => true, 
                'message' => 'Imóvel atualizado com sucesso!'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false, 
                'message' => 'Erro ao atualizar imóvel: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * DELETE - Exclui um imóvel
     * @param int $id
     * @return array ['success' => bool, 'message' => string]
     */
    public function delete($id) {
        try {
            // Validação de sessão
            if (!$this->verificarLogin()) {
                return ['success' => false, 'message' => 'Usuário não autenticado'];
            }
            
            // Busca o imóvel
            $imovel = $this->buscarPorId($id);
            
            if (!$imovel) {
                return ['success' => false, 'message' => 'Imóvel não encontrado'];
            }
            
            // Deleta as fotos físicas
            if (!empty($imovel->fotos)) {
                $listaFotos = json_decode($imovel->fotos, true);
                
                if (is_array($listaFotos)) {
                    foreach ($listaFotos as $foto) {
                        $arquivo = $this->uploadDir . $foto;
                        if (file_exists($arquivo)) {
                            unlink($arquivo);
                        }
                    }
                }
            }
            
            // Deleta o registro do banco
            $sql = $this->conn->prepare("DELETE FROM imoveis WHERE id = :id");
            $sql->bindValue(":id", $id);
            $sql->execute();
            
            return [
                'success' => true, 
                'message' => 'Imóvel excluído com sucesso!'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false, 
                'message' => 'Erro ao excluir imóvel: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Processa upload de múltiplas fotos
     * @param array $arquivos $_FILES
     * @return string JSON com nomes dos arquivos
     */
    private function processarUploadFotos($arquivos) {
        $listaFotos = [];
        
        if (!empty($arquivos['fotos']['name'][0])) {
            foreach ($arquivos['fotos']['name'] as $i => $nomeFoto) {
                $tmp = $arquivos['fotos']['tmp_name'][$i];
                
                // Nome único para evitar conflito
                $novoNome = uniqid() . "_" . basename($nomeFoto);
                
                // Move o arquivo
                if (move_uploaded_file($tmp, $this->uploadDir . $novoNome)) {
                    $listaFotos[] = $novoNome;
                }
            }
        }
        
        return json_encode($listaFotos);
    }
    
    /**
     * Processa upload de fotos retornando array (para merge no update)
     * @param array $arquivos $_FILES
     * @return array Array com nomes dos arquivos
     */
    private function processarUploadFotosArray($arquivos) {
        $listaFotos = [];
        
        if (!empty($arquivos['fotos']['name'][0])) {
            foreach ($arquivos['fotos']['name'] as $i => $nomeFoto) {
                $tmp = $arquivos['fotos']['tmp_name'][$i];
                
                // Nome único para evitar conflito
                $novoNome = uniqid() . "_" . basename($nomeFoto);
                
                // Move o arquivo
                if (move_uploaded_file($tmp, $this->uploadDir . $novoNome)) {
                    $listaFotos[] = $novoNome;
                }
            }
        }
        
        return $listaFotos;
    }
    
    /**
     * Verifica se o usuário está logado
     * @return bool
     */
    private function verificarLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['usuario_id']);
    }
    
    /**
     * Obtém a primeira foto de um imóvel ou retorna imagem padrão
     * @param string $fotosJSON
     * @return string Caminho da foto
     */
    public function getPrimeiraFoto($fotosJSON) {
        $fotos = json_decode($fotosJSON, true);
        
        if (is_array($fotos) && count($fotos) > 0) {
            return "../uploads/" . $fotos[0];
        }
        
        return "../Imagens/sem-foto.png";
    }
    
    /**
     * Obtém todas as fotos de um imóvel
     * @param string $fotosJSON
     * @return array Array com caminhos das fotos
     */
    public function getAllFotos($fotosJSON) {
        $fotos = json_decode($fotosJSON, true);
        
        if (!is_array($fotos)) {
            return [];
        }
        
        return array_map(function($foto) {
            return "../uploads/" . $foto;
        }, $fotos);
    }
    
    /**
     * Conta total de imóveis
     * @return int
     */
    public function contarImoveis() {
        try {
            $sql = $this->conn->query("SELECT COUNT(*) as total FROM imoveis");
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result['total'];
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Busca imóveis por tipo
     * @param string $tipo
     * @return array
     */
    public function buscarPorTipo($tipo) {
        try {
            $sql = $this->conn->prepare("SELECT * FROM imoveis WHERE tipo = :tipo ORDER BY id DESC");
            $sql->bindValue(":tipo", $tipo);
            $sql->execute();
            
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Busca imóveis disponíveis em um período
     * @param string $dataInicio
     * @param string $dataFim
     * @return array
     */
    public function buscarDisponiveis($dataInicio, $dataFim) {
        try {
            $sql = $this->conn->prepare("
                SELECT * FROM imoveis 
                WHERE data_inicio <= :dataFim 
                AND data_termino >= :dataInicio
                ORDER BY id DESC
            ");
            $sql->bindValue(":dataInicio", $dataInicio);
            $sql->bindValue(":dataFim", $dataFim);
            $sql->execute();
            
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}