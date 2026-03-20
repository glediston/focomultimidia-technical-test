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
     * TESTE 1: Cadastro de reserva com SUCESSO.
     */
    public function test_can_create_reservation_successfully()
    {
        $hotel = Hotel::create(['id' => '1', 'name' => 'Hotel Teste']);
        
        $room = Room::create([
            'id' => '101', 
            'hotel_id' => $hotel->id, 
            'name' => 'Quarto Luxo 101', 
            'inventory_count' => 10
        ]);

        $payload = [
            "hotel_id"            => $hotel->id,
            "room_id"             => $room->id,
            "customer_first_name"  => "Hospede",
            "customer_last_name"   => "Cinco",
            "arrival_date"        => "2026-06-12",
            "departure_date"      => "2026-06-15",
            "total_price"         => 1050.00
        ];

        $response = $this->postJson('/api/reservations', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reservations', [
            'customer_last_name' => 'Cinco',
            'room_id' => '101'
        ]);
    }

    /**
     * TESTE 2: Validar erro de Hotel Inexistente.
     */
    public function test_cannot_book_with_invalid_hotel_id()
    {
        $response = $this->postJson('/api/reservations', [
            'hotel_id' => '999', 
            'room_id' => '123',
            'customer_first_name' => 'Marcos',
            'customer_last_name' => 'Teste',
            'arrival_date' => '2026-10-01',
            'departure_date' => '2026-10-05',
            'total_price' => 500
        ]);

        $response->assertStatus(422)
                 ->assertJson(['error' => 'Hotel com ID 999 não encontrado.']);
    }

    /**
     * TESTE 3: Validar a Lotação Máxima (Inventário/Overbooking).
     */
    public function test_cannot_book_beyond_inventory_limit()
    {
        $hotel = Hotel::create(['id' => '1', 'name' => 'Hotel Teste']);
        
        // Quarto com apenas 1 unidade disponível
        $room = Room::create([
            'id' => '202', 
            'hotel_id' => $hotel->id, 
            'name' => 'Quarto Standard 202', 
            'inventory_count' => 1 
        ]);
        
        // Cria a primeira reserva para ocupar o único quarto
        Reservation::create([
            'hotel_id' => $hotel->id,
            'room_id' => $room->id,
            'customer_first_name' => "Ocupante",
            'customer_last_name' => "Atual",
            'arrival_date' => '2026-12-01',
            'departure_date' => '2026-12-05',
            'total_price' => 1000
        ]);

        // Tenta reservar o mesmo quarto no mesmo período
        $response = $this->postJson('/api/reservations', [
            'hotel_id' => $hotel->id,
            'room_id' => $room->id,
            'customer_first_name' => 'Marcos',
            'customer_last_name' => 'Excedente',
            'arrival_date' => '2026-12-02', 
            'departure_date' => '2026-12-03',
            'total_price' => 200
        ]);

        $response->assertStatus(422)
                 ->assertJson(['error' => 'Não há disponibilidade para este quarto no período selecionado.']);
    }

    /**
     * TESTE 4: Validar erro de Data de Saída anterior à de Entrada.
     */
    public function test_cannot_book_with_invalid_dates()
    {
        $hotel = Hotel::create(['id' => '1', 'name' => 'Hotel Teste']);
        $room = Room::create([
            'id' => '303', 'hotel_id' => $hotel->id, 'name' => 'Quarto 303', 'inventory_count' => 5
        ]);

        $response = $this->postJson('/api/reservations', [
            'hotel_id' => $hotel->id,
            'room_id' => $room->id,
            'customer_first_name' => 'Erro',
            'customer_last_name' => 'Datas',
            'arrival_date' => '2026-12-10',
            'departure_date' => '2026-12-08', // Data inválida
            'total_price' => 100
        ]);

        $response->assertStatus(422)
                 ->assertJson(['error' => 'A data de saída deve ser posterior à data de entrada.']);
    }
}