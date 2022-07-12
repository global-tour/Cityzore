<?php

namespace App\TicketService;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class Service extends Utilities
{
    public $client;

    public $query;

    public $retry;

    protected $event;

    public $date;

    public $time;

    public $quantity;

    public function __construct()
    {
        $handlerStack = HandlerStack::create();

        $handlerStack->push(Middleware::retry($this->retryDecider(), $this->retryDelay()));

        $config = [
            'handler' => $handlerStack,
            'base_uri' => Utilities::BASE_URI,
            'headers' => Utilities::HEADER,
        ];

        $this->client = new Client($config);
    }

    public function retryDecider()
    {
        return function (
            $retries,
            Request $request,
            Response $response = null,
            RequestException $exception = null
        ) {
            if ($retries >= 10) {
                return false;
            }

            if (isset(json_decode($response->getBody(), 1)['Events'])) {
                if (!$this->checkAvailability(json_decode($response->getBody(), 1)['Events'])) {
                    return true;
                }
            }

            if ($exception instanceof ConnectException) {
                return true;
            }

            return false;
        };
    }

    public function retryDelay()
    {
        return function ($numberOfRetries) {
            return 1000;
        };
    }

    public function checkAvailability($events)
    {
        foreach ($events as $item){
            if (Carbon::make($item['StartDateTime'])->format('H:i') == $this->time) {
                $this->setEvent($item);
                return true;
            }
        }
    }

    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param mixed $event
     */
    public function setEvent($event): void
    {
        $this->event = $event;
    }


}
