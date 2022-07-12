<?php

namespace App\Console\Commands;

use App\Currency;
use Goutte\Client;
use Illuminate\Console\Command;
use Symfony\Component\HttpClient\HttpClient;

class RetrieveCurrencies extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retrieve:currencies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve necessary currencies';

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
        $neededCurrencies = [
            'USD' => 5,
            'EUR' => 4,
            'GBP' => 5,
            'CHF' => 5,
            'CAD' => 5,
            'RUB' => 5,
            'NOK' => 5,
            'AED' => 7,
            'JPY' => 6,
            'INR' => 5,
            'CSK' => 5,
            'AZN' => 5,
            'KRW' => 6,
            'QAR' => 5,
            'THB' => 5
        ];
        $iconClasses = [
            'USD' => 'icon-cz-usd',
            'EUR' => 'icon-cz-eur',
            'GBP' => 'icon-cz-gbp',
            'CHF' => 'icon-cz-chf',
            'CAD' => 'icon-cz-cad',
            'RUB' => 'icon-cz-rub',
            'NOK' => 'icon-cz-nok',
            'AED' => 'icon-cz-aed',
            'JPY' => 'icon-cz-jpy',
            'INR' => 'icon-cz-inr',
            'CSK' => 'icon-cz-czk',
            'AZN' => 'icon-cz-azn',
            'KRW' => 'icon-cz-krw',
            'QAR' => 'icon-cz-qar',
            'THB' => 'icon-cz-thb',
        ];
        $client = new Client(HttpClient::create(['timeout' => 60]));

        $crawler = $client->request('GET', 'https://kur.doviz.com/');
        $crawler = $crawler->filter('#currencies tr')->each(function ($node) {
            return $node->text();
        });

        foreach ($crawler as $i => $c) {
            $exploded = explode(' ', $c);
            if (array_key_exists($exploded[0], $neededCurrencies)) {
                $query = Currency::where('currency', $exploded[0])->first();
                $currency = new Currency();
                if ($query) {
                    $currency = $query;
                }
                $currency->currency = $exploded[0];
                $currency->value = str_replace(',', '.', $exploded[$neededCurrencies[$exploded[0]]]);
                $currency->iconClass = $iconClasses[$exploded[0]];
                $currency->save();
            }
        }
        $queryForTRY = Currency::where('currency', 'TRY')->first();
        $currency = new Currency();
        if ($queryForTRY) {
            $currency = $queryForTRY;
        }
        $currency->currency = 'TRY';
        $currency->value = 1;
        $currency->iconClass = 'icon-cz-try';
        $currency->save();
        $queryForJPY = Currency::where('currency', 'JPY')->first();
        if ($queryForJPY) {
            $queryForJPY->value /= 100;
            $queryForJPY->save();
        }
    }
}
