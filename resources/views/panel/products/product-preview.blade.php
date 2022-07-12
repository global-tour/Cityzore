@include('frontend-partials.head', ['page' => 'product-preview'])


<section>
    <div class="rows banner_book" id="inner-page-title">
        <div class="container">
            <div class="banner_book_1">
                <ul>
                    <li class="dl1">Location :
                        @if (!is_null($product->country))
                            {{$product->countryName->countries_name}}
                        @endif
                    </li>
                    <li class="dl2">Price : <i class="{{$config->currencyName->iconClass}}"></i> {{$config->calculateCurrency($minPrices, $config->currencyName->value)}}</li>
                    @if (count($options) > 0)
                    <li class="dl3">Duration : {{$options[0]['tourDuration']}} @if($options[0]['tourDurationDate']=='m') Minute(s) @elseif($options[0]['tourDurationDate']=='h') Hour(s) @elseif($options[0]['tourDurationDate']=='d') Day(s) @endif</li>
                    @endif
                    <li class="dl4"><a href="#">Book Now</a> </li>
                </ul>
            </div>
        </div>
    </div>
</section>
<section>
    <div class="container">
        <div class="wrapper">
            <div class="main">
                <div class="tour_head">
                    <h2>{{$product->title}} <span class="tour_star"><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star" aria-hidden="true"></i><i class="fa fa-star-half-o" aria-hidden="true"></i></span><span class="tour_rat">4.5</span></h2>
                </div>
                <div class="tour_head1">
                    <h3>Overview</h3>
                    <p>{{$product->shortDesc}}</p>
                </div>
                <div class="tour_head1 hotel-book-room">
                    <div id="myCarousel1" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators carousel-indicators-1">
                            <?php $i=0; ?>
                            @foreach($productImages as $productImage)
                                <li data-target="#myCarousel1" data-slide-to="{{$i}}"><img src="{{Storage::disk('s3')->url('product-images/' . $productImage->src)}}" alt="{{$productImage->name}}"></li>
                                <?php $i++ ?>
                            @endforeach
                        </ol>
                        <div class="carousel-inner carousel-inner1" role="listbox">
                            <?php $count=0 ?>
                            @foreach($productImages as $productImage)
                                    <div style="width: 100%;height: 500px" class="item @if($count==0) active @endif">
                                        <img style="width: 100%;height: 100%" src="{{Storage::disk('s3')->url('product-images/' . $productImage->src)}}" alt="{{$productImage->name}}" width="400" height="300">
                                    </div>
                                <?php $count++ ?>
                            @endforeach
                        </div>
                        <a class="left carousel-control" href="#myCarousel1" role="button" data-slide="prev"> <span><i style="font-size:40px;background: none" class="icon-cz-angle-left hotel-gal-arr" aria-hidden="true"></i></span> </a>
                        <a class="right carousel-control" href="#myCarousel1" role="button" data-slide="next"> <span><i style="font-size:40px;background: none" class="icon-cz-angle-right hotel-gal-arr " aria-hidden="true"></i></span> </a>
                    </div>
                </div>
                <div class="tour_head1">
                    <h3>Description</h3>
                    <p>{!! html_entity_decode($product->fullDesc) !!}</p>
                </div>
                @for($i=0; $i<count($options); $i++)
                    <div class="row" style="margin-top: 5%;">
                        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-10" onclick="myFunction({{$options[$i]->id}})">
                            <ul style="padding-left: 0px !important;">
                                <li class="option" id = "movable{{$options[$i]->id}}" trigger = "0">
                                    <div class="row">
                                        <div class="hidden-xs hidden-sm col-md-1 col-lg-1">
                                            <img src="https://i.ibb.co/0hkM263/eiffel4.png" style="width:37px; margin-left: 70%">
                                        </div>
                                        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                                            <ul>
                                                <li class="name">
                                                    <p>{{$options[$i]->title}}  </p>
                                                </li>

                                                <li class="description">
                                                    <p> {{$product->title}} <br> This tour duration: {{$options[$i]->tourDuration}} @if($options[$i]->tourDurationDate == 'm') Minute(s) @elseif($options[$i]->tourDurationDate == 'h') Hour(s) @elseif($options[$i]->tourDurationDate == 'd') Day(s) @endif</p>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-lg-3">
                                            <ul style="padding-left: 0px;">
                                                <li>
                                                    <p style="font-size:15px;font-weight:bold;"> From <span style="color:#d82121;font-size:20px;font-weight:bold;"><i class="{{$config->currencyName->iconClass}}"></i> {{$config->calculateCurrency($prices[$i], $config->currencyName->value)}}</span></p>
                                                </li>
                                                <li>
                                                    <button class="book-now" id="book-now">
                                                        <p style="color:#d82121;border-top:1px solid #d82121;border-bottom:1px solid #d82121;text-align:center;padding:8px;">Book Now</p>
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endfor
                    <div class="tour_head1">
                        <h3>Includes</h3>
                        <p>
                        @for($i=0;$i<count(explode('|', $product->included));$i++)
                            <p><span style="color: green">&#10004;&nbsp</span>{{explode('|', $product->included)[$i]}}</p>
                            @endfor
                        </p>
                        <p>
                        @for($i=0;$i<count(explode('|', $product->notIncluded));$i++)
                            <p><span style="color: red">&#10008;&nbsp</span>{{explode('|', $product->notIncluded)[$i]}}</p>
                            @endfor
                        </p>
                    </div>
                    <div class="tour_head1">
                        <h3>Know Before You Go</h3>
                        @for($i=0;$i<count(explode('|', $product->knowBeforeYouGo));$i++)
                            <p><span>&#8594;&nbsp</span>{{explode('|', $product->knowBeforeYouGo)[$i]}}</p>
                        @endfor
                    </div>
                    <div class="tour_head1 l-info-pack-days days">
                        <h3>Highlights</h3>
                        <ul>
                            @for($i=0;$i<count(explode('|', $product->highlights));$i++)
                                <p><span>&#8594;&nbsp</span>{{explode('|', $product->highlights)[$i]}}</p>
                            @endfor
                        </ul>
                    </div>
                    <h3>Meeting Point</h3>
                    <ul>
                        <li>
                            @if (count($options) > 0)
                                <p>{{$options[0]->meetingPoint}}</p>
                                @if(!($options[0]->meetingComment==null))
                                <p>{{$options[0]->meetingComment}}</p>
                                @endif
                            @endif
                        </li>
                    </ul>

                <br>
                <input id="productID" type="hidden" value="{{$product->id}}">
            </div>
        </div>
    </div>
</section>
<div class="col-lg-12 col-md-12 col-sm-12" style="text-align:center;padding:20px 0;color:white;background-color: black;">
    Copyright © Cityzore.Com
</div>


@include('frontend-partials.general-scripts', ['page' => 'product-preview'])
