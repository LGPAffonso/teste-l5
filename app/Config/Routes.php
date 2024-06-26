<?php

use App\Controllers\Clientes;
use App\Controllers\Produtos;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

////Clientes

$routes->get('/cliente/list', 'Clientes::list');
$routes->post('/cliente/save', 'Clientes::save');
$routes->post('/cliente/update', 'Clientes::cliUpdate');
$routes->post('/cliente/delete', 'Clientes::cliDelete');


////Produtos

$routes->get('/produto/list', 'Produtos::list');
$routes->post('/produto/save', 'Produtos::save');
$routes->post('/produto/update', 'Produtos::prodUpdate');
$routes->post('/produto/delete', 'Produtos::prodDelete');

////Pedido

$routes->get('/pedido/list', 'Pedidos::list');
$routes->post('/pedido/save', 'Pedidos::save');
$routes->post('/pedido/update', 'Pedidos::pedUpdate');
$routes->post('/pedido/delete', 'Pedidos::pedDelete');
