<?php

namespace App\TicketService;

use App\TicketService\Factory\OrderFactory;
use function now;

class Events extends Service
{

    public $booking;

    public $order;

    public function getAvailabilities($request)
    {
        $this->parseForm($request);

        $query = [
            'key' => parent::KEY,
            'productCode' => parent::PRODUCT_CODE,
            'productExternalCode' => parent::PRODUCT_EXTERNAL_CODE,
            'rateCode' => parent::RATE_CODE,
            'categoryCode' => parent::CATEGORY_CODE,
            'getAll' => parent::GET_ALL,
            'quotaCode' => parent::QUOTA_CODE,
            'organizationCode' => parent::ORGANIZATION_CODE,
            'date' => $this->date
        ];

        $this->client->get('Events/GetEvents', ['query' => $query]);

        if (is_null($this->getEvent()))
            return new \Exception('Event Not Found!');

        if ($this->getEvent()['AvailableQuantity'] <= $this->quantity)
            $this->quantity = $this->getEvent()['AvailableQuantity'];

        return $this->bookEvent();
    }

    public function bookEvent()
    {
        $query = [
            'key' => parent::KEY
        ];

        $basket = [
            'BasketId' => null,
            'CustomerId' => parent::CUSTOMER_ID,
            'EventId' => $this->getEvent()['EventId'],
            'Quantity' => $this->quantity,
            'ExternalCode' => ''
        ];

        try {

            $response = $this->client->post('Baskets/BookEvent', [
                'query' => $query,
                'json' => $basket
            ]);

            $this->booking = json_decode($response->getBody(), 1);

        } catch (\Exception $exception) {

            throw new \Exception('Book Event is not valid: '. $exception->getMessage());

        }

        return $this->previewOrder();
    }

    public function previewOrder()
    {
        $orderData = [];

        $query = [
            'key' => parent::KEY,
            'type' => parent::TYPE,
        ];

        $orderData['Order'] = $this->booking;
        $orderData['Product'][] = $this->getEvent();
        $orderData['Utilities'] = [
            'Date' => $this->date,
            'Time' => $this->time,
            'Quantity' => $this->quantity
        ];

        $orderFactory = new OrderFactory();
        $orderFactory->makeOrder($orderData);
        $order = $orderFactory->getOrder();


        try {
            $response = $this->client->post('Order/previewOrder', [
                'query' => $query,
                'json' => [
                    'Order' => $order
                ]
            ]);

            $this->order = json_decode($response->getBody(), 1);

        } catch (\Exception $exception) {

            throw new \Exception('Order is not valid: ' . $exception->getMessage());

        }

        return $this->getPaymentForm();

    }

    public function getPaymentForm()
    {
        $query = [
            'key' => Utilities::KEY,
            'type' => 130
        ];

        $this->order['Order']['PaymentList'][0]['PaymentAmount'] = (string)$this->order['Order']['Amount'];
        $this->order['Order']['PaymentList'][0]['PaymentModeExternalCode'] = Utilities::PAYMENT_MODE_EXTERNAL_CODE;
        $this->order['Order']['PaymentList'][0]['PaymentDateTime'] = now()->format('Y-m-d\TH:i:s');


        try {
            $payment_form = $this->client->post('Payment/GetPaymentForm', [
                'query' => $query,
                'json' => $this->order
            ]);

            $payment_form = json_decode($payment_form->getBody(), 1);

        }catch (\Exception $exception){

            throw new \Exception('GetPaymentForm invalid: '. $exception->getMessage());

        }

        return [
            'Form' => $payment_form['Form'],
            'Token' => $payment_form['Token'],
            'BasketId' => $this->order['Order']['BasketId'],
            'Amount' => $this->order['Order']['Amount'],
            'Order' => $this->order['Order']['ProductList']
        ];

    }

    public function parseForm($formData)
    {
        $form = [];
        parse_str($formData, $form);

        $this->date = $form['date'];
        $this->time = $form['time'];
        $this->quantity = $form['quantity'];
    }


}
