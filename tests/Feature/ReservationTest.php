<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Reservation;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TESTE 1: Cadastro de reserva com SUCESSO
     */
    public function test_can_create_reservation_successfully()
    {
        $hotel = Hotel::create(['id' => 1, 'name' => 'Hotel Teste', 'location' => 'Rua A', 'rating' => 5]);
        
        // Adicionado o campo 'name' aqui para evitar o erro de SQL
        $room = Room::create([
            'id' => 101, 
            'hotel_id' => $hotel->id, 
            'name' => 'Quarto Luxo 101', 
            'type' => 'Deluxe', 
            'inventory_count' => 10
        ]);

        $payload = [
            "hotel_id"            => $hotel->id,
            "room_id"             => $room->id,
            "customer_first_name"  => "Hospede",
            "customer_last_name"   => "Numero Cinco",
            "arrival_date"        => "2026-06-12",
            "departure_date"      => "2026-06-15",
            "total_price"         => 1050.00
        ];

        $response = $this->postJson('/api/reservations', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reservations', [
            'customer_last_name' => 'Numero Cinco'
        ]);
    }

    /**
     * TESTE 2: Validar erro de Hotel Inexistente
     */
    public function test_cannot_book_with_invalid_hotel_id()
    {
        $response = $this->postJson('/api/reservations', [
            'hotel_id' => 999999, 
            'room_id' => 123,
            'customer_first_name' => 'Marcos',
            'customer_last_name' => 'Inexistente',
            'arrival_date' => '2026-10-01',
            'departure_date' => '2026-10-05',
            'total_price' => 500
        ]);

        $response->assertStatus(422)
                 ->assertJson(['error' => 'Hotel com ID 999999 não encontrado.']);
    }

    /**
     * TESTE 3: Validar erro de Quarto Inexistente
     */
    public function test_cannot_book_with_invalid_room_id()
    {
        $hotel = Hotel::create(['id' => 1, 'name' => 'Hotel Teste', 'location' => 'Rua A', 'rating' => 5]);

        $response = $this->postJson('/api/reservations', [
            'hotel_id' => $hotel->id,
            'room_id' => 888888, 
            'customer_first_name' => 'Marcos',
            'customer_last_name' => 'Sem Quarto',
            'arrival_date' => '2026-10-01',
            'departure_date' => '2026-10-05',
            'total_price' => 500
        ]);

        $response->assertStatus(422)
                 ->assertJson(['error' => "Quarto com ID 888888 não encontrado."]);
    }

    /**
     * TESTE 4: Validar a Lotação Máxima (Inventário)
     */
    public function test_cannot_book_beyond_inventory_limit()
    {
        $hotel = Hotel::create(['id' => 1, 'name' => 'Hotel Teste', 'location' => 'Rua A', 'rating' => 5]);
        
        // Adicionado o campo 'name' aqui também
        $room = Room::create([
            'id' => 202, 
            'hotel_id' => $hotel->id, 
            'name' => 'Quarto Standard 202', 
            'type' => 'Standard', 
            'inventory_count' => 2
        ]);
        
        $arrival = '2026-12-01';
        $departure = '2026-12-05';

        for ($i = 1; $i <= 5; $i++) {
            Reservation::create([
                'hotel_id' => $hotel->id,
                'room_id' => $room->id,
                'customer_first_name' => "Hospede $i",
                'customer_last_name' => "Ocupante",
                'arrival_date' => $arrival,
                'departure_date' => $departure,
                'total_price' => 1000
            ]);
        }

        $response = $this->postJson('/api/reservations', [
            'hotel_id' => $hotel->id,
            'room_id' => $room->id,
            'customer_first_name' => 'Marcos',
            'customer_last_name' => 'O Excedente',
            'arrival_date' => '2026-12-02', 
            'departure_date' => '2026-12-03',
            'total_price' => 200
        ]);

        $response->assertStatus(422)
                 ->assertJson(['error' => 'Não há mais quartos disponíveis desta categoria para o período solicitado.']);
    }
}