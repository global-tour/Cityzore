@include('panel-partials.head', ['page' => 'translateattraction'])
@include('panel-partials.header', ['page' => 'translateattraction'])
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
    <div class="sb2-2-add-blog sb2-2-1">
        <h2>Translate Product from English to {{$languageToTranslate->name}}</h2>
        <form id="translateProductForm" method="POST" action="{{url('general-config/saveAttractionTranslation/'.$attractionID.'/'.$languageID)}}" class="form-horizontal form-label-left">
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <input type="hidden" name="type" id="type" value="{{$type}}">
            <div class="row">



        <ul class="nav nav-tabs" style="margin-top: 40px;">
            <li class="active"><a data-toggle="tab" href="#name">Name</a></li>
            <li><a data-toggle="tab" href="#slug">Slug</a></li>
            <li><a data-toggle="tab" href="#descriptions">Descriptions</a></li>
            <li><a data-toggle="tab" href="#tags">Tags</a></li>
        
          </ul>
 

 

    <div class="tab-content">

               <div id="name" class="tab-pane fade in active">
                     <div class="col-md-12" style="margin-top: 50px;">

                    <div class="col-md-12">
                        <input readonly class="form-control" type="text" value="{{$attraction->name}}" name="nameEnglish" id="nameEnglish">
                    </div>
                    <div class="col-md-12" style="margin-top: 40px;">
                        <input class="form-control" type="text" value="@if($attractionTranslation) {{$attractionTranslation->name}} @endif" name="name" id="name">
                    </div>
                </div>

              </div>



               <div id="slug" class="tab-pane fade">
                     <div class="col-md-12" style="margin-top: 50px;">

                    <div class="col-md-12">
                        <input readonly class="form-control" type="text" value="{{$attraction->slug}}" name="slugEnglish" id="slugEnglish">
                    </div>
                    <div class="col-md-12" style="margin-top: 40px;">
                        <input class="form-control" readonly type="text" value="@if($attractionTranslation) {{$attractionTranslation->slug}} @endif" name="slug" id="slug">
                    </div>
                </div>

              </div>





                 <div id="descriptions" class="tab-pane fade">
                     <div class="col-md-12" style="margin-top: 50px;">

                    <div class="col-md-6">
                        <span readonly class="form-control" name="descriptionEnglish" id="descriptionEnglish" style="height: 100%;">{!! html_entity_decode($attraction->description) !!}</span>
                    </div>
                    <div class="col-md-6" style="margin-top: 40px;" id="sticky-full-description">
                        <textarea title="Full Description" name="description" id="description" class="materialize-textarea form-control">@if($attractionTranslation) {!! html_entity_decode($attractionTranslation->description) !!} @endif</textarea>
                    </div>
                </div>

              </div>


                 <div id="tags" class="tab-pane fade">

                     <?php
                        $tags = '';
                        $tagsTranslation = '';
                        if (!is_null($attraction->tags)) {
                            $tags = implode('{}', explode('|', $attraction->tags));
                            if (!is_null($attractionTranslation)) {
                                $tagsTranslation = implode('{}', explode('|', $attractionTranslation->tags));
                            }
                        }
                    ?>


                     <div class="col-md-12" style="margin-top: 50px;">

                    <div class="col-md-12">
                        <input readonly class="form-control" type="text" value="{{$tags}}" name="tagsEnglish" id="tagsEnglish">
                    </div>
                    <div class="col-md-12" style="margin-top: 40px;">
                        <input class="form-control" type="text" value="@if($attractionTranslation) {{$tagsTranslation}} @endif" name="tags" id="tagsTranslation">
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
</div>


@include('panel-partials.scripts', ['page' => 'translateattraction'])
