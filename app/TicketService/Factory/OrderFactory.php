<?php

namespace App\TicketService\Factory;

use App\TicketService\Utilities;

class OrderFactory
{
    public $_orderList;

    public function makeOrder($orderData)
    {
        $order = new Order();

        $order->basketId = $orderData['Order']['BasketId'];
        $order->externalCode = (string)rand(1111111111, 9999999999);

        foreach ($orderData['Product'] as $item){
            $order1 = $order->makeProduct();
            $order1->setConsumptionDate($item['StartDateTime']);
            $order1->setEventId($item['EventId']);
            $order1->setQuantity($orderData['Utilities']['Quantity']);
            $order1->setProductId(Utilities::PRODUCT_ID);
        }

        $order2 = $order->makePayment();
        $order2->IsDeferred = true;
        $order2->PaymentAmount = '0.00';
        $order2->PaymentBankingTransactionId = null;
        $order2->PaymentDateTime = now()->format('Y-m-d\TH:i:s');
        $order2->PaymentModeExternalCode = '';


        return $this->_orderList = $order;

    }

    public function getOrder()
    {
        return $this->_orderList;
    }



}
