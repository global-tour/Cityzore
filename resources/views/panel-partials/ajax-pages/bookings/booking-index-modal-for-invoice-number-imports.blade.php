<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel" style="display: inline-block; font-size: 18px"><i class="icon-cz-copy"></i>Import Booking İnvoice Number</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true" style="font-size: 28px!important">&times;</span>
    </button>
</div>
<div class="modal-body">

    {{-- @foreach ($booking->extra_files as $file)
        <div class="extra-image-wrap" style="margin: 15px 0; border: solid 1px #ccc; padding: 10px;">{{$file->image_name}}     <a href="#"> <label class="label label-danger icon-cz-cancel pull-right delete-booking-extra-file" data-id="{{$file->id}}">Delete</label> </a> <a href="https://cityzore.s3.eu-central-1.amazonaws.com/booking-files/{{$file->image_name}}"> <label class="label label-success icon-cz-copy pull-right">Show</label></a></div>
    @endforeach --}}


    <form action="{{url('booking/ajax')}}" id="booking-extra-invoice-number-import-form" method="POST">
        @csrf
        <input type="hidden" name="booking_id" value="{{$booking->id}}">
        <input type="hidden" name="action" value="insert_booking_invoice_numbers">

        <div class="old-wrap">
            @foreach ($booking->invoice_numbers as $invoice)

                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-10">
                            @switch($invoice->type)
                                @case(1)
                                <input type="text" class="form-control" value="{{$invoice->invoice_number}}" readonly style="background: #ccc;">
                                @break
                                @case(11)
                                <a target="_blank" href="https://cityzore.s3.eu-central-1.amazonaws.com/invoices/{{$invoice->src}}"><img height="100" class="pull-right" src="https://cityzore.s3.eu-central-1.amazonaws.com/invoices/{{$invoice->src}}" alt=""></a>
                                @break
                                @case(12)
                                <a target="_blank" href="https://cityzore.s3.eu-central-1.amazonaws.com/invoices/{{$invoice->src}}"><img height="100" class="pull-right" src="https://cityzore.s3.eu-central-1.amazonaws.com/invoices/{{$invoice->src}}" alt=""></a>
                                @break
                                @case(13)
                                <a target="_blank" href="https://cityzore.s3.eu-central-1.amazonaws.com/invoices/{{$invoice->src}}"><img height="100" class="pull-right" src="https://cityzore.s3.eu-central-1.amazonaws.com/invoices/{{$invoice->src}}" alt=""></a>
                                @break
                                @case(21)
                                <a target="_blank" href="https://cityzore.s3.eu-central-1.amazonaws.com/invoices/{{$invoice->src}}"><img height="100" class="pull-right" src="/img/file-pdf.png" title="{{(str_replace('.pdf','',$invoice->src))}}" alt=""></a>
                                @break
                            @endswitch
                        </div>
                        <div class="col-xs-2" style="vertical-align: center">
                            <button data-id="{{$invoice->id}}" class="btn active btn-danger btn-block icon-cz-trash remove-booking-old-invoice"></button>
                        </div>
                    </div>

                </div>
            @endforeach


        </div>


        <div class="live-wrap">

            <div class="form-group">
                <div class="row">
                    <div class="col-xs-10">
                        <input type="text" class="form-control" name="invoices[]" required style="background: #FEF5E7;">
                    </div>
                    <div class="col-xs-2">
                        <button class="btn active btn-danger btn-block icon-cz-trash remove-booking-live-invoice"></button>
                    </div>
                </div>

            </div>

        </div>

    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary send-booking-invoice-form-button">Save</button>
    <input style="display: none" data-id="{{$booking->id}}" type="file" id="input-file-invoice-part">
    <button type="button" class="btn active btn-success pull-left icon-cz-plus add-booking-invoice-part"></button>
    <button type="button" class="btn active btn-danger pull-left icon-cz-cancel remove-booking-invoice-part"></button>
    <button type="button" class="btn active btn-success pull-left add-booking-invoice-file-part-btn">File</button>
</div>
