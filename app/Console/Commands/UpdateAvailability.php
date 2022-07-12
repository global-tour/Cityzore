<?php

namespace App\Console\Commands;

use App\Av;
use App\Availability;
use Illuminate\Console\Command;

class UpdateAvailability extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:updateAvailability';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $summitAvailability = Av::where('id', 1)->first()->hourly;
        $summit = json_decode($summitAvailability, true);
        foreach($summit as $key=>$s)  {
            if(($s['hour'] === "18:30" or $s['hour'] === "20:00" or $s['hour'] === "21:00") and $s['sold'] == 0) {
                unset($summit[$key]);
            }
        }
        $su =  Av::where('id', 1)->first();
        $su->hourly = json_encode(array_values($summit));
        $su->save();
    }
}
