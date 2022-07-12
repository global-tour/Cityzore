<?php

namespace App\Observers;

use App\Adminlog;
use App\ProductGallery;
use Illuminate\Support\Facades\Auth;

class ProductGalleryObServer
{
    /**
     * Handle the product gallery "created" event.
     *
     * @param  \App\ProductGallery  $productGallery
     * @return void
     */
    public function created(ProductGallery $productGallery)
    {
        if (Auth::guard('admin')->check())
        {
            $log = new Adminlog();
            $log->userID = Auth::guard('admin')->user()->id();
            $log->page = 'Gallery';
            $log->url = 'http://admin.cityzore.com/gallery/create';
            $log->action = 'Upload Image';
            $log->details = Auth::guard('admin')->user()->name.' Uploaded her picture '.$productGallery->src;
            $log->tableName = 'product_gallerys';
            $log->result = 'successful';
            $log->save();

        }
    }

    /**
     * Handle the product gallery "updated" event.
     *
     * @param  \App\ProductGallery  $productGallery
     * @return void
     */
    public function updated(ProductGallery $productGallery)
    {
        //
    }

    /**
     * Handle the product gallery "deleted" event.
     *
     * @param  \App\ProductGallery  $productGallery
     * @return void
     */
    public function deleted(ProductGallery $productGallery)
    {
        //
    }

    /**
     * Handle the product gallery "restored" event.
     *
     * @param  \App\ProductGallery  $productGallery
     * @return void
     */
    public function restored(ProductGallery $productGallery)
    {
        //
    }

    /**
     * Handle the product gallery "force deleted" event.
     *
     * @param  \App\ProductGallery  $productGallery
     * @return void
     */
    public function forceDeleted(ProductGallery $productGallery)
    {
        //
    }
}
