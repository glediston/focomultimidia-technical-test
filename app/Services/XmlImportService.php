<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Support\Facades\File;

class XmlImportService
{
    public function importHotels()
{
    $path = database_path('xml/hotels.xml');
    
    if (!File::exists($path)) {
        throw new \Exception("Arquivo não encontrado em: $path");
    }

    $xmlContent = File::get($path);
    
    
    $xmlContent = trim($xmlContent);

   
    $xml = simplexml_load_string($xmlContent, "SimpleXMLElement", LIBXML_NOERROR);

    if ($xml === false) {
        throw new \Exception("Erro ao processar o XML. Verifique se o arquivo hotels.xml está bem formatado.");
    }

    foreach ($xml->hotel as $hotelData) {
        Hotel::updateOrCreate(
            ['id' => (string) $hotelData['id']],
            ['name' => (string) $hotelData->name]
        );
    }
}


public function importRooms()
{
    $xml = simplexml_load_string(File::get(database_path('xml/rooms.xml')));

    foreach ($xml->room as $roomData) {
        \App\Models\Room::updateOrCreate(
            ['id' => (string) $roomData['id']],
            [
                'hotel_id' => (string) $roomData['hotel_id'],
                'name' => (string) $roomData, 
                'inventory_count' => (int) $roomData['inventory_count']
            ]
        );
    }
}

public function importRates()
{
    $xml = simplexml_load_string(File::get(database_path('xml/rates.xml')));

    foreach ($xml->rate as $rateData) {
        \App\Models\Rate::updateOrCreate(
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


public function importReservations()
{
    $xml = simplexml_load_string(File::get(database_path('xml/reservations.xml')));

    foreach ($xml->reservation as $res) {
        \App\Models\Reservation::updateOrCreate(
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


}