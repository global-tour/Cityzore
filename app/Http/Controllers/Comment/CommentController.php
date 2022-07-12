<?php

namespace App\Http\Controllers\Comment;

use App\Comment;
use App\Product;
use App\Country;
use App\Attraction;
use App\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use ReCaptcha\ReCaptcha;

class CommentController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create(Request $request)
    {

      $response = (new ReCaptcha('6LeV48kUAAAAAEZkr-ODsSsNT6tgj8JnqxmIbvV7'))
            ->setExpectedAction($request['recaptchaAction'])
            ->verify($request['recaptchaToken']);

        if (!$response->isSuccess() || $response->getScore() < 0.6) {
            return false;
        }

        $comment = new Comment();
        $comment->username = $request->get('name');
        $comment->email = $request->get('email');
        $comment->title = $request->get('title');
        $comment->description = $request->get('description');
        $comment->rate = $request->get('rating');
        $uniqueID = Session::get('uniqueID');
        if (Auth::user()) {
            $comment->userID = Auth::guard('web')->user()->id;
        } else {
            $comment->userID =$uniqueID;
        }
        $comment->productID = $request->get('type') == 'product' ? $request->get('productID') : null;
        $comment->blogPostID = $request->get('type') == 'blog' ? $request->get('blogPostID') : null;
        $product = Product::where('id', '=', $comment->productID)->first();
        $comment->save();
        $rateTotal = 0;
        $activeComments = Comment::where('productID', '=', $product->id)->where('status', '=', '1')->get();
        $i = count($activeComments) > 0 ? count($activeComments) : 1;
        foreach ($activeComments as $rate) {
            $rateTotal += $rate->rate;
        }
        if ($rateTotal > 0) {
            $product->rate = $rateTotal/$i;
            $product->save();
        }

        $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
        $langCodeForUrl = $langCode == 'en' ? '' : $langCode;

        return redirect($langCodeForUrl.'/'.$product->url);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $suppliers = null;

        if (Auth::guard('supplier')->check())
        {
            $products = Product::select('attractions')->where('supplierID', Auth::guard('supplier')->user()->id)->get()->pluck('attractions')->toArray();
            $countriyIDS = Product::select('country')->where('supplierID', Auth::guard('supplier')->user()->id)->groupBy('country')->get()->pluck('country')->toArray();
            $countries = Country::whereIn('id', $countriyIDS)->get();
            $attractionArray = [];

            foreach ($products as $product)
            {

                $attractionArray = array_merge($attractionArray, array_values(json_decode($product)));
            }

            $attractions = Attraction::where('isActive', 1)->whereIn('id', $attractionArray)->get();
        }
        else
        {
            $attractions = Attraction::where('isActive', 1)->get();
            $countries = Country::all();
            $suppliers = Supplier::select(['id', 'companyName'])->get();
        }

        return view('panel.comment.index',
            [
                'countries' => $countries,
                'attractions' => $attractions,
                'suppliers' => $suppliers
            ]
        );
    }

    /**
     * @param Request $request
     */
    public function setStatus(Request $request)
    {
        $commentModel = new Comment();
        $productModel = new Product();
        $comment = $commentModel->findOrFail($request->id);
        $comment->status = $request->status;
        $comment->save();
        $product = $productModel->where('id', '=', $comment->productID)->first();
        $rateTotal = 0;
        $productRate = 0;
        $productComments = $commentModel->where('productID', $product->id)->where('status', 1)->get();
        if (count($productComments) > 0) {
            foreach ($productComments as $i => $rate) {
                $rateTotal += $rate->rate;
            }
            $productRate = $rateTotal/($i+1);
        }
        $product->rate = $productRate;
        $product->save();
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id, Request $request)
    {
        $commentModel = new Comment();
        $productModel = new Product();
        $comment= $commentModel->findOrFail($id);
        $product = $productModel->where('id', '=', $comment->productID)->first();
        $comment->delete();
        $rateTotal = 0;
        $productRate = 0;
        $productComments = $commentModel->where('productID', $product->id)->where('status', 1)->get();
        if (count($productComments) > 0) {
            foreach ($productComments as $i => $rate) {
                $rateTotal += $rate->rate;
            }
            $productRate = $rateTotal/($i+1);
        }
        $product->rate = $productRate;
        $product->save();
        return redirect('/comments');
    }

}
