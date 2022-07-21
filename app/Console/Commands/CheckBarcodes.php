<?php

namespace App\Console\Commands;
use App\Barcode;
use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;

class CheckBarcodes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:barcodes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'daily checks that barcodes have passed their expiration date';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $expiredBarcodes = Barcode::where('isUsed', 0)->get();

        foreach ($expiredBarcodes as $barcode) {
            try {
            if(Carbon::createFromFormat('d/m/Y H:i:s', $barcode->endTime." 23:59:00")->lt(Carbon::now())){
             $barcode->isExpired = 1;
             $barcode->isReserved = 0;
             $barcode->save();
           }
            } catch (\Exception $e) {
                
            }
         
           
        }
    }
}
