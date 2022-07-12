<?php

namespace App\Http\Controllers\PaymentServices;

use GuzzleHttp\Client;

class ZiraatAuthorization
{

    protected $client_id;

    protected $islemtipi;

    protected $amount;

    protected $oid;

    protected $okUrl;

    protected $failUrl;

    protected $rnd;

    protected $hash;

    protected $storetype;

    protected $lang;

    protected $currency;

    protected $pan;

    protected $cv2;

    protected $expYear;

    protected $expMonth;

    protected $cardType;

    protected $client;

    protected $storeKey;

    protected $data;

    protected $baseUrl;

    protected $storekey;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function before3d()
    {
        $this->data = "clientid=". $this->client_id .
            "&storetype=". $this->storetype .
            "&hash=". $this->hash .
            "&islemtipi=" . $this->islemtipi .
            "&amount=" .  $this->amount .
            "&currency=" . $this->currency .
            "&oid=" . $this->oid .
            "&okUrl=" . $this->okUrl .
            "&failUrl=" . $this->failUrl .
            "&lang=" . $this->lang .
            "&pan=" . $this->pan .
            "&Ecom_Payment_Card_ExpDate_Year=". $this->expYear .
            "&Ecom_Payment_Card_ExpDate_Month=" . $this->expMonth;

    }

    public function getData()
    {
        return $this->data;
    }

    public function charge()
    {
        try {

            $d = [
                'clientid' => $this->client_id,
                'storetype' => $this->storetype,
                'hash' => $this->hash,
                'islemtipi' => $this->islemtipi,
                'amount' => $this->amount,
                'currency' => $this->currency,
                'oid' => $this->oid,
                'okUrl' => $this->okUrl,
                'failUrl' => $this->failUrl,
                'lang' => $this->lang,
                'pan' => $this->pan,
                'Ecom_Payment_Card_ExpDate_Year' => $this->expYear,
                'Ecom_Payment_Card_ExpDate_Month' => $this->expMonth
            ];
            
            $response = $this->client->post($this->baseUrl, [
                'form_params' => $d
            ]);

            return $response->getBody();

        } catch (\Exception $exception) {

            dump('error');
            dd($exception->getMessage());
        }
    }


    /**
     * @param mixed $client_id
     */
    public function setClientId($client_id): void
    {
        $this->client_id = $client_id;
    }

    /**
     * @param mixed $islemtipi
     */
    public function setIslemtipi($islemtipi): void
    {
        $this->islemtipi = $islemtipi;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @param mixed $oid
     */
    public function setOid($oid): void
    {
        $this->oid = $oid;
    }

    /**
     * @param mixed $okUrl
     */
    public function setOkUrl($okUrl): void
    {
        $this->okUrl = $okUrl;
    }

    /**
     * @param mixed $failUrl
     */
    public function setFailUrl($failUrl): void
    {
        $this->failUrl = $failUrl;
    }

    /**
     * @param mixed $rnd
     */
    public function setRnd($rnd): void
    {
        $this->rnd = $rnd;
    }


    public function setHash()
    {
        $hash = $this->client_id . $this->oid . $this->amount . $this->okUrl .$this->failUrl . $this->rnd . $this->storeKey;

        $this->hash = base64_encode(pack('H*', sha1($hash)));
    }

    /**
     * @param mixed $storetype
     */
    public function setStoretype($storetype): void
    {
        $this->storetype = $storetype;
    }

    /**
     * @param mixed $lang
     */
    public function setLang($lang): void
    {
        $this->lang = $lang;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @param mixed $pan
     */
    public function setPan($pan): void
    {
        $this->pan = $pan;
    }

    /**
     * @param mixed $cv2
     */
    public function setCv2($cv2): void
    {
        $this->cv2 = $cv2;
    }

    /**
     * @param mixed $expYear
     */
    public function setExpDate($expYear): void
    {
        $this->expYear = $expYear;
    }

    /**
     * @param mixed $expMonth
     */
    public function setExpMonth($expMonth): void
    {
        $this->expMonth = $expMonth;
    }

    /**
     * @param mixed $cardType
     */
    public function setCardType($cardType): void
    {
        $this->cardType = $cardType;
    }

    /**
     * @param mixed $storeKey
     */
    public function setStoreKey($storeKey): void
    {
        $this->storeKey = $storeKey;
    }

    /**
     * @param mixed $baseUrl
     */
    public function setBaseUrl($baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }
}
