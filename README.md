# Projeto API RESTful - Clientes, Produtos e Pedidos

Este projeto é uma API RESTful desenvolvida com o framework **CodeIgniter 4** para gerenciar clientes, produtos e pedidos. Ele permite a criação, leitura, atualização e exclusão (CRUD) de registros nesses três recursos.

## Requisitos

- PHP 7.4 ou superior
- Composer
- CodeIgniter 4
- Banco de Dados MySQL (ou outro de sua preferência)

## Como baixar o projeto


### 1. Clone o repositório para sua máquina local:

```bash
git clone https://github.com/Davidtricolor2021/API_pedidos.git
cd API_pedidos
```

### 2. Instale as dependências com o Composer:

```bash
composer install
```

### 3. Configuração do .env
Copie o arquivo env para .env:

```bash
cp .env.example .env
```

Abra o arquivo .env e configure as variáveis de ambiente, como baseURL e as configurações do banco de dados:


```bash
CI_ENVIRONMENT = development
app.baseURL = http://localhost:8080

database.default.hostname = localhost
database.default.database = nome_do_banco
database.default.username = usuario_do_banco
database.default.password = senha_do_banco
database.default.DBDriver = MySQLi
database.default.DBPrefix = 
database.default.port = 3306
```

### 4. Criar o Banco de Dados
Utilize o comando abaixo para criar o banco de dados automaticamente:

```bash
php spark db:create
```

### 5. Rodar as Migrations
Depois de criar o banco de dados, rode as migrations para criar as tabelas necessárias:

```bash
php spark migrate
```

### 6. Rodar o Servidor de Desenvolvimento
Para rodar a API localmente, execute o seguinte comando:

```bash
php spark serve
```

Isso vai iniciar o servidor em http://localhost:8080.

## Endpoints da API
A seguir estão os detalhes de cada endpoint, com exemplos de requisições e respostas.

### 1. Endpoints de Clientes

Criar Cliente

- Método: POST
- URL: /clientes
- Body (JSON):

```json
{
  "parametros": {
    "cpf_cnpj": "12345678901234",
    "nome_razao_social": "Empresa Exemplo"
  }
}
```

- Resposta de Sucesso (201):

```json
{
  "cabecalho": {
    "status": 201,
    "mensagem": "Cliente criado com sucesso."
  },
  "retorno": {
    "cpf_cnpj": "12345678901234",
    "nome_razao_social": "Empresa Exemplo"
  }
}
```

- Resposta de Erro (422):
```json
{
  "cabecalho": {
    "status": 422,
    "mensagem": "Erro de validação."
  },
  "retorno": {
    "cpf_cnpj": ["O campo cpf_cnpj é obrigatório."],
    "nome_razao_social": ["O campo nome_razao_social é obrigatório."]
  }
}
```

Listar Todos os Clientes

- Método: GET
- URL: /clientes
- Resposta de Sucesso (200):

```json
{
  "cabecalho": {
    "status": 200,
    "mensagem": "Clientes encontrados."
  },
  "retorno": [
    {
      "cpf_cnpj": "12345678901234",
      "nome_razao_social": "Empresa Exemplo"
    }
  ]
}
```

Mostrar Cliente Específico

- Método: GET
- URL: /clientes/{id}
- Resposta de Sucesso (200):

```json
{
  "cabecalho": {
    "status": 200,
    "mensagem": "Cliente encontrado."
  },
  "retorno": {
    "cpf_cnpj": "12345678901234",
    "nome_razao_social": "Empresa Exemplo"
  }
}
```

Atualizar Cliente

- Método: PUT
- URL: /clientes/{id}
- Body (JSON):

```json
{
  "parametros": {
    "cpf_cnpj": "12345678901234",
    "nome_razao_social": "Nova Empresa"
  }
}
```

- Resposta de Sucesso (200):

```json
{
  "cabecalho": {
    "status": 200,
    "mensagem": "Cliente atualizado com sucesso."
  },
  "retorno": {
    "cpf_cnpj": "12345678901234",
    "nome_razao_social": "Nova Empresa"
  }
}
```

Deletar Cliente

- Método: DELETE
- URL: /clientes/{id}
- Resposta de Sucesso (200):

```json
{
  "cabecalho": {
    "status": 200,
    "mensagem": "Cliente deletado com sucesso."
  },
  "retorno": {
    "cpf_cnpj": "12345678901234",
    "nome_razao_social": "Nova Empresa"
  }
}
```
### 2. Endpoints de Produtos

Criar Produto

- Método: POST
- URL: /produtos
- Body (JSON):

```json
{
  "parametros": {
    "descricao": "Produto Exemplo",
    "preco": 100.0
  }
}
```

 - Resposta de sucesso (201):

```json
{
  "cabecalho": {
    "status": 201,
    "mensagem": "Produto criado com sucesso."
  },
  "retorno": {
    "descricao": "Produto Exemplo",
    "preco": 100.0
  }
}
```

Listar todos os Produtos

- Método: GET
- URL: /produtos

- Resposta de sucesso (200):

```json
{
  "cabecalho": {
    "status": 200,
    "mensagem": "Produtos encontrados."
  },
  "retorno": [
    {
      "id": 1,
      "descricao": "Produto Exemplo",
      "preco": 100.0
    }
  ]
}
```

Mostrar um Produto

- Método: GET
- URL: /produtos/{id}

- Resposta de sucesso (200):

```json
{
  "cabecalho": {
    "status": 200,
    "mensagem": "Produto encontrado."
  },
  "retorno": {
    "id": 1,
    "descricao": "Produto Exemplo",
    "preco": 100.0
  }
}
```

Atualizar Produto

- Método: PUT
- URL: /produtos/{id}
- Body (JSON):

```json
{
  "parametros": {
    "descricao": "Produto Atualizado",
    "preco": 120.0
  }
}
```

- Resposta de sucesso (200):

```json
{
  "cabecalho": {
    "status": 200,
    "mensagem": "Produto atualizado com sucesso."
  },
  "retorno": {
    "descricao": "Produto Atualizado",
    "preco": 120.0
  }
}
```

Deletar Produto

- Método: DELETE
- URL: /produtos/{id}
- Resposta de sucesso (200):

```json
{
  "cabecalho": {
    "status": 200,
    "mensagem": "Produto deletado com sucesso."
  },
  "retorno": {
    "id": 1,
    "descricao": "Produto Atualizado",
    "preco": 120.0
  }
}
```

### 3. Endpoints de Pedidos

Criar Pedido

- Método: POST
- URL: /pedidos
- Body (JSON):

Listar todos os Pedidos

- Método: GET
- URL: /pedidos

Mostrar Pedido Específico

- Método: GET
- URL: /pedidos/{id}

Atualizar Pedido

- Método: PUT
- URL: /pedidos/{id}
- Body (JSON):

Deletar Pedido

- Método: DELETE
- URL: /pedidos/{id}