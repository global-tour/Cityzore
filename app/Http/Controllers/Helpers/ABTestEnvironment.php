<?php

namespace App\Http\Controllers\Helpers;

use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ABTestEnvironment extends Controller
{

    /**
     * @param $testVariable1
     * @param $testVariable2
     * @return mixed
     */
    public function splitRedirection($testVariable1, $testVariable2)
    {
        if (is_null(session()->get('ABRandomValue'))) {
            $rand = rand(1,2);
            session()->put('ABRandomValue',$rand);
        } else {
            $rand = session()->get('ABRandomValue');
        }

        switch ($rand) {
            case 1:
                return $testVariable1;
                break;
            case 2:
                return $testVariable2;
                break;
        }
    }
}
