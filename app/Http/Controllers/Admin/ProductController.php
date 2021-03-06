<?php

namespace App\Http\Controllers\Admin;

use App\Attraction;
use App\Cart;
use App\Category;
use App\Comment;
use App\Config;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Helpers\CommonFunctions;
use App\Mails;
use App\TicketType;
use App\Country;
use App\Http\Controllers\Helpers\RefCodeGenerator;
use App\Option;
use App\Pricing;
use App\Product;
use App\ProductGallery;
use App\Supplier;
use App\Adminlog;
use App\Av;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;


class ProductController extends Controller
{

    public $commonFunctions;
    public $refCodeGenerator;

    public function __construct()
    {
        $this->commonFunctions = new CommonFunctions();
        $this->refCodeGenerator = new RefCodeGenerator();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $countries = Country::has('cities')->get();
        $attractions = Attraction::where('isActive', 1)->get();
        $categories = Category::all();
        $suppliers = null;
        if (auth()->guard('admin')->check()) {
            $suppliers = Supplier::where('isActive', 1)->get();
        }

        return view('panel.products.index',
            [
                'attractions' => $attractions,
                'categories' => $categories,
                'suppliers' => $suppliers,
                'countries' => $countries
            ]
        );
    }

    /**
     * Exports products to excel file
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportToExcel(Request $request)
    {
        return Excel::download(new ProductsExport($request), 'products.xlsx');
    }

    /**
     * Index page for products on pariscitytours.fr
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexPCT()
    {
        $attractions = Attraction::where('isActive', 1)->get();
        $categories = Category::all();
        $suppliers = null;
        if (auth()->guard('admin')->check()) {
            $suppliers = Supplier::where('isActive', 1)->get();
        }

        return view('panel.products.indexpct',
            [
                'attractions' => $attractions,
                'categories' => $categories,
                'suppliers' => $suppliers
            ]
        );
    }

    /**
     * Index page for products on pariscitytours.com
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexPCTcom()
    {
        $attractions = Attraction::where('isActive', 1)->get();
        $categories = Category::all();
        $suppliers = null;
        if (auth()->guard('admin')->check()) {
            $suppliers = Supplier::where('isActive', 1)->get();
        }

        return view('panel.products.indexpctcom',
            [
                'attractions' => $attractions,
                'categories' => $categories,
                'suppliers' => $suppliers
            ]
        );
    }

    /**
     * Index page for products on citytours.paris
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexCTP()
    {
        $attractions = Attraction::where('isActive', 1)->get();
        $categories = Category::all();
        $suppliers = null;
        if (auth()->guard('admin')->check()) {
            $suppliers = Supplier::where('isActive', 1)->get();
        }

        return view('panel.products.indexctp',
            [
                'attractions' => $attractions,
                'categories' => $categories,
                'suppliers' => $suppliers
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $comission = 0;
        $ownerId = -1;
        if (Auth::guard('supplier')->check()) {
            $supplier = Supplier::where('id', Auth::guard('supplier')->user()->id)->first();
            $options = $supplier->option;
            $pricings = $supplier->pricing;
            $availabilities = $supplier->av;
            $comission = $supplier->comission;
            $userType = 'supplier';
            $ownerId = auth()->user()->id;
        }

        if (Auth::guard('admin')->check()) {
            $options = Option::all();
            $pricings = Pricing::where('supplierID', '-1')->get();
            $availabilities = Av::all();
            $userType = 'admin';
        }

        $country = Country::has('cities')->get();
        $category = Category::all();
        $attractions = Attraction::where('isActive', 1)->get();
        $ticketTypes = TicketType::all();

        return view('panel.products.create',
            [
                'pricings' => $pricings,
                'country' => $country,
                'options' => $options,
                'category' => $category,
                'availabilities' => $availabilities,
                'comission' => $comission,
                'userType' => $userType,
                'attractions' => $attractions,
                'ticketTypes' => $ticketTypes,
                'ownerId' => $ownerId
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $highlights = json_decode($request->highlights, true);
        $highlights = collect($highlights);
        $highlights = $highlights->map(function ($row) {
            return $row['value'];
        })->toArray();
        $highlights = implode('|', $highlights);


        $knowBeforeYouGo = json_decode($request->knowBeforeYouGo, true);
        $knowBeforeYouGo = collect($knowBeforeYouGo);
        $knowBeforeYouGo = $knowBeforeYouGo->map(function ($row) {
            return $row['value'];
        })->toArray();
        $knowBeforeYouGo = implode('|', $knowBeforeYouGo);


        $includes = json_decode($request->included, true);
        $includes = collect($includes);
        $includes = $includes->map(function ($row) {
            return $row['value'];
        })->toArray();
        $includes = implode('|', $includes);

        $notIncluded = json_decode($request->notIncluded, true);
        $notIncluded = collect($notIncluded);
        $notIncluded = $notIncluded->map(function ($row) {
            return $row['value'];
        })->toArray();
        $notIncluded = implode('|', $notIncluded);


        $tags = json_decode($request->tags, true);
        $tags = collect($tags);
        $tags = $tags->map(function ($row) {
            return $row['value'];
        })->toArray();
        $tags = implode('|', $tags);

        $adminLog = new Adminlog();
        $adminLog->userID = auth()->user()->id;
        $adminLog->page = 'Product';
        $adminLog->url = $request->url();
        $adminLog->action = 'Saved Product';

        if (Auth::guard('supplier')->check()) {
            $refCode = Auth::guard('supplier')->user()->companyShortCode . $this->refCodeGenerator->refCodeGenerator();
            $isPublished = 0;
        }

        if (Auth::guard('admin')->check()) {
            $rand = rand(0, 99999);
            $refCode = 'PBT' . +$rand;
            $isPublished = 1;
        }

        $product = Product::findOrFail($request->get('product_id'));
        $adminLog->details = auth()->user()->name . ' clicked to Finish button on Product Creation Page and saved a new product with id ' . $request->get('product_id');
        $title = str_replace("  ", " ", $request->get('title'));
        $product->title = $title;
        $product->shortDesc = $request->get('shortDesc');
        $product->fullDesc = $request->get('fullDesc');
        $product->country = $request->get('location');
        $product->city = $request->get('cities');
        $product->countryCode = json_encode($request->get('countryCode'));
        $product->phoneNumber = json_encode($request->get('phoneNumber'));
        $product->highlights = $highlights;
        $product->included = $includes;
        $product->notIncluded = $notIncluded;
        $product->category = $request->get('category');
        $product->knowBeforeYouGo = $knowBeforeYouGo;
        $product->cancelPolicy = $request->get('cancelPolicy');
        $options = null;
        if (in_array("null", $request->get('options'))) {
            $index = array_search('null', $request->get('options'));
            $options = $request->get('options');
            if ($index != false) {
                unset($options[$index]);
            }
        } else {
            $options = $request->get('options');
        }
        $product->options = json_encode($options);
        $product->tags = $tags;
        $product->attractions = json_encode($request->get('attractions'));
        $product->referenceCode = $refCode;
        $adminLog->productRefCode = $refCode;
        $adminLog->tableName = 'products';
        $product->isDraft = $request->get('isDraft');
        $product->isPublished = $isPublished;
        if ($product->isDraft == 0) {
            $product->supplierPublished = 1;
            $urlPart = strtolower($title);
            if (auth()->guard('supplier')->check()) {
                $urlPart = strtolower($title) . ' ' . strtolower(auth()->user()->companyName);
            }
            $product->url = Str::slug(strtolower($product->city), '-') . '/' . Str::slug($urlPart, '-') . '-' . $product->id;

        } else {
            $product->supplierPublished = 0;
            $product->url = 'product-preview/' . $product->id;
        }
        if (!in_array("null", $options)) {
            $product->options()->attach($options);
        }
        if (is_null($product->coverPhoto)) {
            $product->coverPhoto = 53; // This is default product image
        }
        $fileArr = [];
        $fileArr = $this->storeProductFile($fileArr, $request);

        if (count($fileArr) > 0) {
            $product->productFiles = json_encode($fileArr);
        }

        if ($product->save()) {
            // Creating a new product for pariscitytoursfr so its content can be changed
            $productForPCTFR = new Product();
            $productForPCTFR->setConnection('mysql2');
            $productForPCTFR->id = $product->id;
            $productForPCTFR->referenceCode = $product->referenceCode;
            $productForPCTFR->title = $product->title;
            $productForPCTFR->shortDesc = $product->shortDesc;
            $productForPCTFR->fullDesc = $product->fullDesc;
            $productForPCTFR->country = $product->country;
            $productForPCTFR->city = $product->city;
            $productForPCTFR->attractions = $product->attractions;
            $productForPCTFR->countryCode = $product->countryCode;
            $productForPCTFR->phoneNumber = $product->phoneNumber;
            $productForPCTFR->highlights = $product->highlights;
            $productForPCTFR->included = $product->included;
            $productForPCTFR->notIncluded = $product->notIncluded;
            $productForPCTFR->knowBeforeYouGo = $product->knowBeforeYouGo;
            $productForPCTFR->category = $product->category;
            $productForPCTFR->tags = $product->tags;
            $productForPCTFR->cancelPolicy = $product->cancelPolicy;
            $productForPCTFR->options = $product->options;
            $productForPCTFR->supplierID = $product->supplierID;
            $productForPCTFR->coverPhoto = $product->coverPhoto;
            $productForPCTFR->isDraft = $product->isDraft;
            $productForPCTFR->isPublished = $product->isPublished;
            $productForPCTFR->url = $product->url;
            $productForPCTFR->minPrice = $product->minPrice;
            $productForPCTFR->rate = $product->rate;
            $productForPCTFR->supplierPublished = $product->supplierPublished;
            $productForPCTFR->isSpecial = $product->isSpecial;
            $productForPCTFR->imageOrder = $product->imageOrder;
            $productForPCTFR->productFiles = $product->productFiles;
            $productForPCTFR->copyOf = $product->copyOf;
            $productForPCTFR->save();


            $productForPCTCOM = new Product();
            $productForPCTCOM->setConnection('mysql3');
            $productForPCTCOM->id = $product->id;
            $productForPCTCOM->referenceCode = $product->referenceCode;
            $productForPCTCOM->title = $product->title;
            $productForPCTCOM->shortDesc = $product->shortDesc;
            $productForPCTCOM->fullDesc = $product->fullDesc;
            $productForPCTCOM->country = $product->country;
            $productForPCTCOM->city = $product->city;
            $productForPCTCOM->attractions = $product->attractions;
            $productForPCTCOM->countryCode = $product->countryCode;
            $productForPCTCOM->phoneNumber = $product->phoneNumber;
            $productForPCTCOM->highlights = $product->highlights;
            $productForPCTCOM->included = $product->included;
            $productForPCTCOM->notIncluded = $product->notIncluded;
            $productForPCTCOM->knowBeforeYouGo = $product->knowBeforeYouGo;
            $productForPCTCOM->category = $product->category;
            $productForPCTCOM->tags = $product->tags;
            $productForPCTCOM->cancelPolicy = $product->cancelPolicy;
            $productForPCTCOM->options = $product->options;
            $productForPCTCOM->supplierID = $product->supplierID;
            $productForPCTCOM->coverPhoto = $product->coverPhoto;
            $productForPCTCOM->isDraft = $product->isDraft;
            $productForPCTCOM->isPublished = $product->isPublished;
            $productForPCTCOM->url = $product->url;
            $productForPCTCOM->minPrice = $product->minPrice;
            $productForPCTCOM->rate = $product->rate;
            $productForPCTCOM->supplierPublished = $product->supplierPublished;
            $productForPCTCOM->isSpecial = $product->isSpecial;
            $productForPCTCOM->imageOrder = $product->imageOrder;
            $productForPCTCOM->productFiles = $product->productFiles;
            $productForPCTCOM->copyOf = $product->copyOf;
            $productForPCTCOM->save();

            $productForCTP = new Product();
            $productForCTP->setConnection('mysql4');
            $productForCTP->id = $product->id;
            $productForCTP->referenceCode = $product->referenceCode;
            $productForCTP->title = $product->title;
            $productForCTP->shortDesc = $product->shortDesc;
            $productForCTP->fullDesc = $product->fullDesc;
            $productForCTP->country = $product->country;
            $productForCTP->city = $product->city;
            $productForCTP->attractions = $product->attractions;
            $productForCTP->countryCode = $product->countryCode;
            $productForCTP->phoneNumber = $product->phoneNumber;
            $productForCTP->highlights = $product->highlights;
            $productForCTP->included = $product->included;
            $productForCTP->notIncluded = $product->notIncluded;
            $productForCTP->knowBeforeYouGo = $product->knowBeforeYouGo;
            $productForCTP->category = $product->category;
            $productForCTP->tags = $product->tags;
            $productForCTP->cancelPolicy = $product->cancelPolicy;
            $productForCTP->options = $product->options;
            $productForCTP->supplierID = $product->supplierID;
            $productForCTP->coverPhoto = $product->coverPhoto;
            $productForCTP->isDraft = $product->isDraft;
            $productForCTP->isPublished = $product->isPublished;
            $productForCTP->url = $product->url;
            $productForCTP->minPrice = $product->minPrice;
            $productForCTP->rate = $product->rate;
            $productForCTP->supplierPublished = $product->supplierPublished;
            $productForCTP->isSpecial = $product->isSpecial;
            $productForCTP->imageOrder = $product->imageOrder;
            $productForCTP->productFiles = $product->productFiles;
            $productForCTP->copyOf = $product->copyOf;
            $productForCTP->save();

            $adminLog->result = 'successful';
        } else {
            $adminLog->result = 'failed';
        }
        $adminLog->save();

        $mail = new Mails();
        $mail->to = auth()->user()->email;
        $mail->data = json_encode([
            [
                'subject' => 'New Product Has Been Created - ' . $product->referenceCode,
                'companyName' => auth()->user()->companyName,
                'product' => $product,
                'options' => $options,
                'sendToCC' => true,
            ]
        ]);
        $mail->blade = 'mail.product-preview';
        $mail->save();

        if ($request->get('redirectToDashboard') == 1) {
            return redirect('/');
        }
        return redirect('/product');
    }

    /**
     * @param $fileArr
     * @param $request
     * @return mixed
     */
    public function storeProductFile($fileArr, $request)
    {
        $productFiles = $request->product_files;
        if (!is_null($productFiles)) {
            foreach ($productFiles as $file) {
                $fileName = $file->getClientOriginalName();
                $s3 = Storage::disk('s3');
                $filePath = '/product-files/' . $fileName;
                $stream = fopen($file->getRealPath(), 'r+');
                $s3->put($filePath, $stream);
                array_push($fileArr, $fileName);
            }
        }
        return $fileArr;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteProductFile(Request $request)
    {
        $product = Product::findOrFail($request->productId);
        $fileName = $request->fileName;
        $productFiles = json_decode($product->productFiles, true);
        if (($key = array_search($fileName, $productFiles)) != false) {
            unset($productFiles[$key]);
            $productFiles = array_values($productFiles);
            $product->productFiles = json_encode($productFiles);
            $product->save();
        }
        Storage::disk('s3')->delete('/product-files/' . $fileName);

        return response()->json(['success' => 'File ' . $fileName . ' is successfully deleted!']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmSupplierProductEdit(Request $request)
    {
        $product = Product::findOrFail($request->productID);
        $copyProduct = Product::findOrFail($request->editedProductID);


        $highlights = join("|", explode("{}", $copyProduct->highlights));
        $included = join("|", explode("{}", $copyProduct->included));
        $notIncluded = join("|", explode("{}", $copyProduct->notIncluded));
        $knowBeforeYouGo = join("|", explode("{}", $copyProduct->knowBeforeYouGo));
        $tags = join("|", explode("{}", $copyProduct->tags));


        $product->title = $copyProduct->title;
        $product->shortDesc = $copyProduct->shortDesc;
        $product->fullDesc = $copyProduct->fullDesc;
        $product->country = $copyProduct->country;
        $product->city = $copyProduct->city;
        $product->attractions = $copyProduct->attractions;
        $product->countryCode = $copyProduct->countryCode;
        $product->phoneNumber = $copyProduct->phoneNumber;

        //*
        $product->highlights = $highlights;
        $product->included = $included;
        $product->notIncluded = $notIncluded;
        $product->knowBeforeYouGo = $knowBeforeYouGo;
        $product->tags = $tags;
        //*

        $product->category = $copyProduct->category;

        $product->cancelPolicy = $copyProduct->cancelPolicy;
        $product->options = $copyProduct->options;
        $product->coverPhoto = $copyProduct->coverPhoto;
        $product->url = $copyProduct->url;
        $product->productFiles = $copyProduct->productFiles;
        $copyProductOptions = json_decode($copyProduct->options, true);
        $product->options()->detach();
        foreach ($copyProductOptions as $opts) {
            $product->options()->attach($opts);
        }
        $product->productGalleries()->detach();
        foreach ($copyProduct->productGalleries()->get() as $photo) {
            $product->productGalleries()->attach($photo->id);
        }
        $product->save();

        // Copies
        $productMysql2 = Product::on('mysql2')->findOrFail($request->productID);
        $productMysql2->title = $copyProduct->title;
        $productMysql2->shortDesc = $copyProduct->shortDesc;
        $productMysql2->fullDesc = $copyProduct->fullDesc;
        $productMysql2->country = $copyProduct->country;
        $productMysql2->city = $copyProduct->city;
        $productMysql2->attractions = $copyProduct->attractions;
        $productMysql2->countryCode = $copyProduct->countryCode;
        $productMysql2->phoneNumber = $copyProduct->phoneNumber;

        //*

        $productMysql2->highlights = $highlights;
        $productMysql2->included = $included;
        $productMysql2->notIncluded = $notIncluded;
        $productMysql2->knowBeforeYouGo = $knowBeforeYouGo;
        $productMysql2->tags = $tags;
        //*

        $productMysql2->category = $copyProduct->category;

        $productMysql2->cancelPolicy = $copyProduct->cancelPolicy;
        $productMysql2->options = $copyProduct->options;
        $productMysql2->coverPhoto = $copyProduct->coverPhoto;
        $productMysql2->url = $copyProduct->url;
        $productMysql2->productFiles = $copyProduct->productFiles;

        $productMysql2->options()->detach();
        foreach ($copyProductOptions as $opts) {
            $productMysql2->options()->attach($opts);
        }
        $productMysql2->productGalleries()->detach();
        foreach ($copyProduct->productGalleries()->get() as $photo) {
            $productMysql2->productGalleries()->attach($photo->id);
        }

        $productMysql2->save();

        $productMysql3 = Product::on('mysql3')->findOrFail($request->productID);
        $productMysql3->title = $copyProduct->title;
        $productMysql3->shortDesc = $copyProduct->shortDesc;
        $productMysql3->fullDesc = $copyProduct->fullDesc;
        $productMysql3->country = $copyProduct->country;
        $productMysql3->city = $copyProduct->city;
        $productMysql3->attractions = $copyProduct->attractions;
        $productMysql3->countryCode = $copyProduct->countryCode;
        $productMysql3->phoneNumber = $copyProduct->phoneNumber;

        //*
        $productMysql3->highlights = $highlights;
        $productMysql3->included = $included;
        $productMysql3->notIncluded = $notIncluded;
        $productMysql3->knowBeforeYouGo = $knowBeforeYouGo;
        $productMysql3->tags = $tags;
        //*

        $productMysql3->category = $copyProduct->category;

        $productMysql3->cancelPolicy = $copyProduct->cancelPolicy;
        $productMysql3->options = $copyProduct->options;
        $productMysql3->coverPhoto = $copyProduct->coverPhoto;
        $productMysql3->url = $copyProduct->url;
        $productMysql3->productFiles = $copyProduct->productFiles;

        $productMysql3->options()->detach();
        foreach ($copyProductOptions as $opts) {
            $productMysql3->options()->attach($opts);
        }
        $productMysql3->productGalleries()->detach();
        foreach ($copyProduct->productGalleries()->get() as $photo) {
            $productMysql3->productGalleries()->attach($photo->id);
        }

        $productMysql3->save();

        $productMysql4 = Product::on('mysql4')->findOrFail($request->productID);
        $productMysql4->title = $copyProduct->title;
        $productMysql4->shortDesc = $copyProduct->shortDesc;
        $productMysql4->fullDesc = $copyProduct->fullDesc;
        $productMysql4->country = $copyProduct->country;
        $productMysql4->city = $copyProduct->city;
        $productMysql4->attractions = $copyProduct->attractions;
        $productMysql4->countryCode = $copyProduct->countryCode;
        $productMysql4->phoneNumber = $copyProduct->phoneNumber;

        //*
        $productMysql4->highlights = $highlights;
        $productMysql4->included = $included;
        $productMysql4->notIncluded = $notIncluded;
        $productMysql4->knowBeforeYouGo = $knowBeforeYouGo;
        $productMysql4->tags = $tags;
        //*

        $productMysql4->category = $copyProduct->category;

        $productMysql4->cancelPolicy = $copyProduct->cancelPolicy;
        $productMysql4->options = $copyProduct->options;
        $productMysql4->coverPhoto = $copyProduct->coverPhoto;
        $productMysql4->url = $copyProduct->url;
        $productMysql4->productFiles = $copyProduct->productFiles;

        $productMysql4->options()->detach();
        foreach ($copyProductOptions as $opts) {
            $productMysql4->options()->attach($opts);
        }
        $productMysql4->productGalleries()->detach();
        foreach ($copyProduct->productGalleries()->get() as $photo) {
            $productMysql4->productGalleries()->attach($photo->id);
        }

        $productMysql4->save();


        $copyProduct->options()->detach();
        $copyProduct->productGalleries()->detach();
        $copyProduct->delete();

        return response()->json(['success' => 'Successful']);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        if (request()->page) {
            $pageID = request()->page;
        } else {
            $pageID = 1;
        }


        $comission = 0;
        $ownerId = -1;

        $product = Product::findOrFail($id);
        if (Auth::guard('supplier')->check()) {

            if ($product->supplierID != Auth::guard('supplier')->user()->id)
                return redirect()->back()->with(['error' => 'You cannot view this user information']);

            $supplier = Supplier::where('id', Auth::guard('supplier')->user()->id)->first();
            $options = $supplier->option;
            $pricings = $supplier->pricing;
            $availabilities = $supplier->av;
            $comission = $supplier->comission;
            $userType = 'supplier';
            $ownerId = auth()->user()->id;
        }

        if (Auth::guard('admin')->check()) {
            $supplier = Supplier::where('id', $product->supplierID)->first();
            if ($supplier) {
                $options = $supplier->option;
                $pricings = $supplier->pricing;
                $availabilities = $supplier->av;
            } else {
                $options = Option::all();
                $pricings = Pricing::all();
                $availabilities = Av::all();
            }
            $userType = 'admin';
        }
        $ticketTypes = TicketType::all();
        if (is_null($product)) {
            abort(404);
        }
        $countryIsoCode = $product->countryName()->first() ? $product->countryName()->first()->countries_iso_code : null;
        $countryCodes = json_decode($product->countryCode, true);
        $countryIsoCodes = [];
        foreach ($countryCodes as $cCode) {
            $country = Country::where('countries_phone_code', $cCode)->first();
            if ($country) {
                array_push($countryIsoCodes, $country->countries_iso_code);
            }
        }
        $country = Country::has('cities')->get();
        $category = Category::all();
        $attractions = Attraction::where('isActive', 1)->get();
        $productOptions = $product->options()->get();
        $productImages = $product->productGalleries()->get();
        $productAttractions = json_decode($product->attractions, true);

        if (Product::where('copyOf', $id)->count() > 0 && $product->isDraft == false && Auth::guard('admin')->check()) {
            $editedProduct = Product::where('copyOf', '=', (int)$product->id)->first();
            $editedProductImages = $editedProduct->productGalleries()->get();
            $editedProductAttractions = json_decode($editedProduct->attractions, true);
            $editedProductOptions = $editedProduct->options()->get();
            return view('panel.products.product-check', [
                'product' => $product,
                'editedProduct' => $editedProduct,
                'category' => $category,
                'country' => $country,
                'options' => $options,
                'attractions' => $attractions,
                'productAttractions' => $productAttractions,
                'editedProductAttractions' => $editedProductAttractions,
                'countryIsoCodes' => $countryIsoCodes,
                'productImages' => $productImages,
                'editedProductImages' => $editedProductImages,
                'productOptions' => $productOptions,
                'editedProductOptions' => $editedProductOptions,
                'pageID' => $pageID
            ]);
        } else {
            $editedProduct = Product::where('referenceCode', $product->referenceCode . '-EDITED')->first();

            return view('panel.products.edit',
                [
                    'pricings' => $pricings,
                    'product' => $editedProduct ?? $product,
                    'country' => $country,
                    'options' => $options,
                    'category' => $category,
                    'availabilities' => $availabilities,
                    'comission' => $comission,
                    'userType' => $userType,
                    'productOptions' => $productOptions,
                    'productImages' => $productImages,
                    'countryIsoCode' => $countryIsoCode,
                    'countryIsoCodes' => $countryIsoCodes,
                    'productAttractions' => is_null($productAttractions) ? [] : $productAttractions,
                    'attractions' => $attractions,
                    'ticketTypes' => $ticketTypes,
                    'ownerId' => $ownerId,
                    'pageID' => $pageID
                ]
            );
        }
    }

    /**
     * Edit page for products on pariscitytours.fr
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editPCT($id)
    {
        $productPCT = Product::on('mysql2')->findOrFail($id);
        if (request()->page) {
            $pageID = request()->page;
        } else {
            $pageID = 1;
        }


        return view('panel.products.editpct', ['product' => $productPCT, 'pageID' => $pageID]);
    }

    /**
     * Edit page for products on pariscitytours.com
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editPCTcom($id)
    {
        $productPCT = Product::on('mysql3')->findOrFail($id);

        if (request()->page) {
            $pageID = request()->page;
        } else {
            $pageID = 1;
        }

        return view('panel.products.editpctcom', ['product' => $productPCT, 'pageID' => $pageID]);
    }

    /**
     * Edit page for products on citytours.paris
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editCTP($id)
    {
        $productPCT = Product::on('mysql4')->findOrFail($id);

        if (request()->page) {
            $pageID = request()->page;
        } else {
            $pageID = 1;
        }

        return view('panel.products.editctp', ['product' => $productPCT, 'pageID' => $pageID]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        //dd($request->all());
        $highlights = json_decode($request->highlights, true);
        $highlights = collect($highlights);
        $highlights = $highlights->map(function ($row) {
            return $row['value'];
        })->toArray();
        $highlights = implode('|', $highlights);


        $knowBeforeYouGo = json_decode($request->knowBeforeYouGo, true);
        $knowBeforeYouGo = collect($knowBeforeYouGo);
        $knowBeforeYouGo = $knowBeforeYouGo->map(function ($row) {
            return $row['value'];
        })->toArray();
        $knowBeforeYouGo = implode('|', $knowBeforeYouGo);


        $includes = json_decode($request->included, true);
        $includes = collect($includes);
        $includes = $includes->map(function ($row) {
            return $row['value'];
        })->toArray();
        $includes = implode('|', $includes);

        $notIncluded = json_decode($request->notIncluded, true);
        $notIncluded = collect($notIncluded);
        $notIncluded = $notIncluded->map(function ($row) {
            return $row['value'];
        })->toArray();
        $notIncluded = implode('|', $notIncluded);


        $tags = json_decode($request->tags, true);
        $tags = collect($tags);
        $tags = $tags->map(function ($row) {
            return $row['value'];
        })->toArray();
        $tags = implode('|', $tags);

        if (Auth::guard('admin')->check()) {
            $adminLog = new Adminlog();
            $adminLog->userID = auth()->user()->id;
            $adminLog->page = 'Product';
            $adminLog->url = $request->url();
            $adminLog->action = 'Updated Product';

            $product = Product::findOrFail($id);

            $adminLog->details = auth()->user()->name . ' clicked to Update Button and updated product with id ' . $id;
            $adminLog->productRefCode = $product->referenceCode;
            $adminLog->tableName = 'products';

            $title = str_replace("  ", " ", $request->get('title'));
            $product->title = $title;
            $product->shortDesc = $request->get('shortDesc');
            $product->fullDesc = $request->get('fullDesc');
            $product->country = $request->get('location');
            $product->city = $request->get('cities');
            $product->countryCode = json_encode($request->get('countryCode'));
            $product->phoneNumber = json_encode($request->get('phoneNumber'));
            $product->highlights = $highlights;
            $product->included = $includes;
            $product->notIncluded = $notIncluded;
            $product->category = $request->get('category');
            $product->knowBeforeYouGo = $knowBeforeYouGo;
            $product->cancelPolicy = $request->get('cancelPolicy');
            $product->attractions = json_encode($request->get('attractions'));
            $product->tags = $tags;


            /*      if($product->options()->count() == 0){

                              $options = null;
                         if (in_array("null", $request->get('options'))) {
                             $index = array_search('null', $request->get('options'));
                             $options = $request->get('options');
                             if ($index != false) {
                                 unset($options[$index]);
                             }
                         } else {
                             $options = $request->get('options');
                         }
                         $product->options = json_encode($options);
                         $product->options()->detach();
                         $product->options()->attach($options);

                     }else{*/
            if ($request->productOptionIds != '["null"]') {
                $product->options()->detach();
                $product->options = $request->productOptionIds;
                $product->options()->attach(json_decode($request->productOptionIds, true));

            }


            /*   }*/

            $product->isDraft = $request->get('isDraft');
            if ($product->isDraft == 1) {
                $product->supplierPublished = 0;
                $product->isPublished = 0;
                $product->url = 'product-preview/' . $product->id;
            } else {
                $product->supplierPublished = 1;
                $urlPart = strtolower($title);
                $product->url = Str::slug(strtolower($product->city), '-') . '/' . Str::slug($urlPart, '-') . '-' . $product->id;
            }


            $fileArr = is_null(json_decode($product->productFiles, true)) ? [] : json_decode($product->productFiles, true);
            $fileArr = $this->storeProductFile($fileArr, $request);

            if (count($fileArr) > 0) {
                $product->productFiles = json_encode($fileArr);
            } else {
                $product->productFiles = null;
            }
            foreach ($product->productGalleries()->get() as $img) {
                $img->attractions = json_encode($request->attractions);
                $img->save();
            }


            if (!is_null($product->productGalleries()->first())) {

                if (is_null($product->coverPhoto)) {
                    $product->coverPhoto = $product->productGalleries()->first()->id;
                }
                if ($product->save()) {
                    $adminLog->result = 'successful';


                    $productForPCTFR = Product::on('mysql2')->findOrFail($product->id);
                    $productForPCTFR->referenceCode = $product->referenceCode;
                    $productForPCTFR->title = $product->title;
                    $productForPCTFR->shortDesc = $product->shortDesc;
                    $productForPCTFR->fullDesc = $product->fullDesc;
                    $productForPCTFR->country = $product->country;
                    $productForPCTFR->city = $product->city;
                    $productForPCTFR->attractions = $product->attractions;
                    $productForPCTFR->countryCode = $product->countryCode;
                    $productForPCTFR->phoneNumber = $product->phoneNumber;
                    $productForPCTFR->highlights = $product->highlights;
                    $productForPCTFR->included = $product->included;
                    $productForPCTFR->notIncluded = $product->notIncluded;
                    $productForPCTFR->knowBeforeYouGo = $product->knowBeforeYouGo;
                    $productForPCTFR->category = $product->category;
                    $productForPCTFR->tags = $product->tags;
                    $productForPCTFR->cancelPolicy = $product->cancelPolicy;
                    $productForPCTFR->options = $product->options;
                    $productForPCTFR->supplierID = $product->supplierID;
                    $productForPCTFR->coverPhoto = $product->coverPhoto;
                    $productForPCTFR->isDraft = $product->isDraft;
                    $productForPCTFR->isPublished = $product->isPublished;
                    $productForPCTFR->url = $product->url;
                    $productForPCTFR->minPrice = $product->minPrice;
                    $productForPCTFR->rate = $product->rate;
                    $productForPCTFR->supplierPublished = $product->supplierPublished;
                    $productForPCTFR->isSpecial = $product->isSpecial;
                    $productForPCTFR->imageOrder = $product->imageOrder;
                    $productForPCTFR->productFiles = $product->productFiles;
                    $productForPCTFR->copyOf = $product->copyOf;
                    $productForPCTFR->save();

                    if ($productForPCTFR->productOptionIds != '["null"]') {
                        $productForPCTFR->options()->detach();
                        $productForPCTFR->options = $productForPCTFR->productOptionIds;
                        $productForPCTFR->options()->attach(json_decode($productForPCTFR->productOptionIds, true));

                    }


                    $productForPCTCOM = Product::on('mysql3')->findOrFail($product->id);
                    $productForPCTCOM->referenceCode = $product->referenceCode;
                    $productForPCTCOM->title = $product->title;
                    $productForPCTCOM->shortDesc = $product->shortDesc;
                    $productForPCTCOM->fullDesc = $product->fullDesc;
                    $productForPCTCOM->country = $product->country;
                    $productForPCTCOM->city = $product->city;
                    $productForPCTCOM->attractions = $product->attractions;
                    $productForPCTCOM->countryCode = $product->countryCode;
                    $productForPCTCOM->phoneNumber = $product->phoneNumber;
                    $productForPCTCOM->highlights = $product->highlights;
                    $productForPCTCOM->included = $product->included;
                    $productForPCTCOM->notIncluded = $product->notIncluded;
                    $productForPCTCOM->knowBeforeYouGo = $product->knowBeforeYouGo;
                    $productForPCTCOM->category = $product->category;
                    $productForPCTCOM->tags = $product->tags;
                    $productForPCTCOM->cancelPolicy = $product->cancelPolicy;
                    $productForPCTCOM->options = $product->options;
                    $productForPCTCOM->supplierID = $product->supplierID;
                    $productForPCTCOM->coverPhoto = $product->coverPhoto;
                    $productForPCTCOM->isDraft = $product->isDraft;
                    $productForPCTCOM->isPublished = $product->isPublished;
                    $productForPCTCOM->url = $product->url;
                    $productForPCTCOM->minPrice = $product->minPrice;
                    $productForPCTCOM->rate = $product->rate;
                    $productForPCTCOM->supplierPublished = $product->supplierPublished;
                    $productForPCTCOM->isSpecial = $product->isSpecial;
                    $productForPCTCOM->imageOrder = $product->imageOrder;
                    $productForPCTCOM->productFiles = $product->productFiles;
                    $productForPCTCOM->copyOf = $product->copyOf;
                    $productForPCTCOM->save();

                    if ($productForPCTCOM->productOptionIds != '["null"]') {
                        $productForPCTCOM->options()->detach();
                        $productForPCTCOM->options = $productForPCTCOM->productOptionIds;
                        $productForPCTCOM->options()->attach(json_decode($productForPCTCOM->productOptionIds, true));

                    }


                    $productForCTP = Product::on('mysql4')->findOrFail($product->id);
                    $productForCTP->referenceCode = $product->referenceCode;
                    $productForCTP->title = $product->title;
                    $productForCTP->shortDesc = $product->shortDesc;
                    $productForCTP->fullDesc = $product->fullDesc;
                    $productForCTP->country = $product->country;
                    $productForCTP->city = $product->city;
                    $productForCTP->attractions = $product->attractions;
                    $productForCTP->countryCode = $product->countryCode;
                    $productForCTP->phoneNumber = $product->phoneNumber;
                    $productForCTP->highlights = $product->highlights;
                    $productForCTP->included = $product->included;
                    $productForCTP->notIncluded = $product->notIncluded;
                    $productForCTP->knowBeforeYouGo = $product->knowBeforeYouGo;
                    $productForCTP->category = $product->category;
                    $productForCTP->tags = $product->tags;
                    $productForCTP->cancelPolicy = $product->cancelPolicy;
                    $productForCTP->options = $product->options;
                    $productForCTP->supplierID = $product->supplierID;
                    $productForCTP->coverPhoto = $product->coverPhoto;
                    $productForCTP->isDraft = $product->isDraft;
                    $productForCTP->isPublished = $product->isPublished;
                    $productForCTP->url = $product->url;
                    $productForCTP->minPrice = $product->minPrice;
                    $productForCTP->rate = $product->rate;
                    $productForCTP->supplierPublished = $product->supplierPublished;
                    $productForCTP->isSpecial = $product->isSpecial;
                    $productForCTP->imageOrder = $product->imageOrder;
                    $productForCTP->productFiles = $product->productFiles;
                    $productForCTP->copyOf = $product->copyOf;
                    $productForCTP->save();

                    if ($productForCTP->productOptionIds != '["null"]') {
                        $productForCTP->options()->detach();
                        $productForCTP->options = $productForCTP->productOptionIds;
                        $productForCTP->options()->attach(json_decode($productForCTP->productOptionIds, true));

                    }


                } else {
                    $adminLog->result = 'failed';
                }

                $adminLog->save();
                $url = $_SERVER['HTTP_REFERER'];
                $pageID = count(explode('=', $url)) > 1 ? explode('=', $url)[1] : 1;
                return redirect('/product/' . '?page=' . $pageID);
            } else {
                if ($product->isDraft == 0) {
                    abort(404);
                } else {
                    if ($product->save()) {
                        $adminLog->result = 'successful Draft';


                        $productForPCTFR = Product::on('mysql2')->findOrFail($product->id);
                        $productForPCTFR->referenceCode = $product->referenceCode;
                        $productForPCTFR->title = $product->title;
                        $productForPCTFR->shortDesc = $product->shortDesc;
                        $productForPCTFR->fullDesc = $product->fullDesc;
                        $productForPCTFR->country = $product->country;
                        $productForPCTFR->city = $product->city;
                        $productForPCTFR->attractions = $product->attractions;
                        $productForPCTFR->countryCode = $product->countryCode;
                        $productForPCTFR->phoneNumber = $product->phoneNumber;
                        $productForPCTFR->highlights = $product->highlights;
                        $productForPCTFR->included = $product->included;
                        $productForPCTFR->notIncluded = $product->notIncluded;
                        $productForPCTFR->knowBeforeYouGo = $product->knowBeforeYouGo;
                        $productForPCTFR->category = $product->category;
                        $productForPCTFR->tags = $product->tags;
                        $productForPCTFR->cancelPolicy = $product->cancelPolicy;
                        $productForPCTFR->options = $product->options;
                        $productForPCTFR->supplierID = $product->supplierID;
                        $productForPCTFR->coverPhoto = $product->coverPhoto;
                        $productForPCTFR->isDraft = $product->isDraft;
                        $productForPCTFR->isPublished = $product->isPublished;
                        $productForPCTFR->url = $product->url;
                        $productForPCTFR->minPrice = $product->minPrice;
                        $productForPCTFR->rate = $product->rate;
                        $productForPCTFR->supplierPublished = $product->supplierPublished;
                        $productForPCTFR->isSpecial = $product->isSpecial;
                        $productForPCTFR->imageOrder = $product->imageOrder;
                        $productForPCTFR->productFiles = $product->productFiles;
                        $productForPCTFR->copyOf = $product->copyOf;
                        $productForPCTFR->save();

                        if ($productForPCTFR->productOptionIds != '["null"]') {
                            $productForPCTFR->options()->detach();
                            $productForPCTFR->options = $productForPCTFR->productOptionIds;
                            $productForPCTFR->options()->attach(json_decode($productForPCTFR->productOptionIds, true));

                        }


                        $productForPCTCOM = Product::on('mysql3')->findOrFail($product->id);
                        $productForPCTCOM->referenceCode = $product->referenceCode;
                        $productForPCTCOM->title = $product->title;
                        $productForPCTCOM->shortDesc = $product->shortDesc;
                        $productForPCTCOM->fullDesc = $product->fullDesc;
                        $productForPCTCOM->country = $product->country;
                        $productForPCTCOM->city = $product->city;
                        $productForPCTCOM->attractions = $product->attractions;
                        $productForPCTCOM->countryCode = $product->countryCode;
                        $productForPCTCOM->phoneNumber = $product->phoneNumber;
                        $productForPCTCOM->highlights = $product->highlights;
                        $productForPCTCOM->included = $product->included;
                        $productForPCTCOM->notIncluded = $product->notIncluded;
                        $productForPCTCOM->knowBeforeYouGo = $product->knowBeforeYouGo;
                        $productForPCTCOM->category = $product->category;
                        $productForPCTCOM->tags = $product->tags;
                        $productForPCTCOM->cancelPolicy = $product->cancelPolicy;
                        $productForPCTCOM->options = $product->options;
                        $productForPCTCOM->supplierID = $product->supplierID;
                        $productForPCTCOM->coverPhoto = $product->coverPhoto;
                        $productForPCTCOM->isDraft = $product->isDraft;
                        $productForPCTCOM->isPublished = $product->isPublished;
                        $productForPCTCOM->url = $product->url;
                        $productForPCTCOM->minPrice = $product->minPrice;
                        $productForPCTCOM->rate = $product->rate;
                        $productForPCTCOM->supplierPublished = $product->supplierPublished;
                        $productForPCTCOM->isSpecial = $product->isSpecial;
                        $productForPCTCOM->imageOrder = $product->imageOrder;
                        $productForPCTCOM->productFiles = $product->productFiles;
                        $productForPCTCOM->copyOf = $product->copyOf;
                        $productForPCTCOM->save();

                        if ($productForPCTCOM->productOptionIds != '["null"]') {
                            $productForPCTCOM->options()->detach();
                            $productForPCTCOM->options = $productForPCTCOM->productOptionIds;
                            $productForPCTCOM->options()->attach(json_decode($productForPCTCOM->productOptionIds, true));

                        }


                        $productForCTP = Product::on('mysql4')->findOrFail($product->id);
                        $productForCTP->referenceCode = $product->referenceCode;
                        $productForCTP->title = $product->title;
                        $productForCTP->shortDesc = $product->shortDesc;
                        $productForCTP->fullDesc = $product->fullDesc;
                        $productForCTP->country = $product->country;
                        $productForCTP->city = $product->city;
                        $productForCTP->attractions = $product->attractions;
                        $productForCTP->countryCode = $product->countryCode;
                        $productForCTP->phoneNumber = $product->phoneNumber;
                        $productForCTP->highlights = $product->highlights;
                        $productForCTP->included = $product->included;
                        $productForCTP->notIncluded = $product->notIncluded;
                        $productForCTP->knowBeforeYouGo = $product->knowBeforeYouGo;
                        $productForCTP->category = $product->category;
                        $productForCTP->tags = $product->tags;
                        $productForCTP->cancelPolicy = $product->cancelPolicy;
                        $productForCTP->options = $product->options;
                        $productForCTP->supplierID = $product->supplierID;
                        $productForCTP->coverPhoto = $product->coverPhoto;
                        $productForCTP->isDraft = $product->isDraft;
                        $productForCTP->isPublished = $product->isPublished;
                        $productForCTP->url = $product->url;
                        $productForCTP->minPrice = $product->minPrice;
                        $productForCTP->rate = $product->rate;
                        $productForCTP->supplierPublished = $product->supplierPublished;
                        $productForCTP->isSpecial = $product->isSpecial;
                        $productForCTP->imageOrder = $product->imageOrder;
                        $productForCTP->productFiles = $product->productFiles;
                        $productForCTP->copyOf = $product->copyOf;
                        $productForCTP->save();

                        if ($productForCTP->productOptionIds != '["null"]') {
                            $productForCTP->options()->detach();
                            $productForCTP->options = $productForCTP->productOptionIds;
                            $productForCTP->options()->attach(json_decode($productForCTP->productOptionIds, true));

                        }


                    } else {
                        $adminLog->result = 'failed Draft';
                    }

                    $adminLog->save();

                    $url = $_SERVER['HTTP_REFERER'];
                    $pageID = count(explode('=', $url)) > 1 ? explode('=', $url)[1] : 1;
                    return redirect('/product/' . '?page=' . $pageID);
                }
            }
        } else if (Auth::guard('supplier')->check()) {
            $product = Product::findOrFail($id);
            if ($product->supplierID != Auth::guard('supplier')->user()->id)
                return redirect()->back()->with(['error' => 'You cannot update this product informations']);

            return $this->supplierProductUpdate($id, $request);
        }
    }

    /**
     * Updates product for pariscitytours.fr
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePCT(Request $request, $id)
    {
        $productPCT = Product::on('mysql2')->findOrFail($id);
        $productPCT->title = $request->title;
        if ($productPCT->isDraft == 1) {
            $productPCT->url = 'product-preview/' . $productPCT->id;
        } else {
            $urlPart = strtolower($productPCT->title);
            $productPCT->url = Str::slug(strtolower($productPCT->city), '-') . '/' . Str::slug($urlPart, '-') . '-' . $productPCT->id;
        }
        $productPCT->shortDesc = $request->shortDesc;
        $productPCT->fullDesc = $request->fullDesc;
        $productPCT->highlights = $request->highlights;
        $productPCT->included = $request->included;
        $productPCT->notIncluded = $request->notIncluded;
        $productPCT->knowBeforeYouGo = $request->knowBeforeYouGo;
        $productPCT->tags = $request->tags;
        $productPCT->save();

        return response()->json(['success' => 'Successful']);
    }

    /**
     * Updates product for pariscitytours.com
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePCTcom(Request $request, $id)
    {
        $productPCT = Product::on('mysql3')->findOrFail($id);
        $productPCT->title = $request->title;
        if ($productPCT->isDraft == 1) {
            $productPCT->url = 'product-preview/' . $productPCT->id;
        } else {
            $urlPart = strtolower($productPCT->title);
            $productPCT->url = Str::slug(strtolower($productPCT->city), '-') . '/' . Str::slug($urlPart, '-') . '-' . $productPCT->id;
        }
        $productPCT->shortDesc = $request->shortDesc;
        $productPCT->fullDesc = $request->fullDesc;
        $productPCT->highlights = $request->highlights;
        $productPCT->included = $request->included;
        $productPCT->notIncluded = $request->notIncluded;
        $productPCT->knowBeforeYouGo = $request->knowBeforeYouGo;
        $productPCT->tags = $request->tags;
        $productPCT->save();

        return response()->json(['success' => 'Successful']);
    }

    /**
     * Updates product for citytours.paris
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCTP(Request $request, $id)
    {
        $productPCT = Product::on('mysql4')->findOrFail($id);
        $productPCT->title = $request->title;
        if ($productPCT->isDraft == 1) {
            $productPCT->url = 'product-preview/' . $productPCT->id;
        } else {
            $urlPart = strtolower($productPCT->title);
            $productPCT->url = Str::slug(strtolower($productPCT->city), '-') . '/' . Str::slug($urlPart, '-') . '-' . $productPCT->id;
        }
        $productPCT->shortDesc = $request->shortDesc;
        $productPCT->fullDesc = $request->fullDesc;
        $productPCT->highlights = $request->highlights;
        $productPCT->included = $request->included;
        $productPCT->notIncluded = $request->notIncluded;
        $productPCT->knowBeforeYouGo = $request->knowBeforeYouGo;
        $productPCT->tags = $request->tags;
        $productPCT->save();

        return response()->json(['success' => 'Successful']);
    }

    /**
     * @param $id
     * @param $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function supplierProductUpdate($id, $request)
    {

        $product = Product::findOrFail($id);
        if (is_null($product->copyOf)) {
            // Delete old copy product
            $oldCopy = Product::where('copyOf', $product->id)->first();
            if ($oldCopy) {
                $oldCopy->options()->detach();
                $oldCopy->productGalleries()->detach();
                $oldCopy->delete();
            }


            //

            $product = Product::with('productGalleries')->with('options')->findOrFail($id);

            //dd($request->all());


            $productCopy = $product->replicate();
            $productCopy->referenceCode = $product->referenceCode . '-EDITED';
            $productCopy->title = $request->title;
            $productCopy->shortDesc = $request->shortDesc;
            $productCopy->fullDesc = $request->fullDesc;
            $productCopy->country = $request->location;
            $productCopy->city = $request->cities;
            $productCopy->countryCode = json_encode($request->countryCode);
            $productCopy->phoneNumber = json_encode($request->phoneNumber);
            $productCopy->highlights = $request->highlights;
            $productCopy->included = $request->included;
            $productCopy->notIncluded = $request->notIncluded;
            $productCopy->category = $request->category;
            $productCopy->knowBeforeYouGo = $request->knowBeforeYouGo;
            $productCopy->cancelPolicy = $request->cancelPolicy;
            $productCopy->attractions = json_encode($request->attractions);
            $productCopy->tags = $request->tags;
            $productCopy->isDraft = (int)$request->isDraft;
            $productCopy->supplierPublished = 1;
            $productCopy->options = $request->productOptionIds;
            $productCopy->isPublished = 0;
            $productCopy->copyOf = (int)$id;


            if (!$request->title || !$request->shortDesc || !$request->fullDesc || !$request->location || !$request->cities
                || !$request->attractions || !$request->countryCode || !$request->phoneNumber || !$request->highlights || !$request->included
                || !$request->notIncluded || !$request->knowBeforeYouGo || !$request->cancelPolicy || !$request->category || !$request->tags
                || !$request->options
            ) {

                $request->isDraft = 1;
                $product->isDraft = 1;

                $product->save();

            }


            if ($productCopy->isDraft == 1) {
                $productCopy->url = 'product-preview/' . $productCopy->id;
            } else {
                $urlPart = strtolower($request->title) . ' ' . strtolower(auth()->user()->companyName);
                $productCopy->url = Str::slug(strtolower($product->city), '-') . '/' . Str::slug($urlPart, '-') . '-' . $product->id;
            }

            $productCopy->push();
            foreach ($product->options()->get() as $option) {
                $productCopy->options()->attach($option->id);
            }
            foreach ($product->productGalleries()->get() as $photo) {
                $productCopy->productGalleries()->attach($photo->id);
            }
        }
        $productCopy->options()->detach();
        $productCopy->options = $request->productOptionIds;
        $productCopy->options()->attach(json_decode($request->productOptionIds, true));

        $fileArr = is_null(json_decode($product->productFiles, true)) ? [] : json_decode($product->productFiles, true);
        $fileArr = $this->storeProductFile($fileArr, $request);

        if (count($fileArr) > 0) {
            $productCopy->productFiles = json_encode($fileArr);
        } else {
            $productCopy->productFiles = null;
        }

        if (!is_null($productCopy->productGalleries()->first())) {
            if (is_null($productCopy->coverPhoto)) {
                $productCopy->coverPhoto = $product->productGalleries()->first()->id;
            }
        }

        return redirect('/product?page=' . $request->page_id);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function destroy($id)
    {
        $adminLog = new Adminlog();
        $adminLog->userID = auth()->user()->id;
        $adminLog->page = 'Product';
        $adminLog->url = env('APP_ADMIN', 'https://admin.cityzore.com') . '/product/' . $id . '/delete';
        $adminLog->action = 'Deleted Product';
        $adminLog->details = auth()->user()->name . ' clicked to Delete Icon Button and deleted product with id ' . $id;
        $adminLog->tableName = 'products';

        $product = Product::findOrFail($id);
        $productPCT = Product::on('mysql2')->find($id);
        $productPCTcom = Product::on('mysql3')->find($id);
        $productCTP = Product::on('mysql4')->find($id);
        if ($product->delete()) {
            if ($productPCT) {
                $productPCT->delete();
            }
            if ($productPCTcom) {
                $productPCTcom->delete();
            }
            if ($productCTP) {
                $productCTP->delete();
            }
            $this->commonFunctions->unsetProductFromProductOrderForOtherPage($id);
            $comments = Comment::where('productID', $id)->get();
            foreach ($comments as $c) {
                $c->delete();
            }
            $adminLog->result = 'successful';
            $adminLog->save();
        }
        return redirect('/product');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function deleteDraft($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return redirect('/');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deletePhoto(Request $request)
    {
        $fileID = intval($request->fileID);


        if ($request->whichPage == 'gallery') {
            $img = ProductGallery::where('id', $fileID)->first();
            $usedAsCoverPhoto = Product::where('coverPhoto', $img->id)->get();
            foreach ($usedAsCoverPhoto as $u) {
                $u->coverPhoto = null;
                $u->save();
                $u->productGalleries()->detach($img->id);
            }
            Storage::disk('s3')->delete('/product-images/' . $img->src);
            Storage::disk('s3')->delete('/product-images-xs/' . $img->src);
            Storage::delete('/product-images/' . $img->src);
            Storage::delete('/product-images-xs/' . $img->src);
            $img->delete();
        } else if ($request->whichPage == 'product') {
            $ownerID = -1;
            if (auth()->guard('supplier')->check()) {
                $ownerID = auth()->user()->id;
            }
            $img = ProductGallery::where('id', $fileID)->first();
            $product = Product::where('id', $request->productId)->first();
            $product->productGalleries()->detach($fileID);

            Storage::disk('s3')->delete('/product-images/' . $img->src);
            Storage::disk('s3')->delete('/product-images-xs/' . $img->src);
            Storage::delete('/product-images/' . $img->src);
            if ($product->coverPhoto == $img->id) {
                $product->coverPhoto = null;
            }
            $product->save();
            $img->delete();
        }else if($request->whichPage == 'soft'){
            $ownerID = -1;
            $img = ProductGallery::where('id', $fileID)->first();
            $product = Product::where('id', $request->productId)->first();
            $product->productGalleries()->detach($fileID);
        }
        return response()->json(['success' => 'Product photo has been deleted', 'fileName' => $img->src]);
//        return response()->json(['success' => 'Product photo has been deleted','up'=>'file id:'.$fileID]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setAsCoverPhoto(Request $request)
    {
        $fileID = $request->fileID;
        $productId = $request->productId;
        $fileName = $request->fileName;

        $img = ProductGallery::findOrFail($fileID);
//        //$img = ProductGallery::where('src', $fileName)->first();
        Product::where('id', $productId)->update(['coverPhoto' => $img->id]);
        Product::on('mysql2')->where('id', $productId)->update(['coverPhoto' => $img->id]);
        Product::on('mysql3')->where('id', $productId)->update(['coverPhoto' => $img->id]);
        Product::on('mysql4')->where('id', $productId)->update(['coverPhoto' => $img->id]);

        return response()->json(['success' => 'Photo is successfully set as cover photo', 'fileName' => $fileName]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getImageGallery(Request $request)
    {
        $ownerID = $request->ownerId;
        $attractions = Attraction::where('cities', 'like', '%'.$request->city.'%')->get();
        $imagesOfCategory = ProductGallery::where('ownerID', $ownerID)->where('category', $request->category)->get();
        if($request->has('galleryAttraction')) {
            $newImagesOfCategory = [];
            foreach($imagesOfCategory as $img) {
                $imgAttractions = json_decode($img->attractions, true);
                if(is_array($imgAttractions) && in_array($request->galleryAttraction, $imgAttractions)) {
                    array_push($newImagesOfCategory, $img);
                }
            }
            $imagesOfCategory = collect($newImagesOfCategory);
        }
        if($request->has('galleryName') && strlen($request->get('galleryName')) > 0) {
            $imagesOfCategory = ProductGallery::where('ownerID', $ownerID)->where('name', 'like', '%' . $request->get('galleryName') . '%')->get();
        }
        $category = null;
        if (count($imagesOfCategory) > 0) {
            $category = $imagesOfCategory->first()->category;
        }else{
            $imagesOfCategory=null;
        }
        $imagesUncategorized = ProductGallery::where('ownerID', $ownerID)->where('category', null)->get();
        return response()->json(
            [
                'success' => 'Successful!',
                'imagesOfCategory' => $imagesOfCategory,
                'imagesUncategorized' => $imagesUncategorized,
                'category' => $category,
                'attractions' => $attractions
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setImagesForProduct(Request $request)
    {
        $imageIds = $request->imageIds;
        $category = $request->category;
        $productId = $request->productId;
        $product = Product::findOrFail($productId);
        $images = [];
        $attachedIds = [];
        foreach ($product->productGalleries as $gallery) {
            array_push($attachedIds, $gallery->id);
        }
        $boolArr = [];
        foreach ($imageIds as $id) {
            if (!in_array(intval($id), $attachedIds)) {
                $image = ProductGallery::findOrFail($id);
                $image->category = $category;
                $image->attractions = json_encode($request->attractions);
                $image->save();
                $product->productGalleries()->attach($id);
                array_push($images, $image);
                array_push($boolArr, true);
            } else {
                array_push($boolArr, false);
            }
        }
        if (!empty($product->imageOrder)) {
            $imageOrder = $product->imageOrder;
            $imageOrder = json_decode($imageOrder);
            foreach ($imageIds as $imageId) {
                $imageOrder[] = $imageId;
            }
            $product->imageOrder = json_encode($imageOrder);
            $product->save();
        }


        if (in_array(false, $boolArr)) {
            return response()->json(['error' => 'Some of images has been stored before! Please try another image']);
        }

        return response()->json(['success' => 'Successful!', 'images' => $images]);
    }

    /**
     * Updates product when clicking next button
     *
     * @param Request $request
     */
    public function updateProductStepByStep(Request $request)
    {
        $whichStep = $request->whichStep;
        $values = $request->values;
        $productId = $request->productId;

        $product = Product::findOrFail($productId);

        if ($whichStep == '0') {
            $product->title = $values['title'];
            $product->shortDesc = $values['shortDesc'];
            $product->fullDesc = $values['fullDesc'];
            $product->countryCode = json_encode($values['countryCodes']);
            $product->phoneNumber = json_encode($values['phoneNumbers']);
            $product->country = $values['location'];
            $product->city = $values['cities'];
            $product->attractions = json_encode($values['attractions']);
        }
        if ($whichStep == '1') {
            $highlights = json_decode($values['highlights'], true);
            $highlights = collect($highlights);
            $highlights = $highlights->map(function ($row) {
                return $row['value'];
            })->toArray();
            $highlights = implode('|', $highlights);
            $product->highlights = $highlights;

            $includes = json_decode($values['included'], true);
            $includes = collect($includes);
            $includes = $includes->map(function ($row) {
                return $row['value'];
            })->toArray();
            $includes = implode('|', $includes);
            $product->included = $includes;

            $notIncluded = json_decode($values['notIncluded'], true);
            $notIncluded = collect($notIncluded);
            $notIncluded = $notIncluded->map(function ($row) {
                return $row['value'];
            })->toArray();
            $notIncluded = implode('|', $notIncluded);
            $product->notIncluded = $notIncluded;

            $knowBeforeYouGo = json_decode($values['knowBeforeYouGo'], true);
            $knowBeforeYouGo = collect($knowBeforeYouGo);
            $knowBeforeYouGo = $knowBeforeYouGo->map(function ($row) {
                return $row['value'];
            })->toArray();
            $knowBeforeYouGo = implode('|', $knowBeforeYouGo);
            $product->knowBeforeYouGo = $knowBeforeYouGo;

            $product->cancelPolicy = $values['cancelPolicy'];

            $tags = json_decode($values['tags'], true);
            $tags = collect($tags);
            $tags = $tags->map(function ($row) {
                return $row['value'];
            })->toArray();
            $tags = implode('|', $tags);
            $product->tags = $tags;

            $product->category = $values['category'];
        }
        $product->save();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function productDraft(Request $request)
    {
        $whichStep = $request->whichStep;
        $values = $request->values;
        $productId = $request->productId;

        //Auth Supplier ID Check
        if (Auth::guard('supplier')->check()) {
            $supplier_id = Auth::guard('supplier')->user()->id;
            $refCode = $this->refCodeGenerator->refCodeGenerator();
        }
        //If Admin => -1
        if (Auth::guard('admin')->check()) {
            $supplier_id = -1;
            $rand = rand(0, 99999);
            $refCode = 'PBT' . +$rand;
        }
        if ($whichStep == '1') {
            if ($productId == '') {
                $product = new Product();
            } else {
                $product = Product::findOrFail($productId);
            }
            $product->title = $values['title'];
            $product->shortDesc = $values['shortDesc'];
            $product->fullDesc = $values['fullDesc'];
            $product->countryCode = json_encode($values['countryCodes']);
            $product->phoneNumber = json_encode($values['phoneNumbers']);
            $product->country = $values['location'];
            $product->city = $values['cities'];
            $product->attractions = json_encode($values['attractions']);
        }
        if ($whichStep == '2') {
            $product = Product::findOrFail($productId);

            $highlights = json_decode($values['highlights'], true);
            $highlights = collect($highlights);
            $highlights = $highlights->map(function ($row) {
                return $row['value'];
            })->toArray();
            $highlights = implode('|', $highlights);
            $product->highlights = $highlights;

            $includes = json_decode($values['included'], true);
            $includes = collect($includes);
            $includes = $includes->map(function ($row) {
                return $row['value'];
            })->toArray();
            $includes = implode('|', $includes);
            $product->included = $includes;

            $notIncluded = json_decode($values['notIncluded'], true);
            $notIncluded = collect($notIncluded);
            $notIncluded = $notIncluded->map(function ($row) {
                return $row['value'];
            })->toArray();
            $notIncluded = implode('|', $notIncluded);
            $product->notIncluded = $notIncluded;

            $knowBeforeYouGo = json_decode($values['knowBeforeYouGo'], true);
            $knowBeforeYouGo = collect($knowBeforeYouGo);
            $knowBeforeYouGo = $knowBeforeYouGo->map(function ($row) {
                return $row['value'];
            })->toArray();
            $knowBeforeYouGo = implode('|', $knowBeforeYouGo);
            $product->knowBeforeYouGo = $knowBeforeYouGo;

            $product->cancelPolicy = $values['cancelPolicy'];

            $tags = json_decode($values['tags'], true);
            $tags = collect($tags);
            $tags = $tags->map(function ($row) {
                return $row['value'];
            })->toArray();
            $tags = implode('|', $tags);
            $product->tags = $tags;

            $product->category = $values['category'];
        }
        $product->options = '';
        $product->referenceCode = $refCode;
        $product->isDraft = 1;
        $product->supplierID = $supplier_id;
        $product->supplierPublished = 0;
        $product->isPublished = 0;
        $product->save();
        $product->url = 'product-preview/' . $product->id;
        $product->save();
        if (Auth::guard('supplier')->check() && $whichStep == '1') {
            $product->supplier()->attach($product->supplierID);
        }
        return response()->json(['success' => 'Product has been created', 'product_id' => $product->id]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeDraftStatus(Request $request)
    {
        $adminLog = new Adminlog();
        $adminLog->userID = auth()->user()->id;
        $adminLog->page = 'Product';
        $adminLog->action = 'Confirmed/Draft Product';
        $adminLog->url = env('APP_ADMIN', 'https://admin.cityzore.com') . '/changeProductDraftStatus';
        $adminLog->tableName = 'products';


        switch ($request->platform) {
            case 'main':
                $product = Product::findOrFail($request->id);
                break;

            case 'pct':
                $product = Product::on("mysql2")->findOrFail($request->id);
                break;

            case 'pctcom':
                $product = Product::on("mysql3")->findOrFail($request->id);
                break;

            case 'ctp':
                $product = Product::on("mysql4")->findOrFail($request->id);
                break;

            default:
                # code...
                break;
        }


        $product->isDraft = $request->isDraft;
        if ($product->isDraft == 1) {
            $adminLog->details = auth()->user()->name . ' clicked to Confirmed/Draft toggle and changed product status to draft for product with id ' . $request->id;
            $product->url = 'product-preview/' . $request->id;
            $product->isPublished = 0;
            $product->supplierPublished = 0;
        } else {

            //return response()->json($product);
            if (!$product->title || !$product->shortDesc || !strip_tags($product->fullDesc) || !$product->highlights || !$product->included
                || !$product->notIncluded || !$product->knowBeforeYouGo || !$product->category || !$product->tags || !$product->cancelPolicy
            ) {
                return response()->json(["status" => "0", 'error' => "Make sure to fill in all required fields for this product"]);
            }


            $adminLog->details = auth()->user()->name . ' clicked to Confirmed/Draft toggle and changed product status to confirmed for product with id ' . $request->id;
            $url = Str::slug(strtolower($product->city), '-') . '/' . Str::slug(strtolower($product->title), '-') . '-' . $product->id;
            $product->url = $url;
            $product->supplierPublished = 1;
        }

        if ($product->save()) {
            $this->commonFunctions->unsetProductFromProductOrderForOtherPage($request->id);
            $adminLog->result = 'successful';
        } else {
            $adminLog->result = 'failed';
        }
        $adminLog->save();

        return response()->json(['status' => '1', 'success' => 'Status change successfully.', 'isDraft' => $product->isDraft]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePublishedStatus(Request $request)
    {
        $adminLog = new Adminlog();
        $adminLog->userID = auth()->user()->id;
        $adminLog->page = 'Product';
        $adminLog->action = 'Published/Not Published Product';
        $adminLog->url = env('APP_ADMIN', 'https://admin.cityzore.com') . '/changeProductPublishedStatus';
        $adminLog->tableName = 'products';


        /*    $product = Product::findOrFail($request->id);
            $productPCT = Product::on('mysql2')->find($request->id);
            $productPCTcom = Product::on('mysql3')->find($request->id);
            $productCTP = Product::on('mysql4')->find($request->id);*/

        switch ($request->platform) {
            case 'main':
                $product = Product::findOrFail($request->id);
                break;

            case 'pct':
                $product = Product::on("mysql2")->findOrFail($request->id);
                break;

            case 'pctcom':
                $product = Product::on("mysql3")->findOrFail($request->id);
                break;

            case 'ctp':
                $product = Product::on("mysql4")->findOrFail($request->id);
                break;

            default:
                # code...
                break;
        }


        $isPublished = $product->isDraft == 1 ? 0 : $request->isPublished;
        $product->isPublished = $isPublished;
        /*    if ($productPCT) {
                $productPCT->isPublished = $isPublished;
            }
            if ($productPCTcom) {
                $productPCTcom->isPublished = $isPublished;
            }
            if ($productCTP) {
                $productCTP->isPublished = $isPublished;
            }*/
        if ($isPublished == 1) {
            $adminLog->details = auth()->user()->name . ' clicked to Published/Not Published Toggle and Published Product ' . $request->platform . ' with id ' . $request->id;
            $url = Str::slug(strtolower($product->city), '-') . '/' . Str::slug(strtolower($product->title), '-') . '-' . $product->id;
            $product->url = $url;
            /*  if ($productPCT) {
                  $productPCT->url = $url;
              }
              if ($productPCTcom) {
                  $productPCTcom->url = $url;
              }
              if ($productCTP) {
                  $productCTP->url = $url;
              }*/
        } else {
            $adminLog->details = auth()->user()->name . ' clicked to Published/Not Published Toggle and Not Published Product ' . $request->platform . ' with id ' . $request->id;
            $product->url = 'product-preview/' . $request->id;
            /*   if ($productPCT) {
                   $productPCT->url = 'product-preview/'.$request->id;
               }
               if ($productPCTcom) {
                   $productPCTcom->url = 'product-preview/'.$request->id;
               }
               if ($productCTP) {
                   $productCTP->url = 'product-preview/'.$request->id;
               }*/
        }
        if ($product->save()) {
            /*    if ($productPCT) {
                    $productPCT->save();
                }
                if ($productPCTcom) {
                    $productPCTcom->save();
                }
                if ($productCTP) {
                    $productCTP->save();
                }*/
            if (!is_null($product->copyOf)) {
                $oldProduct = Product::where('id', $product->copyOf)->first();
                $oldProduct->delete();
                $product->id = (int)$product->copyOf;
                $product->referenceCode = explode('-', $product->referenceCode)[0];
                $product->copyOf = null;
                $product->save();
            }
            $this->commonFunctions->unsetProductFromProductOrderForOtherPage($request->id);
            $adminLog->result = 'successful';
            $supplier = $product->supplier()->first();
            if (is_null($supplier)) {
                $companyName = 'Paris Business and Travel';
                $email = 'contact@parisviptrips.com';
                $sendToCC = true;
            } else {
                $companyName = $supplier->companyName;
                $email = $supplier->email;
                $sendToCC = false;
            }
            $mail = new Mails();
            $data = [];
            $options = $product->options()->get();
            $mail->to = $email;

            if ($product->isPublished == 1) {
                array_push($data, ['sendToCC' => $sendToCC, 'subject' => 'Your Product is Activated !', 'companyName' => $companyName, 'referenceCode' => $product->referenceCode, 'options' => $options]);
                $mail->blade = 'mail.product-activated';
            } else {
                array_push($data, ['sendToCC' => $sendToCC, 'subject' => 'Your Product is Deactivated !', 'companyName' => $companyName, 'referenceCode' => $product->referenceCode, 'options' => $options]);
                $mail->blade = 'mail.product-deactivated';
            }
            $mail->data = json_encode($data);
            $mail->save();
        } else {
            $adminLog->result = 'failed';
        }
        $adminLog->save();

        return response()->json(['success' => 'Status changed successfully.']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function supplierPublished(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->supplierPublished = $request->supplierPublished;
        $product->save();

        return response()->json(['success' => 'Status change successfully.']);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getProductGalleryData(Request $request)
    {
        $product = Product::findOrFail($request->productID);
        $productGalleries = $product->productGalleries()->get();

        return ['productGalleries' => $productGalleries];
    }

    /**
     * Checks if title is used before
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function titleValidation(Request $request)
    {
        $supplierID = -1;
        if (auth()->guard('supplier')->check()) {
            $supplierID = auth()->user()->id;
        }
        $isThereAnyProduct = Product::where('title', $request->title)->where('supplierID', $supplierID)
            ->where('id', '!=', $request->productID)->count();
        if ($isThereAnyProduct > 0) {
            return response()->json(['error' => 'There is a product using same title. <br> Please change it before submitting.']);
        }
        return response()->json(['success' => 'There is no product using this title.']);
    }

    public function isFilledAll(Request $request)
    {
        //return response()->json($request->all());

        if ($request->productIsDraft == "0" || $request->isDraft == "0") {

            if (!$request->title || !$request->shortDesc || !strip_tags($request->fullDesc) || $request->countryCode == [null] || !$request->cities

                || $request->phoneNumber == [null] || !$request->highlights || !$request->included || !$request->notIncluded || !$request->knowBeforeYouGo
                || !$request->cancelPolicy || !$request->category || !$request->tags) {


                return response()->json(['status' => '0', "message" => "Make sure You Fill in All Required Fields!"]);
            }
        }

        return response()->json(['status' => '1', "message" => "Ready To Submit!!"]);


    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOptions(Request $request)
    {
        $ownerID = -1;
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->guard('supplier')->user()->id;
        }
        $options = Option::where('supplierID', $ownerID)->where('isPublished', 1)->get();
        $product = Product::findOrFail($request->productId);
        $attachedOptions = $product->options()->select('id')->get();
        return response()->json(['success' => 'Options fetched successfully', 'options' => $options, 'attachedOptions' => $attachedOptions]);
    }

    public function getOptionsAjax(Request $request)
    {
        if (Auth::guard('supplier')->check()) {
            $options = Option::where('supplierID', Auth::guard('supplier')->user()->id)->get();
        } else {

            return $options = Option::where('supplierID', -1)->get();
        }

        return response()->json($options);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getCities(Request $request)
    {
        $country = Country::findOrFail($request->countryID);
        $citiesArr = [];
        $cities = $country->cities;
        foreach ($cities as $c) {
            array_push($citiesArr, $c->name);
        }

        return ['cities' => $citiesArr];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductImages(Request $request)
    {
        $product = Product::findOrFail($request->productId);
        $productImages = $product->productGalleries()->get();
        $productImagesOrdered = $productImages;
        if (!is_null($product->imageOrder)) {
            $imageOrder = json_decode($product->imageOrder, true);
            $productImagesOrdered = [];

            foreach ($productImages as $i => $productImage) {
                $productImagesOrdered[$i] = $productImages->where('id', $imageOrder[$i])->first();
            }
        }
        return response()->json(['productImages' => $productImagesOrdered]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderImages(Request $request)
    {
        $product = Product::findOrFail($request->productId);
        $product->imageOrder = json_encode($request->orderArr);
        if ($product->save()) {
            return response()->json(['success' => 'Successful']);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function copyProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $newProduct = new Product();
        if (Auth::guard('supplier')->check()) {
            $refCode = Auth::guard('supplier')->user()->companyShortCode . $this->refCodeGenerator->refCodeGenerator();
        }

        if (Auth::guard('admin')->check()) {
            $rand = rand(0, 99999);
            $refCode = 'PBT' . +$rand;
        }

        $newProduct->referenceCode = $refCode;
        $newProduct->title = $product->title . ' (Copy)';
        $newProduct->shortDesc = $product->shortDesc;
        $newProduct->fullDesc = $product->fullDesc;
        $newProduct->country = $product->country;
        $newProduct->city = $product->city;
        $newProduct->attractions = $product->attractions;
        $newProduct->countryCode = $product->countryCode;
        $newProduct->phoneNumber = $product->phoneNumber;
        $newProduct->highlights = $product->highlights;
        $newProduct->included = $product->included;
        $newProduct->notIncluded = $product->notIncluded;
        $newProduct->knowBeforeYouGo = $product->knowBeforeYouGo;
        $newProduct->category = $product->category;
        $newProduct->tags = $product->tags;
        $newProduct->cancelPolicy = $product->cancelPolicy;
        $newProduct->options = $product->options;
        $newProduct->supplierID = $product->supplierID;
        $newProduct->coverPhoto = $product->coverPhoto;
        $newProduct->isDraft = 1;
        $newProduct->isPublished = 0;
        $newProduct->url = $product->url . '-copy';
        $newProduct->minPrice = $product->minPrice;
        $newProduct->rate = $product->rate;
        $newProduct->supplierPublished = 0;
        $newProduct->isSpecial = 0;
        $newProduct->imageOrder = $product->imageOrder;
        $newProduct->productFiles = $product->productFiles;

        $newProduct->save();
        $newProduct->url = $newProduct->url . $product->id;
        $newProduct->save();

        $optionsDecoded = json_decode($product->options, true);
        $newProduct->options()->attach($optionsDecoded);
        foreach ($product->productGalleries as $pg) {
            $newProduct->productGalleries()->attach($pg->id);
        }

        return redirect('/product?page=1');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function productCheck()
    {
        return view('panel.products.product-check');
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function productPreview(Request $request, $id)
    {
        // This function is created only for testing purposes. It will be deleted after availability is truly functional.
        // product_id must be changed for testing S, O, S+S, O+O, S+O
        $product = Product::findOrFail($id);
        $options = $product->options()->get();
        $prices = [];
        foreach ($options as $o) {
            $pricing = $o->pricings()->first()->adultPriceCom;
            array_push($prices, $pricing);
        }
        $minPrices = 0;
        if (count($prices) > 0) {
            $minPrices = min($prices);
        }
        $image = ProductGallery::findOrFail($product->coverPhoto)->src;
        $productImages = $product->productGalleries()->get();
        $optionName = null;
        $totalPrice = null;
        $cart = null;
        $user = Auth::guard('web')->user();

        if ($user) {
            $cart = Cart::where('userID', '=', Auth::guard('web')->user()->id)->where('status', '=', 0)->get();
        } else {
            $cart = Cart::where('userID', '=', session()->get('uniqueID'))->where('status', '=', 0)->get();
        }

        $cartCount = count($cart);

        $items = [];
        $ids = [];
        $totalPrice = [];
        foreach ($cart as $c) {
            array_push($items, json_decode($c->bookingItems, true));
            array_push($ids, json_decode($c->id, true));
            array_push($totalPrice, json_decode($c->totalPrice, true));
        }

        foreach ($options as $opt) {
            $optionID = $opt->id;
            $optionName = $options->where('id', '=', $optionID)->first()->title;
        }

        $userID = -1;
        if (auth()->guard('supplier')->check()) {
            $userID = auth()->user()->id;
        }
        $config = Config::where('userID', $userID)->first();

        return view('panel.products.product-preview', [
            'items' => $items,
            'cart' => $cart,
            'product' => $product,
            'options' => $options,
            'image' => $image,
            'optionName' => $optionName,
            'totalPrice' => $totalPrice,
            'ids' => $ids,
            'cartCount' => $cartCount,
            'minPrices' => $minPrices,
            'productImages' => $productImages,
            'prices' => $prices,
            'config' => $config
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function switchAll(Request $request)
    {
        $route = $request->route;

        if ($route == 'productPCT') {
            return redirect('/' . $route);
        } else if ($route == 'blogPCT') {
            return redirect('/' . $route);
        } else if ($route == 'productTranslationsPCT') {
            return redirect('/general-config/product-translations-pct');
        } else if ($route == 'productMetaTagsTranslationsPCT') {
            return redirect('/general-config/product-meta-tags-translations-pct');
        } else if ($route == 'blogTranslationsPCT') {
            return redirect('/general-config/blog-translations-pct');
        } else if ($route == 'blogMetaTagsTranslationsPCT') {
            return redirect('/general-config/blog-meta-tags-translations-pct');
        }

        return redirect('/product');
    }

}
