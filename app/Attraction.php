<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Attraction extends Model
{

    protected $table = 'attractions';

    protected $fillable = [
        'id', 'name', 'description', 'tags', 'slug', 'minPrice', 'cities', 'pageID', 'image', 'isActive',
    ];

    protected $appends = [
        'min_price'
    ];

    public function getAttractionNames($idArr)
    {
        $attractions = json_decode($idArr, true);
        $attractionNames = '';
        foreach ($attractions as $ind => $att) {
            $attObj = \App\Attraction::findOrFail($att);
            $attractionNames .= $attObj->name;
            if ($ind != 0 && (count($attractions) - 1) != $ind) {
                $attractionNames .= ', ';
            }
        }

        return $attractionNames;
    }

    /**
     * En uygun fiyatı döndürür
     *
     * @return string
     */
    private function getAllPrices(): string
    {
        $pricesArray = [];

        $allPriceQuery = DB::select(DB::raw("SELECT `adultPrice` FROM `pricings` WHERE id IN (SELECT pricing_id FROM `option_pricing` WHERE option_id IN(SELECT option_id FROM `option_product` WHERE product_id IN (SELECT id FROM products WHERE `attractions` LIKE '%\"$this->id\"%' AND `isPublished` = 1 AND `isDraft` = 0)))"));

        foreach ($allPriceQuery as $item) {
            $pricesArray[] = json_decode($item->adultPrice, true)[0];
        }

        return count($pricesArray) ? min($pricesArray) : 0.00;


    }

    public function translations()
    {
        $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
        $langID = \App\Language::where('code', $langCode)->first()->id;
        return $this->hasOne(AttractionTranslation::class, 'attractionID')->where('languageID', $langID);
    }

    /**
     * En uygun fiyatı min_price attr e tanımlar.
     *
     * @return string
     */
    public function getMinPriceAttribute()
    {
        return $this->appends['min_price'] = $this->getAllPrices();
    }


}
