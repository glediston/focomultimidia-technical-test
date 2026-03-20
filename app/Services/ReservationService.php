<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Hotel;
use Exception;
use Carbon\Carbon;

class ReservationService
{
    /**
     * Valida se um quarto possui inventário disponível para o período solicitado.
     * Regra: O número de reservas ativas não pode superar o inventory_count do quarto.
     */
    public function isRoomAvailable($hotelId, $roomId, $arrivalDate, $departureDate, $excludeReservationId = null): bool
    {
        // 1. Validação de existência do Hotel e Quarto
        $hotel = Hotel::find($hotelId);
        if (!$hotel) {
            throw new Exception("Hotel com ID {$hotelId} não encontrado.");
        }

        $room = Room::find($roomId);
        if (!$room) {
            throw new Exception("Quarto com ID {$roomId} não encontrado.");
        }

        // 2. Garante integridade: o quarto deve pertencer ao hotel informado
        if ($room->hotel_id != $hotelId) {
            throw new Exception("Este quarto não pertence ao hotel informado.");
        }

        $totalInventory = (int) $room->inventory_count;

        /**
         * 3. Lógica de Conflito de Datas:
         * Uma reserva conflita se: (Chegada < SaídaExistente) E (Saída > ChegadaExistente)
         */
        $query = Reservation::where('room_id', $roomId)
            ->where(function ($query) use ($arrivalDate, $departureDate) {
                $query->where('arrival_date', '<', $departureDate)
                      ->where('departure_date', '>', $arrivalDate);
            });

        // Ignora a própria reserva em caso de atualização (update)
        if ($excludeReservationId) {
            $query->where('id', '!=', $excludeReservationId);
        }

        $occupiedCount = $query->count();

        return $occupiedCount < $totalInventory;
    }

    /**
     * Cria uma nova reserva após validar datas e disponibilidade.
     */
    public function createReservation(array $data): Reservation
    {
        // Validação básica de cronologia
        if (strtotime($data['arrival_date']) >= strtotime($data['departure_date'])) {
            throw new Exception("A data de saída deve ser posterior à data de entrada.");
        }

        // Validação de Overbooking
        if (!$this->isRoomAvailable(
            $data['hotel_id'],
            $data['room_id'],
            $data['arrival_date'],
            $data['departure_date']
        )) {
            throw new Exception("Não há disponibilidade para este quarto no período selecionado.");
        }

        return Reservation::create($data);
    }

    /**
     * Atualiza uma reserva existente.
     * Importante: Valida a disponibilidade ignorando as datas da própria reserva atual.
     */
    public function updateReservation(Reservation $reservation, array $data): Reservation
    {
        $arrival = $data['arrival_date'] ?? $reservation->arrival_date;
        $departure = $data['departure_date'] ?? $reservation->departure_date;
        $roomId = $data['room_id'] ?? $reservation->room_id;
        $hotelId = $data['hotel_id'] ?? $reservation->hotel_id;

        if (strtotime($arrival) >= strtotime($departure)) {
            throw new Exception("A data de saída deve ser posterior à data de entrada.");
        }

        // Valida disponibilidade passando o ID atual para não causar "falso positivo" de ocupação
        if (!$this->isRoomAvailable($hotelId, $roomId, $arrival, $departure, $reservation->id)) {
            throw new Exception("Alteração impossível: O novo período ou quarto não possui disponibilidade.");
        }

        $reservation->update($data);
        return $reservation;
    }
}