@include('panel-partials.head', ['page' => 'product-editpctcom'])
@include('panel-partials.header', ['page' => 'product-editpctcom'])
@include('panel-partials.sidebar')


<div class="option-edit sb2-2 sb2-2-1">
    <h4>Edit {{$product->title}}</h4>
    <hr>
    <input type="hidden" id="productID" value="{{$product->id}}">
    <input type="hidden" id="pageID" value="{{$pageID}}">
    <div class="productPCTEditForm">
        <div class="form-group">
            <div class="input-field col-md-12 s12">
                <input id="title" name="title" type="text" class="validate form-control" value="{{$product->title}}">
                <label for="title">Title</label>
            </div>
        </div>
        <div class="form-group">
            <div class="input-field col-md-12">
                <input id="shortDesc" name="shortDesc" type="text" class="validate form-control" value="{{$product->shortDesc}}">
                <label for="shortDesc">Short Description</label>
            </div>
        </div>
        <div class="form-group">
            <div class="input-field col-md-12">
                <p style="font-size: 15px!important;">Full Description</p>
                <textarea name="fullDesc" id="fullDesc" class="materialize-textarea form-control">
                {!! html_entity_decode($product->fullDesc) !!}
                </textarea>
            </div>
        </div>
        <?php
        $highlights = implode('{}', (explode('|', $product->highlights)));
        $included = implode('{}', explode('|', $product->included));
        $notIncluded = implode('{}', explode('|', $product->notIncluded));
        $knowBeforeYouGo = implode('{}', explode('|', $product->knowBeforeYouGo));
        $tags = implode('{}', explode('|', $product->tags));
        ?>
        <div class="form-group" style="margin-top: 40px;">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <label> Highlights</label>
                <input name="highlights" id="highlights" value="{{$highlights}}" type="text" class="tags form-control"/>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <label> Included</label>
                <input name="included" id="included" value="{{$included}}" type="text" class="tags form-control"/>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <label> Not Included</label>
                <input name="notIncluded" id="notincluded" value="{{$notIncluded}}" type="text" class="tags form-control"/>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <label> Know Before You Go</label>
                <input name="knowBeforeYouGo" id="beforeyougo" value="{{$knowBeforeYouGo}}" type="text" class="tags form-control"/>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <label>Product Tags</label>
                <input name="tags" id="tags_1" value="{{$tags}}" type="text" class="tags form-control"/>
            </div>
        </div>
        <div class="col-md-12" style="margin-top: 20px;">
            <button class="btn btn-primary" id="productPCTEditButton">Update</button>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'product-editpctcom'])
