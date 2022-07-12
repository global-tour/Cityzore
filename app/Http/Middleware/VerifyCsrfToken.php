<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'http://admin.localhost:8000/blog/create/uploadImageForBlogPost',
        'http://admin.cityzore.com/blog/create/uploadImageForBlogPost',
        'https://www.cityzore.com/getAvailableDatesNew',
        'https://www.cityzore.com/booking-successful',
        'https://www.cityzore.com/booking-failed',
        '/guide/planning/cal_quicksave',
        '/guide/planning/cal_description',
        '/guide/planning/cal_check_rep_events',
        '/guide/planning/cal_edit_update',
        '/guide/planning/cal_delete',
        '/guide/planning/cal_update',
        '/guide/planning/importer',
        '/guide/planning/loader',
        '/external-payment-successful',
        '/external-payment-failed',
    ];
}
