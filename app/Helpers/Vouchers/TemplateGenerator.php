<?php

namespace App\Helpers\Vouchers;

use Illuminate\Support\Facades\Storage;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;

class TemplateGenerator
{

    public $replacementClasses;
    public $dom;
    public $loopDom;
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
        $this->replacementClasses = [
            "dom-replace-reference-no",
            "dom-replace-traveler-name",
            "dom-replace-traveler-lastname",
            "dom-replace-main-loop-import-wrap",
            "dom-replace-product-image-wrap",
            "dom-replace-product-image",
            "dom-replace-logo-wrap",
            "dom-replace-logo",
            "dom-replace-product-title",
            "dom-replace-ticket-type-name",
            "dom-replace-option-title",
            "dom-replace-barcode-image",
            "dom-replace-code",
            "dom-replace-booking-date-time",
            "dom-replace-booking-hour",
            "dom-replace-category-name-count",
            "dom-replace-muse-delarme-price",
            "dom-replace-paginate",
            "dom-replace-address-information",
            "dom-replace-important-information",
            "dom-replace-special-ref-code",
            "dom-replace-cancel-policy",
            "dom-replace-what-is-includes",
            "dom-replace-what-is-not-includes",
            "dom-replace-contact-information",
            "dom-replace-map-wrap",
            "dom-replace-map-image",
            "dom-replace-barcode-image-right",
            "dom-replace-opera-national-price",
            "dom-replace-booking-date-and-time",
            "dom-replace-travel-date-and-time",
            "dom-replace-mobil-barcode-image",
            "dom-replace-restaurant-reservation-code",
            "dom-replace-cruise-barcode-area-wrap",
        ];
    }

    public function run()
    {
        $loopHTML = '';
        $this->dom = $this->createDom((new Options())
            ->setRemoveStyles(false));
        $this->dom->loadStr($this->data['template']['en']);

        $referenceDom = $this->dom->find('.dom-replace-reference-no');
        $travelerNameDom = $this->dom->find('.dom-replace-traveler-name');
        $travelerLastNameDom = $this->dom->find('.dom-replace-traveler-lastname');

        foreach ($referenceDom as $item) {
            if ($this->data['booking']->gygBookingReference == null) {
                $item->firstChild()->setText($this->data['bkn']);
            } else {
                $item->firstChild()->setText($this->data['booking']->gygBookingReference);
            }
        }

        foreach ($travelerNameDom as $item) {
            $item->firstChild()->setText($this->data['travelerName']);
        }
        foreach ($travelerLastNameDom as $item) {
            $item->firstChild()->setText($this->data['travelerLastname']);
        }
        //unset($this->replacementClasses['dom-replace-reference-no']);
        //unset($this->replacementClasses['dom-replace-traveler-name']);
        //unset($this->replacementClasses['dom-replace-traveler-lastname']);

        $this->loopDom = $this->dom->find('.dom-replace-main-loop-import-wrap')[0];
     if($this->dom->find('.dom-replace-main-loop-import-wrap')->count()){
         for ($i=0; $i<$this->data['participantSum']; $i++){
             foreach ($this->replacementClasses as $key => $className) {
                 $this->workerByClassName($className);
             }
             $loopHTML .= $this->loopDom->innerHtml;
         }
     }else{
         foreach ($this->replacementClasses as $className) {
             $this->workerByClassName($className);
         }
         $loopHTML .= $this->loopDom->innerHtml;
     }

      $this->loopDom->firstChild()->setText($loopHTML);


        return $this->dom;
    }

    protected function createDom($options = false)
    {
        $dom = new Dom();
        if ($options) {
            $dom->setOptions(
            // this is set as the global option level.
                $options
            );
        }
        return $dom;

    }

    protected function workerByClassName($class)
    {
        switch ($class) {
            case 'dom-replace-product-image':
                if($this->loopDom->find(".".$class)){
                    if($this->data['ticketType'] || (($this->data['ticketType'] === null) && ($this->data['booking']->gygBookingReference == null) && $this->data['booking']->isBokun == 0 && $this->data['booking']->isViator == 0)){
                        if(count($this->data['productImage'])){
                            $this->loopDom->find(".".$class)[0]->getTag()->setAttribute('src',Storage::disk('s3')->url('product-images/' . $this->data['productImage'][0]) );
                        }else{
                            $elem = $this->loopDom->find(".".$class)[0];
                            $elem->delete();
                            unset($elem);
                            unset($this->replacementClasses[array_search($class, $this->replacementClasses)]);
                        }
                    }else{ //dd($this->data);

                        $ch = $this->loopDom->find('.dom-replace-product-image-wrap')[0];
                        $ch->delete();
                        unset($this->replacementClasses[array_search($class, $this->replacementClasses)]);
                        unset($this->replacementClasses[array_search('dom-replace-product-image-wrap', $this->replacementClasses)]);

                    }

                }

                break;

            case 'dom-replace-logo':

                break;

            case '':

                break;


            case '':

                break;


            case '':

                break;


            case '':

                break;


            case '':

                break;


            case '':

                break;


            case '':

                break;


            case '':

                break;


            case '':

                break;


            case '':

                break;


            case '':

                break;


            case '':

                break;

            case '':

                break;

            case '':

                break;


            case '':

                break;

            case '':

                break;

            case '':

                break;

            case '':

                break;

            case '':

                break;


            case '':

                break;


            case '':

                break;

            default:
        }
    }
}
