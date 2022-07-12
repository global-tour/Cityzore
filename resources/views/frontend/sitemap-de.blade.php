<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <url>
        <loc>https://www.cityzore.com/de</loc>
        <lastmod>{{ $now }}</lastmod>
        <changefreq>Daily</changefreq>
        <priority>1.0</priority>
    </url>

    @foreach($attractions as $attraction)
        @foreach(\App\AttractionTranslation::where('attractionID','=',$attraction->id)->where('languageID', '=', 18)->get() as $t)
            <url>
                <loc>https://www.cityzore.com/de/attraktion/{{$t->slug}}-{{$t->id}}</loc>
                <lastmod>{{ $now }}</lastmod>
                <changefreq>Daily</changefreq>
                <priority>0.8</priority>
            </url>
        @endforeach
    @endforeach

    <!--Product Slugs-->

    @foreach($products as $product)
        @foreach(\App\ProductTranslation::where('productID','=',$product->id)->where('languageID', '=', 18)->get() as $t)
        <url>
            <loc>https://www.cityzore.com/de/{{$t->url}}</loc>
            <lastmod>{{ $product->updated_at->toAtomString() }}</lastmod>
            <changefreq>Daily</changefreq>
            <priority>0.8</priority>
        </url>
        @endforeach
    @endforeach

<!-- Blog Slugs -->

    <url>
        <loc>https://www.cityzore.com/blog</loc>
        <lastmod>{{ $now }}</lastmod>
        <changefreq>Daily</changefreq>
        <priority>0.7</priority>
    </url>

    @foreach($blogPosts as $blogPost)
        <url>
            <loc>https://www.cityzore.com/blog{{\App\BlogTranslation::where('blogID', $blogPost->id)->where('languageID', 18)->first()->url ?? ''}}</loc>
            <lastmod>{{ $now }}</lastmod>
            <changefreq>Daily</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach

    <url>
        <loc>https://www.cityzore.com/de/geschaeftsbedingungen</loc>
        <lastmod>{{ $now }}</lastmod>
        <changefreq>Daily</changefreq>
        <priority>0.6</priority>
    </url>

    <url>
        <loc>https://www.cityzore.com/de/datenschutz-bestimmungen</loc>
        <lastmod>{{ $now }}</lastmod>
        <changefreq>Daily</changefreq>
        <priority>0.6</priority>
    </url>

    <url>
        <loc>https://www.cityzore.com/de/ueber-uns</loc>
        <lastmod>{{ $now }}</lastmod>
        <changefreq>Daily</changefreq>
        <priority>0.6</priority>
    </url>

    <url>
        <loc>https://www.cityzore.com/de/spezialangebot</loc>
        <lastmod>{{ $now }}</lastmod>
        <changefreq>Daily</changefreq>
        <priority>0.6</priority>
    </url>

    <url>
        <loc>https://www.cityzore.com/de/alle-produkte</loc>
        <lastmod>{{ $now }}</lastmod>
        <changefreq>Daily</changefreq>
        <priority>0.6</priority>
    </url>

    <url>
        <loc>https://www.cityzore.com/de/haeufig-gestellte-fragen</loc>
        <lastmod>{{ $now }}</lastmod>
        <changefreq>Daily</changefreq>
        <priority>0.6</priority>
    </url>

</urlset>
