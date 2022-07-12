@include('frontend-partials.head', ['page' => 'about-us'])
@include('frontend-partials.header')

    <section class="tourb2-ab-p-2 com-colo-abou">
        <div class="container">
            <div class="spe-title">
                <h2>{{__('aboutUs')}}</h2>
                <div class="title-line">
                    <div class="tl-1"></div>
                    <div class="tl-2"></div>
                    <div class="tl-3"></div>
                </div>
                <p>{{__('aboutUs1')}}</p>
            </div>
            <div class="row tourb2-ab-p1">
                <div class="col-md-6 col-sm-6">
                    <div class="tourb2-ab-p1-left">
                        <h3>{{__('aboutUs2')}}</h3>
                        <span>{{__('aboutUs3')}}</span>
                        <p></p>
                        <a href="#" class="link-btn" style="display: none;">{{__('callUs')}}: +33184208801</a>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6">
                    <div class="tourb2-ab-p1-right"> <img src="{{asset('img/about-us.jpg')}}" alt="Paris City Tours" width="100px" height="300px" /> </div>
                </div>
            </div>
        </div>
    </section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'about-us'])

