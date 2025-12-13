<?php

namespace App\Controllers;


use App\Models\Imovel;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


class ImovelController
{
    private $imovelModel;
    private $uploadDir;


    public function __construct()
    {
        $this->imovelModel = new Imovel();
        $this->uploadDir = __DIR__ . "./uploads/";
       
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }


    //Lista imóveis (versão pública)
    //GET /imoveis
    public function index()
    {
        $filtros = [
            'localizacao' => $_GET['localizacao'] ?? '',
            'hospedes' => $_GET['hospedes'] ?? '',
            'tipo' => $_GET['tipo'] ?? '',
            'estado' => $_GET['estado'] ?? ''
        ];
       
        $facilidades = [
            'wifi', 'ar_condicionado', 'estacionamento', 'pet_friendly',
            'piscina', 'cozinha', 'tv', 'area_trabalho', 'cafe_manha', 'maquina_lavar'
        ];
       
        foreach ($facilidades as $fac) {
            if (isset($_GET[$fac])) {
                $filtros[$fac] = 1;
            }
        }
       
        $imoveis = $this->imovelModel->readWithFilters($filtros);
       
        foreach ($imoveis as &$imovel) {
            $imovel['foto_principal'] = $this->getPrimeiraFoto($imovel['fotos']);
        }
       
        $loader = new FilesystemLoader(__DIR__ . "/../Views");
        $twig = new Environment($loader);


        echo $twig->render('user/listar_imoveis.html.twig', [
            'imoveis' => $imoveis,
            'total' => count($imoveis),
            'filtros' => $filtros
        ]);
    }


    //Exibe detalhes de um imóvel
    //GET /imoveis/$id
    public function detalhes($id)
    {
        $imovel = $this->imovelModel->readOne($id);
       
        if (!$imovel) {
            $this->redirect('/imoveis');
            return;
        }


        $fotos = $this->getAllFotos($imovel->fotos);


        $this->render('user/detalhes_imovel.html.twig', [
            'imovel' => $imovel,
            'fotos' => $fotos
        ]);
    }


    //Lista imóveis (versão admin)
    //GET /imoveis/admin
    public function admin()
    {
        if (!$this->verificarLogin()) {
            $this->redirect('/login');
            return;
        }


        $mensagem = $_SESSION['mensagem'] ?? '';
        $erro = $_SESSION['erro'] ?? '';
       
        unset($_SESSION['mensagem'], $_SESSION['erro']);


        $imoveis = $this->imovelModel->readAll();
       
        foreach ($imoveis as &$imovel) {
            $imovel['foto_principal'] = $this->getPrimeiraFoto($imovel['fotos']);
        }
       
        $this->render('user/listar_imoveis_admin.html.twig', [
            'imoveis' => $imoveis,
            'total' => count($imoveis),
            'mensagem' => $mensagem,
            'erro' => $erro
        ]);
    }


    //Exibe formulário de cadastro
    //GET /imoveis/create
    public function cadastrar()
    {
        if (!$this->verificarLogin()) {
            $this->redirect('/login');
            return;
        }


        $mensagem = $_SESSION['mensagem'] ?? '';
        $erro = $_SESSION['erro'] ?? '';
       
        unset($_SESSION['mensagem'], $_SESSION['erro']);


        $this->render('user/cadastrar_imovel.html.twig', [
            'mensagem' => $mensagem,
            'erro' => $erro
        ]);
    }


    //Processa cadastro de imóvel
    //POST /imoveis/create
    public function store()
    {
        if (!$this->verificarLogin()) {
            $this->redirect('/login');
            return;
        }


        try {
            $this->imovelModel->tipo = $_POST['propertyType'];
            $this->imovelModel->titulo = $_POST['title'];
            $this->imovelModel->descricao = $_POST['description'];
            $this->imovelModel->cidade = $_POST['cidade'];
            $this->imovelModel->logradouro = $_POST['logradouro'];
            $this->imovelModel->numero = $_POST['numero'];
            $this->imovelModel->complemento = $_POST['complemento'] ?? '';
            $this->imovelModel->bairro = $_POST['bairro'];
            $this->imovelModel->estado = $_POST['estado'];
            $this->imovelModel->quartos = $_POST['quartos'];
            $this->imovelModel->suites = $_POST['suites'] ?? 0;
            $this->imovelModel->banheiros = $_POST['banheiros'];
            $this->imovelModel->capacidade = $_POST['capacidade'];
            $this->imovelModel->wifi = isset($_POST['wifi']) ? 1 : 0;
            $this->imovelModel->ar_condicionado = isset($_POST['ar_condicionado']) ? 1 : 0;
            $this->imovelModel->estacionamento = isset($_POST['estacionamento']) ? 1 : 0;
            $this->imovelModel->pet_friendly = isset($_POST['pet_friendly']) ? 1 : 0;
            $this->imovelModel->piscina = isset($_POST['piscina']) ? 1 : 0;
            $this->imovelModel->cozinha = isset($_POST['cozinha']) ? 1 : 0;
            $this->imovelModel->tv = isset($_POST['tv']) ? 1 : 0;
            $this->imovelModel->area_trabalho = isset($_POST['area_trabalho']) ? 1 : 0;
            $this->imovelModel->cafe_manha = isset($_POST['cafe_manha']) ? 1 : 0;
            $this->imovelModel->maquina_lavar = isset($_POST['maquina_lavar']) ? 1 : 0;
            $this->imovelModel->valor = $_POST['valor'];
            $this->imovelModel->tipo_preco = $_POST['tipo_preco'];
            $this->imovelModel->data_inicio = $_POST['data_inicio'];
            $this->imovelModel->data_termino = $_POST['data_termino'];
            $this->imovelModel->whatsapp = $_POST['whatsapp'];
            $this->imovelModel->email_proprietario = $_POST['email_proprietario'] ?? '';
            $this->imovelModel->fotos = $this->processarUploadFotos($_FILES);
           
            $erros = $this->imovelModel->validate();
           
            if (!empty($erros)) {
                $_SESSION['erro'] = implode('<br>', $erros);
                $this->redirect('/imoveis/create');
                return;
            }
           
            $id = $this->imovelModel->create();
           
            if ($id) {
                $_SESSION['mensagem'] = 'Imóvel cadastrado com sucesso!';
                $this->redirect('/imoveis/admin');
            } else {
                $_SESSION['erro'] = 'Erro ao cadastrar imóvel.';
                $this->redirect('/imoveis/create');
            }
           
        } catch (\Exception $e) {
            $_SESSION['erro'] = 'Erro: ' . $e->getMessage();
            $this->redirect('/imoveis/create');
        }
    }


    //Exibe formulário de edição
    //GET /imoveis/$id/update
    public function editar($id)
    {
        if (!$this->verificarLogin()) {
            $this->redirect('/login');
            return;
        }


        $imovel = $this->imovelModel->readOne($id);
       
        if (!$imovel) {
            $_SESSION['erro'] = 'Imóvel não encontrado.';
            $this->redirect('/imoveis/admin');
            return;
        }


        $mensagem = $_SESSION['mensagem'] ?? '';
        $erro = $_SESSION['erro'] ?? '';
       
        unset($_SESSION['mensagem'], $_SESSION['erro']);


        $this->render('user/edit.html.twig', [
            'imovel' => $imovel,
            'mensagem' => $mensagem,
            'erro' => $erro
        ]);
    }


    //Processa atualização do imóvel
    //POST /imoveis/$id/update
    public function update($id)
    {
        if (!$this->verificarLogin()) {
            $this->redirect('/login');
            return;
        }


        try {
            $imovel = $this->imovelModel->readOne($id);
           
            if (!$imovel) {
                $_SESSION['erro'] = 'Imóvel não encontrado.';
                $this->redirect('/imoveis/admin');
                return;
            }


            $this->imovelModel->tipo = $_POST['propertyType'];
            $this->imovelModel->titulo = $_POST['title'];
            $this->imovelModel->descricao = $_POST['description'];
            $this->imovelModel->cidade = $_POST['cidade'];
            $this->imovelModel->logradouro = $_POST['logradouro'];
            $this->imovelModel->numero = $_POST['numero'];
            $this->imovelModel->complemento = $_POST['complemento'] ?? '';
            $this->imovelModel->bairro = $_POST['bairro'];
            $this->imovelModel->estado = $_POST['estado'];
            $this->imovelModel->quartos = $_POST['quartos'];
            $this->imovelModel->suites = $_POST['suites'] ?? 0;
            $this->imovelModel->banheiros = $_POST['banheiros'];
            $this->imovelModel->capacidade = $_POST['capacidade'];
            $this->imovelModel->wifi = isset($_POST['wifi']) ? 1 : 0;
            $this->imovelModel->ar_condicionado = isset($_POST['ar_condicionado']) ? 1 : 0;
            $this->imovelModel->estacionamento = isset($_POST['estacionamento']) ? 1 : 0;
            $this->imovelModel->pet_friendly = isset($_POST['pet_friendly']) ? 1 : 0;
            $this->imovelModel->piscina = isset($_POST['piscina']) ? 1 : 0;
            $this->imovelModel->cozinha = isset($_POST['cozinha']) ? 1 : 0;
            $this->imovelModel->tv = isset($_POST['tv']) ? 1 : 0;
            $this->imovelModel->area_trabalho = isset($_POST['area_trabalho']) ? 1 : 0;
            $this->imovelModel->cafe_manha = isset($_POST['cafe_manha']) ? 1 : 0;
            $this->imovelModel->maquina_lavar = isset($_POST['maquina_lavar']) ? 1 : 0;
            $this->imovelModel->valor = $_POST['valor'];
            $this->imovelModel->tipo_preco = $_POST['tipo_preco'];
            $this->imovelModel->data_inicio = $_POST['data_inicio'];
            $this->imovelModel->data_termino = $_POST['data_termino'];
            $this->imovelModel->whatsapp = $_POST['whatsapp'];
            $this->imovelModel->email_proprietario = $_POST['email_proprietario'] ?? '';
           
            $fotosAntigas = json_decode($imovel->fotos, true) ?: [];
           
            if (!empty($_FILES['fotos']['name'][0])) {
                $novasFotos = $this->processarUploadFotosArray($_FILES);
                $fotosAntigas = array_merge($fotosAntigas, $novasFotos);
            }
           
            $this->imovelModel->fotos = json_encode($fotosAntigas);
           
            if ($this->imovelModel->update()) {
                $_SESSION['mensagem'] = 'Imóvel atualizado com sucesso!';
                $this->redirect('/imoveis/admin');
            } else {
                $_SESSION['erro'] = 'Erro ao atualizar imóvel.';
                $this->redirect('/imoveis/' . $id . '/update');
            }
           
        } catch (\Exception $e) {
            $_SESSION['erro'] = 'Erro: ' . $e->getMessage();
            $this->redirect('/imoveis/' . $id . '/update');
        }
    }


   
    //Confirma exclusão (exibe view de confirmação)
    //GET /imoveis/$id/delete
    public function confirmDelete($id)
    {
        if (!$this->verificarLogin()) {
            $this->redirect('/login');
            return;
        }


        $imovel = $this->imovelModel->readOne($id);
       
        if (!$imovel) {
            $_SESSION['erro'] = 'Imóvel não encontrado.';
            $this->redirect('/imoveis/admin');
            return;
        }


        $this->render('user/delete.html.twig', [
            'imovel' => $imovel
        ]);
    }


    //Processa exclusão
    //POST /imoveis/$id/delete
    public function deletar($id)
    {
        if (!$this->verificarLogin()) {
            $this->redirect('/login');
            return;
        }


        try {
            $imovel = $this->imovelModel->readOne($id);
           
            if (!$imovel) {
                $_SESSION['erro'] = 'Imóvel não encontrado.';
                $this->redirect('/imoveis/admin');
                return;
            }


            if (!empty($imovel->fotos)) {
                $fotos = json_decode($imovel->fotos, true);
               
                if (is_array($fotos)) {
                    foreach ($fotos as $foto) {
                        $arquivo = $this->uploadDir . $foto;
                        if (file_exists($arquivo)) {
                            unlink($arquivo);
                        }
                    }
                }
            }
           
            $this->imovelModel->id = $id;
           
            if ($this->imovelModel->delete()) {
                $_SESSION['mensagem'] = 'Imóvel excluído com sucesso!';
            } else {
                $_SESSION['erro'] = 'Erro ao excluir imóvel.';
            }
           
        } catch (\Exception $e) {
            $_SESSION['erro'] = 'Erro: ' . $e->getMessage();
        }


        $this->redirect('/imoveis/admin');
    }


    // MÉTODOS AUXILIARES PRIVADOS
    private function processarUploadFotos($arquivos)
    {
        $listaFotos = [];
       
        if (!empty($arquivos['fotos']['name'][0])) {
            foreach ($arquivos['fotos']['name'] as $i => $nomeFoto) {
                $tmp = $arquivos['fotos']['tmp_name'][$i];
                $novoNome = uniqid() . "_" . basename($nomeFoto);
               
                if (move_uploaded_file($tmp, $this->uploadDir . $novoNome)) {
                    $listaFotos[] = $novoNome;
                }
            }
        }
       
        return json_encode($listaFotos);
    }


    private function processarUploadFotosArray($arquivos)
    {
        $listaFotos = [];
       
        if (!empty($arquivos['fotos']['name'][0])) {
            foreach ($arquivos['fotos']['name'] as $i => $nomeFoto) {
                $tmp = $arquivos['fotos']['tmp_name'][$i];
                $novoNome = uniqid() . "_" . basename($nomeFoto);
               
                if (move_uploaded_file($tmp, $this->uploadDir . $novoNome)) {
                    $listaFotos[] = $novoNome;
                }
            }
        }
       
        return $listaFotos;
    }


    private function getPrimeiraFoto($fotosJSON)
    {
        $fotos = json_decode($fotosJSON, true);
       
        if (is_array($fotos) && count($fotos) > 0) {
            return "/uploads/" . $fotos[0];
        }
       
        return "/assets/imagens/sem-foto.png";
    }


    private function getAllFotos($fotosJSON)
    {
        $fotos = json_decode($fotosJSON, true);
       
        if (!is_array($fotos)) {
            return [];
        }
       
        return array_map(function($foto) {
            return "/uploads/" . $foto;
        }, $fotos);
    }


    private function verificarLogin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['usuario_id']);
    }


    private function redirect($url)
    {
        header("Location: " . $url);
        exit;
    }
}

