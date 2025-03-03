<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ProdutosModel;
use CodeIgniter\RESTful\ResourceController;

class ProdutosController extends ResourceController
{
    protected $modelName = 'App\Models\ProdutosModel';
    protected $format    = 'json';

    // Criar um novo produto
    public function create(){
        $data = $this->request->getJSON();

        if (!$data || !isset($data->parametros)) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 400,
                    'mensagem' => 'JSON inválido ou campo "parametros" ausente.'
                ],
                'retorno' => new \stdClass()
            ], 400);
        }

        $parametros = $data->parametros;

        $validation = \Config\Services::validation();

        $validation->setRules([
            'descricao' => 'required|min_length[3]|max_length[255]',
            'preco' => 'required|decimal'
        ]);

        if (!$validation->run((array)$parametros)) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 422,
                    'mensagem' => 'Erro de validação.'
                ],
                'retorno' => $validation->getErrors()
            ], 422);
        }

        $produto = [
            'descricao' => $parametros->descricao,
            'preco' => $parametros->preco
        ];

        $this->model->insert($produto);

        return $this->respond([
            'cabecalho' => [
                'status' => 201,
                'mensagem' => 'Produto criado com sucesso.'
            ],
            'retorno' => $produto
        ], 201);
    }

    // Listar todos os produtos
    public function index(){
        $produtos = $this->model->findAll();
        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Produtos encontrados.'
            ],
            'retorno' => $produtos
        ], 200);
    }

    // Mostrar um produto específico
    public function show($id = null){
        $produto = $this->model->find($id);
        if (!$produto) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Produto não encontrado.'
                ],
                'retorno' => new \stdClass()
            ], 404);
        }

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Produto encontrado.'
            ],
            'retorno' => $produto
        ], 200);
    }

    // Atualizar um produto
    public function update($id = null){
        $produto = $this->model->find($id);
        if (!$produto) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Produto não encontrado.'
                ],
                'retorno' => new \stdClass()
            ], 404);
        }

        $data = $this->request->getJSON();

        if (!$data || !isset($data->parametros)) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 400,
                    'mensagem' => 'JSON inválido ou campo "parametros" ausente.'
                ],
                'retorno' => new \stdClass()
            ], 400);
        }

        $parametros = $data->parametros;

        $validation = \Config\Services::validation();

        $validation->setRules([
            'descricao' => 'required|min_length[3]|max_length[255]',
            'preco' => 'required|decimal'
        ]);

        if (!$validation->run((array)$parametros)) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 422,
                    'mensagem' => 'Erro de validação.'
                ],
                'retorno' => $validation->getErrors()
            ], 422);
        }

        $produto = [
            'descricao' => $parametros->descricao,
            'preco' => $parametros->preco
        ];

        $this->model->update($id, $produto);

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Produto atualizado com sucesso.'
            ],
            'retorno' => $produto
        ], 200);
    }

    // Deletar um produto
    public function delete($id = null){
        $produto = $this->model->find($id);
        if (!$produto) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Produto não encontrado.'
                ],
                'retorno' => new \stdClass()
            ], 404);
        }

        $this->model->delete($id);
        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Produto deletado com sucesso.'
            ],
            'retorno' => $produto
        ], 200);
    }
}