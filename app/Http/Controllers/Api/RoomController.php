<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RoomController extends Controller
{
    /**
     * Lista todos os quartos cadastrados no sistema.
     * Inclui a relação com o hotel para facilitar a visualização.
     */
    public function index(): JsonResponse
    {
        return response()->json(Room::with('hotel')->get());
    }

    /**
     * Cadastra um novo quarto manualmente via API.
     * Valida se o ID é único e se o hotel associado existe no banco.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id' => 'required|string|unique:rooms,id',
            'hotel_id' => 'required|exists:hotels,id',
            'name' => 'required|string',
            'inventory_count' => 'required|integer|min:0'
        ]);

        $room = Room::create($data);
        
        return response()->json([
            'message' => 'Quarto cadastrado com sucesso!',
            'data' => $room
        ], 201);
    }

    /**
     * Exibe os detalhes de um quarto específico.
     * Retorna os dados do hotel vinculado através do eager loading (with).
     */
    public function show(string $id): JsonResponse
    {
        $room = Room::with('hotel')->find($id);
        
        if (!$room) {
            return response()->json(['error' => 'Quarto não encontrado.'], 404);
        }

        return response()->json($room);
    }

    /**
     * Atualiza os dados de um quarto (ex: alteração de inventário ou nome).
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $room = Room::find($id);
        
        if (!$room) {
            return response()->json(['error' => 'Quarto não encontrado.'], 404);
        }

        $room->update($request->all());
        
        return response()->json([
            'message' => 'Quarto atualizado com sucesso!',
            'data' => $room
        ]);
    }

    /**
     * Remove um quarto do sistema.
     */
    public function destroy(string $id): JsonResponse
    {
        $room = Room::find($id);
        
        if (!$room) {
            return response()->json(['error' => 'Quarto não encontrado.'], 404);
        }

        $room->delete();
        
        return response()->json([
            'message' => 'Quarto excluído com sucesso.'
        ], 200);
    }
}