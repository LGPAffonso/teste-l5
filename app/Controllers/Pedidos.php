<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

helper('helpers');

class Pedidos extends ResourceController
{

    private $pedidosModel;
    private $pedidoProdutosModel;
    private $token = 'teste';

    public function __construct()
    {
        $this->pedidosModel = new \App\Models\PedidosModel();
        $this->pedidoProdutosModel = new \App\Models\PedidoProdutosModel();
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
        try {
            //code...
            $resp = $this->pedidosModel->findAll();

            foreach ($resp as $ped) {
                $pedidos['id_pedido'] = $ped['id'];
                $pedidos['total'] = $ped['total'];
                $pedidos['status'] = $ped['stt_ped'];
                $resp2= $this->pedidoProdutosModel->join('produtos', 'produtos.id = pedido_produto.id_produto', 'left')->where('pedido_produto.id_pedido', $ped['id'])->findAll();
                foreach ($resp2 as $pedProd) {
                    $produtos['nome'] = $pedProd['nome'];
                    $produtos['valor'] = $pedProd['valor'];
                    $produtos['qtde_produtos'] = $pedProd['qtde_produto'];
                    $produtos['id_produto'] = $pedProd['id_produto'];
                    $pedidos['produtos'][] = $produtos;
                    
                }
                $data['pedidos'][] = $pedidos;
                $pedidos=null;
            }

            $result = responseHelper(200, 'clientes retornados com sucesso', $data);
        } catch (\Throwable $th) {
            $result = responseHelper(500, 'erro', $th->getMessage() . ' ' . $th->getLine());
        }

        return $this->response->setJSON($result);
    }

    public function save()
    {
        if (!$this->_validaToken()) {
            $result = responseHelper(498, 'token inválido');
        } else {

            $requisicao = $this->request->getJSON()->parametros;
            $pedido['id_cliente'] = $requisicao->id_cliente;
            $pedido['total'] = 0;


            try {
                foreach ($requisicao->produtos as $produto) {
                    $pedido['total'] += floatval(convert_valor($produto->valor)) * intval($produto->qtde);
                }
                if ($this->pedidosModel->insert($pedido)) {
                    $idPed = $this->pedidosModel->getInsertID();
                } else {
                    $result = responseHelper(500, "erro ao cadastrar pedido. " . $this->pedidosModel->errors(), $requisicao);
                }
            } catch (\Throwable $th) {
                $result = responseHelper(500,  $th->getMessage() . $th->getLine(), $requisicao);
                return $this->response->setJSON($result);
            }

            try {
                $pedidoProduto = [];
                foreach ($requisicao->produtos as $produto) {
                    $pedidoProduto['id_pedido'] = $idPed;
                    $pedidoProduto['id_produto'] = $produto->id;
                    $pedidoProduto['qtde_produto'] = $produto->qtde;
                    if ($this->pedidoProdutosModel->insert($pedidoProduto)) {
                        $result = responseHelper(200, "Pedido realizado com sucesso. ", $$requisicao);
                    } else {
                        $result = responseHelper(500, "erro ao cadastrar pedido. " . $this->pedidosModel->errors(), $requisicao);
                    }
                }
            } catch (\Throwable $th) {
                $result = responseHelper(500,  $th->getMessage() . 'linha ' . $th->getLine(), $requisicao);
                return $this->response->setJSON($result);
            }
        }
        return $this->response->setJSON($result);
    }

    public function pedUpdate()
    {
        if (!$this->_validaToken()) {
            $result = responseHelper(498, 'token inválido');
        } else {
            $requisicao = $this->request->getJSON()->parametros;
            $pedido['id_cliente'] = $requisicao->id_cliente;
            $pedido['total'] = 0;

            if($requisicao->status == 3){
            $pedido['stt_ped']= 'Cancelado';
            }else if($requisicao->status == 2){
            $pedido['stt_ped']= 'Pago';
            }else{
            $pedido['stt_ped']= 'Em Aberto';}

            echo $requisicao->status;




            try {
                foreach ($requisicao->produtos as $produto) {
                    $pedido['total'] += floatval(convert_valor($produto->valor)) * intval($produto->qtde);
                }
                if (!$this->pedidosModel->where('id', $requisicao->id_pedido)->set($pedido)->update()) {
                    $result = responseHelper(500, "erro ao atualizar pedido. " . $this->pedidosModel->errors(), $requisicao);
                    return $this->response->setJSON($result);

                }
            } catch (\Throwable $th) {
                $result = responseHelper(500,  $th->getMessage() . $th->getLine(), $requisicao);
                return $this->response->setJSON($result);
            }

            try {
                if($this->pedidoProdutosModel->where('id_pedido', $requisicao->id_pedido)->delete()){
                    $pedidoProduto = [];
                    foreach ($requisicao->produtos as $produto) {
                        $pedidoProduto['id_pedido'] = $requisicao->id_pedido;
                        $pedidoProduto['id_produto'] = $produto->id;
                        $pedidoProduto['qtde_produto'] = $produto->qtde;
                        if ($this->pedidoProdutosModel->insert($pedidoProduto)) {
                            $result = responseHelper(200, "Pedido atualizado com sucesso. ", $requisicao);
                        } else {
                            $result = responseHelper(500, "erro ao atualizar pedido. " . $this->pedidosModel->errors(), $requisicao);
                        }
                    }

                }else{
                    $result = responseHelper(500, "erro ao atualizar pedido. " . $this->pedidosModel->errors(), $requisicao);
                }
            } catch (\Throwable $th) {
                $result = responseHelper(500,  $th->getMessage() . 'linha ' . $th->getLine(), $requisicao);
                return $this->response->setJSON($result);
            }
        }
        return $this->response->setJSON($result);
    }

    public function pedDelete()
    {
        if (!$this->_validaToken()) {
            $result = responseHelper(498, 'token inválido');
        } else {
            $requisicao = $this->request->getJSON()->parametros;
            $id = $requisicao->id;
            try {
                if ($this->pedidosModel->where('id', $id)
                    ->set(['excluido' => 1])
                    ->update()
                ) {

                    $result = responseHelper(200, 'Pedido excluido com sucesso.', $requisicao);
                } else {
                    $result = responseHelper(500,  $this->pedidosModel->errors(), $requisicao);
                }
            } catch (\Throwable $th) {
                $result = responseHelper(500, 'erro ao excluir Pedido. ' . $th->getMessage(), $requisicao);
                return $this->response->setJSON($result);
            }
        }
        return $this->response->setJSON($result);
    }
}
