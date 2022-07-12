<?php

namespace App\TicketService\Factory;

class Product
{

    public $barcodeList = [
        [
            'Barcode' => '',
            'Nomination' => ''
        ]
    ];

    public $ConsumptionDate;

    public $eventId;

    public $productId;

    public $quantity;

    /**
     * @return \string[][]
     */
    public function getBarcodeList(): array
    {
        return $this->barcodeList;
    }

    /**
     * @return mixed
     */
    public function getConsumptionDate()
    {
        return $this->ConsumptionDate;
    }

    /**
     * @param mixed $ConsumptionDate
     */
    public function setConsumptionDate($ConsumptionDate): void
    {
        $this->ConsumptionDate = $ConsumptionDate;
    }

    /**
     * @return mixed
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @param mixed $eventId
     */
    public function setEventId($eventId): void
    {
        $this->eventId = $eventId;
    }

    /**
     * @return mixed
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param mixed $quantity
     */
    public function setQuantity($quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param mixed $productId
     */
    public function setProductId($productId): void
    {
        $this->productId = $productId;
    }

}
