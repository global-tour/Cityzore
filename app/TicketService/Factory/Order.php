<?php

namespace App\TicketService\Factory;

use App\TicketService\Utilities;

class Order
{

    public $basketId;

    public $customerId = Utilities::CUSTOMER_ID;

    public $externalCode;

    public $isBooking = false;

    public $sendDocument = true;

    /**
     * @var array<Payment>
     */
    public $paymentList;

    /**
     * @var array<Product>
     */
    public $productList;

    public function makeProduct()
    {
        return $this->productList[] = new Product();
    }

    public function makePayment()
    {
        return $this->paymentList[] = new Payment();
    }

}
