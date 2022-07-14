<?php

namespace App\Http\Controllers\Helpers;

use App\Apilog;
use App\BigbusLog;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use phpDocumentor\Reflection\Types\This;

/**
 * Ventrata Test
 * https://agent.edinexplore.com/
 * email: test123@gmail.com
 * pw   : Test123
 * Bearer Token: febbfcd2-c875-4738-b468-7ad417045883
 *
 * Documentation
 * https://docs.ventrata.com/booking
 *
 **/
class BigBusRelated
{

    const BOOKING_URI = 'bookings/' ;

    /**
     * @var Client $client
     */
    protected $client;

    /**
     * @var string $base_uri
     */
    protected $base_uri = 'https://api.ventrata.com/octo/';

    /**
     * @var string $token
     */
    protected $token;

    /**
     * @var array $product
     */
    protected $product;

    /**
     * @var string $option
     */
    protected $option = 'DEFAULT';

    /**
     * @var array $availability
     */
    protected $availability;

    public function setClient()
    {
        $this->setToken();

        $config = [
            'base_uri'  => $this->base_uri,
            'headers'   => [
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json'
            ]
        ];

        $this->client = new Client($config);

        return $this;
    }

    public function setToken()
    {
        $this->token = config('app.env') === 'local'
            ? 'febbfcd2-c875-4738-b468-7ad417045883'
            : '38f7e980-5df8-4357-b2f9-9925dc377a12';

        return $this;
    }

    public function getProducts()
    {
        try {

            $response = $this->client->get('suppliers/0/products');

        }catch (\Exception $exception){

            return [
                'errorCode'     => $exception->getCode(),
                'errorMessage'  => $exception->getMessage()
            ];

        }

        return [
            'status'    => true,
            'data'      => json_decode($response->getBody(), 1)
        ];
    }

    public function checkProduct($productId)
    {
        try {

            $response = $this->client->get('products/'. $productId);

        } catch (ClientException $exception) {

            return [
                'status'        => false,
                'errorCode'     => $exception->getCode(),
                'errorMessage'  => json_decode($exception->getResponse()->getBody()->getContents(), 1)
            ];

        }

        return [
            'status' => true,
            'data' => json_decode($response->getBody(), 1)
        ];

    }


    public function checkAvailability($productId, $localDateStart, $diffDay = 0)
    {
        try {
            $response = $this->client->post('availability', [
                'json' => [
                    'productId' => $productId,
                    'optionId' => $this->option,
                    'localDateStart' => Carbon::parse($localDateStart)->format('Y-m-d'),
                    'localDateEnd' => Carbon::parse($localDateStart)->addDays($diffDay)->format('Y-m-d')
                ]
            ]);


        } catch (ClientException $exception) {

            return [
                'status'        => false,
                'errorCode'     => $exception->getCode(),
                'errorMessage'  => json_decode($exception->getResponse()->getBody()->getContents(), 1)
            ];

        }

        return [
            'status'    => true,
            'data'      => json_decode($response->getBody(), 1)
        ];
    }

    public function reserve($data)
    {
        try {

            $response = $this->client->post(self::BOOKING_URI, [
                'json' => $data
            ]);

        } catch (ClientException $exception) {

            return [
                'status'        => false,
                'errorCode'     => $exception->getCode(),
                'errorMessage'  => json_decode($exception->getResponse()->getBody()->getContents(), 1)
            ];

        }

        return [
            'status'    => true,
            'data'      => json_decode($response->getBody(), 1)
        ];

    }

    public function reserveUpdate($id, $data): array
    {
        try {

            $response = $this->client->post(self::BOOKING_URI . $id, [
                'json' => $data
            ]);

        } catch (ClientException $exception) {

            return [
                'status'        => false,
                'errorCode'     => $exception->getCode(),
                'errorMessage'  => json_decode($exception->getResponse()->getBody()->getContents(), 1)
            ];

        }

        return [
            'status'    => true,
            'data'      => json_decode($response->getBody(), 1)
        ];
    }

    public function delete($id, $data)
    {
        try {

            $response = $this->client->delete(self::BOOKING_URI . $id, [
                'json' => $data
            ]);

        } catch (ClientException $exception) {
            return [
                'status'        => false,
                'errorCode'     => $exception->getCode(),
                'errorMessage'  => json_decode($exception->getResponse()->getBody()->getContents(), 1)
            ];

        }

        return [
            'status'    => true,
            'data'      => json_decode($response->getBody(), 1)
        ];
    }

    public function confirm($id, $data)
    {
        try {

            $response = $this->client->post(self::BOOKING_URI . $id. '/confirm', [
                'json' => $data
            ]);

        } catch (ClientException $exception) {

            return [
                'status'        => false,
                'errorCode'     => $exception->getCode(),
                'errorMessage'  => json_decode($exception->getResponse()->getBody()->getContents(), 1)
            ];

        }

        return [
            'status'    => true,
            'data'      => json_decode($response->getBody(), 1)
        ];
    }

    public function extend($id, $data)
    {
        try {

            $response = $this->client->post(self::BOOKING_URI . $id. '/extend', [
                'json' => $data
            ]);

        } catch (ClientException $exception) {

            return [
                'status'        => false,
                'errorCode'     => $exception->getCode(),
                'errorMessage'  => json_decode($exception->getResponse()->getBody()->getContents(), 1)
            ];

        }

        return [
            'status'    => true,
            'data'      => json_decode($response->getBody(), 1)
        ];
    }

    public function getBooking($id)
    {

        try {

            $response = $this->client->get(self::BOOKING_URI . $id);

        } catch (ClientException $exception) {

            return [
                'status'        => false,
                'errorCode'     => $exception->getCode(),
                'errorMessage'  => json_decode($exception->getResponse()->getBody()->getContents(), 1)
            ];

        }

        return [
            'status'    => true,
            'data'      => json_decode($response->getBody(), 1)
        ];
    }

    public function setLog($booking_id, $status, $response = [])
    {
        try {
            BigbusLog::create([
                'booking_id' => $booking_id,
                'status' => $status,
                'response' => json_encode($response)
            ]);
        } catch (\Exception $exception) {

            return [
                'status'        => false,
            ];
        }

        return [
            'status'    => true,
        ];
    }

}
