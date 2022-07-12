@include('panel-partials.head', ['page' => 'faq-edit'])
@include('panel-partials.header', ['page' => 'faq-edit'])
@include('panel-partials.sidebar')


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> Edit FAQ</a></li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <h3>Edit FAQ</h3>
        <div class="form-horizontal form-label-left">
            <form id="faqUpdateForm" method="POST" action="{{url('faq/update')}}" enctype="multipart/form-data" class="form-horizontal form-label-left">
                <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
                <input type="hidden" name="faqID" class="faqID" value="{{$faq->id}}">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="input-field col-md-12" style="border: 1px solid #e0e0e0;">
                                <div class="row" style="padding: 30px;">
                                    <span style="font-size: 20px!important;padding-bottom: 5%;">Select Category</span> <br>
                                    <select class="browser-default custom-select col-md-9" name="category" id="category">
                                        @foreach($faqCategories as $category)
                                            <option value="{{$category->id}}" @if($faq->category == $category->id) selected @endif>{{$category->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 40px;">
                            <div class="input-field col-md-12" style="border: 1px solid #e0e0e0;">
                                <div class="row" style="padding: 30px;">
                                    <span style="font-size: 20px!important;">FAQ Question</span><br>
                                    <input id="question" name="question" type="text" value="{{$faq->question}}" class="validate form-control" style="margin-top:2%; font-size:15px;">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-field col-lg-12" style="border: 1px solid #e0e0e0;">
                                <div class="row" style="padding: 30px;">
                                    <span style="font-size: 20px!important;">Short Description</span><br>
                                    <textarea rows="9" id="answer" name="answer" type="text" class="validate form-control" style="margin-top:2%; font-size:15px;">{{$faq->answer}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'faq-edit'])
