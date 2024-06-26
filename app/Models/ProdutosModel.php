<?php

namespace App\Models;

use CodeIgniter\Model;


class ProdutosModel extends Model
{
    protected $table = "produtos";
    protected $primaryKey = 'id';
    protected $allowedFields = ['nome', 'descricao', 'tipo', 'excluido','valor', 'qtde'];
    protected $validationRules = ['nome' => 'is_unique[produtos.nome]'];
    protected $validationMessages = ['nome' => ['is_unique' => 'já cadastrado',],];
}
