<?php

namespace App\Http\Controllers\Admin;

use App\Adminlog;
use App\ProductGallery;
use App\Supplier;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Throwable;
use App\Country;
use App\CityImage;
use function MongoDB\BSON\toJSON;
use App\City;
use App\Attraction;


class GalleryController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $countries = Country::has('cities')->get();
        $ownerID = -1;
        $titleSuffix = '';
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->user()->id;
            $titleSuffix = '|' . auth()->user()->companyName;
        }
        $suppliers = Supplier::where('isActive', 1)->get(['id', 'companyName']);
        $attractions = Attraction::all();
        $galleryCollection = new ProductGallery();
        if ($request->has('galleryID')) {
            $gallery = [];
            $galleryCollection = $galleryCollection->where('id', $request->galleryID)->get();
            foreach ($galleryCollection as $img) {
                $country = Country::where('id', City::where('name', $img->category)->first()->countryID)->first();
                array_push($gallery, [
                    'id' => $img->id,
                    'alt' => $img->alt,
                    'name' => $img->name,
                    'src' => $img->src,
                    'category' => $img->category,
                    'attractions' => $img->attractions,
                    'ownerID' => $img->ownerID,
                    'uploadedBy' => $img->uploadedBy,
                    'country' => $country->countries_name
                ]);
            }
        } else if ($request->has('galleryName')) {
            $gallery = $galleryCollection->where('name', 'like', '%' . $request->galleryName . '%')->get();
        } else if ($request->has('supplierID')) {
            $gallery = [];
            $galleryCollection = $galleryCollection->where('ownerID', $request->supplierID)->get();
            foreach ($galleryCollection as $img) {
                $country = Country::where('id', City::where('name', $img->category)->first()->countryID)->first();
                array_push($gallery, [
                    'id' => $img->id,
                    'alt' => $img->alt,
                    'name' => $img->name,
                    'src' => $img->src,
                    'category' => $img->category,
                    'attractions' => $img->attractions,
                    'ownerID' => $img->ownerID,
                    'uploadedBy' => $img->uploadedBy,
                    'country' => $country->countries_name
                ]);
            }
        } else if ($request->has('attraction')) {
            $gallery = [];
            $galleryCollection = $galleryCollection->where('ownerID', $ownerID)->orderBy('id', 'ASC')->get();
            foreach ($galleryCollection as $img) {
                if ((!is_null($img->attractions) && $img->attractions != 'null') && in_array($request->attraction, json_decode($img->attractions, true))) {
                    $country = Country::where('id', City::where('name', $img->category)->first()->countryID)->first();
                    array_push($gallery, [
                        'id' => $img->id,
                        'alt' => $img->alt,
                        'name' => $img->name,
                        'src' => $img->src,
                        'category' => $img->category,
                        'attractions' => $img->attractions,
                        'ownerID' => $img->ownerID,
                        'uploadedBy' => $img->uploadedBy,
                        'country' => $country->countries_name
                    ]);
                }
            }
        } else if ($request->has('city')) {
            $gallery = $galleryCollection->where('ownerID', $ownerID)->where('category', $request->city)->orderBy('id', 'ASC')->get();
        } else {
            $gallery = $galleryCollection->where('ownerID', $ownerID)->orderBy('id', 'ASC')->get();
        }

        return view('panel.gallery.index',
            [
                'gallery' => $gallery,
                'titleSuffix' => $titleSuffix,
                'countries' => $countries,
                'country' => $request->country,
                'city' => $request->city,
                'attraction' => $request->attraction,
                'suppliers' => $suppliers,
                'supplierID' => $request->supplierID,
                'galleryID' => $request->galleryID,
                'galleryName' => $request->galleryName,
                'attractions' => $attractions
            ]
        );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {

        $countries = Country::has('cities')->get();
        $ownerID = -1;
        if (auth()->guard('supplier')->check()) {
            $ownerID = auth()->user()->id;
        }
        return view('panel.gallery.create',
            [
                'ownerID' => $ownerID,
                'countries' => $countries
            ]
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $userID = -1;
        $titleSuffix = '';

        if (auth()->guard('supplier')->check()) {
            $userID = auth()->user()->id;
            $titleSuffix = '|' . auth()->user()->companyName;
        }

        $name = $request->name;
        $alt = $request->alt;

        $sameAttrValidation = ProductGallery::where('id', '!=', $request->id)->where('ownerID', $userID)
            ->where(function ($query) use ($name, $alt, $titleSuffix) {
                $query->where('name', $name . $titleSuffix)
                    ->orWhere('alt', $alt . $titleSuffix);
            })->get();

        if (count($sameAttrValidation) > 0) {
            return response()->json(['error' => 'An image is using same Name or Alt']);
        }

        $gallery = ProductGallery::findOrFail($request->id);
        $gallery->alt = $request->alt . $titleSuffix;
        $gallery->name = $request->name . $titleSuffix;
        if($request->has('attractions'))
            $gallery->attractions = json_encode($request->attractions);
        else
            $gallery->attractions = json_encode([""]);
        $gallery->save();
        return response()->json(
            [
                'success' => 'Image has been updated',
                'id' => $gallery->id,
                'alt' => $gallery->alt,
                'name' => $gallery->name,
                'titleSuffix' => $titleSuffix,
                'attractions' => $gallery->attractions
            ]
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $productGallery = ProductGallery::findOrFail($id);
        Storage::disk('s3')->delete('/product-images/' . $productGallery->src);
        Storage::disk('s3')->delete('/product-images-xs/' . $productGallery->src);
        Storage::delete('/product-images/' . $productGallery->src);
        Storage::delete('/product-images-xs/' . $productGallery->src);
        $productGallery->delete();
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @param $which
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPhoto(Request $request, $which)
    {
        if ($request->isAjax) {
            if(!($request->type=="image/jpeg" || $request->type=="image/png" || $request->type=="image/webp"))
                return response()->json(['error' => 'You can\'t upload this file.Allowable file types jpg,png,webp :'.$request->type]);

            $ownerID = -1;
            $supplier = null;
            if (auth()->guard('supplier')->check()) {
                $ownerID = auth()->user()->id;
                $supplier = Supplier::findOrFail($ownerID);
            }
            $fileName = $request->fileName;

            $file_index = strrpos($fileName, '.', 0);
            $fileNameRaw = substr($fileName, 0, $file_index);
            $fileExtRaw = substr($fileName, $file_index + 1, strlen($fileName));


            $isUploadedBefore = ProductGallery::where('alt', $fileNameRaw)->count();
            $isThereAny = ProductGallery::where('alt', $fileNameRaw)->where('ownerID', $ownerID)->count();

            if ($isThereAny == 0) {
                $fileExt = $fileExtRaw;
                $fileNameWithoutExt = $fileNameRaw;
                $slug = Str::slug($fileNameRaw) . '.' . $fileExt;
                $productGallery = new ProductGallery();
                $productGallery->alt = $fileNameRaw;
                $productGallery->name = $fileNameRaw;
                $productGallery->src = $slug;
                $productGallery->ownerID = $ownerID;
                $productGallery->uploadedBy = auth()->user()->name;

                if ($which == 'product') {
                    $productGallery->category = $request->location;
                    $productGallery->attractions = json_encode($request->attractions);
                }

                if ($which == 'gallery') {
                    if ($request->location != "null") {
                        $productGallery->category = $request->location;
                    }
                    if ($request->attractions != "null") {
                        $productGallery->attractions = json_encode(explode(',', $request->attractions));
                    }


                }
                if ($productGallery->save()) {
                    if ($isUploadedBefore == 0) {


                        $data = $request->img_data;
                        $image_array_1 = explode(";", $data);
                        $image_array_2 = explode(",", $image_array_1[1]);

                        $data = base64_decode($image_array_2[1]);

                        Storage::put('product-images/' . $slug, $data);
                        Storage::disk('s3')->put('product-images/' . $slug, $data);
                        $this->makeImage($slug, 'product-images-xs', 248, 149);
                    }
                    if (!is_null($supplier)) {
                        $supplier->productGallery()->attach($productGallery->id);
                    }
                    if ($which == 'product') {
                        $product = Product::findOrFail($request->productId);
                        $product->productGalleries()->attach($productGallery->id);
                        if (!empty($product->imageOrder)) {
                            $imageOrder = $product->imageOrder;
                            $imageOrder = json_decode($imageOrder);
                            $imageOrder[] = $productGallery->id;
                            $product->imageOrder = $imageOrder;
                            $product->save();
                        }

                    }
                }
                $log = new Adminlog();
                $log->userID = Auth::guard('admin')->user()->id;
                $log->page = 'Gallery';
                $log->url = url()->current();
                $log->action = 'Upload Photo';
                $log->details = 'Image uploaded :' . $fileName;
                $log->tableName = 'product_gallerys';
                $log->result = 'successful';
                $log->save();
                return response()->json(['success' => 'Images have been stored.', 'imageID' => $productGallery->id, 'src' => $productGallery->src, 'file_name' => $fileNameRaw]);
            }

            return response()->json(['error' => 'This image has been stored before. Please try another image!(Resize)']);

        } else {
            $file = $request->file('file');
            if (gettype($file) == 'object') {
                $ownerID = -1;
                $supplier = null;
                if (auth()->guard('supplier')->check()) {
                    $ownerID = auth()->user()->id;
                    $supplier = Supplier::findOrFail($ownerID);
                }
                $fileName = $request->get('fileName');
                $fileNameExploded = explode('.', $fileName);
                $isUploadedBefore = ProductGallery::where('alt', $fileNameExploded[0])->count();
                $isThereAny = ProductGallery::where('alt', $fileNameExploded[0])->where('ownerID', $ownerID)->count();
                if ($isThereAny == 0) {
                    $fileExt = $file->getClientOriginalExtension();
                    $fileNameWithoutExt = $file->getClientOriginalName();
                    $slug = Str::slug($fileNameExploded[0]) . '.' . $fileExt;
                    $productGallery = new ProductGallery();
                    $productGallery->alt = $fileNameExploded[0];
                    $productGallery->name = $fileNameExploded[0];
                    $productGallery->src = $slug;
                    $productGallery->ownerID = $ownerID;
                    $productGallery->uploadedBy = auth()->user()->name;
                    if ($which == 'product') {
                        $productGallery->category = $request->location;
                        $productGallery->attractions = json_encode(explode(',', $request->get('attractions')));
                    }

                    if ($which == 'gallery') {
                        if ($request->location != "null") {
                            $productGallery->category = $request->location;
                        }
                        if ($request->get('attractions') != "null") {
                            $productGallery->attractions = json_encode(explode(',', $request->get('attractions')));
                        }


                    }
                    if ($productGallery->save()) {
                        if ($isUploadedBefore == 0) {


                            $file->storeAs('product-images', $slug);
                            Storage::disk('s3')->put('product-images/'.$slug, file_get_contents($file));
                            $this->makeImage($slug, 'product-images-xs', 248, 149);
                        }
                        if (!is_null($supplier)) {
                            $supplier->productGallery()->attach($productGallery->id);
                        }
                        if ($which == 'product') {
                            $product = Product::findOrFail($request->productId);
                            $product->productGalleries()->attach($productGallery->id);
                            if (!empty($product->imageOrder)) {
                                $imageOrder = $product->imageOrder;
                                $imageOrder = json_decode($imageOrder);
                                $imageOrder[] = $productGallery->id;
                                $product->imageOrder = $imageOrder;
                                $product->save();
                            }

                        }
                    }
                    $log = new Adminlog();
                    $log->userID = Auth::guard('admin')->user()->id;
                    $log->page = 'Gallery';
                    $log->url = url()->current();
                    $log->action = 'Upload Photo';
                    $log->details = 'Image uploaded :' . $fileName;
                    $log->tableName = 'product_gallerys';
                    $log->result = 'successful';
                    $log->save();
                    return response()->json(['success' => 'Images have been stored.', 'imageID' => $productGallery->id, 'src' => $productGallery->src]);
                }
            }

            return response()->json(['error' => 'This image has been stored before. Please try another image!']);
        }


    }

    /**
     * Function can be extended and made more global. It works as intended right now. Creates images and stores it on
     * amazon s3
     *
     * @param $fileName
     * @param $folder
     * @param $width
     * @param $height
     * @return bool
     */
    public function makeImage($fileName, $folder, $width, $height)
    {
        $newImg = Image::canvas($width, $height);
        $newImg->save(storage_path('app/public/' . $folder . '/' . $fileName));
        $img = Image::make(storage_path('app/public/product-images/' . $fileName));
        $img->fit($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $newImg->insert($img, 'center');
        $newImg->save(storage_path('app/public/' . $folder . '/' . $fileName));
        Storage::disk('s3')->put($folder . '/' . $fileName, file_get_contents(storage_path('app/public/product-images-xs/' . $fileName)));
        return true;
    }


    /**
     * Changes xs images from 150x150 to  248x149
     */

    public function makeImageCustom($fileName, $folder, $width, $height,$data)
    {
        $newImg = Image::canvas($width, $height);
        $newImg->save(storage_path('app/public/' . $folder . '/' . $fileName));
        $img = Image::make(storage_path('app/public/product-images/' . $fileName));
        $img->fit($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $newImg->insert($img, 'center');
        $newImg->save(storage_path('app/public/' . $folder . '/' . $fileName));
//        Storage::disk('s3')->put($folder . '/' . $fileName, file_get_contents(storage_path('app/public/product-images-xs/' . $fileName)));
        return true;
    }
    public function changeXS()
    {
        // id range is 50, you need to change it to cover all images
        $images = ProductGallery::where('id', '>', 0)->where('id', '<', 51)->get();
        foreach ($images as $image) {
            if ($image->src != '') {
                try {
                    $this->makeImage2($image->src, 'testsmall', 248, 149);
                } catch (Throwable $e) {
                    report($e);
                }
            }
        }
    }

    /**
     * Test function for making images.
     * To use this function you need to create a folder named testbig in storage/app/public
     *
     * @param $fileName
     * @param $folder
     * @param $width
     * @param $height
     * @return bool
     */
    public function makeImage2($fileName, $folder, $width, $height)
    {
        $newImg = Image::canvas($width, $height);
        $newImg->save(storage_path('app/public/' . $folder . '/' . $fileName));
        $img = Image::make(storage_path('app/public/testbig/' . $fileName));
        $img->fit($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $newImg->insert($img, 'center');
        $newImg->save(storage_path('app/public/' . $folder . '/' . $fileName));
        return true;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cityPhotos()
    {
        $cityPhotos = CityImage::all();

        return view('panel.gallery.cityphotos', ['cityPhotos' => $cityPhotos]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addCityPhoto()
    {
        $countries = Country::has('cities')->get();

        return view('panel.gallery.addcityphoto', ['countries' => $countries]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function uploadPhotoForCity(Request $request)
    {
        $file = $request->file('coverPhoto');

        $savedBefore = CityImage::where('countryID', $request->countries)->where('city', $request->cities)->get();
        if (is_null($savedBefore) || count($savedBefore) == 0) {
            $cityImage = new CityImage();
            $cityImage->countryID = $request->countries;
            $cityImage->city = $request->cities;
            if ($file) {
                $cityImage->image = $request->cities . '.' . $file->getClientOriginalExtension();
                $cityImage->save();
                Storage::disk('s3')->put('city-images/' . $request->cities . '.' . $file->getClientOriginalExtension(), file_get_contents($file));

                return redirect('/gallery/cityPhotos');
            }
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editCityPhoto($id)
    {
        $cityImage = CityImage::findOrFail($id);

        return view('panel.gallery.editcityphoto', ['cityImage' => $cityImage]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function uploadOnlyPhotoForCity(Request $request)
    {
        $cityImage = CityImage::findOrFail($request->cityImageID);
        $file = $request->file('coverPhoto');
        if ($file) {
            Storage::disk('s3')->delete('/city-images/' . $cityImage->image);
            $cityImage->image = $cityImage->city . '.' . $file->getClientOriginalExtension();
            Storage::disk('s3')->put('city-images/' . $cityImage->city . '.' . $file->getClientOriginalExtension(), file_get_contents($file));
            $cityImage->save();

            return redirect('/gallery/cityPhotos');
        }
    }

}
