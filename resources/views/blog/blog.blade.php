@include('frontend-partials.head', ['page' => 'blog'])
@include('frontend-partials.header')

<?php
$commonFunctions = new \App\Http\Controllers\Helpers\CommonFunctions;
$langCode = !is_null(session()->get('userLanguage')) ? session()->get('userLanguage') : 'en';
$language = \App\Language::where('code', $langCode)->first();
$langCodeForUrl = $langCode == 'en' ? '' : $langCode;

?>

<div class="container" style="margin-top: 50px">
    <div class="row">
        <div class="col-md-9 text-center">
            <div class="row" style="display: flex; flex-wrap: wrap">
                @forelse($blogPosts as $blogPost)
                    @php
                        $blog = $blogPost->translations ?? $blogPost;
                    @endphp
                    <div class="col-md-6" style="margin-bottom: 30px">
                        <a href="{{url($langCodeForUrl.'/blog'.$blog->url)}}" class="post-item">
                            <div class="post-image">
                                <img
                                    src="{{Storage::disk('s3')->url('blog/' .$blogPost->gallery->src)}}"
                                    alt="{{$blogPost->gallery->alt}}">
                                <div class="date">
                                    <div class="day">{{date('d', strtotime($blogPost->created_at))}}</div>
                                    <div class="month">{{date('M', strtotime($blogPost->created_at))}}</div>
                                </div>
                            </div>

                            <div class="post-content">
                                <p class="post-title">
                                    {{$blog->title}}
                                </p>
                                <div class="description">
                                    {!! \Illuminate\Support\Str::limit($blog->postContent, 100, '...') !!}
                                </div>
                                <div class="post-info">
                                    <div class="post-time">
                                        <i class="icon-cz-availability"></i>
                                        {{\App\Http\Controllers\Helpers\TimeRelatedFunctions::calculateElapsedTimeOver($blogPost->created_at)}}
                                    </div>
                                    <div class="post-comment">
                                        <i class="icon-cz-comment"></i>
                                        <span href="#"> {{count($blogPost->comments)}} comments</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    Not Found!
                @endforelse
            </div>
            <div class="col-md-12">
                <div class="paginate-area">
                    {{ $blogPosts->appends(request()->input())->links() }}
                </div>
            </div>
        </div>

        <!-- Utilities -->
        <div class="col-md-3">

            <div class="utilities categories">
                <h3>Categories</h3>
                <ul>
                    <li>
                        <a href="{{ url($langCodeForUrl.'/blog') }}">
                            Show All
                        </a>
                    </li>
                    @forelse($categories as $category)
                        @php
                            $cat = $category->translations ?? $category;
                        @endphp
                        <li>
                            <a href="{{ request()->url(). '?cat='. $category->id }}">
                                {{ $cat->categoryName }}
                            </a>
                        </li>
                    @empty
                        <li>
                            Category not found.
                        </li>
                    @endforelse
                </ul>
            </div>

            <div class="utilities attractions">
                <h3>Attractions</h3>
                <ul>
                    @forelse($activeAttractions as $attraction)
                        @php
                            $productsOfAttraction = \App\Product::whereJsonContains("attractions",(string)$attraction->id)->count();
                            $attr = $attraction->translations ?? $attraction;
                        @endphp
                        <li>
                            <a href="{{url($langCodeForUrl.'/'.$commonFunctions->getRouteLocalization('attraction').'/'.$commonFunctions->getAttractionLocalization($attraction, $language)) .'-'.$attraction->id}}">
                                <div class="attraction-thumb"
                                     style="background: url('{{\Illuminate\Support\Facades\Storage::disk('s3')->url('attraction-images/' . $attraction->image)}}') center center no-repeat; background-size: cover">
                                    <p>
                                        {{ $attr->name }}
                                    </p>
                                    <span class="activity">
                                        {{$productsOfAttraction}} {{__('activitiesFound')}}
                                    </span>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li>
                            Attraction not found.
                        </li>

                    @endforelse
                </ul>
            </div>

        </div>
    </div>
</div>

<div class="col-md-12">
    @include('frontend-partials.footer')
</div>

@include('frontend-partials.general-scripts', ['page' => 'blog'])
