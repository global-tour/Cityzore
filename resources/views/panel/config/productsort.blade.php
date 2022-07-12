@include('panel-partials.head', ['page' => 'product-sort'])
@include('panel-partials.header', ['page' => 'product-sort'])
@include('panel-partials.sidebar')


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> Change Page Meta Tags</a></li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <h3>Product Sort</h3>
        <a href="{{url('/general-config')}}" class="btn btn-default pull-right">Go Back</a>
        <div class="form-horizontal form-label-left">
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group" style="margin-top: 40px;">
                        <div class="input-field col-md-12">
                            <select id="pageSelect" class="custom-select browser-default select2 select2-hidden-accessible">
                                <option>Choose a page</option>
                                @foreach($pages as $page)
                                    <option value="{{$page->id}}" data-foo="{{$page->url}}">{{$page->title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-field col-md-12">
                            <select id="productSelect" class="custom-select browser-default select2 select2-hidden-accessible">
                            </select>
                        </div>
                        <div style="margin-top: 50px" class="col-md-6" id="products">

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                    <button type="button" class="btn btn-large" id="sendButton">Send</button>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'product-sort'])

