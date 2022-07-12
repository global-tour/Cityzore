<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [

        'title', 'shortDesc', 'fullDesc', 'country', 'city', 'countryCode', 'phoneNumber', 'highlights', 'included', 'notIncluded',
        'knowBeforeYouGo', 'foodAndDrink', 'tags', 'options', 'category', 'supplierID', 'supplierPublished', 'referenceCode', 'cancelPolicy'

    ];

    public function options()
    {
        return $this->belongsToMany(Option::class, 'option_product');
    }

    public function prodOpt()
    {
        return $this->belongsToMany(Option::class, 'option_product');
    }

    public function images()
    {
        return $this->hasMany('App\ProductGallery');
    }

    public function supplier()
    {
        return $this->belongsToMany(Supplier::class);
    }

    public function country()
    {
        return $this->belongsTo('App\Country', 'countryCode', 'countries_phone_code');
    }

    public function attractions()
    {
        return $this->belongsTo('App\Attraction');
    }

    public function productGalleries()
    {
        return $this->belongsToMany(ProductGallery::class);
    }

    public function metaTag()
    {
        return $this->belongsToMany(MetaTag::class, 'metatag_product');
    }

    public function countryName()
    {
        return $this->belongsTo('App\Country', 'country', 'id');
    }

    public function productCover()
    {
        return $this->belongsTo('App\ProductGallery', 'coverPhoto', 'id');
    }

    public function copies()
    {
        return $this->hasMany(Product::class, 'copyOf');
    }

    public function translations()
    {
        $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
        $langID = \App\Language::where('code', $langCode)->first()->id;
        return $this->hasOne(ProductTranslation::class, 'productID')->where('languageID', $langID);
    }

    public function specialOffers()
    {
        return $this->hasMany(SpecialOffers::class, 'productID', 'id');
    }

}
