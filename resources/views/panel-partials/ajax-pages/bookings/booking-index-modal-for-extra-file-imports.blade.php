           <form action="{{ url('/bookings-extra-file-import') }}" id="booking-extra-file-import-form" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="booking_id" value="{{$booking->id}}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="display: inline-block; font-size: 18px"><i class="icon-cz-copy"></i> Booking Extra File Import</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-size: 28px!important">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="plus-button" style="margin-bottom: 20px; cursor: pointer;">
                        <label for="" class="label label-primary"><i class="icon-cz-plus"></i></label>
                    </div>
                    <hr>

                    @foreach ($booking->extra_files as $file)
                        <div class="extra-image-wrap" style="margin: 15px 0; border: solid 1px #ccc; padding: 10px;">{{$file->image_base_name}}     <a href="#"> <label class="label label-danger icon-cz-cancel pull-right delete-booking-extra-file" data-id="{{$file->id}}">Delete</label> </a> <a href="{{$file->image_name}}"> <label class="label label-success icon-cz-copy pull-right">Show</label></a>
                            <a href="/downloadExtraFile/{{$file->id}}"> <label class="label label-primary icon-cz-copy pull-right">Download</label></a>
                        </div>
                    @endforeach




                    <div id="stats"></div>
                    <div class="form-group">
                        {{--<label class="control-label" for="">Select Extra Files</label>--}}
                        <div style="display: flex; justify-content: center;">
                        <input type="text" class="form-control" name="filename[]" required style="height: 2.2rem;">
                        <input type="file" class="form-control" name="file[]" required>
                        <button class="btn btn-sm btn-danger active remove-unload-item"><i class="icon-cz-trash"></i></button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Import</button>
                </div>
            </form>
