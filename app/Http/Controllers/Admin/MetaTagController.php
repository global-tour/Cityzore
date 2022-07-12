<?php

namespace App\Http\Controllers\Admin;

use App\MetaTag;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MetaTagController extends Controller
{

    /**
     * @param Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $type = $request->productType;
        $product = new Product();
        if ($type == 'productCZ') {
            $product = $product->findOrFail($request->productID);
            $metaTag = $product->metaTag()->first() ? $product->metaTag()->first() : new MetaTag();
        } else {


        

           if($type == 'productPCT'){
            $product = $product->on('mysql2')->findOrFail($request->productID);
            if ($product->metaTag()->first()) {
                $metaTag = $product->metaTag()->first();
            } else {
                $metaTag = new MetaTag();
                $metaTag->setConnection('mysql2');
              }

           }

             if($type == 'productPCTcom'){
            $product = $product->on('mysql3')->findOrFail($request->productID);
            if ($product->metaTag()->first()) {
                $metaTag = $product->metaTag()->first();
            } else {
                $metaTag = new MetaTag();
                $metaTag->setConnection('mysql3');
              }

           }


             if($type == 'productCTP'){
            $product = $product->on('mysql4')->findOrFail($request->productID);
            if ($product->metaTag()->first()) {
                $metaTag = $product->metaTag()->first();
            } else {
                $metaTag = new MetaTag();
                $metaTag->setConnection('mysql4');
              }

           }
            



        }

        $metaTag->title = $request->metaTagTitle;
        $metaTag->description = $request->metaTagDescription;;
        $metaTag->keywords = $request->metaTagKeywords;
        $metaTag->save();
        if (is_null($product->metaTag()->first())) {
            $product->metaTag()->attach($metaTag->id);
        }

        return ['product' => $product];
    }

}
