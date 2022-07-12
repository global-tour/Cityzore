<?php

namespace App\Http\Controllers\Admin;

use App\Attraction;
use App\Av;
use App\BlogGallery;
use App\BlogPost;
use App\Http\Controllers\Controller;
use App\MetaTag;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $blogPosts = BlogPost::all();

        return view('panel.blog.index', ['blogPosts' => $blogPosts]);
    }

    /**
     * Index page for blogs that will be displayed on pariscitytours.fr
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexPCT()
    {
        $blogPosts = BlogPost::on('mysql2')->get();

        return view('panel.blog.indexpct', ['blogPosts' => $blogPosts]);
    }

    /**
     * Index page for blogs that will be displayed on pariscitytours.com
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexPCTcom()
    {
        $blogPosts = BlogPost::on('mysql3')->get();

        return view('panel.blog.indexpctcom', ['blogPosts' => $blogPosts]);
    }

    /**
     * Index page for blogs that will be displayed on citytours.paris
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function indexCTP()
    {
        $blogPosts = BlogPost::on('mysql4')->get();

        return view('panel.blog.indexctp', ['blogPosts' => $blogPosts]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::all();
        $attractions = Attraction::where('isActive', 1)->get();

        return view('panel.blog.create', ['categories' => $categories, 'type' => 'cz', 'attractions' => $attractions]);
    }

    /**
     * Create page for blogs that will be displayed on pariscitytours.fr
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createPCT()
    {
        $categories = Category::all();
        $attractions = Attraction::where('isActive', 1)->get();


        return view('panel.blog.create', ['categories' => $categories, 'type' => 'pct', 'attractions' => $attractions]);
    }

    /**
     * Create page for blogs that will be displayed on pariscitytours.com
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createPCTcom()
    {
        $categories = Category::all();
        $attractions = Attraction::where('isActive', 1)->get();


        return view('panel.blog.create', ['categories' => $categories, 'type' => 'pctcom', 'attractions' => $attractions]);
    }

    /**
     * Create page for blogs that will be displayed on pariscitytours.com
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function createCTP()
    {
        $categories = Category::all();
        $attractions = Attraction::where('isActive', 1)->get();


        return view('panel.blog.create', ['categories' => $categories, 'type' => 'ctp',  'attractions' => $attractions]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function createNewBlogPost(Request $request)
    {
        $file = $request->file('coverPhoto');
        $blogPost = new BlogPost();
        if ($request->type == 'pct') {
            $blogPost->setConnection('mysql2');
        }
        elseif ($request->type == 'pctcom') {
            $blogPost->setConnection('mysql3');
        }
        elseif ($request->type == 'ctp') {
            $blogPost->setConnection('mysql4');
        }
        $blogPost->postContent = $request->postContent;
        $blogPost->author = Auth::guard('admin')->user()->name . ' ' . Auth::guard('admin')->user()->surname;
        $blogPost->category = $request->category;
        $blogPost->title = $request->postTitle;
        $blogPost->attraction = $request->attraction;
        $slug = Str::slug($blogPost->title, '-');
        $blogPost->slug = $slug;
        $category = Category::findOrFail($request->category);
        $blogPost->url = '/' . Str::slug($category->categoryName, '-') . '/' . $slug;
        if ($blogPost->save()) {
            $blogGallery = new BlogGallery();
            if ($request->type == 'pct') {
                $blogGallery->setConnection('mysql2');
            }
            elseif ($request->type == 'pctcom') {
                $blogGallery->setConnection('mysql3');
            }
            elseif ($request->type == 'ctp') {
                $blogGallery->setConnection('mysql4');
            }
            $blogGallery->name = $slug;
            $blogGallery->alt = $slug;
            $blogGallery->src = $slug . '-coverphoto.' . $file->getClientOriginalExtension();
            if ($blogGallery->save()) {
                $blogPost->coverPhoto = $blogGallery->id;
                $blogPost->save();
            }
            $metaTag = new MetaTag();
            if ($request->type == 'pct') {
                $metaTag->setConnection('mysql2');
            }
            elseif ($request->type == 'pctcom') {
                $metaTag->setConnection('mysql3');
            }
            elseif ($request->type == 'ctp') {
                $metaTag->setConnection('mysql4');
            }
            $metaTag->title = $request->metaTitle;
            $metaTag->description = $request->metaDescription;
            $metaTag->keywords = $request->metaKeywords;
            if ($metaTag->save()) {
                $blogPost->metaTag()->attach($metaTag->id);
            }
            Storage::disk('s3')->put('blog/' . $slug . '-coverphoto.' . $file->getClientOriginalExtension(), file_get_contents($file));
            $file->storeAs('blog', $slug);
        }

        return redirect('/blog');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $categories = Category::all();
        $blogPost = BlogPost::findOrFail($id);
        $blogPhoto = BlogGallery::findOrFail($blogPost->coverPhoto);
        $attractions = Attraction::where('isActive', 1)->get();

        return view('panel.blog.edit', [
            'blogPost' => $blogPost,
            'categories' => $categories,
            'blogPhoto' => $blogPhoto,
            'type' => 'cz',
            'attractions' => $attractions
        ]);
    }

    /**
     * Edit page for blogs that will be displayed on pariscitytours.fr
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editPCT($id)
    {
        $categories = Category::all();
        $blogPost = BlogPost::on('mysql2')->findOrFail($id);
        $blogPhoto = BlogGallery::on('mysql2')->findOrFail($blogPost->coverPhoto);
        $attractions = Attraction::where('isActive', 1)->get();


        return view('panel.blog.edit', [
            'blogPost' => $blogPost,
            'categories' => $categories,
            'blogPhoto' => $blogPhoto,
            'type' => 'pct',
            'attractions' => $attractions
        ]);
    }

    /**
     * Edit page for blogs that will be displayed on pariscitytours.com
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editPCTcom($id)
    {
        $categories = Category::all();
        $blogPost = BlogPost::on('mysql3')->findOrFail($id);
        $blogPhoto = BlogGallery::on('mysql3')->findOrFail($blogPost->coverPhoto);
        $attractions = Attraction::where('isActive', 1)->get();

        return view('panel.blog.edit', [
            'blogPost' => $blogPost,
            'categories' => $categories,
            'blogPhoto' => $blogPhoto,
            'type' => 'pctcom',
            'attractions' => $attractions
        ]);
    }

    /**
     * Edit page for blogs that will be displayed on pariscitytours.com
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editCTP($id)
    {
        $categories = Category::all();
        $blogPost = BlogPost::on('mysql4')->findOrFail($id);
        $blogPhoto = BlogGallery::on('mysql4')->findOrFail($blogPost->coverPhoto);
        $attractions = Attraction::where('isActive', 1)->get();

        return view('panel.blog.edit', [
            'blogPost' => $blogPost,
            'categories' => $categories,
            'blogPhoto' => $blogPhoto,
            'type' => 'ctp',
            'attractions' => $attractions
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $type = $request->type;
        if ($type == 'cz') {
            $blogPost = BlogPost::findOrFail($id);
        } elseif ($type == 'pct') {
            $blogPost = BlogPost::on('mysql2')->findOrFail($id);
        } elseif ($type == 'pctcom') {
            $blogPost = BlogPost::on('mysql3')->findOrFail($id);
        } elseif ($type == 'ctp') {
            $blogPost = BlogPost::on('mysql4')->findOrFail($id);
        }
        $file = $request->file('coverPhoto');
        $blogPost->postContent = $request->postContent;
        $blogPost->author = Auth::guard('admin')->user()->name . ' ' . Auth::guard('admin')->user()->surname;
        $blogPost->category = $request->category;
        $blogPost->title = $request->postTitle;
        $blogPost->attraction = $request->attraction;
        $slug = Str::slug($blogPost->title, '-');
        $category = Category::findOrFail($request->category);
        $blogPost->url = '/' . Str::slug($category->categoryName, '-') . '/' . $slug;
        if (!is_null($file)) {
            $blogPost->save();
            $blogGallery = new BlogGallery();
            if ($type == 'pct') {
                $blogGallery->setConnection('mysql2');
            }
            elseif ($type == 'pctcom') {
                $blogGallery->setConnection('mysql3');
            }
            elseif ($type == 'ctp') {
                $blogGallery->setConnection('mysql4');
            }
            $blogGallery->name = $slug;
            $blogGallery->alt = $slug;
            $blogGallery->src = $slug . '-coverphoto.' . $file->getClientOriginalExtension();
            if ($blogGallery->save()) {
                $blogPost->coverPhoto = $blogGallery->id;
                $blogPost->save();
            }
            $metaTag = new MetaTag();
            if ($type == 'pct') {
                $metaTag->setConnection('mysql2');
            }
            elseif ($type == 'pctcom') {
                $metaTag->setConnection('mysql3');
            }
            elseif ($type == 'ctp') {
                $metaTag->setConnection('mysql4');
            }
            $metaTag->title = $request->metaTitle;
            $metaTag->description = $request->metaDescription;
            $metaTag->keywords = $request->metaKeywords;
            if ($metaTag->save()) {
                $blogPost->metaTag()->detach();
                $blogPost->metaTag()->attach($metaTag->id);
            }
            Storage::disk('s3')->put('blog/' . $slug . '-coverphoto.' . $file->getClientOriginalExtension(), file_get_contents($file));
            $file->storeAs('blog', $slug);

            return redirect('/blog');
        } else {
            $blogPost->save();
            $metaTag = new MetaTag();
            if ($type == 'pct') {
                $metaTag->setConnection('mysql2');
            }
            elseif ($type == 'pctcom') {
                $metaTag->setConnection('mysql3');
            }
            elseif ($type == 'ctp') {
                $metaTag->setConnection('mysql4');
            }
            $metaTag->title = $request->metaTitle;
            $metaTag->description = $request->metaDescription;
            $metaTag->keywords = $request->metaKeywords;
            if ($metaTag->save()) {
                $blogPost->metaTag()->detach();
                $blogPost->metaTag()->attach($metaTag->id);
            }

            return redirect('/blog');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImageForBlogPost(Request $request)
    {
        Storage::disk('s3')->put('blog\\'.$_FILES['file-0']['name'], file_get_contents($_FILES['file-0']['tmp_name']));

        $blogGallery = new BlogGallery();
        $blogGallery->name = $_FILES['file-0']['name'];
        $blogGallery->alt = $_FILES['file-0']['name'];
        $blogGallery->src = $_FILES['file-0']['tmp_name'];
        $blogGallery->save();

        return response()->json([
            'errorMessage' => 'Insert Error Message',
            'result' => [
                'url' => Storage::disk('s3')->url('blog/' . $_FILES['file-0']['name']),
                'name' => $_FILES['file-0']['name'],
                'size' => $_FILES['file-0']['size']
        ]]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        $blogPost = BlogPost::findOrFail($id);
        $blogPost->delete();

        return redirect('/blog');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function destroyPCT($id)
    {
        $blogPost = BlogPost::on('mysql2')->findOrFail($id);
        $blogPost->delete();

        return redirect('/blogPCT');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function destroyPCTcom($id)
    {
        $blogPost = BlogPost::on('mysql3')->findOrFail($id);
        $blogPost->delete();

        return redirect('/blogPCTcom');
    }
    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function destroyCTP($id)
    {
        $blogPost = BlogPost::on('mysql4')->findOrFail($id);
        $blogPost->delete();

        return redirect('/blogCTP');
    }


    public function ajax(Request $request){
        $blogPost = new BlogPost();

        if($request->model == "pct"){
            $blogPost->setConnection('mysql2');
        }
        if($request->model == "pctcom"){
            $blogPost->setConnection('mysql3');
        }
        if($request->model == "ctp"){
            $blogPost->setConnection('mysql4');
        }

        switch ($request->action) {
            case 'turn_draft':

           $blogPost = $blogPost->findOrFail($request->data_id);
           $blogPost->is_draft = abs($blogPost->is_draft-1);

           if($blogPost->is_draft == 1){
            $blogPost->is_active = 0;
           }
           if($blogPost->save()){
            return response()->json(['status' => 1, 'is_draft' => $blogPost->is_draft]);
           }
           return response()->json(['status' => 0]);

                break;

                 case 'turn_action':


              $blogPost = $blogPost->findOrFail($request->data_id);
           $blogPost->is_active = abs($blogPost->is_active-1);
           if($blogPost->save()){
            return response()->json(['status' => 1, 'is_action' => $blogPost->is_active]);
           }
           return response()->json(['status' => 0]);


                break;

            default:
                # code...
                break;
        }
    }

}
