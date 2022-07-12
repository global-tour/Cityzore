@include('panel-partials.head', ['page' => 'config-general'])
@include('panel-partials.header', ['page' => 'config-general'])
@include('panel-partials.sidebar')


<div class="ad-v2-hom-info">
    <div class="ad-v2-hom-info-inn hidden-xs">
        <ul>
            <li>
                <a href="{{url('general-config/seo-for-pages')}}">
                    <div class="ad-hom-box ad-hom-box-1">
                        <div class="ad-hom-view-com">
                            <h5>SEO for Pages</h5>
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{url('general-config/product-translations?page=1')}}">
                    <div class="ad-hom-box ad-hom-box-1">
                        <div class="ad-hom-view-com">
                            <h5>Product Translations</h5>
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{url('general-config/option-translations?page=1')}}">
                    <div class="ad-hom-box ad-hom-box-1">
                        <div class="ad-hom-view-com">
                            <h5>Option Translations</h5>
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{url('general-config/product-sort')}}">
                    <div class="ad-hom-box ad-hom-box-1">
                        <div class="ad-hom-view-com">
                            <h5>Sort Products For Pages</h5>
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{url('general-config/product-meta-tags-translations?page=1')}}">
                    <div class="ad-hom-box ad-hom-box-1">
                        <div class="ad-hom-view-com">
                            <h5>Product Meta Tags Translations</h5>
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{url('general-config/page-meta-tags-translations?page=1')}}">
                    <div class="ad-hom-box ad-hom-box-1">
                        <div class="ad-hom-view-com">
                            <h5>Page Meta Tags Translations</h5>
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{url('general-config/category-translations?page=1')}}">
                    <div class="ad-hom-box ad-hom-box-1">
                        <div class="ad-hom-view-com">
                            <h5>Category Translations</h5>
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{url('general-config/attraction-translations')}}">
                    <div class="ad-hom-box ad-hom-box-1">
                        <div class="ad-hom-view-com">
                            <h5>Attraction Translations</h5>
                        </div>
                    </div>
                </a>
            </li>
            @if(auth()->guard('admin')->user()->isSuperUser == 1)
                <li>
                    <a href="{{url('general-config/route-translations')}}">
                        <div class="ad-hom-box ad-hom-box-1">
                            <div class="ad-hom-view-com">
                                <h5>Route Translations</h5>
                            </div>
                        </div>
                    </a>
                </li>
            @endif
            <li>
                <a href="{{url('general-config/blog-translations')}}">
                    <div class="ad-hom-box ad-hom-box-1">
                        <div class="ad-hom-view-com">
                            <h5>Blog Translations</h5>
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{url('general-config/blog-meta-tags-translations')}}">
                    <div class="ad-hom-box ad-hom-box-1">
                        <div class="ad-hom-view-com">
                            <h5>Blog Meta Tags Translations</h5>
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{url('general-config/country-translations')}}">
                    <div class="ad-hom-box ad-hom-box-1">
                        <div class="ad-hom-view-com">
                            <h5>Country Translations</h5>
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{url('general-config/city-translations')}}">
                    <div class="ad-hom-box ad-hom-box-1">
                        <div class="ad-hom-view-com">
                            <h5>City Translations</h5>
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{url('general-config/faq-translations')}}">
                    <div class="ad-hom-box ad-hom-box-1">
                        <div class="ad-hom-view-com">
                            <h5>FAQ Translations</h5>
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{url('general-config/update-home-banner')}}">
                    <div class="ad-hom-box ad-hom-box-1">
                        <div class="ad-hom-view-com">
                            <h5>Update Home Banner</h5>
                        </div>
                    </div>
                </a>
            </li>
            <li>
                <a href="{{url('general-config/update-home-banner-pct')}}">
                    <div class="ad-hom-box ad-hom-box-1">
                        <div class="ad-hom-view-com">
                            <h5>Update Home Banner for PCT</h5>
                        </div>
                    </div>
                </a>
            </li>
        </ul>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'config-general'])
