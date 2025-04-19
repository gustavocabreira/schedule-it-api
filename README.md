## Instalação

### Requisitos

- [Docker](https://docs.docker.com/engine/install/)
- [Docker Compose](https://docs.docker.com/compose/install/)
- Deverá ter as portas `80`, `1025`, `3306`, `6379`, `7700`, `8025` e `9051` abertas e desocupadas.

### Executando o projeto

1. Crie a network `internal` e `spa` com os seguintes comandos:

```bash
docker network create internal
docker network create spa
```

2. Clone o repositório

```bash
git clone https://github.com/gustavocabreira/schedule-it-api.git
```

3. Entre na pasta do projeto

```bash
cd schedule-it-api
```
4. Entre na pasta docker/local

```bash
cd docker/local
```

5. Execute o comando para instalar o projeto

```bash
sh install.sh --app-name=schedule-it-api
```

6. Acesse a aplicação em http://localhost

7. Você pode acessar a documentação do projeto em http://localhost/docs/api

## Código de respostas HTTP

| Código | Descrição             | Explicação                                                                     | 
|--------|-----------------------|--------------------------------------------------------------------------------|
| 200    | OK                    | A requisição performou com sucesso.                                            |
| 201    | Created               | O recurso foi criado com sucesso.                                              |
| 204    | No Content            | A requisição performou com sucesso, mas não retornou nenhum conteúdo.          |
| 403    | Forbidden             | O recurso não pode ser acessado/alterado pois você não possui permissão.       |
| 404    | Not Found             | O recurso não foi encontrado.                                                  |
| 422    | Unprocessable Entity  | O recurso não pode ser processado devido a um erro nas informações fornecidas. |
| 500    | Internal Server Error | Ocorreu um erro no servidor.                                                   |

## Testes

Os testes do projeto estão no diretório `tests/` e foram desenvolvidos utilizando o pacote [Pest](https://pestphp.com/docs/installation).
Pest é uma biblioteca de testes para PHP que permite escrever testes de forma fácil e rápida.

Para executar os testes, siga os passos abaixo:

1. Acesse o diretório /docker/local
2. Execute o comando para rodar os testes

```bash
docker compose exec -it laravel composer test
```