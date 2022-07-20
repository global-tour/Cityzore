@include('panel-partials.head', ['page' => 'voucher-template-edit'])
@include('panel-partials.header', ['page' => 'voucher-template-edit'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> Edit Voucher Template</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Create Voucher Template</h4>
                </div>
                <div class="tab-inn">
                    <span class="col-md-12 invalid-feedback" role="alert">
                        <strong style="color:darkred"></strong>
                    </span>
                    @if($error = session('error'))
                        <div class="alert alert-success">
                            <p>{{$error}}</p>
                        </div>
                    @endif
                    <form action="{{url('voucher-template/'.$template->id)}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="form-group input-field col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <input type="text" id="name" name="name" value="{{$template->name}}">
                                <label for="travelerName">Template Name</label>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 50px;">
                            <div class="form-group input-field col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <input type="file" id="image" name="image">
                                <label for="image">Template Image</label>
                            </div>
                        </div>
                        <div class="row">
                            <ul class="nav nav-tabs">
                                @foreach($langs as $lang)
                                    <li class="{{($loop->iteration == 1) ? 'active' : ''}}"><a data-toggle="tab" href="#{{$lang->code}}">{{$lang->code}}</a></li>
                                @endforeach


                            </ul>

                            <div class="tab-content">
                                @foreach($langs as $lang)
                                    <div id="{{$lang->code}}" class="tab-pane fade in {{($loop->iteration == 1) ? 'active' : ''}}">
                                        <div class="input-field col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <textarea class="cked" id="template-{{$lang->code}}" name="template[{{$lang->code}}]">{!! $template->template[$lang->code] !!}</textarea>
                                        </div>
                                    </div>
                                @endforeach

                            </div>
                        </div>




                <div class="row">
                    <div class="input-field col s12  col-md-12 col-sm-12 col-xs-12 col-lg-12 ">
                        <input type="submit" class="btn btn-block btn-primary large btn-large" value="Update" style="padding: 5px; font-size: 14px; margin-bottom:30px; height: 50px;">
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>


@include('panel-partials.scripts', ['page' => 'voucher-template-edit'])
