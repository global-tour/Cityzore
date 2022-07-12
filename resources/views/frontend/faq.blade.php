@include('frontend-partials.head', ['page' => 'faq'])
@include('frontend-partials.header')


<section>
    <div class="form form-spac rows">
        <div class="container accordion-container">
            <div class="spe-title col-md-12">
                <h2>{!! __('faq') !!}</h2>
                <div class="title-line">
                    <div class="tl-1"></div>
                    <div class="tl-2"></div>
                    <div class="tl-3"></div>
                </div>
                <p>{{__('faq1')}}</p>
            </div>
            @foreach($faqs as $index => $faq)
                @if(! is_null($faq->translate))
                    @php
                        $faq = $faq->translate;
                    @endphp
                    <button class="accordion">{{$faq->question}}</button>
                    <div class="panel">
                        <p>{{$faq->answer}}</p>
                    </div>
                @else
                    <button class="accordion">{{$faq->question}}</button>
                    <div class="panel">
                        <p>{{$faq->answer}}</p>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>


@include('frontend-partials.footer')
@include('frontend-partials.general-scripts', ['page' => 'faq'])

