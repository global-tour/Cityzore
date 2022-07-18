<?php

namespace App\Imports;

use App\Barcode;
use App\TicketType;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BarcodeImport implements ToCollection, WithHeadingRow, ShouldQueue, WithBatchInserts, WithChunkReading
{
    use Importable;

    public $success_data = ["counter" => 0, "items" => []];
    public $failed_data = ["counter" => 0, "items" => []];
    public $ownerID;
    private $ticketType;
    private $importedBy;

    public function __construct($ownerID, $ticketType, $importedBy)
    {
        $this->ticketType = $ticketType;
        $this->ownerID = $ownerID;
        $this->importedBy = $importedBy;
    }

    public function collection(Collection $rows)
    {
        try {
            foreach ($rows as $row) {

                if (!$row['code'] || empty($row['code']))
                    continue;

                if (Barcode::where('code', $row['code'])->first())
                    continue;

                $barcode = new Barcode();

                $barcode->endTime = $row['expired_date'] ?? Carbon::now()->addYear()->format('d/m/Y');
                $barcode->reservationNumber = $row['reservation_number'] ?? '';
                $barcode->code = $row['code'] ?? '';
                $barcode->isUsed = 0;
                $barcode->isReserved = 0;
                $barcode->isExpired = 0;
                $barcode->ownerID = -1;
                $barcode->cartID = null;
                $barcode->bookingID = null;
                $barcode->recode = null;
                $barcode->contact = null;
                $barcode->ticketType = $this->ticketType->id;
                $barcode->description = $row['description'] ?? '';
                $barcode->searchableTicketType = $this->ticketType->name ;
                $barcode->save();
            }

            Log::info(json_encode([
                'status' => 'Success',
                'ticketType' => $this->ticketType->name,
                'importedBy' => $this->importedBy,
            ]));

        } catch (\Exception $exception) {

            Log::error('Barcode Import Error: '. $exception);

        }
    }

    public function batchSize(): int
    {
        return 300;
    }

    public function chunkSize(): int
    {
        return 300;
    }
}
