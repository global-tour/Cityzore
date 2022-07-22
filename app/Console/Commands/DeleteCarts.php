<?php

namespace App\Console\Commands;

use App\Helpers\Commands\DeleteCarts as RemoveOldCarts;
use Illuminate\Console\Command;


class DeleteCarts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:carts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically removes all images on storage daily';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $instance = new RemoveOldCarts();
        $instance->run();
    }
}
