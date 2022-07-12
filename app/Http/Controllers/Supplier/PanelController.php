<?php

namespace App\Http\Controllers\Supplier;

use App\Booking;
use App\Cart;
use App\Option;
use App\Supplier;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Config;

class PanelController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:supplier,subUser');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function supplierPanel()
    {
        // variables
        $config = Config::where('userID', auth()->user()->id)->first();

        if (Auth::guard('supplier')->check()) {
            $user = auth()->guard('supplier')->user();
            $options = Option::where('supplierID', '=', $user->id)->get();

            $suppliers = Supplier::all();
            $users = User::all();
            $userCount = count($users);
            $supplierCount = count($suppliers);
            $totalAmount = 0.00;
            $totalPrice = 0;
            $cartPercentage = '%0';
            $cartBought = [];
            $totalPricesForOption = [];
            $result = [];
            $bookingPercentage = '%0';
            $gygPercentage = '%0';
            $bookingsLast5 = [];

            foreach ($options as $opt) {
                $bookings = Booking::where('companyID', '=', $user->id)->get();
                $bookingsLast5 = $bookings->sortByDesc('created_at')->take(5);
                $cart = Cart::where('optionID', '=', $opt->id)->get();
                // Total earnings for last 1 month
                if ($bookings) {
                    foreach ($bookings as $ind => $booking) {
                        $createdAtAtomic = strtotime($booking->created_at);
                        $now = strtotime('now');
                        if ($createdAtAtomic + (30 * 24 * 60 * 60) >= $now) {
                            $bookingTotalPrice = $config->calculateCurrency($booking->totalPrice, $config->currencyName->value, $booking->currencyID);
                            $totalAmount += number_format($bookingTotalPrice, 2, '.', '');
                        }
                        // Calculating percentage between cancelled bookings and all bookings.
                        $cancelledBookings = DB::select(
                            DB::raw("select * from bookings where status = 2 or status = 3"));
                        if (count($cancelledBookings) > 0) {
                            $bookingPercentage = '% '.number_format((count($cancelledBookings) / count($bookings)) * 100, 2, '.', '');
                        }

                        // 5 options informations that have maximum booking counts.
                        $result = DB::select(
                            DB::raw(" select * from (select optionRefCode, COUNT(optionRefCode) as totalCount
                from bookings where status = 1 or status =  0 group by optionRefCode)a where  a.totalCount > 0 "));
                        usort($result, function($a, $b) {return ( $b->totalCount - $a->totalCount);});
                        foreach ($result as $r) {
                            $booking = Booking::where('optionRefCode', '=', $r->optionRefCode)->get();
                            foreach ($booking as $b) {
                                $bookingTotalPrice = $config->calculateCurrency($b->totalPrice, $config->currencyName->value, $b->currencyID);
                                $totalPrice += $bookingTotalPrice;
                            }
                            $totalPricesForOption[$r->optionRefCode] = [];
                            array_push($totalPricesForOption[$r->optionRefCode], ['optionRefCode' => $r->optionRefCode, 'totalPrice' => $totalPrice]);
                        }
                        // Calculating percentage between GYG bookings and all bookings.
                        $gygBookings = DB::select(DB::raw("select * from bookings where gygBookingReference is not null"));
                        if (count($gygBookings) > 0) {
                            $gygPercentage = '% '. number_format((count($gygBookings) / count($bookings) * 100), 2 , '.', '');
                        }
                    }
                    // Calculating percentage between bought carts and total carts.

                    if ($cart) {
                        foreach ($cart as $c) {
                            if ($c->status == 2 || $c->status == 3) {
                                array_push($cartBought, $c);
                            }
                        }
                        if (count($cart) > 0) {
                            $cartPercentage = '% '.number_format((count($cartBought) / count($cart)) * 100, 2, '.', '');
                        }
                    }
                }
            }
        } elseif (Auth::guard('subUser')->check()  || Auth::guard('supplier')->check()) {
            $totalAmount = 0;
            $cartPercentage = 0;
            $supplierCount = 0;
            $userCount = 0;
            $bookingsLast5 = [];
            $result = [];
            $totalPricesForOption = [];
            $bookingPercentage = 0;
            $gygPercentage = 0;
            $totalPrice = 0;
        }

        return view('panel.dashboard', [
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
            'config' => $config
            ]
        );
    }

    public function closeWindow() {
        return view('panel.close-window');
    }

}
