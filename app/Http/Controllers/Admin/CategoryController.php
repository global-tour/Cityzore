<?php

namespace App\Http\Controllers\Admin;

use App\Attraction;
use App\Category;
use App\CategoryAttraction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        $attractions=DB::table('category_attractions')
            ->join('attractions','attractions.id','=','category_attractions.attraction_id')
            ->select('category_attractions.*','attractions.name')
            ->get();
        return view('panel.categories.index',['categories'=> $categories,'attractions'=> $attractions]);
    }

    public function create()
    {
        $attractions = Attraction::select('id', 'name')->get();

        return view('panel.categories.create', ['attractions' => $attractions,]);
    }

    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|min:3|max:120',
        ]);
        $category_id = Category::insertGetId([
            'categoryName' => $request->name
        ]);

        if ($category_id > 0 && isset($request->attractions)) {
            $attractions = $request->attractions;
            $ct = new CategoryAttraction();
            foreach ($attractions as $attraction) {
                $ct->create([
                    'category_id' => $category_id,
                    'attraction_id' => intval($attraction),
                ]);
            }
        }

        return redirect('/category');
    }


    public function edit($id)
    {
        $category=Category::findOrFail($id);
        $selected_att=array();
        $selected_att_raws=CategoryAttraction::where('category_id',$id)->get();
        foreach ($selected_att_raws  as $selected_att_raw){
            array_push($selected_att,$selected_att_raw->attraction_id);
        }
        $attractions = Attraction::select('id', 'name')->get();
        return view('panel.categories.edit', ['attractions' => $attractions,'category' => $category,'selected_att' => $selected_att]);
    }
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|min:3|max:120',
        ]);
        $category = Category::findOrFail($id);
        $category->categoryName=$request->name;
        $category_status=$category->save();
        if ($category_status && isset($request->attractions)) {
            $attractions = $request->attractions;
            if(CategoryAttraction::where('category_id',$id)->exists()) {
                CategoryAttraction::where('category_id',$id)->delete();
            }
            foreach ($attractions as $attraction) {
                CategoryAttraction::create([
                    'category_id' => $id,
                    'attraction_id' => intval($attraction),
                ]);
            }
        }
        return redirect('category')->with(['success' => 'Changes has been done successfully!']);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $delStatus=$category->delete();

        if(!$delStatus) return redirect('category')->with(['error' => 'Category Deletion Failed']);

        if($delStatus && CategoryAttraction::where('category_id',$id)->exists()){
            if(!CategoryAttraction::where('category_id',$id)->delete()) return redirect('category')->with(['error' => 'Category Attraction Deletion Failed']);
        }
        return redirect('category')->with(['success' => 'Successfully Deleted!']);
    }




}
