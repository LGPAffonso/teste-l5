<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

helper('helpers');

class Produtos extends ResourceController
{

    private $produtosModel;
    private $token = 'teste';

    public function __construct()
    {
        $this->produtosModel = new \App\Models\ProdutosModel();
    }

    private function _validaToken()
    {
        if ($this->request->getHeaderLine('token') == $this->token) {
            $resp = true;
        } else {
            $resp = false;
        }
        return $resp;
    }

    public function list()
    {
        $data = $this->produtosModel->findAll();

        $result = responseHelper(200, 'Produtos retornados com sucesso', $data);

        return $this->response->setJSON($result);
    }

    public function save()
    {
        if (!$this->_validaToken()) {
            $result = responseHelper(498, 'token inválido');
        } else {
            $requisicao = $this->request->getJSON()->parametros;

            $produto['nome'] = $requisicao->nome;
            $produto['tipo'] = $requisicao->tipo;
            $produto['qtde'] = $requisicao->qtde;

            $produto['valor'] = floatval(convert_valor($requisicao->valor));
            $produto['descricao'] = $requisicao->descricao;

            try {
                if ($this->produtosModel->insert($produto)) {

                    $result = responseHelper(200, 'Produto cadastrado com sucesso.', $requisicao);
                } else {
                    $result = responseHelper(500,  $this->produtosModel->errors(), $requisicao);
                }
            } catch (\Throwable $th) {
                $result = responseHelper(500, 'erro ao cadastrar produto. ' . $th->getMessage(), $requisicao);
            }
        }
        return $this->response->setJSON($result);
    }

    public function prodUpdate()
    {
        if (!$this->_validaToken()) {
            $result = responseHelper(498, 'token inválido');
        } else {
            $requisicao = $this->request->getJSON()->parametros;
            $id = $requisicao->id;
            if ($this->produtosModel->where('nome', $requisicao->nome)->whereNotIn('id', [$id])->findAll()) {
                $produto['nome'] = $requisicao->nome;
            }
            $produto['tipo'] = $requisicao->tipo;
            $produto['qtde'] = $requisicao->qtde;

            $produto['valor'] = floatval(convert_valor($requisicao->valor));
            $produto['descricao'] = $requisicao->descricao;
            try {
                if ($this->produtosModel->where('id', $id)->set($produto)->update()) {
                    $result = responseHelper(200, 'Produto alterado com sucesso.', $requisicao);
                } else {
                    $result = responseHelper(500,  $this->produtosModel->errors(), $requisicao);
                }
            } catch (\Throwable $th) {
                $result = responseHelper(500, 'erro ao atualizar produto. ' . $th->getMessage(), $requisicao);
                return $this->response->setJSON($result);
            }
        }
        return $this->response->setJSON($result);
    }

    public function prodDelete()
    {
        if (!$this->_validaToken()) {
            $result = responseHelper(498, 'token inválido');
        } else {
            $requisicao = $this->request->getJSON()->parametros;
            $id = $requisicao->id;
            try {
                if ($this->produtosModel->where('id', $id)
                    ->set(['excluido' => 1])
                    ->update()
                ) {

                    $result = responseHelper(200, 'Produto excluido com sucesso.', $requisicao);
                } else {
                    $result = responseHelper(500,  $this->produtosModel->errors(), $requisicao);
                }
            } catch (\Throwable $th) {
                $result = responseHelper(500, 'erro ao excluir produto. ' . $th->getMessage(), $requisicao);
                return $this->response->setJSON($result);
            }
        }
        return $this->response->setJSON($result);
    }
}
