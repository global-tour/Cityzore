<?php

namespace App\Http\Controllers\Helpers;

use App\OldProduct;
use App\OldProductTranslation;
use App\Product;
use App\ProductTranslation;
use App\Supplier;
use Cocur\Slugify\Slugify;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class TemporaryFunctions extends Controller
{

    /**
     * @return string
     */
    public function renamedSuleymanTours()
    {
        $products = Product::where('title', 'like', '%Suleyman%')->get();
        $code = 'PTT58694';
        foreach ($products as $product) {
            $productTitle = explode('Suleyman', $product->title)[1];
            $productTitle = $productTitle . '-' . $code;
            $product->title = $productTitle;
            $product->save();
        }

        return 'done';
    }

    /**
     * @return string
     */
    public function editUrlOfSuleymanTours()
    {
        $products = Product::where('title', 'like', '%PTT58694%')->get();
        foreach ($products as $product) {
            $productURL = explode('suleyman-', $product->url);
            $productURL = $productURL[0].$productURL[1].'-ptt58694';
            $product->url = $productURL;
            $product->save();
        }
        return 'done';
    }

    /**
     * @return string
     */
    public function translateUrlFields()
    {
        $pts = ProductTranslation::where('title', '!=', null)->get();
        foreach ($pts as $pt) {
            $product = Product::findOrFail($pt->productID);
            $slugify = new Slugify();
            $url = Str::slug(strtolower($product->city), '-') . '/' . $slugify->slugify($pt->title) . '-' . $pt->productID;
            $pt->url = $url;
            $pt->save();
        }

        return 'done';
    }

    /**
     * @return string
     */
    public function replaceProductTranslationUrls()
    {
        // add -{productID} to all urls
        $pTranslations = ProductTranslation::all();

        foreach ($pTranslations as $pTranslation) {
            $pTranslation->url = $pTranslation->url . '-' . $pTranslation->productID;
            $pTranslation->save();
        }

        return 'done';
    }

    /**
     * @return string
     */
    public function replaceProductUrls()
    {
        // if isDraft is 0, add -{productID} to url
        $products = Product::where('isDraft', 0)->get();

        foreach ($products as $product) {
            $product->url = $product->url . '-' . $product->id;
            $product->save();
        }

        return 'done';
    }

    /**
     * @return string
     */
    public function migrateProductUrls()
    {
        $products = Product::where('isDraft', 0)->get();

        foreach ($products as $product) {
            $oldProduct = new OldProduct();
            $oldProduct->productID = $product->id;
            $oldProduct->oldUrl = $product->url; // for prod
            //$explodedProductUrl = explode('-', $product->url); // for local env
            //array_pop($explodedProductUrl); // for local env
            //$productUrl = join('-', $explodedProductUrl); // for local env
            //$oldProduct->oldUrl = $productUrl; // for local env

            $oldProduct->save();
        }

        return 'done';
    }

    /**
     * @return string
     */
    public function migrateProductTranslationUrls()
    {
        $pTranslations = ProductTranslation::all();

        foreach ($pTranslations as $pTranslation) {
            $oldPTranslation = new OldProductTranslation();
            $oldPTranslation->productTranslationID = $pTranslation->id;
            $oldPTranslation->oldUrl = $pTranslation->url; // for prod
            //$explodedProductUrl = explode('-', $pTranslation->url); // for local env
            //array_pop($explodedProductUrl); // for local env
            //$productUrl = join('-', $explodedProductUrl); // for local env
            //$oldPTranslation->oldUrl = $productUrl; // for local env
            $oldPTranslation->save();
        }

        return 'done';
    }

    /**
     * @return string
     */
    public function supplierMailHash()
    {
        $suppliers = Supplier::all();
        foreach ($suppliers as $supplier) {
            $supplier->mailHash = md5($supplier->email.env('HASH_STRING', '79425'));
            $supplier->save();
        }

        return 'done';
    }

}
