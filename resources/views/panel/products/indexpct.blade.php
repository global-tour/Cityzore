@include('panel-partials.head', ['page' => 'product-indexpct'])
@include('panel-partials.header', ['page' => 'product-indexpct'])
@include('panel-partials.sidebar')

<style>
    .inn-title .select-dropdown,  .inn-title .caret{
        display: none!important;
    }
</style>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <input type="hidden" id="productType" value="productPCT">
                <div class="inn-title">
                    <h4>All Products</h4>
                    @if (auth()->guard('admin')->check())
{{--                    <a href="{{url('/switchToPCT?route=productcz')}}" class="btn btn-default pull-right" style="margin-left: 10px;">Switch To CZ</a>--}}
                        <select name="" id="" class="" style="width: 200px; display: inline-block; float: right; border: 1px solid; height: 37px;" onchange="window.location.href = this.value">
                            <option value="/product">Cityzore.com</option>
                            <option value="/productPCT" selected>Pariscitytours.fr</option>
                            <option value="/productPCTcom">Paris-city-tours.com</option>
                            <option value="/productCTP">Citytours.paris</option>
                        </select>
                    @endif
                </div>
                <div class="col-md-12" style="margin-bottom: 20px;">
                    <h5>Filters <button class="btn btn-primary" id="showHideFilters" data-shown="1">Hide</button></h5>
                </div>
                <div class="filters">
                    <input type="hidden" id="pageID" name="pageID" value="0">
                    <input type="hidden" id="isRun" name="isRun" value="0">
                    <div class="col-md-12">
                        <label class="col-md-12" style="margin-top: 20px;">Attraction</label>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <select class="select2 browser-default custom-select" name="attractions" id="attractions">
                                    <option value="" disabled selected>Choose product attraction</option>
                                    @foreach($attractions as $attraction)
                                        <option value="{{$attraction->id}}">{{$attraction->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <label class="col-md-12" style="margin-top: 20px;">Category</label>
                        <div class="col-md-12" style="">
                            <div class="col-md-4">
                                <select class="select2 browser-default custom-select" id="categoryId" style="width:100% !important;">
                                    <option selected disabled value="">Choose a Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{$category->categoryName}}">{{$category->categoryName}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @if (auth()->guard('admin')->check())
                        <label class="col-md-12" style="margin-top: 20px;">Supplier</label>
                        <div class="col-md-12">
                            <div class="col-md-4">
                                <select class="select2 browser-default custom-select" id="supplierId" style="width:100% !important;">
                                    <option selected disabled value="-1">Paris Business and Travel</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{$supplier->id}}">{{$supplier->companyName}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <label class="col-md-12" style="margin-top: 20px;">Published/Not Published</label>
                        <div class="col-md-12">
                            <div class="col-md-2">
                                <input type="checkbox" name="type" class="filled-in" id="publishedFilter" value="1" checked="checked"/>
                                <label for="publishedFilter">Published</label>
                            </div>
                            <div class="col-md-2">
                                <input type="checkbox" name="type" class="filled-in" id="notPublishedFilter" value="1" checked="checked"/>
                                <label for="notPublishedFilter">Not Published</label>
                            </div>
                        </div>
                        <label class="col-md-12" style="margin-top: 20px;">Order By</label>
                        <div class="col-md-12" style="margin-bottom: 25px;">
                            <div class="col-md-4">
                                <select class="select2 browser-default custom-select" name="orderBy" id="orderBy">
                                    <option value="newest" selected>By Date (Newest)</option>
                                    <option value="oldest">By Date (Oldest)</option>
                                    <option value="titleAsc">Title (A-Z)</option>
                                    <option value="titleDesc">Title (Z-A)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-bottom: 25px; margin-top: 25px;">
                            <div class="col-md-1">
                                <button id="applyFiltersButton" class="btn btn-primary">Apply</button>
                            </div>
                            <div class="col-md-2">
                                <button id="clearFiltersButton" class="btn btn-primary">Clear</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table table-hover">
                            <thead>
                            <tr>
                                <th>Index</th>
                                <th>Image</th>
                                <th>Ref. Code</th>
                                <th>Company Name</th>
                                <th>Title</th>
                                <th>Confirmed</th>
                                <th>Published</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>


                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('panel-partials.scripts', ['page' => 'product-indexpct'])
@include('panel-partials.datatable-scripts', ['page' => 'product-indexpct'])
