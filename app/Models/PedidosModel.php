<?php

namespace App\Models;

use CodeIgniter\Model;


class PedidosModel extends Model
{
    protected $table = "pedidos";
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_cliente', 'total', 'excluido', 'stt_ped'];
}
