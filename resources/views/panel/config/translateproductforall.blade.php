@include('panel-partials.head', ['page' => 'translateproductforall'])
@include('panel-partials.header', ['page' => 'translateproductforall'])
@include('panel-partials.sidebar')
<style>
    .nav-tabs-sub{
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }
    .nav-tabs-sub>li{
        background: #fafafa !important;
        border-right: 1px solid #D5D8DC;
          border-left: 2px solid #fff;
          transition: all .3s;
    }

    .nav-tabs-sub>li:hover{
        transform: scale(1.2) translate(0px,0px);
        z-index: 999999;
       border-left: solid 1px #444;
       border-right: solid 1px #444;

    }

/*    .nav-tabs>li:nth-child(odd){
      border-right: 1px solid #ccc;
    }

    .nav-tabs>li:nth-child(even){
      border-left: 1px solid #fff;
    }*/


    .nav-tabs-sub>li>a{
    font-size: 13px !important;
    }

    textarea.materialize-textarea{
        resize: auto !important;
    }
</style>


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li>
                <a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
            </li>
            <li class="active-bre">
                <a href="#"> Translate Product from English to <span id="langToTranslate">{{$initialLanguage->name}}</span></a>
            </li>
        </ul>
    </div>
    <input type="hidden" id="languageID" value="{{$initialLanguage->id}}">
    <input type="hidden" id="languageCode" value="{{$initialLanguage->code}}">
    <input type="hidden" id="productID" value="{{$product->id}}">
    <ul class="nav nav-tabs">
        @foreach($languages as $ind => $lang)
            @if ($ind == 0)
            <li class="active"><a data-toggle="tab" class="languageTab" data-lang-id="{{$lang->id}}" data-lang-code="{{$lang->code}}" href="#{{$lang->code}}">{{$lang->name}}</a></li>
            @else
            <li><a data-toggle="tab" class="languageTab" data-lang-id="{{$lang->id}}" data-lang-code="{{$lang->code}}" href="#{{$lang->code}}">{{$lang->name}}</a></li>
            @endif
        @endforeach
    </ul>
    <div class="tab-content">
        @foreach($languages as $ind => $lang)
            @if($ind == 0)
            <div id="{{$lang->code}}" class="tab-pane fade in active">

            </div>
            @else
            <div id="{{$lang->code}}" class="tab-pane fade">

            </div>
            @endif
        @endforeach
    </div>
    <div id="accordion" class="optionWrapper" style="margin-top: 30px;">

    </div>
</div>


@include('panel-partials.scripts', ['page' => 'translateproductforall'])
