<?php

namespace App\Models;

use CodeIgniter\Model;


class PedidoProdutosModel extends Model
{
    protected $table = "pedido_produto";
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_pedido', 'id_produto', 'qtde_produto'];
}
