# Hotel Foco API

API REST desenvolvida com **Laravel 11** para gerenciamento de hotéis, quartos e reservas, incluindo importação de dados via XML e validação de disponibilidade de quartos.

---

## Tecnologias Utilizadas

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
- Overbooking: O sistema impede novas reservas caso o limite de inventário do quarto tenha sido atingido no período solicitado.
- Validação de Período: A API retorna erro caso a data de saída seja igual ou anterior à data de entrada.

## Testes

php artisan test


## Como testar no insomnia

Endpoints Disponíveis


-/api/rooms GET Lista todos os quartos e seu inventário
-/api/reservations GET Lista todas as reservas cadastradas
-/api/reservations POST Cria uma nova reserva (Valida disponibilidade)
-/api/reservations/{id}  DELETE  Remove uma reserva específica

Exemplo de JSON para Nova Reserva (POST)

{
    "hotel_id": "1375988",
    "room_id": "137598802",
    "customer_first_name": "Glediston",
    "customer_last_name": "Developer",
    "arrival_date": "2026-12-20",
    "departure_date": "2026-12-25",
    "total_price": 600.00
}

##  Autor

Glediston Ferreira Azevedo


