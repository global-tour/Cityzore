@include('frontend-partials.head', ['page' => 'contact'])
@include('frontend-partials.header')
    <section>
        <div class="form form-spac rows con-page">
            <div class="container">
                <div class="spe-title col-md-12">
                    <h2><span>{{__('contactUs')}}</span></h2>
                    <div class="title-line">
                        <div class="tl-1"></div>
                        <div class="tl-2"></div>
                        <div class="tl-3"></div>
                    </div>
                </div>
                <div class="pg-contact">
                    <div class="col-md-4 col-sm-6 col-xs-12 new-con new-con1">
                        <h4>{{__('address')}}</h4>
                        <p>GLOBALTOURSANDTICKETS TURIZM BILGISAYAR ILETISIM VE TICARET LIMITED SIRKETI
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12 new-con new-con3">
                        <h4>{{__('contactInfo')}}:</h4>
                        <p> <a href="tel://0033(0)185084639" class="contact-icon">{{__('phone')}} 1: 0033(0)185084639</a>
                            <br> <a href="tel://0033(0)629632393" class="contact-icon">{{__('phone')}} 2: 0033(0)629632393</a>
                            <br> <a href="mailto:contact@cityzore.com" class="contact-icon">{{__('email')}}: contact@cityzore.com </a> </p>
                    </div>
                    <div class="col-md-4 col-sm-6 col-xs-12 new-con new-con4">
                        <h4>Website</h4>
                        <p> <a href="#">{{__('website')}}: www.cityzore.com</a>
                            <br> <a href="#">Facebook: https://www.facebook.com/pariscitytours.fr/</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'contact'])

