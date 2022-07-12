<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    protected $table = 'faqs';

    public function categoryName()
    {
        return $this->belongsTo('App\FaqCategory', 'category', 'id');
    }

    public function translate()
    {
        $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
        $langID = \App\Language::where('code', $langCode)->first()->id;
        return $this->hasOne(FAQTranslation::class, 'faqID')->where('languageID', $langID);
    }

    public function translations()
    {
        return $this->hasMany(FAQTranslation::class, 'faqID');
    }
}
