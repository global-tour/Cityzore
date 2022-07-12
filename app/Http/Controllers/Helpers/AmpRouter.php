<?php

namespace App\Http\Controllers\Helpers;


use Illuminate\View\Factory;


class AmpRouter
{

    /**
     * @param null $view
     * @param array $data
     * @param array $mergeData
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function view($view = null, $data = [], $mergeData = [])
    {
        $factory = app(Factory::class);

        if (func_num_args() == 0) {
            return $factory;
        }

        //if amp, add '-amp' to view name
        if (request()->segment(1) == 'amp') {
            if (view()->exists('frontend.amp.' . explode('.', $view)[1] . '-amp')) {
                $view = 'frontend.amp.' . explode('.', $view)[1] . '-amp';
            } else {
                abort(404);
            }
        }
        return $factory->make($view, $data, $mergeData);
    }

}
