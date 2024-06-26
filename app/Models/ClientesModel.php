<?php

namespace App\Models;

use CodeIgniter\Model;


class ClientesModel extends Model
{
    protected $table = "clientes";
    protected $primaryKey = 'id';
    protected $allowedFields = ['nome/razao_social', 'cpf/cnpj', 'tipo', 'excluido'];
    protected $validationRules = ['cpf/cnpj' => 'is_unique[clientes.cpf/cnpj]'];
    protected $validationMessages = ['cpf/cnpj' => ['is_unique' => 'já cadastrado',],];
}
