<?php
namespace App\Http\Controllers\Admin;

use App\Av;
use App\Barcode;
use App\Booking;
use App\Cart;
use App\Currency;
use App\Http\Controllers\Controller;
use App\Option;
use App\Supplier;
use App\TicketType;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Config;
use PhpParser\Node\Expr\Array_;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PanelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function dashboard()
    {
        $currentCurrency = Currency::select('value', 'iconClass')->where('id', session()->get('currencyCode'))->first();
        $incomeMonth = Booking::with(['currency'])->where('created_at', '>=', Carbon::now()->startOfMonth())->get();

        /*
         * Monthly Total Earning
         */
        $totalEarning = $incomeMonth->sum(function($d) use ($currentCurrency){
            return  round(floatval($d->totalPrice) * floatval($d->currency->value) / floatval($currentCurrency->value), 2) ;
        });

        /*
         * Cart Bought Percentage
         */
        $boughtPercentage = Cart::boughtPercentage();

        /*
         * Cart Canceled Percentage
         */
        $canceledPercentage = Cart::canceledPercentage();

    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function adminPanel()
    { 
        $cacheInformations = Storage::disk('public')->exists('page_cache.json') ? json_decode(Storage::disk('public')->get('page_cache.json'), true) : [];
        $cacheTime = ($checkCacheable = array_key_exists('dashboard', $cacheInformations)) ? $cacheInformations['dashboard']['cache_time']*3600 : 3600;
        if(Cache::has('dashboardStatistics') && $checkCacheable){
           
            $viewData = Cache::get('dashboardStatistics');
        }
        else{
        $currencies = Currency::where('isActive',1)->select('id','value')->pluck('value','id')->toArray();
        $s=date("Y-m-d H:i:s", time()-(60*60*24*30));
        $e=date("Y-m-d H:i:s", time());
        $config = Config::where('userID', -1)->first();
        $cartCount = Cart::count();
        $userCount = User::count();
        $supplierCount = Supplier::count();
        $totalAmount = 0.00;
        $bookingsLast5 = Booking::select('travelers','gygBookingReference','totalPrice','created_at','status','isBokun','currencyID','bookingRefCode')->orderBy('id','desc')->take(5)->get();
        $totalPrice = 0;
        $cartPercentage = '%0';
        $cartBought = Cart::whereIn('status',[2,3])->count();
        $totalPricesForOption = [];
        $bookingPercentage = '%0';
        $gygPercentage = '%0';
        // $barcodes=Barcode::all();
        $ticketTypes=TicketType::select('id','name','warnTicket')->where('usableAsTicket',1)->where('warnTicket','>',0)->get();
        $ticketArray=Array();
        $ticketIds = $ticketTypes->map(function($ticket){
            return $ticket->id;
        })->toArray();
        $barcodeGroups = Barcode::groupBy('ticketType')->selectRaw('COUNT(id) as totalCount, ticketType')->whereIn('ticketType', $ticketIds)->where('isUsed',0)->where('isExpired',0)->where('ownerID', -1)->pluck('totalCount', 'ticketType')->toArray();
        
        foreach($ticketTypes as $ticketType){
            $temp['ticket_type_id']=intval($ticketType->id);
            $temp['ticket_type_name']=$ticketType->name;
            $temp['warnTicket']=intval($ticketType->warnTicket);
            $temp['count']= $barcodeGroups[$temp['ticket_type_id']] ?? 0;
            $temp['diff']=$temp['count']-$temp['warnTicket'];
            $temp['diff'] <11 ? array_push($ticketArray,$temp):null;
        }
       
        $types=array_column($ticketArray,'diff');
        array_multisort($types,SORT_ASC,$ticketArray);
         

        // Total earnings for last 1 month
        $euroC=floatval(Currency::where('id',2)->value('value'));
        $defIcon=Currency::where('id',2)->value('iconClass');
        $sumTotalGroups = Booking::groupBy('currencyID')->selectRaw('SUM(totalPrice) AS tempTotal, currencyID')->whereIn('currencyID',array_keys($currencies))->whereBetween('created_at',[$s,$e])->pluck('tempTotal', 'currencyID');
        foreach ($sumTotalGroups as $currencyId => $tempTotal){
            $tempTotal=floatval($tempTotal);
            if($tempTotal>0) $totalAmount+=round(($tempTotal*floatval($currencies[$currencyId]))/$euroC,2);
        }
        
        // 5 options informations that have maximum booking counts.
        $result = DB::select(
            DB::raw(" select (select title from options where referenceCode = bookings.optionRefCode) as title, optionRefCode,  COUNT(optionRefCode) AS totalCount from `bookings` WHERE status IN (0,1) GROUP BY optionRefCode ORDER BY totalCount DESC LIMIT 5 "));
        //usort($result, function($a, $b) {return ( $b->totalCount - $a->totalCount);});
        //$result = array_slice($result, 0, 5, true); 
        $mapped_refcodes = array_map(function($r){
            if (Option::where('referenceCode', '=', $r->optionRefCode)->count()) {
                return $r->optionRefCode;
            }
            return false;
         },$result);
        $mapped_refcodes = array_filter($mapped_refcodes);

        $bookings = Booking::groupBy('optionRefCode')->selectRaw('SUM(totalPrice) AS total_price, optionRefCode')->whereIn('optionRefCode', $mapped_refcodes)->pluck('total_price', 'optionRefCode');
         
         foreach ($bookings as $refCode => $totalPrice) {
             $totalPricesForOption[$refCode] = [];
            array_push($totalPricesForOption[$refCode], ['optionRefCode' => $refCode, 'totalPrice' => floatval($totalPrice)]);
         }
     
        $allbookingCounts = DB::select(
            DB::raw(" select (select COUNT(id) from bookings  where gygBookingReference is not null) as gygBookingsCount,(select COUNT(id) from bookings where status IN (2,3)) as cancelledBookingsCount, COUNT(id) AS bookingsCount from `bookings`  LIMIT 1 "));
        // Calculating percentage between cancelled bookings and all bookings.
        $bookingsCount = $allbookingCounts[0]->bookingsCount;
         $cancelledBookingsCount = $allbookingCounts[0]->cancelledBookingsCount; 
        if ($cancelledBookingsCount > 0) {
            $bookingPercentage = '% '.number_format(($cancelledBookingsCount / $bookingsCount) * 100, 2, '.', '');
        }

        // Calculating percentage between GYG bookings and all bookings.
        $gygBookingsCount = $allbookingCounts[0]->gygBookingsCount;

        if ($gygBookingsCount > 0) {
            $gygPercentage = '% '. number_format(($gygBookingsCount / $bookingsCount * 100), 2 , '.', '');
        }

        // Calculating percentage between bought carts and total carts.
        if ($cartCount > 0) {
            $cartPercentage = '% '.number_format(($cartBought / $cartCount) * 100, 2, '.', '');
        }

        $viewData = [
            'totalAmount' => $totalAmount,
            'cartPercentage' => $cartPercentage,
            'supplierCount' => $supplierCount,
            'userCount' => $userCount,
            'bookingsLast5' => $bookingsLast5,
            'result' => $result,
            'totalPricesForOption' => $totalPricesForOption,
            'totalPrice' => $totalPrice,
            'bookingPercentage' => $bookingPercentage,
            'gygPercentage' => $gygPercentage,
            'config' => $config,
            'defIcon' => $defIcon,
            'ticketArray' => $ticketArray,
            'mapped_refcodes' => $mapped_refcodes
        ];
        
        
       if($checkCacheable){
        Cache::forget('dashboardStatistics');
        Cache::put('dashboardStatistics', $viewData, $cacheTime);
       }
        
      }


        return view('panel.dashboard', $viewData);
    }
    public function availabilityDate()
    {
        $availability = Av::where('id', 1)->first();
        $hourly = json_decode($availability->hourly, true);
        $startTimesTemporary = [];
        $now = Carbon::now();
        foreach ($hourly as $h) {
            $startDay = $h['day'];
            $availabilityDate = Carbon::createFromFormat("d/m/Y", $startDay);
           // $availabilityDate->toDateTimeString();
            if($now->timestamp < $availabilityDate->timestamp) {
                array_push($startTimesTemporary, ["day" => $startDay]);
            }
        }
        return view('panel.availability', ['availability' => $availability, 'startTimesTemporary' => $startTimesTemporary] );
    }

    public function closeWindow() {
        return view('panel.close-window');
    }

}
