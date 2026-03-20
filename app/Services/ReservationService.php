<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Hotel; // Importante importar o Model Hotel
use Exception;

class ReservationService
{
    
    public function isRoomAvailable($hotelId, $roomId, $arrivalDate, $departureDate)
    {
        
        $hotel = Hotel::find($hotelId);
        if (!$hotel) {
            throw new Exception("Hotel com ID {$hotelId} não encontrado.");
        }

        $room = Room::find($roomId);
        if (!$room) {
            throw new Exception("Quarto com ID {$roomId} não encontrado.");
        }

        if ($room->hotel_id != $hotelId) {
            throw new Exception("Este quarto não pertence ao hotel informado.");
        }

        $totalInventory = (int) $room->inventory_count;

        // 4. Conta reservas conflitantes
        $occupiedCount = Reservation::where('room_id', $roomId)
            ->where(function ($query) use ($arrivalDate, $departureDate) {
                $query->where('arrival_date', '<', $departureDate)
                      ->where('departure_date', '>', $arrivalDate);
            })
            ->count();

        return $occupiedCount < $totalInventory;
    }

    public function createReservation(array $data)
{
   
    if (strtotime($data['arrival_date']) >= strtotime($data['departure_date'])) {
        throw new Exception("A data de saída deve ser maior que a data de entrada.");
    }

  
    if (!$this->isRoomAvailable(
        $data['hotel_id'],
        $data['room_id'],
        $data['arrival_date'],
        $data['departure_date']
    )) {
        throw new Exception("Não há mais quartos disponíveis desta categoria para o período solicitado.");
    }

    return Reservation::create($data);
}
}