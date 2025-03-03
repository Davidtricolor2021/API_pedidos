<?php

namespace App\Controllers;

use App\Models\ClientesModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\PedidosModel;
use App\Models\PedidosProdutosModel;
use App\Models\ProdutosModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Database\BaseBuilder;

class PedidosController extends ResourceController
{
    protected $modelName = 'App\Models\PedidosModel';
    protected $format    = 'json';

    // Criar um novo pedido
    public function create() {
        $pedidoModel = new PedidosModel();
        $pedidoProdutoModel = new PedidosProdutosModel();
        $produtoModel = new ProdutosModel();
        $clienteModel = new ClientesModel();

        $data = $this->request->getJSON();

        // Verifica se os dados foram passados corretamente
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

        // Validação dos campos obrigatórios
        $pedidoValidation = \Config\Services::validation();
        $pedidoValidation->setRules([
            'cliente_id' => 'required|integer',
            'produtos' => 'required'
        ]);

        if (!$pedidoValidation->run((array)$parametros) || !is_array($parametros->produtos)) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 422,
                    'mensagem' => 'Erro de validação dos dados do pedido.'
                ],
                'retorno' => $pedidoValidation->getErrors()
            ], 422);
        }

        // Verifica se o cliente existe
        $cliente = $clienteModel->find($parametros->cliente_id);
        if (!$cliente) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Cliente não encontrado.'
                ],
                'retorno' => new \stdClass()
            ], 404);
        }

        // Verifica se todos os produtos existem
        $produtosInvalidos = [];
        foreach ($parametros->produtos as $produto) {
            $produtoDetalhes = $produtoModel->find($produto->produto_id);
            if (!$produtoDetalhes) {
                $produtosInvalidos[] = $produto->produto_id;
            }
        }

        if (!empty($produtosInvalidos)) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Os seguintes produtos não foram encontrados: ' . implode(', ', $produtosInvalidos)
                ],
                'retorno' => new \stdClass()
            ], 404);
        }

        // Cria o pedido
        $pedidoData = [
            'cliente_id' => $parametros->cliente_id,
            'status'     => 'Em Aberto', // Status sempre será "Em Aberto" na criação de um pedido
        ];
        $pedidoModel->insert($pedidoData);
        $pedidoId = $pedidoModel->getInsertID();

        // Insere cada produto no pedido
        $produtosInseridos = [];
        foreach ($parametros->produtos as $produto) {
            $produtoDetalhes = $produtoModel->find($produto->produto_id);

            $produtoData = [
                'pedido_id'  => $pedidoId,
                'produto_id' => $produto->produto_id,
                'quantidade' => $produto->quantidade,
                'preco'      => $produtoDetalhes['preco']
            ];
            $pedidoProdutoModel->insert($produtoData);
            $produtosInseridos[] = $produtoData;
        }

        // Recupera o pedido completo
        $pedidoCompleto = $pedidoModel->find($pedidoId);

        // Retorna todos os dados
        return $this->respond([
            'cabecalho' => [
                'status' => 201,
                'mensagem' => 'Pedido criado com sucesso.'
            ],
            'retorno' => [
                'pedido' => $pedidoCompleto,
                'produtos' => $produtosInseridos
            ]
        ], 201);
    }                   

    // Listar todos os pedidos
    public function index() {
        $pedidoModel = new PedidosModel();
        
        $data = $pedidoModel
            ->select('pedidos.*, produtos.descricao AS descricao, pedidos_produtos.quantidade, pedidos_produtos.preco')
            ->join('pedidos_produtos', 'pedidos.id = pedidos_produtos.pedido_id')
            ->join('produtos', 'produtos.id = pedidos_produtos.produto_id')
            ->findAll();

        if (empty($data)) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Nenhum pedido encontrado.'
                ],
                'retorno' => new \stdClass()
            ], 404);
        }

        // Agrupar produtos por pedido
        $pedidos = [];
        foreach ($data as $item) {
            $pedidoId = $item['id'];

            if (!isset($pedidos[$pedidoId])) {
                $pedidos[$pedidoId] = [
                    'id' => $pedidoId,
                    'cliente_id' => $item['cliente_id'],
                    'status' => $item['status'],
                    'produtos' => []
                ];
            }

            $pedidos[$pedidoId]['produtos'][] = [
                'descricao' => $item['descricao'],
                'quantidade' => $item['quantidade'],
                'preco' => $item['preco']
            ];
        }

        $pedidos = array_values($pedidos);

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Pedidos listados com sucesso.'
            ],
            'retorno' => $pedidos
        ], 200);
    }

    // Mostrar um pedido específico
    public function show($id = null) {
        $pedidoModel = new PedidosModel();
        $pedido = $pedidoModel->find($id);

        if (!$pedido) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Pedido não encontrado.'
                ],
                'retorno' => new \stdClass()
            ], 404);
        }

        $produtos = (new PedidosProdutosModel())
            ->select('produtos.descricao AS descricao, pedidos_produtos.quantidade, pedidos_produtos.preco')
            ->join('produtos', 'produtos.id = pedidos_produtos.produto_id')
            ->where('pedido_id', $id)
            ->findAll();

        $pedido['produtos'] = array_map(function($produto) {
            return [
                'descricao' => $produto['descricao'],
                'quantidade' => $produto['quantidade'],
                'preco' => $produto['preco']
            ];
        }, $produtos);

        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Pedido encontrado.'
            ],
            'retorno' => $pedido
        ], 200);
    }

    public function update($id = null) {
        $pedidoModel = new PedidosModel();
        $pedidoProdutoModel = new PedidosProdutosModel();
        $produtoModel = new ProdutosModel();
    
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
    
        if (!$pedidoModel->find($id)) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Pedido não encontrado.'
                ],
                'retorno' => new \stdClass()
            ], 404);
        }
    
        // Atualiza o status, se fornecido
        if (isset($parametros->status)) {
            $pedidoModel->update($id, ['status' => $parametros->status]);
        }
    
        // Verifica se há produtos para atualizar
        if (isset($parametros->produtos)) {
            // Exclui os produtos atuais para este pedido (caso seja necessário atualizar)
            $produtosRemover = [];
            foreach ($parametros->produtos as $produto) {
                if (isset($produto->produto_id)) {
                    $produtoDetalhes = $produtoModel->find($produto->produto_id);
                    if (!$produtoDetalhes) {
                        $produtosRemover[] = $produto->produto_id;
                        continue;
                    }
    
                    // Verifica se o produto já existe no pedido
                    $produtoExistente = $pedidoProdutoModel
                        ->where('pedido_id', $id)
                        ->where('produto_id', $produto->produto_id)
                        ->first();
    
                    // Se o produto existe, atualiza
                    if ($produtoExistente) {
                        $pedidoProdutoModel->update(
                            $produtoExistente['id'], 
                            [
                                'quantidade' => $produto->quantidade ?? $produtoExistente['quantidade'],
                                'preco' => $produto->preco ?? $produtoExistente['preco']
                            ]
                        );
                    } else {
                        // Se o produto não existe, insere no pedido
                        $produtoData = [
                            'pedido_id' => $id,
                            'produto_id' => $produto->produto_id,
                            'quantidade' => $produto->quantidade,
                            'preco' => $produto->preco
                        ];
                        $pedidoProdutoModel->insert($produtoData);
                    }
                }
            }
    
            if (!empty($produtosRemover)) {
                return $this->respond([
                    'cabecalho' => [
                        'status' => 404,
                        'mensagem' => 'Os seguintes produtos não foram encontrados: ' . implode(', ', $produtosRemover)
                    ],
                    'retorno' => new \stdClass()
                ], 404);
            }
        }
    
        // Se houver necessidade de excluir produtos
        if (isset($parametros->produtos_excluir)) {
            foreach ($parametros->produtos_excluir as $produtoExcluir) {
                $pedidoProdutoModel->where('pedido_id', $id)
                    ->where('produto_id', $produtoExcluir->produto_id)
                    ->delete();
            }
        }
    
        $pedidoAtualizado = $pedidoModel->find($id);
        $produtosAtualizados = $pedidoProdutoModel->where('pedido_id', $id)->findAll();
    
        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Pedido atualizado com sucesso.'
            ],
            'retorno' => [
                'pedido' => $pedidoAtualizado,
                'produtos' => $produtosAtualizados
            ]
        ], 200);
    }

    // Deletar um pedido
    public function delete($id = null) {
        $pedidoModel = new PedidosModel();
        $pedidoProdutoModel = new PedidosProdutosModel();

        // Verifica se o pedido existe
        if (!$pedidoModel->find($id)) {
            return $this->respond([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Pedido não encontrado.'
                ],
                'retorno' => null
            ], 404);
        }

        // Deleta os produtos associados ao pedido
        $pedidoProdutoModel->where('pedido_id', $id)->delete();
        
        // Deleta o pedido
        $pedidoModel->delete($id);

        // Resposta de sucesso
        return $this->respond([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Pedido deletado com sucesso.'
            ],
            'retorno' => [
                'pedido_id' => $id
            ]
        ], 200);
    }
}