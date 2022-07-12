<?php

namespace App\Http\Controllers\Helpers;

use App\Attraction;
use App\AttractionTranslation;
use App\Http\Controllers\Controller;
use App\RouteLocalization;
use Illuminate\Http\Request;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Blog\BlogController;
use App\Http\Controllers\Pdfs\PdfController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\CartController;
use App\Http\Controllers\User\CommissionerController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Arr;

class UrlRelated extends Controller
{

    public $productController;
    public $blogController;
    public $pdfController;
    public $loginController;
    public $paymentController;
    public $cartController;
    public $commissionerController;
    public $userController;

    public function __construct()
    {
        $this->productController = new ProductController();
        $this->blogController = new BlogController();
        $this->pdfController = new PdfController();
        $this->loginController = new LoginController();
        $this->paymentController = new PaymentController();
        $this->cartController = new CartController();
        $this->commissionerController = new CommissionerController();
        $this->userController = new UserController();
    }

    /**
     * @param Request $request
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function redirectAttraction(Request $request, $slug)
    {

        $slugArray = explode('-', $slug);

        $attractionId = preg_replace('/[^0-9]/', '', Arr::last($slugArray));
        if (empty($attractionId))
        {

            $attraction = Attraction::where('slug', $slug)->first();

            $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
            $langID = \App\Language::where('code', $langCode)->first()->id;

            $routeTrans = RouteLocalization::where('routeID', 27)->where('languageID', $langID)->first();

            if ($routeTrans)
            {
                $routeTrans = $routeTrans->route;
            }
            else
            {
                $routeTrans = 'attraction';
            }


            return redirect($routeTrans.'/'.$attraction->slug.'-'.$attraction->id, 301);
        }



       $targetAttraction = Attraction::findOrFail($attractionId);
       array_pop($slugArray);
       $originalSlug = join("-",$slugArray);
       $langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
       $langID = \App\Language::where('code', $langCode)->first()->id;
       $routeTrans = RouteLocalization::where('routeID', 27)->where('languageID', $langID)->first();
          if ($routeTrans)
            {
                $routeTrans = $routeTrans->route;
            }
            else
            {
                $routeTrans = 'attraction';
            }


            if($langCode == 'en'){
            if($targetAttraction->slug != $originalSlug){
                return redirect($routeTrans.'/'.$targetAttraction->slug.'-'.$targetAttraction->id, 301);
            }


            }else{

            $targetAttractionTranslation =  AttractionTranslation::where('attractionID', $targetAttraction->id)->where('languageID',$langID)->first();

            if($targetAttractionTranslation){



            if($targetAttractionTranslation->slug != $originalSlug){

                return redirect($langCode.'/'.$routeTrans.'/'.$targetAttractionTranslation->slug.'-'.$targetAttraction->id, 301);
            }




            }else{

             return redirect()->back();
            }


            }











        //return $this->productController->attractionPage($langCode, $attractionId);
        return $this->productController->paginateAttractionPage($langCode, $slug);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function redirectProduct(Request $request)
    {
        $explodedPath = explode('/', $request->path());
        $location = $explodedPath[0];
        $slug = $explodedPath[1];

        return $this->productController->getProduct($request,'en', $location, $slug);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function redirectBlogPost(Request $request)
    {
        $explodedPath = explode('/', $request->path());
        $category = $explodedPath[1];
        $slug = $explodedPath[2];

        return $this->blogController->getBlogPost('en', $category, $slug);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function redirectPrintVoucherByToken(Request $request)
    {
        $explodedPath = explode('/', $request->path());
        $token = $explodedPath[1];

        return $this->pdfController->printVoucherByToken('en', $token);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function redirectResetPasswordLink(Request $request)
    {
        $explodedPath = explode('/', $request->path());
        $token = $explodedPath[1];

        return $this->loginController->resetPasswordLink('en', $token);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function redirectVoucher(Request $request)
    {
        $explodedPath = explode('/', $request->path());
        $id = $explodedPath[1];

        return $this->pdfController->voucher('en', $id);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function redirectInvoice(Request $request)
    {
        $explodedPath = explode('/', $request->path());
        $id = $explodedPath[1];

        return $this->pdfController->invoice('en', $id);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function redirectCommissionerInvoice(Request $request)
    {
        $explodedPath = explode('/', $request->path());
        $id = $explodedPath[1];
        $totalCommission = $explodedPath[2];

        return $this->pdfController->commissionerInvoice('en', $id, $totalCommission);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function redirectExternalPaymentDetails(Request $request)
    {
        $explodedPath = explode('/', $request->path());
        $link = $explodedPath[1];

        return $this->paymentController->externalPaymentDetails('en', $link);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Nahid\JsonQ\Exceptions\ConditionNotAllowedException
     */
    public function redirectDeleteItemFromCart(Request $request)
    {
        $explodedPath = explode('/', $request->path());
        $id = $explodedPath[1];

        return $this->cartController->deleteItemFromCart('en', $id, $request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function redirectPaymentDetails(Request $request)
    {
        $explodedPath = explode('/', $request->path());
        $id = $explodedPath[1];

        return $this->commissionerController->paymentDetails('en', $id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function redirectStorePaymentDetails(Request $request)
    {
        $explodedPath = explode('/', $request->path());
        $id = $explodedPath[1];

        return $this->commissionerController->storePaymentDetails('en', $request, $id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function redirectLicenseFiles(Request $request)
    {
        $explodedPath = explode('/', $request->path());
        $id = $explodedPath[1];

        return $this->commissionerController->licenseFiles('en', $id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirectStoreLicenseFiles(Request $request)
    {
        $explodedPath = explode('/', $request->path());
        $id = $explodedPath[1];

        return $this->commissionerController->storeLicenseFiles('en', $request, $id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function redirectEditFrontend(Request $request)
    {
        $explodedPath = explode('/', $request->path());
        $id = $explodedPath[1];

        return $this->userController->editFrontend('en', $id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirectUpdateFrontend(Request $request)
    {
        $explodedPath = explode('/', $request->path());
        $id = $explodedPath[1];

        return $this->userController->updateFrontend('en', $request, $id);
    }

}
