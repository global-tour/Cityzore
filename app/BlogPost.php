<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $table = 'blog_posts';

    public function metaTag()
    {
        return $this->belongsToMany(MetaTag::class, 'blogpost_metatag', 'blogpost_id', 'metatag_id');
    }

    public function blogCategory()
    {
        return $this->belongsTo('App\Category', 'category', 'id');
    }

      public function translations()
    {
        $langCode = session()->has('userLanguage') ? session()->get('userLanguage') : 'en';
        $langID = \App\Language::where('code', $langCode)->first()->id;
        return $this->hasOne(BlogTranslation::class, 'blogID', 'id')->where('languageID', $langID);
    }

    public function gallery()
    {
        return $this->hasOne(BlogGallery::class, 'id', 'coverPhoto');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'blogPostID', 'id');
    }

}
