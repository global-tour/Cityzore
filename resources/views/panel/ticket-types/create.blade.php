@include('panel-partials.head', ['page' => 'ticket-types-create'])
@include('panel-partials.header', ['page' => 'ticket-types-create'])
@include('panel-partials.sidebar')


<div class="sb2-2-2">
    <ul>
        <li><a href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
        <li class="active-bre"><a href="#">Create Ticket</a></li>
        <li class="page-back"><a href="{{url('/')}}" style="font-size: 18px;"><i class="icon-cz-double-left" aria-hidden="true"></i> Panel</a></li>
    </ul>
</div>
<div class="sb2-2-add-blog sb2-2-1">
    <form method="POST" action="{{url('ticket-type/create')}}">
        @csrf
        <div class="tab-content">
            <div id="home" class="tab-pane fade in active">
                <div class="box-inn-sp">
                    <div class="inn-title">
                        <h4>Create Ticket</h4>
                    </div>
                    <div class="bor">
                        <div class="row">
                            <div class="input-field col s4">
                                <input name="name" placeholder="Ticket Type Name">
                            </div>
                            <div class="input-field col s4">
                                <input name="bladeName" placeholder="Ticket Blade Name">
                            </div>
                            <div class="input-field col s4">
                                <button type="submit" class="btn btn-primary">Create</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


@include('panel-partials.scripts', ['page' => 'ticket-types-create'])
