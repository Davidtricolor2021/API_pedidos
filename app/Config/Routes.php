<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Rota inicial
$routes->get('/', 'Home::index');

// Rotas para Clientes
$routes->group('clientes', function($routes) {
    $routes->get('', 'ClientesController::index');      // Listar todos os clientes (GET /clientes)
    $routes->get('(:num)', 'ClientesController::show/$1'); // Detalhar um cliente (GET /clientes/1)
    $routes->post('', 'ClientesController::create');    // Criar um cliente (POST /clientes)
    $routes->put('(:num)', 'ClientesController::update/$1'); // Atualizar cliente (PUT /clientes/1)
    $routes->delete('(:num)', 'ClientesController::delete/$1'); // Deletar cliente (DELETE /clientes/1)
});

// Rotas para Produtos
$routes->group('produtos', function($routes) {
    $routes->get('', 'ProdutosController::index');
    $routes->get('(:num)', 'ProdutosController::show/$1');
    $routes->post('', 'ProdutosController::create');
    $routes->put('(:num)', 'ProdutosController::update/$1');
    $routes->delete('(:num)', 'ProdutosController::delete/$1');
});

// Rotas para Pedidos
$routes->group('pedidos', function($routes) {
    $routes->get('', 'PedidosController::index');
    $routes->get('(:num)', 'PedidosController::show/$1');
    $routes->post('', 'PedidosController::create');
    $routes->put('(:num)', 'PedidosController::update/$1');
    $routes->delete('(:num)', 'PedidosController::delete/$1');
});