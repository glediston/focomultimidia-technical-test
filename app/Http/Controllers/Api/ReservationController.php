<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class ReservationController extends Controller
{
    protected $reservationService;

    /**
     * Injeção do Service no construtor .
     */
    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    /**
     * Lista todas as reservas cadastradas no sistema.
     */
    public function index(): JsonResponse
    {
        return response()->json(Reservation::all());
    }

    /**
     * Cria uma nova reserva.
     * Valida disponibilidade e regras de negócio através do Service.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $reservation = $this->reservationService->createReservation($data);

            return response()->json([
                'message' => 'Reserva criada com sucesso!',
                'data' => $reservation
            ], 201);

        } catch (Exception $e) {
            // Retorna erros de validação ou de disponibilidade 
            return response()->json([
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Exibe os detalhes de uma reserva específica.
     */
    public function show(string $id): JsonResponse
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return response()->json(['error' => 'Reserva não encontrada.'], 404);
        }

        return response()->json($reservation);
    }

    /**
     * Atualiza os dados de uma reserva existente.
     * Revalida o período e o quarto caso as datas sejam alteradas.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $reservation = Reservation::find($id);

            if (!$reservation) {
                return response()->json(['error' => 'Reserva não encontrada.'], 404);
            }

            $updated = $this->reservationService->updateReservation($reservation, $request->all());

            return response()->json([
                'message' => 'Reserva atualizada com sucesso!',
                'data' => $updated
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Remove uma reserva do sistema .
     */
    public function destroy(string $id): JsonResponse
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return response()->json(['error' => 'Reserva não encontrada.'], 404);
        }

        $reservation->delete();

        return response()->json([
            'message' => 'Reserva removida com sucesso.'
        ], 200);
    }
}