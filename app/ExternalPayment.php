<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExternalPayment extends Model
{
    /**
     * ISO code to string
     */
    const CURRENCY = [
        '978' => 'EUR',
        '949' => 'TRY',
        '826' => 'GBP',
        '840' => 'TRY',
    ];

    protected $table = 'external_payments';

    /**
     * @var string[]
     * currency code mutator
     */
    protected $appends = [
        'currency_code'
    ];

    /**
     * @return string
     * Assign asset value to currency
     */
    protected function getCurrencyCodeAttribute(): ?string
    {
        return $this->appends['currency_code'] = self::CURRENCY[$this->currency];
    }

}
