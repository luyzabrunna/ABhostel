<?php

//Rotas do sistema usando PHPRouter
use App\Controllers\AppController;
use App\Controllers\ImovelController;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/router.php';

// ROTAS DO SISTEMA

// PÁGINA INICIAL
get('/', function () {
  $controller = new AppController();
  $controller->index();
});

// ROTAS DE IMÓVEIS - PÚBLICAS

// Listar imóveis (público)
get('/imoveis', function () {
  $controller = new ImovelController();
  $controller->index();
});

// ROTAS DE IMÓVEIS - ADMIN


// Listar imóveis (admin)
get('/imoveis/admin', function () {
  $controller = new ImovelController();
  $controller->admin();
});

// Exibir formulário de cadastro
get('/imoveis/create', function () {
  $controller = new ImovelController();
  $controller->cadastrar();
});

// Processar cadastro
post('/imoveis/create', function () {
  $controller = new ImovelController();
  $controller->store();
});

// Exibir formulário de edição
get('/imoveis/$id/update', function ($id) {
  $controller = new ImovelController();
  $controller->editar($id);
});

// Processar atualização
post('/imoveis/$id/update', function ($id) {
  $controller = new ImovelController();
  $controller->update($id);
});

// Confirmar exclusão
get('/imoveis/$id/delete', function ($id) {
  $controller = new ImovelController();
  $controller->confirmDelete($id);
});

// Processar exclusão
post('/imoveis/$id/delete', function ($id) {
  $controller = new ImovelController();
  $controller->deletar($id);
});

// ROTA 404
any('/404', 'views/404.php');
