<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FAQTranslation extends Model
{
    protected $table = 'faq_translations';

    protected $fillable = [
        'faqID', 'question', 'answer', 'languageID'
    ];
}
