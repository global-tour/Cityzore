<?php
namespace App;
use Illuminate\Database\Eloquent\Model;


class Option extends Model
{

    protected $table = 'options';

    protected $fillable = ["bigBusID"];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'option_product');
    }

    public function pricings()
    {
        return $this->belongsToMany(Pricing::class, 'option_pricing');
    }

    public function pricingsRel()
    {
        return $this->belongsToMany(Pricing::class, 'option_pricing');
    }

    public function supplier()
    {
        return $this->belongsToMany(Supplier::class, 'option_supplier');
    }

    public function bookings()
    {
        return $this->belongsTo(Booking::class);
    }

    public function avs()
    {
        return $this->belongsToMany(Av::class, 'option_av');
    }

    public function tootbus(){
        return $this->hasOne(TootbusConnection::class);
    }

    public function meetings(){
        return $this->hasMany(Meeting::class, 'option', 'referenceCode');
    }

    public function translations()
    {
        $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
        $langID = \App\Language::where('code', $langCode)->first()->id;
        return $this->hasOne(OptionTranslation::class, 'optionID')->where('languageID', $langID);
    }

    public function ticket_types()
    {
        return $this->belongsToMany(TicketType::class, 'option_ttype');
    }

    public function bigBus()
    {
        return $this->hasOne(Bigbus::class, 'option_id', 'id');
    }

}
