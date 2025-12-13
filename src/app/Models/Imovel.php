<?php
namespace App\Models;




class Imovel {
   
    // Propriedades da tabela imoveis
    public $id;
    public $tipo;
    public $titulo;
    public $descricao;
   
    // Localização
    public $cidade;
    public $logradouro;
    public $numero;
    public $complemento;
    public $bairro;
    public $estado;
   
    // Estrutura
    public $quartos;
    public $suites;
    public $banheiros;
    public $capacidade;
   
    // Facilidades
    public $wifi;
    public $ar_condicionado;
    public $estacionamento;
    public $pet_friendly;
    public $piscina;
    public $cozinha;
    public $tv;
    public $area_trabalho;
    public $cafe_manha;
    public $maquina_lavar;
   
    // Preço e período
    public $valor;
    public $tipo_preco;
    public $data_inicio;
    public $data_termino;
   
    // Contato
    public $whatsapp;
    public $email_proprietario;
   
    // Fotos (JSON)
    public $fotos;
   
    // Timestamps
    public $created_at;
    public $updated_at;
   
    // Conexão com banco
    private $conn;
    private $table = "imoveis";
   
 
    //Construtor
    public function __construct() {
       
        $this->conn = BD::getConexao();
    }
   
   
    //CREATE - Insere um novo imóvel no banco
    public function create() {
        try {
            $sql = $this->conn->prepare("
                INSERT INTO {$this->table} (
                    tipo, titulo, descricao,
                    cidade, logradouro, numero, complemento, bairro, estado,
                    quartos, suites, banheiros, capacidade,
                    wifi, piscina, estacionamento, ar_condicionado, tv, pet_friendly,
                    cozinha, area_trabalho, cafe_manha, maquina_lavar,
                    valor, tipo_preco, data_inicio, data_termino,
                    whatsapp, email_proprietario, fotos
                )
                VALUES (
                    :tipo, :titulo, :descricao,
                    :cidade, :logradouro, :numero, :complemento, :bairro, :estado,
                    :quartos, :suites, :banheiros, :capacidade,
                    :wifi, :piscina, :estacionamento, :ar_condicionado, :tv, :pet_friendly,
                    :cozinha, :area_trabalho, :cafe_manha, :maquina_lavar,
                    :valor, :tipo_preco, :data_inicio, :data_termino,
                    :whatsapp, :email_proprietario, :fotos
                )
            ");
           
            $sql->execute([
                ':tipo' => $this->tipo,
                ':titulo' => $this->titulo,
                ':descricao' => $this->descricao,
                ':cidade' => $this->cidade,
                ':logradouro' => $this->logradouro,
                ':numero' => $this->numero,
                ':complemento' => $this->complemento,
                ':bairro' => $this->bairro,
                ':estado' => $this->estado,
                ':quartos' => $this->quartos,
                ':suites' => $this->suites,
                ':banheiros' => $this->banheiros,
                ':capacidade' => $this->capacidade,
                ':wifi' => $this->wifi,
                ':piscina' => $this->piscina,
                ':estacionamento' => $this->estacionamento,
                ':ar_condicionado' => $this->ar_condicionado,
                ':tv' => $this->tv,
                ':pet_friendly' => $this->pet_friendly,
                ':cozinha' => $this->cozinha,
                ':area_trabalho' => $this->area_trabalho,
                ':cafe_manha' => $this->cafe_manha,
                ':maquina_lavar' => $this->maquina_lavar,
                ':valor' => $this->valor,
                ':tipo_preco' => $this->tipo_preco,
                ':data_inicio' => $this->data_inicio,
                ':data_termino' => $this->data_termino,
                ':whatsapp' => $this->whatsapp,
                ':email_proprietario' => $this->email_proprietario,
                ':fotos' => $this->fotos
            ]);
           
            return $this->conn->lastInsertId();
           
        } catch (\PDOException $e) {
            error_log("Erro ao criar imóvel: " . $e->getMessage());
            return false;
        }
    }
   
   
    //READ - Busca todos os imóveis
    public function readAll() {
        try {
            $sql = $this->conn->prepare("SELECT * FROM {$this->table} ORDER BY id DESC");
            $sql->execute();
            return $sql->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erro ao buscar imóveis: " . $e->getMessage());
            return [];
        }
    }
   


    //READ - Busca um imóvel por ID
    public function readOne($id) {
        try {
            $sql = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = :id");
            $sql->bindParam(':id', $id);
            $sql->execute();
           
            $row = $sql->fetch(\PDO::FETCH_ASSOC);
           
            if ($row) {
                $this->hydrate($row);
                return $this;
            }
           
            return false;
           
        } catch (\PDOException $e) {
            error_log("Erro ao buscar imóvel: " . $e->getMessage());
            return false;
        }
    }
   


    //READ - Busca imóveis com filtros
    public function readWithFilters($filtros = []) {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE 1=1";
            $params = [];
           
            // Filtro por localização (cidade)
            if (!empty($filtros['localizacao'])) {
                $sql .= " AND cidade LIKE :localizacao";
                $params[':localizacao'] = "%" . $filtros['localizacao'] . "%";
            }
           
            // Filtro por capacidade
            if (!empty($filtros['hospedes'])) {
                $sql .= " AND capacidade >= :hospedes";
                $params[':hospedes'] = $filtros['hospedes'];
            }
           
            // Filtro por tipo
            if (!empty($filtros['tipo'])) {
                $sql .= " AND tipo = :tipo";
                $params[':tipo'] = $filtros['tipo'];
            }
           
            // Filtro por estado
            if (!empty($filtros['estado'])) {
                $sql .= " AND estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
           
            // Filtro por cidade específica
            if (!empty($filtros['cidade'])) {
                $sql .= " AND cidade = :cidade";
                $params[':cidade'] = $filtros['cidade'];
            }
           
            // Filtros de facilidades
            $facilidades = [
                'wifi', 'ar_condicionado', 'estacionamento', 'pet_friendly',
                'piscina', 'cozinha', 'tv', 'area_trabalho', 'cafe_manha', 'maquina_lavar'
            ];
           
            foreach ($facilidades as $facilidade) {
                if (!empty($filtros[$facilidade])) {
                    $sql .= " AND {$facilidade} = 1";
                }
            }
           
            // Filtro por faixa de preço
            if (!empty($filtros['valor_min'])) {
                $sql .= " AND valor >= :valor_min";
                $params[':valor_min'] = $filtros['valor_min'];
            }
           
            if (!empty($filtros['valor_max'])) {
                $sql .= " AND valor <= :valor_max";
                $params[':valor_max'] = $filtros['valor_max'];
            }
           
            // Filtro por disponibilidade (data)
            if (!empty($filtros['data_inicio']) && !empty($filtros['data_fim'])) {
                $sql .= " AND data_inicio <= :data_fim AND data_termino >= :data_inicio";
                $params[':data_inicio'] = $filtros['data_inicio'];
                $params[':data_fim'] = $filtros['data_fim'];
            }
           
            // Filtro por número mínimo de quartos
            if (!empty($filtros['quartos_min'])) {
                $sql .= " AND quartos >= :quartos_min";
                $params[':quartos_min'] = $filtros['quartos_min'];
            }
           
            // Filtro por número mínimo de banheiros
            if (!empty($filtros['banheiros_min'])) {
                $sql .= " AND banheiros >= :banheiros_min";
                $params[':banheiros_min'] = $filtros['banheiros_min'];
            }
           
            // Ordenação
            $orderBy = $filtros['order_by'] ?? 'id';
            $orderDir = $filtros['order_dir'] ?? 'DESC';
            $sql .= " ORDER BY {$orderBy} {$orderDir}";
           
            // Limite e paginação
            if (!empty($filtros['limit'])) {
                $sql .= " LIMIT :limit";
                $params[':limit'] = (int)$filtros['limit'];
               
                if (!empty($filtros['offset'])) {
                    $sql .= " OFFSET :offset";
                    $params[':offset'] = (int)$filtros['offset'];
                }
            }
           
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
           
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
           
        } catch (\PDOException $e) {
            error_log("Erro ao buscar imóveis com filtros: " . $e->getMessage());
            return [];
        }
    }
   
    //UPDATE - Atualiza um imóvel existente
    public function update() {
        try {
            $sql = $this->conn->prepare("
                UPDATE {$this->table} SET
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
                    email_proprietario = :email_proprietario,
                    fotos = :fotos
                WHERE id = :id
            ");
           
            return $sql->execute([
                ':tipo' => $this->tipo,
                ':titulo' => $this->titulo,
                ':descricao' => $this->descricao,
                ':cidade' => $this->cidade,
                ':logradouro' => $this->logradouro,
                ':numero' => $this->numero,
                ':complemento' => $this->complemento,
                ':bairro' => $this->bairro,
                ':estado' => $this->estado,
                ':quartos' => $this->quartos,
                ':suites' => $this->suites,
                ':banheiros' => $this->banheiros,
                ':capacidade' => $this->capacidade,
                ':wifi' => $this->wifi,
                ':piscina' => $this->piscina,
                ':estacionamento' => $this->estacionamento,
                ':ar_condicionado' => $this->ar_condicionado,
                ':tv' => $this->tv,
                ':pet_friendly' => $this->pet_friendly,
                ':cozinha' => $this->cozinha,
                ':area_trabalho' => $this->area_trabalho,
                ':cafe_manha' => $this->cafe_manha,
                ':maquina_lavar' => $this->maquina_lavar,
                ':valor' => $this->valor,
                ':tipo_preco' => $this->tipo_preco,
                ':data_inicio' => $this->data_inicio,
                ':data_termino' => $this->data_termino,
                ':whatsapp' => $this->whatsapp,
                ':email_proprietario' => $this->email_proprietario,
                ':fotos' => $this->fotos,
                ':id' => $this->id
            ]);
           
        } catch (\PDOException $e) {
            error_log("Erro ao atualizar imóvel: " . $e->getMessage());
            return false;
        }
    }
   
    //DELETE - Exclui um imóvel
    public function delete() {
        try {
            $sql = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :id");
            return $sql->execute([':id' => $this->id]);
        } catch (\PDOException $e) {
            error_log("Erro ao excluir imóvel: " . $e->getMessage());
            return false;
        }
    }
   
   
    //Conta total de imóveis
    public function count() {
        try {
            $sql = $this->conn->query("SELECT COUNT(*) as total FROM {$this->table}");
            $result = $sql->fetch(\PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (\PDOException $e) {
            error_log("Erro ao contar imóveis: " . $e->getMessage());
            return 0;
        }
    }
   
   
    //Busca imóveis por tipo
    public function findByTipo($tipo) {
        try {
            $sql = $this->conn->prepare("SELECT * FROM {$this->table} WHERE tipo = :tipo ORDER BY id DESC");
            $sql->execute([':tipo' => $tipo]);
            return $sql->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erro ao buscar imóveis por tipo: " . $e->getMessage());
            return [];
        }
    }
   
   
    //Busca imóveis disponíveis em um período
    public function findDisponiveis($dataInicio, $dataFim) {
        try {
            $sql = $this->conn->prepare("
                SELECT * FROM {$this->table}
                WHERE data_inicio <= :dataFim
                AND data_termino >= :dataInicio
                ORDER BY id DESC
            ");
           
            $sql->execute([
                ':dataInicio' => $dataInicio,
                ':dataFim' => $dataFim
            ]);
           
            return $sql->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erro ao buscar imóveis disponíveis: " . $e->getMessage());
            return [];
        }
    }
   
   
    //Busca imóveis por cidade
    public function findByCidade($cidade) {
        try {
            $sql = $this->conn->prepare("SELECT * FROM {$this->table} WHERE cidade LIKE :cidade ORDER BY id DESC");
            $sql->execute([':cidade' => "%{$cidade}%"]);
            return $sql->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erro ao buscar imóveis por cidade: " . $e->getMessage());
            return [];
        }
    }
   
    //Busca imóveis por estado
    public function findByEstado($estado) {
        try {
            $sql = $this->conn->prepare("SELECT * FROM {$this->table} WHERE estado = :estado ORDER BY id DESC");
            $sql->execute([':estado' => $estado]);
            return $sql->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erro ao buscar imóveis por estado: " . $e->getMessage());
            return [];
        }
    }
   
    //Busca imóveis por faixa de preço
    public function findByFaixaPreco($valorMin, $valorMax) {
        try {
            $sql = $this->conn->prepare("
                SELECT * FROM {$this->table}
                WHERE valor >= :valorMin AND valor <= :valorMax
                ORDER BY valor ASC
            ");
           
            $sql->execute([
                ':valorMin' => $valorMin,
                ':valorMax' => $valorMax
            ]);
           
            return $sql->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Erro ao buscar imóveis por faixa de preço: " . $e->getMessage());
            return [];
        }
    }


    //Hidrata o objeto com dados de um array
    private function hydrate($data) {
        $this->id = $data['id'] ?? null;
        $this->tipo = $data['tipo'] ?? null;
        $this->titulo = $data['titulo'] ?? null;
        $this->descricao = $data['descricao'] ?? null;
        $this->cidade = $data['cidade'] ?? null;
        $this->logradouro = $data['logradouro'] ?? null;
        $this->numero = $data['numero'] ?? null;
        $this->complemento = $data['complemento'] ?? null;
        $this->bairro = $data['bairro'] ?? null;
        $this->estado = $data['estado'] ?? null;
        $this->quartos = $data['quartos'] ?? null;
        $this->suites = $data['suites'] ?? null;
        $this->banheiros = $data['banheiros'] ?? null;
        $this->capacidade = $data['capacidade'] ?? null;
        $this->wifi = $data['wifi'] ?? 0;
        $this->ar_condicionado = $data['ar_condicionado'] ?? 0;
        $this->estacionamento = $data['estacionamento'] ?? 0;
        $this->pet_friendly = $data['pet_friendly'] ?? 0;
        $this->piscina = $data['piscina'] ?? 0;
        $this->cozinha = $data['cozinha'] ?? 0;
        $this->tv = $data['tv'] ?? 0;
        $this->area_trabalho = $data['area_trabalho'] ?? 0;
        $this->cafe_manha = $data['cafe_manha'] ?? 0;
        $this->maquina_lavar = $data['maquina_lavar'] ?? 0;
        $this->valor = $data['valor'] ?? null;
        $this->tipo_preco = $data['tipo_preco'] ?? null;
        $this->data_inicio = $data['data_inicio'] ?? null;
        $this->data_termino = $data['data_termino'] ?? null;
        $this->whatsapp = $data['whatsapp'] ?? null;
        $this->email_proprietario = $data['email_proprietario'] ?? null;
        $this->fotos = $data['fotos'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }
   


    //Retorna todas as fotos como array
    public function getFotosArray() {
        $fotos = json_decode($this->fotos, true);
       
        if (!is_array($fotos)) {
            return [];
        }
       
        return array_map(function($foto) {
            return "../uploads/" . $foto;
        }, $fotos);
    }
   
    //Retorna a primeira foto ou uma imagem padrão
    public function getPrimeiraFoto() {
        $fotos = json_decode($this->fotos, true);
       
        if (is_array($fotos) && count($fotos) > 0) {
            return "../uploads/" . $fotos[0];
        }
       
        return "../Imagens/sem-foto.png";
    }
   
    //Valida os dados do imóvel antes de salvar
    public function validate() {
        $erros = [];
       
        // Validações obrigatórias
        if (empty($this->tipo)) {
            $erros[] = "O tipo de imóvel é obrigatório";
        }
       
        if (empty($this->titulo)) {
            $erros[] = "O título é obrigatório";
        }
       
        if (empty($this->descricao)) {
            $erros[] = "A descrição é obrigatória";
        }
       
        if (empty($this->cidade)) {
            $erros[] = "A cidade é obrigatória";
        }
       
        if (empty($this->logradouro)) {
            $erros[] = "O logradouro é obrigatório";
        }
       
        if (empty($this->numero)) {
            $erros[] = "O número é obrigatório";
        }
       
        if (empty($this->bairro)) {
            $erros[] = "O bairro é obrigatório";
        }
       
        if (empty($this->estado)) {
            $erros[] = "O estado é obrigatório";
        }
       
        if (empty($this->quartos) || $this->quartos < 1) {
            $erros[] = "O número de quartos deve ser no mínimo 1";
        }
       
        if (empty($this->banheiros) || $this->banheiros < 1) {
            $erros[] = "O número de banheiros deve ser no mínimo 1";
        }
       
        if (empty($this->capacidade) || $this->capacidade < 1) {
            $erros[] = "A capacidade deve ser no mínimo 1";
        }
       
        if (empty($this->valor) || $this->valor <= 0) {
            $erros[] = "O valor deve ser maior que zero";
        }
       
        if (empty($this->tipo_preco)) {
            $erros[] = "O tipo de preço é obrigatório";
        }
       
        if (empty($this->data_inicio)) {
            $erros[] = "A data de início é obrigatória";
        }
       
        if (empty($this->data_termino)) {
            $erros[] = "A data de término é obrigatória";
        }
       
        if (empty($this->whatsapp)) {
            $erros[] = "O WhatsApp é obrigatório";
        }
       
        // Validação de datas
        if (!empty($this->data_inicio) && !empty($this->data_termino)) {
            if (strtotime($this->data_inicio) > strtotime($this->data_termino)) {
                $erros[] = "A data de término deve ser posterior à data de início";
            }
        }
       
        return $erros;
    }
   
    //Converte o objeto em array
    public function toArray() {
        return [
            'id' => $this->id,
            'tipo' => $this->tipo,
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'cidade' => $this->cidade,
            'logradouro' => $this->logradouro,
            'numero' => $this->numero,
            'complemento' => $this->complemento,
            'bairro' => $this->bairro,
            'estado' => $this->estado,
            'quartos' => $this->quartos,
            'suites' => $this->suites,
            'banheiros' => $this->banheiros,
            'capacidade' => $this->capacidade,
            'wifi' => $this->wifi,
            'ar_condicionado' => $this->ar_condicionado,
            'estacionamento' => $this->estacionamento,
            'pet_friendly' => $this->pet_friendly,
            'piscina' => $this->piscina,
            'cozinha' => $this->cozinha,
            'tv' => $this->tv,
            'area_trabalho' => $this->area_trabalho,
            'cafe_manha' => $this->cafe_manha,
            'maquina_lavar' => $this->maquina_lavar,
            'valor' => $this->valor,
            'tipo_preco' => $this->tipo_preco,
            'data_inicio' => $this->data_inicio,
            'data_termino' => $this->data_termino,
            'whatsapp' => $this->whatsapp,
            'email_proprietario' => $this->email_proprietario,
            'fotos' => $this->fotos,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
