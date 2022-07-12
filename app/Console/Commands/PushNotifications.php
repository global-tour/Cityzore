<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\GygNotification;
use App\Http\Controllers\Helpers\ApiRelated;

class PushNotifications extends Command
{

    public $apiRelated;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends push notications to GYG (notify-availability-update)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->apiRelated = new ApiRelated();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $notifications = GygNotification::where('status', 0)->take(5)->get();
        foreach ($notifications as $notification) {
            $productId = $notification->optionRefCode;
            $dateTime = $notification->dateTime;
            $vacancies = $notification->vacancies;
            $this->apiRelated->notifyAvailabilityUpdate($productId, $dateTime, $vacancies);
            $notification->status = 1;
            $notification->save();
        }

    }
}
