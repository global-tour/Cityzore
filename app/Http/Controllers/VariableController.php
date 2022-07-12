<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VariableController extends Controller
{
    public static $months = ["01" => "January", "02" => "February", "03" => "March", "04" => "April", "05" => "May", "06" => "June", "07" => "July", "08" => "August", "09" => "September", "10" => "October", "11" => "November", "12" => "December"];
    public static $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];

    public static function returnMonths() {
        return static::$months;
    }

    public static function returnDays() {
        return static::$days;
    }
}
