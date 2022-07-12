@include('panel-partials.head', ['page' => 'blog-index'])
@include('panel-partials.header', ['page' => 'blog-index'])
@include('panel-partials.sidebar')

<style>
    .inn-title .select-dropdown, .inn-title .caret {
        display: none !important;
    }
</style>
<section>
    <div class="sb2-2-2">
        <ul>
            <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
            <li class="active-bre"><a href="#"> Blog</a></li>
            <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left"
                                                                                     aria-hidden="true"></i> Panel</a>
            </li>
        </ul>
    </div>
    <div class="sb2-2-1">
        <h2 style="margin-bottom: 1%;">All Blog Posts</h2>
        {{--        <a href="{{url('/switchToPCT?route=blogPCT')}}" class="btn btn-default pull-right" style="margin-left: 10px;">Switch To PCT</a>--}}
        <select name="" id="" class=""
                style="width: 200px; display: inline-block; float: right; border: 1px solid; height: 37px;"
                onchange="window.location.href = this.value">
            <option value="/blog" selected>Cityzore.com</option>
            <option value="/blogPCT">Pariscitytours.fr</option>
            <option value="/blogPCTcom">Paris-city-tours.com</option>
            <option value="/blogCTP">Citytours.paris</option>
        </select>
        <table id="datatable" class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Categories</th>
                <th>Date</th>
                <th>Tags</th>
                <th>Confirmed</th>
                <th>Published</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($blogPosts as $blogPost)
                <tr>
                    <td>1</td>
                    <td>{{$blogPost->title}}</td>
                    <td>{{$blogPost->category}}</td>
                    <td>{{$blogPost->created_at}}</td>
                    <td>{{$blogPost->metaTag()->first()->keywords}}</td>

                    <td>
                        @if($blogPost->is_draft == 1)
                            <label for="" class="btn btn-default turn_draft" data-id="{{$blogPost->id}}">Draft</label>
                        @else
                            <label for="" class="btn btn-success active turn_draft" data-id="{{$blogPost->id}}">Confirmed</label>
                        @endif
                    </td>

                    <td>
                        @if($blogPost->is_active == 1)
                            <label for="" class="btn btn-success active turn_action" data-id="{{$blogPost->id}}">Published</label>
                        @else
                            <label for="" class="btn active btn-danger turn_action" data-id="{{$blogPost->id}}">Not
                                Published</label>
                        @endif
                    </td>

                    <td>
                        <a href="{{url('/blog/'.$blogPost->id.'/edit')}}" class="sb2-2-1-edit">
                            <i class="icon-cz-edit" aria-hidden="true"></i>
                        </a>
                        <a href="#" class="sb2-2-1-edit" data-delete="{{url('/blog/'.$blogPost->id.'/delete')}}">
                            <i class="icon-cz-trash" aria-hidden="true"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</section>


@include('panel-partials.scripts', ['page' => 'blog-index'])
@include('panel-partials.datatable-scripts', ['page' => 'blog-index'])
