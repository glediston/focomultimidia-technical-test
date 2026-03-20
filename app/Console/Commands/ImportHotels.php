<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\XmlImportService; // <--- ESTA LINHA É A CHAVE!

class ImportHotels extends Command
{
    protected $signature = 'app:import-hotels';
    protected $description = 'Importa dados dos XMLs para o banco de dados';

    // No método handle, o Laravel faz a "Injeção de Dependência" 
    // Ele lê o tipo (XmlImportService) e instancia para você
    public function handle(XmlImportService $service) 
    {
        $this->info('Iniciando importação...');

        $service->importHotels();
        $this->info('1. Hotéis importados!');

        $service->importRooms();
        $this->info('2. Quartos importados!');

        $service->importRates();
        $this->info('3. Tarifas importadas!');

        $service->importReservations();
        $this->info('4. Reservas importadas!');

        $this->info('Importação completa com sucesso!');
    }
}