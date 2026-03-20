<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\Rate;
use App\Models\Reservation;
use Illuminate\Support\Facades\File;
use Exception;

class XmlImportService
{
    /**
     * Importa os hotéis do arquivo XML.
     * Utiliza updateOrCreate para evitar duplicidade e permitir atualizações.
     */
    public function importHotels(): void
    {
        $path = database_path('xml/hotels.xml');
        $xml = $this->loadXmlFile($path);

        foreach ($xml->hotel as $hotelData) {
            Hotel::updateOrCreate(
                ['id' => (string) $hotelData['id']],
                ['name' => (string) $hotelData->name]
            );
        }
    }

    /**
     * Importa os quartos vinculando-os aos hotéis.
     */
    public function importRooms(): void
    {
        $path = database_path('xml/rooms.xml');
        $xml = $this->loadXmlFile($path);

        foreach ($xml->room as $roomData) {
            Room::updateOrCreate(
                ['id' => (string) $roomData['id']],
                [
                    'hotel_id' => (string) $roomData['hotel_id'],
                    'name' => (string) $roomData, 
                    'inventory_count' => (int) $roomData['inventory_count']
                ]
            );
        }
    }

    /**
     * Importa as tarifas (Rates) e seus respectivos preços.
     */
    public function importRates(): void
    {
        $path = database_path('xml/rates.xml');
        $xml = $this->loadXmlFile($path);

        foreach ($xml->rate as $rateData) {
            Rate::updateOrCreate(
                ['id' => (string) $rateData['id']],
                [
                    'hotel_id' => (string) $rateData['hotel_id'],
                    'name' => (string) $rateData,
                    'price' => (float) $rateData['price'],
                    'active' => (bool) $rateData['active']
                ]
            );
        }
    }

    /**
     * Importa as reservas iniciais contidas no XML.
     * Mapeia os nós aninhados (customer e room) para as colunas do banco.
     */
    public function importReservations(): void
    {
        $path = database_path('xml/reservations.xml');
        $xml = $this->loadXmlFile($path);

        foreach ($xml->reservation as $res) {
            Reservation::updateOrCreate(
                ['id' => (string) $res->id],
                [
                    'hotel_id'           => (string) $res->hotel_id,
                    'room_id'            => (string) $res->room->id,
                    'customer_first_name' => (string) $res->customer->first_name,
                    'customer_last_name'  => (string) $res->customer->last_name,
                    'arrival_date'       => (string) $res->room->arrival_date,
                    'departure_date'     => (string) $res->room->departure_date,
                    'total_price'        => (float) $res->room->totalprice,
                ]
            );
        }
    }

    /**
     * Método auxiliar para carregar e validar o arquivo XML.
     * Centraliza o tratamento de erros de leitura.
     */
    private function loadXmlFile(string $path): \SimpleXMLElement
    {
        if (!File::exists($path)) {
            throw new Exception("Arquivo XML obrigatório não encontrado em: $path");
        }

        $xmlContent = File::get($path);
        
        // Desabilita erros internos do libxml para podermos tratar com Exception
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string(trim($xmlContent));

        if ($xml === false) {
            $errors = libxml_get_errors();
            libxml_clear_errors();
            throw new Exception("Erro de sintaxe no arquivo XML ($path): " . ($errors[0]->message ?? 'Formato inválido'));
        }

        return $xml;
    }
}