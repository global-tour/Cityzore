<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $table = 'categories';

       public function translations()
    {
        $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
        $langID = \App\Language::where('code', $langCode)->first()->id;
        return $this->hasOne(CategoryTranslation::class, 'categoryID')->where('languageID', $langID);
    }

}
