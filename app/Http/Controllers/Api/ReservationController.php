<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    return response()->json(\App\Models\Reservation::all());
}

   
    public function store(Request $request, \App\Services\ReservationService $service)
{
    try {
       
        $data = $request->all();

        $reservation = $service->createReservation($data);

        return response()->json([
            'message' => 'Reserva criada com sucesso!',
            'data' => $reservation
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage()
        ], 422);
    }
}

  
    public function show(string $id)
{
    $reservation = \App\Models\Reservation::find($id);
    if (!$reservation) {
        return response()->json(['error' => 'Reserva não encontrada.'], 404);
    }
    return response()->json($reservation);
}

    public function update(Request $request, string $id, \App\Services\ReservationService $service)
{
    try {
        $reservation = \App\Models\Reservation::find($id);
        if (!$reservation) {
            return response()->json(['error' => 'Reserva não encontrada.'], 404);
        }

        $data = $request->all();
        // A lógica de check-in/check-out e disponibilidade também deve valer para update
        $updated = $service->updateReservation($reservation, $data);

        return response()->json([
            'message' => 'Reserva atualizada com sucesso!',
            'data' => $updated
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 422);
    }
}

    public function destroy(string $id)
{
    $reservation = \App\Models\Reservation::find($id);
    if (!$reservation) {
        return response()->json(['error' => 'Reserva não encontrada.'], 404);
    }
    $reservation->delete();
    return response()->json(['message' => 'Reserva removida com sucesso.'], 200);
}

}
