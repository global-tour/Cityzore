<?php

namespace App\Http\Controllers\Blog;

use App\Attraction;
use App\BlogPost;
use App\BlogTranslation;
use App\Category;
use App\Http\Controllers\Controller;
use \Illuminate\Http\Request;

class BlogController extends Controller
{

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getBlog()
    {
        $cat = \request()->has('cat') ? trim(htmlspecialchars(\request()->input('cat'))) :  false;

        $blogPosts = BlogPost::with(['translations', 'gallery', 'comments'])
            ->when($cat, function ($query, $cat){
                $query->where('category', $cat);
            })
            ->where("is_draft", 0)
            ->where('is_active', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = Category::with('translations')->get();

        $activeAttractions = Attraction::with('translations')->where('isActive', 1)->take(3)->get();

        return view('blog.blog', compact('blogPosts', 'categories', 'activeAttractions'));
    }

    /**
     * @param $lang
     * @param $category
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getBlogPost($lang, $category, $slug)
    {
        $langCode = session()->get('userLanguage');

        if ($langCode == "en") {
            $blogPost = BlogPost::where("is_draft", 0)->where('is_active', 1)->where('url', '/' . $category . '/' . $slug)->first();

        } else {
            $blogPostTranslation = BlogTranslation::where('url', '/' . $category . '/' . $slug)->first();

            if (empty($blogPostTranslation)) {
                return redirect('blog/' . $category . '/' . $slug);
            }

            $blogPost = BlogPost::where("is_draft", 0)->where('is_active', 1)->findOrFail($blogPostTranslation->blogID);
        }


        $category = Category::where('id', $blogPost->category)->get();
        $recentPost = BlogPost::all()->take(3);
        return view('blog.blog-inner', ['blogPost' => $blogPost, 'category' => $category, 'recentPost' => $recentPost]);
    }

}
