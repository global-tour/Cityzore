@include('panel-partials.head', ['page' => 'ticket-types-index'])
@include('panel-partials.header', ['page' => 'ticket-types-index'])
@include('panel-partials.sidebar')


<div class="sb2-2-3">
    <div class="row">
        <div class="col-md-12">
            <div class="box-inn-sp">
                <div class="inn-title">
                    <h4>Attractions</h4>
                    <a href="{{url('/ticket-type/create')}}" class="btn btn-default pull-right">Add New</a>
                </div>
                <div class="tab-inn">
                    <div class="table-responsive table-desi" style="overflow-x: inherit;">
                        <table id="datatable" class="table">
                            <thead>
                                <tr>
                                    <th>Ticket Type Name</th>
                                    <th>Edit</th>
                                    <th>Usable as Ticket</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($ticketTypes as $ticketType)
                                <input type="hidden" class="attractionID" value="{{$ticketType->id}}">
                                <tr>
                                    <td class="attractionName"><span style="margin-bottom: 5px" class="list-enq-name">{{$ticketType->name}}</span></td>
                                    <td>
                                        <a href="{{url('ticket-type/'.$ticketType->id.'/edit')}}" style="float:left"><i class="icon-cz-edit"></i></a>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary usableAsTicketButton @if($ticketType->usableAsTicket == 1) usableAsTicket @else notUsableAsTicket @endif" data-tickettype-id="{{$ticketType->id}}" >
                                            @if($ticketType->usableAsTicket == 0) Not Usable @else Usable @endif
                                        </button>
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


@include('panel-partials.scripts', ['page' => 'ticket-types-index'])
@include('panel-partials.datatable-scripts', ['page' => 'ticket-types-index'])
