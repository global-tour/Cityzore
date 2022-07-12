@include('panel-partials.head', ['page' => 'attraction-edit'])
@include('panel-partials.header', ['page' => 'attraction-edit'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#"> Edit Ticket Type</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Edit {{$ticketType->name}} Ticket Type</h4>
                </div>
                <div class="tab-inn">
                    <form action="{{url('ticket-type/'.$ticketType->id.'/update')}}" enctype="multipart/form-data" method="POST">
                        @csrf
                        @method('POST')
                        <div style="text-align: center" class="row">
                            <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <input id="name" type="text" value="{{$ticketType->name}}" class="validate @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <label for="name">Ticket Type Name</label>
                            </div>
                            <div class="input-field col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <input id="warnTicket" type="number" value="{{$ticketType->warnTicket}}" min="0" class="validate @error('warnTicket') is-invalid @enderror" name="warnTicket" value="{{ old('warnTicket') }}" required autocomplete="warnTicket" autofocus>
                                @error('warnTicket')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                <label for="warnTicket">Minimum Ticket Number</label>
                            </div>
                        </div>
                        <input type="submit" class="btn btn-primary large btn-large" value="Update" style="padding: 10px; font-size: 18px; height: 50px;">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@include('panel-partials.scripts', ['page' => 'attraction-edit'])
