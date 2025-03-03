<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ClientesModel;
use CodeIgniter\RESTful\ResourceController;

class ClientesController extends ResourceController
{
    protected $modelName = 'App\Models\ClientesModel';
    protected $format    = 'json';
 
    // Criar um novo cliente
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
            'cpf_cnpj' => 'required|min_length[11]|max_length[18]',
            'nome_razao_social' => 'required|min_length[3]|max_length[255]'
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

        $cliente = [
            'cpf_cnpj' => $parametros->cpf_cnpj,
            'nome_razao_social' => $parametros->nome_razao_social
        ];

        $this->model->insert($cliente);

        return $this->respond([
            'cabecalho' => [
                'status' => 201,
                'mensagem' => 'Cliente criado com sucesso.'
            ],
            'retorno' => $cliente
        ], 201);
    }
 
    // Listar todos os clientes
    public function index(){
        $clientes = $this->model->findAll();
        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Clientes encontrados.'
            ],
            'retorno' => $clientes
        ], 200);
    }

 
    // Mostrar um cliente específico
    public function show($id = null){
        $cliente = $this->model->find($id);
        if (!$cliente) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Cliente não encontrado.'
                ],
                'retorno' => new \stdClass()
            ], 404);
        }

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Cliente encontrado.'
            ],
            'retorno' => $cliente
        ], 200);
    }

    // Atualizar um cliente
    public function update($id = null){
        $cliente = $this->model->find($id);
        if (!$cliente) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Cliente não encontrado.'
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
            'cpf_cnpj' => 'required|min_length[11]|max_length[18]',
            'nome_razao_social' => 'required|min_length[3]|max_length[255]'
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

        $cliente = [
            'cpf_cnpj' => $parametros->cpf_cnpj,
            'nome_razao_social' => $parametros->nome_razao_social
        ];

        $this->model->update($id, $cliente);

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Cliente atualizado com sucesso.'
            ],
            'retorno' => $cliente
        ], 200);
    }

    // Deletar um cliente
    public function delete($id = null)
    {
        $cliente = $this->model->find($id);
        if (!$cliente) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Cliente não encontrado.'
                ],
                'retorno' => new \stdClass()
            ], 404);
        }

        $this->model->delete($id);
        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Cliente deletado com sucesso.'
            ],
            'retorno' => $cliente
        ], 200);
    }
}