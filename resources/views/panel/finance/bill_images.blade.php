  <div class="col-md-12">


     @foreach ($bills as $bill)

          <div class="col-md-2 col-xs-12 col-sm-6">
           <div class="imagewrap">
            <span class="icon icon-cz-preview"></span>


               <a href="{{Storage::disk('s3')->url('billing-files/' . $bill->name)}}" data-lightbox="bill"><img src="{{Storage::disk('s3')->url('billing-files/' . $bill->name)}}" alt="">

               </a>

           </div>
           <label for="" style="font-weight: bold; font-size: 13px; background-color: #ffddc6; width: 100%;">{{$bill->billingable->name}} {{$bill->billingable->surname}} -- {{$bill->created_at->format('d/m/Y H:i')}}</label><br>

            <label for="" style="font-weight: bold; font-size: 13px; background-color: #ffddc6; width: 100%;"><a href="{{url('finance/download-bill-image/'.$bill->id)}}" target="_blank"  class="btn btn-warning btn-block action" data-id="{{$bill->id}}">Download <i class="icon-cz-floppy"></i></a></label>
           <div></div>

        </div>

     @endforeach








        </div>
