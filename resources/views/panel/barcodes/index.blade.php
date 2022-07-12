@include('panel-partials.head', ['page' => 'barcodes-index'])
@include('panel-partials.header', ['page' => 'barcodes-index'])
@include('panel-partials.sidebar')


<div class="ad-v2-hom-info">
    <div class="ad-v2-hom-info-inn hidden-xs">
        <ul>
            @foreach($ticketTypes as $ticket)
            <li>
                <div class="ad-hom-box ad-hom-box-1">
                    <div class="ad-hom-view-com">
                        <p><i class="fa fa-arrow-up up"></i>{{$ticket->name }}</p>
                        <h5>{{ $ticket->is_used }} Used / {{ $ticket->barcodes_count }} Total : Remaining {{ $ticket->barcodes_count - $ticket->is_used }}</h5>
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
    </div>
</div>
<section>
    <div class="sb2-2-2">
        <ul>
            <li>
                <a href="#">
                    <i class="fa fa-home" aria-hidden="true"></i>
                    Home
                </a>
            </li>
            <li class="active-bre">
                <a href="#">
                    All Barcodes
                </a>
            </li>
            <li class="page-back">
                <a href="{{url('/')}}" style="font-size: 18px;">
                    <i class="icon-cz-double-left" aria-hidden="true"></i>
                    Panel
                </a>
            </li>
            <a href="{{url('/barcode/multiple-ticket')}}" id="multipleTicket"
               style="border:none;padding:5px 10px;background-color:#00aced;margin-right:30px;float:right;font-size:16px;color:white"
               type="button">
                Create Multiple Tickets
            </a>
        </ul>
    </div>
    <div class="db">
        <div class="col-lg-12">
            <div class="db-2-com db-2-main" style="background-color: white; padding: 25px;overflow-x: auto;">
                <h4 style="margin-bottom: 2%;">All Barcodes</h4>
                <div class="db-2-main-com db-2-main-com-table">
                    <table id="datatable" class="table">
                        <thead class="hidden-md hidden-sm hidden-xs">
                        <tr>
                            <th>Barcode</th>
                            <th>Barcode Type</th>
                            <th>Status</th>
                            <th>End Time</th>
                            <th>Description</th>
                            <th>usedDate</th>
                            <th>Actions</th>
                            <th>info</th>
                        </tr>
                        </thead>

                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    function removeBarcode(el, id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    data: {id: id, _token: '{{ csrf_token() }}'},
                    url: "/barcodes/remove",
                    success: function (response) {
                        if (response.status === "ok") {
                            el.parent().parent().remove()
                            Swal.fire(
                                'Deleted!',
                                'Your barcode has been deleted.',
                                'success'
                            )
                        }
                    },
                    error: function (err) {
                        Swal.fire(
                            'Oops!',
                            'Deletion failed. Please try again.',
                            'error'
                        )
                    }
                })

            }
        })
    }
</script>
@include('panel-partials.scripts', ['page' => 'barcodes-index'])
@include('panel-partials.datatable-scripts', ['page' => 'barcodes-index'])


