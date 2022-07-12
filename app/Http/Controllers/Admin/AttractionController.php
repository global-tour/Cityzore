<?php

namespace App\Http\Controllers\Admin;

use App\Attraction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Country;
use App\Page;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AttractionController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {

        return view('panel.attractions.index', ['type' => 'cz']);
    }

     public function indexPCT()
    {
        return view('panel.attractions.indexpct', ['type' => 'pct']);
    }

     public function indexPCTcom()
    {
        return view('panel.attractions.indexpctcom', ['type' => 'pctcom']);
    }

     public function indexCTP()
    {
        return view('panel.attractions.indexctp', ['type' => 'ctp']);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $countries = Country::has('cities')->get();
        $attraction = Attraction::findOrFail($id);

        return view('panel.attractions.edit', ['attraction' => $attraction, 'countries' => $countries]);
    }

       public function editpct($id)
    {
        $countries = Country::has('cities')->get();
        $attraction = Attraction::on('mysql2')->findOrFail($id);

        return view('panel.attractions.editpct', ['attraction' => $attraction, 'countries' => $countries]);
    }

       public function editpctcom($id)
    {
        $countries = Country::has('cities')->get();
        $attraction = Attraction::on('mysql3')->findOrFail($id);

        return view('panel.attractions.editpctcom', ['attraction' => $attraction, 'countries' => $countries]);
    }

       public function editctp($id)
    {
        $countries = Country::has('cities')->get();
        $attraction = Attraction::on('mysql4')->findOrFail($id);

        return view('panel.attractions.editctp', ['attraction' => $attraction, 'countries' => $countries]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {



         $validator = Validator::make($request->all(), [
        'name' => 'required',
        'description' => 'required',
        //'attractionImage' => 'required',
        //'countries' => 'required',
        //'cities' => 'required',
        'tagAttraction' => 'required'
        ]);

          if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }




        $platform = $request->type;

        if($platform == 'pct'){
       $attraction = Attraction::on('mysql2')->findOrFail($id);
        }elseif ($platform == 'pctcom') {
        $attraction = Attraction::on('mysql3')->findOrFail($id);
        }elseif ($platform == 'ctp') {
        $attraction = Attraction::on('mysql4')->findOrFail($id);
        }else{
        $attraction = Attraction::findOrFail($id);
        }

        
        $attraction->tags = $request->tagAttraction;
        $attraction->name = $request->name;
        $attraction->description = $request->description;
        $attraction->slug = Str::slug(strtolower($request->name), '-');

        if (!is_null($request->attractionImage)) {
            $imageFile = $request->attractionImage;
            $fileName = $imageFile->getClientOriginalName();
            $s3 = Storage::disk('s3');
            $filePath = '/attraction-images/' . $fileName;
            $stream = fopen($imageFile->getRealPath(), 'r+');
            $s3->put($filePath, $stream);

            if (!is_null($attraction->image)) {
                Storage::disk('s3')->delete('/attraction-images/' . $attraction->image); // Delete old attraction image first
            }

            $attraction->image = $fileName;
        }

        $page = Page::find($attraction->pageID);
        if ($page) {
            $page->url = '/attraction/'.Str::slug(strtolower($request->name), '-');
            $page->save();
        }

        $attraction->save();

        return redirect('/attraction');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $countries = Country::has('cities')->get();

        return view('panel.attractions.create', ['countries' => $countries]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function  store(Request $request)
    {

        $validator = Validator::make($request->all(), [
        'name' => 'required',
        'description' => 'required',
        'attractionImage' => 'required',
        //'countries' => 'required',
        //'cities' => 'required',
        'tagAttraction' => 'required',
        'bindedCities' => 'required'
        ]);

          if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }


  


        $attraction = new Attraction();
        $attraction->name = $request->name;
        $attraction->description = $request->description;
        $attraction->slug = Str::slug(strtolower($request->name), '-');
        $attraction->cities = $request->has('bindedCities') ? json_encode($request->bindedCities) : null;
        $attraction->isActive = 0;
        $attraction->tags = $request->tagAttraction;
        if (!is_null($request->attractionImage)) {
            $imageFile = $request->attractionImage;
            $fileName = $imageFile->getClientOriginalName();
            $s3 = Storage::disk('s3');
            $filePath = '/attraction-images/' . $fileName;
            $stream = fopen($imageFile->getRealPath(), 'r+');
            $s3->put($filePath, $stream);
            $attraction->image = $fileName;
        }



      



        if ($attraction->save()) {
            $page = new Page();
            $page->name = 'Attraction | ' . $attraction->name;
            $page->title = 'Attraction | ' . $attraction->name;
            $page->description = 'Attraction | ' . $attraction->name;
            $page->keywords = 'Attraction | ' . $attraction->name;
            $page->url = '/attraction/'. $attraction->slug;
            if ($page->save()) {
                $attraction->pageID = $page->id;
                $attraction->save();
            }
        }





        // create attraction pct

        $attractionPCT = new Attraction();
        $attractionPCT->setConnection('mysql2');
        $attractionPCT->name = $request->name;
        $attractionPCT->description = $request->description;
        $attractionPCT->slug = Str::slug(strtolower($request->name), '-');
        $attractionPCT->cities = $request->has('bindedCities') ? json_encode($request->bindedCities) : null;
        $attractionPCT->isActive = 0;
        $attractionPCT->tags = $request->tagAttraction;

         if (!is_null($request->attractionImage)) {
            $attractionPCT->image = $fileName;
         }
         $attractionPCT->pageID = $page->id;
         $attractionPCT->save();



         // create attraction pctcom

        $attractionPCTcom = new Attraction();
        $attractionPCTcom->setConnection('mysql3');
        $attractionPCTcom->name = $request->name;
        $attractionPCTcom->description = $request->description;
        $attractionPCTcom->slug = Str::slug(strtolower($request->name), '-');
        $attractionPCTcom->cities = $request->has('bindedCities') ? json_encode($request->bindedCities) : null;
        $attractionPCTcom->isActive = 0;
        $attractionPCTcom->tags = $request->tagAttraction;

         if (!is_null($request->attractionImage)) {
            $attractionPCTcom->image = $fileName;
         }
         $attractionPCTcom->pageID = $page->id;
         $attractionPCTcom->save();

        

              // create attraction ctp

        $attractionCTP = new Attraction();
        $attractionCTP->setConnection('mysql4');
        $attractionCTP->name = $request->name;
        $attractionCTP->description = $request->description;
        $attractionCTP->slug = Str::slug(strtolower($request->name), '-');
        $attractionCTP->cities = $request->has('bindedCities') ? json_encode($request->bindedCities) : null;
        $attractionCTP->isActive = 0;
        $attractionCTP->tags = $request->tagAttraction;

         if (!is_null($request->attractionImage)) {
            $attractionCTP->image = $fileName;
         }
         $attractionCTP->pageID = $page->id;
         $attractionCTP->save();




      



        return redirect('/attraction');
    }

    /**
     * Function for binding cities with attractions
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bindCity(Request $request)
    {
        $attraction = Attraction::findOrFail($request->attractionID);

        $citiesCol = is_null($attraction->cities) ? [] : json_decode($attraction->cities, true);
        if (!in_array($request->city, $citiesCol)) {
            array_push($citiesCol, $request->city);
            $attraction->cities = json_encode($citiesCol);
            $attraction->save();

            return response()->json(['success' => 'Successful', 'city' => $request->city]);
        }

        return response()->json(['error' => 'Error']);
    }

    /**
     * Function for deleting cities from attractions
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCity(Request $request)
    {
        $attraction = Attraction::findOrFail($request->attractionID);

        $citiesCol = json_decode($attraction->cities, true);
        $newCitiesCol = array_values(array_diff($citiesCol, [$request->city]));
        $attraction->cities = count($newCitiesCol) == 0 ? null : json_encode($newCitiesCol);
        $attraction->save();

        return response()->json(['success' => 'Successful']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setStatus(Request $request)
    {
        $attraction = Attraction::findOrFail($request->attractionID);
        $attraction->isActive = $attraction->isActive == 1 ? 0 : 1;
        $attraction->save();
        $buttonText = $attraction->isActive == 0 ? 'Set Active' : 'Set Passive';

        return response()->json(['success' => 'Successful!', 'buttonText' => $buttonText, 'isActive' => $attraction->isActive]);
    }

}
