# 🏨 Hotel Management API

API REST desenvolvida com **Laravel 11** para gerenciamento de hotéis, quartos e reservas, incluindo importação de dados via XML e validação de disponibilidade de quartos.

---

## 🚀 Tecnologias Utilizadas

- PHP 8.2+
- Laravel 11
- MySQL (XAMPP)
- SQLite (Testes)
- PHPUnit



## Funcionalidades

✔️ Importação de dados via XML  
✔️ CRUD de reservas  
✔️ Listagem de quartos  
✔️ Validação de disponibilidade de quartos  
✔️ Prevenção de overbooking (conflito de datas)  
✔️ Testes automatizados  

---

## Configuração do Ambiente

### 1. Clonar o projeto

git clone <url-do-repositorio>
cd nome-do-projeto


### 2. Instalar dependências

composer install


### 3. Configurar variáveis de ambiente

cp .env.example .env

Edite o .env:

DB_CONNECTION=mysql
DB_DATABASE=motel_api
DB_USERNAME=root
DB_PASSWORD=

---

### 4. Gerar chave

php artisan key:generate


### 5. Rodar migrations

php artisan migrate


## Importação de Dados

php artisan app:import-hotels


## Endpoints

GET /api/rooms  
POST /api/reservations  


## Regras de Negócio

- Não permite reservas em hotéis inexistentes  
- Não permite reservas em quartos inexistentes  
- Não permite overbooking  


## Testes

php artisan test


##  Autor

Glediston
