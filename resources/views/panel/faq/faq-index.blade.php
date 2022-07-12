@include('panel-partials.head', ['page' => 'faq-index'])
@include('panel-partials.header', ['page' => 'faq-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>All FAQs</h4>
                    <a href="{{url('/faq/create')}}" class="btn btn-default pull-right">Add New</a>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table">
                            <thead>
                            <tr>
                                <th>Question</th>
                                <th>Answer</th>
                                <th>Category</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($faqs as $faq)
                                <tr>
                                    <td>{{$faq->question}}</td>
                                    <td>{{$faq->answer}}</td>
                                    <td>{{$faq->categoryName->name}}</td>
                                    <td>
                                        <a href="{{url('/faq/'.$faq->id.'/edit')}}" class="sb2-2-1-edit">
                                            <i class="icon-cz-edit" aria-hidden="true"></i>
                                        </a>
                                        <a href="{{url('/faq/'.$faq->id.'/delete')}}" class="sb2-2-1-delete">
                                            <i style="background: #ee6e73;" class="icon-cz-trash" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'faq-index'])
