<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Av;

class KeremFindSameDateTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kerem:datetime';

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
        $avs = [1, 2];
        $sameDT = [];
        foreach($avs as $avID) {
            $av = Av::find($avID);
            $hourly = json_decode($av->hourly);

            foreach($hourly as $key1 => $h1) {
                foreach($hourly as $key2 => $h2) {
                    if($h1->day == $h2->day && $h1->hour == $h2->hour && $key1 != $key2) {
                        $var = $h1->day . " " . $h1->hour . " | av: " . $avID;
                        array_push($sameDT, $var);
                    }
                }
            }
        }
        dd($sameDT);
    }
}
