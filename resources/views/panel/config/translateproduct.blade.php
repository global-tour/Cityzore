@include('panel-partials.head', ['page' => 'translateproduct'])
@include('panel-partials.header', ['page' => 'translateproduct'])
@include('panel-partials.sidebar')

<style>
    .nav-tabs{
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }
    .nav-tabs>li{
        background: #fafafa !important;
        border-right: 1px solid #D5D8DC;
          border-left: 2px solid #fff;
          transition: all .3s;
    }

    .nav-tabs>li:hover{
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


    .nav-tabs>li>a{
    font-size: 13px !important;
    }
    .tags{
        min-height: 34px !important;
    }





</style>


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li>
                <a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
            </li>
            <li class="active-bre">
                <a href="#"> Translate Product from English to {{$languageToTranslate->name}}</a>
            </li>
        </ul>
    </div>
    @if (count($options) > 0)
    <div class="alert alert-danger" role="alert">
        <p>Some of this product's options are not translated to {{$languageToTranslate->name}}.</p>
        @foreach($options as $option)
            <p>{{$option->title}}<a target="_blank" href="{{url('/general-config/translateOption/'.$option->id.'/'.$languageToTranslate->id)}}" class="btn btn-primary" style="margin-left: 30px;">Translate</a></p>
        @endforeach
    </div>
    @endif
    <div class="sb2-2-add-blog sb2-2-1">
        <h2>Translate Product from English to {{$languageToTranslate->name}}</h2>
        <form id="translateProductForm" method="POST" action="{{url('general-config/saveProductTranslation/'.$productID.'/'.$languageID)}}" class="form-horizontal form-label-left">
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <input type="hidden" name="type" id="type" value="{{$type}}">

     <div class="row">

          <ul class="nav nav-tabs" style="margin-top: 40px;">
            <li class="active"><a data-toggle="tab" href="#title">Title</a></li>
            <li><a data-toggle="tab" href="#short-description">Short Description</a></li>
            <li><a data-toggle="tab" href="#full-description">Full Description</a></li>
            <li><a data-toggle="tab" href="#highlightss">Highlights</a></li>
            <li><a data-toggle="tab" href="#includedd">Included</a></li>
            <li><a data-toggle="tab" href="#not-included">Not Included</a></li>
            <li><a data-toggle="tab" href="#know-before-you-go">Know Before You Go</a></li>
            <li><a data-toggle="tab" href="#category">Category</a></li>
            <li><a data-toggle="tab" href="#cancel-policy">Cancel Policy</a></li>
          </ul>

          <div class="tab-content">

             <?php
             if(!empty($productTranslation)){
               $productTranslation->highlights = implode('{}', explode('|', $productTranslation->highlights));
                $productTranslation->included = implode('{}', explode('|', $productTranslation->included));
                $productTranslation->notIncluded = implode('{}', explode('|', $productTranslation->notIncluded));
                $productTranslation->knowBeforeYouGo = implode('{}', explode('|', $productTranslation->knowBeforeYouGo));


             }
                
                

            ?>

              <div id="title" class="tab-pane fade in active">
                     <div class="col-md-12" style="margin-top: 50px;">

                    <div class="col-md-12">
                        <input readonly class="form-control" type="text" value="{{$product->title}}" name="titleEnglish" id="titleEnglish">
                    </div>
                    <div class="col-md-12">
                        <input class="form-control" type="text" value="@if($productTranslation) {{$productTranslation->title}} @endif" name="title" id="title">
                    </div>
                </div>

              </div>



              <div id="short-description" class="tab-pane fade">

                    <div class="col-md-12" style="margin-top: 50px;">

                    <div class="col-md-12">
                        <textarea readonly class="materialize-textarea form-control" name="shortDescEnglish" id="shortDescEnglish">{{$product->shortDesc}}</textarea>
                    </div>
                    <div class="col-md-12">
                        <textarea class="materialize-textarea form-control" name="shortDesc" id="shortDesc">@if($productTranslation) {{$productTranslation->shortDesc}} @endif</textarea>
                    </div>
                </div>

              </div>

               <div id="full-description" class="tab-pane fade">
                     <div class="col-md-12" style="margin-top: 50px;">

                    <div class="col-md-12">
                        <span readonly class="materialize-textarea form-control" name="fullDescEnglish" id="fullDescEnglish" style="height: 100%;">{!! html_entity_decode($product->fullDesc) !!}</span>
                    </div>
                    <div class="col-md-12">
                        <textarea title="Full Description" name="fullDesc" id="fullDesc" class="materialize-textarea form-control">@if($productTranslation){!! html_entity_decode($productTranslation->fullDesc) !!}@endif</textarea>
                    </div>
                </div>

              </div>

               <div id="highlightss" class="tab-pane fade">

                     {{-- <h2 style="color: #dc3545; font-size: 15px; text-align: center; margin-top: 20px;">(Don't delete the "|" character!)</h2> --}}

                      <div class="col-md-12" style="margin-top: 50px;">

                    <div class="col-md-12">
                        <textarea readonly class="materialize-textarea form-control" name="highlightsEnglish" id="highlightsEnglish">{{str_replace("|"," |\n",$product->highlights)}}</textarea>
                    </div>
                    <div class="col-md-12">

                        <textarea class="materialize-textarea tags" name="highlights" id="highlights">@if($productTranslation) {{$productTranslation->highlights}} @endif</textarea>
                    </div>
                </div>

              </div>

               <div id="includedd" class="tab-pane fade">
                {{-- <h2 style="color: #dc3545; font-size: 15px; text-align: center; margin-top: 20px;">(Don't delete the "|" character!)</h2> --}}

                    <div class="col-md-12" style="margin-top: 50px;">

                    <div class="col-md-12">
                        <textarea readonly class="materialize-textarea form-control" name="includedEnglish" id="includedEnglish">{{str_replace("|"," |\n",$product->included)}}</textarea>
                    </div>
                    <div class="col-md-12">
                        <textarea class="materialize-textarea tags" name="included" id="included">@if($productTranslation) {{$productTranslation->included}} @endif</textarea>
                    </div>
                </div>

              </div>

               <div id="not-included" class="tab-pane fade">
                {{--<h2 style="color: #dc3545; font-size: 15px; text-align: center; margin-top: 20px;">(Don't delete the "|" character!)</h2>--}}

                     <div class="col-md-12" style="margin-top: 50px;">

                    <div class="col-md-12">
                        <textarea readonly class="materialize-textarea form-control" name="notIncludedEnglish" id="notIncludedEnglish">{{str_replace("|"," |\n",$product->notIncluded)}}</textarea>
                    </div>
                    <div class="col-md-12">
                        <textarea class="materialize-textarea tags" name="notIncluded" id="notIncluded">@if($productTranslation) {{$productTranslation->notIncluded}} @endif</textarea>
                    </div>
                </div>

              </div>

               <div id="know-before-you-go" class="tab-pane fade">
               {{--<h2 style="color: #dc3545; font-size: 15px; text-align: center; margin-top: 20px;">(Don't delete the "|" character!)</h2>--}}
                     <div class="col-md-12" style="margin-top: 50px;">

                    <div class="col-md-12">
                        <textarea readonly class="materialize-textarea form-control" name="knowBeforeYouGoEnglish" id="knowBeforeYouGoEnglish">{{str_replace("|"," |\n",$product->knowBeforeYouGo)}}</textarea>
                    </div>
                    <div class="col-md-12">

                        <textarea class="materialize-textarea tags" name="knowBeforeYouGo" id="knowBeforeYouGo" value>@if($productTranslation) {{$productTranslation->knowBeforeYouGo}} @endif</textarea>
                    </div>
                </div>

              </div>

               <div id="category" class="tab-pane fade">

                    <div class="col-md-12" style="margin-top: 50px;">

                    <div class="col-md-12">
                        <input readonly class="form-control" type="text" value="{{$product->category}}" name="categoryEnglish" id="categoryEnglish">
                    </div>
                    <div class="col-md-12">
                        <input class="form-control" type="text" value="@if($productTranslation) {{$productTranslation->category}} @endif" name="category" id="category">
                    </div>
                </div>

              </div>

               <div id="cancel-policy" class="tab-pane fade">

                       <div class="col-md-12" style="margin-top: 50px;">

                    <div class="col-md-12">
                        <textarea readonly class="materialize-textarea form-control" name="cancelPolicyEnglish" id="cancelPolicyEnglish">{{$product->cancelPolicy}}</textarea>
                    </div>
                    <div class="col-md-12">
                        <textarea class="materialize-textarea form-control" name="cancelPolicy" id="cancelPolicy">@if($productTranslation) {{$productTranslation->cancelPolicy}} @endif</textarea>
                    </div>
                </div>

              </div>
          </div>












            </div>
            <div class="row">
                <div class="input-field col s12">
                    <input type="submit" class="btn btn-large bnt-primary" value="Save Translation">
                </div>
            </div>
        </form>
    </div>


    @php
      $targetProduct = \App\Product::findOrFail($productID);
      $targetOptions = $targetProduct->options;

       $targetOptions = json_decode($targetOptions);

       $targetOptionsModel = \App\Option::whereIn("id", $targetOptions);
    @endphp

@if($targetOptionsModel->count())
    <div class="row" style="margin-top: 30px;">
      <div class="col-md-12">




@foreach ($targetOptionsModel->get() as $om)
@php
if((new \App\Http\Controllers\Admin\ConfigController)->isTranslated($om->id, $languageID, 'option')){
$class_name = "alert-info";
$link_class = "";
}else{
$class_name = "alert-danger";
$link_class = "text-danger";
}
  
@endphp

                          




      <div class="alert {{$class_name}}">


        
        <a href="/general-config/translateOption/{{$om->id}}/{{$languageID}}?page=1" style="font-size: 15px;" class="{{$link_class}}">{{$om->referenceCode}} options translation for this product - ( English to {{$languageToTranslate->name}} )</a>
      </div>
@endforeach
        
        

      </div>
     
    </div>
    @endif
</div>


@include('panel-partials.scripts', ['page' => 'translateproduct'])
<script>
       $(document).ready(function() {

     $('form textarea, form input').on('change', function(event) {
         event.preventDefault();

       $.ajax({
           url: '{{url('general-config/saveProductTranslation/'.$productID.'/'.$languageID)}}',
           type: 'POST',
           dataType: 'json',
           data: $("#translateProductForm").serialize(),
       })
       .done(function(response) {
           if(response.status === 'success'){
            Materialize.toast(response.message, 4000, 'toast-success');
            console.log(response.message);
           }
       })
       .fail(function() {
           console.log("error");
       })
       .always(function() {
           console.log("complete");
       });

     });

    




  


  $(document).on('keydown', '.tagify__input', function(event) {

    $("tags").attr("style","");
   
  });

  $(document).on('keyup', '.tagify__input', function(event) {

    $("tags").attr("style","");
   
  });



    });
</script>
