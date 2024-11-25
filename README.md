
# Sistema de Inscrição para Cursos

Este projeto é um sistema de inscrições para cursos, desenvolvido com **Laravel 11** no backend, **Vue.js 3** no frontend, e utiliza **Inertia.js** para integração entre as duas tecnologias. Ele permite que os alunos se inscrevam em cursos, com funcionalidades de gerenciamento de usuários e cursos.

## Requisitos

Antes de executar o projeto, certifique-se de ter as seguintes ferramentas instaladas em sua máquina:

- **PHP** (Versão 8.2 ou superior)
- **Composer** (Gerenciador de dependências PHP)
- **Node.js** (Versão 16 ou superior)
- **NPM ou Yarn** (Gerenciador de pacotes JavaScript)
- **MySQL ou MariaDB** (Banco de dados)
- **Laravel** (Versão 11 ou superior)

## Passo a Passo para Executar o Projeto

### 1. Clonando o Repositório

Primeiro, clone o repositório para a sua máquina local utilizando o seguinte comando:

```bash
git clone https://github.com/luysfernnando/teste_marcasite_cursos
```

Entre na pasta do projeto:

```bash
cd teste_marcasite_cursos
```

### 2. Configuração do Ambiente

#### 2.1. Configuração do `.env`

Crie um arquivo `.env` a partir do arquivo `.env.example`:

```bash
cp .env.example .env
```

Agora, edite o arquivo `.env` para configurar as credenciais do seu banco de dados. As variáveis de ambiente mais importantes são:

- `DB_CONNECTION=mysql`
- `DB_HOST=127.0.0.1`
- `DB_PORT=3306`
- `DB_DATABASE=nome_do_banco`
- `DB_USERNAME=usuario_do_banco`
- `DB_PASSWORD=senha_do_banco`
- `VITE_STRIPE_KEY="pk_test_sua_chave_publica"`
- `STRIPE_SECRET=sk_test_sua_chave_secreta`

#### 2.2. Como Obter as Chaves da Stripe

Para configurar o Stripe, você precisará das suas chaves secretas e públicas. Para isso:

1. Crie uma conta no [Stripe](https://stripe.com/).
2. Acesse a página de [ApiKeys](https://dashboard.stripe.com/test/apikeys).
4. Copie a **Publishable key** (chave pública) e a **Secret key** (chave secreta) para colar nas variáveis de ambiente `VITE_STRIPE_KEY` e `STRIPE_SECRET`, respectivamente, no seu arquivo `.env`.

#### 2.3. Instalando Dependências PHP

Instale as dependências do Laravel utilizando o Composer:

```bash
composer install
```

#### 2.4. Gerando a Chave de Aplicação

Gere a chave da aplicação Laravel com o seguinte comando:

```bash
php artisan key:generate
```

#### 2.5. Migrando o Banco de Dados

Se ainda não tiver um banco de dados configurado, crie-o no MySQL/MariaDB e depois execute as migrações:

```bash
php artisan migrate
```

#### 2.6. Instalando Dependências JavaScript

Agora, instale as dependências JavaScript com NPM ou Yarn:

```bash
npm install

# ou se preferir, use Yarn
yarn install
```

### 3. Executando o Projeto

Agora que o ambiente está configurado, você pode executar o servidor de desenvolvimento do Laravel e do Vue.js.

#### 3.1. Iniciando o Servidor Laravel

Para iniciar o servidor do Laravel, use o comando:

```bash
php artisan serve
```

O servidor será iniciado no endereço `http://localhost:8000`.

#### 3.2. Iniciando o Frontend (Vue.js com Inertia.js)

Execute o seguinte comando para compilar os assets do frontend:

```bash
npm run dev

# ou com Yarn
yarn dev
```

Isso iniciará o servidor de desenvolvimento para o Vue.js, e os arquivos JavaScript serão compilados. O frontend estará acessível no mesmo endereço do backend ( `http://localhost:8000` ).

### 4. Testando a Aplicação

Agora você pode acessar a aplicação no navegador utilizando o endereço `http://localhost:8000`.

Caso não tiver um usuário cadastrado, você pode se registrar usando o endereço: `http://localhost:8000/register`.

## Licença

Este projeto está licenciado sob a [MIT License](LICENSE).
