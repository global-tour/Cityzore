@include('panel-partials.head', ['page' => 'faq-create'])
@include('panel-partials.header', ['page' => 'faq-create'])
@include('panel-partials.sidebar')


<div class="col-md-12">
    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> Create New FAQ</a></li>
        </ul>
    </div>
    <div class="sb2-2-add-blog sb2-2-1">
        <h3>Create New FAQ</h3>
        <div class="form-horizontal form-label-left">
            <input type="hidden" name="_token" class="csrfToken" value="{{ csrf_token() }}">
            <input type="hidden" name="_token" id="faqCategoryID" value="">
            <input type="hidden" name="_token" id="addNewCategoryIterator" value="">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group row" style="margin-top: 40px;">
                        <div class="input-field col-md-6">
                            <button class="btn btn-primary" id="addFaqCategoryButton">Add New Category</button>
                            <button class="btn btn-primary" id="chooseFromOldCategories">Choose From Old Categories</button>
                        </div>
                        <div class="col-md-4">

                        </div>
                    </div>
                   <div id="faqCategoryDiv">

                   </div>
                    <div id="faqQuestionAnswerDiv">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'faq-create'])
