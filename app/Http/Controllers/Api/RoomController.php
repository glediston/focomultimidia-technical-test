<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoomController extends Controller
{
   
    public function index()
{
    return response()->json(\App\Models\Room::all());
}


    public function store(Request $request)
{
    $data = $request->validate([
        'id' => 'required|string|unique:rooms,id',
        'hotel_id' => 'required|exists:hotels,id',
        'name' => 'required|string',
        'inventory_count' => 'required|integer|min:0'
    ]);

    $room = \App\Models\Room::create($data);
    return response()->json($room, 201);
}

    public function show(string $id)
{
    $room = \App\Models\Room::with('hotel')->find($id);
    return $room ? response()->json($room) : response()->json(['error' => 'Quarto não encontrado.'], 404);
}

    public function update(Request $request, string $id)
{
    $room = \App\Models\Room::find($id);
    if (!$room) return response()->json(['error' => 'Quarto não encontrado.'], 404);

    $room->update($request->all());
    return response()->json($room);
}

    public function destroy(string $id)
{
    $room = \App\Models\Room::find($id);
    if (!$room) return response()->json(['error' => 'Quarto não encontrado.'], 404);

    $room->delete();
    return response()->json(['message' => 'Quarto excluído.'], 200);
}
}
