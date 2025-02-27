<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePedidosTable extends Migration
{
    public function up()
    {
        // Tabela de Pedidos
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'cliente_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Em Aberto', 'Pago', 'Cancelado'],
                'default'    => 'Em Aberto',
            ],
        ]);
        $this->forge->addForeignKey('cliente_id', 'clientes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('pedidos');

         // Tabela de Relacionamento entre os Pedidos e Produtos
         $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'pedido_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'produto_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'quantidade' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
            ],
            'preco' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
            ],
        ]);
        $this->forge->addForeignKey('pedido_id', 'pedidos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('produto_id', 'produtos', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('pedidos_produtos');
    }

    public function down()
    {
        $this->forge->dropTable('pedidos');
        $this->forge->dropTable('pedidos_produtos');
    }
}
