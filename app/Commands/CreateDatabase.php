<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CreateDatabase extends BaseCommand
{
    protected $group       = 'custom';
    protected $name        = 'db:create';
    protected $description = 'Cria o banco de dados definido no .env';

    public function run(array $params)
    {
        $dbName = env('database.default.database');
        $dbUser = env('database.default.username');
        $dbPass = env('database.default.password');
        $dbHost = env('database.default.hostname');

        $mysqli = new \mysqli($dbHost, $dbUser, $dbPass);

        if ($mysqli->connect_error) {
            CLI::error('Erro ao conectar ao banco: ' . $mysqli->connect_error);
            return;
        }

        if ($mysqli->query("CREATE DATABASE IF NOT EXISTS $dbName")) {
            CLI::write("Banco de dados `$dbName` criado com sucesso!", 'green');
        } else {
            CLI::error('Erro ao criar o banco: ' . $mysqli->error);
        }

        $mysqli->close();
    }
}