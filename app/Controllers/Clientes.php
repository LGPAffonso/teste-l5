<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

helper('helpers');

class Clientes extends ResourceController
{

    private $clienteModel;
    private $token = 'teste';

    public function __construct()
    {
        $this->clienteModel = new \App\Models\ClientesModel();
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
        $data = $this->clienteModel->findAll();

        $result = responseHelper(200, 'clientes retornados com sucesso', $data);

        return $this->response->setJSON($result);
    }

    public function save()
    {
        if (!$this->_validaToken()) {
            $result = responseHelper(498, 'token inválido');
        } else {
            $requisicao = $this->request->getJSON()->parametros;

            $cliente['nome/razao_social'] = $requisicao->nome;
            $cliente['tipo'] = $requisicao->tipo;
            if ($cliente['tipo'] == 1 && valid_cpf($requisicao->documento)) {
                $cliente['cpf/cnpj'] = $requisicao->documento;
            } else if ($cliente['tipo'] == 2 && valid_cnpj($requisicao->documento)) {
                $cliente['cpf/cnpj'] = $requisicao->documento;
            } else {
                $result = responseHelper(200, 'CPF/CNPJ informado é inválido.', $requisicao);
                return $this->response->setJSON($result);
            }

            try {
                if ($this->clienteModel->insert($cliente)) {

                    $result = responseHelper(200, 'Cliente cadastrado com sucesso.', $requisicao);
                } else {
                    $result = responseHelper(500,  $this->clienteModel->errors(), $requisicao);
                }
            } catch (\Throwable $th) {
                $result = responseHelper(500, 'erro ao cadastrar cliente. ' . $th->getMessage(), $requisicao);
            }
        }
        return $this->response->setJSON($result);
    }

    public function cliUpdate()
    {
        if (!$this->_validaToken()) {
            $result = responseHelper(498, 'token inválido');
        } else {
            $requisicao = $this->request->getJSON()->parametros;

            $cliente['nome/razao_social'] = $requisicao->nome;
            $id = $requisicao->id;
            $tipo = $requisicao->tipo;
            try {
                if ($this->clienteModel->where('cpf/cnpj', $requisicao->documento)->whereNotIn('id', [$id])->findAll()) {
                    if ($tipo == 1 && valid_cpf($requisicao->documento)) {
                        $cliente['tipo'] = $requisicao->tipo;
                        $cliente['cpf/cnpj'] = $requisicao->documento;
                    } else if ($tipo == 2 && valid_cnpj($requisicao->documento)) {
                        $cliente['tipo'] = $requisicao->tipo;
                        $cliente['cpf/cnpj'] = $requisicao->documento;
                    } else {
                        $result = responseHelper(200, 'CPF/CNPJ informado é inválido.', $requisicao);
                        return $this->response->setJSON($result);
                    }
                }
            } catch (\Throwable $th) {
                $result = responseHelper(500, 'erro ao atualizar cliente. ' . $th->getMessage(), $requisicao);
                return $this->response->setJSON($result);
            }

            try {
                if ($this->clienteModel->where('id', $id)
                    ->set($cliente)
                    ->update()
                ) {

                    $result = responseHelper(200, 'Cliente alterado com sucesso.', $requisicao);
                } else {
                    $result = responseHelper(500,  $this->clienteModel->errors(), $requisicao);
                }
            } catch (\Throwable $th) {
                $result = responseHelper(500, 'erro ao atualizar cliente. ' . $th->getMessage(), $requisicao);
                return $this->response->setJSON($result);
            }
        }
        return $this->response->setJSON($result);
    }

    public function cliDelete()
    {
        if (!$this->_validaToken()) {
            $result = responseHelper(498, 'token inválido');
        } else {
            $requisicao = $this->request->getJSON()->parametros;
            $id = $requisicao->id;
            try {
                if ($this->clienteModel->where('id', $id)
                    ->set(['excluido' => 1])
                    ->update()
                ) {

                    $result = responseHelper(200, 'Cliente excluido com sucesso.', $requisicao);
                } else {
                    $result = responseHelper(500,  $this->clienteModel->errors(), $requisicao);
                }
            } catch (\Throwable $th) {
                $result = responseHelper(500, 'erro ao excluir cliente. ' . $th->getMessage(), $requisicao);
                return $this->response->setJSON($result);
            }
        }
        return $this->response->setJSON($result);
    }
}
